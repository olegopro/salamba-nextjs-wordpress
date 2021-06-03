<?php
/*
 * WPSPowerbox
 * @author   AcmeeDesign
 * @url     http://acmeedesign.com
*/

defined('ABSPATH') || die;

if (!class_exists('WPSAE_REDIRECT_USERS')) {

    class WPSAE_REDIRECT_USERS extends WPSHAPERE
    {
        public $aof_options;

        private static $instance;

        function __construct()
        {
            $this->aof_options = parent::get_wps_option_data(POWERBOX_OPTIONS_SLUG);
            add_action('admin_menu', array($this, 'add_redirect_users_menu'), 24);
            add_action('plugins_loaded', array($this, 'wpspb_save_redirection'));
            add_filter('login_redirect', array($this, 'wpspb_login_redirect_user'), 10, 3 );
        }

        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof WPSAE_REDIRECT_USERS)) {
                self::$instance = new WPSAE_REDIRECT_USERS();
            }

            return self::$instance;
        }

        function add_redirect_users_menu() {
            add_submenu_page( WPSHAPERE_MENU_SLUG , __('Redirect Users after login', 'powerbox'), __('Redirect Users', 'powerbox'), 'manage_options', POWERBOX_REDIRECT_USERS_SLUG, array($this, 'wpspb_redirect_users_page') );
        }

        function wpspb_redirect_users_page() {
            global $menu, $submenu;
            $redirect_users_data = (isset($this->aof_options['manage']['wpspb_redirect_users'])) ? $this->aof_options['manage']['wpspb_redirect_users'] : null;
            ?>

            <div class="wrap wps-wrap">
                <h2><?php esc_html_e('Redirect Users after login', 'powerbox'); ?></h2>
        <?php
            if(isset($_GET['page']) && $_GET['page'] == POWERBOX_REDIRECT_USERS_SLUG && isset($_GET['status']) && $_GET['status'] == 'updated')
            {
                ?>
                <div class="updated top">
                    <p><strong><?php echo __('Settings Updated!', 'powerbox'); ?></strong></p>
                </div>
        <?php
            }
            ?>
                <div class="redirect_users_to">
                    <h3><?php esc_html_e('Set redirection for user roles.', 'powerbox'); ?></h3>
                    <h5 class="wpspb-note"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php esc_html_e('Make sure the user role has permission to access to the page where the user to be redirected to. Otherwise the redirection will end up in Permission error!', 'powerbox'); ?></h5>
                    <form name="redirect_users_to" method="post">
                    <?php
                        $wpspb_wp_roles = parent::wps_get_wproles();
                        foreach($wpspb_wp_roles as $alter_wp_role_key => $alter_wp_role_value) {
                            $custom_url = false;
                            if (isset($redirect_users_data[$alter_wp_role_key]) && $this->is_page_type($redirect_users_data[$alter_wp_role_key]) == "custom" ) {
                                $custom_url = true;
                            }
                            ?>
                            <div class="redirect-users role-<?php echo esc_html($alter_wp_role_key); ?>">
                                <h3><?php esc_html_e('User role:', 'powerbox'); ?> <?php echo esc_html($alter_wp_role_value); ?></h3>
                                <div class="pages">
                                    <label for="redirect-to"><?php esc_html_e('Redirect to page', 'powerbox'); ?></label>
                                    <select class="select_redirect_page" name="redirect-role-to-page[<?php echo $alter_wp_role_key; ?>]">
                                    <option value=""><?php esc_html_e('Default Page', 'powerbox'); ?></option>
                                    <!-- <option value="custom_url" <?php if($custom_url === true) echo "selected=selected"; ?>>- <?php esc_html_e('Custom url', 'powerbox'); ?> -</option> -->
                                <?php
                                foreach($menu as $menu_key => $top_lv_menu) {
                                    if(!empty($top_lv_menu[0])) {
                                        $top_lv_menu_slug =parent::wps_clean_slug($top_lv_menu[2]);
                                        ?>
                                        <option value="<?php echo $top_lv_menu[2]; ?>" <?php if(isset($redirect_users_data[$alter_wp_role_key]) && $top_lv_menu[2] == $redirect_users_data[$alter_wp_role_key]) echo "selected=selected" ?>><?php echo parent::clean_title($top_lv_menu[0]); ?></option>
                                        <?php
                                        if(isset($submenu[$top_lv_menu[2]]) && !empty($submenu[$top_lv_menu[2]])) {
                                            foreach($submenu[$top_lv_menu[2]] as $sub_menu_key => $sub_menu_value) {
                                            ?>
                                        <option value="<?php echo $sub_menu_value[2]; ?>" <?php if(isset($redirect_users_data[$alter_wp_role_key]) && $sub_menu_value[2] == $redirect_users_data[$alter_wp_role_key]) echo "selected=selected" ?>> &nbsp;&nbsp; &raquo; <?php echo parent::clean_title($sub_menu_value[0]); ?></option>
                                        <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                                </select>
                                </div>
                                <div class="custom_url">
                                    <label for="redirect-to"><?php esc_html_e('Redirect to Custom url', 'powerbox'); ?></label><br />
                                    <input type="text" name="redirect-role-to-url[<?php echo esc_html($alter_wp_role_key); ?>]" value="<?php if(isset( $redirect_users_data[$alter_wp_role_key]) && $custom_url === true) echo  $redirect_users_data[$alter_wp_role_key]; ?>" size="50" />
                                </div>
                            </div>
                    <?php
                        }
                    ?>
                        <br /><br />
                        <input type="hidden" name="wpspb_redirect_users" value="" />
                        <input type="submit" class="button button-primary button-large" value="<?php esc_html_e('Save Changes', 'powerbox'); ?>" />
                    </form>
                </div>
            </div>
        <?php

        }

        function wpspb_save_redirection() {
            $redirect_to_pages = array();
            if(isset($_POST) && isset($_POST['wpspb_redirect_users'])) {
                foreach ($_POST['redirect-role-to-page'] as $usr_role => $value) {
                    if(!empty($value)) {
                        if($value != "custom_url") {
                            $redirect_to_pages[$usr_role] = $value;
                        }
                        elseif(isset($_POST['redirect-role-to-url'][$usr_role]) && !empty($_POST['redirect-role-to-url'][$usr_role])) {
                            $redirect_to_pages[$usr_role] = $_POST['redirect-role-to-url'][$usr_role];
                        }
                    }
                }

                $redirect_users['manage'] = array('wpspb_redirect_users' => $redirect_to_pages);

                $saved_data = parent::get_wps_option_data(POWERBOX_OPTIONS_SLUG);

                if($saved_data)
                    $data = parent::wps_array_merge($saved_data, $redirect_users);//$data = array_merge($saved_data, $custom_data);
                else
                    $data = $redirect_users;

                parent::updateOption(POWERBOX_OPTIONS_SLUG, $data);
                wp_safe_redirect( admin_url( 'admin.php?page='.POWERBOX_REDIRECT_USERS_SLUG.'&status=updated' ) );
                exit();
            }
        }

        function wpspb_login_redirect_user($redirect_to, $request, $user) {
            if ( isset( $user->roles ) && is_array( $user->roles ) ) {

                $the_user_role = $user->roles;

                $redirect_page = isset($this->aof_options['manage']['wpspb_redirect_users'][$the_user_role[0]]) ? $this->aof_options['manage']['wpspb_redirect_users'][$the_user_role[0]] : null;
                if(!empty($redirect_page)) {
                    if($this->is_page_type($redirect_page) == "toplevel") {
                        return admin_url( $redirect_page );
                    }
                    elseif($this->is_page_type($redirect_page) == "pluginspage") {
                        return admin_url( "admin.php?page=".$redirect_page );
                    }
                    elseif(!empty($redirect_page)) {
                        return $redirect_page;
                    }
                }
            }

            return $redirect_to;

        }

        function is_page_type($url) {
            if(strpos($url, 'http') !== false) {
                return "custom";
            }
            elseif(strpos($url, '.php') !== false) {
                return "toplevel";
            }
            else {
                return "pluginspage";
            }
        }

    }

}

function CustomizeRedirectUsers_init()
{
    WPSAE_REDIRECT_USERS::instance();
}
CustomizeRedirectUsers_init();
