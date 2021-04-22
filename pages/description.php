<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version April 2021
 */
#
# --- Beschreibung
$string='
<div>Die Navigationszeilen zeigen den Artikelnamen an. Sie
werden nötigenfalls umgebrochen und im Falle besonders langer
Wörter auch abgeschnitten.</div>

<div><br/>Navigationen können für eine oder mehrere Kategorien
einer Website eingerichtet werden und umfassen alle Artikel und
Unterkategorien der gewählten (Basis-)Kategorie. Offline-Artikel
sind dabei grundsätzlich &quot;verborgen&quot;, ebenso
Offline-Kategorien samt allen ihren Artikeln.</div>

<div><br/>Die Navigation ist beschränkt auf einen
&quot;Sprachraum&quot;, definiert durch die Sprachversion des
aktuell angezeigten Artikels. Alle Links in den Zeilen führen
grundsätzlich nur zu Seiten innerhalb dieses Sprachraums.</div>

<div><br/>Die Anzahl der angezeigten Navigationszeilen kann in
drei Stufen variiert werden:</div>

<div>Minimalkonfiguration (Typ 1):</div>
<ul class="nav_narr">
    <li>alle Kindartikel des Basisartikels (= Startartikel der
        Basiskategorie)</li>
    <li>alle Kindkategorien des Basisartikels</li>
    <li>alle Kategorien im Pfad des aktuellen Artikels</li>
    <li>der aktuelle Artikel</li>
    <li>seine Geschwisterartikel und -kategorien
        (bei online-Elternkategorie)</li>
    <li>seine Kindartikel (soweit vorhanden)</li>
</ul>

<div>Normalkonfiguration (Typ 2):</div>
<ul class="nav_narr">
    <li>Minimalkonfiguration und zusätzlich:</li>
    <li>alle Geschwisterkategorien im Pfad des aktuellen Artikels</li>
</ul>

<div>Maximalkonfiguration (Typ 3):</div>
<ul class="nav_narr">
    <li>Normalkonfiguration und zusätzlich:</li>
    <li>alle Geschwisterartikel im Pfad des aktuellen Artikels</li>
</ul>
';
echo $string;
?>
