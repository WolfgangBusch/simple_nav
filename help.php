<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version August 2023
 */
$intro=file_get_contents(__DIR__.'/README.md');
$intro=substr($intro,strpos($intro,'<div>'));
$len=strpos($intro,'</div>')+6;
echo substr($intro,0,$len);
?>
