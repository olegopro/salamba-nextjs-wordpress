<?php
/*
 * WPSPowerbox
 * @author   AcmeeDesign
 * @url     http://acmeedesign.com
*/

defined('ABSPATH') || die;

if(!class_exists('ManageUser'))
{
    class ManageUser extends WPSHAPERE {

        private static $instance;

        function __construct() {
            $this->users_list = parent::get_wps_option_data(POWERBOX_OPTIONS_SLUG);
            $this->aof_options = parent::get_wps_option_data(WPSHAPERE_OPTIONS_SLUG);
            add_action('admin_menu', array($this, 'wpspb_createmenu'), 22);
            add_action('admin_enqueue_scripts', array($this, 'wpspb_assets'));
            add_action('plugins_loaded', array($this, 'save_data'));
            add_filter('users_list_table_query_args', array($this, 'hide_userslist'));
            add_filter('wps_dash_widgets_number', array($this, 'dash_widgets_number'), 20);
            add_filter('views_users', array($this, 'moditify_users_count'));
        }

        function dash_widgets_number() {
          $numbers = (isset($this->aof_options['set_dash_widget_count'])) ? $this->aof_options['set_dash_widget_count'] : 4;
          return $numbers;
        }

        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof ManageUser)) {
                self::$instance = new ManageUser();
            }

            return self::$instance;
        }

        public function wpspb_createmenu()
        {
            add_submenu_page( WPSHAPERE_MENU_SLUG , __('Hide Users from Users list', 'powerbox'), __('Hide Users', 'powerbox'), 'manage_options', POWERBOX_USER_PAGE_SLUG, array($this, 'wpspb_createform') );
        }

        function save_data() {
            if(isset($_POST['manage_users'])) {
                $custom_data = array();$saved_data = array();
                if($_POST){
                    foreach($_POST['manage']['hide_users'] as $key => $val){
                        if(isset($_POST['ishide'][$val]) && !empty($_POST['ishide'][$val])){
                            $custom_data['manage']['hide_users'][$val] = 1;
                        } else {
                            $custom_data['manage']['hide_users'][$val] = 0;
                        }
                    }
                }
                $saved_data = parent::get_wps_option_data(POWERBOX_OPTIONS_SLUG);
                if($saved_data){
                    $data = parent::wps_array_merge($saved_data, $custom_data);
                }
                else
                    $data = $custom_data;


                parent::updateOption(POWERBOX_OPTIONS_SLUG, $data);
                wp_safe_redirect( admin_url( 'admin.php?page='.POWERBOX_USER_PAGE_SLUG ) );
                exit();
            }
        }

        public function wpspb_userslist() {

            $users    = get_users();
            print('<ul>');
            if (isset($users) && !empty($users)){
                foreach($users as $key => $row){
                    $checked = "";
                    if(isset($this->users_list['manage']['hide_users'][$row->ID]) && !empty($this->users_list['manage']['hide_users'][$row->ID])){
                        $checked = "checked";
                    }
                    print("<li>");
                    printf('<input type="hidden" name="manage[hide_users][]" id="manage[hide_users][]" value="%s">', $row->ID);
                    printf('<label class="container">%s<input type="checkbox" name="ishide[%s]" id="ishide[%s]" value="1" %s/><span class="checkmark"></span></label>', get_the_author_meta('display_name', $row->ID), $row->ID, $row->ID, $checked);
                    print("</li>");
                }
            }
            print('</ul>');
        }

        public function hide_userslist( $query ) {
            $exclude = array();
            $hideusers = isset($this->users_list['manage']['hide_users']) ? $this->users_list['manage']['hide_users'] : "";
            $current_user_id = get_current_user_id();
            $wps_users_list_access = (isset($this->aof_options['show_all_users_list_to_admin'])) ? $this->aof_options['show_all_users_list_to_admin'] : "";
            $wps_privilege_users = (!empty($this->aof_options['privilege_users'])) ? $this->aof_options['privilege_users'] : array();

            if(is_super_admin($current_user_id)) {
              if(isset($wps_users_list_access) && $wps_users_list_access == 1) {
                return $query;
              }
              elseif(isset($wps_users_list_access) && $wps_users_list_access == 2 && !empty($wps_privilege_users) && in_array($current_user_id, $wps_privilege_users)) {
                return $query;
              }
            }

            if(isset($hideusers) && !empty($hideusers)){
                foreach ($hideusers as $key => $val) {
                    if(isset($val) && !empty($val)){
                        $exclude[] = $key;
                    }
                }

                $user_ids = implode(',', $exclude);
                $query['exclude'] = $user_ids;
                //$query->query_vars['exclude'] = $user_ids;
            }

            return $query;
        }

        public function wpspb_createform()
        {
            ?>
            <div class="wrap wps-wrap">
                <h2><?php esc_html_e('Hide Users from Users list', 'powerbox'); ?></h2>
                <h2><?php esc_html_e('Select users to hide.', 'powerbox'); ?></h2>
                <div id="message" class="updated below-h2"><p>
                <?php _e('By default, all user names will be shown to administrator users. ', 'powerbox');
                echo '<a href="' . admin_url() . 'admin.php?page='. WPSHAPERE_MENU_SLUG .'#aof_options_tab9"><strong>';
                echo __('Click here ', 'powerbox');
                echo '</strong></a>';
                echo __('to customize who can access to all user names list.', 'powerbox');
                ?>
                </p></div>
                <form name="powerbox_manage_users" method="post">
                    <?php $this->wpspb_userslist();?>
                    <br/>
                    <input type="hidden" name="manage_users" value="" />
                    <input type="submit"  class="button button-primary button-large" value="<?php esc_html_e('Save Changes', 'powerbox'); ?>" />
                </form>
            </div>
            <?php
        }

        public function moditify_users_count( $views ){
            $hideusers = isset($this->users_list['manage']['hide_users']) ? $this->users_list['manage']['hide_users'] : "";
            $exclude = array();
            if(isset($hideusers) && !empty($hideusers)){
                $total_hide_users = 0;
                foreach ($hideusers as $key => $val) {
                    if(isset($val) && !empty($val)){
                        $user_roles = get_userdata($key);
                        $total_hide_users += 1;
                        if(!isset($exclude[$user_roles->roles[0]])){
                            $exclude[$user_roles->roles[0]] = 0;
                        }
                        $exclude[$user_roles->roles[0]] += 1;
                    }
                }
                $exclude['all'] = $total_hide_users;
            }


            if(isset($views) && !empty($views) && (isset($exclude)) && !empty($exclude)){
                foreach($views as $index => $view){
                    preg_match_all('!\d+!', $view, $matches);
                    $temp = implode(' ', $matches[0]);
                    $count = (isset($exclude[$index]) && !empty($exclude[$index])) ? $exclude[$index] : 0;
                    $_view[$index] = (int)$temp - (int)$count;
                    $views[ $index ] = preg_replace('([0-9]+)', $_view[$index], $view);
                }
            }
            return $views;
        }

        public function wpspb_assets()
        {
            wp_enqueue_style('powerbox-styles', POWERBOX_DIR_URI . 'assets/css/powerbox.css', '', POWERBOX_VERSION);
        }
    }
}



function CustomizeUsers_init()
{
    ManageUser::instance();
}
CustomizeUsers_init();
