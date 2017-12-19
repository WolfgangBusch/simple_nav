<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Dezember 2017
 */
#
# --- Definition der Konstanten
define("NAVIGATION", $this->getPackageId()); // Package-Id
function simple_nav_define_al() {
   return array("nav_typ","levelwidth");
   }
function simple_nav_define_bl() {
   return array("navwidth","lineheight","fontsize","brlwidth","bruwidth","borrad");
   }
function simple_nav_define_cl() {
   return array("navlinkcol","navbor0","navcol0","navbor1","navcol1","navbor2","navcol2","navtxt2");
   }
#
# --- Setzen der Default-Konfiguration der Stylesheet-Daten
$defdata=simple_nav_default_data();
$keys=array_keys($defdata);
$first=TRUE;
for($i=0;$i<count($keys);$i=$i+1)
   if(!empty(rex_config::get(NAVIGATION,$keys[$i]))) $first=FALSE;
#     direkt nach der Installation:
if($first)
  for($i=0;$i<count($keys);$i=$i+1)
     rex_config::set(NAVIGATION,$keys[$i],$defdata[$keys[$i]]);
#
# -----------------------------------------------
#   simple_nav_write_css($data)
#      simple_nav_default_data()
#      simple_nav_define_css($data,$zus)
#         simple_nav_set_styles($data,$mc)
# -----------------------------------------------
#   simple_nav_example()
#      simple_nav_default_data()
#      simple_nav_define_css($data,$zus)
#         simple_nav_set_styles($data,$mc)
#      simple_nav_example_data()
#      simple_nav_example_entries($navtyp)
#         simple_nav_sort($entries)
#      simple_nav_print_line($entries,$incr,$increment)
# -----------------------------------------------
#
function simple_nav_write_css($data) {
   #   Schreiben der Stylesheets fuer die Navigation
   #   $data             Array der Daten, aus denen die Stylesheet-
   #                     Klassenbezeichnungen zusammengestellt werden
   #   benutzte functions:
   #      simple_nav_default_data()
   #      simple_nav_define_css($data,$zus)
   #
   # --- fuer den allerersten Aufruf bei der Installation
   if(count($data)<2) $data=simple_nav_default_data();
   #
   # --- Stylesheet erzeugen
   $buffer=simple_nav_define_css($data,"");
   #
   # --- Schreiben der Stylesheet-Datei
   $file=rex_path::assets()."addons/simple_nav/simple_nav.css";
   $handle=fopen($file,"w");
   fwrite($handle,$buffer);
   fclose($handle);
   }
function simple_nav_default_data() {
   #   Rueckgabe der Default-Werte der simple_nav-Stylesheet-Daten
   #   inkl. zugehoerige Array-Keys
   #   benutzte functions:
   #      simple_nav_define_al()
   #      simple_nav_define_bl()
   #
   $al=simple_nav_define_al();
   $bl=simple_nav_define_bl();
   $defdata=array(
      $al[0]=>2,    $al[1]=>10,
      $bl[0]=>150,  $bl[1]=>"0.8", $bl[2]=>"0.8",
      $bl[3]=>"0",  $bl[4]=>"1",   $bl[5]=>"0",
      "rlink"=>153, "glink"=>51,   "blink"=>0,  "alink"=>"1",
      "r0r"=>255,   "g0r"=>190,    "b0r"=>60,   "a0r"=>"1",
      "r0bg"=>255,  "g0bg"=>255,   "b0bg"=>255, "a0bg"=>"0",
      "r1r" =>255,  "g1r" =>190,   "b1r"=>60,   "a1r"=>"1",
      "r1bg" =>255, "g1bg" =>190,  "b1bg"=>60,  "a1bg"=>"0.3",
      "r2r" =>255,  "g2r" =>190,   "b2r"=>60,   "a2r"=>"1",
      "r2bg" =>204, "g2bg" =>102,  "b2bg"=>51,  "a2bg"=>"1",
      "r2t" =>255,  "g2t" =>255,   "b2t"=>255,  "a2t"=>"1");
   return $defdata;
   }
function simple_nav_define_css($data,$zus="") {
   #   Rueckgabe eines Strings mit dem Inhalt der Stylesheets
   #   fuer die Navigation
   #   $data             Array der Daten, aus denen die Stylesheet-
   #                     Klassenbezeichnungen zusammengestellt werden
   #   $zus              Zusatzstring zu den simple_nav-Klassennamen fuer die
   #                     div-Container (nur fuer die Beispiele benoetigt)
   #   benutzte functions:
   #      simple_nav_define_bl()
   #      simple_nav_define_cl()
   #      simple_nav_set_styles($data,$cl[$i])
   #
   # --- Setzen der fuer die Styles benutzte Klassenbezeichnungen:
   $bl=simple_nav_define_bl();
   for($i=0;$i<count($bl);$i=$i+1):
      $styval=simple_nav_set_styles($data,$bl[$i]);
      $line="\$".$bl[$i]."=\"$styval\";";
      eval($line);
      endfor;
   $cl=simple_nav_define_cl();
   for($i=0;$i<count($cl);$i=$i+1):
      $styval=simple_nav_set_styles($data,$cl[$i]);
      $line="\$".$cl[$i]."=\"$styval\";";
      eval($line);
      endfor;
   #
   # --- Klassennamen der div-Container
   $nav=DIV_CLASS.$zus;
   $nav1=DIV_CLASS1.$zus;
   $typ0=DIV_TYP."0".$zus;
   $typ1=DIV_TYP."1".$zus;
   $typ2=DIV_TYP."2".$zus;
   #
   # --- String der Stylesheet-Anweisungen erzeugen
   $buffer=
'/*   s i m p n a v - N a v i g a t i o n   */
div.'.$nav.' {
   padding:3px; width:'.$navwidth.'px; line-height:'.$lineheight.'em;
   border-bottom:solid '.$bruwidth.'px '.$navbor0.';
   border-top:   solid '.$brlwidth.'px '.$navbor0.';
   border-left:  solid '.$brlwidth.'px '.$navbor0.';
   border-right: solid '.$brlwidth.'px '.$navbor0.';
   border-radius:'.$borrad.'em; }
div.'.$nav1.' {
   padding:3px; width:'.$navwidth.'px; line-height:'.$lineheight.'em;
   border-bottom:solid '.$bruwidth.'px '.$navbor0.';
   border-top:   solid '.$bruwidth.'px '.$navbor0.';
   border-left:  solid '.$brlwidth.'px '.$navbor0.';
   border-right: solid '.$brlwidth.'px '.$navbor0.';
   border-radius:'.$borrad.'em; }
div.'.$typ0.' {
   background-color:'.$navcol0.';
   border-color:'.$navbor0.'; }
div.'.$typ1.' {
   background-color:'.$navcol1.';
   border-color:'.$navbor1.'; }
div.'.$typ2.' {
   background-color:'.$navcol2.';
   border-color:'.$navbor2.'; }
div.'.$typ0.' div a, div.'.$typ1.' div a {
   font-size:'.$fontsize.'em;
   color:'.$navlinkcol.'; }
div.'.$typ2.' div {
   font-size:'.$fontsize.'em;
   color:'.$navtxt2.'; }';
   #
   # --- und zurueckgeben
   return $buffer;
   }
function simple_nav_set_styles($data,$mc) {
   #   Rueckgabe der fuer die Styles benutzte Varablenwerte
   #   $data             Array der Daten, aus denen die Stylesheet-
   #                     Klassenbezeichnungen zusammengestellt werden
   #   $mc               Name der Variablen
   #   benutzte functions:
   #      simple_nav_define_al()
   #      simple_nav_define_bl()
   #      simple_nav_define_cl()
   #
   $al=simple_nav_define_al();
   $bl=simple_nav_define_bl();
   $cl=simple_nav_define_cl();
   $offs=count($al);
   $cbl=count($bl);
   $offt=$cbl+$offs;
   $ke=array_keys($data);
   for($i=0;$i<$cbl;$i=$i+1)
      if($mc==$bl[$i]) return $data[$ke[$i+$offs]];
   for($i=0;$i<count($cl);$i=$i+1):
      $k=4*$i+$offt;
      if($mc==$cl[$i])
        return "rgba(".$data[$ke[$k]].",".$data[$ke[$k+1]].",".$data[$ke[$k+2]].",".
               $data[$ke[$k+3]].")";
      endfor;
   }
function simple_nav_example() {
   #   Rueckgabe des HTML-Codes zweier Beispiel-Navigationen
   #   gemaess konfigurierten bzw. modifizierten Daten/Stylesheet
   #   unter Beruecksichtigung des konfigurierten Navigationstyps
   #   benutzte functions:
   #      simple_nav_default_data()
   #      simple_nav_define_css($data,$zus)
   #      simple_nav_example_data()
   #      simple_nav_example_entries($navtyp)
   #      simple_nav_print_line($entries,$incr,$increment)
   #
   # --- 1) konfigurierte Stylesheet-Daten
   $data=simple_nav_default_data();
   $keys=array_keys($data);
   for($i=0;$i<count($keys);$i=$i+1) $data[$keys[$i]]=rex_config::get(NAVIGATION,$keys[$i]);
   $navtyp=$data[$keys[0]];
   $incr1=rex_config::get(NAVIGATION,$keys[1]);
   $styles1=simple_nav_define_css($data,$incr1);
   $bgc="rgba($data[r0bg],$data[g0bg],$data[b0bg],$data[a0bg])";
   $col="rgba($data[rlink],$data[glink],$data[blink],$data[alink])";
   $bor="rgba($data[r0r],$data[g0r],$data[b0r],$data[a0r])";
   $col="border:solid 1px $bor; background-color:$bgc; color:$col;";
   #
   # --- 2) konfigurierte Stylesheet-Daten (Navigationstyp wird uebernommen)
   $xmpdata=simple_nav_example_data();
   $xmpdata[$key[0]]=$navtyp;
   $zus="xmp";
   $incr2=$xmpdata[$keys[1]];
   $styles2=simple_nav_define_css($xmpdata,$zus);
   $xbgc="rgba($xmpdata[r0bg],$xmpdata[g0bg],$xmpdata[b0bg],$xmpdata[a0bg])";
   $xcol="rgba($xmpdata[rlink],$xmpdata[glink],$xmpdata[blink],$xmpdata[alink])";
   $xbor="rgba($xmpdata[r0r],$xmpdata[g0r],$xmpdata[b0r],$xmpdata[a0r])";
   $xcol="border:solid 1px $xbgc; background-color:$xbor; color:$xcol;";
   #
   # --- Entries (Navigationszeilen)
   $entries=simple_nav_example_entries($navtyp);
   #
   # --- Darstellung beider Navigationen nebeneinander
   return "<style>\n".$styles1."\n"."</style>\n".
      "<style>\n".$styles2."\n"."</style>\n".
      "<div align=\"center\">".
      "<table>\n".
      "    <tr valign=\"top\">\n".
      "        <td style=\"padding:10px; white-space:nowrap;\">\n".
      "            <b>Navigation (Typ $navtyp), Darstellung mit ...</b><br/>\n".
      "            &nbsp; &nbsp; (ohne Navigations-Basisartikel)<br/>\n".
      "            <table>\n".
      "                <tr><td>Typ 1: &nbsp; </td><td>ohne &nbsp; </td><td>...onkelkategorien/-artikel</td></tr>\n".
      "                <tr><td>Typ 2: &nbsp; </td><td>mit &nbsp; </td><td>...onkelkategorien</td></tr>\n".
      "                <tr><td>Typ 3: &nbsp; </td><td>mit &nbsp; </td><td>...onkelkategorien und -artikeln</td></tr>\n".
      "            </table>\n".
      "        <td style=\"padding:10px; $col\">\n".
      "            <div align=\"center\"><b>... konfigurierten Daten/Styles</b></div><br/>\n".
      simple_nav_print_line($entries,$incr1,$incr1).
      "        </td>\n".
      "        <td style=\"padding:10px; $xcol\">\n".
      "            <div align=\"center\"><b>... modifizierten Daten/Styles</b></div><br/>\n".
      simple_nav_print_line($entries,$zus,$incr2).
      "        </td></tr>\n".
      "</table>\n".
      "</div>\n";
   }
function simple_nav_example_data() {
   #   Rueckgabe der Beispiel-Werte der simple_nav-Stylesheet-Daten
   #   inkl. zugehoerige Array-Keys
   #   benutzte functions:
   #      simple_nav_define_al()
   #      simple_nav_define_bl()
   #
   $b=array(72,120,160,1);  // blau
   $al=simple_nav_define_al();
   $bl=simple_nav_define_bl();
   $defdata=array(
      $al[0]=>2,     $al[1]=>20,
      $bl[0]=>220,   $bl[1]=>"1.5", $bl[2]=>"1.2",
      $bl[3]=>"1",   $bl[4]=>"1",   $bl[5]=>"0.5",
      "rlink"=>255,  "glink"=>255,  "blink"=>255,  "alink"=>"1",    // weiss
      "r0r" =>$b[0], "g0r" =>$b[1], "b0r" =>$b[2], "a0r" =>$b[3], // blau
      "r0bg"=>$b[0], "g0bg"=>$b[1], "b0bg"=>$b[2], "a0bg"=>$b[3], // blau
      "r1r" =>$b[0], "g1r" =>$b[1], "b1r" =>$b[2], "a1r" =>$b[3], // blau
      "r1bg"=>$b[0], "g1bg"=>$b[1], "b1bg"=>$b[2], "a1bg"=>$b[3], // blau
      "r2r" =>$b[0], "g2r" =>$b[1], "b2r" =>$b[2], "a2r" =>$b[3], // blau
      "r2bg"=>183,   "g2bg"=>81,    "b2bg"=>0,     "a2bg"=>"1",     // rot
      "r2t" =>255,   "g2t" =>255,   "b2t"=>255,    "a2t"=>"1");     // weiss
   return $defdata;
   }
function simple_nav_example_entries($navtyp) {
   #   Rueckgabe der Zeilen einer Beispielnavigation unter
   #   Beruecksichtigung des konfigurierten Navigationstyps
   #   $navtyp           konfigurierter Navigationstyp
   #                     Klassenbezeichnungen zusammengestellt werden
   #   benutzte functions:
   #      simple_nav_sort($entries)
   #
   # --- Definition der Texte des Beispiels
   $aktu="aktueller Artikel";
   $first="Hauptkategorie Urahne";
   $entries=array(
       1=>array("id"=> 5, "re_id"=> 1, "name"=>"Hauptseite 1"),
       2=>array("id"=> 6, "re_id"=> 1, "name"=>"Hauptseite 2"),
       3=>array("id"=>11, "re_id"=> 1, "name"=>"Hauptkategorie 1"),
       4=>array("id"=>12, "re_id"=> 1, "name"=>$first),
       5=>array("id"=>13, "re_id"=> 1, "name"=>"Hauptkategorie 3"),
       6=>array("id"=>21, "re_id"=>12, "name"=>"Urgroßonkelkategorie 1"),
       7=>array("id"=>22, "re_id"=>12, "name"=>"Urgroßvaterkategorie"),
       8=>array("id"=>23, "re_id"=>12, "name"=>"Urgroßonkelkategorie 2"),
       9=>array("id"=>31, "re_id"=>22, "name"=>"Großonkelartikel"),
      10=>array("id"=>32, "re_id"=>22, "name"=>"Großvaterkategorie"),
      11=>array("id"=>33, "re_id"=>22, "name"=>"Großonkelkategorie"),
      12=>array("id"=>41, "re_id"=>32, "name"=>"Onkelartikel 1"),
      13=>array("id"=>42, "re_id"=>32, "name"=>"Onkelartikel 2"),
      14=>array("id"=>43, "re_id"=>32, "name"=>"Onkelartikel 3"),
      15=>array("id"=>44, "re_id"=>32, "name"=>"Onkelkategorie"),
      16=>array("id"=>45, "re_id"=>32, "name"=>"Vaterkategorie"),
      17=>array("id"=>51, "re_id"=>45, "name"=>"Bruderartikel 1"),
      18=>array("id"=>52, "re_id"=>45, "name"=>$aktu),
      19=>array("id"=>53, "re_id"=>45, "name"=>"Bruderartikel 2")
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
        if(strpos($name,"kelartikel")>0) continue;
        if($navtyp==1 and strpos($name,"kelkategorie")>0) continue;
        $m=$m+1;
        $entneu[$m]=$entries[$i];
        $entneu[$m][nr]=$m;
        endfor;
     $entries=$entneu;
     endif;
   #
   # --- Sortieren
   simple_nav_sort($entries);
   #
   # --- Namen utf8-gemeass konvertieren
   for($i=1;$i<=count($entries);$i=$i+1)
      $entries[$i][name]=utf8_encode($entries[$i][name]);
   #
   # --- (festen) URL und Ausgabetyp einfuegen
   $url=$_SERVER["REQUEST_URI"];
   for($i=1;$i<=count($entries);$i=$i+1):
      $name=$entries[$i][name];
      if($name==$aktu):
        $entries[$i][url]="";
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
?>
