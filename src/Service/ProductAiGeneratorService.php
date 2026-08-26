<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Autowire\Relation\SiblingRelationManager;
use Flexgrid\App\Settings\Settings;
use Flexgrid\AI\OpenAI\OpenAIResponsesClient;
use Flexgrid\Media\MediaContext;
use Flexgrid\Media\MediaManager;
use Flexgrid\Media\MediaSourceFactory;
use Flexgrid\Modules\Webshop\Entity\WebshopProduct;
use Flexgrid\Modules\Webshop\Entity\WebshopProductGroup;
use Flexgrid\Modules\Webshop\Repository\WebshopProductGroupRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopProductMainGroupRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopProductRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopTaxRateRepository;

class ProductAiGeneratorService
{
    /** @var OpenAIResponsesClient */
    protected $client;

    public function __construct(?OpenAIResponsesClient $client = null)
    {
        $this->client = $client ?: new OpenAIResponsesClient();
    }

    public function createProduct(
        string $seedTitle = '',
        array $imageUpload = [],
        string $manufacturer = '',
        float $purchasePrice = 0.0,
        bool $useWebSearch = false,
        ?float $desiredMarginPercentage = null,
        bool $allowGroupCreate = false,
        bool $allowPurchasePriceEstimate = false,
        int $minimumDescriptionParagraphs = 2,
        bool $allowTitleRewrite = false,
        bool $allowVariantDetection = false
    ): array
    {
        $seedTitle = trim($seedTitle);
        $manufacturer = trim($manufacturer);
        $purchasePrice = max(0, $purchasePrice);
        $desiredMarginPercentage = $desiredMarginPercentage === null
            ? $this->getDesiredMarginPercentage()
            : max(0, $desiredMarginPercentage);
        $minimumDescriptionParagraphs = max(1, min(8, $minimumDescriptionParagraphs));
        $imageUploads = $this->normalizeImageUploads($imageUpload);
        $imageParts = $this->buildImageInputParts($imageUploads);

        if ($seedTitle === '' && empty($imageParts)) {
            return [
                'success' => false,
                'message' => 'Vul een titel in of upload een afbeelding.',
            ];
        }

        $result = $this->client->createResponse(
            $this->buildPayload(
                $seedTitle,
                $imageParts,
                $manufacturer,
                $purchasePrice,
                $useWebSearch,
                $desiredMarginPercentage,
                $allowGroupCreate,
                $allowPurchasePriceEstimate,
                $minimumDescriptionParagraphs,
                $allowTitleRewrite,
                $allowVariantDetection
            ),
            [
                'credit_action' => 'ai.webshop.product_generate',
                'credit_reference' => 'webshop_product:' . substr(sha1($seedTitle . ':' . microtime(true)), 0, 16),
                'timeout' => 90,
            ]
        );

        if (empty($result['success'])) {
            return [
                'success' => false,
                'message' => (string)($result['error'] ?? 'Product kon niet worden gegenereerd.'),
                'result' => $result,
            ];
        }

        $data = $this->decodeJson((string)$result['text']);
        if (empty($data)) {
            return [
                'success' => false,
                'message' => 'AI response bevatte geen geldige productdata.',
                'result' => $result,
            ];
        }

        $mediaIdsByIndex = $this->storeUploadedImages($imageUploads);
        $products = $this->createProductsFromAiData(
            $data,
            $seedTitle,
            $manufacturer,
            $purchasePrice,
            $desiredMarginPercentage,
            $allowPurchasePriceEstimate,
            $allowGroupCreate,
            $allowTitleRewrite,
            $allowVariantDetection,
            $mediaIdsByIndex
        );

        if (empty($products)) {
            return [
                'success' => false,
                'message' => 'AI response bevatte geen bruikbare productdata.',
                'result' => $result,
            ];
        }

        $product = $products[0];

        return [
            'success' => true,
            'product' => $product,
            'products' => $products,
            'data' => $data,
            'credits' => $result['credits'] ?? [],
            'result' => $result,
        ];
    }

    protected function buildPayload(
        string $seedTitle,
        array $imageParts,
        string $manufacturer,
        float $purchasePrice,
        bool $useWebSearch,
        float $desiredMarginPercentage,
        bool $allowGroupCreate,
        bool $allowPurchasePriceEstimate,
        int $minimumDescriptionParagraphs,
        bool $allowTitleRewrite,
        bool $allowVariantDetection
    ): array
    {
        $content = [
            [
                'type' => 'input_text',
                'text' => $this->buildInputText(
                    $seedTitle,
                    $manufacturer,
                    $purchasePrice,
                    $useWebSearch,
                    $desiredMarginPercentage,
                    $allowGroupCreate,
                    $allowPurchasePriceEstimate,
                    $minimumDescriptionParagraphs,
                    $allowTitleRewrite,
                    $allowVariantDetection,
                    array_keys($imageParts)
                ),
            ],
        ];

        foreach ($imageParts as $imagePart) {
            $content[] = $imagePart;
        }

        $payload = [
            'model' => 'gpt-4.1-mini',
            'instructions' => $this->buildInstructions(
                $useWebSearch,
                $allowGroupCreate,
                $allowPurchasePriceEstimate,
                $minimumDescriptionParagraphs,
                $allowTitleRewrite,
                $allowVariantDetection,
                array_keys($imageParts)
            ),
            'input' => [
                [
                    'role' => 'user',
                    'content' => $content,
                ],
            ],
        ];

        if ($useWebSearch) {
            $payload['tools'] = [
                [
                    'type' => 'web_search',
                ],
            ];
        }

        return $payload;
    }

    protected function buildInstructions(
        bool $useWebSearch,
        bool $allowGroupCreate,
        bool $allowPurchasePriceEstimate,
        int $minimumDescriptionParagraphs,
        bool $allowTitleRewrite,
        bool $allowVariantDetection,
        array $imageIndexes
    ): string
    {
        $instructions = [
            'Je vult een webshop product voor een CMS.',
            'Gebruik de aangeleverde titel en/of afbeelding als bron. Verzin geen harde productspecificaties die niet aannemelijk zijn.',
            $allowGroupCreate
                ? 'Kies groupId uit de meegegeven productgroepen. Als niets logisch past, gebruik groupId 0 en vul newGroupTitle met een korte nieuwe productgroepnaam. Vul newGroupMainGroupId met een bestaande hoofdgroep-id als die logisch past.'
                : 'Kies groupId alleen uit de meegegeven productgroepen. Gebruik 0 als geen groep logisch past.',
            $allowTitleRewrite
                ? 'Je mag de titel verbeteren, aanvullen of corrigeren op basis van de afbeelding.'
                : 'Als seedTitle is ingevuld, gebruik die titel exact en wijzig of vul hem niet aan.',
            'Schrijf Nederlands, commercieel maar concreet.',
            'Schrijf description als eenvoudige HTML met minimaal ' . $minimumDescriptionParagraphs . ' p-tags.',
            'Return uitsluitend geldige JSON zonder markdown.',
            'Schema voor een product:',
            '{"title":"string","sku":"string","groupId":0,"newGroupTitle":"string","newGroupMainGroupId":0,"imageIndexes":[0],"highlightImageIndex":0,"shortDescription":"string","description":"html string","highlightText":"html string","price":0,"purchasePrice":0,"salePrice":0,"taxRateId":0,"taxRate":21,"stock":0,"trackStock":0,"isActive":1,"status":"draft","color":"string","size":"string","manufacturer":"string"}',
        ];

        if ($allowVariantDetection) {
            $instructions[] = 'Root schema bij varianten: {"products":[product, product]}.';
            $instructions[] = 'Als de afbeeldingen duidelijke kleur- of maatvarianten tonen, return dan products als array met meerdere productobjecten volgens hetzelfde schema.';
            $instructions[] = 'Gebruik imageIndexes per product om te verwijzen naar de meegegeven afbeeldingen, met indexen vanaf 0. Koppel varianten inhoudelijk door consistente titelbasis, kleur/maat en SKU.';
            $instructions[] = 'Als er geen duidelijke varianten zijn, return dan products met exact één product.';
        } else {
            $instructions[] = 'Maak altijd exact één product. Gebruik alle afbeeldingen alleen als context en galerie.';
        }

        if ($useWebSearch) {
            $instructions[] = 'Online zoeken is toegestaan. Gebruik dit voor fabrikant/merk en marktprijs wanneer de input daarvoor onvoldoende is.';
            $instructions[] = 'Baseer prijsadvies op vergelijkbare producten, maar return alleen JSON en geen bronvermelding.';
        } else {
            $instructions[] = 'Online zoeken is niet beschikbaar. Vul fabrikant/merk alleen als dit uit input of afbeelding betrouwbaar blijkt.';
        }

        if ($allowPurchasePriceEstimate) {
            $instructions[] = 'Als purchasePrice niet is aangeleverd, bepaal een aannemelijke inkoopprijs groter dan 0. Gebruik web search als die beschikbaar is, anders een conservatieve schatting.';
        } else {
            $instructions[] = 'Schat geen inkoopprijs als purchasePrice niet is aangeleverd; laat purchasePrice dan 0.';
        }

        return implode("\n", $instructions);
    }

    protected function buildInputText(
        string $seedTitle,
        string $manufacturer,
        float $purchasePrice,
        bool $useWebSearch,
        float $desiredMarginPercentage,
        bool $allowGroupCreate,
        bool $allowPurchasePriceEstimate,
        int $minimumDescriptionParagraphs,
        bool $allowTitleRewrite,
        bool $allowVariantDetection,
        array $imageIndexes
    ): string
    {
        return json_encode([
            'seedTitle' => $seedTitle,
            'manufacturer' => $manufacturer,
            'purchasePrice' => $purchasePrice,
            'desiredMarginPercentage' => $desiredMarginPercentage,
            'webSearchEnabled' => $useWebSearch,
            'allowGroupCreate' => $allowGroupCreate,
            'allowPurchasePriceEstimate' => $allowPurchasePriceEstimate,
            'allowTitleRewrite' => $allowTitleRewrite,
            'allowVariantDetection' => $allowVariantDetection,
            'minimumDescriptionParagraphs' => $minimumDescriptionParagraphs,
            'imageIndexes' => $imageIndexes,
            'productMainGroups' => $this->getProductMainGroupsForPrompt(),
            'productGroups' => $this->getProductGroupsForPrompt(),
            'taxRates' => $this->getTaxRatesForPrompt(),
            'defaults' => [
                'status' => 'draft',
                'isActive' => 1,
                'trackStock' => 0,
                'stock' => 0,
                'taxRate' => 21,
                'salePrice' => 0,
            ],
            'fieldNotes' => [
                'sku' => 'Maak een korte, nette SKU op basis van titel/kleur/maat.',
                'title' => 'Als allowTitleRewrite=false en seedTitle is ingevuld, gebruik seedTitle exact. Als allowTitleRewrite=true mag je de titel verbeteren op basis van de afbeelding.',
                'products' => 'Alleen gebruiken als allowVariantDetection=true. Elk item is een product met optioneel imageIndexes.',
                'imageIndexes' => 'Array met afbeelding-indexen die bij dit product horen. De eerste index wordt hoofdafbeelding.',
                'highlightImageIndex' => 'Optionele afbeelding-index voor het highlight blok.',
                'newGroupTitle' => 'Alleen vullen als allowGroupCreate=true, groupId=0 en geen bestaande productgroep goed past.',
                'newGroupMainGroupId' => 'Alleen vullen met een bestaande productMainGroups id als newGroupTitle wordt gebruikt en een hoofdgroep logisch past.',
                'description' => 'Gebruik eenvoudige HTML met minimaal minimumDescriptionParagraphs p-tags en eventueel een ul.',
                'highlightText' => 'Korte redactionele tekst voor het middenblok, met h2 en een of twee p-tags.',
                'manufacturer' => 'Gebruik de ingevulde fabrikant/merk exact. Als dit leeg is: vul alleen als betrouwbaar af te leiden; met webSearchEnabled=true mag je dit online zoeken.',
                'purchasePrice' => 'Gebruik de aangeleverde inkoopprijs exact. Als deze 0 is en allowPurchasePriceEstimate=true, schat een aannemelijke inkoopprijs groter dan 0.',
                'price' => 'Als purchasePrice groter is dan 0 wordt de uiteindelijke prijs door het CMS berekend met desiredMarginPercentage. Zonder purchasePrice: geef alleen een realistische adviesprijs als die betrouwbaar is; anders 0.',
                'color' => 'Vul alleen een duidelijke kleur in als die uit titel of afbeelding blijkt.',
                'size' => 'Vul alleen een duidelijke maat in als die uit titel of afbeelding blijkt.',
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function getProductGroupsForPrompt(): array
    {
        $groups = [];
        foreach ((new WebshopProductGroupRepository())->getActive() as $group) {
            $groups[] = [
                'id' => (int)$group->getId(),
                'title' => (string)$group->getTitle(),
                'mainGroupId' => method_exists($group, 'getMainGroupId') ? (int)$group->getMainGroupId() : 0,
                'mainGroupTitle' => method_exists($group, 'getWebshopProductMainGroupValue') ? (string)$group->getWebshopProductMainGroupValue() : '',
            ];
        }

        return $groups;
    }

    protected function getProductMainGroupsForPrompt(): array
    {
        $groups = [];
        foreach ((new WebshopProductMainGroupRepository())->getActive() as $group) {
            $groups[] = [
                'id' => (int)$group->getId(),
                'title' => (string)$group->getTitle(),
            ];
        }

        return $groups;
    }

    protected function getTaxRatesForPrompt(): array
    {
        $rates = [];
        foreach ((new WebshopTaxRateRepository())->getActive() as $taxRate) {
            $rates[] = [
                'id' => (int)$taxRate->getId(),
                'title' => (string)$taxRate->getTitle(),
                'rate' => (float)$taxRate->getRate(),
            ];
        }

        return $rates;
    }

    protected function normalizeImageUploads(array $imageUpload): array
    {
        if (empty($imageUpload)) {
            return [];
        }

        if (isset($imageUpload['tmp_name']) && is_array($imageUpload['tmp_name'])) {
            $uploads = [];
            foreach ($imageUpload['tmp_name'] as $index => $tmpName) {
                $uploads[] = [
                    'name' => $imageUpload['name'][$index] ?? ('image-' . $index),
                    'type' => $imageUpload['type'][$index] ?? '',
                    'tmp_name' => $tmpName,
                    'error' => $imageUpload['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                    'size' => $imageUpload['size'][$index] ?? 0,
                ];
            }

            return $uploads;
        }

        return [$imageUpload];
    }

    protected function buildImageInputParts(array $imageUploads): array
    {
        $parts = [];
        foreach ($imageUploads as $index => $imageUpload) {
            $part = $this->buildImageInputPart($imageUpload);
            if (!empty($part)) {
                $parts[(int)$index] = $part;
            }
        }

        return $parts;
    }

    protected function buildImageInputPart(array $imageUpload): array
    {
        if (empty($imageUpload['tmp_name']) || !is_file((string)$imageUpload['tmp_name']) || (int)($imageUpload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return [];
        }

        $mimeType = $this->getUploadMimeType($imageUpload);
        if (strpos($mimeType, 'image/') !== 0) {
            return [];
        }

        $data = base64_encode((string)file_get_contents((string)$imageUpload['tmp_name']));
        if ($data === '') {
            return [];
        }

        return [
            'type' => 'input_image',
            'image_url' => 'data:' . $mimeType . ';base64,' . $data,
        ];
    }

    protected function getUploadMimeType(array $imageUpload): string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mimeType = (string)finfo_file($finfo, (string)$imageUpload['tmp_name']);
                finfo_close($finfo);
                return $mimeType;
            }
        }

        return (string)($imageUpload['type'] ?? '');
    }

    protected function decodeJson(string $text): array
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*/', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);

        $data = json_decode((string)$text, true);
        if (is_array($data)) {
            return $data;
        }

        if (preg_match('/\{.*\}/s', $text, $match)) {
            $data = json_decode($match[0], true);
            return is_array($data) ? $data : [];
        }

        return [];
    }

    protected function createProductFromData(array $data, string $fallbackTitle): WebshopProduct
    {
        $product = new WebshopProduct();
        $groupId = $this->normalizeGroupId((int)($data['groupId'] ?? $data['group_id'] ?? 0));
        $taxRateId = $this->normalizeTaxRateId((int)($data['taxRateId'] ?? $data['tax_rate_id'] ?? 0));

        $product
            ->setTitle($this->text($data['title'] ?? $fallbackTitle, 'Nieuw product'))
            ->setSku($this->text($data['sku'] ?? ''))
            ->setGroupId($groupId)
            ->setShortDescription($this->text($data['shortDescription'] ?? $data['short_description'] ?? ''))
            ->setDescription($this->html($data['description'] ?? ''))
            ->setHighlightText($this->html($data['highlightText'] ?? $data['highlight_text'] ?? ''))
            ->setPrice($this->money($data['price'] ?? 0))
            ->setPurchasePrice($this->money($data['purchasePrice'] ?? $data['purchase_price'] ?? 0))
            ->setSalePrice($this->money($data['salePrice'] ?? $data['sale_price'] ?? 0))
            ->setTaxRateId($taxRateId)
            ->setTaxRate((float)str_replace(',', '.', (string)($data['taxRate'] ?? $data['tax_rate'] ?? 21)))
            ->setStock(max(0, (int)($data['stock'] ?? 0)))
            ->setTrackStock((int)!empty($data['trackStock'] ?? $data['track_stock'] ?? 0))
            ->setIsActive((int)($data['isActive'] ?? $data['is_active'] ?? 1))
            ->setStatus(in_array((string)($data['status'] ?? 'draft'), ['draft', 'published', 'archived'], true) ? (string)$data['status'] : 'draft')
            ->setColor($this->text($data['color'] ?? ''))
            ->setSize($this->text($data['size'] ?? ''))
            ->setManufacturer($this->text($data['manufacturer'] ?? ''));

        return $product;
    }

    protected function createProductsFromAiData(
        array $data,
        string $seedTitle,
        string $manufacturer,
        float $purchasePrice,
        float $desiredMarginPercentage,
        bool $allowPurchasePriceEstimate,
        bool $allowGroupCreate,
        bool $allowTitleRewrite,
        bool $allowVariantDetection,
        array $mediaIdsByIndex
    ): array
    {
        $productDataList = $this->getProductDataList($data, $allowVariantDetection);
        $repository = new WebshopProductRepository();
        $products = [];
        $multipleProducts = count($productDataList) > 1;

        foreach ($productDataList as $index => $productData) {
            if (!is_array($productData)) {
                continue;
            }

            $productData = $this->applyTitleInput($productData, $seedTitle, $allowTitleRewrite || $multipleProducts);
            $productData = $this->applyCommercialInputs($productData, $manufacturer, $purchasePrice, $desiredMarginPercentage, $allowPurchasePriceEstimate);
            $productData['groupId'] = $this->resolveGroupId($productData, $allowGroupCreate);

            $product = $this->createProductFromData($productData, $seedTitle);
            $this->applyProductImages($product, $productData, $mediaIdsByIndex, (int)$index, $multipleProducts);

            $products[] = $repository->add($product);
        }

        $this->syncProductSiblings($products);

        return $products;
    }

    protected function getProductDataList(array $data, bool $allowVariantDetection): array
    {
        if ($allowVariantDetection && !empty($data['products']) && is_array($data['products'])) {
            return $data['products'];
        }

        return [$data];
    }

    protected function applyProductImages(WebshopProduct $product, array $data, array $mediaIdsByIndex, int $productIndex, bool $multipleProducts): void
    {
        if (empty($mediaIdsByIndex)) {
            return;
        }

        $imageIndexes = $this->normalizeImageIndexes($data['imageIndexes'] ?? $data['image_indexes'] ?? [], $mediaIdsByIndex);
        if (empty($imageIndexes)) {
            $imageIndexes = $multipleProducts ? [$productIndex] : array_keys($mediaIdsByIndex);
        }

        $mediaIds = [];
        foreach ($imageIndexes as $imageIndex) {
            if (!empty($mediaIdsByIndex[$imageIndex])) {
                $mediaIds[] = (int)$mediaIdsByIndex[$imageIndex];
            }
        }

        if (empty($mediaIds)) {
            $mediaIds = array_values(array_map('intval', $mediaIdsByIndex));
        }

        $mediaIds = array_values(array_unique(array_filter($mediaIds)));
        if (empty($mediaIds)) {
            return;
        }

        $product->setImage($mediaIds[0]);
        if (count($mediaIds) > 1) {
            $product->setImages(implode(',', array_slice($mediaIds, 1)));
        }

        $highlightIndex = (int)($data['highlightImageIndex'] ?? $data['highlight_image_index'] ?? -1);
        if ($highlightIndex >= 0 && !empty($mediaIdsByIndex[$highlightIndex])) {
            $product->setHighlightImage((int)$mediaIdsByIndex[$highlightIndex]);
        } elseif (count($mediaIds) > 1) {
            $product->setHighlightImage((int)$mediaIds[1]);
        }
    }

    protected function normalizeImageIndexes($value, array $mediaIdsByIndex): array
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }

        if (!is_array($value)) {
            return [];
        }

        $indexes = [];
        foreach ($value as $index) {
            $index = (int)$index;
            if (isset($mediaIdsByIndex[$index])) {
                $indexes[] = $index;
            }
        }

        return array_values(array_unique($indexes));
    }

    protected function syncProductSiblings(array $products): void
    {
        $ids = [];
        foreach ($products as $product) {
            if ($product && (int)$product->getId() > 0) {
                $ids[] = (int)$product->getId();
            }
        }

        $ids = array_values(array_unique($ids));
        if (count($ids) < 2) {
            return;
        }

        foreach ($ids as $sourceId) {
            SiblingRelationManager::sync(
                WebshopProduct::class,
                'linkedProducts',
                $sourceId,
                WebshopProduct::class,
                array_values(array_diff($ids, [$sourceId]))
            );
        }
    }

    protected function applyTitleInput(array $data, string $seedTitle, bool $allowTitleRewrite): array
    {
        if (!$allowTitleRewrite && $seedTitle !== '') {
            $data['title'] = $seedTitle;
        }

        return $data;
    }

    protected function applyCommercialInputs(
        array $data,
        string $manufacturer,
        float $purchasePrice,
        float $desiredMarginPercentage,
        bool $allowPurchasePriceEstimate
    ): array
    {
        if ($manufacturer !== '') {
            $data['manufacturer'] = $manufacturer;
        }

        if ($purchasePrice > 0) {
            $data['purchasePrice'] = $purchasePrice;
            $data['price'] = round($purchasePrice * (1 + ($desiredMarginPercentage / 100)), 2);
        } elseif ($allowPurchasePriceEstimate) {
            $estimatedPurchasePrice = $this->money($data['purchasePrice'] ?? $data['purchase_price'] ?? 0);
            $price = $this->money($data['price'] ?? 0);

            if ($estimatedPurchasePrice <= 0 && $price > 0 && $desiredMarginPercentage > 0) {
                $estimatedPurchasePrice = round($price / (1 + ($desiredMarginPercentage / 100)), 2);
            }

            if ($estimatedPurchasePrice > 0) {
                $data['purchasePrice'] = $estimatedPurchasePrice;
                $data['price'] = round($estimatedPurchasePrice * (1 + ($desiredMarginPercentage / 100)), 2);
            }
        }

        return $data;
    }

    protected function resolveGroupId(array $data, bool $allowGroupCreate): int
    {
        $groupId = $this->normalizeGroupId((int)($data['groupId'] ?? $data['group_id'] ?? 0));
        if ($groupId > 0 || !$allowGroupCreate) {
            return $groupId;
        }

        $newGroupTitle = $this->text($data['newGroupTitle'] ?? $data['new_group_title'] ?? '');
        if ($newGroupTitle === '') {
            return 0;
        }

        return $this->findOrCreateProductGroup($newGroupTitle, (int)($data['newGroupMainGroupId'] ?? $data['new_group_main_group_id'] ?? 0));
    }

    protected function findOrCreateProductGroup(string $title, int $mainGroupId = 0): int
    {
        $title = $this->text($title);
        if ($title === '') {
            return 0;
        }

        $repository = new WebshopProductGroupRepository();
        foreach ($repository->getActive() as $group) {
            if ($group && strcasecmp(trim((string)$group->getTitle()), $title) === 0) {
                return (int)$group->getId();
            }
        }

        $group = new WebshopProductGroup();
        $group
            ->setTitle($title)
            ->setMainGroupId($this->normalizeMainGroupId($mainGroupId))
            ->setDescription('<p>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</p>')
            ->setIsActive(1);

        $group = $repository->add($group);

        return (int)$group->getId();
    }

    protected function normalizeMainGroupId(int $mainGroupId): int
    {
        if ($mainGroupId <= 0) {
            return 0;
        }

        $group = (new WebshopProductMainGroupRepository())->findById($mainGroupId);
        return $group && (int)$group->getId() > 0 ? $mainGroupId : 0;
    }

    protected function getDesiredMarginPercentage(): float
    {
        new Settings();
        $setting = Settings::get('webshop_ai_desired_margin_percentage', [
            'value' => 30,
            'label' => 'Gewenste marge (%)',
        ]);

        return max(0, (float)str_replace(',', '.', (string)($setting['value'] ?? 30)));
    }

    protected function storeUploadedImages(array $imageUploads): array
    {
        $mediaIds = [];
        foreach ($imageUploads as $index => $imageUpload) {
            $mediaId = $this->storeUploadedImage($imageUpload);
            if ($mediaId > 0) {
                $mediaIds[(int)$index] = $mediaId;
            }
        }

        return $mediaIds;
    }

    protected function storeUploadedImage(array $imageUpload): int
    {
        if (empty($imageUpload['tmp_name']) || (int)($imageUpload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return 0;
        }

        try {
            $previousCurrentEntity = $_SERVER['current_entity'] ?? null;
            $_SERVER['current_entity'] = 'WebshopProduct';
            $source = (new MediaSourceFactory())->createFromUpload($imageUpload);
            $media = (new MediaManager())->ingestSource($source, new MediaContext('Flexgrid\Modules\Webshop\Entity\WebshopProduct_edit', 'image', 'webshop_ai_product'));
            return (int)$media->getId();
        } catch (\Throwable $exception) {
            return 0;
        } finally {
            if (isset($previousCurrentEntity)) {
                $_SERVER['current_entity'] = $previousCurrentEntity;
            } else {
                unset($_SERVER['current_entity']);
            }
        }
    }

    protected function normalizeGroupId(int $groupId): int
    {
        if ($groupId <= 0) {
            return 0;
        }

        $group = (new WebshopProductGroupRepository())->findById($groupId);
        return $group && (int)$group->getId() > 0 ? $groupId : 0;
    }

    protected function normalizeTaxRateId(int $taxRateId): int
    {
        if ($taxRateId <= 0) {
            return 0;
        }

        $taxRate = (new WebshopTaxRateRepository())->findById($taxRateId);
        return $taxRate && (int)$taxRate->getId() > 0 ? $taxRateId : 0;
    }

    protected function text($value, string $fallback = ''): string
    {
        $value = trim(strip_tags((string)$value));
        return $value !== '' ? $value : $fallback;
    }

    protected function html($value): string
    {
        return trim((string)$value);
    }

    protected function money($value): float
    {
        return max(0, (float)str_replace(',', '.', (string)$value));
    }
}
