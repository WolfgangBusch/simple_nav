# simple_nav
<h3>Einfache Navigation für Redaxo 5</h3>
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

<div>Die Anzahl der Navigationszeilen kann in drei Stufen variiert
werden:</div>

<div><br/><b>Minimalkonfiguration (Typ 1):</b></div>
<ul style="padding-left:30px; margin-bottom:0px;">
    <li>alle Kindartikel des Startartikels</li>
    <li>alle Kindkategorien des Startartikels
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
