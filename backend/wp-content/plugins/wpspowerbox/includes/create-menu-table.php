<?php
/*
 * WPSPowerbox
 * @author   AcmeeDesign
 * @url     http://acmeedesign.com
*/

defined('ABSPATH') || die;

global $wps_menu_table_version;

/**
* WPS menu table structure
* @since db version 1.0
*/
$create_menu_table = "CREATE TABLE IF NOT EXISTS $wps_menu_table (
      id int(11) NOT NULL AUTO_INCREMENT,
      menu_title varchar(250) NOT NULL,
      menu_data longtext,
      user_type varchar(20),
      menu_role_id varchar(100),
      status varchar(20),
      other_data longtext,
      PRIMARY KEY  (id)
  ) $charset_collate;";
