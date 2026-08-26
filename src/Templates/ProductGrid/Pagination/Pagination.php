<?php

$target = isset($target) ? (string)$target : '';
$pagination = is_array($pagination ?? null) ? $pagination : [];
$totalPages = max(0, (int)($pagination['totalPagesAvailable'] ?? 0));
$currentPage = max(0, (int)($pagination['currentPage'] ?? 0));
$records = max(0, (int)($pagination['records'] ?? 0));
$window = 2;
$startPage = max(0, $currentPage - $window);
$endPage = min($totalPages - 1, $currentPage + $window);
?>
<nav class="webshop-product-pagination pagination<?= $totalPages <= 1 ? ' is-empty' : '' ?>"
     data-webshop-product-pagination="<?= htmlspecialchars($target, ENT_QUOTES, 'UTF-8') ?>"
     aria-label="<?= htmlspecialchars(t('webshop_product_pagination_label', 'Product paginatie'), ENT_QUOTES, 'UTF-8') ?>"
     data-records="<?= $records ?>">
    <?php if ($totalPages > 1) { ?>
        <?php if ($startPage > 0) { ?>
            <a href="#" data-page="0" class="webshop-product-pagination__link<?= $currentPage === 0 ? ' is-active' : '' ?>">1</a>
            <?php if ($startPage > 1) { ?>
                <span class="webshop-product-pagination__gap">...</span>
            <?php } ?>
        <?php } ?>

        <?php for ($page = $startPage; $page <= $endPage; $page++) { ?>
            <a href="#"
               data-page="<?= $page ?>"
               class="webshop-product-pagination__link<?= $page === $currentPage ? ' is-active' : '' ?>"
               <?= $page === $currentPage ? 'aria-current="page"' : '' ?>>
                <?= $page + 1 ?>
            </a>
        <?php } ?>

        <?php if ($endPage < $totalPages - 1) { ?>
            <?php if ($endPage < $totalPages - 2) { ?>
                <span class="webshop-product-pagination__gap">...</span>
            <?php } ?>
            <a href="#"
               data-page="<?= $totalPages - 1 ?>"
               class="webshop-product-pagination__link<?= $currentPage === $totalPages - 1 ? ' is-active' : '' ?>">
                <?= $totalPages ?>
            </a>
        <?php } ?>

        <?php if ($currentPage < $totalPages - 1) { ?>
            <a href="#"
               data-page="<?= $currentPage + 1 ?>"
               class="webshop-product-pagination__link webshop-product-pagination__next"
               aria-label="<?= htmlspecialchars(t('webshop_product_pagination_next', 'Volgende pagina'), ENT_QUOTES, 'UTF-8') ?>">
                &rarr;
            </a>
        <?php } ?>
    <?php } ?>
</nav>
