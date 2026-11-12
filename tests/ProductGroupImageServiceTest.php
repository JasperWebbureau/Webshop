<?php
namespace {
    if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
}
namespace Repository {
    // Entity persistence is isolated; image processing uses the real framework.
    class ModuleEntity {
        public $saveCount = 0;
        public function save() { ++$this->saveCount; return $this; }
    }
}
namespace Flexgrid\Modules\Webshop\Repository {
    class WebshopProductRepository {
        public static $product;
        public static $queries = [];
        public function getFirstWithImageByGroupIds(array $ids) {
            self::$queries[] = $ids;
            return self::$product;
        }
    }
    class WebshopProductGroupRepository {
        public static $queries = [];
        public function getIdsByMainGroupId($id) {
            self::$queries[] = $id;
            return [7, 8];
        }
    }
}
namespace {
use Flexgrid\Modules\Webshop\Entity\WebshopProductGroup;
use Flexgrid\Modules\Webshop\Entity\WebshopProductMainGroup;
use Flexgrid\Modules\Webshop\Repository\WebshopProductRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopProductGroupRepository;
use Flexgrid\Modules\Webshop\Service\ProductGroupImageService;
use Flexgrid\Repository\MediaRepository;
use Flexgrid\Utils\Files\ImageFile;

$root = str_replace('\\', '/', sys_get_temp_dir()) . '/fg-security-' . bin2hex(random_bytes(8));
mkdir($root);
define('__ROOTDIR__', $root);
require dirname(__DIR__, 3) . '/Flexgrid/tests/Security/Bootstrap.php';
require __DIR__ . '/../src/Service/ProductGroupImageService.php';
require __DIR__ . '/../src/Entity/WebshopProductGroup.php';
require __DIR__ . '/../src/Entity/WebshopProductMainGroup.php';

try {
    mkdir($root . '/Files/Images/products', 0775, true);
    $path = $root . '/Files/Images/products/source.png';
    $image = imagecreatetruecolor(16, 8); imagepng($image, $path); unset($image);
    $original = file_get_contents($path);
    $product = new class {
        public $path = 'Files/Images/products/source.png';
        public function getImage() { return ImageFile::getImageFile($this->path); }
    };
    WebshopProductRepository::$product = $product;
    $_POST = $_REQUEST = $_FILES = [];
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $ids = [];
    foreach ([new WebshopProductGroup(), new WebshopProductMainGroup()] as $entity) {
        $entity->setId(7)->setImage('Files/Images/legacy.png');
        $copy = $entity->getImage();
        $ids[] = $copy->getMediaId();
        securityAssert($copy->exists() && $copy->getMediaId() > 0, 'GET getter clones product image without auth/CSRF');
        securityAssert($copy->getPath() !== $path && file_get_contents($path) === $original, 'Product source remains intact');
        $media = MediaRepository::$records[$copy->getMediaId()];
        securityAssert($media->getNamespace() === get_class($entity) && $media->getField() === 'image', 'Group destination metadata retained');
        securityAssert($entity->saveCount === 1, 'Cloned media ID persisted once');
        $queries = count(WebshopProductRepository::$queries);
        $again = $entity->getImage();
        securityAssert($again->getMediaId() === $copy->getMediaId() && $entity->saveCount === 1 && $queries === count(WebshopProductRepository::$queries), 'Reading again reuses persisted media');
    }
    securityAssert($ids[0] !== $ids[1], 'Group and main group own independent clones');
    securityAssert(WebshopProductRepository::$queries === [[7], [7, 8]] && WebshopProductGroupRepository::$queries === [7], 'Both group selection paths preserved');

    $service = new ProductGroupImageService();
    WebshopProductRepository::$product = null;
    securityAssert($service->cloneFirstProductImageForGroup(7, WebshopProductGroup::class) === 0, 'No product returns zero');
    WebshopProductRepository::$product = $product;
    $product->path = 'Files/Images/products/missing.png';
    $before = securitySnapshot($root);
    securityAssert($service->cloneFirstProductImageForGroup(7, WebshopProductGroup::class) === 0, 'Missing product image returns zero');
    securityAssert($before === securitySnapshot($root), 'Missing product image creates no files');
    file_put_contents($root . '/Files/Images/products/invalid.gif', 'GIF89aTESTTRAVERSAL');
    $product->path = 'Files/Images/products/invalid.gif';
    $before = securitySnapshot($root);
    securityReject(fn() => $service->cloneFirstProductImageForGroup(7, WebshopProductGroup::class), 'Corrupt product image is still rejected');
    securityAssert($before === securitySnapshot($root), 'Corrupt product image creates no files');

    echo 'Product group image regressions passed: ' . $GLOBALS['securityAssertions'] . " assertions\n";
} finally {
    securityCleanup($root);
}
}
