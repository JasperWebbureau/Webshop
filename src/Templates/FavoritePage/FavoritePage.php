<section class="webshop-favorite-page">
    <div class="webshop-favorite-page__header">
        <h1><?=t('webshop_favorite_page_title', 'Favorieten')?></h1>
        <p><?=sprintf(t('webshop_favorite_page_count', '%d producten'), count($entities ?? []))?></p>
    </div>

    <?php if (empty($entities)) { ?>
        <div class="webshop-favorite-page__empty">
            <i class="fas fa-heart" aria-hidden="true"></i>
            <h2><?=t('webshop_favorite_page_empty_title', 'Nog geen favorieten')?></h2>
            <p><?=t('webshop_favorite_page_empty_text', 'Bewaar producten met het hartje en vind ze hier snel terug.')?></p>
        </div>
    <?php } else { ?>
        <grid class="webshop-favorite-page__grid">
            <?php foreach ($entities as $entity) { ?>
                <?=new \Flexgrid\Response\TemplateResponse($card, [
                    'entity' => $entity,
                    'pageId' => $pageId,
                    'cardWidth' => $cardWidth,
                    'parentWidth' => $parentWidth,
                ])?>
            <?php } ?>
        </grid>
    <?php } ?>
</section>
