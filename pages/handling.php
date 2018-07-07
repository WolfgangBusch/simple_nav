<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Juli 2018
 */
$sty="style=\"padding-left:20px;\"";
$string='
<div><b>Erzeugung einer Navigation:</b></div>
<div style="padding-left:20px;">
Navigationen können für beliebige Kategorien eingerichtet werden und
umfassen alle Artikel und Unterkategorien dieser Basiskategorie.
In das Template für die zugehörigen Artikel wird eine entsprechende
Navigation mit dem Kategorie-Startartikel als Basisartikel eingefügt,
und zwar durch eine solche PHP-Anweisung:<br/>
<code>simple_nav::print_navigation($bas_id,$b_line);</code>
<div style="padding-left:20px;">
<tt>$bas_id</tt> &nbsp; Id des Navigations-Basisartikels
(Default: Site-Startartikel-Id)<br/>
<tt>$b_line</tt> &nbsp; = <tt>TRUE/FALSE</tt>:
Die Basisartikel-Zeile selbst wird angezeigt / nicht angezeigt
(Default: FALSE)</div>
</div>

<div><br/><b>Anpassung von Formen, Farben und Trennlinien an das
Site-Layout:</b></div>
<div style="padding-left:20px;">
Die Navigationszeilen sind zweifach geschachtelte div-Container.
Der äußere Container bestimmt das Layout der Zeile, der innere legt
die berechnete Einrückung des Linktextes fest. Jede Zeile hat einen
HTML-Code der folgenden Form (zur einfacheren Beschreibung wird hier
ein Pfad-orientierter URL angenommen):
<div style="padding-left:20px;">
<pre style="margin:5px; background-color:inherit;">
&lt;div class="'.DIV_CLASS.' '.DIV_TYP.'0"&gt;
    &lt;div style="margin-left:20px;"&gt;
        &lt;a href="/start/aaaa/basis/bbbb/cccc.html"&gt;CCCC&lt;/a&gt;
    &lt;/div&gt;
&lt;/div&gt;
</pre>
<table cellpadding="0" cellspacing="0" style="background-color:inherit;">
    <tr><td '.$sty.'><tt>start</tt></td>
        <td '.$sty.'>Site-Startkategorie</td></tr>
    <tr><td '.$sty.'><tt>basis</tt></td>
        <td '.$sty.'>Basiskategorie der Navigation</td></tr>
    <tr><td '.$sty.'><tt>20px</tt></td>
        <td '.$sty.'>Einrückung des Artikels <tt>cccc.html</tt>
            (hier je 10 Pixel pro Kategorie-Hierarchie)</td></tr>
</table>
Die css-Klassen für den äußeren div-Container sind konfigurierbar
und werden in einer Stylesheet-Datei abgelegt.</div>';
echo utf8_encode($string);
?>
