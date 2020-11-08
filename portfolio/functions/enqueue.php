<?php

//добавляет скрипты и стили
function portfolio_enqueue(){
    // стили
    wp_enqueue_style('main_style', get_template_directory_uri() . '/assets/styles/main.css', false, '2');

    // отключаем текущий jquery
    wp_deregister_script( 'jquery' );

    // подключаем свой jquery
     wp_register_script('jquery', get_template_directory_uri() . '/assets/scripts/jquery-3.5.1.min.js' , false, '3.5.1', true);
     wp_enqueue_script( 'jquery' );

    // скрипт темы
    wp_enqueue_script('interactions_script', get_template_directory_uri() . '/assets/scripts/script.js', false, '1.0.0', true);

    // основной скрипт
    wp_enqueue_script('main_script', get_template_directory_uri() . '/assets/scripts/main.js', false, '1.0.0', true);

    // mail скрипт
    wp_enqueue_script('mail_script', get_template_directory_uri() . '/mailer/mail.js', false, '1.0.0', true);
}

add_action('wp_enqueue_scripts', 'portfolio_enqueue');