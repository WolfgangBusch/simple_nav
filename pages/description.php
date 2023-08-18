<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version August 2023
 */
$descr=file_get_contents(dirname(__DIR__).'/README.md');
$descr=substr($descr,strpos($descr,'<div><br>Die Navigationszeilen'));
echo $descr;
?>
