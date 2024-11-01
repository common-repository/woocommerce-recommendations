<?php
/**
 * Plugin Name: WooCommerce Recommendation
 * Plugin URI: http://ntoklo.com
 * Description: A plugin that provides recommendations for the WooCommerce e-Commerce plugin.
 * Version: : 1.0.1
 * Author: ntoklo.com
 * Author URI: http://ntoklo.com
 * License: A "Slug" license name e.g. GPL2
 */
define( 'WOORMD_FILE_PATH', dirname( __FILE__ ) );
define( 'WOORMD_DIR_NAME', basename( WOORMD_FILE_PATH ) );
define( 'WOORMD_FOLDER', dirname( plugin_basename( __FILE__ ) ) );
define(	'WOORMD_NAME', plugin_basename(__FILE__) );
define( 'WOORMD_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
define( 'WOORMD_DIR', WP_CONTENT_DIR . '/plugins/' . WOORMD_FOLDER );
define( 'WOORMD_JS_URL',  WOORMD_URL . '/assets/js' );
define( 'WOORMD_CSS_URL',  WOORMD_URL . '/assets/css' );
define( 'WOORMD_IMAGES_URL',  WOORMD_URL . '/img' );

include 'libs/Curl.php';
include 'util/class-wc-setcookie.php';
include 'util/class-wc-widget-config.php';
include 'util/get_version.php';

include 'util/class-wc-rec-shortcode.php';
include 'util/class-wc-chart-shortcode.php';

include 'classes/Data.php';
include 'classes/Universal_Variable.php';

include 'classes/class-wc-event-get.php';
include 'classes/class-wc-event-post.php';

include 'widgets/recommendation_widget.php';
include 'widgets/chart_widget.php';

include 'admin/classes/class-wc-admin-tabs.php';
include 'admin/recommendation_init.php';

$Data_Object = new Data_Object();
$Data_Object->load();

$Universal_Variable = new Universal_Variable();
$Universal_Variable->load();

$event_get = new Event_Get();
$event_get->load();

$event_post = new Event_Post();
$event_post->load();

register_activation_hook(__FILE__, 'woorec_activate_plugin');

register_deactivation_hook(__FILE__, 'woorec_deactivate_plugin');
?>
