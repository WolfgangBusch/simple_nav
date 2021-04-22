<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version April 2021
 */
#
$string='
<div><b>Zur Unterstützung eines responsiven Designs</b></div>
<div class="nav_hand">sollte das Seiten-Template die folgende Zeile
im HTML-Kopfteil enthalten, um zu verhindern, dass Seiteninhalte
im Falle schmaler Bildschirme (Smartphone) automatisch verkleinert
(skaliert) dargestellt werden:<br/><code>
&lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
</code></div>

<div><br/><b>Erzeugung einer Navigation:</b></div>
<div class="nav_hand">
Navigationen können für beliebige Kategorien eingerichtet werden.
In das Seiten-Template für die zugehörigen Artikel wird eine
entsprechende Navigation mit dem Kategorie-Startartikel als
Basisartikel eingefügt, und zwar durch eine solche PHP-Anweisung:
<br/>
<code>simple_nav::print_navigation($bas_id,$b_line);</code>
<table class="nav_table">
    <tr valign="top">
        <td class="nav_hand"><tt>$bas_id </tt></td>
        <td class="nav_hand">Id des Navigations-Basisartikels
            (falls leer, wird die Site-Startartikel-Id angenommen;<br/>
            falls der Artikel kein Startartikel ist, wird
            stattdessen sein Elternartikel genommen)</td></tr>
    <tr valign="top">
        <td class="nav_hand"><tt>$b_line </tt></td>
        <td class="nav_hand">= <tt>TRUE/FALSE</tt>: Die
            Basisartikel-Zeile selbst wird angezeigt / nicht
            angezeigt (Default: FALSE)</td></tr>
</table>
</div>

<div><br/><b>Anpassung von Formen, Farben und Trennlinien an das
Site-Design:</b></div>
<div class="nav_hand">
Die Navigationszeilen sind zweifach geschachtelte div-Container.
Der äußere Container bestimmt das Layout der Zeile, der innere legt
die berechnete Einrückung des Linktextes fest. Die CSS-Klassen für
den äußeren div-Container sowie die Breite der Einrückung pro Level
sind konfigurierbar und werden in einer Stylesheet-Datei abgelegt.<br/>
Jede Zeile hat einen HTML-Code der folgenden Form (der Einfachheit
halber wird hier ein Pfad-orientierter URL angenommen, bei der Zeile
der aktuell angezeigten Seite entfällt der Link):
<div class="nav_hand">
<div class="nav_box">
&lt;div class="'.DIV_BORDER.' '.DIV_FORMAT.' '.DIV_TYP.'0"&gt;
<div class="nav_hand">&lt;div style="margin-left:20px;"&gt;
<div class="nav_hand">&lt;a href="/aaaa/BASIS/cccc/dddd.html"&gt;DDDD&lt;/a&gt;</div>
&lt;/div&gt;</div>
&lt;/div&gt;</div>
</div>
<table class="nav_table">
    <tr valign="top">
        <td class="nav_hand"><tt>typ0&nbsp;</tt></td>
        <td class="nav_hand">Normaltyp einer Navigationszeile
            (<tt>typ2: </tt>aktuelle Seite,
            <tt> typ1: </tt>Urahne der aktuellen Seite)</td></tr>
    <tr valign="top">
        <td class="nav_hand"><tt>20px&nbsp;</tt></td>
        <td class="nav_hand">Einrückung von je 10 Pixel pro
            Kategorie-Level des Artikels <tt>dddd.html</tt></td></tr>
    <tr valign="top">
        <td class="nav_hand"><tt>BASIS</tt></td>
        <td class="nav_hand">Basiskategorie der Navigation</td></tr>
</table>
</div>
';
echo $string;
?>
