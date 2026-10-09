<?php

/**
 * @var string $eventClass
 * @var int $pageId
 * @var string $card
 * @var int $limit
 */

use Flexgrid\Event\AjaxEvent;

$instance = 'webshop-search-' . bin2hex(random_bytes(6));
$popoverId = $instance . '-popover';
$event = new AjaxEvent((string)$eventClass, 'searchSuggestions');
$event->setMinimumAccessLevel(0);
$event->setMethodArguments((int)$pageId);
$event->setMethodArguments((string)$card);
$event->setMethodArguments((int)$limit);
?>
<div
    class="webshop-header-summary webshop-search"
    data-webshop-search
    data-webshop-search-hint="<?=htmlspecialchars(t('webshop_search_hint', 'Typ minstens 3 tekens om te zoeken.'), ENT_QUOTES, 'UTF-8')?>"
    data-webshop-search-loading="<?=htmlspecialchars(t('webshop_search_loading', 'Zoeken...'), ENT_QUOTES, 'UTF-8')?>"
>
    <button
        type="button"
        class="webshop-header-summary__trigger webshop-search__trigger"
        aria-label="<?=htmlspecialchars(t('webshop_search_open', 'Zoek producten'), ENT_QUOTES, 'UTF-8')?>"
        aria-expanded="false"
        aria-controls="<?=htmlspecialchars($popoverId, ENT_QUOTES, 'UTF-8')?>"
        data-webshop-search-trigger
    >
        <span class="webshop-header-summary__icon"><i class="fas fa-search" aria-hidden="true"></i></span>
    </button>

    <div class="webshop-header-summary__popover webshop-search__popover" id="<?=htmlspecialchars($popoverId, ENT_QUOTES, 'UTF-8')?>" aria-hidden="true">
        <form class="webshop-search__form" ajax="true" action="<?=htmlspecialchars($event->getName(), ENT_QUOTES, 'UTF-8')?>" role="search" data-webshop-search-form>
            <input type="hidden" name="search_instance" value="<?=htmlspecialchars($instance, ENT_QUOTES, 'UTF-8')?>">
            <label for="<?=htmlspecialchars($instance, ENT_QUOTES, 'UTF-8')?>">
                <?=htmlspecialchars(t('webshop_search_label', 'Zoek een product'), ENT_QUOTES, 'UTF-8')?>
            </label>
            <input
                id="<?=htmlspecialchars($instance, ENT_QUOTES, 'UTF-8')?>"
                type="search"
                name="q"
                autocomplete="off"
                placeholder="<?=htmlspecialchars(t('webshop_search_placeholder', 'Zoek op product of artikelnummer'), ENT_QUOTES, 'UTF-8')?>"
                data-webshop-search-input
            >
        </form>
        <div class="webshop-search__results" data-webshop-search-results="<?=htmlspecialchars($instance, ENT_QUOTES, 'UTF-8')?>" aria-live="polite">
            <p class="webshop-search__message"><?=htmlspecialchars(t('webshop_search_hint', 'Typ minstens 3 tekens om te zoeken.'), ENT_QUOTES, 'UTF-8')?></p>
        </div>
    </div>
</div>
