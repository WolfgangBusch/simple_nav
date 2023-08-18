<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version August 2023
 */
#
class simple_nav_config {
#
#   Basis-Funktionen
#      get_default_data()
#      get_config_data()
#      set_config_data($data)
#      inthex($int)
#      icon_colors($bgcol,$fac)
#   Installation
#      write_css_file($data)
#      write_svg_icons($bgcol)
#      set_configuration()
#   Konfigurationsmenue:
#      split_color($color)
#      split_data($data)
#      join_data($daten)
#      config_lines()
#      config_form($msg,$daten)
#      print_config_form()
#
# --------------------------- Konstanten
const this_addon=simple_nav::this_addon;   // AddOn-Identifier
#
# --------------------------- Basis-Funktionen
public static function get_default_data() {
   #   Rueckgabe der Default-Werte der Stylesheet-Daten als assoziatives Array.
   #   Hier wird die Reihenfolge der Schluessel festgelegt.
   #
   $addon=self::this_addon;
   $defdata=array(
      $addon::NAV_TYP        =>2,
      $addon::NAV_INDENT     =>10,
      $addon::NAV_FOLDER_ICON=>0,
      $addon::NAV_FILE_ICON  =>0,
      $addon::NAV_WIDTH      =>150,
      $addon::NAV_LINE_HEIGHT=>floatval(0.8),
      $addon::NAV_FONT_SIZE  =>floatval(0.8),
      $addon::NAV_BOR_LRWIDTH=>0,
      $addon::NAV_BOR_OUWIDTH=>1,
      $addon::NAV_BOR_RAD    =>0,
      $addon::NAV_COL_LINK   =>'rgba(153, 51,  0,1)',
      $addon::NAV_COL_BORD_0 =>'rgba(255,190, 60,1)',
      $addon::NAV_COL_BACK_0 =>'rgba(255,255,255,0)',
      $addon::NAV_COL_BORD_1 =>'rgba(255,190, 60,1)',
      $addon::NAV_COL_BACK_1 =>'rgba(255,190, 60,0.3)',
      $addon::NAV_COL_BORD_2 =>'rgba(255,190, 60,1)',
      $addon::NAV_COL_BACK_2 =>'rgba(204,102, 51,1)',
      $addon::NAV_COL_TEXT_2 =>'rgba(255,255,255,1)');
   #
   # --- Trimmen der rgba-Parameter / Dezimalkomma durch Dezimalpunkt ersetzen
   $keys=array_keys($defdata);
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $val=$defdata[$key];
      if(substr($val,0,4)=='rgba'):
        $arr=explode(',',$val);
        $val='rgba('.trim(substr($arr[0],5)).','.trim($arr[1]).','.
           trim($arr[2]).','.trim(substr($arr[3],0,strlen($arr[3])-1)).')';
        $defdata[$key]=$val;
        else:
        $defdata[$key]=str_replace(',','.',$val);
        endif;
      endfor;
   return $defdata;
   }
public static function get_config_data() {
   #   Rueckgabe der konfigurierten Daten. Falls noch keine Konfiguration
   #   definiert wurde, wird ein leeres Array zurueck gegeben.
   #   benutzte functions:
   #      self::get_default_data()
   #
   $addon=self::this_addon;
   #
   # --- Default-Konfigurationsdaten
   $defdat=self::get_default_data();
   $keys=array_keys($defdat);
   #
   # --- Auslesen der Konfigurationsdaten
   $confdat=array();
   $leer=TRUE;
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $val=rex_config::get($addon,$key);
      $confdat[$key]='';
      if(!empty($val) or $val=='0') $confdat[$key]=$val;
      if(!empty($val)) $leer=FALSE;
      endfor;
   if(!$leer):
     return $confdat;
     else:
     return array();
     endif;
   }
public static function set_config_data($data) {
   #   Schreiben der Konfigurationsdaten.
   #   $data             Array der zu schreibenden Daten
   #   benutzte functions:
   #      self::get_default_data()
   #
   # --- Default-Konfigurationsdaten
   $defdat=self::get_default_data();
   $keys=array_keys($defdat);
   #
   $confdat=array();
   $leer=TRUE;
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $val=$data[$key];
      if(is_integer($val)) $val=intval($val);
      if(is_float($val))   $val=floatval($val);
      $confdat[$key]=$val;
      if(!empty($val)) $leer=FALSE;
      endfor;
   if(!$leer)
     for($i=0;$i<count($keys);$i=$i+1)
        rex_config::set(self::this_addon,$keys[$i],$confdat[$keys[$i]]);
   }
public static function inthex($int) {
   #   Rueckgabe der Hexadezimal-Darstellung einer Dezimalzahl.
   #   $int              gegebene Dezimalzahl (0 <= $int <= 255)
   #
   $k1=intval($int/16);
   $k2=$int-$k1*16;
   #
   if($k2<10)  $hex2=strval($k2);
   if($k2==10) $hex2='a';
   if($k2==11) $hex2='b';
   if($k2==12) $hex2='c';
   if($k2==13) $hex2='d';
   if($k2==14) $hex2='e';
   if($k2==15) $hex2='f';
   if($k1<=0) return '0'.$hex2;
   #
   if($k1<10)  $hex1=strval($k1);
   if($k1==10) $hex1='a';
   if($k1==11) $hex1='b';
   if($k1==12) $hex1='c';
   if($k1==13) $hex1='d';
   if($k1==14) $hex1='e';
   if($k1==15) $hex1='f';
   return $hex1.$hex2;
   }
public static function icon_colors($bgcol,$fac) {
   #   Rueckgabe von 3 Farben im Hex-Format basierend auf einer Grundfarbe im
   #   rgba-Format in Form eines nummerierten Arrays (Nummerierung ab 1).
   #   Dabei entspricht die Farbe 1 der Grundfarbe als helle Hintergrundfarbe.
   #   Die Farben 2 und 3 sind stufenweise dunkler als Farbe 1.
   #   $bgcol            Grundfarbe, der Opac-Anteil spielt keine Rolle
   #   $fac              Array der Abdunklungsfaktoren (Nummerierung ab 1),
   #                     Abdunkelung unterschiedlich in den rgb-Anteilen:
   #                     dunkelster Anteil: zweimal Faktor $fac[1] (am staerksten)
   #                     mittlerer Anteil:  zweimal Faktor $fac[2]
   #                     hellster Anteil:   zweimal Faktor $fac[3] (an wenigsten)
   #                                        $fac[1] < $fac[2] < $fac[3]
   #   benutzte functions:
   #      self::inthex($int)
   #
   $spcol=self::split_color($bgcol);
   $ccc=array();
   $ccc[1]=intval($spcol['red']);
   $ccc[2]=intval($spcol['green']);
   $ccc[3]=intval($spcol['blue']);
   $ddd=$ccc;
   #     Sortierung der Farbanteile: dunkel: $ddd[1], mittel: $ddd[2], hell: $ddd[3]
   asort($ddd);
   $keys=array_keys($ddd);
   $ind=array();
   for($i=0;$i<count($keys);$i=$i+1) $ind[$i+1]=$keys[$i];
   #
   # --- Hintergrundfarbe 1 (hell)
   $hex1='#'.self::inthex($ccc[1]).self::inthex($ccc[2]).self::inthex($ccc[3]);
   #
   # --- Farbe 2 (mittel, Farbe des Folder-Reiters)
   for($i=1;$i<=count($fac);$i=$i+1) 
      $ccc[$ind[$i]]=intval($fac[$i]*$ccc[$ind[$i]]);
   $hex2='#'.self::inthex($ccc[1]).self::inthex($ccc[2]).self::inthex($ccc[3]);
   # --- Farbe 3 (dunkel, Farbe des Folder-Randes)
   for($i=1;$i<=count($fac);$i=$i+1) 
      $ccc[$ind[$i]]=intval($fac[$i]*$ccc[$ind[$i]]);
   $hex3='#'.self::inthex($ccc[1]).self::inthex($ccc[2]).self::inthex($ccc[3]);
   #
   return array(1=>$hex1,2=>$hex2,3=>$hex3);
   }
#
# --------------------------- Installation
public static function write_css_file($data) {
   #   Schreiben der Stylesheets in den AddOn-Assets-Ordner /assets/addons/simple_nav/.
   #   $data             Array der konfigurierten Daten
   #
   $addon=self::this_addon;
   #
   # --- Auslesen der gesetzten Konfigurationsdaten
   $width       =$data[$addon::NAV_WIDTH];
   $line_height =$data[$addon::NAV_LINE_HEIGHT];
   $font_size   =$data[$addon::NAV_FONT_SIZE];
   $bor_lrwidth =$data[$addon::NAV_BOR_LRWIDTH];
   $bor_ouwidth =$data[$addon::NAV_BOR_OUWIDTH];
   $bor_rad     =$data[$addon::NAV_BOR_RAD];
   $col_link    =$data[$addon::NAV_COL_LINK];
   $col_border_0=$data[$addon::NAV_COL_BORD_0];
   $col_backgr_0=$data[$addon::NAV_COL_BACK_0];
   $col_border_1=$data[$addon::NAV_COL_BORD_1];
   $col_backgr_1=$data[$addon::NAV_COL_BACK_1];
   $col_border_2=$data[$addon::NAV_COL_BORD_2];
   $col_backgr_2=$data[$addon::NAV_COL_BACK_2];
   $col_text_2  =$data[$addon::NAV_COL_TEXT_2];
   #
   # --- Klassennamen der div-Container
   $typ0=$addon::DIV_TYPE.'0';
   $typ1=$addon::DIV_TYPE.'1';
   $typ2=$addon::DIV_TYPE.'2';
   #
   # --- Styles fuer die Navigation selbst
   $buffer=
'/*   s i m p l e _ n a v - N a v i g a t i o n   */
div.'.$addon::DIV_BORDER1.' {
    border-bottom:solid '.$bor_ouwidth.'px '.$col_border_0.';
    border-top:   solid '.$bor_ouwidth.'px '.$col_border_0.';     /* auch oberer Rand */
    border-left:  solid '.$bor_lrwidth.'px '.$col_border_0.';
    border-right: solid '.$bor_lrwidth.'px '.$col_border_0.';
    border-radius:'.$bor_rad.'em; }
div.'.$addon::DIV_BORDER.' {
    border-bottom:solid '.$bor_ouwidth.'px '.$col_border_0.';
    border-top:   solid 0px '.$col_border_0.';     /* kein oberer Rand */
    border-left:  solid '.$bor_lrwidth.'px '.$col_border_0.';
    border-right: solid '.$bor_lrwidth.'px '.$col_border_0.';
    border-radius:'.$bor_rad.'em; }
div.'.$addon::DIV_FORMAT.' { padding:3px; line-height:'.$line_height.'em;
    min-width:'.$width.'px; max-width:'.$width.'px; }
div.'.$typ0.'          { background-color:'.$col_backgr_0.'; }
div.'.$typ1.'          { background-color:'.$col_backgr_1.'; }
div.'.$typ2.'          { background-color:'.$col_backgr_2.'; }
div.'.$typ0.' div a    { font-size:'.$font_size.'em; color:'.$col_link.'; }
div.'.$typ1.' div a    { font-size:'.$font_size.'em; color:'.$col_link.'; }
div.'.$typ2.' div span { font-size:'.$font_size.'em; color:'.$col_text_2.'; }
div.'.$typ0.' div table, div.'.$typ1.' div table, div.'.$typ2.' div table { background-color:transparent; } 
div.'.$typ0.' div,       div.'.$typ1.' div,       div.'.$typ2.' div       { overflow:hidden; }
table.nav_tabline       { border-spacing:0; border:collapse:collapse; }
table.nav_tabline tr td { padding:0; vertical-align:top; }
.nav_ico                { margin-right:3px; min-width:'.$line_height.'em; max-width:'.$line_height.'em; }
/*   a n g e z e i g t    o d e r    v e r b o r g e n   */
#'.$addon.' { display:block; padding-top:0.25em; }
@media screen and (max-width:'.$addon::WIDTH_MOBIL.'em) { #'.$addon.' { display:none; } }';
   #
   # --- Styles fuer das Hamburger-Icon
   $buffer=$buffer.'
/*   H a m b u r g e r - / K r e u z - I c o n   */
#'.$addon::HAMBURGER.' { display:none; }
#'.$addon::KREUZ.' { display:none; }
@media screen and (max-width:'.$addon::WIDTH_MOBIL.'em) {
    #'.$addon::HAMBURGER.' { position:fixed; left:10px; margin-top:-1em; display:block;
        width:28px; cursor:pointer; }
    div.bar { margin:4px; height:2px; background-color:'.$col_backgr_2.'; }
    #'.$addon::KREUZ.' { position:fixed; left:10px; margin-top:-1em; display:none;
        width:28px; cursor:pointer; }
    div.cross1 { margin:4px; height:2px; background-color:'.$col_backgr_2.';
        transform:translateY(6px) rotate(45deg); }
    div.cross2 { margin:4px; height:2px; background-color:transparent; }
    div.cross3 { margin:4px; height:2px; background-color:'.$col_backgr_2.';
        transform:translateY(-6px) rotate(-45deg); }
    }';
   #
   # --- Styles fuer die Konfiguration
   $buffer=$buffer.'
/*   K o n f i g u r a t i o n   */
.nav_table  { background-color:inherit; }
.nav_guide  { padding-left:1em; line-height:1.5em; white-space:nowrap; }
.nav_center { padding-left:1em; text-align:center; white-space:nowrap; }
.nav_right  { text-align:right; }
.nav_fleft  { float:left; }
.nav_fright { float:right; }
.nav_input  { width:4em; line-height:1.5em; text-align:right;
              border:solid 1px silver; background-color:transparent; }
.nav_chkbox { font-weight:normal; }
.nav_select { width:3em; height:1.8em; text-align:right;
              border:solid 1px silver; background-color:transparent; }
.nav_short  { min-width:60px !important; max-width:60px !important; }
.nav_clink  { color:'.$col_link.'; font-size:'.$font_size.'em; }
.nav_ctext  { color:'.$col_text_2.'; font-size:'.$font_size.'em; }
/*   B e s c h r e i b u n g   */
.nav_hand   { padding-left:2em; }
.nav_box    { padding:2px; border:solid 1px silver; }';
   #
   # --- Styles fuer das Beispiel
   $buffer=$buffer.'
/*   B e i s p i e l   */
.xmp_pad    { padding:0 1em 0 1em; vertical-align:top; }
.xmp_bgcol  { background-color:rgb(230,230,230); }
.xmp_nav    { color:'.$col_link.'; }';
   #
   # --- Schreiben der Stylesheet-Datei in /assets/addons/simple_nav/
   #     noetigenfalls AddOn-Assets-Ordner neu erstellen
   $ordner=rex_path::addonAssets($addon);
   if(!file_exists($ordner)) mkdir($ordner);
   $file=rex_path::addonAssets($addon,$addon.'.css');
   $handle=fopen($file,'w');
   fwrite($handle,$buffer);
   fclose($handle);
   }
public static function write_svg_icons($bgcol) {
   #   Schreiben der SVG-Dateien fuer ein Folder-Icon und ein File-Icon fuer
   #   die Navigation in den AddOn-Assets-Ordner /assets/addons/simpl_nav/.
   #   $bgcol            Grundfarbe des Folder-Icons im rgba-Format,
   #                     der Opac-Anteil spielt keine Rolle
   #   benutzte functions:
   #      self::icon_colors($bgcol,$fac)
   #
   $addon=self::this_addon;
   #
   # --- Folder-Icon
   #     3 Farben fuer das Icon:              $hex[1], $hex[2], $hex[3]
   $fac1=array(1=>0.7, 2=>0.8, 3=>0.9);    // Abdunklung
   $hex=self::icon_colors($bgcol,$fac1);   // hell (=$bgcol), mittel, dunkel
   $icon[1]=$addon::FOLDER_SVG;
   $buff[1]='
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="128px" height="128px" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" xmlns:xlink="http://www.w3.org/1999/xlink">
<g><path style="opacity:1" fill="'.$hex[2].'" d="M 127.5,30.5 C 127.5,31.1667 127.5,31.8333 127.5,32.5C 126.164,30.6932 124.497,29.1932 122.5,28C 100.167,27.3333 77.8333,27.3333 55.5,28C 53.9221,29.0945 52.2554,29.9278 50.5,30.5C 48.4068,31.0447 46.4068,31.878 44.5,33C 30.5,33.3333 16.5,33.6667 2.5,34C 1.47344,34.5133 0.473444,35.0133 -0.5,35.5C -0.5,29.1667 -0.5,22.8333 -0.5,16.5C 0.836207,14.6932 2.50287,13.1932 4.5,12C 17.1667,11.3333 29.8333,11.3333 42.5,12C 44.2464,12.4709 45.913,13.1376 47.5,14C 50.5,17.6667 53.5,21.3333 56.5,25C 78.5,25.3333 100.5,25.6667 122.5,26C 124.687,27.025 126.354,28.525 127.5,30.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[1].'" d="M 127.5,32.5 C 127.5,56.5 127.5,80.5 127.5,104.5C 126.038,105.458 124.705,106.624 123.5,108C 83.5,108.667 43.5,108.667 3.5,108C 2.29493,106.624 0.961599,105.458 -0.5,104.5C -0.5,82.5 -0.5,60.5 -0.5,38.5C 0.316435,36.8564 1.64977,35.6897 3.5,35C 17.5,34.6667 31.5,34.3333 45.5,34C 47.5844,33.3028 49.2511,32.1361 50.5,30.5C 52.2554,29.9278 53.9221,29.0945 55.5,28C 77.8333,27.3333 100.167,27.3333 122.5,28C 124.497,29.1932 126.164,30.6932 127.5,32.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[3].'" d="M 50.5,30.5 C 49.2511,32.1361 47.5844,33.3028 45.5,34C 31.5,34.3333 17.5,34.6667 3.5,35C 1.64977,35.6897 0.316435,36.8564 -0.5,38.5C -0.5,37.5 -0.5,36.5 -0.5,35.5C 0.473444,35.0133 1.47344,34.5133 2.5,34C 16.5,33.6667 30.5,33.3333 44.5,33C 46.4068,31.878 48.4068,31.0447 50.5,30.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[2].'" d="M -0.5,104.5 C 0.961599,105.458 2.29493,106.624 3.5,108C 43.5,108.667 83.5,108.667 123.5,108C 124.705,106.624 126.038,105.458 127.5,104.5C 127.5,105.167 127.5,105.833 127.5,106.5C 126.038,107.458 124.705,108.624 123.5,110C 83.5,110.667 43.5,110.667 3.5,110C 2.29493,108.624 0.961599,107.458 -0.5,106.5C -0.5,105.833 -0.5,105.167 -0.5,104.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[3].'" d="M -0.5,106.5 C 0.961599,107.458 2.29493,108.624 3.5,110C 43.5,110.667 83.5,110.667 123.5,110C 124.705,108.624 126.038,107.458 127.5,106.5C 127.5,107.167 127.5,107.833 127.5,108.5C 126.684,110.144 125.35,111.31 123.5,112C 83.5,112.667 43.5,112.667 3.5,112C 1.64977,111.31 0.316435,110.144 -0.5,108.5C -0.5,107.833 -0.5,107.167 -0.5,106.5 Z"/></g>
</svg>';
   #
   # --- File-Icon
   #     3 Farben fuer das Icon:              $hex[1], $hex[2], $hex[3]
   $fac2=array(1=>0.1, 2=>0.5, 3=>0.9);    // Abdunklung
   $hex=self::icon_colors($bgcol,$fac2);   // hell (=$bgcol), mittel, dunkel
   $icon[2]=$addon::FILE_SVG;
   $buff[2]='
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="128px" height="128px" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" xmlns:xlink="http://www.w3.org/1999/xlink">
<g><path style="opacity:1" fill="'.$hex[3].'" d="M 14.5,-0.5 C 37.5,-0.5 60.5,-0.5 83.5,-0.5C 92.7978,9.29838 102.298,18.965 112,28.5C 112.825,61.5715 112.659,94.5715 111.5,127.5C 79.5,127.5 47.5,127.5 15.5,127.5C 14.5058,84.9056 14.1725,42.2389 14.5,-0.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[2].'" d="M 82.5,28.5 C 81.5067,19.6822 81.1734,10.6822 81.5,1.5C 60.1667,1.5 38.8333,1.5 17.5,1.5C 17.5,42.8333 17.5,84.1667 17.5,125.5C 48.1667,125.5 78.8333,125.5 109.5,125.5C 109.5,93.5 109.5,61.5 109.5,29.5C 110.498,61.6624 110.832,93.9957 110.5,126.5C 79.1667,126.5 47.8333,126.5 16.5,126.5C 16.5,84.5 16.5,42.5 16.5,0.5C 38.5,0.5 60.5,0.5 82.5,0.5C 82.5,9.83333 82.5,19.1667 82.5,28.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[1].'" d="M 82.5,28.5 C 91.3178,29.4933 100.318,29.8266 109.5,29.5C 109.5,61.5 109.5,93.5 109.5,125.5C 78.8333,125.5 48.1667,125.5 17.5,125.5C 17.5,84.1667 17.5,42.8333 17.5,1.5C 38.8333,1.5 60.1667,1.5 81.5,1.5C 81.1734,10.6822 81.5067,19.6822 82.5,28.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[1].'" d="M 107.5,26.5 C 99.6841,27.4924 91.6841,27.8257 83.5,27.5C 83.3336,19.4931 83.5003,11.4931 84,3.5C 91.7017,11.3691 99.5351,19.0358 107.5,26.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[2].'" d="M 107.5,26.5 C 108.043,26.56 108.376,26.8933 108.5,27.5C 100.009,28.8209 91.6758,28.8209 83.5,27.5C 91.6841,27.8257 99.6841,27.4924 107.5,26.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[3].'" d="M 98.5,46.5 C 75.5,46.5 52.5,46.5 29.5,46.5C 40.6467,45.1677 52.1467,44.501 64,44.5C 75.8533,44.501 87.3533,45.1677 98.5,46.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[2].'" d="M 29.5,46.5 C 52.5,46.5 75.5,46.5 98.5,46.5C 87.3533,47.8323 75.8533,48.499 64,48.5C 52.1467,48.499 40.6467,47.8323 29.5,46.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[3].'" d="M 98.5,61.5 C 75.5,61.5 52.5,61.5 29.5,61.5C 40.6467,60.1677 52.1467,59.501 64,59.5C 75.8533,59.501 87.3533,60.1677 98.5,61.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[2].'" d="M 29.5,61.5 C 52.5,61.5 75.5,61.5 98.5,61.5C 87.3533,62.8323 75.8533,63.499 64,63.5C 52.1467,63.499 40.6467,62.8323 29.5,61.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[3].'" d="M 98.5,76.5 C 75.5,76.5 52.5,76.5 29.5,76.5C 40.6467,75.1677 52.1467,74.501 64,74.5C 75.8533,74.501 87.3533,75.1677 98.5,76.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[2].'" d="M 29.5,76.5 C 52.5,76.5 75.5,76.5 98.5,76.5C 87.3533,77.8323 75.8533,78.499 64,78.5C 52.1467,78.499 40.6467,77.8323 29.5,76.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[3].'" d="M 98.5,92.5 C 75.5,92.5 52.5,92.5 29.5,92.5C 40.6467,91.1677 52.1467,90.501 64,90.5C 75.8533,90.501 87.3533,91.1677 98.5,92.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[2].'" d="M 29.5,92.5 C 52.5,92.5 75.5,92.5 98.5,92.5C 87.3533,93.8323 75.8533,94.499 64,94.5C 52.1467,94.499 40.6467,93.8323 29.5,92.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[3].'" d="M 98.5,108.5 C 75.5,108.5 52.5,108.5 29.5,108.5C 40.6467,107.168 52.1467,106.501 64,106.5C 75.8533,106.501 87.3533,107.168 98.5,108.5 Z"/></g>
<g><path style="opacity:1" fill="'.$hex[2].'" d="M 29.5,108.5 C 52.5,108.5 75.5,108.5 98.5,108.5C 87.3533,109.832 75.8533,110.499 64,110.5C 52.1467,110.499 40.6467,109.832 29.5,108.5 Z"/></g>
</svg>';
   #
   # --- Schreiben der Icon-Dateien in /assets/addons/simple_nav/
   #     noetigenfalls AddOn-Assets-Ordner neu erstellen
   $ordner=rex_path::addonAssets($addon);
   if(!file_exists($ordner)) mkdir($ordner);
   for($i=1;$i<=2;$i=$i+1):
      $file=rex_path::addonAssets($addon,$icon[$i]);
      $handle=fopen($file,'w');
      fwrite($handle,$buff[$i]);
      fclose($handle);
      endfor;
   }
public static function set_configuration() {
   #   Schreiben der Stylesheet-Datei in Abhaengigkeit von der gesetzten
   #   Konfiguration. Falls noch keine Konfiguration vorhanden ist, werden
   #   vorher Default-Daten gesetzt. - Wird nur in install.php benutzt.
   #   benutzte functions:
   #      self::get_config_data()
   #      self::get_default_data()
   #      self::set_config_data($data)
   #      self::write_css_file($data)
   #      self::write_svg_icons($bgcol)
   #
   # --- Konfigurationsdaten ermitteln, ggf. Default setzen
   $data=self::get_config_data();
   if(count($data)<=0):
     $data=self::get_default_data();
     self::set_config_data($data);
     endif;
   #
   # --- Stylesheet-Datei schreiben
   self::write_css_file($data);
   #
   # --- svg-Icons schreiben
   $bgcol=rex_config::get(self::this_addon,self::this_addon::NAV_COL_BACK_1);
   self::write_svg_icons($bgcol);
   }
#
# --------------------------- Konfigurationsmenue
public static function split_color($color) {
   #   Rueckgabe der RGBA-Komponenten eines RGBA-Farbstrings
   #   in Form eines assoziativen Arrays mit diesen Keys:
   #      ['red']    rote Komponente
   #      ['green']  gruene Komponente
   #      ['blue']   blaue Komponente
   #      ['opac']   Deckungsgrad
   #   $color            RGBA-String der Farbe
   #
   $arr=explode(',',$color);
   $red  =trim(substr($arr[0],5));
   $green=trim($arr[1]);
   $blue =trim($arr[2]);
   $opac =trim(substr($arr[3],0,strlen($arr[3])-1));
   return array('red'=>$red, 'green'=>$green, 'blue'=>$blue, 'opac'=>$opac);
   }
public static function split_data($data) {
   #   Zerlegen eines assoziativen Arays im Format der konfigurierten Daten
   #   (18 Parameter) in ein assoziatives Array, in dem die konfigurierten
   #   RGBA-Farben in ihre Parameter zerlegt sind (42 Parameter).
   #   $data             Eingabe-Array
   #   Rueckgabe-Array in diesem Format:
   #      [$addon::NAV_TYP]              Integer
   #      [$addon::NAV_INDENT]           Integer
   #      [$addon::NAV_FOLDER_ICON]      ''/'on' Zeile ohne/mit Folder-Icon
   #      [$addon::NAV_FILE_ICON         ''/'on' Zeile ohne/mit File-Icon
   #      [$addon::NAV_WIDTH]            Integer
   #      [$addon::NAV_LINE_HEIGHT]      Dezimalzahl mit/ohne Dezimalpunkt
   #      [$addon::NAV_FONT_SIZE]        Dezimalzahl mit/ohne Dezimalpunkt
   #      [$addon::NAV_BOR_LRWIDTH]      Integer
   #      [$addon::NAV_BOR_OUWIDTH]      Integer
   #      [$addon::NAV_BOR_RAD]          Integer
   #      [$addon::NAV_COL_LINK/xxx]     Integer (xxx=red/green/blue/opac)
   #      [$addon::NAV_COL_BORD_0/xxx]   Integer (xxx=red/green/blue/opac)
   #      [$addon::NAV_COL_BACK_0/xxx]   Integer (xxx=red/green/blue/opac)
   #      [$addon::NAV_COL_BORD_1/xxx]   Integer (xxx=red/green/blue/opac)
   #      [$addon::NAV_COL_BACK_1/xxx]   Integer (xxx=red/green/blue/opac)
   #      [$addon::NAV_COL_BORD_2/xxx]   Integer (xxx=red/green/blue/opac)
   #      [$addon::NAV_COL_BACK_2/xxx]   Integer (xxx=red/green/blue/opac)
   #      [$addon::NAV_COL_TEXT_2/xxx]   Integer (xxx=red/green/blue/opac)
   #   benutzte functions:
   #      self::split_color($color)
   #
   $keys=array_keys($data);
   $daten=array();
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $val=$data[$key];
      if(substr($key,0,4)!='col_'):
        $daten[$key]=$val;
        else:
        $col=self::split_color($val);
        $daten[$key.'/red']  =$col['red'];
        $daten[$key.'/green']=$col['green'];
        $daten[$key.'/blue'] =$col['blue'];
        $daten[$key.'/opac'] =$col['opac'];
        endif;
      endfor;
   return $daten;
   }
public static function join_data($daten) {
   #   Zusammenfuehren des Arrays der eingelesenen Konfigurationsdaten
   #      (Parameter 0, 1, ..., 41, Farben: 4 separaten RGBA-Werten)
   #   in ein Array der abzuspeichernden Konfigurationsdaten
   #      (Parameter 0, 1, ..., 17, Farben: je ein RGBA-String).
   #   $daten            Eingabe-Array (42 Parameter)
   #
   $keys=array_keys($daten);
   $data=array();
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $val=trim($daten[$key]);
      if(substr($key,0,4)!='col_'):
        $data[$key]=$val;
        $k=0;
        else:
        $k=$k+1;
        if($k==1) $red  =$val;
        if($k==2) $green=$val;
        if($k==3) $blue =$val;
        if($k==4):
          $rgba='rgba('.$red.','.$green.','.$blue.','.$val.')';
          $arr=explode('/',$key);
          $key=$arr[0];
          $data[$key]=$rgba;
          $k=0;
          endif;
        endif;
      endfor;
   return $data;
   }
public static function config_lines() {
   #   Rueckgabe der Ausgabezeilen fuer das Konfigurationsformular in Form eines
   #   nummerierten Arrays (Nummerierung ab 0), wobei jedes Element den linken
   #   und den rechten Teil der Zeile in Form eines nummeriertes Arrays mit den
   #   Spalten [0] und [1] enthaelt.
   #   benutzte functions:
   #      $addon::folder_icon()
   #      $addon::file_icon()
   #
   $addon=self::this_addon;
   $icon0=$addon::folder_icon();
   $icon1=$addon::file_icon();
   $hgf=' Hintergrundfarbe entspricht (**)';
   $tx='Linktext';
   $txt=array(
      array('Navigationstyp (=1/2/3)',                            '&nbsp;'),
      array('Einrückung pro Navigations-Level',                   'px'),
      array('Navigationszeilen: Kategorien mit Folder-Icon',      $icon0.$hgf),
      array('Navigationszeilen: Artikel mit Artikel-Icon',        $icon1.$hgf),
      array('Navigationszeilen: Breite',                          'px &nbsp; (*)'),
      array('Navigationszeilen: Zeilenhöhe',                      'em'),
      array('Linktexte: Zeichengröße (&le;Zeilenhöhe)',           'em'),
      array('Rand: Dicke links/rechts (0/1/2 Pixel)',             'px'),
      array('Rand: Dicke oben/unten (0/1/2 Pixel)',               'px'),
      array('Rand: Krümmungsradius für abgerundete Ecken',        'em'),
      array('alle Navigationszeilen: Farbe der Linktexte',        $tx),
      array('Standardzeile: Randfarbe',                           '&nbsp;'),
      array('Standardzeile: Hintergrundfarbe',                    $tx),
      array('Zeile des ältesten Ahnen: Randfarbe',                '&nbsp;'),
      array('Zeile des ältesten Ahnen: Hintergrundfarbe (**)',    $tx),
      array('Zeile des aktuellen Artikels: Randfarbe',            '&nbsp;'),
      array('Zeile des aktuellen Artikels: Hintergrundfarbe',     'Artikelname'),
      array('Zeile des aktuellen Artikels: Textfarbe (kein Link)','Text'));
   return $txt;
   }
public static function config_form($txt,$daten) {
   #   Rueckgabe des Eingabeformulars fuer die Konfigurationsdaten fuer das Stylesheet.
   #   $txt              Menue-Strings als nummeriertes Array in der Form
   #                     $txt[$i][0]: Erlaeuterungstext links und
   #                     $txt[$i][1]: Erlaeuterungstext rechts ($i=0, 1, ..., 17)
   #   $daten            assoziatives Array der Daten fuer die Input-Felder
   #                     (Parameter 0, 1, ..., 41))
   #
   $addon=self::this_addon;
   $longkeys=array_keys($daten);
   $navwidth=$daten[$addon::NAV_WIDTH];
   #
   # --- Farben fuer das Kontrollfeld bestimmen
   for($i=0;$i<count($longkeys);$i=$i+1):
      $key=$longkeys[$i];
      if(substr($key,0,4)!='col_'):
        $iw=$i;   // zum Weiterzaehlen der Basis-Keys auch bei den Farben
        else:
        if(strpos($key,'/red')>0):
          $iw=$iw+1;
          $arr=explode('/',$key);
          $ke=$arr[0];
          $rgba[$iw]='rgba('.
             trim($daten[$ke.'/red']).','.
             trim($daten[$ke.'/green']).','.
             trim($daten[$ke.'/blue']).','.
             trim($daten[$ke.'/opac']).')';
          endif;
        endif;
      endfor;
   #
   # --- Formularanfang
   $string='
   <form method="post">
   <table class="nav_table">
       <tr><td colspan="6">
               <b>allgemeine Parameter:</b></td></tr>';
   $colors=FALSE;
   for($i=0;$i<count($longkeys);$i=$i+1):
      $key=$longkeys[$i];
      $val=$daten[$key];
      #
      # --- Zwischenueberschrift (vor den Zeilen-Icons)
      if($key==$addon::NAV_FOLDER_ICON) $string=$string.'
       <tr><td colspan="6">
               <b>Stylesheet-Parameter:</b></td></tr>';
      #
      # --- Nicht-Farben-Parameter
      if(!$colors):
        #     input-Feld: Einrueckung, Breite, Zeilenhoehe, Zeichengroesse, Kruemmungsradius
        if($key==$addon::NAV_INDENT      or $key==$addon::NAV_WIDTH     or
           $key==$addon::NAV_LINE_HEIGHT or $key==$addon::NAV_FONT_SIZE or
           $key==$addon::NAV_BOR_RAD):
          $string=$string.'
       <tr><td class="nav_guide">
               '.$txt[$i][0].'</td>
           <td class="nav_guide">
               <input class="nav_input" type="text" name="'.$key.'" value="'.$val.'"></td>
           <td class="nav_guide" colspan="4">
               '.$txt[$i][1].'</td></tr>';
          endif;
        #
        #     Select-Menue: Navigationstyp, Randdicke l/r, Randdicke o/u
        if($key==$addon::NAV_TYP or $key==$addon::NAV_BOR_LRWIDTH or
           $key==$addon::NAV_BOR_OUWIDTH):
          $arr=array(0,1,2);
          if($key==$addon::NAV_TYP) $arr=array(1,2,3);
          $str='';
          for($k=0;$k<count($arr);$k=$k+1):
             if($arr[$k]==$val):
               $str=$str.'
                   <option selected="selected">'.$arr[$k].'</option>';
               else:
               $str=$str.'
                   <option>'.$arr[$k].'</option>';
               endif;
             endfor;
          $string=$string.'
       <tr><td class="nav_guide">
               '.$txt[$i][0].'</td>
           <td class="nav_guide">
               <select name="'.$key.'" class="nav_select">'.$str.'
               </select></td>
           <td class="nav_guide" colspan="4">
               '.$txt[$i][1].'</td></tr>';
          endif;
        #
        #     Checkbox: Folder-Icon, File-Icon
        if($key==$addon::NAV_FOLDER_ICON or $key==$addon::NAV_FILE_ICON):
          $chk='';
          if(strtolower($val)=='on' or $val>0) $chk=' checked';
          $string=$string.'
       <tr><td class="nav_guide">
               '.$txt[$i][0].'</td>
           <td class="nav_guide nav_right">
               <input type="checkbox" name="'.$key.'"'.$chk.'></td>
           <td class="nav_guide" colspan="4">
               '.$txt[$i][1].'</td></tr>';
          endif;
        $iw=$i;   // zum Weiterzaehlen der Basis-Keys auch bei den Farben
        else:
      #
      # --- Farben-Parameter
        if(strpos($key,'/red')>0):
          $iw=$iw+1;
          #     Anfang Farbdefinitions-Zeilen     
          $string=$string.'
       <tr><td class="nav_guide">
               '.$txt[$iw][0].'</td>';
          endif;
        $string=$string.'
           <td class="nav_guide">
               <input class="nav_input" type="text" name="'.$key.'" value="'.$val.'"></td>';
        #
        # --- Kontrollfelder (ganz rechts)
        if(strpos($key,'/opac')>0):
          $class1='nav_clink';
          #     Linktext (ohne Rand)
          if(str_contains($key,$addon::NAV_COL_LINK))
            $class=$addon::DIV_FORMAT;
          #     Randfarben
          if(str_contains($key,$addon::NAV_COL_BORD_0) or
             str_contains($key,$addon::NAV_COL_BORD_1) or
             str_contains($key,$addon::NAV_COL_BORD_2))
            $class=$addon::DIV_BORDER1.' '.$addon::DIV_FORMAT.' nav_short';
          #     Hintergrundfarben (inkl. Rand)
          if(str_contains($key,$addon::NAV_COL_BACK_0))
            $class=$addon::DIV_BORDER1.' '.$addon::DIV_FORMAT.' typ0';
          if(str_contains($key,$addon::NAV_COL_BACK_1))
            $class=$addon::DIV_BORDER1.' '.$addon::DIV_FORMAT.' typ1';
          if(str_contains($key,$addon::NAV_COL_BACK_2)):
            $class1='nav_ctext';
            $class=$addon::DIV_BORDER1.' '.$addon::DIV_FORMAT.' typ2';
            endif;
          #     Inhalt des aktuellen Artikels (inkl. Rand)
          if(str_contains($key,$addon::NAV_COL_TEXT_2)):
            $class1='nav_ctext';
            $class=$addon::DIV_BORDER1.' '.$addon::DIV_FORMAT.' typ2 nav_short';
            endif;
          $string=$string.'
           <td class="nav_guide">
               <div class="'.$class.'"><div class="'.$class1.'">'.$txt[$iw][1].'</div></div></td></tr>';
          endif;
        endif;
      #
      # --- Vorspann vor den Farben (nach dem Kruemmungsradius)
      if($key==$addon::NAV_BOR_RAD):
          $string=$string.'
       <tr><td align="right">
               Farben + Deckungsgrad (RGBA-Werte):</td>
           <td class="nav_center">
               rot</td>
           <td class="nav_center">
               grün</td>
           <td class="nav_center">
               blau</td>
           <td class="nav_center">
               Deckung</td>
           <td class="nav_center">
               <span class="nav_fleft">&lArr;</span>
               '.$navwidth.' px &nbsp; (*)
               <span class="nav_fright">&rArr;</span></td></tr>';
      #
      # --- Umschalten auf Farben-Parameter
          $colors=TRUE;
          endif;
      endfor;
   #
   # --- Buttons und Formular-Abschluss
   $sptit='Parameter und css-Stylesheet speichern';
   $str='auf Defaultwerte zurücksetzen und ';
   $title=$str.'speichern';
   $retit='Parameter '.$str."\n".$sptit;
   $string=$string.'
       <tr><td class="nav_guide"><br>
               <button class="btn btn-save"   type="submit" name="save"  value="save"  title="'.$sptit.'"> speichern </button></td>
           <td class="nav_guide" colspan="5"><br>
               <button class="btn btn-update" type="submit" name="reset" value="reset" title="'.$retit.'">'.$title.'</button></td></tr>
   </table>
   </form>
   <br>
   <br>
   ';
   return $string;
   }
public static function print_config_form() {
   #   Ausgabe des Eingabeformulars fuer die Konfigurationsdaten des Stylesheets.
   #   Wird nur in der Datei pages/settings.php benutzt.
   #   benutzte functions:
   #      self::config_lines()
   #      self::get_default_data()
   #      self::get_config_data()
   #      self::split_data($data)
   #      self::join_data($daten)
   #      self::set_config_data($data)
   #      self::write_css_file($data)
   #      self::write_svg_icons($bgcol)
   #      self::config_form($msg,$daten)
   #
   $addon=self::this_addon;
   #
   # --- Menue-Strings
   $txt=self::config_lines();
   #
   # --- Default-Konfigurationsdaten
   $defdat=self::get_default_data();
   $keys=array_keys($defdat);
   #
   # --- Konfigurationsdaten (falls noch nicht gesetzt: Default-Daten)
   $confdat=self::get_config_data();
   $ifarb='';
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      if(empty($confdat[$key]) and $confdat[$key]!='0') $confdat[$key]=$defdat[$key];
      if($key==$addon::NAV_COL_LINK) $ifarb=$i;   // Nr. des Keys fuer die Linkfarbe
      endfor;
   #
   # --- Aufspalten der Konfigurationsdaten auf Einzelparameter (Farben)
   $daten=self::split_data($confdat);
   $longkeys=array_keys($daten);
   #
   # --- Auslesen und Ueberpruefen der eingegebenen Parameter
   $falschrueck='&nbsp; &nbsp; falscher Wert, &nbsp; zurück gesetzt zu &nbsp; ';
   $warn='';
   for($i=0;$i<count($longkeys);$i=$i+1):
      $key=$longkeys[$i];
      $post='';
      #
      # --- Parameter auslesen
      if(!empty($_POST[$key]) or
         (($key==$addon::NAV_FOLDER_ICON or $key==$addon::NAV_FILE_ICON) and
          !empty($_POST['save']))):
        $post=$_POST[$key];
        else:
        $post=$daten[$key];
        endif;
      #
      # --- Parameter ueberpruefen
      if(empty($post) and
         (substr($key,0,4)=='col_' or $key==$addon::NAV_BOR_RAD))
        $post='0';
      #     ggf. Dezimalkomma durch Dezimalpunkt ersetzen (nicht bei Farben)
      if(substr($key,0,4)!='col_') $post=str_replace(',','.',$post);
      #     Eingabedaten gemaess Beschraenkungen korrigieren
      if($key==$addon::NAV_FONT_SIZE and $post>$daten[$addon::NAV_LINE_HEIGHT]):
        $korr=$daten[$key];
        $warn=$txt[$i][0].': &nbsp; <code>'.$post.'</code> (>Zeilenhöhe) '.$falschrueck.
           ' <code>'.$korr.'</code>';
        $post=$korr;
        endif;
      if($key==$addon::NAV_BOR_RAD and $post>0 and
         ($daten[$addon::NAV_BOR_LRWIDTH]<=0 or $daten[$addon::NAV_BOR_OUWIDTH]<=0)):
        $korr=0;
        $warn=$txt[$i][0].': &nbsp; <code>'.$post.'</code> (nur bei Randdicke&gt;0) '.$falschrueck.
           ' <code>'.$korr.'</code>';
        $post=$korr;
        endif;
      if($post>255 and (strpos($key,'red')>0 or strpos($key,'green')>0 or strpos($key,'blue')>0)):
        $korr=$daten[$key];
        $k=intval($ifarb+($i-$ifarb)/4);
        $warn=$txt[$k][0].': &nbsp; <code>'.$post.'</code> (>255) '.$falschrueck.
           ' <code>'.$korr.'</code>';
        $post=$korr;
        endif;
      if($post>1 and strpos($key,'opac')>0):
        $korr=$daten[$key];
        $k=intval($ifarb+($i-$ifarb)/4);
        $warn=$txt[$k][0].': &nbsp; <code>'.$post.'</code> (>1) '.$falschrueck.
           ' <code>'.$korr.'</code>';
        $post=$korr;
        endif;
      $daten[$key]=$post;
      endfor;
   #
   # --- Warnung zu falschen (und korrigierten) Eingaben
   $msg='';
   if(!empty($warn)) $msg=rex_view::warning($warn);
   #
   # --- speichern bzw. zuruecksetzen und speichern (Daten + Stylesheet-Datei)
   $save='';
   if(!empty($_POST['save'])) $save=$_POST['save'];
   $reset='';
   if(!empty($_POST['reset'])) $reset=$_POST['reset'];
   if(!empty($save) or !empty($reset)):
     if(!empty($reset)):
       $data=$defdat;
       $daten=self::split_data($data);
       else:
       $data=self::join_data($daten);
       endif;
     #
     # --- Konfigurationsdaten speichern, Stylesheet-Datei und svg-Icons schreiben
     self::set_config_data($data);
     self::write_css_file($data);
     $bgcol=rex_config::get(self::this_addon,self::this_addon::NAV_COL_BACK_1);
     self::write_svg_icons($bgcol);
     endif;
   #
   # --- Warnmeldungen und Eingabeformular ausgeben
   echo $msg.self::config_form($txt,$daten);
   }
}
?>