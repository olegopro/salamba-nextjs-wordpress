<?php
/*
 * WPSSPowerbox
 * @author   AcmeeDesign
 * @url     https://acmeedesign.com
*/

$current_url = $this->get_url();

$is_dashboard = $this->is_dashboard();

  if(!empty($this->aof_options['wpspb_ext_logo_url']) && filter_var($this->aof_options['wpspb_ext_logo_url'], FILTER_VALIDATE_URL)) {
    $custom_logo = esc_url( $this->aof_options['wpspb_ext_logo_url']);
  }
  elseif( !empty( $this->aof_options['wpspb_sidebar_logo'] ) ) {
    $custom_logo = ( is_numeric($this->aof_options['wpspb_sidebar_logo']) ) ? $this->get_wps_image_url($this->aof_options['wpspb_sidebar_logo']) : $this->aof_options['wpspb_sidebar_logo'];
  }

  if(!empty($this->aof_options['wpspb_ext_logo_url_collapsed']) && filter_var($this->aof_options['wpspb_ext_logo_url_collapsed'], FILTER_VALIDATE_URL)) {
    $pinned_logo = esc_url( $this->aof_options['wpspb_ext_logo_url_collapsed']);
  }
  elseif( !empty( $this->aof_options['wpspb_sidebar_logo_collapsed'] ) ) {
    $pinned_logo = ( is_numeric($this->aof_options['wpspb_sidebar_logo_collapsed']) ) ? $this->get_wps_image_url($this->aof_options['wpspb_sidebar_logo_collapsed']) : $this->aof_options['wpspb_sidebar_logo_collapsed'];
  }

  $admin_logo_url = ( !empty($this->aof_options['adminbar_logo_link']) ) ? $this->aof_options['adminbar_logo_link'] : admin_url();
 ?>

<div class="wps-mobile-nav">
  <a class="wps-mobile-nav-logo" href="<?php echo esc_url( $admin_logo_url ); ?>"><img src="<?php echo esc_url( $pinned_logo ); ?>" alt="<?php bloginfo('name'); ?>" /></a>
  <a class="mob-hamburger" id="toggle-sidebar" href="#"><i class="fa fa-bars"></i></a>
</div>
<nav id="wps-sidebar" class="wps-sidebar-wrapper">

    <div class="wps-sidebar-content">
      <div class="wps-sidebar-header">
        <div class="hamburger"><a id="pin-sidebar" href="#"><i class="fa fa-bars"></i></a></div>
        <!-- wps-sidebar-brand  -->
        <div class="wps-sidebar-item wps-sidebar-brand">
            <a class="default-logo" href="<?php echo esc_url( $admin_logo_url ); ?>"><img src="<?php echo esc_url( $custom_logo ); ?>" alt="<?php bloginfo('name'); ?>" /></a>
            <a class="pinned-logo" href="<?php echo esc_url( $admin_logo_url ); ?>"><img src="<?php echo esc_url( $pinned_logo ); ?>" alt="<?php bloginfo('name'); ?>" /></a>
        </div>
      </div>
      <!-- wps-sidebar-header  -->
      <div class="wps-sidebar-item wps-sidebar-userinfo d-flex flex-nowrap">
          <div class="user-info">
              <?php echo $this->menu_user_avatar() ?>
          </div>
      </div>

        <!-- wps-sidebar-menu  -->
        <div class=" wps-sidebar-item wps-sidebar-menu">
            <ul>
              <?php
                if ( !empty ( $wps_menu_data ) ) {
                  $wps_admin_menu = json_decode ( $wps_menu_data->menu_data );

                  //get current menu page
                  $wps_current_page = "";
                  if( ! $is_dashboard && isset( $_COOKIE['wps_current_menu']) ) {
                    $wps_current_page = $_COOKIE['wps_current_menu'];
                  }

                  $list_id = 0;

                  foreach ( $wps_admin_menu as $wps_menu_key => $wps_menu_data ) {

                    $wps_current_menu_class = ( 'wps-menu-' . $wps_menu_data->id == $wps_current_page ) ? 'wps-curent-menu-item' : '';

                    if( empty( $wps_current_page ) && $is_dashboard ) {
                      $dashboard_url = admin_url();
                      if( rtrim( $dashboard_url, '/' ) == rtrim( $wps_menu_data->wps_menu_url, '/' ) ||
                        $dashboard_url . 'index.php' == rtrim( $wps_menu_data->wps_menu_url, '/' ) ) {
                        $wps_current_menu_class = 'wps-curent-menu-item';
                      }
                    }

                    $wps_menu_type = ( !empty( $wps_menu_data->wps_menu_type && is_numeric( $wps_menu_data->wps_menu_type ) ) ) ? $wps_menu_data->wps_menu_type : '1';

                    if( !empty( $wps_menu_type ) && $wps_menu_type == 3 ) {
                      echo '<li id="wps-menu-' . $wps_menu_data->id .'" class="wps-separator"><span></span></li>';
                    }
                    elseif( !empty( $wps_menu_type ) && $wps_menu_type == 2 ) {
                      //show group title
                      echo '<li id="wps-menu-' . $wps_menu_data->id .'" class="header-menu"><span>' . $wps_menu_data->title . '</span></li>';
                    }
                    elseif( isset( $wps_menu_data->children ) && !empty( $wps_menu_data->children ) ) {

                      $wps_submenu_lists = '';
                      foreach ( $wps_menu_data->children as $wps_submenu_data ) {
                        $wps_submenu_lists .= '<li id="wps-menu-' . $wps_submenu_data->id .'" class="wps-submenu-item">
                            <a href="' . $wps_submenu_data->wps_menu_url . '">' . $wps_submenu_data->title .'</a>
                        </li>';
                      }

                      $wps_menu_icon = $this->wpspb_menu_icon( $wps_menu_data->menu_icon );

                      echo '<li id="wps-menu-' . $wps_menu_data->id .'" class="wps-sidebar-dropdown wps-menu-item ' . $wps_current_menu_class . '">';
                      echo '<a href="#">';
                      if( !empty( $wps_menu_icon ) ) echo wp_kses_post( $wps_menu_icon );
                      echo '<span class="menu-text">' . $wps_menu_data->title . '</span>' .
                      '</a>';
                      echo '<div class="wps-sidebar-submenu"><ul>';

                      echo $wps_submenu_lists;

                      echo '</ul>
                      </div>';
                      echo '</li>';

                    }
                    else {

                      if( empty( $wps_current_page ) && $is_dashboard ) {
                        $dashboard_url = admin_url();
                        if( rtrim( $dashboard_url, '/' ) == rtrim( $wps_menu_data->wps_menu_url, '/' ) ||
                          $dashboard_url . 'index.php' == rtrim( $wps_menu_data->wps_menu_url, '/' ) ) {
                          $wps_current_menu_class = 'wps-curent-menu-item';
                        }
                      }

                      $wps_menu_icon = $this->wpspb_menu_icon( $wps_menu_data->menu_icon );
                      echo '<li id="wps-menu-' . $wps_menu_data->id .'" class="wps-menu-item ' . $wps_current_menu_class . '">
                          <a href="' . $wps_menu_data->wps_menu_url . '">';
                      if( !empty( $wps_menu_icon ) ) echo wp_kses_post( $wps_menu_icon );
                      echo '<span class="menu-text">' . $wps_menu_data->title . '</span>
                          </a>
                      </li>';

                    }

                  }
                }
              ?>

            </ul>
        </div>
        <!-- wps-sidebar-menu  -->
    </div>

</nav>
<?php
