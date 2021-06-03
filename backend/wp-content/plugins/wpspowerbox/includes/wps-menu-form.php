<?php
/*
 * WPSPowerbox
 * @author   AcmeeDesign
 * @url     https://acmeedesign.com
*/

$action_type = ( isset( $_GET['action'] ) && $_GET['action'] == 'wps_edit_menu' ) ? 'menu_edit' : 'menu_new';
?>

  <div class="wps-menu-save-note">
    <?php _e('Menu settings have changed, you should save menu!', 'powerbox'); ?>
  </div>
  <div class="dd" id="domenu-0">
  <button class="dd-new-item"><span class="lni lni-plus"></span><?php _e('Add Parent Menu', 'powerbox'); ?></button>
  <li class="dd-item-blueprint">
    <button class="collapse" data-action="collapse" type="button" style="display: none;">–</button>
    <button class="expand" data-action="expand" type="button" style="display: none;">+</button>
    <div class="dd-handle dd3-handle"></div>
    <div class="dd3-content">
      <span class="item-name">[item_name]</span>
      <span class="menu-group-title"><?php _e('Menu group title', 'powerbox'); ?></span>
      <span class="menu-separator"><?php _e('Menu Separator', 'powerbox'); ?></span>
      <div class="dd-button-container">
        <div class="dd-buttons-wrap tooltip-add">
          <span class="dd-tooltip"><?php _e('Edit menu', 'powerbox'); ?></span>
          <button class="edit-menu-btn"><span class="lni lni-pencil"></span></button>
        </div>
        <div class="dd-buttons-wrap tooltip-add">
          <span class="dd-tooltip"><?php _e('Add sub menu', 'powerbox'); ?></span>
          <button class="item-add">+</button>
        </div>
        <div class="dd-buttons-wrap tooltip-rem">
          <span class="dd-tooltip"><?php _e('Delete menu', 'powerbox'); ?></span>
          <button class="item-remove" data-confirm-class="item-remove-confirm">&times;</button>
        </div>
      </div>
      <div class="dd-edit-box" style="display: none;">
        <input class="wps-menu-title" type="text" name="title" autocomplete="off" placeholder="Item"
               data-placeholder="Enter title"
               data-default-value="Powerbox menu {?numeric.increment}" />
        <input class="wps-menu-url" type="text" name="wps_menu_url" value="" data-placeholder="Enter url" />
        <div class="menu_icon">
            <label for="icon_picker"><em><?php _e('Choose Icon', 'powerbox'); ?></em></label>
            <div id="" data-target="#menu-icon-for-" class="icon-picker divicon"></div>
  					<input type="hidden" id="menu-icon-for-" name="menu_icon" class="txticon" />
        </div>
        <div class="wps-menu-type-wrap">
          <label for="wps_menu_type"><?php echo __('Select menu type', 'powerbox'); ?></label>
          <select class="wps-menu-type" name="wps_menu_type">
            <option value="1"><?php echo __('Menu', 'powerbox'); ?></option>
            <option value="2"><?php echo __('Group title', 'powerbox'); ?></option>
            <option value="3"><?php echo __('Separator', 'powerbox'); ?></option>
          </select>
        </div>
        <p class="note"><?php _e('To add a separator, put only a hyphen (-) in the title field.', 'powerbox'); ?></p>
        <i class="end-edit">save</i>
      </div>
    </div>
  </li>
  <ol class="dd-list"></ol>

  <div id="wps-custom-menu-set" class="wps-custom-menu-set">
    <div class="wps-settings-title">
      <h2><?php echo __('Powerbox Custom Menu', 'powerbox'); ?></h2>
    </div>
    <div class="wps-settings-desc white">
      <?php echo __('Menu Settings', 'powerbox'); ?>
    </div>
    <form class="wps-custom-menu-set-form" name="wps-custom-menu-set" method="post">
      <div class="wps-menu-field-wrap">

        <h3><?php echo __('Menu title', 'powerbox'); ?> *</h3>
        <input class="wps-menu-field wps-menuset-title" type="text" name="wps_menu_title" size="30" value="<?php if ( !empty( $menu_title ) ) echo esc_html( $menu_title ) ?>" spellcheck="true" autocomplete="off">

      </div>

      <div class="wps-menu-field-wrap">
        <h3><?php echo __('Status', 'powerbox'); ?></h3>
        <?php
        if( empty( $status ) )
          $status = 'publish';
        $status_selected = ( $status == 'publish' ) ? 'selected' : '';
         ?>
        <select class="wps-menu-field wps-menu-status" name="wps_menu_status">
          <option value="publish" <?php if( $status == 'publish' ) echo 'selected'; ?>><?php echo __('Publish', 'powerbox'); ?></option>
          <option value="draft" <?php if( $status == 'draft' ) echo 'selected'; ?>><?php echo __('Draft', 'powerbox'); ?></option>
        </select>
      </div>

    <?php
      if( !empty( $get_all_roles ) ) {
    ?>
      <h3><?php echo __('Menu set applies to:', 'powerbox'); ?></h3>
      <div class="wps-menu-field-wrap menu-appliesto">
      <?php

          echo '<div class="wps-menu-tooltip">' .
          __('User roles assigned to other menu sets will be hidden here. In order use previously assigned user roles, delete that menu set.', 'powerbox')
          . '</div>';
          echo '<select class="wps-menu-field wps-menu-roleid" name="user_role_id">';
          foreach ( $get_all_roles as $key => $value) {
            $role_selected = ( $key == $user_data ) ? 'selected' : '';
            echo '<option value="'. $key .'"' . $role_selected .'>' .$value . '</option>';
          }
          echo '</select>';

       ?>
      </div>
       <?php
       }

       $menu_data = ( !empty( $menu_data ) ) ? $menu_data : '';

       ?>
      <h3><?php echo __('Additional block URLs (separate with comma)', 'powerbox'); ?></h3>
      <textarea style="width: 100%; min-height: 100px;" class="block-urls" name="block_urls"><?php if( !empty( $blocked_urls ) ) echo $blocked_urls; ?></textarea>
      <small><?php echo __('Add extra urls to restrict by Powerbox plugin.', 'powerbox'); ?></small>

      <h3><?php echo __('Additional unblock URLs (separate with comma)', 'powerbox'); ?></h3>
      <textarea style="width: 100%; min-height: 100px;" class="unblock-urls" name="unblock_urls"><?php if( !empty( $unblocked_urls ) ) echo $unblocked_urls; ?></textarea>
      <small><?php echo __('Add unblock urls to restricted by Powerbox plugin.', 'powerbox'); ?></small>
      <small><?php echo __("Note: This doesn't unblock urls for users who do not have permission to access the URL. The must have enough permission with WordPress capability to access the url.", 'powerbox'); ?></small>


      <textarea style="width: 100%; min-height: 100px;" name="wps_custom_menu_set_data" class="jsonOutput"><?php if( !empty( $menu_data ) ) echo esc_html( $menu_data ) ?></textarea>
      <input type="hidden" name="custom_admin_menu_set" value="<?php echo esc_html( $action_type ); ?>" />
      <input type="hidden" name="wps_edit_menu_id" value="<?php if( isset( $wps_menu_id ) && !empty( $wps_menu_id )) echo esc_html( $wps_menu_id ); ?>" />
      <button disabled type="submit" class="wps-button wps-save-action"><?php echo __('Save Menu', 'powerbox'); ?></button>
      <div class="wps-menuform-message"><span class="dashicons dashicons-warning"></span> <?php echo __('Save each menu item before Saving the Menu.', 'powerbox'); ?></div>
    </form>

    <div class="wps-menu-form-hints">
      <h4><?php echo __('HINTS', 'powerbox'); ?></h4>
      <ul>
        <li>
          <div class="edit-menu-buttons wps-menu-edit-btn"><span class="lni lni-pencil"></span></div> <span><?php echo __('Click to edit and configure the added menu item.', 'powerbox'); ?></span>
        </li>
        <li>
          <div class="edit-menu-buttons wps-submenu-add-btn"><span class="lni lni-plus"></span></div> <span><?php echo __('Add new sub menu item to the parent.', 'powerbox'); ?></span>
        </li>
        <li>
          <div class="edit-menu-buttons wps-submenu-add-remove"><span class="lni lni-close"></span></div> <span><?php echo __('Double click to delete and confirm delete.', 'powerbox'); ?></span>
        </li>
        <li>
          <div class="edit-menu-buttons wps-menu-drag"><span class="fa fa-navicon"></span></div> <span><?php echo __('Drag and drop menu items to desired position. Move a little right, to change it to a sub menu', 'powerbox'); ?></span>
        </li>
      </ul>
    </div>
  </div>

</div>
