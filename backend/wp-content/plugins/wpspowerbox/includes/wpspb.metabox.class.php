<?php
/*
 * WPSPowerbox
 * @author   AcmeeDesign
 * @url     http://acmeedesign.com
*/

defined('ABSPATH') || die;

if( ! class_exists('HIDEMETABOXES') ){
    class HIDEMETABOXES extends WPSHAPERE
    {
        private static $instance;
        function __construct() {
            $this->wpspb_options = parent::get_wps_option_data( POWERBOX_OPTIONS_SLUG );
            $this->aof_options = parent::get_wps_option_data( WPSHAPERE_OPTIONS_SLUG );
            add_action( 'admin_menu', array( $this, 'wpspb_createmenu' ), 21 );
            add_action( 'plugins_loaded', array( $this, 'wpspb_save_metabox_selection' ), 1 );
            add_action( 'admin_enqueue_scripts', array($this, 'wpspb_assets') );
            add_action( 'in_admin_header', array( $this, 'wp_metaboxes' ), 10 );
            add_action( 'in_admin_header', array( $this, 'remove_metaboxes' ), 20 );
        }

        public function wpspb_assets()
        {
          wp_enqueue_style('tabstyles', POWERBOX_DIR_URI . 'assets/css/jquery.tabs.min.css', '', POWERBOX_VERSION);
          wp_enqueue_script('jquery');
          wp_enqueue_script('jqtabs', POWERBOX_DIR_URI . 'assets/js/jquery.tabs.min.js', array('jquery'), '', true);
          wp_enqueue_script('tabsjs', POWERBOX_DIR_URI . 'assets/js/jqtab.js', '', '', true);
        }

        public function wpspb_createmenu() {
            add_submenu_page( WPSHAPERE_MENU_SLUG , __('Hide Meta Boxes', 'powerbox'), __('Hide Meta Boxes', 'powerbox'), 'manage_options', POWERBOX_HIDE_META_BOXES, array($this, 'wpspb_metabox_options') );
        }

        function wpspb_save_metabox_selection() {

          if(isset($_POST) && is_array($_POST) && isset($_POST['wpspb_metabox_options'])) {

            $saved_data = array();
            $meta_box_options = array( 'hidden_metaboxes' => $_POST );

            if( $meta_box_options && is_array( $meta_box_options )) {
              $saved_data = parent::get_wps_option_data( POWERBOX_OPTIONS_SLUG );
              if($saved_data){
                  $data = parent::wps_array_merge( $saved_data, $meta_box_options ) ;
              }
              else
                  $data = $meta_box_options;

              parent::updateOption( POWERBOX_OPTIONS_SLUG, $data );
            }
            wp_safe_redirect( admin_url( 'admin.php?page=' . POWERBOX_HIDE_META_BOXES ) );
            exit();

          }

        }

        function wp_metaboxes() {

          global $wp_meta_boxes, $pagenow;

          $wppost_type = null;
          if ( 'post.php' === $pagenow && isset($_GET['post']) ) {
              $wppost_type = get_post_type( $_GET['post'] );
          }

          $all_meta_boxes = array();

          if( !empty($wppost_type) && !empty($wp_meta_boxes) && is_array($wp_meta_boxes) ) {

            foreach ($wp_meta_boxes[$wppost_type] as $box_key => $priority ) {
              //foreach ( array_keys( $context ) as $context ) {
                foreach ( array( 'high', 'core', 'default', 'low' ) as $priority ) {
                  if ( ! isset( $wp_meta_boxes[$wppost_type][ $box_key ][ $priority ] ) ) {
                    continue;
                  }
                  foreach ( $wp_meta_boxes[ $wppost_type ][ $box_key ][ $priority ] as $box ) {
                    if ( false == $box || ! $box['title'] ) {
                      continue;
                    }

                    $widget_title = $box['title'];

                    if ( isset($box['args']) && is_array( $box['args'] ) && isset( $box['args']['__widget_basename'] ) ) {
                      $widget_title = $box['args']['__widget_basename'];
                    }

                    $all_meta_boxes[ $wppost_type ][$box_key][ $priority ][ $box['id'] ] = array(
                      'id' => $box['id'],
                      'title' => wp_strip_all_tags( $widget_title, true ),
                    );

                  }
                }
              //}
            }

            if( !empty($all_meta_boxes) && is_array($all_meta_boxes) ) {

              $meta_boxes = array(
                'wp_meta_boxes' => $all_meta_boxes
              );

              $saved_data = parent::get_wps_option_data(POWERBOX_OPTIONS_SLUG);

              if($saved_data) {
                  $data = parent::wps_array_merge($saved_data, $meta_boxes);
                }
              else
                  $data = $meta_boxes;

              parent::updateOption( POWERBOX_OPTIONS_SLUG, $data );

            }

          }
        }

        function wpspb_metabox_options() {
          $meta_boxes = $this->wpspb_options['wp_meta_boxes'];
          $hidden_metaboxes = ( isset ( $this->wpspb_options['hidden_metaboxes'] ) ) ? $this->wpspb_options['hidden_metaboxes'] : '';
          ?>
        <div class="wps-settings-wrap">
          <div class="wps-settings-title">
            <h2><?php esc_html_e('Hide Meta Boxes', 'powerbox'); ?></h2>
          </div>
          <div class="wps-settings-content wps-settings-no-pad">
            <form class="wps-meta-boxes-options" name="wps_meta_boxes_options" method="post">
              <?php
              $get_all_roles = parent::wps_get_wproles();

              if(!empty( $get_all_roles ) && is_array( $get_all_roles )) {
?>
                <div id="wps-settings-tabs" class="wps-tab-wrapper">
                  <div class="jq-tab-menu">
                    <?php
                    $role_num = 0;
                    foreach ( $get_all_roles as $wprole_name => $wprole_label ) {
                      $role_num++;
                      $active = ( $role_num == 1 ) ? 'active' : '';
                      echo '<div class="jq-tab-title ' . $active . '" data-tab="' . $wprole_name . '-tab">' . $wprole_label . '</div>';
                    }
                    ?>
                  </div>
                  <div class="jq-tab-content-wrapper">
<?php
                $role_num = 0;
                //loop through all user roles
                foreach ( $get_all_roles as $wprole_name => $wprole_label ) {
                  $role_num++;
                  $active = ( $role_num == 1 ) ? 'active' : '';
                  echo '<div class="jq-tab-content ' . $active . '" data-tab="' . $wprole_name . '-tab">';
                  echo '<div class="wps-settings-tab wps-content-flex">';
                  echo '<div class="user-role">' . $wprole_label . '</div>';

                    foreach ( $meta_boxes as $box_key => $context ) {

                      //don't display dashboard widgets
                      if ( 'dashboard' == $box_key ) {
                        continue;
                      }

                      echo '<div class="meta-context">';
                      echo '<div class="meta-box-title">' . $this->wpspb_convert_to_title( $box_key ) . '</div>';
                      echo '<div class="meta-boxes">';
                      foreach ( array_keys( $context ) as $context ) {
                        foreach ( array_keys($meta_boxes[ $box_key ][ $context ]) as $priority ) {
                          foreach ( $meta_boxes[ $box_key ][ $context ][ $priority ] as $box ) {

                            if ( false == $box || ! $box['title'] ) {
                              continue;
                            }

                            $widget_title = $box['title'];

                            if ( isset($box['args']) && is_array( $box['args'] ) && isset( $box['args']['__widget_basename'] ) ) {
                              $widget_title = $box['args']['__widget_basename'];
                            }

                            $checked = ( isset( $hidden_metaboxes[ $wprole_name ][ $box_key ][ $context ][ $priority ][ $box['id'] ] ) &&
                            $box['id'] == $hidden_metaboxes[ $wprole_name ][ $box_key ][ $context ][ $priority ][ $box['id'] ] ) ?
                            'checked' : '';

                            echo '<div class="meta-box-select">';
                            printf(
                              '<label for="%1$s-hidebox">
                              <input name="%1$s[%2$s][%3$s][%4$s][%7$s]" type="checkbox" id="%1$s-hide" value="%5$s" %8$s />%6$s
                              </label>',
                              $wprole_name,
                              $box_key,
                              $context,
                              $priority,
                              esc_attr( $box['id'] ),
                              $widget_title,
                              esc_attr( $box['id'] ),
                              $checked
                            );
                            echo '</div>';
                          }

                        }
                      }
                      echo '</div>';
                      echo '</div>';
                    }

                    echo '</div>';
                    echo '</div>';
                  }
                  ?>
                    </div>
                  </div>
            <?php
                }
              ?>

            <br /><br />
            <input type="hidden" name="wpspb_metabox_options" value="1" />
            <input type="submit" class="button button-primary button-large" value="<?php esc_html_e('Save Changes', 'powerbox'); ?>" />
            </form>
          </div>
        </div>
          <?php
        }

        function remove_metaboxes() {
          global $wp_meta_boxes;
          $current_user_role = parent::wps_get_user_role();
          $current_user_id = get_current_user_id();
          $wps_privilege_users = (!empty($this->aof_options['privilege_users'])) ? $this->aof_options['privilege_users'] : array();
          $hidden_metaboxes = isset( $this->wpspb_options['hidden_metaboxes'][$current_user_role] ) ? $this->wpspb_options['hidden_metaboxes'][$current_user_role] : NULL;

          if( !empty($wp_meta_boxes) && is_array($wp_meta_boxes) && !empty($hidden_metaboxes) && is_array($hidden_metaboxes) ) {
            foreach ( $hidden_metaboxes as $box_key => $context ) {
              foreach ( array_keys( $context ) as $context ) {
                foreach ( array_keys($hidden_metaboxes[ $box_key ][ $context ]) as $priority ) {
                  foreach ( array_keys($hidden_metaboxes[ $box_key ][ $context ][ $priority ]) as $box_id ) {

                    if( is_super_admin($current_user_id) ) {
                      if( !empty($wps_privilege_users) && !in_array( $current_user_id, $wps_privilege_users ) ) {
                        unset( $wp_meta_boxes[ $box_key ][ $context ][ $priority ][$box_id] );
                      }
                    }
                    else {
                      unset( $wp_meta_boxes[ $box_key ][ $context ][ $priority ][$box_id] );
                    }

                  }


                }
              }
            }
          }
        }

        function wpspb_convert_to_title( $string ) {
          if( !empty($string) ) {
            $output = preg_replace( "/[\-_]/", " ", $string );
            return ucwords( $output );
          }
          else
            return NULL;
        }



    }
}

new HIDEMETABOXES();
