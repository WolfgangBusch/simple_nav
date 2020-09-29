<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version September 2020
 */
#
# --- if not exist configuration data set default values
$data=simple_nav_config::get_config_data();
if(count($data)<=0):
  $data=simple_nav_config::get_default_data();
  simple_nav_config::set_config_data($data);
  endif;
?>
