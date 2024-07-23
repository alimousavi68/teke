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
// function enqueue_bootstrap() {
//     // بارگذاری استایل بوت‌استرپ
//     wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
//     // بارگذاری اسکریپت بوت‌استرپ
//     wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
// }

// add_action('admin_enqueue_scripts', 'enqueue_bootstrap');

// // Enqueue Bootstrap CSS and JS in Admin Area
// add_action( 'admin_enqueue_scripts', i8_enqueue_bootstrap());
// function i8_enqueue_bootstrap() {
//     wp_enqueue_style('bootstrap-css', plugins_url('assets/css/bootstrap.min.css', dirname(__FILE__)));
//     wp_enqueue_script('bootstrap-js', plugins_url('assets/js/bootstrap.min.js', dirname(__FILE__)), array('jquery'), '5.2', true);
//     // wp_add_inline_script('select2-js', 'jQuery(document).ready(function($) { $(".select2").select2(); });');

// }


