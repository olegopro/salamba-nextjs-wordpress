<?php
/*
 * WPSPowerbox
 * @author   AcmeeDesign
 * @url     http://acmeedesign.com
*/

defined('ABSPATH') || die;

if(!class_exists('ADDADMINMENU')){
    class ADDADMINMENU extends CUSTOMIZEADMINMENU
    {
        private static $instance;
        private $external_menu_data = array();
        function __construct() {
            $this->aof_options = parent::get_wps_option_data(POWERBOX_OPTIONS_SLUG);
            add_action('admin_menu', array($this, 'wpspb_createmenu'), 21);
            add_action('plugins_loaded', array($this, 'save_data'));
            add_action('admin_enqueue_scripts', array($this, 'wpspb_assets'));
        }

        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof ADDADMINMENU)) {
                self::$instance = new ADDADMINMENU();
            }

            return self::$instance;
        }

        public function wpspb_assets()
        {
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('wpspb-scripts', POWERBOX_DIR_URI . 'assets/js/wpspb-scripts.min.js', array('jquery'), '', false);
            wp_enqueue_script('wps-nested-sortable', POWERBOX_DIR_URI . 'assets/js/jquery.mjs.nestedSortable.js', array('jquery-ui-sortable'), '', false);
        }

        public function dump($array)
        {
            echo "<div style='margin-left:15%'>";
            echo "<pre>";
            print_r($array);
            echo "</pre>";
            echo "</div>";
        }

        public function endKey($array){
            end($array);
            return key($array);
        }

        public function wpspb_createmenu() {
            add_submenu_page( WPSHAPERE_MENU_SLUG , __('Add Menu to Admin menus', 'powerbox'), __('Add Admin Menus', 'powerbox'), 'manage_options', POWERBOX_CUSTOM_ADMIN_MENU_SLUG, array($this, 'wpspb_createform') );

            global $menu, $submenu;

           // $this->dump($menu);
            $capability = 'read';
            if(isset($this->aof_options['manage']['wp_custom_admin_menu']) && !empty($this->aof_options['manage']['wp_custom_admin_menu'])){
                foreach($this->aof_options['manage']['wp_custom_admin_menu'] as $key => $row) {
                    $start = count($menu) + 1;
                    $endpos = (int)max(array_keys($menu)) + 1;
                    //$this->dump($start);
                    $custom_top_menu[] = array($row['menu_title'], $capability, $row['menu_url'], $row['menu_title'], 'menu-top'. ' '.$row['menu_url'], $row['menu_url']);
                    array_splice($menu, $start, 0, $custom_top_menu);
                    //$menu["$endpos"] = $custom_top_menu;


                    unset($custom_top_menu);
                    if(isset($row['submenu']) && !empty($row['submenu'])){
                        if(is_array($row['submenu'])){
                            foreach($row['submenu'] as $subkey => $subrow){
                                $custom_sub_menu[] = array($subrow['menu_title'], $capability, $subrow['menu_url']);
                                if(isset($submenu[$row['menu_url']]) && !empty($submenu[$row['menu_url']])) {
                                    $startsub = count($submenu[$row['menu_url']]);
                                    array_splice($submenu[$row['menu_url']], $startsub, 0, $custom_sub_menu);
                                } else {
                                    /*
                                    $custom_top_sub_menu[] = array($row['menu_title'], $capability, $row['menu_url'], $row['menu_title']);
                                    $submenu[$row['menu_url']] =  $custom_top_sub_menu;
                                    unset($custom_top_sub_menu);
                                    global $submenu;
                                    $startsub = count($submenu[$row['menu_url']]);
                                    array_splice($submenu[$row['menu_url']], $startsub, 0, $custom_sub_menu);
                                    */
                                    $submenu[$row['menu_url']] =  $custom_sub_menu;
                                }
                                unset($custom_sub_menu);
                            }
                        }
                    }
                }
            }

        }

        function save_data() {
            if(isset($_POST['manage_menus']) || isset($_POST['delete_custom_menus'])) {
                $custom_data = array();$saved_data = array();
                $data = array();
                //$this->dump($_POST);
            }

            if(isset($_POST['manage_menus'])) {
                $custom_data = array();$saved_data = array();$data = array();
                unset($_POST['manage_menus']);
                $custom_data = $_POST;
                $saved_data = parent::get_wps_option_data(POWERBOX_OPTIONS_SLUG);
                if($saved_data){
                    if(isset($saved_data['manage']['wp_custom_admin_menu']) && !empty($saved_data['manage']['wp_custom_admin_menu'])){
                        //$custom_data['manage']['wp_custom_admin_menu'] = array_merge($saved_data['manage']['wp_custom_admin_menu'], $custom_data['manage']['wp_custom_admin_menu']);
                    }
                    $data = parent::wps_array_merge($saved_data, $custom_data);
                }
                else
                    $data = $custom_data;

                if(isset($data)){
                    parent::updateOption(POWERBOX_OPTIONS_SLUG, $data);
                }
                wp_safe_redirect( admin_url( 'admin.php?page='.POWERBOX_CUSTOM_ADMIN_MENU_SLUG ) );
                exit();
            }

            if(isset($_POST['delete_custom_menus'])) {
                $saved_data = parent::get_wps_option_data(POWERBOX_OPTIONS_SLUG);
                if(isset($saved_data['manage']['wp_custom_admin_menu']) && !empty($saved_data['manage']['wp_custom_admin_menu'])){
                    if(isset($_POST['isdelete']) && !empty($_POST['isdelete'])) {
                        foreach($_POST['isdelete'] as $position => $row){
                            if(is_array($row)){
                                if(isset($row) && !empty($row)){
                                    foreach($row as $subposition => $subrow){
                                        unset($saved_data['manage']['wp_custom_admin_menu'][$position]['submenu'][$subposition]);
                                    }
                                    if(empty($saved_data['manage']['wp_custom_admin_menu'][$position]['submenu'])){
                                        unset($saved_data['manage']['wp_custom_admin_menu'][$position]['submenu']);
                                    } else {
                                        $saved_data['manage']['wp_custom_admin_menu'][$position]['submenu'] = array_values($saved_data['manage']['wp_custom_admin_menu'][$position]['submenu']);
                                    }
                                }
                            } else {
                                if(isset($saved_data['manage']['wp_custom_admin_menu'][$position])
                                        && !empty($saved_data['manage']['wp_custom_admin_menu'][$position])){

                                    if(isset($saved_data['manage']['wp_custom_admin_menu'][$position]['submenu'])
                                            && !empty($saved_data['manage']['wp_custom_admin_menu'][$position]['submenu'])){
                                        foreach($saved_data['manage']['wp_custom_admin_menu'][$position]['submenu'] as $pos => $sub){
                                            unset($saved_data['manage']['wp_custom_admin_menu'][$position]['submenu'][$pos]);
                                        }
                                    }

                                    unset($saved_data['manage']['wp_custom_admin_menu'][$position]);
                                }
                            }

                        }
                        if(empty($saved_data['manage']['wp_custom_admin_menu'])){
                            unset($saved_data['manage']['wp_custom_admin_menu']);
                        } else {
                            $saved_data['manage']['wp_custom_admin_menu'] = array_values($saved_data['manage']['wp_custom_admin_menu']);
                        }
                    }

                    $data = $saved_data;
                }

                if($data){
                    parent::updateOption(POWERBOX_OPTIONS_SLUG, $data);
                }
                wp_safe_redirect( admin_url( 'admin.php?page='.POWERBOX_CUSTOM_ADMIN_MENU_SLUG ) );
                exit();

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

        public function dropdown($list, $name, $id, $selectedtext)
        {
            printf('<select name="%1$s" id="%2$s">', $name, $id );
            print('<option value="">--Choose Capabilities--</option>');
            if($list):
                foreach($list as $item_key => $item):
                    $selected = "";
                    if($selectedtext == $item):
                        $selected = "selected";
                    endif;
                    printf('<option value="%1$s" %2$s >%3$s</option>', $item, $selected, $item);
                    unset($selected);
                endforeach;
            endif;
            echo '</select>';
        }

        public function wpspb_custom_admin_menu_list($opt = 'true')
        {
            $custom_admin_menu_list = $this->aof_options['manage']['wp_custom_admin_menu'];

            if (isset($custom_admin_menu_list) && !empty($custom_admin_menu_list)){
                $sl = 0;
                foreach($custom_admin_menu_list as $key => $row){
                    $checked = "";
                    printf("<li id='%s'>", "top-li-".$sl);
                    if ($opt == "true"){
                        printf('<label class="container">%s<input type="checkbox" name="isdelete[%s]" id="isdelete[%s]" value="%s"/><span class="checkmark"></span></label>', $row['menu_title'], $sl, $sl,$row['menu_url']);
                        if(isset($row['submenu']) && !empty($row['submenu'])){
                            if(is_array($row['submenu'])){
                                $k=0;
                                echo "<ol class='wps-menu-remove-list'>";
                                foreach($row['submenu'] as $subkey => $subrow){
                                    printf("<li id='%s'>", "top-li-".$sl."-sub-".$k);
                                    if ($opt == "true"){
                                        printf('<label class="container sublist">%s<input type="checkbox" name="isdelete[%s][%s]" id="isdelete[%s][%s]" value="%s"/><span class="checkmark"></span></label>', $subrow['menu_title'],$sl, $k, $sl, $k, $subrow['menu_url']);
                                    }
                                    echo "</li>";
                                    $k++;
                                }
                                echo "</ol>";
                            }
                        }
                    } else {
                        echo '<div class="alter-sort-list">';
                            printf('<span class="menu_title">%s</span>', $row['menu_title']);
                            printf('<a href="javascript:void(0)" class="wps-edit-expand"><i class="fa fa-chevron-down" aria-hidden="true"></i> <span>%s</span></a>', "Edit");
                            //printf('<a href="javascript:void(0)" class="wps-remove-menu"><i class="fa fa-chevron-down" aria-hidden="true"></i> <span>%s</span></a>', "Remove");

                            echo '<div class="alter-menu-contents"> ';
                                echo '<div class="menu-title">';
                                    printf('<label for="input-title-top-li-'.$sl.'">%s</label>', "Title");
                                    printf('<input type="text" id="%s" name="%s" value="%s" class="customtitles"/>', "input-title-top-li-".$sl, "manage[wp_custom_admin_menu][".$sl."][menu_title]", $row['menu_title']);
                                echo '</div>';
                                echo '<div class="menu-url">';
                                    printf('<label for="input-url-top-li-'.$sl.'">%s</label>', "URL");
                                    printf('<input type="text" id="%s" name="%s" value="%s"/>', "input-url-top-li-".$sl, "manage[wp_custom_admin_menu][".$sl."][menu_url]", $row['menu_url']);
                                echo '</div>';

                                if(isset($row['submenu']) && !empty($row['submenu'])){
                                    if(is_array($row['submenu'])){
                                        $k=0;
                                        echo "<ol class='wps-menu-remove-list'>";
                                        foreach($row['submenu'] as $subkey => $subrow){
                                            printf("<li id='%s'>", "top-li-".$sl."-sub-".$k);
                                            if ($opt == "true"){
                                                printf('<label class="container sublist">%s<input type="checkbox" name="isdelete[%s][%s]" id="isdelete[%s][%s]" value="%s"/><span class="checkmark"></span></label>', $subrow['menu_title'],$sl, $k, $sl, $k, $subrow['menu_url']);
                                            } else {
                                                echo '<div class="alter-sort-list submenu_contents">';
                                                    printf('<span class="menu_title">%s</span>', $subrow['menu_title']);
                                                    printf('<a href="javascript:void(0)" class="wps-edit-expand"><i class="fa fa-chevron-down" aria-hidden="true"></i> <span>%s</span></a>', "Edit");
                                                    //printf('<a href="javascript:void(0)" class="wps-remove-menu"><i class="fa fa-chevron-down" aria-hidden="true"></i> <span>%s</span></a>', "Remove");
                                                    echo '<div class="alter-menu-contents">';
                                                        echo '<div class="menu-title">';
                                                            printf('<label for="input-title-top-li-'.$sl.'-sub-'.$k.'">%s</label>', "Title");
                                                            printf('<input type="text" id="%s" name="%s" value="%s" class="customtitles"/>', "input-title-top-li-".$sl, "manage[wp_custom_admin_menu][".$sl."][submenu][".$k."][menu_title]", $subrow['menu_title']);
                                                        echo '</div>';
                                                    echo '<div class="menu-url">';
                                                        printf('<label for="input-url-top-li-'.$sl.'-sub-'.$k.'">%s</label>', "Title");
                                                        printf('<input type="text" id="%s" name="%s" value="%s"/>', "input-url-top-li-".$sl, "manage[wp_custom_admin_menu][".$sl."][submenu][".$k."][menu_url]", $subrow['menu_url']);
                                                    echo '</div>';
                                                echo '</div>';
                                            }
                                            echo "</li>";
                                            $k++;
                                        }
                                        echo "</ol>";
                                    }
                                }
                            echo '</div>';
                        echo '</div>';
                    }

                    $sl++;
                    print("</li>");
                }
            }
        }

        public function wpspb_createform()
        {
            ?>
            <div class="wrap wps-wrap">
                <h2><?php esc_html_e('Add Admin Menus', 'powerbox'); ?></h2>

                <div>
                    <div style="float:left; width:50%">
                        <form name="powerbox_manage_menus" method="post" autocomplete="off">
                            <table class="table table-bordered" cellspacing="10" width="100%">
                                <tbody>
                                    <tr>
                                        <td><?php esc_html_e('Title', 'powerbox'); ?></td>
                                        <td><input type="text" name="manage[wp_custom_admin_menu][menu_title]" id="menu_title" value=""/></td>
                                    </tr>
                                    <tr>
                                        <td><?php esc_html_e('Custom URL', 'powerbox'); ?></td>
                                        <td>
                                            <input type="text" name="manage[wp_custom_admin_menu][menu_url]" id="menu_url" value="" style="width: 100%"/>
                                            <input type="hidden" name="manage[wp_custom_admin_menu][menu_capabilities]" id="manage[wp_custom_admin_menu][menu_capabilities]" value="read" />
                                        </td>
                                    </tr>
                                    <!--
                                    <tr>
                                        <td>Capabilities</td>
                                        <td>
                                            <?php
                                            //$selected = "";
                                            //$this->dropdown($admin_capabilities, 'manage[wp_custom_admin_menu][menu_capabilities]', 'manage[wp_custom_admin_menu][menu_capabilities]', $selected);
                                            //unset($selected);
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><label for="icon_picker"><em><?php //esc_html_e('Choose Icon', 'powerbox'); ?></em></label></td>
                                        <td>
                                            <div id="" data-target="#menu-icon-for-99" class="icon-picker"></div>
                                            <input type="hidden" id="menu-icon-for-99" name="manage[wp_custom_admin_menu][menu_icon]" value="" />
                                        </td>
                                    </tr>
                                  -->
                                </tbody>
                            </table>

                            <br/>
                            <input type="hidden" name="manage_menus" value="" />
                            <input type="button"  class="button button-primary button-large pull-right" id="add_to_menu" value="<?php esc_html_e('Add to Menu', 'powerbox'); ?>" />
                        </form>

                        <hr/>
                        <div >
                            <?php if(isset($this->aof_options['manage']['wp_custom_admin_menu'])
                                    && !empty($this->aof_options['manage']['wp_custom_admin_menu'])):?>
                            <form name="powerbox_delete_custom_menus" method="post" autocomplete="off">
                                <ol class="wps-menu-remove-list">
                                <?php $this->wpspb_custom_admin_menu_list();?>
                                </ol>
                                <input type="hidden" name="delete_custom_menus" value="" />
                                <input type="submit"  class="button button-primary button-large pull-left" value="<?php esc_html_e('Remove', 'powerbox'); ?>" />
                            </form>


                            <?php else: ?>
                                <p><?php esc_html_e('No List', 'powerbox'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="float:right;width:38%">
                        <h2><?php esc_html_e('Menus', 'powerbox'); ?></h2>

                        <form name="powerbox_sort_custom_menus" method="post" autocomplete="off">
                            <div id="listItems" class="wps-menu-box">
                                <ol id="sortable" class="wps-menu-list sortable">
                                    <?php if(isset($this->aof_options['manage']['wp_custom_admin_menu'])
                                            && !empty($this->aof_options['manage']['wp_custom_admin_menu'])):?>
                                        <?php $this->wpspb_custom_admin_menu_list('false');?>
                                    <?php endif; ?>
                                </ol>
                            </div>
                            <input type="hidden" name="manage_menus" value="" />
                            <input type="submit"  class="button button-primary button-large pull-right" value="<?php esc_html_e('Save Changes', 'powerbox'); ?>" />
                        </form>
                    </div>
                </div>
                <div style="clear:both; padding: 0; margin: 0"></div>

            </div>
            <?php
        }


    }
}

function add_adminmenu_init() {
    ADDADMINMENU::instance();
}
add_adminmenu_init();
