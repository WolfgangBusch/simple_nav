# simple_nav
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
