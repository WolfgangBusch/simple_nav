# simple_nav
<h3>Einfache Navigation für Redaxo 5</h3>

<div>Dieses AddOn bietet ein System zur Erzeugung einer einfachen
Navigation für Websites.<br>
Die Navigation wird in Form untereinander angeordneter Zeilen
mit Links auf Nachbarseiten des aktuell angezeigten Artikels
dargestellt.<br>
Das Design der Navigationszeilen ist responsiv. Auf
Smartphone-Displays sind sie normalerweise ausgeblendet, können
aber über einen Schalter ein- und ausgeblendet werden.<br>
Umfang und Layout der Navigationszeilen sind konfigurierbar.</div>

<div><br>Die Navigationszeilen zeigen den Artikelnamen an.
Sie werden nötigenfalls umgebrochen und im Falle besonders langer
Wörter auch abgeschnitten. Artikel und Kategorie-Startartikel
können mit unterschiedlichen Icons gekennzeichnet werden.</div>

<div><br>Navigationen können für eine oder mehrere Kategorien
einer Website eingerichtet werden und umfassen alle Artikel und
Unterkategorien der gewählten (Basis-)Kategorie. Offline-Artikel
sind dabei grundsätzlich "verborgen", ebenso Offline-Kategorien samt
allen ihren Artikeln.</div>

<div><br>Die Navigation ist beschränkt auf einen "Sprachraum",
definiert durch die Sprachversion des aktuell angezeigten Artikels.
Alle Links in den Zeilen führen grundsätzlich nur zu Seiten
innerhalb dieses Sprachraums.</div>

<div><br>Die Anzahl der angezeigten Navigationszeilen kann in drei Stufen
variiert werden:</div>
<div>Minimalkonfiguration (Typ 1):</div>
<ul style="margin:0;">
    <li>alle Kindartikel des Basisartikels (= Startartikel der
        Basiskategorie)</li>
    <li>alle Kindkategorien des Basisartikels</li>
    <li>alle Kategorien im Pfad des aktuellen Artikels</li>
    <li>der aktuelle Artikel</li>
    <li>seine Bruderartikel und -kategorien
        (bei online-Elternkategorie)</li>
    <li>seine Kindartikel (soweit vorhanden)</li>
</ul>
<div>Normalkonfiguration (Typ 2):</div>
<ul style="margin:0;">
    <li>Minimalkonfiguration und zusätzlich:</li>
    <li>alle Bruderkategorien im Pfad des aktuellen Artikels
        ("Onkel"-Kategorien, "Großonkel"-Kategorien, ...)</li>
</ul>
<div>Maximalkonfiguration (Typ 3):</div>
<ul style="margin:0;">
    <li>Normalkonfiguration und zusätzlich:</li>
    <li>alle Bruderartikel im Pfad des aktuellen Artikels
        ("Onkel"-Artikel, "Großonkel"-Artikel,...)</li>
</ul>