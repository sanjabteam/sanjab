<?php

namespace Sanjab\Helpers;

use ReflectionClass;

class MaterialColors
{
    public const COLOR_NAMES = [
        'RED',
        'PINK',
        'PURPLE',
        'DEEP_PURPLE',
        'INDIGO',
        'BLUE',
        'LIGHT_BLUE',
        'CYAN',
        'TEAL',
        'GREEN',
        'LIGHT_GREEN',
        'LIME',
        'YELLOW',
        'AMBER',
        'ORANGE',
        'DEEP_ORANGE',
        'BROWN',
        'GREY',
        'BLUE_GREY',
    ];

    public const RED = '#f44336';
    public const RED_50 = '#ffebee';
    public const RED_100 = '#ffcdd2';
    public const RED_200 = '#ef9a9a';
    public const RED_300 = '#e57373';
    public const RED_400 = '#ef5350';
    public const RED_500 = '#f44336';
    public const RED_600 = '#e53935';
    public const RED_700 = '#d32f2f';
    public const RED_800 = '#c62828';
    public const RED_900 = '#b71c1c';
    public const RED_A100 = '#ff8a80';
    public const RED_A200 = '#ff5252';
    public const RED_A400 = '#ff1744';
    public const RED_A700 = '#d50000';

    public const PINK = '#e91e63';
    public const PINK_50 = '#fce4ec';
    public const PINK_100 = '#f8bbd0';
    public const PINK_200 = '#f48fb1';
    public const PINK_300 = '#f06292';
    public const PINK_400 = '#ec407a';
    public const PINK_500 = '#e91e63';
    public const PINK_600 = '#d81b60';
    public const PINK_700 = '#c2185b';
    public const PINK_800 = '#ad1457';
    public const PINK_900 = '#880e4f';
    public const PINK_A100 = '#ff80ab';
    public const PINK_A200 = '#ff4081';
    public const PINK_A400 = '#f50057';
    public const PINK_A700 = '#c51162';

    public const PURPLE = '#9c27b0';
    public const PURPLE_50 = '#f3e5f5';
    public const PURPLE_100 = '#e1bee7';
    public const PURPLE_200 = '#ce93d8';
    public const PURPLE_300 = '#ba68c8';
    public const PURPLE_400 = '#ab47bc';
    public const PURPLE_500 = '#9c27b0';
    public const PURPLE_600 = '#8e24aa';
    public const PURPLE_700 = '#7b1fa2';
    public const PURPLE_800 = '#6a1b9a';
    public const PURPLE_900 = '#4a148c';
    public const PURPLE_A100 = '#ea80fc';
    public const PURPLE_A200 = '#e040fb';
    public const PURPLE_A400 = '#d500f9';
    public const PURPLE_A700 = '#a0f';

    public const DEEP_PURPLE = '#673ab7';
    public const DEEP_PURPLE_50 = '#ede7f6';
    public const DEEP_PURPLE_100 = '#d1c4e9';
    public const DEEP_PURPLE_200 = '#b39ddb';
    public const DEEP_PURPLE_300 = '#9575cd';
    public const DEEP_PURPLE_400 = '#7e57c2';
    public const DEEP_PURPLE_500 = '#673ab7';
    public const DEEP_PURPLE_600 = '#5e35b1';
    public const DEEP_PURPLE_700 = '#512da8';
    public const DEEP_PURPLE_800 = '#4527a0';
    public const DEEP_PURPLE_900 = '#311b92';
    public const DEEP_PURPLE_A100 = '#b388ff';
    public const DEEP_PURPLE_A200 = '#7c4dff';
    public const DEEP_PURPLE_A400 = '#651fff';
    public const DEEP_PURPLE_A700 = '#6200ea';

    public const INDIGO = '#3f51b5';
    public const INDIGO_50 = '#e8eaf6';
    public const INDIGO_100 = '#c5cae9';
    public const INDIGO_200 = '#9fa8da';
    public const INDIGO_300 = '#7986cb';
    public const INDIGO_400 = '#5c6bc0';
    public const INDIGO_500 = '#3f51b5';
    public const INDIGO_600 = '#3949ab';
    public const INDIGO_700 = '#303f9f';
    public const INDIGO_800 = '#283593';
    public const INDIGO_900 = '#1a237e';
    public const INDIGO_A100 = '#8c9eff';
    public const INDIGO_A200 = '#536dfe';
    public const INDIGO_A400 = '#3d5afe';
    public const INDIGO_A700 = '#304ffe';

    public const BLUE = '#2196f3';
    public const BLUE_50 = '#e3f2fd';
    public const BLUE_100 = '#bbdefb';
    public const BLUE_200 = '#90caf9';
    public const BLUE_300 = '#64b5f6';
    public const BLUE_400 = '#42a5f5';
    public const BLUE_500 = '#2196f3';
    public const BLUE_600 = '#1e88e5';
    public const BLUE_700 = '#1976d2';
    public const BLUE_800 = '#1565c0';
    public const BLUE_900 = '#0d47a1';
    public const BLUE_A100 = '#82b1ff';
    public const BLUE_A200 = '#448aff';
    public const BLUE_A400 = '#2979ff';
    public const BLUE_A700 = '#2962ff';

    public const LIGHT_BLUE = '#03a9f4';
    public const LIGHT_BLUE_50 = '#e1f5fe';
    public const LIGHT_BLUE_100 = '#b3e5fc';
    public const LIGHT_BLUE_200 = '#81d4fa';
    public const LIGHT_BLUE_300 = '#4fc3f7';
    public const LIGHT_BLUE_400 = '#29b6f6';
    public const LIGHT_BLUE_500 = '#03a9f4';
    public const LIGHT_BLUE_600 = '#039be5';
    public const LIGHT_BLUE_700 = '#0288d1';
    public const LIGHT_BLUE_800 = '#0277bd';
    public const LIGHT_BLUE_900 = '#01579b';
    public const LIGHT_BLUE_A100 = '#80d8ff';
    public const LIGHT_BLUE_A200 = '#40c4ff';
    public const LIGHT_BLUE_A400 = '#00b0ff';
    public const LIGHT_BLUE_A700 = '#0091ea';

    public const CYAN = '#00bcd4';
    public const CYAN_50 = '#e0f7fa';
    public const CYAN_100 = '#b2ebf2';
    public const CYAN_200 = '#80deea';
    public const CYAN_300 = '#4dd0e1';
    public const CYAN_400 = '#26c6da';
    public const CYAN_500 = '#00bcd4';
    public const CYAN_600 = '#00acc1';
    public const CYAN_700 = '#0097a7';
    public const CYAN_800 = '#00838f';
    public const CYAN_900 = '#006064';
    public const CYAN_A100 = '#84ffff';
    public const CYAN_A200 = '#18ffff';
    public const CYAN_A400 = '#00e5ff';
    public const CYAN_A700 = '#00b8d4';

    public const TEAL = '#009688';
    public const TEAL_50 = '#e0f2f1';
    public const TEAL_100 = '#b2dfdb';
    public const TEAL_200 = '#80cbc4';
    public const TEAL_300 = '#4db6ac';
    public const TEAL_400 = '#26a69a';
    public const TEAL_500 = '#009688';
    public const TEAL_600 = '#00897b';
    public const TEAL_700 = '#00796b';
    public const TEAL_800 = '#00695c';
    public const TEAL_900 = '#004d40';
    public const TEAL_A100 = '#a7ffeb';
    public const TEAL_A200 = '#64ffda';
    public const TEAL_A400 = '#1de9b6';
    public const TEAL_A700 = '#00bfa5';

    public const GREEN = '#4caf50';
    public const GREEN_50 = '#e8f5e9';
    public const GREEN_100 = '#c8e6c9';
    public const GREEN_200 = '#a5d6a7';
    public const GREEN_300 = '#81c784';
    public const GREEN_400 = '#66bb6a';
    public const GREEN_500 = '#4caf50';
    public const GREEN_600 = '#43a047';
    public const GREEN_700 = '#388e3c';
    public const GREEN_800 = '#2e7d32';
    public const GREEN_900 = '#1b5e20';
    public const GREEN_A100 = '#b9f6ca';
    public const GREEN_A200 = '#69f0ae';
    public const GREEN_A400 = '#00e676';
    public const GREEN_A700 = '#00c853';

    public const LIGHT_GREEN = '#8bc34a';
    public const LIGHT_GREEN_50 = '#f1f8e9';
    public const LIGHT_GREEN_100 = '#dcedc8';
    public const LIGHT_GREEN_200 = '#c5e1a5';
    public const LIGHT_GREEN_300 = '#aed581';
    public const LIGHT_GREEN_400 = '#9ccc65';
    public const LIGHT_GREEN_500 = '#8bc34a';
    public const LIGHT_GREEN_600 = '#7cb342';
    public const LIGHT_GREEN_700 = '#689f38';
    public const LIGHT_GREEN_800 = '#558b2f';
    public const LIGHT_GREEN_900 = '#33691e';
    public const LIGHT_GREEN_A100 = '#ccff90';
    public const LIGHT_GREEN_A200 = '#b2ff59';
    public const LIGHT_GREEN_A400 = '#76ff03';
    public const LIGHT_GREEN_A700 = '#64dd17';

    public const LIME = '#cddc39';
    public const LIME_50 = '#f9fbe7';
    public const LIME_100 = '#f0f4c3';
    public const LIME_200 = '#e6ee9c';
    public const LIME_300 = '#dce775';
    public const LIME_400 = '#d4e157';
    public const LIME_500 = '#cddc39';
    public const LIME_600 = '#c0ca33';
    public const LIME_700 = '#afb42b';
    public const LIME_800 = '#9e9d24';
    public const LIME_900 = '#827717';
    public const LIME_A100 = '#f4ff81';
    public const LIME_A200 = '#eeff41';
    public const LIME_A400 = '#c6ff00';
    public const LIME_A700 = '#aeea00';

    public const YELLOW = '#ffeb3b';
    public const YELLOW_50 = '#fffde7';
    public const YELLOW_100 = '#fff9c4';
    public const YELLOW_200 = '#fff59d';
    public const YELLOW_300 = '#fff176';
    public const YELLOW_400 = '#ffee58';
    public const YELLOW_500 = '#ffeb3b';
    public const YELLOW_600 = '#fdd835';
    public const YELLOW_700 = '#fbc02d';
    public const YELLOW_800 = '#f9a825';
    public const YELLOW_900 = '#f57f17';
    public const YELLOW_A100 = '#ffff8d';
    public const YELLOW_A200 = '#ff0';
    public const YELLOW_A400 = '#ffea00';
    public const YELLOW_A700 = '#ffd600';

    public const AMBER = '#ffc107';
    public const AMBER_50 = '#fff8e1';
    public const AMBER_100 = '#ffecb3';
    public const AMBER_200 = '#ffe082';
    public const AMBER_300 = '#ffd54f';
    public const AMBER_400 = '#ffca28';
    public const AMBER_500 = '#ffc107';
    public const AMBER_600 = '#ffb300';
    public const AMBER_700 = '#ffa000';
    public const AMBER_800 = '#ff8f00';
    public const AMBER_900 = '#ff6f00';
    public const AMBER_A100 = '#ffe57f';
    public const AMBER_A200 = '#ffd740';
    public const AMBER_A400 = '#ffc400';
    public const AMBER_A700 = '#ffab00';

    public const ORANGE = '#ff9800';
    public const ORANGE_50 = '#fff3e0';
    public const ORANGE_100 = '#ffe0b2';
    public const ORANGE_200 = '#ffcc80';
    public const ORANGE_300 = '#ffb74d';
    public const ORANGE_400 = '#ffa726';
    public const ORANGE_500 = '#ff9800';
    public const ORANGE_600 = '#fb8c00';
    public const ORANGE_700 = '#f57c00';
    public const ORANGE_800 = '#ef6c00';
    public const ORANGE_900 = '#e65100';
    public const ORANGE_A100 = '#ffd180';
    public const ORANGE_A200 = '#ffab40';
    public const ORANGE_A400 = '#ff9100';
    public const ORANGE_A700 = '#ff6d00';

    public const DEEP_ORANGE = '#ff5722';
    public const DEEP_ORANGE_50 = '#fbe9e7';
    public const DEEP_ORANGE_100 = '#ffccbc';
    public const DEEP_ORANGE_200 = '#ffab91';
    public const DEEP_ORANGE_300 = '#ff8a65';
    public const DEEP_ORANGE_400 = '#ff7043';
    public const DEEP_ORANGE_500 = '#ff5722';
    public const DEEP_ORANGE_600 = '#f4511e';
    public const DEEP_ORANGE_700 = '#e64a19';
    public const DEEP_ORANGE_800 = '#d84315';
    public const DEEP_ORANGE_900 = '#bf360c';
    public const DEEP_ORANGE_A100 = '#ff9e80';
    public const DEEP_ORANGE_A200 = '#ff6e40';
    public const DEEP_ORANGE_A400 = '#ff3d00';
    public const DEEP_ORANGE_A700 = '#dd2c00';

    public const BROWN = '#795548';
    public const BROWN_50 = '#efebe9';
    public const BROWN_100 = '#d7ccc8';
    public const BROWN_200 = '#bcaaa4';
    public const BROWN_300 = '#a1887f';
    public const BROWN_400 = '#8d6e63';
    public const BROWN_500 = '#795548';
    public const BROWN_600 = '#6d4c41';
    public const BROWN_700 = '#5d4037';
    public const BROWN_800 = '#4e342e';
    public const BROWN_900 = '#3e2723';
    public const BROWN_A100 = '#d7ccc8';
    public const BROWN_A200 = '#bcaaa4';
    public const BROWN_A400 = '#8d6e63';
    public const BROWN_A700 = '#5d4037';

    public const GREY = '#9e9e9e';
    public const GREY_50 = '#fafafa';
    public const GREY_100 = '#f5f5f5';
    public const GREY_200 = '#eee';
    public const GREY_300 = '#e0e0e0';
    public const GREY_400 = '#bdbdbd';
    public const GREY_500 = '#9e9e9e';
    public const GREY_600 = '#757575';
    public const GREY_700 = '#616161';
    public const GREY_800 = '#424242';
    public const GREY_900 = '#212121';
    public const GREY_A100 = '#f5f5f5';
    public const GREY_A200 = '#eee';
    public const GREY_A400 = '#bdbdbd';
    public const GREY_A700 = '#616161';

    public const BLUE_GREY = '#607d8b';
    public const BLUE_GREY_50 = '#eceff1';
    public const BLUE_GREY_100 = '#cfd8dc';
    public const BLUE_GREY_200 = '#b0bec5';
    public const BLUE_GREY_300 = '#90a4ae';
    public const BLUE_GREY_400 = '#78909c';
    public const BLUE_GREY_500 = '#607d8b';
    public const BLUE_GREY_600 = '#546e7a';
    public const BLUE_GREY_700 = '#455a64';
    public const BLUE_GREY_800 = '#37474f';
    public const BLUE_GREY_900 = '#263238';
    public const BLUE_GREY_A100 = '#cfd8dc';
    public const BLUE_GREY_A200 = '#b0bec5';
    public const BLUE_GREY_A400 = '#78909c';
    public const BLUE_GREY_A700 = '#455a64';

    /**
     * Get all colors with specific lightness as array
     *
     * @param string $lightness
     * @return array
     */
    public static function colors(string $lightness = '500')
    {
        return array_filter(
            (new ReflectionClass(static::class))->getConstants(),
            function ($key) use ($lightness) {
                return preg_match('/.*_'.$lightness.'/', $key);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Get a random material color.
     *
     * @param string $lightness  light/darkness of color.
     * @return string
     */
    public static function random(string $lightness = '500')
    {
        return constant('static::'.array_random(static::COLOR_NAMES).'_'.$lightness);
    }

    /**
     * Array of random colors.
     *
     * @param int $count         how many colors needed.
     * @param string $lightness  light/darkness of color.
     * @return string[]
     */
    public static function randomArray(int $count, string $lightness = '500')
    {
        $colors = static::colors($lightness);
        while (count($colors) < $count) {
            $colors = array_merge($colors, static::colors($lightness));
        }
        shuffle($colors);
        return array_slice($colors, 0, $count);
    }
}
