<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version März 2020
 */
#   simple_nav-Functions:
#      get_default_data()
#      sort($entries)
#      print_line($entries,$zus,$increment)
#   Konfiguration:
#      config()
#         get_default_data()
#         split_data($data)
#         join_data($daten)
#         set_config_data($data)
#         write_css_file($data)
#            define_css($data,$zus)
#         config_form($msg,$daten)
#   Beispiel-Navigation:
#      example()
#         get_default_data()
#         sort($entries)
#         print_line($entries,$zus,$increment)
#         define_css($data,$zus)
#         example_data()
#         example_entries($navtyp)
#
class simple_nav_config {
#
# --------------------------- simple_nav-Functions
public static function get_default_data() {
   return simple_nav::get_default_data();
   }
public static function sort($entries) {
   return simple_nav::sort($entries);
   }
public static function print_line($entries,$zus,$increment) {
   return simple_nav::print_line($entries,$zus,$increment);
   }
#
# --------------------------- Konfiguration
public static function config() {
   #   Eingabeformular fuer die Konfigurationsdaten fuer das Stylesheet
   #   benutzte functions:
   #      self::get_default_data()
   #      self::split_data($data)
   #      self::join_data($daten)
   #      self::set_config_data($data)
   #      self::write_css_file($data)
   #      self::config_form($msg,$daten)
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
      $dat=rex_config::get('simple_nav',$key);
      if(empty($dat)) $anz=$anz+1;
      if(empty($dat)) $dat=$defdat[$key];
      $confdat[$key]=$dat;
      endfor;
   #
   # --- Aufspalten der Konfigurationsdaten auf Einzelparameter (Farben)
   $daten=self::split_data($confdat);
   $longkeys=array_keys($daten);
   #
   # --- falls noch keine Konfiguration gesetzt ist, Stylesheet-Datei schreiben
   if($anz>3) self::write_css_file($daten);
   #
   # --- Menue-Strings
   $txt=array(
      'Navigationstyp (=1/2/3)',
      'Einrückung pro Navigations-Level',
      'Navigationszeilen: Breite',
      'Navigationszeilen: Zeilenhöhe',
      'Linktexte: Zeichengröße (&le;Zeilenhöhe)',
      'Rand: Dicke links/rechts (0/1/2 Pixel)',
      'Rand: Dicke oben/unten (0/1/2 Pixel)',
      'Rand: Krümmungsradius für abgerundete Ecken',
      'alle Navigationszeilen: Farbe der Linktexte',
      'Standardzeile: Randfarbe',
      'Standardzeile: Hintergrundfarbe',
      'Zeile des ältesten Ahnen: Randfarbe',
      'Zeile des ältesten Ahnen: Hintergrundfarbe',
      'Zeile des aktuellen Artikels: Randfarbe',
      'Zeile des aktuellen Artikels: Hintergrundfarbe',
      'Zeile des aktuellen Artikels: Textfarbe (kein Link)');
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
      #     Eingabedaten gemaess Beschraenkungen korrigieren
      if($i==4 and $post>$daten[$longkeys[3]]):
        $korr=$daten[$key];
        $warn=$txt[$i].': &nbsp; <code>'.$post.'</code> (>Zeilenhöhe) '.$wa.
           ' <code>'.$korr.'</code>';
        $post=$korr;
        endif;
      if($i==7 and $post>0 and ($daten[$longkeys[5]]<=0 or $daten[$longkeys[6]]<=0)):
        $korr=0;
        $warn=$txt[$i].': &nbsp; <code>'.$post.'</code> (nur bei Randdicke&gt;0) '.$wa.
           ' <code>'.$korr.'</code>';
        $post=$korr;
        endif;
      if($post>255 and (strpos($key,'red')>0 or strpos($key,'green')>0 or strpos($key,'blue')>0)):
        $korr=$daten[$key];
        $k=intval(8+($i-8)/4);
        $warn=$txt[$k].': &nbsp; <code>'.$post.'</code> (>255) '.$wa.
           ' <code>'.$korr.'</code>';
        $post=$korr;
        endif;
      if($post>1 and strpos($key,'opac')>0):
        $korr=$daten[$key];
        $k=intval(8+($i-8)/4);
        $warn=$txt[$k].': &nbsp; <code>'.$post.'</code> (>1) '.$wa.
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
       $data=self::get_default_data();
       $daten=self::split_data($data);
       else:
       $data=self::join_data($daten);
       endif;
     #
     # --- Konfigurationsdaten zurueckspeichern und Stylesheet-Datei neu schreiben
     self::set_config_data($data);
     self::write_css_file($data);
     endif;
   #
   # --- Warnmeldungen und Eingabeformular ausgeben
   echo $msg.self::config_form($txt,$daten);
   }
public static function set_config_data($data) {
   #   Schreiben der Konfigurationsdaten
   #   $data             Array der zu schreibenden Daten
   #
   $keys=array_keys($data);
   for($i=0;$i<count($keys);$i=$i+1):
      $val=$data[$keys[$i]];
      if(is_int($val) or $val=='0') $val=intval($val);
      rex_config::set('simple_nav',$keys[$i],$val);
      endfor;
   }
public static function split_data($data) {
   #   Zerlegen eines assoziativen Arays im Format der konfigurierten Daten
   #   (16 Parameter) in ein assoziatives Array, in dem die konfigurierten
   #   RGBA-Farben in ihre Parameter zerlegt sind (40 Parameter
   #   $data             Eingabe-Array
   #   Rueckgabe-Array in diesem Format:
   #      ['navtyp']             Integer
   #      ['indent']             Integer
   #      ['width']              Integer
   #      ['line_height']        Dezimalzahl mit/ohne Dezimalpunkt
   #      ['font_size']          Dezimalzahl mit/ohne Dezimalpunkt
   #      ['bor_lrwidth']        Integer
   #      ['bor_ouwidth']        Integer
   #      ['bor_rad']            Integer
   #      ['col_link/$par']      Integer (xxx=red/green/blue/opac)
   #      ['col_border_0/xxx']   Integer (xxx=red/green/blue/opac)
   #      ['col_backgr_0/xxx']   Integer (xxx=red/green/blue/opac)
   #      ['col_border_1/xxx']   Integer (xxx=red/green/blue/opac)
   #      ['col_backgr_1/xxx']   Integer (xxx=red/green/blue/opac)
   #      ['col_border_2/xxx']   Integer (xxx=red/green/blue/opac)
   #      ['col_backgr_2/xxx']   Integer (xxx=red/green/blue/opac)
   #      ['col_text_2/xxx']     Integer (xxx=red/green/blue/opac)
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
   #   $txt              Menue-Strings als nummeriertes Array (Nummerierung ab 0)
   #   $daten            assoziatives Array der Daten fuer die Input-Felder (40 Parameter)
   #
   # --- Konstanten
   $tx='Linktext';
   $erg=array('','px','px &nbsp; (*)','em','em','px','px','em',
      $tx,'',$tx,'',$tx,'','Artikelname','Text');
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
       <tr><td class="nav_guide">'.$txt[$iw].'</td>
           <td class="nav_guide"><input class="form-control nav_input" type="text" name="'.$key.'" value="'.$val.'" /></td>
           <td class="nav_guide" colspan="4">'.$erg[$iw].'</td></tr>';
          endif;
        if($iw==0 or $iw==5 or $iw==6):
          $arr=array(0,1,2);
          if($iw==0) $arr=array(1,2,3);
          $str='<select name="'.$key.'" class="form-control nav_input">';
          for($k=0;$k<count($arr);$k=$k+1):
             if($arr[$k]==$val):
               $str=$str.'<option selected="selected">'.$arr[$k].'</option>';
               else:
               $str=$str.'<option>'.$arr[$k].'</option>';
               endif;
             endfor;
          $string=$string.'
       <tr><td class="nav_guide">'.$txt[$iw].'</td>
           <td class="nav_guide">'.$str.'</select></td>
           <td class="nav_guide" colspan="4">'.$erg[$iw].'</td></tr>';
          endif;
        else:
        #
        # --- Farben
        if(strpos($key,'/red')>0):
          $iw=$iw+1;
          $string=$string.'
       <tr><td class="nav_guide">'.$txt[$iw].'</td>';
          endif;
        $string=$string.'
           <td class="nav_guide"><input class="form-control nav_input" type="text" name="'.$key.'" value="'.$val.'" /></td>';
        #
        # --- Kontrollfelder
        if(strpos($key,'/opac')>0):
          $fsize='font-size:'.$daten[$longkeys[4]].'em;';
          if($iw==8):                        // Linktext
            $col='color:'.$rgba[$iw].';';
            $style1='style="padding:3px;"';
            $style2='style="'.$fsize.' '.$col.'"';
            $text=$erg[$iw];
            endif;
          if($iw==9 or $iw==11 or $iw==13):  // Randfarben
              $style1='style="width:60px;"';
              $style2='style="padding-left:3px; font-size:0.5em; '.$bgcol.
                      'border-top:   solid '.$daten[$longkeys[6]].'px '.$rgba[$iw].';'.
                      'border-bottom:solid '.$daten[$longkeys[6]].'px '.$rgba[$iw].';'.
                      'border-left:  solid '.$daten[$longkeys[5]].'px '.$rgba[$iw].';'.
                      'border-right: solid '.$daten[$longkeys[5]].'px '.$rgba[$iw].';'.
                      'border-radius:'.$daten[$longkeys[7]].'em;"';
              $text='&nbsp;';
            endif;
          if($iw==10 or $iw==12 or $iw==14): // Hintergrundfarben
            $col=$rgba[8];
            if($iw==14) $col=$rgba[$iw+1];
            $col='color:'.$col.';';
            if($iw==10) $style1='class="simple_nav1 typ0"';
            if($iw==12) $style1='class="simple_nav1 typ1"';
            if($iw==14) $style1='class="simple_nav1 typ2"';
            $style2='style="'.$fsize.' '.$col.'"';
            $text=$erg[$iw];
            endif;
          if($iw==15):                       // Text des aktuellen Artikels
            $col='color:'.$rgba[$iw].';';
            $bgcol='background-color:'.$rgba[14].';';
            $style1='style="width:60px;"';
            $style2='style="padding-left:3px; '.$fsize.' '.$col.' '.$bgcol.'"';
            $text=$erg[$iw];
            endif;
          $string=$string.'
           <td class="nav_guide">
               <div '.$style1.'><div '.$style2.'>'.$text.'</div></div></td></tr>';
          endif;
        endif;
      if($i==1) $string=$string.'
       <tr><td colspan="6"><b>Stylesheet-Parameter:</b></td></tr>';
      if($i==7) $string=$string.'
       <tr><td align="right">Farben + Deckungsgrad (RGBA-Werte):</td>
           <td class="nav_guide" align="center">rot</td>
           <td class="nav_guide" align="center">grün</td>
           <td class="nav_guide" align="center">blau</td>
           <td class="nav_guide" align="center">Deckung</td>
           <td class="nav_guide" align="center" width="'.$navwidth.'">&lt;--- &nbsp; '.$navwidth.' px &nbsp; (*) &nbsp; ---&gt;</td></tr>';
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
public static function write_css_file($data) {
   #   Schreiben der Stylesheets fuer die Navigation in beide Assets-Ordner des AddOns
   #   $data             Array der Daten, aus denen die Stylesheet-
   #                     Klassenbezeichnungen zusammengestellt werden
   #   benutzte functions:
   #      self::define_css($data,$zus)
   #
   # --- Stylesheet erzeugen
   $buffer=self::define_css($data,'');
   #
   # --- Schreiben der Stylesheet-Datei in /redaxo/src/addons/simple_nav/assets/
   $file=rex_path::addon('simple_nav').'assets/simple_nav.css';
   $handle=fopen($file,'w');
   fwrite($handle,$buffer);
   fclose($handle);
   #
   # --- Schreiben der Stylesheet-Datei in /assets/addons/simple_nav/
   $file=rex_path::addonAssets('simple_nav','simple_nav.css');
   $handle=fopen($file,'w');
   fwrite($handle,$buffer);
   fclose($handle);
   }
public static function define_css($data,$zus='') {
   #   Rueckgabe eines Strings mit dem Inhalt der Stylesheets
   #   fuer die Navigation
   #   $data             Array der Daten, aus denen die Stylesheet-
   #                     Klassenbezeichnungen zusammengestellt werden
   #   $zus              Zusatzstring zu den Klassennamen fuer die
   #                     div-Container (nur fuer die Beispiele benoetigt)
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
   $nav=DIV_CLASS.$zus;
   $nav1=DIV_CLASS1.$zus;
   $typ0=DIV_TYP.'0'.$zus;
   $typ1=DIV_TYP.'1'.$zus;
   $typ2=DIV_TYP.'2'.$zus;
   #
   # --- Styles fuer die Navigation selbst
   $buffer=
'/*   s i m p l e _ n a v - N a v i g a t i o n   */
div.'.$nav1.' {
   padding:3px; width:'.$width.'px; line-height:'.$line_height.'em;
   border-bottom:solid '.$bor_ouwidth.'px '.$col_border_0.';
   border-top:   solid '.$bor_ouwidth.'px '.$col_border_0.';     /* auch oberer Rand */
   border-left:  solid '.$bor_lrwidth.'px '.$col_border_0.';
   border-right: solid '.$bor_lrwidth.'px '.$col_border_0.';
   border-radius:'.$bor_rad.'em; }
div.'.$nav.' {
   padding:3px; width:'.$width.'px; line-height:'.$line_height.'em;
   border-bottom:solid '.$bor_ouwidth.'px '.$col_border_0.';
   border-top:   solid 0px '.$col_border_0.';     /* kein oberer Rand */
   border-left:  solid '.$bor_lrwidth.'px '.$col_border_0.';
   border-right: solid '.$bor_lrwidth.'px '.$col_border_0.';
   border-radius:'.$bor_rad.'em; }
div.'.$typ0.' { background-color:'.$col_backgr_0.'; }
div.'.$typ1.' { background-color:'.$col_backgr_1.'; }
div.'.$typ2.' { background-color:'.$col_backgr_2.'; }
div.'.$typ0.' div a, div.'.$typ1.' div a {
   font-size:'.$font_size.'em; color:'.$col_link.'; }
div.'.$typ2.' div {
   font-size:'.$font_size.'em; color:'.$col_text_2.'; }';
   #
   # --- Styles fuer die Konfiguration
   $buffer=$buffer.'
/*   fuer die Konfiguration   */
.nav_table { background-color:inherit; }
.nav_guide { padding-left:10px; white-space:nowrap; }
.nav_input { width:60px; text-align:right; }';
   #
   # --- Styles fuer das Beispiel
   $buffer=$buffer.'
/*   fuer das Beispiel   */
.xmp_nowrap { vertical-align:top; white-space:nowrap; }
.xmp_pad { padding-left:20px; }
.xmp_bgcolor { background-color:silver; }
.xmp_border { padding:5px; border:solid 3px silver; }';
   #
   return $buffer;
   }
#
# --------------------------- Beispiel-Navigation
public static function example() {
   #   Ausgabe des HTML-Codes zweier Beispiel-Navigationen
   #   gemaess konfigurierten bzw. modifizierten Daten/Stylesheet
   #   unter Beruecksichtigung des konfigurierten Navigationstyps
   #   benutzte functions:
   #      self::get_default_data()
   #      self::define_css($data,$zus)
   #      self::example_data()
   #      self::sort($entries)
   #      self::example_entries($navtyp)
   #      self::print_line($entries,$zus,$increment)
   #
   # --- 1) konfigurierte Stylesheet-Daten
   $data=self::get_default_data();
   $keys=array_keys($data);
   for($i=0;$i<count($keys);$i=$i+1) $data[$keys[$i]]=rex_config::get('simple_nav',$keys[$i]);
   $navtyp=$data[$keys[0]];
   $incr1=rex_config::get('simple_nav',$keys[1]);
   $stx='style="background-color:'.$data[$keys[10]].'; color:'.$data[$keys[8]].';"';
   #
   # --- 2) Beispiel-Stylesheet-Daten (Navigationstyp wird uebernommen)
   $xmpdata=self::example_data();
   $xmpdata[$keys[0]]=$navtyp;
   $zus='xmp';
   $incr2=$xmpdata[$keys[1]];
   $styles2=self::define_css($xmpdata,$zus);
   $sty='style="background-color:'.$xmpdata[$keys[10]].'; color:'.$xmpdata[$keys[8]].';"';
   #
   # --- Entries (Navigationszeilen)
   $entries=self::example_entries($navtyp);
   $entries=self::sort($entries);  // sortieren
   #
   # --- Darstellung beider Navigationen nebeneinander
   echo '
<style>
'.$styles2.'
</style>
<table class="nav_table">
    <tr valign="top">
        <td><b>Darstellung ohne Basisartikel:</b>
            <div><br/>
            Abhängig vom Navigationstyp (1/2/3)<br/>
            werden Onkelkategorien bzw. -artikel<br/>
            aller Generationen angezeigt (+)<br/>
            oder nicht (-):
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
                <tr><td class="xmp_pad xmp_nowrap '.$class.'">'.$type.':</td>
                    <td class="xmp_pad xmp_nowrap '.$class.'">Onkelkategorien</td>
                    <td class="xmp_pad xmp_nowrap '.$class.'">'.$shka.'</td></tr>
                <tr><td class="xmp_pad xmp_nowrap '.$class.'"></td>
                    <td class="xmp_pad xmp_nowrap '.$class.'">Onkelartikel</td>
                    <td class="xmp_pad xmp_nowrap '.$class.'">'.$shar.'</td></tr>';
      endfor;
   echo '
            </table></div></td>
        <td class="xmp_pad">
            <div align="center"><b>konfigurierte&nbsp;Styles</b></div><br/>
            <div class="xmp_border" '.$stx.'>
'.self::print_line($entries,'',$incr1).'</div>
        </td>
        <td class="xmp_pad">
            <div align="center"><b>feste&nbsp;Beispiel-Styles</b></div></br/>
            <div class="xmp_border" '.$sty.'>
'.self::print_line($entries,$zus,$incr2).'</div>
        </td></tr>
</table>';
   }
public static function example_data() {
   #   Rueckgabe der Beispiel-Werte der Stylesheet-Daten
   #   als assoziatives Array
   #
   $blau ='rgba(72,120,160,1)';
   $weiss='rgba(255,255,255,1)';
   $rot  ='rgba(183,81,0,1)';
   return array(
      'navtyp'      =>0,
      'indent'      =>20,
      'width'       =>220,
      'line_height' =>'1.5',
      'font_size'   =>'1.2',
      'bor_lrwidth' =>1,
      'bor_ouwidth' =>1,
      'bor_rad'     =>'0.5',
      'col_link'    =>$weiss,
      'col_border_0'=>$blau,
      'col_backgr_0'=>$blau,
      'col_border_1'=>$blau,
      'col_backgr_1'=>$blau,
      'col_border_2'=>$blau,
      'col_backgr_2'=>$rot,
      'col_text_2'  =>$weiss);
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
