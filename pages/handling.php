<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version März 2020
 */
$sty="style=\"padding-left:20px;\"";
$string='
<div><b>Erzeugung einer Navigation:</b></div>
<div style="padding-left:20px;">
Navigationen können für beliebige Kategorien eingerichtet werden.
In das Template für die zugehörigen Artikel wird eine entsprechende
Navigation mit dem Kategorie-Startartikel als Basisartikel eingefügt,
und zwar durch eine solche PHP-Anweisung:<br/>
<code>simple_nav::print_navigation($bas_id,$b_line);</code>
<div '.$sty.'"><tt>$bas_id </tt> &nbsp; Id des
Navigations-Basisartikels (Default: Site-Startartikel-Id)<br/>
<tt>$b_line </tt> &nbsp; = <tt>TRUE/FALSE</tt>:
Die Basisartikel-Zeile selbst wird angezeigt / nicht angezeigt
(Default: FALSE)</div>
</div>

<div><br/><b>Anpassung von Formen, Farben und Trennlinien an das
Site-Layout:</b></div>
<div '.$sty.'">
Die Navigationszeilen sind zweifach geschachtelte div-Container.
Der äußere Container bestimmt das Layout der Zeile, der innere legt
die berechnete Einrückung des Linktextes fest. Jede Zeile hat einen
HTML-Code der folgenden Form (der Einfachheit halber wird hier ein
Pfad-orientierter URL angenommen):
<div '.$sty.'">
<pre style="margin:5px; background-color:inherit;">
&lt;div class="'.DIV_CLASS.' '.DIV_TYP.'0"&gt;
    &lt;div style="margin-left:20px;"&gt;
        &lt;a href="/start/aaaa/basis/bbbb/cccc.html"&gt;CCCC&lt;/a&gt;
    &lt;/div&gt;
&lt;/div&gt;
</pre>
<div '.$sty.'><tt>typ0&nbsp;</tt> &nbsp; Normaltyp einer
Navigationszeile (<tt>typ2: </tt>aktuelle Seite,
<tt> typ1: </tt>Urahne der aktuellen Seite)</div>
<div '.$sty.'><tt>20px&nbsp;</tt> &nbsp; Einrückung von je 10 Pixel
pro Kategorie-Hierarchie des Artikels <tt>cccc.html</tt></div>
<div '.$sty.'><tt>start</tt> &nbsp; Site-Startkategorie</div>
<div '.$sty.'><tt>basis</tt> &nbsp; Basiskategorie der Navigation</div>
Die css-Klassen für den äußeren div-Container sind konfigurierbar
und werden in einer Stylesheet-Datei abgelegt. Auch die Breite der
Einrückung (oben: 10 Pixel pro Level) kann konfiguriert werden.</div>';
echo $string;
?>
