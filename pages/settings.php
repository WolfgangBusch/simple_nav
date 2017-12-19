<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Dezember 2017
 */
function simple_nav_input($daten,$keys,$n,$sty,$width) {
   return '<td '.$sty.'><input style="width:'.$width.'px;" type="text" name="'.
      $keys[$n].'" value="'.$daten[$keys[$n]].'" /></td>';
   }
function simple_nav_select($val,$daten,$keys,$n,$sty,$width) {
   #   Select-Menue fuer einen Eingabewert
   $string='<select name="'.$keys[$n].'" style="width:'.$width.'px;">';
   for($i=0;$i<count($val);$i=$i+1):
      if($val[$i]==$daten[$keys[$n]]):
        $string=$string.'<option selected="selected">'.$val[$i].'</option>';
        else:
        $string=$string.'<option>'.$val[$i].'</option>';
        endif;
      endfor;
   return '<td '.$sty.'>'.$string.'</select></td>';
   }
function simple_nav_good_contraste($col,$bgcol) {
   #   Pruefung, ob zwei gegebene RGBA-Farben genuegend Kontrast haben
   $s=35;
   $t=0.8;
   $a=explode(',',substr($col,  5,strlen($col)-6));
   $b=explode(',',substr($bgcol,5,strlen($bgcol)-6));
   if(abs($a[0]-$b[0])<$s and abs($a[1]-$b[1])<$s and abs($a[2]-$b[2])<$s and $b[3]>$t):
     return FALSE;
     else:
     return TRUE;
     endif;
   }
function simple_nav_col_contraste($col) {
   #   Rueckgabe einer Kontrastfarbe als Hintergrund zu einer gegebenen Farbe.
   #   Wenn weiss nicht genuegend Kontrast hat, wird eine Komplementaerfarbe berechnet.
   #   $col              gegebene Farbe "rgba(r,g,b,Deckung)"
   #   benutzte functions:
   #      simple_nav_good_contraste($col,$bgcol)
   #
   $white="rgba(255,255,255,1)";
   if(simple_nav_good_contraste($col,$white)) return $white;
   $tcol=trim($col);
   $e=substr($tcol,5,strlen($tcol)-6);
   $arr=explode(",",$e);
   return "rgba(".strval(255-$arr[0]).",".strval(255-$arr[1]).",".
      strval(255-$arr[2]).",1)";
   }
function simple_nav_print_input($daten,$keys,$na,$txt,$erg,$narr,$stx,$sty,$width) {
   #   Input-Menue fuer einen Eingabewert
   $off=count(simple_nav_define_al())+count(simple_nav_define_bl());
   $ns=4*$na+$off;
   $nt=  $na+$off;
   #
   # --- Text-, Rand-, Hintergrundfarbe
   $cl=simple_nav_define_cl();
   $nam=array('col','bor','bgcol');
   for($i=0;$i<=2;$i=$i+1):
      if($i==2 and ($na==1 or $na==3 or $na==5)):
        #  Hintergrundfarbe $val mit genuegend Kontrast zur Randfarbe
        $val=simple_nav_col_contraste($col);
        else:
        $k=$narr[$i];
        $val=simple_nav_set_styles($daten,$cl[$k]);
        endif;
      $line='$'.$nam[$i].'="'.$val.'";';
      eval($line);
      endfor;
   #
   # --- Ausgabe
   $str='
    <tr><td '.$stx.'>'.$txt[$nt].'</td>';
   for($i=$ns;$i<=$ns+3;$i=$i+1)
      $str=$str.'
        <td '.$sty.'><input style="width:'.$width.'px;" type="text" name="'.$keys[$i].
        '" value="'.$daten[$keys[$i]].'" /></td>';
   #
   if($na==0 or $na==1 or $na==3 or $na==5 or $na==7):
     # --- letzte Spalte (Linktext/Raender)
     $iwd=70;
     return $str.'
        <td '.$stx.' align="right" width="'.$daten[$keys[2]].'">'.
           '<span style="width:'.$iwd.'px; '.
           'background-color:'.$bgcol.'; color:'.$col.';" />'.
           '&nbsp;&nbsp;'.$erg[$nt].'&nbsp;&nbsp;</span></td></tr>';
     else:
     # --- letzte Spalte (Navigationszeile)
     #     ggf. Hinweis auf fehlenden Kontrast Hintergrundfarbe - Textfarbe
     if(!simple_nav_good_contraste($col,$bgcol))
       echo rex_view::warning(utf8_encode($txt.
          ' &nbsp; <code>zu geringer Kontrast zur Farbe der Linktexte<code>'));
     $iwd=$daten[$keys[2]];
     return $str.'
        <td '.$stx.' align="center" width="'.$daten[$keys[2]].'">
            <input type="text" value=" '.$erg[$nt].' "
                   style="width:'.$iwd.'px; line-height:'.$daten[$keys[3]].'em; font-size:'.$daten[$keys[4]].'em;
                          border-radius:'.$daten[$keys[7]].'em;
                          border-left:  solid '.$daten[$keys[5]].'px '.$bor.';
                          border-right: solid '.$daten[$keys[5]].'px '.$bor.';
                          border-top:   solid '.$daten[$keys[6]].'px '.$bor.';
                          border-bottom:solid '.$daten[$keys[6]].'px '.$bor.';
                          background-color:'.$bgcol.'; color:'.$col.';" /></td></tr>';
     endif;
   }
$defdat=simple_nav_default_data();
$keys=array_keys($defdat);
#
# --- Menütexte, Konstanten
$txt=array(
   'Navigationstyp (=1/2/3)',
   'Einrückung pro Navigations-Level',
   'Navigationszeilen: Breite',
   'Navigationszeilen: Höhenfaktor',
   'Linktexte: Größenfaktor (&le;Höhenfaktor)',
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
$t1='Linktext';
$t2='<b>&mdash;&mdash;&mdash;&mdash;</b>';
$erg=array('','px','px','em','em','px','px','em',
   $t1,$t2,$t1,$t2,$t1,$t2,"Artikelname","Text");
$stx='style="padding-left:20px; padding-right:5px; white-space:nowrap;"';
$sty='style="padding-left:10px; white-space:nowrap;"';
$stz='style="padding-left:10px; text-align:center;"';
$width=40;
#
# --- Auslesen der gesetzen Konfiguration
for($i=0;$i<count($keys);$i=$i+1) $confdat[$keys[$i]]=rex_config::get(NAVIGATION,$keys[$i]);
#
# --- eingegebene Parameter auslesen
$wa='&nbsp; [falscher Wert, korrigiert]';
$warn='';
for($i=0;$i<count($keys);$i=$i+1):
   $ke=$keys[$i];
   $post=$_POST["$ke"];
   if($post=='0') $post=-1;
   if(empty($post)) $post=$confdat[$ke];
   if($post<0) $post=0;
   # --- Eingabedaten gemaess Beschraenkungen korrigieren
   if($i==4 and $post>$daten[$keys[3]]):
     $warn=utf8_encode($txt[$i].': <code>'.$post.'</code> &nbsp; '.$wa);
     $post=$daten[$keys[3]];
     endif;
   # --- umspeichern
   $daten[$ke]=$post;
   endfor;
if(!empty($warn)) echo rex_view::warning($warn);
#
# --- speichern oder zuruecksetzen und speichern (Daten + Stylesheet-Datei)
$sendit=$_POST['sendit'];
$reset=$_POST['reset'];
if(!empty($reset))
  for($i=0;$i<count($keys);$i=$i+1) $daten[$keys[$i]]=$defdat[$keys[$i]];
if(!empty($sendit) or !empty($reset)):
  for($i=0;$i<count($keys);$i=$i+1)
     rex_config::set(NAVIGATION,$keys[$i],$daten[$keys[$i]]);
  simple_nav_write_css($daten);
  endif;
#
# --- Formularanfang
echo utf8_encode('
<form method="post">
<table>');
#
# --- Navigationstyp, Einrueckung, Groessen-Parameter
$count=count(simple_nav_define_al())+count(simple_nav_define_bl());
$string='
    <tr><td colspan="6"><b>allgemeine Parameter:</b></td></tr>';
for($i=0;$i<$count;$i=$i+1):
   if($i==0 or $i==5 or $i==6):
     $arr=array(0,1,2);
     if($i==0) $arr=array(1,2,3);
     $str=simple_nav_select($arr,$daten,$keys,$i,$sty,$width);
     else:
     $str=simple_nav_input($daten,$keys,$i,$sty,$width);
     endif;
   $s='';
   if($i==intval($count-1)) $s='Anzeige';
   $string=$string.'
    <tr><td '.$stx.'>'.$txt[$i].'</td>
        '.$str.'
        <td '.$sty.'>'.$erg[$i].'</td>
        <td></td><td></td><td '.$stz.'>'.$s.'</td></tr>';
   if($i==1) $string=$string.'
    <tr><td colspan="6"><b>Stylesheet-Parameter:</b></td></tr>';
   endfor;
echo utf8_encode($string);
#
# --- Ueberschrift Farben
echo utf8_encode('
    <tr><td align="right">Farben + Deckungsgrad (RGBA-Werte):</td>
        <td '.$stz.'>rot</td>
        <td '.$stz.'>grün</td>
        <td '.$stz.'>blau</td>
        <td '.$stz.'>Deck.</td>
        <td '.$stz.'>(vergl. auch Beispiel)</td></tr>');
#
# --- Farben: array(Text,Rand,Hintergrund)
$narr=array(array(0,2, 2), array( 1,2,2), array( 0, 1, 2), array( 3,2, 2),
            array(0,3, 4), array( 5,2,2), array( 7, 5, 6), array( 7,2, 6));
$string='';
for($i=0;$i<count(simple_nav_define_cl());$i=$i+1)
   $string=$string.simple_nav_print_input($daten,$keys,$i,$txt,$erg,$narr[$i],$stx,$sty,$width);
echo utf8_encode($string);
#
# --- Submit-Button und Formular-Abschluss
$sptit='Parameter und css-Stylesheet speichern';
$str='auf Defaultwerte zurücksetzen und ';
$title=$str.'speichern';
$retit='Parameter '.$str."\n".$sptit;
echo utf8_encode('
    <tr><td '.$stx.'><br/>
            <button class="btn btn-save" type="submit" name="sendit" value="sendit" title="'.$sptit.'"> speichern </button></td>
        <td '.$sty.' colspan="5"><br/>
            <button class="btn btn-update" type="submit" name="reset" value="reset" title="'.$retit.'">'.$title.'</button></td></tr>
</table>
</form>
<br/>
');
?>
