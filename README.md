# Standaard Flexgrid Webshop

De module levert catalogus, hoofdgroepen, productgroepen, productdetail, zoeken, favorieten, winkelwagen, checkout en webshopbeheer zonder `App/Webshop`. Projectspecifieke uitbreidingen zijn optioneel. Algemene verbeteringen horen hier; leverancierimports, branchevelden en dienstenaanvragen horen in een projectuitbreiding.

## Installatiecontract

Benodigd: Flexgrid, Repository en de modules AdminCore, AdminCustomer, AdminInvoice en AdminPayment. AdminPdf verzorgt de factuur-PDF via AdminInvoice. De bestaande projectbasis verzorgt generieke paginaopbouw, navigatie en filters; de webshop neemt die infrastructuur niet over.

Definieer in de omgevingsconfiguratie:

```php
define('__ADMIN_TENANT_ID__', 'administratie-van-deze-winkel');
define('__WEBSHOP_SHOP_ID__', 'unieke-stabiele-winkel-id');
```

Gebruik een eigen shop-id per gekopieerde winkel. De identiteit wordt bij checkout bevroren; wijzig haar niet voor bestaande orders. Installeer met een lege database voor transactionele webshop- en administratiedata. Neem geen betalingen, administratietaken of nummerreeksen uit een andere winkel over.

Voer de normale bevoegde `force_aw=true` scan uit. Gebruik geen `clean=true` voor deze installatie. Die scan ontdekt de module, relations, zichtbaarheid, kaarten, beheer en template-overrides. Stel in Webshop → Instellingen de product-, winkelwagen-, favorieten-, checkout- en betaalretourpagina in. Pagina-ID's komen uit de betreffende installatie. Maak hoofdgroepen, productgroepen en producten via de bestaande EntityEditor; de installatie mag geen branchegegevens vereisen. Installerautomatisering en voorbeeldinhoud volgen in een afzonderlijke opdracht.

## Catalogus en templates

`WebshopProductController::productSlider()` is de algemene productvariant van `ModuleController::slider()`, met hetzelfde presentatiecontract voor template, kaart, desktop-/tabletaantallen, pagina, limiet, overzichtsknop, groep, parentbreedte, controls, autoplay, extra selectie en optionele cache. De standaardtemplate `ProductSlider/ProductSlider.php` geeft deze gegevens door aan dezelfde gedeelde `App/Slider/Slider.php`; er is geen eigen webshop-carousel of tweede initialisatie. De module controleert zichtbaarheid van product, groep en hoofdgroep, clustert varianten vóór de limiet en retourneert niets bij een lege selectie. Ondersteunde kaarten: `ProductCard`, `ProductCard2`, `ProductCardCompact`. De extra selectie is een vertrouwde CMS-/controller-WHERE-expressie; sortering is op titel. Dienstselecties horen in een App-subclass.

| Onderdeel | Contract |
| --- | --- |
| Hoofdgroep | `WebshopProductMainGroup`, bundelt productgroepen |
| Productgroep | `WebshopProductGroup.mainGroupId`, verwijst naar één hoofdgroep |
| Product | `WebshopProduct.groupId`, verwijst naar één productgroep |
| Zichtbaarheid | Alleen het geërfde `isHidden` / `is_hidden`: `0` of `NULL` zichtbaar, `1` verborgen; dezelfde vlag als de lijsteditors |
| ProductCard | Uitgebreide verticale kaart met beschrijving |
| ProductCard2 | Visuele kaart zonder beschrijving |
| ProductCardCompact | Horizontale kaart, stapelt op mobiel |
| ProductCardContent | Gedeelde renderer en één gedeelde stylesheet, geen selecteerbare kaart |
| ProductGrid | Grid, toolbar en afzonderlijke paginering; detailbranch met eigen galerij |
| Hoofdgroeplanding | Banner, productgroepen, CTA en highlight zijn afzonderlijke blokken |

Alle drie kaarten zijn via de bestaande `card`-parameter beschikbaar in productgrid en favorieten. Kaarten bepalen geen vaste gridbreedte. Varianten van hetzelfde product gebruiken de bestaande sibling-relatie. De prijs komt uit PriceService; een minimumprijs krijgt alleen ‘Vanaf’ wanneer de beschikbare varianten verschillende prijzen hebben. Een cluster toont geen misleidende doorgestreepte prijs van een willekeurige variant.

Zichtbaarheid gebruikt voor alle webshop-entities de bestaande `ModuleEntity`-vlag, ook voor verzendmethoden, btw-tarieven, kortingscodes en moodboards. Er is geen aparte activatieproperty. De beheerfilters bieden zichtbaar/verborgen aan en de standaard lijsteditor kan deze waarden direct wijzigen. Bestaande repositorymethoden met de naam `getActive()` selecteren zichtbare records; hun naam introduceert geen extra vlag.

Wanneer de ingestelde site-URL `localhost` als host heeft, worden alle nieuwe producten zichtbaar opgeslagen, ook bij AI-aanmaak en imports. Elders worden AI-producten verborgen aangemaakt.

Een hoofdgroepgrid toont alleen zichtbare hoofdgroepen met zichtbare productgroepen die zichtbare producten hebben. Controleer daarom bij een ontbrekende hoofdgroep zowel de groep als haar producten. Voor de eenmalige overgang van de ontwikkelversie is er een CLI-migratie:

```text
php Flexgrid/Modules/Webshop/tools/migrate-visibility.php
php Flexgrid/Modules/Webshop/tools/migrate-visibility.php --apply --backup=C:/backups/webshop-visibility.json
```

De eerste opdracht inspecteert alleen. Voer de tweede opdracht uit tijdens onderhoud, voordat de nieuwe code publiek draait. Gebruik een nieuw backupbestand in een bestaande schrijfbare map. De migratie bewaart de schema's en oorspronkelijke waarden. Ze zet bestaande concepten en gearchiveerde producten op verborgen en verwijdert daarna de oude productkolom `status`. Lege statuswaarden volgen de bestaande `is_hidden`-vlag. De migratie verwijdert ook eventuele oude `is_active`-kolommen uit de vaste webshoptabellen. Voer na de migratie en codewijziging de bevoegde `force_aw=true`-scan uit, zonder `clean=true`.

Clustering gebeurt vóór paginering, zodat een vaste fetchfactor geen producten tussen pagina's overslaat. De huidige implementatie laadt de gefilterde collectie om clusters te bepalen. Voor zeer grote catalogi is een geïndexeerde clusterrepresentatie nodig; behoud daarbij dezelfde pagineringsuitkomst.

## Typografie en assets

Globale typografie staat in `Files/Assets/Css/Variables.scss`; de gedeelde tekst- en buttonmixins koppelen de properties aan die tokens. h1 t/m h5, a, p en span krijgen in componenten geen vaste font-family, font-size, line-height of font-weight. Gebruik de bestaande `--font-*`, `--line-height` en headingtokens. De gedeelde `--custom-*` tokens blijven een bestaande expliciete ontwerpoverride. Zie het projectcontract `docs/templates/typography.md`.

Elke runtimevariant heeft eigen, gelijknamige assets. De drie kaarten renderen dezelfde ProductCardContent via TemplateResponse; diens CSS registreert eenmaal, ook bij gemengde kaartvarianten. De productgalerij heeft geen afhankelijkheid van App/Image, reviews of vaste USP-groepen. Branchekleuren horen in de projectvariabelen, niet in kaartvarianten.

De standaard hoofdgroep- en productgroepkaarten gebruiken dezelfde oppervlakken, randen en accentkleur als de productkaarten. `ProductGroupCard` heeft een beschrijving en knop; `ProductGroupCard2` toont beeld en titel met een pijl. Er zijn geen wisselende kleuren per kaartindex. Afbeeldingen worden zonder crop aangevraagd en met `object-fit: contain` getoond; ontbrekende beelden krijgen een neutraal icoon. Lange titels mogen ombreken, beschrijvingen worden visueel tot drie regels beperkt. Knoppen volgen de gedeelde buttonstijl; de module stelt daarvoor geen eigen fontproperties in.

Alle product-, groep- en hoofdgroepkaarten, inclusief zoekresultaatkaarten, gebruiken `.clickable` met één zichtbare navigatielink: de actieknop of een benoemde pijl. Titel, afbeelding en overige inhoud zijn geen linkwrappers. De bestaande frontendhandler maakt de rest van de kaart muisklikbaar; de echte link verzorgt toetsenbordnavigatie. Favorietenbuttons blijven afzonderlijke controls en worden door die handler overgeslagen. Cursor en hover-schaduw komen uit de gedeelde `.clickable`-stijl; de kaartvarianten dupliceren deze animatie niet. Zie `docs/templates/cards.md`.

## Productcategoriebalk

Het PageBuilder-blok **Productcategoriebalk** gebruikt `WebshopProductMainGroupController::productMainGroupNavigation($pageId = 0, $limit = 99, $groupLimit = 99)`. Plaats het blok in een eigen headersectie of andere PageBuilder-kolom. Bij pagina-ID 0 gebruikt het de ingestelde productoverzichtpagina.

De balk volgt dezelfde catalogusselectie als het hoofdgroepgrid. Een hoofdgroep verschijnt zodra een zichtbare onderliggende groep een zichtbaar gepubliceerd product bevat. De hoofdgroeptitel linkt naar de hoofdgroep. Met de pijl open je de subgroepen. De dropdown werkt met muis, aanraking en toetsenbord. Escape sluit hem en zet de focus terug op de pijl wanneer de focus in de dropdown staat.

Het template bezit zijn eigen SCSS en JavaScript. De PageBuilder bepaalt de plaatsing en kolombreedte.

## Headeracties

`CartSummary`, `FavoriteSummary` en `SearchBar` renderen dezelfde `HeaderSummary/HeaderSummaryTrigger.php`. Dat component bezit de gedeelde SCSS voor de actietrigger, teller en samenvattingspopover. Iedere actie werkt zelfstandig; de stylesheet laadt eenmaal bij een combinatie. De bestaande controllercalls, hoofdtemplates, zoekhooks en `js-webshop-cart-count` / `js-webshop-favorite-count` blijven behouden. Een lege teller toont voor beide acties `0`.

De defaults zitten in de module, niet in `Files/Assets/Css/Header.scss`: een trigger van 44 bij 44 pixels, een teller rechtsboven en een lichte popover. Ze werken ook zonder `.header__shop-actions`. De projectheader bepaalt alleen kleurcontext, onderlinge plaatsing en de gewone/sticky navigatie. Stijl een laatste menu-item niet automatisch als CTA; de volgorde van de navigatie mag geen buttonsemantiek bepalen.

Belangrijke overrides (op `:root`, header of een afzonderlijke actie):

| Token | Betekenis |
| --- | --- |
| `--webshop-header-summary-trigger-size` | Breedte en hoogte van alle triggers |
| `--webshop-header-summary-color` / `--webshop-header-summary-hover-color` | Icoonkleur; een expliciete `--custom-color` krijgt voorrang |
| `--webshop-header-summary-count-background` / `--webshop-header-summary-count-color` | Tellerachtergrond en tekstkleur |
| `--webshop-header-summary-surface` / `--webshop-header-summary-popover-color` | Afzonderlijke kleurcontext van de popover |
| `--webshop-header-summary-border` / `--webshop-header-summary-muted` | Rand en secundaire popovertekst |

Gebruik gewone teksttokens voor defaults en reserveer `--custom-*` voor bewuste overrides. Stel voor een donkere popover zowel surface als popover-color en muted in; de headerkleur wordt daar bewust niet overgenomen. Globale typografietokens blijven de fontkeuze en tekstgrootte bepalen.

## Betaling en administratie

1. Checkout valideert en vergrendelt actuele productregels, bewaart klant-, prijs- en ordersnapshots, reserveert voorraad en maakt de betaaltransactie binnen één transactie.
2. De order bewaart een administratiejob met vaste tenant, shop-id en factuurregels. Er ontstaat nog geen officiële factuur.
3. Een Mollie-callback haalt de betaling opnieuw op en controleert providerreferentie, bedrag, valuta, modus en betaaldatum. De publiek geregistreerde route is `POST /api/WebshopPayment/webhook`; hiervoor is geen App-adapter nodig. Het retourpad gebruikt een willekeurig transactietoken.
4. Na betaling maakt de Administration-integratie via publieke use-cases één AdminCustomer, definitieve AdminInvoice en AdminPayment-ontvangst. De eigen webshoporder is vergrendeld tijdens deze keten. Bronreferenties bevatten de vaste shop-id. Herhalingen hergebruiken dezelfde records. Klanten worden niet automatisch op e-mailadres samengevoegd.
5. De gewone webshop rekent inclusief btw. OrderInvoiceMapper verdeelt de korting in centen over productregels en neemt verzending afzonderlijk over. Het bevroren inclusief-regelbedrag is leidend; AdminInvoice berekent netto en btw vanuit dat bedrag. De getoonde netto stuksprijs kan daardoor afgerond zijn terwijl aantal en regelbedrag exact blijven. Het bestaande nettofactuurpad blijft werken.
6. Mislukte verwerking bewaart een fout bij de order en is opnieuw uitvoerbaar vanuit het orderdetail. Daar verschijnt na succes een link naar AdminInvoice. Een nieuwe standaardorder krijgt geen tweede legacy WebshopInvoice.

Mollie-modus is een expliciete webshopinstelling, standaard `test`, en wordt op de transactie opgeslagen. De webhook gebruikt dezelfde modus, ongeacht CMS-login. Testbetalingen worden uitsluitend in een ontwikkelinstallatie administratief geboekt en krijgen een TEST-notitie. Gebruik daarvoor een geïsoleerde testadministratie. Een handmatige ontvangst wordt bewust door de beheerder bevestigd via de betaaltransactie.

Factuurdatum is de verwerkingsdatum, boekdatum de bevestigde betaaldatum, beide Europe/Amsterdam. Officiële factuur-PDF en verzending lopen via AdminInvoice. Automatische refunds/chargebacks, creditnota-afhandeling vanuit Mollie en factuurmail na checkout zijn nog geen onderdeel van de gekoppelde orderflow; verwerk correcties via de bestaande administratie. Een webhookfixture vervangt geen publiek bezorgde Mollie-testbetaling.

## Verificatie

Vanuit de projectroot, met een PHP CLI met PDO MySQL en mbstring:

```text
php Flexgrid/Modules/Webshop/tests/CatalogRuntimeTest.php
php Flexgrid/Modules/Webshop/tests/HeaderSummaryRuntimeTest.php
php Flexgrid/Modules/Webshop/tests/HeaderSummaryRuntimeTest.php CartSummary
php Flexgrid/Modules/Webshop/tests/HeaderSummaryRuntimeTest.php FavoriteSummary
php Flexgrid/Modules/Webshop/tests/HeaderSummaryRuntimeTest.php SearchBar
php Flexgrid/Modules/Webshop/tests/CatalogQueryTest.php
php Flexgrid/Modules/Webshop/tests/VisibilityTest.php
php Flexgrid/Modules/Webshop/tests/VisibilityMigrationTest.php
php Flexgrid/Modules/Webshop/tests/AdministrationTest.php
php Flexgrid/Modules/Webshop/tests/AdministrationJobTest.php
php Flexgrid/Modules/Webshop/tests/CheckoutTest.php
php Flexgrid/Modules/Webshop/tests/PaymentEndpointTest.php
php Flexgrid/Modules/AdminInvoice/tests/InclusiveInvoiceLineTest.php
php Flexgrid/Flexgrid/tools/template-override-self-test.php
```

De databasefixtures gebruiken uitsluitend tijdelijke tabellen of teruggedraaide transacties en versturen geen mail of betalingen. CatalogRuntimeTest rendert echte templates en compileert hun SCSS; `tmp/webshop-card-preview.html` is een lokale controle-uitvoer, geen productiepagina. Controleer bovendien de echte pagina's in de browser op breed/smal scherm, fontwijzigingen, productdetail, filters en kaartkeuzes, en bewijs de publiek bezorgde webhook in een testomgeving.
