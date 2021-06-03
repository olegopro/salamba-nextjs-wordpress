<?php
/*
 * WPSPowerbox
 * @author   AcmeeDesign
 * @url     http://acmeedesign.com
*/

if ( ! class_exists( 'ManageFont' ) ) {
    class ManageFont extends WPSHAPERE {

        private static $instance;

        function __construct() {
            $this->fonts_list = parent::get_wps_option_data(POWERBOX_OPTIONS_SLUG);
            add_action('admin_menu', array($this, 'wpspb_createmenu'), 20);
            add_action('admin_enqueue_scripts', array($this, 'wpspb_assets'));
            add_action('login_enqueue_scripts', array($this, 'wpspb_assets'));
            add_action('plugins_loaded', array($this, 'save_data'));

            add_action('wp_ajax_showfontvariants', array($this, 'get_fontvariants'));
            add_action('admin_head', array($this, 'wpspb_apply_fonts'));
            add_action('login_head', array($this, 'wpspb_apply_fonts'));
        }

        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof ManageFont)) {
                self::$instance = new ManageFont();
            }

            return self::$instance;
        }

        public function wpspb_createmenu()
        {
            add_submenu_page( WPSHAPERE_MENU_SLUG , __('Add Google Fonts', 'powerbox'), __('Google Fonts', 'powerbox'), 'manage_options', POWERBOX_FONT_PAGE_SLUG, array($this, 'wpspb_createform') );
        }

        function save_data() {
            if(isset($_POST['manage_fonts'])) {
                $custom_data = array();$saved_data = array();
                if($_POST){

                    $primary_font = (isset($_POST['manage']['fonts']['primary_font']['name'])
                                        && !empty($_POST['manage']['fonts']['primary_font']['name'])) ? $_POST['manage']['fonts']['primary_font']['name'] : "";

                    $secondary_font = (isset($_POST['manage']['fonts']['secondary_font']['name'])
                                        && !empty($_POST['manage']['fonts']['secondary_font']['name'])) ? $_POST['manage']['fonts']['secondary_font']['name'] : "";

                    $primary_font_variants = (isset($_POST['manage']['fonts']['primary_font']['variants'])
                                        && !empty($_POST['manage']['fonts']['primary_font']['variants'])) ? $_POST['manage']['fonts']['primary_font']['variants'] : "";

                    $secondary_font_variants = (isset($_POST['manage']['fonts']['secondary_font']['variants'])
                                        && !empty($_POST['manage']['fonts']['secondary_font']['variants'])) ? $_POST['manage']['fonts']['secondary_font']['variants'] : "";

                    $heading_font = (isset($_POST['manage']['fonts']['heading_font'])
                                        && !empty($_POST['manage']['fonts']['heading_font'])) ? $_POST['manage']['fonts']['heading_font'] : "";

                    $body_font = (isset($_POST['manage']['fonts']['body_font'])
                                        && !empty($_POST['manage']['fonts']['body_font'])) ? $_POST['manage']['fonts']['body_font'] : "";

                    $custom_data['manage']['fonts'] = array(
                        'primary_font'              => $primary_font,
                        'primary_font_weight'       => $primary_font_variants,
                        'secondary_font'            => $secondary_font,
                        'secondary_font_weight'     => $secondary_font_variants,
                        'heading_font'              => $heading_font,
                        'body_font'                 => $body_font
                    );
                }

                $saved_data = parent::get_wps_option_data(POWERBOX_OPTIONS_SLUG);
                if($saved_data){
                    $data = parent::wps_array_merge($saved_data, $custom_data);
                }
                else
                    $data = $custom_data;

                parent::updateOption(POWERBOX_OPTIONS_SLUG, $data);
                wp_safe_redirect( admin_url( 'admin.php?page='.POWERBOX_FONT_PAGE_SLUG ) );
                exit();
            }
        }

        public function wpspb_fontlist($selectfont) {
            $aof_gfonts_list = new AOFgfonts();
            $gfonts = $aof_gfonts_list->get_gfonts();
            if(isset($gfonts) && !empty($gfonts)){
                foreach($gfonts as $key => $font){
                    if($font['name'] == $selectfont) {
                        return $gfonts[$key];
                    }
                }
            }

            return false;
        }

        public function dropdown($list, $name, $id, $selectedtext)
        {
            printf('<select name="%1$s" id="%2$s">', $name, $id );
            print('<option value="">--Choose Fonts--</option>');
            if($list):
                foreach($list as $item_key => $item):
                    $selected = "";
                    if(is_array($selectedtext)):
                        if (in_array($item['name'], $selectedtext)):
                            $selected = "selected";
                        endif;
                    else:
                        if($selectedtext == $item['name']):
                            $selected = "selected";
                        endif;
                    endif;
                    printf('<option value="%1$s" %2$s >%3$s</option>', $item['name'], $selected, $item['name']);
                    unset($selected);
                endforeach;
            endif;
            echo '</select>';
        }

        public function get_fontvariants() {
            check_ajax_referer( "showfontvariants" , 'security');
            $aof_gfonts_list = new AOFgfonts();
            $fontslist = $aof_gfonts_list->get_gfonts();
            $fontname = $_POST['fontname'];
            $selectedfont = $this->wpspb_fontlist($fontname);
            $chkfonts = array();
            if (isset($this->fonts_list['manage']['fonts'][$fontname])
                    && !empty($this->fonts_list['manage']['fonts'][$fontname])) {

                $chkfonts['checked'] =  $this->fonts_list['manage']['fonts'][$fontname];
            }

            if(isset($selectedfont) && !empty($selectedfont)){
                $selectedfont = array_merge($selectedfont, $chkfonts);
                echo json_encode($selectedfont);
            } else {
                echo json_encode(array(''));
            }

            die();
        }

        public function wpspb_createform()
        {
            $aof_gfonts_list = new AOFgfonts();
            $fontslist = $aof_gfonts_list->get_gfonts();
            $optlist = array(array('name' => 'Primary Font'), array('name' =>'Secondary Font'));

            ?>
            <div class="wrap wps-wrap">
                <h2><?php esc_html_e('Add Google Fonts', 'powerbox'); ?></h2>

                <form name="powerbox_manage_font" method="post">
                  <table>
                    <tr>
                      <td colspan="2">
                        <h3>
                            <?php esc_html_e('Set Font for Headings', 'powerbox'); ?>
                        </h3>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <?php esc_html_e('Select Font Family', 'powerbox'); ?>
                      </td>
                      <td>
                        <p>
                            <?php
                            $selected = "";
                            if(isset($this->fonts_list['manage']['fonts']['primary_font']) && !empty($this->fonts_list['manage']['fonts']['primary_font'])){
                                $selected = $this->fonts_list['manage']['fonts']['primary_font'];
                            }
                            $this->dropdown($fontslist, 'manage[fonts][primary_font][name]', 'manage_fonts_primary_font', $selected);
                            unset($selected);
                            ?>
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <p>
                            <?php esc_html_e('Select Font Weight', 'powerbox'); ?>
                        </p>
                      </td>
                      <td>
                        <p>
                            <?php
                            print('<select name="manage[fonts][primary_font][variants]" id="manage_fonts_primary_variants">');
                            print('<option value="">--Choose Variants--</option>');
                            if(isset($this->fonts_list['manage']['fonts']['primary_font']) && !empty($this->fonts_list['manage']['fonts']['primary_font'])){

                                $fontname = $this->fonts_list['manage']['fonts']['primary_font'];
                                $selectedfont = $this->wpspb_fontlist($fontname);
                                if (isset($selectedfont['variants']) && !empty($selectedfont['variants'])){
                                    foreach($selectedfont['variants'] as $key => $row){
                                        $selected = "";

                                        if(isset($this->fonts_list['manage']['fonts']['primary_font_weight']) && !empty($this->fonts_list['manage']['fonts']['primary_font_weight'])){
                                            if ($row == $this->fonts_list['manage']['fonts']['primary_font_weight']) {
                                                $selected = "selected";
                                            }
                                        }

                                        printf('<option value="%s" %s>%s</option>', $row, $selected, $row);
                                    }
                                }
                            }
                            print('</select>');
                            ?>
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <h3>
                            <?php esc_html_e('Set Font for Body (Other Contents)', 'powerbox'); ?>
                        </h3>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <p>
                            <?php esc_html_e('Select Font Family', 'powerbox'); ?>
                        </p>
                      </td>
                      <td>
                        <p>
                            <?php
                            $selected = "";
                            if(isset($this->fonts_list['manage']['fonts']['secondary_font']) && !empty($this->fonts_list['manage']['fonts']['secondary_font'])){
                                $selected = $this->fonts_list['manage']['fonts']['secondary_font'];
                            }
                            $this->dropdown($fontslist, 'manage[fonts][secondary_font][name]', 'manage_fonts_secondary_font', $selected);
                            unset($selected);
                            ?>
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <p>
                            <?php esc_html_e('Select Font Weight', 'powerbox'); ?>
                        </p>
                      </td>
                      <td>
                        <p>
                            <?php
                            print('<select name="manage[fonts][secondary_font][variants]" id="manage_fonts_secondary_variants">');
                            print('<option value="">--Choose Variants--</option>');
                            if(isset($this->fonts_list['manage']['fonts']['secondary_font']) && !empty($this->fonts_list['manage']['fonts']['secondary_font'])){

                                $fontname = $this->fonts_list['manage']['fonts']['secondary_font'];
                                $selectedfont = $this->wpspb_fontlist($fontname);
                                if (isset($selectedfont['variants']) && !empty($selectedfont['variants'])){
                                    foreach($selectedfont['variants'] as $key => $row){
                                        $selected = "";

                                        if(isset($this->fonts_list['manage']['fonts']['secondary_font_weight']) && !empty($this->fonts_list['manage']['fonts']['secondary_font_weight'])){
                                            if ($row == $this->fonts_list['manage']['fonts']['secondary_font_weight']) {
                                                $selected = "selected";
                                            }
                                        }

                                        printf('<option value="%s" %s>%s</option>', $row, $selected, $row);
                                    }
                                }
                            }
                            print('</select>');
                            ?>
                        </p>
                      </td>
                    </tr>
                  </table>


                    <input type="hidden" name="manage_fonts" value="" />
                    <input type="submit"  class="button button-primary button-large" value="<?php esc_html_e('Save Changes', 'powerbox'); ?>" />
                </form>
            </div>
            <?php
        }

        public function wpspb_assets($nowpage)
        {
            $primaryfontname = (isset($this->fonts_list['manage']['fonts']['primary_font']) && !empty($this->fonts_list['manage']['fonts']['primary_font'])) ? $this->fonts_list['manage']['fonts']['primary_font'] : "";
            $secondaryfontname = (isset($this->fonts_list['manage']['fonts']['secondary_font']) && !empty($this->fonts_list['manage']['fonts']['secondary_font'])) ? $this->fonts_list['manage']['fonts']['secondary_font'] : "";

            $primaryfontvariants =  (isset($this->fonts_list['manage']['fonts']['primary_font_weight']) && !empty($this->fonts_list['manage']['fonts']['primary_font_weight'])) ? $this->fonts_list['manage']['fonts']['primary_font_weight'] : "";
            $secondaryfontvariants =  (isset($this->fonts_list['manage']['fonts']['secondary_font_weight']) && !empty($this->fonts_list['manage']['fonts']['secondary_font_weight'])) ? $this->fonts_list['manage']['fonts']['secondary_font_weight'] : "";

            if(!empty($primaryfontname) && !empty($secondaryfontname) && $primaryfontname == $secondaryfontname) {
                $url = POWERBOX_GOOGLE_FONT_PATH;
                if ($primaryfontname)
                    $url .= "family=".$primaryfontname;

                if($primaryfontvariants && $secondaryfontvariants){
                    $variants = $primaryfontvariants.','.$secondaryfontvariants;
                    $variantslist = explode(",", $variants);
                    $variantslist = array_unique($variantslist);
                    $url .= ':'. implode(",", $variantslist);
                } else {
                    if($primaryfontvariants)
                        $url .= ':'.$primaryfontvariants;

                    if($secondaryfontvariants)
                        $url .= ':'.$secondaryfontvariants;
                }

                wp_enqueue_style( 'wps-google-primary-fonts', $url, false );

            } else {
                if ($primaryfontname){
                    $url = POWERBOX_GOOGLE_FONT_PATH;
                    if ($primaryfontname)
                        $url .= "family=".$primaryfontname;
                    if($primaryfontvariants)
                        $url .= ':'.$primaryfontvariants;

                    wp_enqueue_style( 'wps-google-primary-fonts', $url, false );
                }

                if ($secondaryfontname) {
                    $url = POWERBOX_GOOGLE_FONT_PATH;
                    if ($secondaryfontname)
                        $url .= "family=".$secondaryfontname;
                    if($secondaryfontvariants)
                        $url .= ':'.$secondaryfontvariants;

                    wp_enqueue_style( 'wps-google-secondary-fonts', $url, false );
                }
            }

            if('wpshapere_page_powerbox_font_options' == $nowpage) {
              wp_register_style( 'wps-multiselect-style', POWERBOX_DIR_URI . 'assets/css/select2.css' );
              wp_enqueue_style( 'wps-multiselect-style' );
              wp_enqueue_script('jquery');
              wp_enqueue_script('wps-multiselect', POWERBOX_DIR_URI . 'assets/js/select2.js', array('jquery'), '', false);
              wp_enqueue_script('wpspb-scripts', POWERBOX_DIR_URI . 'assets/js/wpspb-scripts.min.js', array('jquery'), '', false);
              $ajax_url = array( 'admin_ajax_url' => admin_url( 'admin-ajax.php' ) ,'nonce' => wp_create_nonce( 'showfontvariants' ) );
              wp_localize_script( 'wpspb-scripts', 'wpspb_admin_ajax_url',$ajax_url );
            }
        }

        function wpspb_apply_fonts() {
          if(isset($this->fonts_list['manage']['fonts'])) {
            $primary_font = isset($this->fonts_list['manage']['fonts']['primary_font']) ? $this->fonts_list['manage']['fonts']['primary_font'] : "";
            $primary_font_weight = isset($this->fonts_list['manage']['fonts']['primary_font_weight']) ? $this->fonts_list['manage']['fonts']['primary_font_weight'] : "500";
            $secondary_font = isset($this->fonts_list['manage']['fonts']['secondary_font']) ? $this->fonts_list['manage']['fonts']['secondary_font'] : "";
            $secondary_font_weight = isset($this->fonts_list['manage']['fonts']['secondary_font_weight']) ? $this->fonts_list['manage']['fonts']['secondary_font_weight'] : "300";

            $g_font_styles = "";
            if(isset($primary_font) && !empty($primary_font)) {
              $g_font_styles .= "h1,h2,h3,h4,h5,h6{font-family:'" . $primary_font . "';font-weight:" . $primary_font_weight . ";}";
            }
            if(isset($secondary_font) && !empty($secondary_font)) {
              $g_font_styles .= "body{font-family:'" . $secondary_font . "';font-weight:" . $secondary_font_weight . ";}";
            }
            ?>
            <style>
            <?php
              echo wp_kses($g_font_styles, array());
             ?>
            </style>
            <?php
          }
        }

    }
}

function CustomizeFonts_init()
{
    ManageFont::instance();
}
CustomizeFonts_init();
