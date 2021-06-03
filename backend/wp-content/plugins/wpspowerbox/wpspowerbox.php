<?php
/*
Plugin Name: WPSPowerbox
Plugin URI: http://acmeedesign.com
Description: Addons plugin for WPShapere
Version: 2.1.4
Author: AcmeeDesign Softwares and Solutions
Author URI: http://acmeedesign.com
Text-Domain: powerbox
Domain Path: /languages
 *
*/

/*
*   POWERBOX Version
*/
define( 'POWERBOX_VERSION' , '2.1.4' );

/*
*   POWERBOX Path Constant
*/
define( 'POWERBOX_PATH' , dirname(__FILE__) . "/");

/*
*   POWERBOX URI Constant
*/
define( 'POWERBOX_DIR_URI' , plugin_dir_url(__FILE__) );

/*
*   POWERBOX Options slug constant
*/
define( 'POWERBOX_OPTIONS_SLUG' , 'powerbox_options' );

/*
*   POWERBOX users slug constant
*/
define( 'POWERBOX_USER_PAGE_SLUG' , 'powerbox_user_options' );

/*
*   POWERBOX fonts slug constant
*/
define( 'POWERBOX_FONT_PAGE_SLUG' , 'powerbox_font_options' );

/*
*   POWERBOX google fonts path constant
*/
define( 'POWERBOX_GOOGLE_FONT_PATH' , '//fonts.googleapis.com/css?' );

/*
*   POWERBOX redirect users slug constant
*/
define( 'POWERBOX_REDIRECT_USERS_SLUG' , 'powerbox_redirect_users');

/*
*   POWERBOX add menu slug constant
*/
define( 'POWERBOX_CUSTOM_MENU_SET', 'powerbox_custom_menu_set');

/*
*   POWERBOX meta box options slug constant
*/
define( 'POWERBOX_HIDE_META_BOXES', 'powerbox_hide_meta_boxes');

/*
*   POWERBOX custom menu table constant
*/
global $wpdb;
define( 'POWERBOX_MENU_TABLE', $wpdb->prefix . 'wps_custom_menu');

/*
*   POWERBOX custom menu version
*/
global $wps_menu_table_version;
$wps_menu_table_version = '1.0';
$wpspb_db_version = get_option('wps_menu_db_version');

function powerbox_load_textdomain()
{
   load_plugin_textdomain('powerbox', false, dirname( plugin_basename( __FILE__ ) )  . '/languages' );
}
add_action('plugins_loaded', 'powerbox_load_textdomain');

function powerbox_plugin_activate()
{
    add_option('powerbox_activated_plugin', POWERBOX_OPTIONS_SLUG);
}

register_activation_hook(__FILE__, 'powerbox_plugin_activate');


function load_plugin()
{
    /* do stuff once right after activation */
    if (is_admin() && get_option('powerbox_activated_plugin') == POWERBOX_OPTIONS_SLUG) {
        delete_option( 'powerbox_activated_plugin' );

        if (!class_exists('WPSHAPERE')) {
            add_action('admin_notices', 'powerbox_deactivate_notice');

            //Deactivate the plugin
            deactivate_plugins(plugin_basename(__FILE__));

            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }
        }
    }
}

//add_action('admin_init', 'load_plugin');


/**
* Create WPS menu table
* @since 2.0
*/
function wps_create_menu_table() {

  global $wpdb, $wps_menu_table_version;

  $wps_menu_table = POWERBOX_MENU_TABLE;
  $charset_collate = $wpdb->get_charset_collate();

  require_once( POWERBOX_PATH . 'includes/create-menu-table.php' );
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $create_menu_table );

  add_option( 'wps_menu_db_version', $wps_menu_table_version );

  //generate recovery key
  powerbox_recovery_key();

}
register_activation_hook( __FILE__ , 'wps_create_menu_table' );

if( empty( $wpspb_db_version ) ) {

  //create menu table
  wps_create_menu_table();

  //generate recovery key
  powerbox_recovery_key();

}

function powerbox_recovery_key() {

  //create recovery key
  $wps_recovery_key = powerbox_rand_key(12);
  add_option( 'wps_recovery_key', $wps_recovery_key );

}

/**
 * Display an error message when parent plugin is missing
 */
function powerbox_deactivate_notice()
{
    ?>
    <div class="error">
        <p><?php esc_html_e( 'Please install and activate WPShapere plugin before activating WPSPowerbox.', 'powerbox' ); ?></p>
    </div>
    <?php
}

function powerbox_rand_key($length) {
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

  $size = strlen( $chars );
  $str = "";
  for( $i = 0; $i < $length; $i++ ) {
          $str .= $chars[ rand( 0, $size - 1 ) ];
  }

  return $str;
}

if(class_exists('WPSHAPERE')) {
    //include plugin files
    include_once POWERBOX_PATH . 'includes/classes/aof.gfonts.class';
    include_once POWERBOX_PATH . 'includes/wpspb.admin.class.php';
    include_once POWERBOX_PATH . 'includes/wpspb.newmenu.class.php';
    include_once POWERBOX_PATH . 'includes/wpspb.user.class.php';
    include_once POWERBOX_PATH . 'includes/wpspb.font.class.php';
    include_once POWERBOX_PATH . 'includes/wpspb.redirectusers.class.php';
    include_once POWERBOX_PATH . 'includes/wpspb.metabox.class.php';
    include_once POWERBOX_PATH . 'includes/wpspb.impexport.class.php';
}
