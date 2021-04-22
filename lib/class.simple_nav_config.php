<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version April 2021
 */
define ('NAVIGATION',     'simple_nav');     // AddOn-Identifier
define ('SCREEN_MOBILE',  35);               // Stylesheet Smartphone 'max-width:...em'
define ('HAMBURGER',      'hamburger_icon'); // Id des Icons, das die Navigation anzeigt
define ('KREUZ',          'kreuz_icon');     // Id des Icons, das die Navigation verbirgt
#     Konfigurationsparameter-Keys 0 - 15
define ('NAV_TYP',        'navtyp');
define ('NAV_INDENT',     'indent');
define ('NAV_WIDTH',      'width');
define ('NAV_LINE_HEIGHT','line_height');
define ('NAV_FONT_SIZE',  'font_size');
define ('NAV_BOR_LRWIDTH','bor_lrwidth');
define ('NAV_BOR_OUWIDTH','bor_ouwidth');
define ('NAV_BOR_RAD',    'bor_rad');
define ('NAV_COL_LINK',   'col_link');
define ('NAV_COL_BORD_0', 'col_border_0');
define ('NAV_COL_BACK_0', 'col_backgr_0');
define ('NAV_COL_BORD_1', 'col_border_1');
define ('NAV_COL_BACK_1', 'col_backgr_1');
define ('NAV_COL_BORD_2', 'col_border_2');
define ('NAV_COL_BACK_2', 'col_backgr_2');
define ('NAV_COL_TEXT_2', 'col_text_2');
#
#   Installation:
#   set_configuration()
#      get_config_data()
#      get_default_data()
#      set_config_data($data)
#      write_css_file($data)
#      write_script_file()
#   Konfigurationsmenue:
#   config()
#      get_default_data()
#      split_data($data)
#         split_color($color)
#      join_data($daten)
#      set_config_data($data)
#      write_css_file($data)
#      write_script_file()
#      config_form($msg,$daten)
#   Beispiel-Navigation:
#   example()
#      get_config_data()
#      example_entries($navtyp)
#      sort($entries)                        [class simple_nav]
#      print_line($entries,$zus,$increment)  [class simple_nav]
#
class simple_nav_config {
#
# --------------------------- Installation
public static function set_configuration() {
   #   benutzte functions:
   #      self::get_config_data()
   #      self::get_default_data()
   #      self::set_config_data($data)
   #      self::write_css_file($data)
   #      self::write_script_file()
   #
   # --- Konfigurationsdaten ermitteln, ggf. Default setzen
   $data=self::get_config_data();
   if(count($data)<=0):
     $data=self::get_default_data();
     self::set_config_data($data);
     endif;
   #
   # --- Stylesheet- und Javascript-Datei schreiben
   self::write_css_file($data);
   self::write_script_file();
   }
public static function get_default_data() {
   #   Rueckgabe der Default-Werte der Stylesheet-Daten als assoziatives Array
   #
   $defdata=array(
      NAV_TYP        =>2,
      NAV_INDENT     =>10,
      NAV_WIDTH      =>150,
      NAV_LINE_HEIGHT=>floatval(0.8),
      NAV_FONT_SIZE  =>floatval(0.8),
      NAV_BOR_LRWIDTH=>0,
      NAV_BOR_OUWIDTH=>1,
      NAV_BOR_RAD    =>0,
      NAV_COL_LINK   =>'rgba(153, 51,  0,1)',
      NAV_COL_BORD_0 =>'rgba(255,190, 60,1)',
      NAV_COL_BACK_0 =>'rgba(255,255,255,0)',
      NAV_COL_BORD_1 =>'rgba(255,190, 60,1)',
      NAV_COL_BACK_1 =>'rgba(255,190, 60,0.3)',
      NAV_COL_BORD_2 =>'rgba(255,190, 60,1)',
      NAV_COL_BACK_2 =>'rgba(204,102, 51,1)',
      NAV_COL_TEXT_2 =>'rgba(255,255,255,1)');
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
   #   Rueckgabe der konfigurierten Daten. Falls knoch keine Konfiguration
   #   definiert wurde, wird ein leeres Array zurueck gegeben.
   #   benutzte functions:
   #      self::get_default_data()
   #
   # --- Default-Konfigurationsdaten
   $defdat=self::get_default_data();
   $keys=array_keys($defdat);
   #
   # --- Auslesen der Konfigurationsdaten
   $confdat=array();
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $val=rex_config::get(NAVIGATION,$key);
      if(!empty($val) or $val=='0') $confdat[$key]=$val;
      endfor;
   return $confdat;
   }
public static function set_config_data($data) {
   #   Schreiben der Konfigurationsdaten
   #   $data             Array der zu schreibenden Daten
   #   benutzte functions:
   #      self::get_default_data()
   #
   # --- Default-Konfigurationsdaten
   $defdat=self::get_default_data();
   $keys=array_keys($defdat);
   #
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $val=$data[$key];
      if(is_integer($val)) $val=intval($val);
      if(is_float($val))   $val=floatval($val);
      rex_config::set(NAVIGATION,$key,$val);
      endfor;
   }
public static function write_css_file($data) {
   #   Schreiben der Stylesheets fuer die Navigation in beide Assets-Ordner des AddOns
   #   $data             Array der Daten, aus denen die Stylesheet-
   #                     Klassenbezeichnungen zusammengestellt werden
   #
   # --- Auslesen der gesetzten Konfigurationsdaten
   $keys=array_keys($data);
   $width       =$data[$keys[2]];
   $line_height =$data[$keys[3]];
   $font_size   =$data[$keys[4]];
   $bor_lrwidth =$data[$keys[5]];
   $bor_ouwidth =$data[$keys[6]];
   $bor_rad     =$data[$keys[7]];
   $col_link    =$data[$keys[8]];
   $col_border_0=$data[$keys[9]];
   $col_backgr_0=$data[$keys[10]];
   $col_border_1=$data[$keys[11]];
   $col_backgr_1=$data[$keys[12]];
   $col_border_2=$data[$keys[13]];
   $col_backgr_2=$data[$keys[14]];
   $col_text_2  =$data[$keys[15]];
   #
   # --- Klassennamen der div-Container
   $typ0=DIV_TYP.'0';
   $typ1=DIV_TYP.'1';
   $typ2=DIV_TYP.'2';
   #
   # --- Styles fuer die Navigation selbst
   $buffer=
'/*   s i m p l e _ n a v - N a v i g a t i o n   */
div.'.DIV_BORDER1.' {
    border-bottom:solid '.$bor_ouwidth.'px '.$col_border_0.';
    border-top:   solid '.$bor_ouwidth.'px '.$col_border_0.';     /* auch oberer Rand */
    border-left:  solid '.$bor_lrwidth.'px '.$col_border_0.';
    border-right: solid '.$bor_lrwidth.'px '.$col_border_0.';
    border-radius:'.$bor_rad.'em; }
div.'.DIV_BORDER.' {
    border-bottom:solid '.$bor_ouwidth.'px '.$col_border_0.';
    border-top:   solid 0px '.$col_border_0.';     /* kein oberer Rand */
    border-left:  solid '.$bor_lrwidth.'px '.$col_border_0.';
    border-right: solid '.$bor_lrwidth.'px '.$col_border_0.';
    border-radius:'.$bor_rad.'em; }
div.'.DIV_FORMAT.' { padding:3px; width:'.$width.'px; line-height:'.$line_height.'em; }
div.'.$typ0.' { background-color:'.$col_backgr_0.'; }
div.'.$typ1.' { background-color:'.$col_backgr_1.'; }
div.'.$typ2.' { background-color:'.$col_backgr_2.'; }
div.'.$typ0.' div a, div.'.$typ1.' div a { font-size:'.$font_size.'em;
    color:'.$col_link.'; display:block; overflow:hidden; }
div.'.$typ2.' div { font-size:'.$font_size.'em; color:'.$col_text_2.'; overflow:hidden; }
/*   a n g e z e i g t    o d e r    v e r b o r g e n   */
#'.NAVIGATION.' { display:block; padding:0.25em; }
@media screen and (max-width:'.SCREEN_MOBILE.'em) { #'.NAVIGATION.' { display:none; } }';   

   #
   # --- Styles fuer das Hamburger-Icon
   $buffer=$buffer.'
/*   H a m b u r g e r - / K r e u z - I c o n   */
#'.HAMBURGER.' { display:none; }
#'.KREUZ.' {display:none; }
@media screen and (max-width:'.SCREEN_MOBILE.'em) {
    #'.HAMBURGER.' { position:fixed; left:10px; margin-top:-1em; display:block;
        width:28px; cursor:pointer; }
    div.bar { margin:4px; height:2px; background-color:'.$col_backgr_2.'; }
    #'.KREUZ.' { position:fixed; left:10px; margin-top:-1em; display:none;
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
.nav_table { background-color:inherit; }
.nav_guide { padding-left:1em; line-height:1.5em; white-space:nowrap; }
.nav_center { padding-left:1em; text-align:center; white-space:nowrap; }
.nav_left  { float:left; }
.nav_right { float:right; }
.nav_input { width:4em; line-height:1.5em; text-align:right;
    border:solid 1px silver; background-color:transparent; }
.nav_select { width:3em; height:1.8em; text-align:right;
    border:solid 1px silver; background-color:transparent; }
.nav_short { width:60px !important; }
.nav_conf_link { color:'.$col_link.';   font-size:'.$font_size.'em; }
.nav_conf_text { color:'.$col_text_2.'; font-size:'.$font_size.'em; }
/*   B e s c h r e i b u n g   */
.nav_narr { margin:0; }
.nav_hand { padding-left:2em; }
.nav_box  { margin:0.5em 0 0.5em 0; padding:0.5em; width:33em; font-family:monospace; 
    border:solid 1px silver; }';
   #
   # --- Styles fuer das Beispiel
   $buffer=$buffer.'
/*   B e i s p i e l   */
.xmp_pad { padding:0 1em 0 1em; vertical-align:top; }
.xmp_bgcolor { background-color:rgb(230,230,230); }
.xmp_nav { color:'.$col_link.'; }';
   #
   # --- Schreiben der Stylesheet-Datei in /redaxo/src/addons/simple_nav/assets/
   $file=rex_path::addon(NAVIGATION).'assets/simple_nav.css';
   $handle=fopen($file,'w');
   fwrite($handle,$buffer);
   fclose($handle);
   #
   # --- Schreiben der Stylesheet-Datei in /assets/addons/simple_nav/
   $file=rex_path::addonAssets(NAVIGATION,NAVIGATION.'.css');
   $handle=fopen($file,'w');
   fwrite($handle,$buffer);
   fclose($handle);
   }
public static function write_script_file() {
   #   Erstellen der Javascript-Funktion fuer das Anzeigen/Verstecken der Navigation
   #   und Ablegen der zugehoerigen Datei in beiden Assets-Ordner des AddOns.
   #
   $buffer='function show_hide(nav,h_icon,x_icon) {
   /*   Schalter zum Anzeigen/Verbergen des Navigations-Containers id="nav"   */
   var display=document.getElementById(nav).style.display;
   if(display==\'\' || display==\'none\') {
     document.getElementById(nav).style.display=\'block\';
     document.getElementById(x_icon).style.display=\'block\';
     document.getElementById(h_icon).style.display=\'none\';
     } else {
     document.getElementById(nav).style.display=\'none\';
     document.getElementById(x_icon).style.display=\'none\';
     document.getElementById(h_icon).style.display=\'block\';
     }
   }';
   #
   # --- Schreiben der Javascript-Datei in /redaxo/src/addons/simple_nav/assets/
   $file=rex_path::addon(NAVIGATION).'assets/'.NAVIGATION.'.js';
   $handle=fopen($file,'w');
   fwrite($handle,$buffer);
   fclose($handle);
   #
   # --- Schreiben der Javascript-Datei in /assets/addons/simple_nav/
   $file=rex_path::addonAssets(NAVIGATION,NAVIGATION.'.js');
   $handle=fopen($file,'w');
   fwrite($handle,$buffer);
   fclose($handle);
   }
#
# --------------------------- Konfigurationsmenue
public static function config() {
   #   Eingabeformular fuer die Konfigurationsdaten fuer das Stylesheet
   #   benutzte functions:
   #      self::get_default_data()
   #      self::split_data($data)
   #      self::join_data($daten)
   #      self::set_config_data($data)
   #      self::write_css_file($data)
   #      self::write_script_file()
   #      self::config_form($msg,$daten)
   #
   # --- Menue-Strings
   $tx='Linktext';
   $txt=array(
      array('Navigationstyp (=1/2/3)',                            '&nbsp;'),
      array('Einrückung pro Navigations-Level',                   'px'),
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
      array('Zeile des ältesten Ahnen: Hintergrundfarbe',         $tx),
      array('Zeile des aktuellen Artikels: Randfarbe',            '&nbsp;'),
      array('Zeile des aktuellen Artikels: Hintergrundfarbe',     'Artikelname'),
      array('Zeile des aktuellen Artikels: Textfarbe (kein Link)','Text'));
   #
   # --- Default-Konfigurationsdaten
   $defdat=self::get_default_data();
   $keys=array_keys($defdat);
   #
   # --- Auslesen der gesetzten Konfigurationsdaten (falls noch nicht gesetzt: Default-Daten)
   $anz=0;
   $confdat=array();
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $dat=rex_config::get(NAVIGATION,$key);
      if(empty($dat)) $anz=$anz+1;
      if(empty($dat)) $dat=$defdat[$key];
      $confdat[$key]=$dat;
      endfor;
   #
   # --- Aufspalten der Konfigurationsdaten auf Einzelparameter (Farben)
   $daten=self::split_data($confdat);
   $longkeys=array_keys($daten);
   #
   # --- Auslesen der eingegebenen Parameter
   $wa='&nbsp; &nbsp; falscher Wert, &nbsp; zurück gesetzt zu &nbsp; ';
   $warn='';
   for($i=0;$i<count($longkeys);$i=$i+1):
      $key=$longkeys[$i];
      $post='';
      if(!empty($_POST[$key]) or $_POST[$key]=='0'):
        $post=$_POST[$key];
        else:
        $post=$daten[$key];
        endif;
      #     ggf. Dezimalkomma durch Dezimalpunkt ersetzen
      if($i<=7) $post=str_replace(',','.',$post);
      #     Eingabedaten gemaess Beschraenkungen korrigieren
      if($i==4 and $post>$daten[$longkeys[3]]):
        $korr=$daten[$key];
        $warn=$txt[$i][0].': &nbsp; <code>'.$post.'</code> (>Zeilenhöhe) '.$wa.
           ' <code>'.$korr.'</code>';
        $post=$korr;
        endif;
      if($i==7 and $post>0 and ($daten[$longkeys[5]]<=0 or $daten[$longkeys[6]]<=0)):
        $korr=0;
        $warn=$txt[$i][0].': &nbsp; <code>'.$post.'</code> (nur bei Randdicke&gt;0) '.$wa.
           ' <code>'.$korr.'</code>';
        $post=$korr;
        endif;
      if($post>255 and (strpos($key,'red')>0 or strpos($key,'green')>0 or strpos($key,'blue')>0)):
        $korr=$daten[$key];
        $k=intval(8+($i-8)/4);
        $warn=$txt[$k][0].': &nbsp; <code>'.$post.'</code> (>255) '.$wa.
           ' <code>'.$korr.'</code>';
        $post=$korr;
        endif;
      if($post>1 and strpos($key,'opac')>0):
        $korr=$daten[$key];
        $k=intval(8+($i-8)/4);
        $warn=$txt[$k][0].': &nbsp; <code>'.$post.'</code> (>1) '.$wa.
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
     # --- Konfigurationsdaten speichern, Stylesheet- und Javascript-Datei schreiben
     self::set_config_data($data);
     self::write_css_file($data);
     self::write_script_file();
     endif;
   #
   # --- Warnmeldungen und Eingabeformular ausgeben
   echo $msg.self::config_form($txt,$daten);
   }
public static function split_data($data) {
   #   Zerlegen eines assoziativen Arays im Format der konfigurierten Daten
   #   (16 Parameter) in ein assoziatives Array, in dem die konfigurierten
   #   RGBA-Farben in ihre Parameter zerlegt sind (40 Parameter)
   #   $data             Eingabe-Array
   #   Rueckgabe-Array in diesem Format:
   #      [NAV_TYP]              Integer
   #      [NAV_INDENT]           Integer
   #      [NAV_WIDTH]            Integer
   #      [NAV_LINE_HEIGHT]      Dezimalzahl mit/ohne Dezimalpunkt
   #      [NAV_FONT_SIZE]        Dezimalzahl mit/ohne Dezimalpunkt
   #      [NAV_BOR_LRWIDTH]      Integer
   #      [NAV_BOR_OUWIDTH]      Integer
   #      [NAV_BOR_RAD]          Integer
   #      [NAV_COL_LINK/xxx]     Integer (xxx=red/green/blue/opac)
   #      [NAV_COL_BORD_0/xxx]   Integer (xxx=red/green/blue/opac)
   #      [NAV_COL_BACK_0/xxx]   Integer (xxx=red/green/blue/opac)
   #      [NAV_COL_BORD_1/xxx]   Integer (xxx=red/green/blue/opac)
   #      [NAV_COL_BACK_1/xxx]   Integer (xxx=red/green/blue/opac)
   #      [NAV_COL_BORD_2/xxx]   Integer (xxx=red/green/blue/opac)
   #      [NAV_COL_BACK_2/xxx]   Integer (xxx=red/green/blue/opac)
   #      [NAV_COL_TEXT_2/xxx]   Integer (xxx=red/green/blue/opac)
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
public static function join_data($daten) {
   #   Zusammenfuehren des Arrays der eingelesenen Konfigurationsdaten
   #      (Farben jeweils in Form von 4 separaten RGBA-Werten, 40 Parameter)
   #   in ein Array der abzuspeichernden Konfigurationsdaten
   #      (Farben jeweils in Form eines RGBA-Strings, 16 Parameter)
   #   $daten            Eingabe-Array (40 Parameter)
   #
   $keys=array_keys($daten);
   $data=array();
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $val=trim($daten[$key]);
      if(substr($key,0,4)!='col_'):
        $data[$key]=$val;
        $m=$i;
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
          $m=$m+1;
          $k=0;
          endif;
        endif;
      endfor;
   return $data;
   }
public static function config_form($txt,$daten) {
   #   Rueckgabe des Eingabeformulars fuer die Konfigurationsdaten fuer das Stylesheet.
   #   $txt              Menue-Strings als nummeriertes Array in der Form
   #                     $txt[$i][0]: Erlaeuterungstext links und
   #                     $txt[$i][1]: Erlaeuterungstext rechts ($i=0, 1, ..., 15)
   #   $daten            assoziatives Array der Daten fuer die Input-Felder (40 Parameter)
   #
   $longkeys=array_keys($daten);
   $navwidth=$daten[$longkeys[2]];
   #
   # --- Farben fuer das Kontrollfeld bestimmen
   for($i=0;$i<count($longkeys);$i=$i+1):
      $key=$longkeys[$i];
      if(substr($key,0,4)!='col_'):
        $iw=$i;
        $rgba[$iw]='';
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
       <tr><td colspan="6"><b>allgemeine Parameter:</b></td></tr>';
   for($i=0;$i<count($longkeys);$i=$i+1):
      $key=$longkeys[$i];
      $val=$daten[$key];
      if(substr($key,0,4)!='col_'):
        #
        # --- Navigationstyp, Einrueckung, Groessen-Parameter
        $iw=$i;
        if($iw==1 or $iw==2 or $iw==3 or $iw==4 or $iw==7):
          $string=$string.'
       <tr><td class="nav_guide">'.$txt[$iw][0].'</td>
           <td class="nav_guide"><input class="nav_input" type="text" name="'.$key.'" value="'.$val.'" /></td>
           <td class="nav_guide" colspan="4">'.$txt[$iw][1].'</td></tr>';
          endif;
        if($iw==0 or $iw==5 or $iw==6):
          $arr=array(0,1,2);
          if($iw==0) $arr=array(1,2,3);
          $str='<select name="'.$key.'" class="nav_select">';
          for($k=0;$k<count($arr);$k=$k+1):
             if($arr[$k]==$val):
               $str=$str.'<option selected="selected">'.$arr[$k].'</option>';
               else:
               $str=$str.'<option>'.$arr[$k].'</option>';
               endif;
             endfor;
          $string=$string.'
       <tr><td class="nav_guide">'.$txt[$iw][0].'</td>
           <td class="nav_guide">'.$str.'</select></td>
           <td class="nav_guide" colspan="4">'.$txt[$iw][1].'</td></tr>';
          endif;
        else:
        #
        # --- Farben
        if(strpos($key,'/red')>0):
          $iw=$iw+1;
          $string=$string.'
       <tr><td class="nav_guide">'.$txt[$iw][0].'</td>';
          endif;
        $string=$string.'
           <td class="nav_guide"><input class="nav_input" type="text" name="'.$key.'" value="'.$val.'" /></td>';
        #
        # --- Kontrollfelder
        if(strpos($key,'/opac')>0):
          $class1='';
          if($iw==8):                        // Linktext
            $class=DIV_FORMAT;
            $class1='nav_conf_link';
            endif;
          if($iw==9 or $iw==11 or $iw==13)   // Randfarben
            $class=DIV_BORDER1.' '.DIV_FORMAT.' nav_short';
          if($iw==10 or $iw==12 or $iw==14): // Hintergrundfarben
            $class1='nav_conf_link';
            if($iw==14) $class1='nav_conf_text';
            if($iw==10) $class=DIV_BORDER1.' '.DIV_FORMAT.' typ0';
            if($iw==12) $class=DIV_BORDER1.' '.DIV_FORMAT.' typ1';
            if($iw==14) $class=DIV_BORDER1.' '.DIV_FORMAT.' typ2';
            endif;
          if($iw==15)                        // Text des aktuellen Artikels
            $class=DIV_BORDER1.' '.DIV_FORMAT.' typ2 nav_short';
          $string=$string.'
           <td class="nav_guide">
               <div class="'.$class.'"><div class="'.$class1.'">'.$txt[$iw][1].'</div></div></td></tr>';
          endif;
        endif;
      if($i==1) $string=$string.'
       <tr><td colspan="6"><b>Stylesheet-Parameter:</b></td></tr>';
      if($i==7) $string=$string.'
       <tr><td align="right">Farben + Deckungsgrad (RGBA-Werte):</td>
           <td class="nav_center">rot</td>
           <td class="nav_center">grün</td>
           <td class="nav_center">blau</td>
           <td class="nav_center">Deckung</td>
           <td class="nav_center nav_conf_link">
               <span class="nav_left">&nbsp;&nbsp;&lt;---</span> '.$navwidth.' px &nbsp; (*) <span class="nav_right">---&gt;</span></td></tr>';
      endfor;
   #
   # --- Buttons und Formular-Abschluss
   $sptit='Parameter und css-Stylesheet speichern';
   $str='auf Defaultwerte zurücksetzen und ';
   $title=$str.'speichern';
   $retit='Parameter '.$str."\n".$sptit;
   $string=$string.'
       <tr><td class="nav_guide"><br/>
               <button class="btn btn-save" type="submit" name="save" value="save" title="'.$sptit.'"> speichern </button></td>
           <td class="nav_guide" colspan="5"><br/>
               <button class="btn btn-update" type="submit" name="reset" value="reset" title="'.$retit.'">'.$title.'</button></td></tr>
   </table>
   </form>';
   return $string;
   }
#
# --------------------------- Beispiel-Navigation
public static function example() {
   #   Ausgabe des HTML-Codes einer Beispiel-Navigation gemaess den konfigurierten
   #   Daten/Stylesheet unter Beruecksichtigung des konfigurierten Navigationstyps.
   #   benutzte functions:
   #      self::get_config_data()
   #      self::example_entries($navtyp)
   #      simple_nav::sort($entries)
   #      simple_nav::print_line($entries,$increment)
   #
   # --- konfigurierte Stylesheet-Daten
   $data=self::get_config_data();
   $navtyp=$data[NAV_TYP];
   $incr=rex_config::get(NAVIGATION,NAV_INDENT);
   #
   # --- Entries (Navigationszeilen) definieren und sortieren
   $entries=self::example_entries($navtyp);
   $entries=simple_nav::sort($entries);
   #
   # --- Darstellung der Beispiel-Navigation
   echo '
<h4>Darstellung gemäß konfigurierten Styles ohne Basisartikel</b><br/>&nbsp;</h4>
<table class="nav_table">
    <tr valign="top">
        <td class="xmp_pad">
            <div class="xmp_nav xmp_bgcolor">
'.simple_nav::print_line($entries,$incr).'</div>
        </td>
        <td class="xmp_pad">&nbsp;&nbsp;&nbsp;</td>
        <td class="xmp_pad">
            <div>Abhängig vom Navigationstyp (1/2/3)<br/>
            werden Onkelkategorien bzw. -artikel<br/>
            aller Generationen des aktuellen<br/>
            Artikels angezeigt (+) oder nicht (-):
            <table class="nav_table">';
   for($n=1;$n<=3;$n=$n+1):
      $type='Typ '.$n;
      $shka='+';
      if($n==1) $shka='-';
      $shar='+';
      if($n==1 or $n==2) $shar='-';
      $class="nav_table";
      if($n==$navtyp) $class="xmp_bgcolor";
      echo '
                <tr><td class="xmp_pad '.$class.'">'.$type.':</td>
                    <td class="xmp_pad '.$class.'">Onkelkategorien</td>
                    <td class="xmp_pad '.$class.'">'.$shka.'</td></tr>
                <tr><td class="xmp_pad '.$class.'"></td>
                    <td class="xmp_pad '.$class.'">Onkelartikel</td>
                    <td class="xmp_pad '.$class.'">'.$shar.'</td></tr>';
      endfor;
   echo '
            </table></div></td></tr>
    <tr><td colspan="3">&nbsp;</td></tr>
</table>';   
   }
public static function example_entries($navtyp) {
   #   Rueckgabe der unsortierten Zeilen einer Beispielnavigation
   #   unter Beruecksichtigung des konfigurierten Navigationstyps
   #   $navtyp           konfigurierter Navigationstyp
   #                     Klassenbezeichnungen zusammengestellt werden
   #
   # --- Definition der Texte des Beispiels
   $aktu='aktueller Artikel';
   $first='Hauptkategorie Urahne';
   $entries=array(
       1=>array('id'=> 5, 'parent_id'=> 1, 'name'=>'Hauptseite 1'),
       2=>array('id'=> 6, 'parent_id'=> 1, 'name'=>'Hauptseite 2'),
       3=>array('id'=>11, 'parent_id'=> 1, 'name'=>'Hauptkategorie 1'),
       4=>array('id'=>12, 'parent_id'=> 1, 'name'=>$first),
       5=>array('id'=>13, 'parent_id'=> 1, 'name'=>'Hauptkategorie 3'),
       6=>array('id'=>21, 'parent_id'=>12, 'name'=>'Urgroßonkelkategorie 1'),
       7=>array('id'=>22, 'parent_id'=>12, 'name'=>'Urgroßvaterkategorie'),
       8=>array('id'=>23, 'parent_id'=>12, 'name'=>'Urgroßonkelkategorie 2'),
       9=>array('id'=>31, 'parent_id'=>22, 'name'=>'Großonkelartikel'),
      10=>array('id'=>32, 'parent_id'=>22, 'name'=>'Großvaterkategorie'),
      11=>array('id'=>33, 'parent_id'=>22, 'name'=>'Großonkelkategorie'),
      12=>array('id'=>41, 'parent_id'=>32, 'name'=>'Onkelartikel 1'),
      13=>array('id'=>42, 'parent_id'=>32, 'name'=>'Onkelartikel 2'),
      14=>array('id'=>43, 'parent_id'=>32, 'name'=>'Onkelartikel 3'),
      15=>array('id'=>44, 'parent_id'=>32, 'name'=>'Onkelkategorie 1'),
      16=>array('id'=>45, 'parent_id'=>32, 'name'=>'Vaterkategorie'),
      17=>array('id'=>46, 'parent_id'=>32, 'name'=>'Onkelkategorie 2'),
      18=>array('id'=>51, 'parent_id'=>45, 'name'=>'Bruderartikel 1'),
      19=>array('id'=>52, 'parent_id'=>45, 'name'=>$aktu),
      20=>array('id'=>53, 'parent_id'=>45, 'name'=>'Bruderartikel 2')
      );
   #
   # --- Level einfuegen
   for($i=1;$i<=count($entries);$i=$i+1):
      $id=$entries[$i]['id'];
      if($id<20) $entries[$i]['level']=1;
      if($id>=20 and $id<30) $entries[$i]['level']=2;
      if($id>=30 and $id<40) $entries[$i]['level']=3;
      if($id>=40 and $id<50) $entries[$i]['level']=4;
      if($id>=50 and $id<60) $entries[$i]['level']=5;
      $entries[$i]['nr']=$i;
      endfor;
   #
   # --- Entries gemaess Navigationstyp 1 oder 2 ausduennen
   if($navtyp<=2):
     $m=0;
     for($i=1;$i<=count($entries);$i=$i+1):
        $name=$entries[$i]['name'];
        if(strpos($name,'kelartikel')>0) continue;
        if($navtyp==1 and strpos($name,'kelkategorie')>0) continue;
        $m=$m+1;
        $entneu[$m]=$entries[$i];
        $entneu[$m]['nr']=$m;
        endfor;
     $entries=$entneu;
     endif;
   #
   # --- (festen) URL und Ausgabetyp einfuegen
   $url=$_SERVER['REQUEST_URI'];
   for($i=1;$i<=count($entries);$i=$i+1):
      $name=$entries[$i]['name'];
      if($name==$aktu):
        $entries[$i]['url']='';
        $entries[$i]['typ']=2;
        else:
        $entries[$i]['url']=$url;
        $entries[$i]['typ']=0;
        endif;
      if($name==$first) $entries[$i]['typ']=1;
      endfor;
   #
   return $entries;
   }
}
?>