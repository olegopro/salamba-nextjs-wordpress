<?php
/*
 * WPSPowerbox
 * @author   AcmeeDesign
 * @url     https://acmeedesign.com
*/


defined('ABSPATH') || die;

if( ! class_exists('Powerbox_Menu_Import_Export') ){

  class Powerbox_Menu_Import_Export extends WPSHAPERE
  {

    function __construct() {

      add_action( 'admin_menu', array( $this, 'wpspb_createmenu' ), 99 );
      add_action( 'plugins_loaded', array( $this, 'wpspb_import_sidebar_settings' ) );

    }

    function wpspb_createmenu() {
      add_submenu_page( WPSHAPERE_MENU_SLUG , __('Import and export Powerbox menus', 'powerbox'), __('Imp-exp Powerbox menus', 'powerbox'), 'manage_options', 'wpspb_impexport_sidebar', array($this, 'wpspb_settings_export_form') );
    }

    function wpspb_settings_export_form() {
        global $aof_options;
        ?>
        <div class="wrap wps-wrap">
          <h1><?php _e('Import/Export Sidebar Menus', 'powerbox'); ?></h1>
          <?php parent::wps_help_link(); ?>
    <?php
        if( isset($_GET['page']) && $_GET['page'] == 'wpspb_impexport_sidebar' && isset($_GET['status']) && $_GET['status'] == 'updated' )
        {
            ?>
            <div class="updated top">
                <p><strong><?php echo __('Settings Imported!', 'powerbox'); ?></strong></p>
            </div>
    <?php
        }
        elseif(isset($_GET['page']) && $_GET['page'] == 'wps_impexp_settings' && isset($_GET['status']) && $_GET['status'] == 'dataerror')
        {
            ?>
            <div class="updated top">
                <p><strong><?php echo __('You are importing empty data or wrong data format.', 'powerbox'); ?></strong></p>
            </div>
    <?php
        }

        ?>

            <h3><?php echo __('Export Settings', 'powerbox'); ?></h3>
            <div style="padding: 15px 0">
            <span><?php echo __('Save the below contents to a text file.', 'powerbox'); ?></span>
            <textarea class="widefat" rows="10" ><?php echo $this->get_sidebar_menus(); ?></textarea>
            </div>

            <h3><?php echo __('Import Settings', 'powerbox'); ?></h3>
            <div style="padding:15px 0">
            <form name="wps_import_settings_form" method="post" action="">
              <input type="hidden" name="wps_import_settings" value="1" />
              <textarea class="widefat" name="wpspb_sidebar_settings" rows="10" ></textarea><br /><br />
              <input class="button button-primary button-hero" type="submit" value="<?php echo __('Import Settings', 'powerbox'); ?>" />
            <?php wp_nonce_field('wpspb_sidebar_settings_nonce','wpspb_sidebar_settings_field'); ?>
            </form>
            </div>

        </div>

<?php
    }

    function wpspb_import_sidebar_settings() {

      global $wpdb;

      if( isset($_POST['wpspb_sidebar_settings_field'] ) ) {
          //if nonce not set, exit!
          if( !wp_verify_nonce( $_POST['wpspb_sidebar_settings_field'], 'wpspb_sidebar_settings_nonce' ) )
            exit();

          $import_data = wp_kses_post( trim( $_POST['wpspb_sidebar_settings'] ) );

          if( empty( $import_data ) ) {
              wp_safe_redirect( admin_url( 'admin.php?page=wpspb_impexport_sidebar&status=dataerror' ) );
              exit();
          }
          else {

              $data = json_decode( $import_data );

              if( is_object( $data ) ) {
                foreach ( $data as $menu_id => $menu_data ) {
                  $wpdb->insert(
                    POWERBOX_MENU_TABLE,
                    array(
                      'menu_title' => $menu_data->menu_title,
                      'menu_data' => $menu_data->menu_data,
                      'user_type' => $menu_data->user_type,
                      'menu_role_id' => $menu_data->menu_role_id,
                      'status' => $menu_data->status,
                      'other_data' => $menu_data->other_data,
                    )
                  );
                }
              }
              wp_safe_redirect( admin_url( 'admin.php?page=wpspb_impexport_sidebar&status=updated' ) );
              exit();
          }
      }

    }

    function get_sidebar_menus() {

      global $wpdb;

      $wps_custom_menus = $wpdb->get_results( "SELECT * FROM " . POWERBOX_MENU_TABLE );
      $data = array();
      foreach ( $wps_custom_menus as $menu_data ) {
        $data[$menu_data->id] = array(
          'menu_title' => $menu_data->menu_title,
          'menu_data' => $menu_data->menu_data,
          'user_type' => $menu_data->user_type,
          'menu_role_id' => $menu_data->menu_role_id,
          'status' => $menu_data->status,
          'other_data' => $menu_data->other_data,
        );
      }

      if( !empty( $data ) )
        return json_encode( $data );

    }

  }

}

new Powerbox_Menu_Import_Export();
