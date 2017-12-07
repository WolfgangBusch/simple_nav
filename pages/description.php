<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Dezember 2017
 */
#
# --- Beschreibung
$string='
<div>Die Anzahl der Navigationszeilen kann in drei Stufen variiert
werden:</div>

<div><br/><b>Minimalkonfiguration (Typ 1):</b></div>
<ul style="padding-left:30px; margin-bottom:0px;">
    <li>alle Kindartikel des Basisartikels</li>
    <li>alle Kindkategorien des Basisartikels
        (&quot;Hauptkategorien&quot;)</li>
    <li>alle Kategorien im Pfad des aktuellen Artikels</li>
    <li>der aktuelle Artikel</li>
    <li>seine Geschwisterartikel und -kategorien
        [bei online-Elternkategorie]</li>
    <li>seine Kindartikel</li>
</ul>

<div><br/><b>Normalkonfiguration (Typ 2):</b></div>
<ul style="padding-left:30px; margin-bottom:0px;">
    <li>Minimalkonfiguration und zusätzlich:</li>
    <li>alle Geschwisterkategorien im Pfad des aktuellen Artikels</li>
</ul>

<div><br/><b>Maximalkonfiguration (Typ 3):</b></div>
<ul style="padding-left:30px; margin-bottom:0px;">
    <li>Normalkonfiguration und zusätzlich:</li>
    <li>alle Geschwisterartikel im Pfad des aktuellen Artikels</li>
</ul>
';
echo utf8_encode($string);
?>
