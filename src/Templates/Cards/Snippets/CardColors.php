<?php

if (!function_exists('webshopCardAccentColors')) {
    function webshopCardAccentColors(): array
    {
        return [
            '#094C58',
            '#D35056',
            '#D78405',
            '#37729B',
        ];
    }
}

if (!function_exists('webshopCardAccentColor')) {
    function webshopCardAccentColor(int $index = 0): string
    {
        $colors = webshopCardAccentColors();
        if (empty($colors)) {
            return '#094C58';
        }

        return $colors[((int)$index % count($colors) + count($colors)) % count($colors)];
    }
}
