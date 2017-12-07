<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Dezember 2017
 */
require_once __DIR__.'/functions/function.install.php';
require_once __DIR__.'/functions/function.simple_nav.php';
#
# --- erstmaliges Erzeugen des Stylesheet-Verzeichnisses
$dir=rex_path::assets()."addons/simple_nav";
if(!file_exists($dir)) mkdir($dir);
#
# --- erstmaliges Erzeugen der Stylesheet-Datei
$file=rex_path::assets()."addons/simple_nav/simple_nav.css";
if(!file_exists($file))
  simple_nav_write_css($data);
?>
