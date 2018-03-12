<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Maerz 2018
 */
$string='
<ul>
    <li>Dieses AddOn bietet ein System zur Erzeugung einer einfachen
        Navigation für Websites. Es ist mit jedem URL-Rewriter
        nutzbar.</li>
    <li>Navigationen können für beliebige Kategorien eingerichtet
        werden und umfassen alle Unterkategorien ihrer Basiskategorie.
        Angezeigt werden aber nur Artikel und Kategorien, die online
        sind.</li>
    <li>Die Darstellung erfolgt in Form von untereinander angeordneten
        Zeilen mit Links auf Nachbarseiten des aktuell angezeigten
        Artikels. Umfang und Layout der Navigationszeilen sind
        konfigurierbar. Die Sprachversion der verlinkten Nachbarseiten
        entspricht derjenigen des aktuellen Artikels.</li>
</ul>
';
echo utf8_encode($string);
?>
