<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Juli 2018
 */
require_once __DIR__.'/lib/class.simple_nav.php';
require_once __DIR__.'/lib/class.simple_nav_config.php';
#
# --- CSS-File auch im Backend einbinden
$my_package=$this->getPackageId();
$file=rex_url::addonAssets($my_package).$my_package.'.css';
rex_view::addCssFile($file);
?>
