<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Maerz 2018
 */
#
# --- Beschreibung
$my_package=$this->getPackageId();
$path=rex_path::addon($my_package);
$arr=explode("/",$path);
$n=count($arr)-2;
$addcss="";
for($i=$n-3;$i<=$n;$i=$i+1) $addcss=$addcss."/".$arr[$i];
$addcss=$addcss."/assets/".$my_package.".css";
$ass='/'.basename(rex_path::assets()).'/addons/'.$my_package.
   '/'.$my_package.'.css';
#
$sty="style=\"padding-left:20px;\"";
$string='
<div><b>Erzeugung einer Navigation:</b></div>
<div '.$sty.'>
Navigationen können für beliebige Kategorien eingerichtet werden.
Sie umfassen alle Unterkategorien ihrer Basiskategorie. Angezeigt
werden aber nur Artikel und Kategorien, die online sind.<br/>
Das Template für die zugehörigen Artikel enthält eine Navigation
mit dem Kategorie-Startartikel als Basisartikel der Navigation.<br/>
Eine Navigation wird erzeugt durch: &nbsp;
<code>echo simple_nav($art_id,$bas_id,$b_line);</code>
<div '.$sty.'>
<table>
    <tr valign="top">
        <td><tt>$art_id</tt></td>
        <td '.$sty.'>Id des aktuellen Artikels</td></tr>
    <tr valign="top">
        <td><tt>$bas_id</tt></td>
        <td '.$sty.'>Id des Navigations-Basisartikels
            (Default: Site-Startartikel-Id)</td></tr>
    <tr valign="top">
        <td><tt>$b_line</tt></td>
        <td '.$sty.'>= <tt>TRUE/FALSE</tt>:
            Die Basisartikel-Zeile wird angezeigt / nicht angezeigt
            (Default: FALSE)</td></tr>
</table>
</div>
</div>
<br/>
<div><b>Anpassung von Formen, Farben und Trennlinien an das
Site-Layout:</b></div>
<div '.$sty.'>
Die Navigationszeilen sind zweifach geschachtelte div-Container.
Der äußere Container bestimmt das Layout der Zeile, der innere legt
die berechnete Einrückung des Linktextes fest. Jede Zeile hat einen
HTML-Code der folgenden Form:
<div '.$sty.'>
<pre style="margin:5px;">
&lt;div class="'.DIV_CLASS.' '.DIV_TYP.'0"&gt;
    &lt;div style="margin-left:20px;"&gt;
        &lt;a href="/aaaa/bbbb/cccc.html"&gt;CCCC&lt;/a&gt;
    &lt;/div&gt;
&lt;/div&gt;
</pre>
</div>
Die css-Klassen für den äußeren div-Container sind konfigurierbar
und werden als Stylesheet-Datei abgelegt:
<div '.$sty.'>
<code>'.$addcss.'</code> &nbsp; und damit auch in<br/>
<code>'.$ass.'</code>
</div>
</div>';
echo utf8_encode($string);
?>
