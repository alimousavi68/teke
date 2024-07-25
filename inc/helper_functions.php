<?php


function enqueue_bootstrap()
{
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
        integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <?php
}

add_action('admin_enqueue_scripts', 'enqueue_bootstrap');
add_action('wp_enqueue_scripts', 'enqueue_bootstrap');

function custom_theme_setup() {
    // اضافه کردن پشتیبانی منو
    add_theme_support('menus');
    
    // ثبت منوها
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'textdomain'),
        'footer' => __('Footer Menu', 'textdomain')
    ));
}
add_action('after_setup_theme', 'custom_theme_setup');


function my_enqueue_scripts() {
    wp_enqueue_script('jquery'); // اطمینان از بارگذاری jQuery
    // wp_enqueue_script('my-custom-script', get_template_directory_uri() . '/js/my-custom-script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'my_enqueue_scripts');





