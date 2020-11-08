<?php

if (function_exists('acf_add_options_page') && current_user_can('manage_options')) {
    acf_add_options_page([
        'page_title' 	=> 'Общие настройки',
        'menu_title'	=> 'Опции темы',
        'menu_slug' 	=> 'theme-general-settings',
        'capability'	=> 'edit_posts',
        'parent_slug'	=> 'themes.php',
        'redirect'		=> false
    ]);
}