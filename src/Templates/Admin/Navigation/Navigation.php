<?php
$currentMethod = (string)\Flexgrid\Flexgrid::getApp()->currentMethod;
$pages = [
    [
        'title' => 'Overzicht',
        'href' => __DOMAIN__ . '/Flexgrid/WebshopAdmin/dashboard',
        'methods' => ['dashboard'],
        'icon' => 'fas fa-chart-pie',
    ],
    [
        'title' => 'Bestellingen',
        'href' => __DOMAIN__ . '/Flexgrid/WebshopAdmin/orders',
        'methods' => ['orders', 'orderDetail'],
        'icon' => 'fas fa-shopping-bag',
    ],
    [
        'title' => 'Facturen',
        'href' => __DOMAIN__ . '/Flexgrid/WebshopAdmin/invoices',
        'methods' => ['invoices', 'invoiceDetail'],
        'icon' => 'fas fa-file-invoice',
    ],
    [
        'title' => 'Klanten',
        'href' => __DOMAIN__ . '/Flexgrid/WebshopAdmin/customers',
        'methods' => ['customers', 'customerDetail'],
        'icon' => 'fas fa-users',
    ],
    [
        'title' => 'Producten',
        'href' => __DOMAIN__ . '/Flexgrid/WebshopAdmin/products',
        'methods' => ['products', 'productDetail'],
        'icon' => 'fas fa-box',
    ],
    [
        'title' => 'Inspiratie',
        'href' => __DOMAIN__ . '/Flexgrid/WebshopAdmin/moodboards',
        'methods' => ['moodboards', 'moodboardDetail'],
        'icon' => 'fas fa-map-marker-alt',
    ],
    [
        'title' => 'Rapporten',
        'href' => __DOMAIN__ . '/Flexgrid/WebshopAdmin/reports',
        'methods' => ['reports'],
        'icon' => 'fas fa-chart-line',
    ],
    [
        'title' => 'Instellingen',
        'href' => __DOMAIN__ . '/Flexgrid/WebshopAdmin/settings',
        'methods' => ['settings'],
        'icon' => 'fas fa-cog',
    ],
];
?>

<div class="dashboard-nav webshop-admin-navigation">
    <a href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/dashboard" class="button button-secondary button-icon">
        <i class="fas fa-home"></i>
    </a>

    <?php foreach ($pages as $page) {
        $isActive = in_array($currentMethod, $page['methods'], true);
        if ($isActive) {
            appendIconAndTitleToHeader($page['icon'], $page['title'], 'webshop');
        }
        ?>
        <a href="<?=$page['href']?>" <?=$isActive ? 'class="is-active"' : ''?>>
            <i class="<?=$page['icon']?>"></i>
            <?=$page['title']?>
        </a>
    <?php } ?>
</div>
