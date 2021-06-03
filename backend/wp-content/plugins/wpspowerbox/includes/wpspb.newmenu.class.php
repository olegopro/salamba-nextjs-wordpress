<?php
/*
 * WPSPowerbox
 * @author   AcmeeDesign
 * @url     http://acmeedesign.com
*/

defined('ABSPATH') || die;

if( !class_exists('NEWWPSMENU') ){
    class NEWWPSMENU extends WPSHAPERE
    {
        private static $instance;
        private $external_menu_data = array();
        function __construct() {
            $this->aof_options = parent::get_wps_option_data( WPSHAPERE_OPTIONS_SLUG );
            add_action( 'plugins_loaded', array( $this, 'create_table') );
            add_action( 'admin_menu', array( $this, 'wpspb_createmenu' ), 21 );
            add_action( 'plugins_loaded', array( $this, 'wps_save_menu_data' ), 1 );
            add_action( 'admin_enqueue_scripts', array( $this, 'wpspb_assets' ) );
            add_action( 'admin_head', array( $this, 'remove_wp_menu') );
            add_action( 'wp_logout', array( $this, 'remove_menu_cookie') );
            add_action( 'admin_head', array($this, 'remove_adminbar'), 9990 );
            add_action( 'in_admin_header', array( $this, 'wpspb_sidebar_menu'), 999 );
            add_filter( 'admin_body_class', array( $this, 'wpspb_body_classes' ) );
            add_action( 'admin_menu', array( $this, 'restrict_urls' ) );
        }

        function wpspb_assets( $nowpage )
        {
          wp_enqueue_style( 'wpspb-sidebar', POWERBOX_DIR_URI . 'assets/css/wpspb-sidebar.min.css', '', POWERBOX_VERSION );
          wp_enqueue_style( 'sidebar-cScrollbar', POWERBOX_DIR_URI . 'assets/css/jquery.mCustomScrollbar.min.css', '', POWERBOX_VERSION );
          wp_enqueue_style( 'powerbox-styles', POWERBOX_DIR_URI . 'assets/css/powerbox.min.css', '', POWERBOX_VERSION );
          wp_enqueue_script( 'jscookie', POWERBOX_DIR_URI . 'assets/js/js.cookie.js', '', '', true );
          wp_enqueue_script( 'wpspb-sidebar-mainjs', POWERBOX_DIR_URI . 'assets/sidebar/js/main.min.js', array( 'jquery' ), '', true );
          wp_enqueue_script( 'wpspb-sidebar-cScrollbar', POWERBOX_DIR_URI . 'assets/sidebar/js/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), '', true );

          if($nowpage == 'wpshapere_page_powerbox_custom_menu_set') {
            remove_action('admin_notices', 'update_nag', 3);
            wp_enqueue_style( 'domenu', POWERBOX_DIR_URI . 'assets/css/powerbox-menu-ui.min.css', '', POWERBOX_VERSION );
            wp_enqueue_script( 'wpspb-menujs', POWERBOX_DIR_URI . 'assets/js/jquery.wpspb-menu.min.js', array('jquery'), '', true );
            wp_enqueue_style( 'iconPicker-styles', POWERBOX_DIR_URI . 'assets/icon-picker/css/icon-picker.css', '', WPSHAPERE_VERSION );
            wp_enqueue_script( 'iconPicker', POWERBOX_DIR_URI . 'assets/icon-picker/js/icon-picker.js', array( 'jquery' ), '', true );
          }

          $menu_data = '';

          if( isset( $_GET['action'] ) && $_GET['action'] == 'wps_edit_menu' ) {

            global $wpdb;

            $wps_menu_id = ( isset($_GET['menu_id']) ) ? $_GET['menu_id'] : '';
            if (!empty( $wps_menu_id ) ) {
              $wps_menu_data = $wpdb->get_row( "SELECT menu_data FROM " . POWERBOX_MENU_TABLE . " WHERE id = $wps_menu_id" );
              $menu_data = $wps_menu_data->menu_data;
            }

          }

          include_once POWERBOX_PATH . 'includes/wpspb-menu-data.php';

        }

        public function wpspb_createmenu() {
          add_submenu_page( WPSHAPERE_MENU_SLUG , __('Powerbox Custom Menu Set', 'powerbox'), __('Powerbox Menus', 'powerbox'), 'manage_options', POWERBOX_CUSTOM_MENU_SET, array($this, 'wpspb_createmenu_form') );
        }

        function wps_save_menu_data() {

          global $wpdb;

          if( isset($_POST) && isset($_POST['custom_admin_menu_set']) ) {

            $menu_title = ( isset( $_POST['wps_menu_title'] ) && !empty( $_POST['wps_menu_title'] ) ) ? $_POST['wps_menu_title'] : '';
            $menu_data = ( isset( $_POST['wps_custom_menu_set_data'] ) && !empty( $_POST['wps_custom_menu_set_data'] ) ) ? $_POST['wps_custom_menu_set_data'] : '';
            $menu_role_id = ( isset( $_POST['user_role_id'] ) && !empty( $_POST['user_role_id'] ) ) ? $_POST['user_role_id'] : '';
            $wps_edit_menu_id = ( isset( $_POST['wps_edit_menu_id'] ) && !empty( $_POST['wps_edit_menu_id'] ) ) ? $_POST['wps_edit_menu_id'] : '';
            $menu_status = ( isset( $_POST['wps_menu_status'] ) && !empty( $_POST['wps_menu_status'] ) ) ? $_POST['wps_menu_status'] : 'publish';
            $block_urls = ( isset( $_POST['block_urls'] ) && !empty( $_POST['block_urls'] ) ) ? $_POST['block_urls'] : '';
            $unblock_urls = ( isset( $_POST['unblock_urls'] ) && !empty( $_POST['unblock_urls'] ) ) ? $_POST['unblock_urls'] : '';

            $other_data = '';
            if( !empty( $block_urls ) || !empty( $unblock_urls ) ) {
              $block_urls = str_replace( ' ', '', $block_urls );
              $blocked_urls =  explode( ',', $block_urls );

              $unblock_urls = str_replace( ' ', '', $unblock_urls );
              $unblocked_urls =  explode( ',', $unblock_urls );

              $other_data = array(
                'blocked_urls' => $blocked_urls,
                'unblocked_urls' => $unblocked_urls,
              );

              $other_data = maybe_serialize( $other_data );
            }

            if( !empty( $menu_title ) && !empty( $menu_data ) && !empty( $menu_role_id ) ) {

              if ( isset( $_POST['custom_admin_menu_set'] ) && $_POST['custom_admin_menu_set'] == 'menu_new' ) {

                $wpdb->insert(
                  POWERBOX_MENU_TABLE,
                  array(
                    'menu_title' => $menu_title,
                    'menu_data' => $menu_data,
                    'user_type' => '',
                    'menu_role_id' => $menu_role_id,
                    'status' => $menu_status,
                    'other_data' => $other_data,
                  )
                );

                $insert_id = $wpdb->insert_id;

                if( $insert_id ) {
                  wp_safe_redirect( admin_url( 'admin.php?page=' . POWERBOX_CUSTOM_MENU_SET . '&action=wps_edit_menu&menu_id=' . $insert_id ) );
                  exit();
                }
              }

              else {

                $wpdb->update(
                  POWERBOX_MENU_TABLE,
                  array(
                    'menu_title' => $menu_title,
                    'menu_data' => $menu_data,
                    'user_type' => '',
                    'menu_role_id' => $menu_role_id,
                    'status' => $menu_status,
                    'other_data' => $other_data,
                	),
                  array( 'ID' => $wps_edit_menu_id )
                );

                wp_safe_redirect( admin_url( 'admin.php?page=' . POWERBOX_CUSTOM_MENU_SET . '&action=wps_edit_menu&menu_id=' . $wps_edit_menu_id ) );
                exit();

              }

              wp_safe_redirect( admin_url( 'admin.php?page=' . POWERBOX_CUSTOM_MENU_SET ) );
              exit();
            }

          }

        }

        public function wpspb_get_capabilities()
        {
            $capability = array();
            $admin_role_set = get_role( 'administrator' )->capabilities;
            if(isset($admin_role_set) && !empty($admin_role_set)){
                foreach($admin_role_set as $key => $value){
                    $capability[] = $key;
                }
                return $capability;
            }

            return false;
        }

        function restrict_access() {
            $user = wp_get_current_user();
            if(isset($user->roles[0]))
                $user_role = $user->roles[0];
            else
                $user_role = 'no_role';
            if('administrator' != $user_role) {
        	$current_screen = get_current_screen();
        	$place = $current_screen->id;
        	if($place == 'edit-post' || $place == 'upload')
        	    wp_die('Restricted Access.');
            }
        }

        function wpspb_createmenu_form() {

          global $wpdb;

          if ( isset( $_GET['message'] ) && $_GET['message'] == 'deleted' ) {
            ?>
            <div class="notice updated is-dismissible">
              <p><?php echo __('Menu deleted successfully.', 'powerbox'); ?></p>
            </div>
            <?php
          }

          if ( isset( $_GET['action'] ) && $_GET['action'] == 'wps_delete_menu' && !empty( $_GET['menu_id'] ) ) {

            $menu_id = sanitize_text_field( $_GET['menu_id'] );

            $delete_wps_menu = $wpdb->delete(
              POWERBOX_MENU_TABLE,
              array(
                'id' => $menu_id,
              )
            );

            $response = ( $delete_wps_menu != false || is_numeric( $delete_wps_menu ) ) ? '&message=deleted' : '';

            wp_safe_redirect( admin_url( 'admin.php?page=' . POWERBOX_CUSTOM_MENU_SET . $response ) );
            exit();

          }

          $get_all_roles = parent::wps_get_wproles();
          $get_all_roles = array_reverse ( $get_all_roles );
          ?>

          <div id="powerbox-menu-formwrap" class="wrap powerbox-menu-formwrap">

            <div class="wps-infobox">
              <p class="recovery-code"><?php echo __('RECOVERY URL: ', 'powerbox'); ?> <strong><?php echo $this->wpspb_recovery_url(); ?></strong></p>
              <p><?php echo __('Copy and save the above link to a file on your local computer.
              You can use the recovery url to access the WPShapere settings in case if you have locked out of the system!', 'powerbox'); ?></p>
            </div>

          <?php ///$this->url_restrict_notice(); ?>

          <?php if( isset( $_GET['action'] ) && $_GET['action'] == 'wps_new_menu' ) {

            //get all registered menu sets
            $wps_custom_menus = $wpdb->get_results( "SELECT menu_role_id FROM " . POWERBOX_MENU_TABLE );

            $applied_roles = array();
            foreach ( $wps_custom_menus as $menu_role ) {
              unset( $get_all_roles[$menu_role->menu_role_id] );
            }

            include_once POWERBOX_PATH . 'includes/wps-menu-form.php';

           }
          elseif( isset( $_GET['action'] ) && $_GET['action'] == 'wps_edit_menu' ) {

              $wps_menu_id = ( isset($_GET['menu_id']) ) ? $_GET['menu_id'] : '';

              if (!empty( $wps_menu_id ) ) {

                //get all registered menu sets except the user role assigned to this menu set
                $wps_custom_menus = $wpdb->get_results( "SELECT menu_role_id FROM " . POWERBOX_MENU_TABLE . " WHERE id != $wps_menu_id" );

                $applied_roles = array();
                foreach ( $wps_custom_menus as $menu_role ) {
                  unset( $get_all_roles[$menu_role->menu_role_id] );
                }

                $wps_menu_data = $wpdb->get_row( "SELECT * FROM " . POWERBOX_MENU_TABLE . " WHERE id = $wps_menu_id" );
                $menu_title = $wps_menu_data->menu_title;
                $menu_data = $wps_menu_data->menu_data;
                $user_data = $wps_menu_data->menu_role_id;
                $status = $wps_menu_data->status;
                $other_data = maybe_unserialize( $wps_menu_data->other_data );
                $blocked_urls = ( !empty( $other_data['blocked_urls'] ) ) ? implode( ',', $other_data['blocked_urls'] ) : '';
                $unblocked_urls = ( !empty( $other_data['unblocked_urls'] ) ) ? implode( ',', $other_data['unblocked_urls'] ) : '';
                include_once POWERBOX_PATH . 'includes/wps-menu-form.php';

              }
          }
          elseif( isset( $_GET['action'] ) && $_GET['action'] == 'wps_clone_menu' ) {
              $wps_menu_id = ( isset($_GET['menu_id']) ) ? $_GET['menu_id'] : '';
              $response = '';
              if (!empty( $wps_menu_id ) ) {
                  $clone_wps_menu = $wpdb->query( "insert into " . POWERBOX_MENU_TABLE . " (menu_title, menu_data, user_type, menu_role_id, status, other_data) SELECT CONCAT(menu_title, ' - Copy'), menu_data, user_type, 'subscriber', status, other_data FROM " . POWERBOX_MENU_TABLE . " WHERE id = ".$wps_menu_id );
                  $response = ( $clone_wps_menu != false || is_numeric( $clone_wps_menu ) ) ? '&message=cloned' : '';
              }
              wp_safe_redirect( admin_url( 'admin.php?page=' . POWERBOX_CUSTOM_MENU_SET . $response ) );
              exit();
          }
          else {

            //get stored menu data
            $wps_custom_menus = $wpdb->get_results( "SELECT id, menu_title, menu_role_id, status FROM " . POWERBOX_MENU_TABLE );
            ?>
            <a href="<?php echo admin_url( 'admin.php?page=' . POWERBOX_CUSTOM_MENU_SET ); ?>&action=wps_new_menu" class="wps-add-new-menu-link page-title-action"><?php echo _e('Add new menu', 'powerbox'); ?></a>

            <table class="wps-menus-table wp-list-table widefat fixed striped pages">
            	<thead>
            	<tr>
                <th scope="col" id="title" class="manage-column column-title column-primary desc">
                  <span><?php echo __('Menu title', 'powerbox'); ?></span>
                </th>
                <th scope="col" id="appliesto" class="manage-column column-appliesto column-primary desc">
                  <span><?php echo __('Applies to', 'powerbox'); ?></span>
                </th>
                <th scope="col" id="status" class="manage-column column-status column-primary desc">
                  <span><?php echo __('Status', 'powerbox'); ?></span>
                </th>
              </tr>
            	</thead>

            	<tbody id="the-list">
                <?php
                 foreach ( $wps_custom_menus as $wps_custom_menu ) {

                ?>
            			<tr id="" class="iedit author-self level-0 post-3 type-page status-draft hentry entry">
                      <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                        <strong>
                          <a class="row-title" href="#" aria-label="(Edit)"><?php echo esc_html( $wps_custom_menu->menu_title ); ?></a>
                        </strong>

                        <div class="row-actions">
                          <span class="edit"><a href="<?php echo admin_url( 'admin.php?page=' . POWERBOX_CUSTOM_MENU_SET ); ?>&action=wps_edit_menu&menu_id=<?php echo esc_html( $wps_custom_menu->id ); ?>" aria-label="Edit"><?php echo esc_html__( 'Edit', 'powerbox' ); ?></a> | </span>
                          <span class="clone"><a href="<?php echo admin_url( 'admin.php?page=' . POWERBOX_CUSTOM_MENU_SET ); ?>&action=wps_clone_menu&menu_id=<?php echo esc_html( $wps_custom_menu->id ); ?>" aria-label="Clone"><?php echo esc_html__( 'Clone', 'powerbox' ); ?></a> | </span>
                          <span class="delete"><a onclick="return confirm('<?php echo __('Are you sure to delete?', 'powerbox'); ?>')" href="<?php echo admin_url( 'admin.php?page=' . POWERBOX_CUSTOM_MENU_SET ); ?>&action=wps_delete_menu&menu_id=<?php echo esc_html( $wps_custom_menu->id ); ?>" class="submitdelete" aria-label="Delete"><?php echo esc_html__('Delete', 'powerbox'); ?></a> </span>
                        </div>
                      </td>

                      <td class="menu-title column-appliesto" data-colname="Menuappliesto">
                        <span class="wps-user-appliesto"><?php echo esc_html( $wps_custom_menu->menu_role_id ); ?></span>
                      </td>

                      <td class="menu-status column-status" data-colname="Status">
                        <span class="wps-menu-status"><?php echo ucfirst( esc_html( $wps_custom_menu->status ) ); ?></span>
                      </td>
                  </tr>
                <?php
                }
                ?>
            			</tbody>

            </table>

          <?php
          }
          ?>

          </div>

          <?php

        }

        /**
        * Create WPS menu table
        * @since 2.0
        */
        function create_table() {

          global $wpdb, $wps_menu_table_version;

          $db_ver = get_option('wps_menu_db_version');

          //if db_ver exists, table is already created
          if( isset($db_ver) && $db_ver != 0 )
            return;

          $wps_menu_table = $wpdb->prefix . 'wps_custom_menu';
          $charset_collate = $wpdb->get_charset_collate();

          require_once( POWERBOX_PATH . 'includes/create-menu-table.php' );
          require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
          dbDelta( $create_menu_table );

          add_option( 'wps_menu_db_version', $wps_menu_table_version );

        }

        function wpspb_body_classes( $classes ) {

          $has_user_wps_sidebar = parent::is_user_has_custom_menu();
          if ( isset( $has_user_wps_sidebar ) && $has_user_wps_sidebar === true )
            $classes = $classes . ' has-wpspb-sidebar ';
          return $classes;

        }

        function menu_user_avatar() {

          //get current user data
          $current_user = wp_get_current_user();

          //if no user ID exists return
          if ( ! $current_user->ID )
      			return;

          $user_avatar = get_avatar( $current_user->ID, 60 );
          $avatar_img = get_avatar_url($current_user->ID);
          $user_displayname = $current_user->display_name;
          $user_profile = '<a href="'. admin_url( 'profile.php' ) .'"><i class="wps-user"></i></a>';
          $logout_icon = '<a class="wps-menu-logout" href="' . wp_logout_url() .'"><i class="wps-power-switch"></i></a>';
          $user_profile_data = "<div class='wps-user-avatar'>" . $user_avatar . "</div>
          <div class='wps-user-displayname'>" . $user_displayname . "</div>
          <div class='wps-menu-user-actions'>" . $user_profile . $logout_icon . "</div>";

          return $user_profile_data;

        }

        /**
        * retrieve recovery code
        * @since 2.0
        */
        function wpspb_recovery_code() {

          $wps_recovery_key = parent::get_wps_option_data( 'wps_recovery_key' );

          if( !empty( $wps_recovery_key )) {
            return $wps_recovery_key;
          }
          else return false;

        }

        /**
        * retrieve recovery url
        * @since 2.0
        */
        function wpspb_recovery_url() {

          $wps_recovery_key = parent::get_wps_option_data( 'wps_recovery_key' );

          if( !empty( $wps_recovery_key )) {
            return admin_url( 'admin.php?page=' . WPSHAPERE_MENU_SLUG . '&wps_recovery_key=' . $wps_recovery_key );
          }
          else return false;

        }

        /**
        * Remove wp menu to use wpspowerbox custom menu
        * @since 2.0
        */
        function remove_wp_menu() {

          $wpspb_recovery_code = $this->wpspb_recovery_code();

          if ( isset( $_GET['wps_recovery_key'] ) && !empty( $wpspb_recovery_code ) && $wpspb_recovery_code == $_GET['wps_recovery_key'] )
            return;

          $has_custom_menu = parent::is_user_has_custom_menu();

          if ( $has_custom_menu ) {
            ?>

            <style>
            #adminmenumain{display: none!important;}
            </style>

          <?php
            //Woocommerce reports depends on WC menu
            if( isset( $_GET['page'] ) && $_GET['page'] == 'wc-admin' )
              return;

            global $menu, $submenu;
            $menu = array();
            $submenu = array();

          ?>
          <?php
          }
        }

        /**
        * Remove wp admin bar menu
        * @since 2.0
        */
        function remove_adminbar() {

          $wpspb_recovery_code = $this->wpspb_recovery_code();

          if ( isset( $_GET['wps_recovery_key'] ) && !empty( $wpspb_recovery_code ) && $wpspb_recovery_code == $_GET['wps_recovery_key'] )
            return;

          $has_custom_menu = parent::is_user_has_custom_menu();

          if ( $has_custom_menu ) {
            ?>
            <style type="text/css">
              #wpadminbar{display: none!important;}
              html.wp-toolbar {padding-top:32px!important;}
              @media screen and (min-width:960px) {
                html.wp-toolbar{padding-top:0!important;}
              }
              @media screen and (max-width: 600px) {
              #wpbody {
                  padding-top:0!important;;
              } }
            </style>
          <?php
          }
        }

        /**
        * Create wpspowerbox custom menu
        * @since 2.0
        */
        function wpspb_sidebar_menu() {

          $wpspb_recovery_code = $this->wpspb_recovery_code();
          // $wps_menu_data = parent::is_user_has_custom_menu( true );
          // $other_data = maybe_unserialize( $wps_menu_data->other_data );
          // echo '<pre>'; print_r($other_data); echo '</pre>';

          if ( isset( $_GET['wps_recovery_key'] ) && !empty( $wpspb_recovery_code ) && $wpspb_recovery_code == $_GET['wps_recovery_key'] )
            return;

          $wps_menu_data = parent::is_user_has_custom_menu( true );

          if ( $wps_menu_data ) {

            //remove cookie if is dashboard
            $this->remove_cookie_for_dashboard();

            include_once POWERBOX_PATH . 'includes/wpspb-sidebar-menu.php';

          }
        }

        function wpspb_menu_icon( $icon ) {

          if ( !empty( $icon ) ) {
            $icon_class = explode( '|', $icon );
            return '<i class="' . $icon_class[0] . " " . $icon_class[1] . '"></i>';
          }
          else return NULL;

        }

        function get_url( $urltype = 'default' ) {

          $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

          if( $urltype == 'full' ) {
            return $current_url;
          }
          elseif ( $urltype == 'noquery' )
            return strtok( $current_url, "?" );
          else
            return strtok( $current_url, "&" );

        }

        function shortened_url( $url = '' ) {
          if( !empty($url) )
            return strtok( $url, "&" );
        }

        function is_dashboard() {
          $current_page = $this->get_url();
          if ( admin_url() == $current_page || admin_url() . 'index.php' == $current_page ) {
            return true;
          }
          else return false;
        }

        function default_urls() {
          $adminurl = admin_url();
          $default_block_urls = array(
            'post.php',
            'edit.php',
            'edit-tags.php',
          );
          return $default_block_urls;
        }

        function restrict_urls() {

          $wpspb_recovery_code = $this->wpspb_recovery_code();
          $current_url = $this->get_url();
          $current_full_url = $this->get_url('full');

          //return if recovery mode url is accessed
          if ( isset( $_GET['wps_recovery_key'] ) && !empty( $wpspb_recovery_code ) && $wpspb_recovery_code == $_GET['wps_recovery_key'] )
            return;

          $wps_menu_data = parent::is_user_has_custom_menu( true );

          if ( $wps_menu_data ) {
            //remove cookie if is dashboard
            $this->remove_cookie_for_dashboard();

            $restrict_cur_page = true;
            $current_url = $this->get_url();
            $current_url_noquery = $this->get_url('noquery');

            $default_block_urls = $this->default_urls();
            foreach ( $default_block_urls as $default_url ) {
              if ( stripos( $current_url, $default_url ) !== false) {
                $restrict_cur_page = false;
              }
            }

            if ( $restrict_cur_page ) {
              $wps_admin_menu = json_decode ( $wps_menu_data->menu_data );

              foreach ( $wps_admin_menu as $menu_data ) {

                if( isset( $menu_data->children ) && !empty( $menu_data->children ) ) {
                  foreach ( $menu_data->children as $submenu_data ) {
                    $wps_menu_url = $this->shortened_url( $submenu_data->wps_menu_url );
                    if( $wps_menu_url == $current_url ) {
                      $restrict_cur_page = false;
                      break;
                    }
                  }
                }
                elseif( !empty( $menu_data->wps_menu_url ) && $menu_data->wps_menu_url == $current_url ) {
                  $restrict_cur_page = false;
                  break;
                }
              }

            }

            $wps_other_data = maybe_unserialize( $wps_menu_data->other_data );
            if( !empty( $wps_other_data['blocked_urls'] ) && is_array( $wps_other_data ) && in_array( $current_url, $wps_other_data['blocked_urls'] ) ) {
              $restrict_cur_page = true;
            }
            if( !empty( $wps_other_data['unblocked_urls'] ) && is_array( $wps_other_data ) && in_array( $current_full_url, $wps_other_data['unblocked_urls'] ) ) {
              $restrict_cur_page = false;
            }
            elseif( !empty( $wps_other_data['unblocked_urls'] ) && is_array( $wps_other_data['unblocked_urls'] ) ){

              foreach ( $wps_other_data['unblocked_urls'] as $key => $unb_url ) {
                $unb_url_data = explode( '*', $unb_url );
                if( false !== $unb_url_data ) {
                  $checkURL =  urldecode($current_full_url);
                  $find =  urldecode($unb_url_data[0]);

                  $pos = strpos($checkURL, $find);
                  if( $pos !== false ) {
                    $restrict_cur_page = false;
                    break;
                  }
                }
              }

            }

            $dashboard_url = admin_url( 'index.php' );
            //disable restriction for media upload page
            $media_upload_url = array( admin_url( 'media.php' ), admin_url('async-upload.php'), admin_url( 'media-upload.php' ), admin_url( 'upload.php' ), admin_url( 'media-new.php' ), admin_url( 'media-template.php' ) );

            if( admin_url() == $current_url || $dashboard_url == $current_url ) {
              $restrict_cur_page = false;
            }

            if ( in_array( $current_url_noquery, $media_upload_url ) ) {
              $restrict_cur_page = false;
            }

            if( $restrict_cur_page == true ) {
              if( defined( 'WPS_DISABLE_RESTRICT' ) )
                return;
              if ( isset( $this->aof_options['wpspb_url_restriction'] ) && $this->aof_options['wpspb_url_restriction'] == 1 ) {
      			     wp_die( __( 'Sorry, you are not allowed to access this page.' ) );
               }
            }

          }


        }

        function url_restrict_notice() {
          if( !isset( $this->aof_options['wpspb_url_restriction'] ) || $this->aof_options['wpspb_url_restriction'] != 1 ) {
            ?>

            <div id="message" class="notice below-h2 dismissible"><p>
            <?php _e('Powerbox url restriction is not enabled. You can ', 'wps');
            echo '<a href="' . admin_url() . 'admin.php?page='. WPSHAPERE_MENU_SLUG .'#aof_options_tab12"><strong>';
            echo __('enable it here ', 'wps');
            echo '</strong></a>';
            ?>
            </p></div>

            <?php
          }
        }

        function remove_menu_cookie() {

            if ( isset( $_COOKIE['wps_current_menu'] )) {
                setcookie( 'wps_current_menu', null, -1, '/' );
            }

        }

        function remove_cookie_for_dashboard() {
          $is_dashboard = $this->is_dashboard();
          if( $is_dashboard ) {
            $this->remove_menu_cookie();
          }
        }


    }
}

new NEWWPSMENU();
