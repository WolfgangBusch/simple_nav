<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Maerz 2018
 */
echo rex_view::title(rex_i18n::msg($this->getPackageId()));
rex_be_controller::includeCurrentPageSubPath();
?>
