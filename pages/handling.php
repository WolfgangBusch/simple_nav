<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version August 2023
 */
#
$addon='simple_nav';
$ind1=rex_config::get($addon,$addon::NAV_INDENT);
$ind2=2*$ind1;
$string='
<div><b>Zur Unterstützung eines responsiven Designs</b></div>
<div class="nav_hand">sollte das Seiten-Template die folgende Zeile
im HTML-Kopfteil enthalten, um zu verhindern, dass Seiteninhalte
im Falle schmaler Bildschirme (Smartphone) automatisch verkleinert
(skaliert) dargestellt werden:<br>
<code>&lt;meta name="viewport" content="width=device-width,
initial-scale=1.0"&gt;</code><br>
Die Javascript-Funktion zum Ein-/Ausschalten der Navigation wird
bei der Installation in den AddOn-Assets-Ordner kopiert.</div>

<div><br><b>Erzeugung einer Navigation:</b></div>
<div class="nav_hand">
Eine Navigation kann für eine beliebige Kategorien eingerichtet werden.
Sie gilt dann für diese (Basis-)Kategorie und alle Unterkategorien.
In das Seiten-Template für die zugehörigen Artikel wird dazu eine
PHP-Anweisung der folgenden Form eingefügt:
<br>
<code>simple_nav::print_navigation($bas_id,$b_line);</code>
<table class="nav_table">
    <tr valign="top">
        <td class="nav_hand"><tt>$bas_id </tt></td>
        <td class="nav_hand">Id des Navigations-Basisartikels
            (Default: leer, es wird der Site-Startartikel angenommen).
            Falls der Artikel kein Kategorie-Startartikels ist, wird
            stattdessen sein Elternartikel genommen.</td></tr>
    <tr valign="top">
        <td class="nav_hand"><tt>$b_line </tt></td>
        <td class="nav_hand">= <tt>TRUE/FALSE</tt> (Default:
            <tt>FALSE</tt>). Die Zeile des Basisartikels selbst wird
            angezeigt bzw. nicht angezeigt. Im Default-Fall wird ein
            Einrückungs-Level für die Zeile des Navigationsbasis-Artikels
            gespart, es fehlt aber evtl. ein statischer Link auf die
            Navigationsbasis-Seite.</td></tr>
</table>
</div>

<div><br><b>Anpassung von Formen, Farben und Trennlinien an das
Site-Design:</b></div>
<div class="nav_hand">
Die Navigationszeilen sind zweifach geschachtelte div-Container.
Der äußere Container bestimmt das Layout der Zeile, der innere legt
die berechnete Einrückung fest. Linktext und ggf. Icon werden in einer
Tabellenzeile darin eingebettet. Die CSS-Klassen für den äußeren
div-Container sowie die Breite der Einrückung pro Level sind konfigurierbar
und werden in der Stylesheet-Datei des AddOns abgelegt.<br>
Jede Zeile erhält einen HTML-Code der folgenden Form (der Einfachheit
halber wird hier ein Pfad-orientierter URL angenommen). In der Zeile der
aktuell angezeigten Seite wird der Link durch ein &lt;span&gt;-tag ersetzt.
<div class="nav_hand">
<div class="nav_box"><tt>
&lt;div class="border format <code>typ0</code>"&gt;<br>
&lt;div style="margin-left:<code>20px</code>;"&gt;<br>
&lt;table class="nav_table"&gt;<br>
 &nbsp; &lt;tr&gt;<br>
 &nbsp; &nbsp; &nbsp;&lt;td&gt;&lt;img src="/assets/addons/simple_nav/icon_file.svg" class="nav_ico"&gt;&lt;/td&gt;<br>
 &nbsp; &nbsp; &nbsp;&lt;td&gt;&lt;a href="/aaaa/<code>BASIS</code>/cccc/ddddd.html"&gt;DDDDD&lt;/a&gt;&lt;/td&gt;<br>
 &nbsp; &lt;/tr&gt;<br>
&lt;/table&gt;<br>
&lt;/div&gt;<br>
&lt;/div&gt;
</tt></div></div>
<table class="nav_table">
    <tr valign="top">
        <td class="nav_hand"><code>'.$addon::DIV_TYPE.'0</code></td>
        <td class="nav_hand">Normaltyp einer Navigationszeile
            (<tt>'.$addon::DIV_TYPE.'2: </tt>aktuelle Seite,
            <tt>'.$addon::DIV_TYPE.'1: </tt>Urahne der aktuellen Seite)</td></tr>
    <tr valign="top">
        <td class="nav_hand"><code>'.$ind2.'px</code></td>
        <td class="nav_hand">Einrückung von je '.$ind1.'
            Pixel pro Kategorie-Level des Artikels <tt>dddd.html</tt></td></tr>
    <tr valign="top">
        <td class="nav_hand"><code>BASIS</code></td>
        <td class="nav_hand">Basiskategorie der Navigation</td></tr>
</table>
';
echo $string;
?>
