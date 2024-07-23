<?php 
/*
Plugin Name: سیستم رزرواسیون تکه
Description: این افزونه اختصاصی برای رزرواسیون تکه طراحی شده است
Version: 1.0
Author: علی موسوی |
Author URI: https://ihasht.ir
*/


// Define Variables
define( 'TEKE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TEKE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );


// Includes File
require_once( plugin_dir_path( __FILE__ ) . 'inc/helper_functions.php');
require_once( plugin_dir_path( __FILE__ ) . 'inc/events_post_type.php');
require_once( plugin_dir_path( __FILE__ ) . 'inc/registrations.php');



