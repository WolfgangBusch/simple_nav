<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Juli 2018
 */
#
# ---------------------------
#   config()
#      get_default_data()
#      config_split_data($data)
#      config_join_data($daten)
#      config_form($msg,$daten)
#      write_css($daten)
#         define_css($data,$zus)
#   example()
#      get_default_data()
#      print_line($entries,$incr,$increment)
#      define_css($data,$zus)
#      example_data()
#      example_entries($navtyp)
#         sort($entries)
# --------------------------- Nutzung von simple_nav-Functions
#
class simple_nav_config {
#
public static function get_default_data() {
   return simple_nav::get_default_data();
   }
public static function sort($entries) {
   return simple_nav::sort($entries);
   }
public static function print_line($entries,$incr,$increment) {
   return simple_nav::print_line($entries,$incr,$increment);
   }
# --------------------------- Functions fuer die Konfiguration
public static function config() {
   #   Eingabeformular fuer die Konfigurationsdaten fuer das Stylesheet
   #   benutzte functions:
   #      self::get_default_data()
   #      self::config_split_data($data)
   #      self::config_join_data($daten)
   #      self::write_css($daten)
   #      self::menuestrings()
   #      self::config_form($msg,$daten)
   #
   # --- Default-Konfigurationsdaten
   $defdat=self::get_default_data();
   $keys=array_keys($defdat);
   #
   # --- Auslesen der gesetzten Konfigurationsdaten (falls noch nicht gesetzt: Default-Daten)
   $anz=0;
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $dat=rex_config::get('simple_nav',$key);
      if(empty($dat)) $anz=$anz+1;
      if(empty($dat)) $dat=$defdat[$key];
      $confdat[$key]=$dat;
      endfor;
   #
   # --- Aufspalten der Konfigurationsdaten auf Einzelparameter (Farben)
   $daten=self::config_split_data($confdat);
   $longkeys=array_keys($daten);
   #
   # --- falls noch keine Konfiguration gesetzt ist, Stylesheet-Datei schreiben
   if($anz>3) self::write_css($daten);
   #
   # --- Auslesen der eingegebenen Parameter
   $txt=self::menuestrings();
   $wa='&nbsp; &nbsp; falscher Wert, &nbsp; korrigiert zu &nbsp; ';
   $warn='';
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $post=$_POST["$key"];
      if(is_scalar($post)):
        if(empty($post) and $post!='0') $post=$confdat[$key];
        # --- Eingabedaten gemaess Beschraenkungen korrigieren
        if($i==4 and $post>$daten[$keys[3]]):
          $korr=$daten[$keys[3]];
          $warn=$txt[$i].': &nbsp; <code>'.$post.'</code> '.$wa.
             ' <code>'.$korr.'</code>';
          $post=$korr;
          endif;
        $daten[$key]=$post;
        else:
        $pkeys=array_keys($post);
        for($k=0;$k<count($pkeys);$k=$k+1):
           $keyc=$pkeys[$k];
           $longkey=$key.'['.$keyc.']';
           $po=$post[$keyc];
           if(empty($po) and $po!='0') $po=$daten[$longkey];
           $daten[$longkey]=$po;
           endfor;
        endif;
      endfor;
   #
   # --- Warnung zu falschen (und korrigierten) Eingaben
   $msg='';
   if(!empty($warn)) $msg=rex_view::warning($warn);
   #
   # --- speichern bzw. zuruecksetzen und speichern (Daten + Stylesheet-Datei)
   $save=$_POST['save'];
   $reset=$_POST['reset'];
   if(!empty($save) or !empty($reset)):
     if(!empty($reset)):
       $data=self::get_default_data();
       $daten=self::config_split_data($data);
       else:
       $data=self::config_join_data($daten);
       endif;
     $keys=array_keys($data);
     #
     # --- Konfigurationsdaten zurueckspeichern
     for($i=0;$i<count($keys);$i=$i+1):
        $val=$data[$keys[$i]];
        if(is_int($val) or $val=='0') $val=intval($val);
        rex_config::set('simple_nav',$keys[$i],$val);
        endfor;
     #
     # --- Stylesheet-Datei neu schreiben
     self::write_css($data);
     $msg=$msg.rex_view::info('Und nun noch das <u>AddOn '.
        're-installieren</u>, damit das neue Stylesheet zur Wirkung kommt!');
     endif;
   #
   # --- Eingabeformular
   echo self::config_form($msg,$daten);
   }
public static function config_split_data($data) {
   #   Zerlegen eines assoziativen Arays im Format der konfigurierten Daten
   #   in ein assoziatives Array, in dem die konfigurierten RGBA-Farben
   #   in ihre Parameter zerlegt sind
   #   $data             Eingabe-Array (Keys gemaess get_default_data())
   #   Rueckgabe-Array in diesem Format:
   #      [navtyp]              Integer
   #      [indent]              Integer
   #      [width]               Integer
   #      [line_height]         Dezimalzahl mit/ohne Dezimalpunkt
   #      [font_size]           Dezimalzahl mit/ohne Dezimalpunkt
   #      [bor_lrwidth]         Integer
   #      [bor_ouwidth]         Integer
   #      [bor_rad]             Integer
   #      [col_link[red]]       Integer
   #      [col_link[green]]     Integer
   #      [col_link[blue]]      Integer
   #      [col_link[opac]]      Integer
   #      [col_border_0[red]]   Integer
   #      [col_border_0[green]] Integer
   #      [col_border_0[blue]]  Integer
   #      [col_border_0[opac]]  Integer
   #      [col_backgr_0[red]]   Integer
   #      [col_backgr_0[green]] Integer
   #      [col_backgr_0[blue]]  Integer
   #      [col_backgr_0[opac]]  Integer
   #      [col_border_1[red]]   Integer
   #      [col_border_1[green]] Integer
   #      [col_border_1[blue]]  Integer
   #      [col_border_1[opac]]  Integer
   #      [col_backgr_1[red]]   Integer
   #      [col_backgr_1[green]] Integer
   #      [col_backgr_1[blue]]  Integer
   #      [col_backgr_1[opac]]  Integer
   #      [col_border_2[red]]   Integer
   #      [col_border_2[green]] Integer
   #      [col_border_2[blue]]  Integer
   #      [col_border_2[opac]]  Integer
   #      [col_backgr_2[red]]   Integer
   #      [col_backgr_2[green]] Integer
   #      [col_backgr_2[blue]]  Integer
   #      [col_backgr_2[opac]]  Integer
   #      [col_text_2[red]]     Integer
   #      [col_text_2[green]]   Integer
   #      [col_text_2[blue]]    Integer
   #      [col_text_2[opac]]    Integer
   #   benutzte functions:
   #      self::split_color($color)
   #
   $keys=array_keys($data);
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $val=$data[$key];
      if(substr($key,0,4)!='col_'):
        $daten[$key]=$val;
        else:
        $col=self::split_color($val);
        $daten[$key.'[red]']  =$col[red];
        $daten[$key.'[green]']=$col[green];
        $daten[$key.'[blue]'] =$col[blue];
        $daten[$key.'[opac]'] =$col[opac];
        endif;
      endfor;
   return $daten;
   }
public static function split_color($color) {
   #   Rueckgabe der RGBA-Komponenten eines RGBA-Farbstrings
   #   in Form eines assoziativen Arrays mit diesen Keys:
   #      [red]    rote Komponente
   #      [green]  gruene Komponente
   #      [blue]   blaue Komponente
   #      [opac]   Deckungsgrad
   #   $color            RGBA-String der Farbe
   #
   $arr=explode(',',$color);
   $red  =trim(substr($arr[0],5));
   $green=trim($arr[1]);
   $blue =trim($arr[2]);
   $opac =trim(substr($arr[3],0,strlen($arr[3])-1));
   return array("red"=>$red, "green"=>$green, "blue"=>$blue, "opac"=>$opac);
   }
public static function nearly_white($color) {
   #   Entscheidung, ob eine gegebene Farbe nahezu weiss ist
   #   $color            RGBA-String der Farbe
   #   benutzte functions:
   #      self::split_color($color)
   #
   $thresh=220;    // Schwellwert
   $arr=self::split_color($color);
   if($arr[red]>$thresh and $arr[green]>$thresh and $arr[blue]>$thresh):
     return TRUE;
     else:
     return FALSE;
     endif;
   }
public static function config_join_data($daten) {
   #   Zusammenfuehren des Arrays der eingelesenen Konfigurationsdaten
   #      (Farben jeweils in Form von 4 separaten RGBA-Werten,
   #      Keys: vergl. config_split_data)
   #   in ein Array der abzuspeichernden Konfigurationsdaten
   #      (Farben jeweils in Form eines RGBA-Strings
   #      Keys: vergl. get_default_data)
   #   $daten            Eingabe-Array
   #
   $keys=array_keys($daten);
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
          $arr=explode('[',$key);
          $key=$arr[0];
          $data[$key]=$rgba;
          $m=$m+1;
          $k=0;
          endif;
        endif;
      endfor;
   return $data;
   }
public static function config_form($msg,$daten) {
   #   Rueckgabe des Eingabeformulars fuer die Konfigurationsdaten fuer das
   #   Stylesheet (im utf8-Format)
   #   $msg              oberhalb des Formulars auszugebender String
   #   $daten            assoziatives Array der Daten fuer die Input-Felder
   #                     (Keys vergl. config_split_data)
   #   benutzte functions:
   #      self::menuestrings()
   #      self::nearly_white($color)
   #
   # --- Menütexte, Konstanten
   $txt=self::menuestrings();
   $t1='Linktext';
   $t2='&mdash;&mdash;&mdash;&mdash;&mdash;';
   $erg=array('','px','px','em','em','px','px','em',
      $t1,$t2,$t1,$t2,$t1,$t2,'Artikelname','Text');
   $stx='style="padding-left:20px; padding-right:5px; white-space:nowrap;"';
   $sty='style="padding-left:10px; white-space:nowrap;"';
   $stz='style="padding-left:10px; text-align:center;"';
   $stc='class="form-control"';
   $width=40;
   if(!empty($stc)) $width=60;
   #
   # --- Farben fuer das Kontrollfeld bestimmen
   $longkeys=array_keys($daten);
   for($i=0;$i<count($longkeys);$i=$i+1):
      $key=$longkeys[$i];
      if(substr($key,0,4)!='col_'):
        $iw=$i;
        $rgba[$iw]='';
        else:
        if(strpos($key,'[red]')>0):
          $iw=$iw+1;
          $arr=explode('[',$key);
          $ke=$arr[0];
          $rgba[$iw]='rgba('.trim($daten[$ke.'[red]']).','.
             trim($daten[$ke.'[green]']).','.
             trim($daten[$ke.'[blue]']).','.
             trim($daten[$ke.'[opac]']).')';
          endif;
        endif;
      endfor;
   #
   # --- Formularanfang
   $string=$msg.'
   <form method="post">
   <table style="background-color:inherit;">
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
       <tr><td '.$stx.'>'.$txt[$iw].'</td>
           <td '.$sty.'><input '.$stc.' type="text" name="'.$key.'" value="'.$val.'" style="width:'.$width.'px; text-align:right;" /></td>
           <td colspan="4" '.$sty.'>'.$erg[$iw].'</td></tr>';
          endif;
        if($iw==0 or $iw==5 or $iw==6):
          $arr=array(0,1,2);
          if($iw==0) $arr=array(1,2,3);
          $str='<select name="'.$key.'" '.$stc.' style="width:'.$width.'px;">';
          for($k=0;$k<count($arr);$k=$k+1):
             if($arr[$k]==$val):
               $str=$str.'<option selected="selected">'.$arr[$k].'</option>';
               else:
               $str=$str.'<option>'.$arr[$k].'</option>';
               endif;
             endfor;
          $string=$string.'
       <tr><td '.$stx.'>'.$txt[$iw].'</td>
           <td '.$sty.'>'.$str.'</select></td>
           <td colspan="4" '.$sty.'>'.$erg[$iw].'</td></tr>';
          endif;
        else:
        #
        # --- Farben
        if(strpos($key,'[red]')>0):
          $iw=$iw+1;
          $string=$string.'
       <tr><td '.$stx.'>'.$txt[$iw].'</td>';
          endif;
        $string=$string.'
           <td '.$sty.'><input '.$stc.' type="text" name="'.$key.'" value="'.$val.'" style="width:'.$width.'px; text-align:right;" /></td>';
        #
        # --- Kontrollfelder
        if(strpos($key,'[opac]')>0):
          if($iw==8 or $iw==9 or $iw==11 or $iw==13):
            #     Linktext und Randfarben
            $pwidth=intval(40+$daten[$longkeys[2]]/4);
            $pwidth=90;
            $col   =$rgba[$iw];
            $bgcol='background-color:inherit;';
            if(self::nearly_white($col)) echo "<div>$col ist nahezu weiß</div>\n";
            if(self::nearly_white($col)) $bgcol='background-color:'.$rgba[10].';';
            $border='border-style:none;';
            endif;
          if($iw==10 or $iw==12 or $iw==14):
            #     Hintergrundfarben
            $pwidth=$daten[$longkeys[2]];
            $col   =$rgba[8];
            $bgcol ='background-color:'.$rgba[$iw].';';
            if($iw==14) $col=$rgba[$iw+1];
            $borcol=$rgba[$iw-1];
            $border='border-radius:'.$daten[$longkeys[7]].'em;
                             border-left:  solid '.$daten[$longkeys[5]].'px '.$borcol.';
                             border-right: solid '.$daten[$longkeys[5]].'px '.$borcol.';
                             border-top:   solid '.$daten[$longkeys[6]].'px '.$borcol.';
                             border-bottom:solid '.$daten[$longkeys[6]].'px '.$borcol.';';
            endif;
          if($iw==15):
            #     Text des aktuellen Artikels
            $pwidth=90;
            $col   =$rgba[$iw];
            $bgcol ='background-color:'.$rgba[14].';';
            $border='border-style:none;';
            endif;
          $string=$string.'
           <td '.$stx.' align="right">
               <input '.$stc.' type="text"  value="'.$erg[$iw].'"
                      style="width:'.$pwidth.'px;
                             line-height:'.$daten[$longkeys[3]].'em;
                             font-size:'.$daten[$longkeys[4]].'em;
                             '.$border.'
                             '.$bgcol.'
                             color:'.$col.';" /></td></tr>';
          endif;
        endif;
      if($i==1) $string=$string.'
       <tr><td colspan="6"><b>Stylesheet-Parameter:</b></td></tr>';
      if($i==7) $string=$string.'
       <tr><td align="right">Farben + Deckungsgrad (RGBA-Werte):</td>
           <td '.$stz.'>rot</td>
           <td '.$stz.'>grün</td>
           <td '.$stz.'>blau</td>
           <td '.$stz.'>Deck.</td>
           <td '.$stz.'>Darstellung</td></tr>';
      endfor;
   #
   # --- Buttons und Formular-Abschluss
   $sptit='Parameter und css-Stylesheet speichern';
   $str='auf Defaultwerte zurücksetzen und ';
   $title=$str.'speichern';
   $retit='Parameter '.$str."\n".$sptit;
   $string=$string.'
       <tr><td '.$stx.'><br/>
               <button class="btn btn-save" type="submit" name="save" value="save" title="'.$sptit.'"> speichern </button></td>
           <td '.$sty.' colspan="5"><br/>
               <button class="btn btn-update" type="submit" name="reset" value="reset" title="'.$retit.'">'.$title.'</button></td></tr>
   </table>
   </form>';
   return utf8_encode($string);
   }
public static function menuestrings() {
   #   Rueckgabe der Menue-Strings als nummeriertes Array
   #
   return array(
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
   }
public static function write_css($data) {
   #   Schreiben der Stylesheets fuer die Navigation
   #   $data             Array der Daten, aus denen die Stylesheet-
   #                     Klassenbezeichnungen zusammengestellt werden
   #   benutzte functions:
   #      self::define_css($data,$zus)
   #
   # --- Stylesheet erzeugen
   $buffer=self::define_css($data,'');
   #
   # --- Schreiben der Stylesheet-Datei
   $file=rex_path::addon('simple_nav').'assets/simple_nav.css';
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
   # --- String der Stylesheet-Anweisungen erzeugen
   $buffer=
'/*   s i m p l e _ n a v - N a v i g a t i o n   */
div.'.$nav1.' {
   padding:3px; width:'.$width.'px; line-height:'.$line_height.'em;
   border-bottom:solid '.$bor_ouwidth.'px '.$col_border_0.';
   border-top:   solid '.$bor_ouwidth.'px '.$col_border_0.';
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
div.'.$typ0.' {
   background-color:'.$col_backgr_0.';
   border-color:'.$col_border_0.'; }
div.'.$typ1.' {
   background-color:'.$col_backgr_1.';
   border-color:'.$col_border_1.'; }
div.'.$typ2.' {
   background-color:'.$col_backgr_2.';
   border-color:'.$col_border_2.'; }
div.'.$typ0.' div a, div.'.$typ1.' div a {
   font-size:'.$font_size.'em;
   color:'.$col_link.'; }
div.'.$typ2.' div {
   font-size:'.$font_size.'em;
   color:'.$col_text_2.'; }';
   #
   # --- und zurueckgeben
   return $buffer;
   }
# --------------------------- Functions fuer die Beispiel-Navigation
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
   #      self::print_line($entries,$incr,$increment)
   #
   # --- 1) konfigurierte Stylesheet-Daten
   $data=self::get_default_data();
   $keys=array_keys($data);
   for($i=0;$i<count($keys);$i=$i+1) $data[$keys[$i]]=rex_config::get('simple_nav',$keys[$i]);
   $navtyp=$data[$keys[0]];
   $incr1=rex_config::get('simple_nav',$keys[1]);
   $stycol='border:solid 1px '.$data[$keys[9]].'; background-color:'.$data[$keys[10]].'; color:'.$data[$keys[8]].';';
   #
   # --- 2) Beispiel-Stylesheet-Daten (Navigationstyp wird uebernommen)
   $xmpdata=self::example_data();
   $xmpdata[$key[0]]=$navtyp;
   $zus='xmp';
   $incr2=$xmpdata[$keys[1]];
   $styles2=self::define_css($xmpdata,$zus);
   $stxcol='border:solid 1px '.$xmpdata[$keys[10]].'; background-color:'.$xmpdata[$keys[9]].'; color:'.$xmpdata[$keys[8]].';';
   #
   # --- Entries (Navigationszeilen)
   $entries=self::example_entries($navtyp);
   $entries=self::sort($entries);  // sortieren
   #
   # --- Darstellung beider Navigationen nebeneinander
   $sty='style="padding-left:10px; vertical-align:top;"';
   echo '
<style>
'.$styles2.'
</style>
<div align="center">
<table style="background-color:inherit;">
    <tr valign="top">
        <td style="padding:10px; white-space:nowrap;">
            <b>Darstellung ohne Navigations-Basisartikel<br/>
            Navigation vom Typ '.$navtyp.'</b><br/><br/>
            Urgro&szlig;-/Gro&szlig;-/Onkelkategorien bzw. -artikel<br/>
            werden je nach Typ angezeigt / nicht angezeigt:<br/>
            <table style="background-color:inherit;">
                <tr><td '.$sty.'>Typ 1:</td><td '.$sty.'>Onkelkategorien</td><td '.$sty.'>nicht angezeigt</td></tr>
                <tr><td '.$sty.'>      </td><td '.$sty.'>Onkelartikel   </td><td '.$sty.'>nicht angezeigt</td></tr>
                <tr><td '.$sty.'>Typ 2:</td><td '.$sty.'>Onkelkategorien</td><td '.$sty.'>angezeigt</td></tr>
                <tr><td '.$sty.'>      </td><td '.$sty.'>Onkelartikel   </td><td '.$sty.'>nicht angezeigt</td></tr>
                <tr><td '.$sty.'>Typ 3:</td><td '.$sty.'>Onkelkategorien</td><td '.$sty.'>angezeigt</td></tr>
                <tr><td '.$sty.'>      </td><td '.$sty.'>Onkelartikel   </td><td '.$sty.'>angezeigt</td></tr>
            </table></div></td>
        <td style="padding:10px;">
            <div align="center"><b>konfigurierte&nbsp;Styles</b></div><br/>
            <div style="padding:10px; '.$stycol.'">
'.self::print_line($entries,'',$incr1).'</div>
        </td>
        <td style="padding:10px;">
            <div align="center"><b>feste&nbsp;Beispiel-Styles</b></div></br/>
            <div style="padding:10px; '.$stxcol.'">
'.self::print_line($entries,$zus,$incr2).'</div>
        </td></tr>
</table>
</div>';
   }
public static function example_data() {
   #   Rueckgabe der Beispiel-Werte der Stylesheet-Daten
   #   als assoziatives Array
   #
   $blau ='rgba(72,120,160,1)';
   $weiss='rgba(255,255,255,1)';
   $rot  ='rgba(183,81,0,1)';
   return array(
      'navtyp'      =>2,
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
   #   Rueckgabe der unsortiereten Zeilen einer Beispielnavigation
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
      15=>array('id'=>44, 'parent_id'=>32, 'name'=>'Onkelkategorie'),
      16=>array('id'=>45, 'parent_id'=>32, 'name'=>'Vaterkategorie'),
      17=>array('id'=>51, 'parent_id'=>45, 'name'=>'Bruderartikel 1'),
      18=>array('id'=>52, 'parent_id'=>45, 'name'=>$aktu),
      19=>array('id'=>53, 'parent_id'=>45, 'name'=>'Bruderartikel 2')
      );
   #
   # --- Level einfuegen
   for($i=1;$i<=count($entries);$i=$i+1):
      $id=$entries[$i][id];
      if($id<20) $entries[$i][level]=1;
      if($id>=20 and $id<30) $entries[$i][level]=2;
      if($id>=30 and $id<40) $entries[$i][level]=3;
      if($id>=40 and $id<50) $entries[$i][level]=4;
      if($id>=50 and $id<60) $entries[$i][level]=5;
      $entries[$i][nr]=$i;
      endfor;
   #
   # --- Entries gemaess Navigationstyp 1 oder 2 ausduennen
   if($navtyp<=2):
     $m=0;
     for($i=1;$i<=count($entries);$i=$i+1):
        $name=$entries[$i][name];
        if(strpos($name,'kelartikel')>0) continue;
        if($navtyp==1 and strpos($name,'kelkategorie')>0) continue;
        $m=$m+1;
        $entneu[$m]=$entries[$i];
        $entneu[$m][nr]=$m;
        endfor;
     $entries=$entneu;
     endif;
   #
   # --- Namen utf8-gemeass konvertieren
   for($i=1;$i<=count($entries);$i=$i+1)
      $entries[$i][name]=utf8_encode($entries[$i][name]);
   #
   # --- (festen) URL und Ausgabetyp einfuegen
   $url=$_SERVER['REQUEST_URI'];
   for($i=1;$i<=count($entries);$i=$i+1):
      $name=$entries[$i][name];
      if($name==$aktu):
        $entries[$i][url]='';
        $entries[$i][typ]=2;
        else:
        $entries[$i][url]=$url;
        $entries[$i][typ]=0;
        endif;
      if($name==$first) $entries[$i][typ]=1;
      endfor;
   #
   return $entries;
   }
}
?>
