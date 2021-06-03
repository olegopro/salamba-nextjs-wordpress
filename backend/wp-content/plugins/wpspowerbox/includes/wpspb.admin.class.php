<?php
/*
 * WPSPowerbox
 * @author   AcmeeDesign
 * @url     http://acmeedesign.com
*/

defined('ABSPATH') || die;

if(!class_exists('CUSTOMIZEADMIN'))
{
    class CUSTOMIZEADMIN extends WPSHAPERE {

        private static $instance;

        function __construct() {
            $this->plugins_list = parent::get_wps_option_data( POWERBOX_OPTIONS_SLUG );
            $this->aof_options = parent::get_wps_option_data( WPSHAPERE_OPTIONS_SLUG );
            add_action('admin_menu', array($this, 'wpspb_createmenu'), 23);
            add_action('admin_enqueue_scripts', array($this, 'wpspb_assets'));
            add_action('plugins_loaded', array($this, 'save_data'));
            add_filter('all_plugins', array($this, 'hide_plugin_from_multisite'));

        }

        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof CUSTOMIZEADMIN)) {
                self::$instance = new CUSTOMIZEADMIN();
            }

            return self::$instance;
        }

        public function wpspb_createmenu()
        {
            add_submenu_page( WPSHAPERE_MENU_SLUG , __('Hide Plugins from Plugins list', 'powerbox'), __('Hide Plugins', 'powerbox'), 'manage_options', POWERBOX_OPTIONS_SLUG, array($this, 'wpspb_createform') );
        }

        function save_data() {
            if(isset($_POST['manage_pluings'])) {
                $custom_data = array();$saved_data = array();
                if($_POST){
                    foreach($_POST['manage']['hide_plugins'] as $key => $val){
                        if(isset($_POST['ishide'][$val]) && !empty($_POST['ishide'][$val])){
                            $custom_data['manage']['hide_plugins'][$val] = 1;
                        } else {
                            $custom_data['manage']['hide_plugins'][$val] = 0;
                        }
                    }
                }
                $saved_data = parent::get_wps_option_data(POWERBOX_OPTIONS_SLUG);
                if($saved_data)
                    $data = parent::wps_array_merge($saved_data, $custom_data);//$data = array_merge($saved_data, $custom_data);
                else
                    $data = $custom_data;

                parent::updateOption(POWERBOX_OPTIONS_SLUG, $data);
                wp_safe_redirect( admin_url( 'admin.php?page='.POWERBOX_OPTIONS_SLUG ) );
                exit();
            }
        }

        public function wpspb_pluginslist() {
            /** WordPress Plugin Administration API */
            if ( ! function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            $all_plugins = get_plugins();
            print('<ul>');
            if (isset($all_plugins) && !empty($all_plugins)){
                foreach($all_plugins as $key => $row){
                    $checked = "";
                    if(isset($this->plugins_list['manage']['hide_plugins'][$key]) && !empty($this->plugins_list['manage']['hide_plugins'][$key])){
                        $checked = "checked";
                    }
                    print("<li>");
                    printf('<input type="hidden" name="manage[hide_plugins][]" id="manage[hide_plugins][]" value="%s">', $key);
                    printf('<label class="container">%s<input type="checkbox" name="ishide[%s]" id="ishide[%s]" value="1" %s/><span class="checkmark"></span></label>', $row['Title'], $key, $key, $checked);
                    print("</li>");
                }
            }
            print('</ul>');
        }

        public function hide_plugin() {
            global $wp_list_table;
            $hidearr = isset($this->plugins_list['ishide']) ? $this->plugins_list['ishide'] : "";

            if(isset($hidearr) && !empty($hidearr)){
                $hide_plugins = array_keys($hidearr);
                $myplugins = $wp_list_table->items;
                foreach ($myplugins as $key => $val) {
                  if (in_array($key,$hide_plugins)) {
                    unset($wp_list_table->items[$key]);
                  }
                }
            }
        }

        public function hide_plugin_from_multisite( $plugins ) {
            $hideplugins = isset($this->plugins_list['manage']['hide_plugins']) ? $this->plugins_list['manage']['hide_plugins'] : "";
            $current_user_id = get_current_user_id();
            $wps_plugin_access = (isset($this->aof_options['show_all_plugins_list_to_admin'])) ? $this->aof_options['show_all_plugins_list_to_admin'] : "";
            $wps_privilege_users = (!empty($this->aof_options['privilege_users'])) ? $this->aof_options['privilege_users'] : array();

            if(is_super_admin($current_user_id)) {
              if(isset($wps_plugin_access) && $wps_plugin_access == 1) {
                return $plugins;
              }
              elseif(isset($wps_plugin_access) && $wps_plugin_access == 2 && !empty($wps_privilege_users) && in_array($current_user_id, $wps_privilege_users)) {
                return $plugins;
              }
            }

            if(isset($hideplugins) && !empty($hideplugins)){
                foreach ($hideplugins as $key => $val) {
                    if(isset($val) && !empty($val)){
                        if (in_array($key,array_keys( $plugins ))) {
                          if (isset($plugins[$key]) && !empty($plugins[$key]))
                            unset( $plugins[$key] );
                        }
                    }
                }
            }

            return $plugins;
        }

        public function wpspb_createform()
        {
            ?>
            <div class="wrap wps-wrap">
                <h2><?php esc_html_e('Hide Plugins', 'powerbox'); ?></h2>
                <h2><?php esc_html_e('Select plugins to hide.', 'powerbox'); ?></h2>
                <div id="message" class="updated below-h2"><p>
                <?php _e('By default, all plugins will be shown to administrator users. ', 'powerbox');
                echo '<a href="' . admin_url() . 'admin.php?page='. WPSHAPERE_MENU_SLUG .'#aof_options_tab9"><strong>';
                echo __('Click here ', 'powerbox');
                echo '</strong></a>';
                echo __('to customize who can access to all plugins list.', 'powerbox');
                ?>
                </p></div>
                <form name="powerbox_manage_plugins" method="post">
                    <?php $this->wpspb_pluginslist();?>
                    <br/>
                    <input type="hidden" name="manage_pluings" value="" />
                    <input type="submit"  class="button button-primary button-large" value="<?php esc_html_e('Save Changes', 'powerbox'); ?>" />
                </form>
            </div>
            <?php
        }

        public function wpspb_assets()
        {
            wp_enqueue_style('powerbox-styles', POWERBOX_DIR_URI . 'assets/css/powerbox.min.css', '', POWERBOX_VERSION);
        }
    }
}



function CustomizeAdmin_init()
{
    CUSTOMIZEADMIN::instance();
}
CustomizeAdmin_init();
