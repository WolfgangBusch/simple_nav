# simple_nav
<h4>Version 1.6</h4>
<ul>
    <li>Kategorien/Artikel können in den Navigationszeilen mit je einem
        Icon kenntlich gemacht werden, deren Farben an die konfigurierten
        Farben der Navigation angepasst sind.</li>
    <li>Geschwisterkategorien werden in der Navigation jetzt vor (statt
        nach) den Geschwisterartikeln eines Artikels angezeigt.</li>
    <li>Das AddOn schreibt die Stylesheet-Datei nur noch in das
        Assets-Verzeichnis des AddOns.</li>
    <li>Die Javascript-Datei steht jetzt im AddOn-assets-Ordner zur
        Verfügung und wird per Install/Re-Install bereit gestellt.</li>
    <li>Konstanten werden nicht mehr per 'define(...)' vereinbart,
        sondern als Klassen-Konstanten definiert.</li>
    <li>Neuinstallationen liefen bisher auf Fehler, die jetzt behoben
        sind.</li>
</ul>
<h4>Version 1.5</h4>
<ul>
    <li>Die Navigation ist jetzt auf Smartphone-Displays normalerweise
        ausgeblendet, kann aber über einen Schalter ein- und
        ausgeblendet werden (responsives Design).</li>
    <li>Mit der De-Installation des AddOns wird auch sein Namespace
        gelöscht.</li>
</ul>
<h4>Version 1.4.1</h4>
<ul>
    <li>Die function get_config_data() ist wieder eingefügt, sodass die
        Installation nun ohne Fehler funktioniert.</li>
</ul>

<h4>Version 1.4.0</h4>
<ul>
    <li>Die Kindartikel und Unterkategorien des Basisartikels einer
        Navigation werden jetzt immer angezeigt. Bisher war das nicht so,
        wenn der Basisartikel der Navigation nicht der Site-Startartikel
        war (Navigationstypen 1 und 2).</li>
    <li>Neu gesetzte Konfigurationsdaten sind jetzt ohne re-install
        sofort wirksam.</li>
    <li>Bei der De-Installation werden die Konfigurationsdaten nicht
        mehr gelöscht.</li>
    <li>Die AddOn-Dateien sind jetzt grundsätzlich in UTF8 codiert.</li>
</ul>

<h4>Version 1.3.0</h4>
<ul>
    <li>Das AddOn wurde grundlegend überabeitet und neu strukturiert.</li>
    <li>Die Function zur Ausgabe einer Navigation enthält jetzt die
        nötigen echo-Anweisungen selbst, sodass der entsprechende Aufruf
        im Template geändert werden muss (vergl. Beschreibung).</li>
</ul>

<h4>Version 1.2.0</h4>
<ul>
    <li>Die Stylesheet-Datei liegt jetzt im AddOn-Unterverzeichnis 'assets'.
        Dort hinein werden auch Konfigurationseinträge eingetragen, sodass
        erst mit einem re-Install das Stylesheet wirksam wird.</li>
    <li>Die Stylesheet-Datei wird jetzt auch auf der Beispiel-Seite
        im Backend benutzt.</li>
</ul>

<h4>Version 1.1.0</h4>
<ul>
    <li>Die Konfiguration der Navigationsparameter wurde so geändert,
        dass sie auch mit älteren PHP-Versionen funktioniert
        (define von Array-Konstanten erst ab PHP-Version 7 erlaubt).</li>
    <li>Die Backendseiten zur Konfiguration und zum simple_nav-Beispiel
        sind neu überarbeitet.</li>
    <li>Die deutsche Bezeichnung ist jetzt 'Einfache Navigation'.</li>
</ul>
