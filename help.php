<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Juli 2018
 */
$string='
<ul>
    <li>Dieses AddOn bietet ein System zur Erzeugung einer einfachen
        Navigation f�r Websites. Es ist mit jedem URL-Rewriter
        nutzbar.</li>
    <li>Navigationen k�nnen f�r beliebige Kategorien eingerichtet
        werden und umfassen alle Artikel und Unterkategorien dieser
        Basiskategorie.</li>
    <li>In der Navigation werden nur Artikel und Kategorien angezeigt,
        die online sind. Offline-Kategorien sind daher samt ihren
        Artikeln grunds�tzlich &quot;verborgen&quot;.</li>
    <li>Die Darstellung erfolgt in Form von untereinander angeordneten
        Zeilen mit Links auf Nachbarseiten des aktuell angezeigten
        Artikels.</li>
    <li>Umfang und Layout der Navigationszeilen sind konfigurierbar.
        Die Sprachversion der verlinkten Nachbarseiten entspricht
        derjenigen des aktuellen Artikels.</li>
</ul>
';
echo utf8_encode($string);
?>
