<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Maerz 2018
 */
require_once __DIR__.'/functions/function.install.php';
require_once __DIR__.'/functions/function.simple_nav.php';
#
# --- CSS-File auch im Backend einbinden
$my_package=$this->getPackageId();
$file=rex_url::addonAssets($my_package).$my_package.'.css';
rex_view::addCssFile($file);
?>
