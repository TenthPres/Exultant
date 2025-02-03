<?php

/* language switcher */

$languages = apply_filters('wpml_active_languages', [], 'orderby=id&order=desc'); // consider adding 'skip_missing=0' or =1 to the query


if (count($languages) > 0) {

    // get item from languages where active is true
    $activeLang = array_filter($languages, function ($lang) {return $lang['active'] === 1;});
    $activeLang = array_shift($languages);

    $activeFlag = $activeLang['country_flag_url'];
    $activeName = $activeLang['translated_name'];

    echo "<label style=\"\" class=\"las la-globe\"></label>";
    echo "<ul>";

    foreach ($languages as $lang) {
        $flag = $lang['country_flag_url'];
        $name = $lang['native_name'];
        $url  = esc_url($lang['url']);

        if ($lang['active'] === 1) {
            continue;
        } else {
            echo "<li><a href=\"$url\"><img src=\"$flag\" alt=\"$name\" /></a></li>";
        }
    }

    echo "</ul>";
}
