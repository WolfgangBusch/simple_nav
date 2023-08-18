<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version August 2023
 */
#
class simple_nav {
#
#   Basis-Funktionen
#      redaxo_path($article)
#      nav_path($article,$bas_id)
#      is_in_path($category,$article)
#      hamburger_kreuz_icon()
#      folder_icon()
#      file_icon()
#   Aufsammeln der Navigationseintraege
#      subcats_of_category($cat,$act_article,$bas_id)
#      articles_of_category($cat,$act_article,$bas_id)
#      get_artnavpar($article,$act_id,$first_id,$bas_id,$b_line)
#   Ausgabe der Navigation
#      proof_parameters($bas_id,$b_line,$act_id)
#      nav_entries($bas_id,$b_line,$act_id,$navtyp)
#      sort_entries($entries)
#      generate_lines($lines,$increment)
#      print_navigation($bas_id,$b_line,$actid,$ntyp)
#   Beispiel-Navigation
#      example_entries($navtyp)
#      print_example($ntyp)
#
# --------------------------- Konstanten
const this_addon=__CLASS__;
const HAMBURGER  ='hamburger_icon';     // Id des Icons, das die Navigation anzeigt
const KREUZ      ='kreuz_icon';         // Id des Icons, das die Navigation verbirgt
const WIDTH_MOBIL=35;                   // Smartphone display size 'max-width:...em'
const FOLDER_SVG ='icon_folder.svg';    // Dateiname für das Folder-Icon
const FILE_SVG   ='icon_file.svg';      // Dateiname für das File-Icon
#     simple_nav-Klassennamen fuer die div-Container
const DIV_TYPE   ='typ';                // da wird noch angehaengt: LINE_TYPE
const DIV_BORDER ='border';
const DIV_BORDER1='border1';
const DIV_FORMAT ='format';
#     Zeilenparameter-Namen
const LINE_ID    ='id';                 // Artikel-Id
const LINE_PID   ='pid';                // Id des Eltern-Artikels
const LINE_LEVEL ='level';              // Level der Navigationstiefe des Artikels
const LINE_FOLDER='folder';             // = 1/0 (Artikel ist/nicht Kategorie-Startseite)
const LINE_PRIO  ='prio';               // Artikel-Priority
const LINE_STATUS='status';             // Artikelstatus (online/offline = 1/0)
const LINE_TYPE  ='typ';                // = '0', '1' oder '2'
const LINE_NAME  ='name';               // Inhalt der Navigationszeile (= Artikelname)
const LINE_URL   ='url';                // abs. URL der Navigationszeile
#     Konfigurationsparameter-Keys
const NAV_TYP        ='navtyp';         // Navigationstyp (= 1/2/3)
const NAV_INDENT     ='indent';         // Einrueckung pro Level (in Anzahl Pixel)
const NAV_WIDTH      ='width';          // Breite der Navigationsspalte (in Anzahl Pixel)
const NAV_FOLDER_ICON='folder_icon';    // Anzeige des Folder-Icons (ja/nein = /'on'/'')
const NAV_FILE_ICON  ='file_icon';      // Anzeige des File-Icons   (ja/nein = /'on'/'')
const NAV_LINE_HEIGHT='line_height';    // Zeilenhoehe der Navigationszeile (in em)
const NAV_FONT_SIZE  ='font_size';      // Fontgroesse (in em)
const NAV_BOR_LRWIDTH='bor_lrwidth';    // Randdicke links/rechts (in Anzahl Pixel)
const NAV_BOR_OUWIDTH='bor_ouwidth';    // Randdicke  oben/unten  (in Anzahl Pixel)
const NAV_BOR_RAD    ='bor_rad';        // Rand-Kruemmungsradius (in em)
const NAV_COL_LINK   ='col_link';       // Link-Farbe (rgba)
const NAV_COL_BORD_0 ='col_border_0';   // Randfarbe        (Normalzeile)
const NAV_COL_BACK_0 ='col_backgr_0';   // Hintergrundfarbe
const NAV_COL_BORD_1 ='col_border_1';   // Randfarbe        (Zeile Urahne)
const NAV_COL_BACK_1 ='col_backgr_1';   // Hintergrundfarbe
const NAV_COL_BORD_2 ='col_border_2';   // Randfarbe        (Zeile akt. Artikel)
const NAV_COL_BACK_2 ='col_backgr_2';   // Hintergrundfarbe
const NAV_COL_TEXT_2 ='col_text_2';     // Textfarbe
#
# --------------------------- Basis-Funktionen
public static function redaxo_path($article) {
   #   Rueckgabe des Redaxo-Artikel-Parameters 'path' (=$article->getValue('path')).
   #   $article          der Artikel (Objekt)
   #   $article->getPath() liefert im Falle von Startartikeln der ersten Ebene
   #   von Unterkategorien der Site-Startkategorie ein anderes Ergebnis:
   #      getValue('path') liefert immer '|'
   #      getPath() liefert dagegen z.B. '|$id|' ($id=$article->getId())
   #
   return $article->getValue('path');
   }
public static function nav_path($article,$bas_id) {
   #   Rueckgabe des Pfads eines Artikels relativ zum Navigations-Basisartikel.
   #   'Pfad' meint hier den Redaxo-Artikel-Parameter 'path' oder einen passend
   #   vorne gekuerzte Teil von 'path'. Leere Rueckgabe, falls der Artikel
   #   nicht im Pfad des Navigations-Basisartikels liegt.
   #   $article          der Artikel (Objekt), i.d.R. der aktuelle Artikel
   #   $bas_id           Id des Navigations-Basisartikels
   #   benutzte functions:
   #      self::redaxo_path($article)
   #   Beispiele fuer den Pfad eines Artikels (Rueckgabewert):
   #      Navigations-Basisartikel = Site-Startartikel:
   #         Pfad = Redaxo-Artikel-Parameter path ($article->getValue('path'))
   #      Navigations-Basisartikel unterhalb vom Site-Startartikel:
   #         path         id   Pfad      Artikel
   #         |1|6|        12   |         Navigations-Basisartikel
   #         |1|6|12|     25   |12|      Artikel in der Basis-Kategorie
   #         |1|6|12|     28   |12|      Unterkategorie der Basis-Kategorie
   #         |1|6|12|28|  35   |12|28|   Artikel in dieser Unterkategorie
   #      Artikel ausserhalb des Pfades zum Navigations-Basisartikels
   #         |1|           8   <leer>    Artikel oberhalb des Navigations-Basisartikels
   #         |1|5|14|     22   <leer>    Artikel nicht im path zum Navigations-Basisartikel
   #
   # --- Navigations-Basisartikel = Site-Startartikel
   $path0=self::redaxo_path($article);
   if($bas_id==rex_article::getSiteStartArticleId()) return $path0;
   #
   # --- Artikel = Navigations-Basisartikel
   if($bas_id==$article->getId()) return '|';
   #
   # --- sonst
   $ids=explode('|',$path0);
   $path='';
   $inside=FALSE;
   for($i=1;$i<count($ids);$i=$i+1):
      $strid=strval($ids[$i]);
      if($strid==$bas_id):
        $path='|';
        $inside=TRUE;
        endif;
      if(!empty($path)):
        if(!empty($strid)) $strid=$strid.'|';
        $path=$path.$strid;
        endif;
      endfor;
   if(empty($path) and $inside) $path='|';
   return $path;
   }
public static function is_in_path($category,$article) {
   #   Ermittlung, ob eine gegebene Kategorie im Pfad des aktuellen Artikels ist.
   #   Das schliesst den Falle ein, dass beide Artikel identisch sind, schliesst
   #   andererseits den Fall aus, dass beide Artikel Brueder sind.
   #   $category         gegebene Kategorie (Objekt)
   #   $article          aktueller Artikel (Objekt)
   #   benutzte functions:
   #      self::redaxo_path($article)
   #
   $art_path=self::redaxo_path($article).$article->getId().'|';
   $cat_path=self::redaxo_path($category).$category->getId().'|';
   if($cat_path==substr($art_path,0,strlen($cat_path))):
     return TRUE;
     else:
     return FALSE;
     endif;
   }
public static function hamburger_kreuz_icon() {
   #   Rueckgabe des HTML-Codes zur Ausgabe eines Hamburger-Icons/Kreuz-Icons
   #   als Button fuer einen Schalter zum Anzeigen bzw. Verbergen der Navigation.
   #   Der Schalter ist realisiert auf der Basis einer Javascript-function.
   #   Er wird nur auf (schmalen) Smartphone-Displays angezeigt (div-Container
   #   mit Id 'hamburger_icon' bzw. Id 'kreuz_icon') und wird mittels CSS-Codes
   #   dargestellt.
   #
   return '
<div id="'.self::HAMBURGER.'">
    <a href="#" title="Navigation anzeigen / verbergen"
       onClick="show_hide(\''.self::this_addon.'\',\''.self::HAMBURGER.'\',\''.self::KREUZ.'\');">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div></a>
</div>
<div id="'.self::KREUZ.'">
    <a href="#" title="Navigation anzeigen / verbergen"
       onClick="show_hide(\''.self::this_addon.'\',\''.self::HAMBURGER.'\',\''.self::KREUZ.'\');">
        <div class="cross1"></div>
        <div class="cross2"></div>
        <div class="cross3"></div></a>
</div>
';
   }
public static function folder_icon() {
   #   Rueckgabe des HTML-Codes des Folder-Icons fuer eine Navigationszeile.
   #
   $src=rex_url::addonAssets(self::this_addon,self::FOLDER_SVG);
   $pos=strpos($src,'/');
   $src=substr($src,$pos);
   return '<img src="'.$src.'" class="nav_ico">';
   }
public static function file_icon() {
   #   Rueckgabe des HTML-Codes des File-Icons fuer eine Navigationszeile.
   #
   $src=rex_url::addonAssets(self::this_addon,self::FILE_SVG);
   $pos=strpos($src,'/');
   $src=substr($src,$pos);
   return '<img src="'.$src.'" class="nav_ico">';
   }
#
# --------------------------- Aufsammeln der Navigationseintraege
public static function subcats_of_category($cat,$act_article,$bas_id) {
   #   Rueckgabe der Unterkategorien (Objekte) einer gegebenen Kategorie.
   #   Randbedingungen an die zurueck gegebenen Unterkategorien:
   #   - nur Kategorien (keine Artikel)
   #   - die Kategorie selbst wird nicht mit zurueck gegeben
   #   - Offline-Unterkategorien muessen mit zurueck gegeben werden
   #   - Falls die gegebene Kategorie selbst offline ist, werden ausser
   #     dem direkten Vorfahren keine Unterkategorien zurueck gegeben
   #   Die Rueckgabe erfolgt als nummeriertes Array (Nummerierung ab 1).
   #   Die Reihenfolge entspricht offenbar der 'rex_article.priority'.
   #   $cat              die Kategorie (Objekt)
   #   $act_article      aktueller Artikel (Objekt)
   #   $bas_id           Id des Navigations-Basisartikels
   #   benutzte functions:
   #      self::is_in_path($category,$article)
   #
   # --- Unterkategorien auswaehlen
   $act_id=$act_article->getId();
   $cat_onl=$cat->isOnline();
   $children=$cat->getChildren();
   $m=0;
   $subcat=array();
   for($i=0;$i<count($children);$i=$i+1):
      $child=$children[$i];
      #     bei Offline-Kategorie: nur der akt. Artikel oder sein direkter Vorfahre
      if(!$cat_onl and !self::is_in_path($child,$act_article) and
         $child->getId()!=$act_id) continue;
      $m=$m+1;
      $subcat[$m]=$child;
      endfor;
   return $subcat;
   }
public static function articles_of_category($cat,$act_article,$bas_id) {
   #   Rueckgabe der Kindartikel (Objekte) einer gegebenen Kategorie.
   #   Randbedingungen an die zurueck gegebenen Artikel:
   #   - es werden keine Unterkategorien zurueck gegeben
   #   - auch der Kategorie-Startartikel wird nicht zurueck gegeben
   #   - Offline-Artikel werden nicht mit zurueck gegeben
   #   - von einer Offline-Kategorie wird nur der aktuelle Artikel zurueck gegeben
   #   Die Rueckgabe erfolgt als nummeriertes Array (Nummerierung ab 1).
   #   Die Reihenfolge entspricht offenbar der 'rex_article.priority'.
   #   $cat              die Kategorie (Objekt)
   #   $act_article      der aktuelle Artikel (Objekt)
   #   $bas_id           Id des Navigations-Basisartikels
   #
   # --- Kindartikel auswaehlen
   $act_id=$act_article->getId();
   $cat_onl=$cat->isOnline();
   $arts=$cat->getArticles();
   $m=0;
   $articles=array();
   for($i=0;$i<count($arts);$i=$i+1):
      $art=$arts[$i];
      #     der Startartikel wird anderweitig zurueck gegeben
      if($art->isStartArticle())               continue;
      #     keine Offline-Artikel
      if(!$art->isOnline())                    continue;
      #     in Offline-Kategorien nur der aktuelle Artikel
      if(!$cat_onl and $art->getId()!=$act_id) continue;
      $m=$m+1;
      $articles[$m]=$art;
      endfor;
   return $articles;
   }
public static function get_artnavpar($article,$act_id,$first_id,$bas_id,$b_line) {
   #   Rueckgabe der fuer die Navigation noetigen Parameter eines Artikels
   #   als assoziatives Array mit diesen Keys (auch in dieser Reihenfolge):
   #      [self::LINE_ID]      Artikel-Id
   #      [self::LINE_PID]     Id der Eltern-Kategorie
   #      [self::LINE_LEVEL]   Navigations-Level (=1,2,3,...)
   #      [self::LINE_FOLDER]  =1/0: Artikel ist Startartikel / kein Startartikel
   #      [self::LINE_PRIO]    Artikel-Priority
   #      [self::LINE_STATUS]  Artikel-Status (Online/Offline: 1/0)
   #      [self::LINE_TYPE]    Typ der Navigationszeile:
   #                           =2: aktueller Artikel
   #                           =1: Ahne der ersten Generation (Hauptkategorie)
   #                           =0: sonst
   #      [self::LINE_NAME]    Artikel-Name (rex_article.name)
   #      [self::LINE_URL]     Artikel-URL (mittels rex_getUrl(id))
   #   $article          gegebener Artikel (Objekt)
   #   $act_id           Id des aktuellen Artikels
   #   $first_id         Id des Ahnen der ersten Generation
   #   $bas_id           Id des Navigations-Basisartikels
   #   $b_line           =TRUE:  Navigations-Basisartikel wird mit angezeigt
   #                     =FALSE: Navigations-Basisartikel wird nicht mit angezeigt
   #   benutzte functions:
   #      self::nav_path($article,$bas_id)
   #
   $id=$article->getId();
   #     online/offline: 1/0
   $onl=$article->isOnline();
   if(empty($onl)) $onl=0;
   #     Startartikel/Kindartikel: 1/0
   $folder=$article->isStartArticle();
   if(empty($folder)) $folder=0;
   #     Level erniedrigen, falls der Navigations-Basisartikel nicht angezeigt wird
   $level=substr_count(self::nav_path($article,$bas_id),'|');
   if(!$b_line and $id!=$bas_id) $level=$level-1;
   #     Zeilentyp
   $typ=0;
   if($id==$first_id) $typ=1;
   if($id==$act_id)    $typ=2;
   #
   #     Hier wird die Reihenfolge der Keys angelegt
   $entry=array();
   $entry[self::LINE_ID]     =$id;
   $entry[self::LINE_PID]    =$article->getParentId();
   $entry[self::LINE_LEVEL]  =$level;
   $entry[self::LINE_FOLDER] =$folder;
   $entry[self::LINE_PRIO]   =$article->getPriority();
   $entry[self::LINE_STATUS] =$onl;
   $entry[self::LINE_TYPE]   =$typ;
   $entry[self::LINE_NAME]   =htmlspecialchars($article->getName()); // fuer & < > im Namen
   $entry[self::LINE_URL]    =rex_getUrl($id);
   return $entry;
   }
#
# --------------------------- Ausgabe der Navigation
public static function proof_parameters($bas_id,$b_line,$act_id) {
   #   Ueberpruefung und noetigenfalls Korrektur und Rueckgabe der Eingabeparameter
   #   fuer die Navigation. Rueckgabe als assoziatives Array.
   #   $bas_id           Id des Navigations-Basisartikels
   #   $b_line           =TRUE/FALSE: Navigations-Basisartikel-Zeile wird angezeigt
   #                                  bzw. nicht angezeigt
   #   $act_id           =0: Id des aktuellen Artikels (zu Testzwecken kann hier
   #                         ein anderer Artikel genommen werden)
   #
   # --- Ueberpruefung von basid (Id des Navigations-Basisartikels)
   $basid=$bas_id;
   if($basid<=0):
     #     falls undefiniert: Site-Startartikel
     $basid=rex_article::getSiteStartArticleId();
     else:
     $bas_article=rex_article::get($basid);
     if($bas_article==null):
       #     falls ungueltige Artikel-Id: stattdessen Site-Startartikel
       $basid=rex_article::getSiteStartArticleId();
       else:
       #     falls kein Startartikel: stattdessen sein Elternartikel
       if(!$bas_article->isStartArticle()) $basid=$bas_article->getParentId();
       endif;
     endif;
   #
   # --- Ueberpruefung von $b_line (Anzeige Navigations-Basisartikel oder nicht)
   $bline=$b_line;
   if(!empty($bline)):
     $bline=TRUE;
     else:
     $bline=FALSE;
     endif;
   #
   # --- Ueberpruefung von $act_id (Id des aktuellen Artikels)
   $actid=$act_id;
   if($actid<=0):
     #     falls undefiniert: aktueller Artikel
     $actid=rex_article::getCurrentId();
     else:
     $act_article=rex_article::get($actid);
     if($act_article==null)
       #     falls ungueltige Artikel-Id: stattdessen aktueller Artikel
       $actid=rex_article::getCurrentId();
      endif;
   return array('bas_id'=>$basid, 'b_line'=>$bline, 'act_id'=>$actid);
   }
public static function nav_entries($bas_id,$b_line,$act_id,$navtyp) {
   #   Berechnung und Rueckgabe der Entries (Zeilen) der Navigation. Noch sind
   #   die Offline-Kategorien enthalten, die Offline-Artikel sind nicht enthalten.
   #   Reihenfolge: Level aufsteigend, Kategorien vor Artikeln, Artikel gemaess
   #   priority. Die Offline-Kategorien und -Artikel koennen erst nach der
   #   Sortierung herausgeloescht werden. Die Entries werden dargestellt als
   #   nummeriertes Array (Nummerierung ab 1, jeder Entry als assoziatives
   #   Array, Schluessel siehe get_artnavpar()).
   #   $bas_id           Id des Navigations-Basisartikels
   #   $b_line           =TRUE/FALSE: Navigations-Basisartikel-Zeile wird angezeigt
   #                                  bzw. nicht angezeigt
   #   $act_id           Id des aktuellen Artikels (zu Testzwecken kann hier
   #                     ein anderer Artikel eingegeben werden)
   #   $navtyp           Navigationstyp (=1/2/3)
   #   benutzte functions:
   #      self::nav_path($article,$bas_id)
   #      self::articles_of_category($cat,$act_article,$bas_id)
   #      self::subcats_of_category($cat,$act_article,$bas_id)
   #      self::get_artnavpar($article,$act_id,$first_id,$bas_id,$b_line)
   #
   # --- weitere Daten zum aktuellen Artikel
   $act_article=rex_article::get($act_id);
   $act_is_start=$act_article->isStartArticle();
   #     Elternartikel des aktuellen Artikels
   $act_pid=$act_article->getParentId();
   if($act_id==$bas_id) $act_pid=$act_id; // = Artikel selbst, falls Navigations-Basisartikel
   #     Id-Liste ueber den Pfad des aktuellen Artikels
   #     (bezogen auf den Navigations-Basisartikel)
   $act_baspath=self::nav_path($act_article,$bas_id);
   $pathid=explode('|',$act_baspath);
   $act_anzid=count($pathid)-1;
   #     Id des aeltesten Ahnen des aktuellen Artikels ...
   $first_id='';
   if($act_anzid>1) $first_id=$pathid[2];   // ... oberhalb des Navigations-Basisartikels
   #
   # --- falls der Navigations-Basisartikel mit ausgegeben wird
   $m=0;
   $entries=array();
   if($b_line):
     $m=1;
     $entries[$m]=self::get_artnavpar(rex_article::get($bas_id),$act_id,$first_id,$bas_id,$b_line);
     endif;
   #
   # --- Auswahl der Ahnen-/Bruder-/Kind-Kategorien und -Artikel (inkl. Offline-Kat.)
   #     zunaechst nach Level aufsteigend sortiert (jeweils Kategorien vor Artikel)
   for($i=1;$i<=$act_anzid;$i=$i+1):
      $cat_id=$pathid[$i];
      if($i==$act_anzid and $act_is_start) $cat_id=$act_id;
      if(empty($cat_id)) continue;
      $cat=rex_category::get($cat_id);
      #
      # --- Kategorien
      if($navtyp==1):
        #     direkte Ahnenkategorien
        if($i>2 and $i<$act_anzid):
          $m=$m+1;
          $entries[$m]=self::get_artnavpar($cat,$act_id,$first_id,$bas_id,$b_line);
          endif;
        #     Unterkategorien der Navigationsbasis | Bruderkategorien | Kindkategorien
        if($i==1 or $cat_id==$act_pid or $cat_id==$act_id):
          $siscat=self::subcats_of_category($cat,$act_article,$bas_id);
          for($k=1;$k<=count($siscat);$k=$k+1):
             $m=$m+1;
             $entries[$m]=self::get_artnavpar($siscat[$k],$act_id,$first_id,$bas_id,$b_line);
             endfor;
          endif;
        endif;
      if($navtyp>=2):
        #     alle benoetigten Kategorien
        $siscat=self::subcats_of_category($cat,$act_article,$bas_id);
        for($k=1;$k<=count($siscat);$k=$k+1):
           $m=$m+1;
           $entries[$m]=self::get_artnavpar($siscat[$k],$act_id,$first_id,$bas_id,$b_line);
           endfor;
        endif;
      #
      # --- Artikel
      if($i==1 or                      // immer:     Kindartikel der Navigationsbasis
         ($navtyp<=2 and               // Navtyp 1/2:
          ((!$act_is_start and $cat_id==$act_pid) // Bruder- oder
           or $cat_id==$act_id)) or               // Kindartikel
         $navtyp==3):                  // Navtyp 3:  alle benoetigten Artikel
        $arts=self::articles_of_category($cat,$act_article,$bas_id);
        for($k=1;$k<=count($arts);$k=$k+1):
           $m=$m+1;
           $entries[$m]=self::get_artnavpar($arts[$k],$act_id,$first_id,$bas_id,$b_line);
           endfor;
        endif;
      endfor;
   return $entries;
   }
public static function sort_entries($entries) {
   #   Die eingegebenen unsortierten Navigationszeilen werden sortiert und
   #   zurueck gegeben.
   #   Die Zeilen werden schon so sortiert eingegeben:
   #         1) Nach Level aufsteigend
   #         2) Innerhalb der Levels Kategorien vor Artikeln
   #         3) Kategorien und Artikel jeweils sortiert nach rex_article.priority
   #   Die weitere Sortierung erfolgt in 2 Durchlaeufen:
   #      nach dem ersten Durchlauf:
   #         1) Kindkategorien der Navigations-Startseite bis inkl. Urahne des
   #            aktuellen Artikels
   #         2) Ahnenkategorien des aktuellen Artikels (ggf. inkl. aktuellem
   #            Artikel, falls dieser ein Startartikel ist)
   #         3) Bruderartikel des aktuellen Artikels und aktueller Artikel bzw.
   #            Kindkategorien und Kindartikel des aktuellen Artikels
   #         4) Rest bleibt unsortiert
   #         Diese Kategorien und Artikel werden nach unten geschoben:
   #         -  Kindkategorien/-artikel der Navigations-Startseite (Nav-Typ 1,2,3)
   #         -  Onkelkategorien des aktuellen Artikels (Nav-Typ 2,3)
   #         -  Onkelartikel des aktuellen Artikels (Nav-Typ 3)
   #      nach dem zweiten Durchlauf (fuer den unsortierten Rest):
   #         1) nach Level absteigend
   #         2) Innerhalb des Levels: Kategorien vor Artikeln
   #         3) Kategorien und Artikel jeweils sortiert nach rex_article.priority
   #   $entries          nummeriertes Array der unsortierten Navigationseintraege
   #                     (Nummerierung ab 1), jeder Navigationseintrag ist ein
   #                     assoziatives Array mit diesen Schluesseln:
   #      [self::LINE_ID]      Artikel-Id
   #      [self::LINE_PID]     Id der Eltern-Kategorie
   #      [self::LINE_LEVEL]   Navigations-Level (=1,2,3,...)
   #      [self::LINE_FOLDER]  =1/0: Artikel ist Startartikel / kein Startartikel
   #      [self::LINE_PRIO]    Artikel-Priority
   #      [self::LINE_STATUS]  Artikel-Status           (hier nicht benutzt)
   #      [self::LINE_TYPE]    Typ der Navigationszeile (hier nicht benutzt)   
   #      [self::LINE_NAME]    Artikel-Name             (hier nicht benutzt)
   #      [self::LINE_URL]     Artikel-URL              (hier nicht benutzt)
   #
   $entr=$entries;
   #
   # --- Erster Durchlauf:
   $levmax=0;
   for($i=1;$i<=count($entr);$i=$i+1):
      $lev=$entr[$i][self::LINE_LEVEL];
      if($lev>$levmax) $levmax=$lev;
      $id=$entr[$i][self::LINE_ID];
      $m=$i;
      for($k=$i+1;$k<=count($entr);$k=$k+1):
         if($entr[$k][self::LINE_PID]==$id):  // Offline-Kategorien nicht auslassen!!!
           $m=$m+1;
           $ent=$entr[$m];
           $entr[$m]=$entr[$k];
           $entr[$k]=$ent;
           endif;
         endfor;
      endfor;
   #
   # --- Zweiter Durchlauf
   $start=0;
   $level=0;
   for($i=1;$i<=count($entr);$i=$i+1):
      $lev=$entr[$i][self::LINE_LEVEL];
      if($start<=0 and $lev>=$level):
        $level=$lev;
        if($lev==$levmax) $start=1;
        if($start<=0) continue;
        endif;
      for($k=$i+1;$k<=count($entr);$k=$k+1):
         $lev=$entr[$i][self::LINE_LEVEL];
         $fo=$entr[$i][self::LINE_FOLDER];
         $pr=$entr[$i][self::LINE_PRIO];
         $level=$entr[$k][self::LINE_LEVEL];
         $fold=$entr[$k][self::LINE_FOLDER];
         $prio=$entr[$k][self::LINE_PRIO];
         if($level>$lev or
            ($level==$lev and $fold>$fo) or
            ($level==$lev and $fold==$fo and $prio<$pr)):
           $ent=$entr[$i];
           $entr[$i]=$entr[$k];
           $entr[$k]=$ent;
           endif;
         endfor;
      endfor;
   return $entr;
   }
public static function generate_lines($lines,$increment) {
   #   Erzeugung und Rueckgabe aller Navigationszeilen.
   #   $lines            nummeriertes Array der Daten der Navigationszeilen, jede
   #                     Zeile ist ein assoziatives Array mit diesen Schluesseln:
   #      [self::LINE_ID]      Artikel-Id                   (hier nicht benutzt)
   #      [self::LINE_PID]     Id der Eltern-Kategorie      (hier nicht benutzt)
   #      [self::LINE_LEVEL]   Navigations-Level (=1,2,3,...)
   #      [self::LINE_FOLDER]  Artikel (nicht) Startartikel
   #      [self::LINE_PRIO]    Artikel-Priority             (hier nicht benutzt)
   #      [self::LINE_STATUS]  Artikel-Status               (hier nicht benutzt)
   #      [self::LINE_TYPE]    Typ der Navigationszeile:
   #                           =2: aktueller Artikel
   #                           =1: Ahne der ersten Generation (Hauptkategorie)
   #                           =0: sonst
   #      [self::LINE_NAME]    Artikel-Name
   #      [self::LINE_URL]     Artikel-URL
   #   $increment        Einrueckung pro Level in Anzahl Pixel
   #   benutzte functions:
   #      self::folder_icon()
   #      self::file_icon()
   #      self::hamburger_kreuz_icon()
   #
   # --- Ausgabestring
   $ausgabe='';
   for($i=1;$i<=count($lines);$i=$i+1):
      $name  =$lines[$i][self::LINE_NAME];
      $url   =$lines[$i][self::LINE_URL];
      $level =$lines[$i][self::LINE_LEVEL];
      $typ   =$lines[$i][self::LINE_TYPE];
      $did   =self::DIV_TYPE.strval($typ);
      $indent=intval(($level-1)*$increment);
      $style ='margin-left:'.$indent.'px; overflow:hidden;';
      if($typ==2):
        $text='<span>'.$name.'</span>';
        else:
        $text='<a href="'.$url.'">'.$name.'</a>';
        endif;
      if($i==1):
        $class=self::DIV_BORDER1;
        else:
        $class=self::DIV_BORDER;
        endif;
      $class=$class.' '.self::DIV_FORMAT.' '.$did;
      $taba='<table class="nav_tabline"><tr>';
      $tabz='</tr></table>';
      $icon='';
      if(!empty(rex_config::get(self::this_addon,self::NAV_FOLDER_ICON))
         and $lines[$i][self::LINE_FOLDER]>0) $icon=self::folder_icon();
      if(!empty(rex_config::get(self::this_addon,self::NAV_FILE_ICON))
         and $lines[$i][self::LINE_FOLDER]<=0) $icon=self::file_icon();
      $ausgabe=$ausgabe.'
<div class="'.$class.'"><div style="'.$style.'">'.$taba.'<td>'.$icon.'</td><td>'.$text.'</td>'.$tabz.'</div></div>';
      endfor;
   #
   # --- Zeilen in div-Container mit id 'simple_nav' packen
   $ausgabe='<div id="'.self::this_addon.'">'.$ausgabe.'
</div>';
   #
   # --- Hamburger-/Kreuz-Icon vorne anfuegen
   $ausgabe='
<!----- Start Navigation -------------------------->'.
   self::hamburger_kreuz_icon().
   $ausgabe.'
<!----- Ende Navigation --------------------------->
';
   return $ausgabe;
   }
public static function print_navigation($basid=0,$bline=FALSE,$actid=0,$ntyp=0) {
   #   Berechnung und Ausgabe einer automatischen vertikalen Navigation sowie
   #   Rueckgabe der Anzahl der Navigationszeilen.
   #   $basid            Id des Navigations-Basisartikels
   #                     (falls <=0: Id des Site-Startartikels)
   #   $bline            =TRUE/FALSE: Navigations-Basisartikel-Zeile wird angezeigt
   #                     bzw. nicht angezeigt (Default: FALSE)
   #   $actid            Id des aktuellen Artikels (ggf. zu Testzwecken ein
   #                     anderer Artikel, falls 0: Id des aktuellen Artikels)
   #   $ntyp             Navigationstyp (=1/2/3) (ggf. zu Testzwecken nicht
   #                     gemaess Konfiguration, falls 0: Typ gemaess Konfig.)
   #   benutzte functions:
   #      self::proof_parameters($bas_id,$b_line,$act_id)
   #      self::nav_entries($bas_id,$b_line,$act_id,$navtyp)
   #      self::sort_entries($entries)
   #      self::generate_lines($lines,$increment)
   #
   # --- Ueberpruefung und ggf.Korrektur der Parameter
   $arr=self::proof_parameters($basid,$bline,$actid);
   $bas_id=$arr['bas_id'];
   $b_line=$arr['b_line'];
   $act_id=$arr['act_id'];
   #
   # --- konfigurierte Daten: Navigationstyp, Einrueckung in Pixel
   $navtyp   =rex_config::get(self::this_addon,self::NAV_TYP);
   $increment=rex_config::get(self::this_addon,self::NAV_INDENT);
   if($ntyp>0) $navtyp=$ntyp;
   #
   # --- Aufsammeln der Navigations-Entries
   $entries=self::nav_entries($bas_id,$b_line,$act_id,$navtyp);
   #
   # --- Sortieren der Entries
   $entries=self::sort_entries($entries);
   #
   # --- Herausloeschen der Offline-Pfadkategorien (Letztere notwendig fuer das Sortieren!)
   $m=0;
   $zeilen=array();
   for($i=1;$i<=count($entries);$i=$i+1)
      if($entries[$i][self::LINE_STATUS]==1):
        $m=$m+1;
        $zeilen[$m]=$entries[$i];
        endif;
   #
   # --- Ausgabe
   echo self::generate_lines($zeilen,$increment);
   return count($zeilen);
   }
#
# --------------------------- Beispiel-Navigation
public static function example_entries($navtyp) {
   #   Rueckgabe der unsortierten Zeilen einer Beispielnavigation
   #   unter Beruecksichtigung des konfigurierten Navigationstyps
   #   $navtyp           konfigurierter Navigationstyp
   #                     Klassenbezeichnungen zusammengestellt werden
   #
   $lev=self::this_addon::LINE_LEVEL;
   $fol=self::this_addon::LINE_FOLDER;
   $typ=self::this_addon::LINE_TYPE;
   $nam=self::this_addon::LINE_NAME;
   $url=self::this_addon::LINE_URL;
   #
   # --- Definition der Texte des Beispiels
   $URL='#';
   $entries=array(
       1=>array('navtyp'=>1,$lev=>1,$fol=>1,$typ=>0,$url=>$URL,$nam=>'Navigations-Basisartikel'),
       2=>array('navtyp'=>1,$lev=>2,$fol=>1,$typ=>0,$url=>$URL,$nam=>'Hauptkategorie 1'),
       3=>array('navtyp'=>1,$lev=>2,$fol=>1,$typ=>1,$url=>$URL,$nam=>'Hauptkategorie Urahne'),
       4=>array('navtyp'=>2,$lev=>3,$fol=>1,$typ=>0,$url=>$URL,$nam=>'Urgroßonkelkategorie 1'),
       5=>array('navtyp'=>1,$lev=>3,$fol=>1,$typ=>0,$url=>$URL,$nam=>'Urgroßvaterkategorie'),
       6=>array('navtyp'=>1,$lev=>4,$fol=>1,$typ=>0,$url=>$URL,$nam=>'Großvaterkategorie'),
       7=>array('navtyp'=>2,$lev=>5,$fol=>1,$typ=>0,$url=>$URL,$nam=>'Onkelkategorie 1'),
       8=>array('navtyp'=>1,$lev=>5,$fol=>1,$typ=>0,$url=>$URL,$nam=>'Vaterkategorie'),
       9=>array('navtyp'=>1,$lev=>6,$fol=>0,$typ=>0,$url=>$URL,$nam=>'Bruderartikel 1'),
      10=>array('navtyp'=>1,$lev=>6,$fol=>0,$typ=>2,$url=>$URL,$nam=>'aktueller Artikel'),
      11=>array('navtyp'=>1,$lev=>6,$fol=>0,$typ=>0,$url=>$URL,$nam=>'Bruderartikel 2'),
      12=>array('navtyp'=>2,$lev=>5,$fol=>1,$typ=>0,$url=>$URL,$nam=>'Onkelkategorie 2'),
      13=>array('navtyp'=>2,$lev=>5,$fol=>1,$typ=>0,$url=>$URL,$nam=>'Onkelkategorie 3'),
      14=>array('navtyp'=>3,$lev=>5,$fol=>0,$typ=>0,$url=>$URL,$nam=>'Onkelartikel'),
      15=>array('navtyp'=>2,$lev=>4,$fol=>1,$typ=>0,$url=>$URL,$nam=>'Großonkelkategorie'),
      16=>array('navtyp'=>3,$lev=>4,$fol=>0,$typ=>0,$url=>$URL,$nam=>'Großonkelartikel 1'),
      17=>array('navtyp'=>3,$lev=>4,$fol=>0,$typ=>0,$url=>$URL,$nam=>'Großonkelartikel 2'),
      18=>array('navtyp'=>2,$lev=>3,$fol=>1,$typ=>0,$url=>$URL,$nam=>'Urgroßonkelkategorie 2'),
      19=>array('navtyp'=>3,$lev=>3,$fol=>0,$typ=>0,$url=>$URL,$nam=>'Urgroßonkelartikel'),
      20=>array('navtyp'=>1,$lev=>2,$fol=>1,$typ=>0,$url=>$URL,$nam=>'Hauptkategorie 3'),
      21=>array('navtyp'=>1,$lev=>2,$fol=>0,$typ=>0,$url=>$URL,$nam=>'Hauptseite 1'),
      22=>array('navtyp'=>1,$lev=>2,$fol=>0,$typ=>0,$url=>$URL,$nam=>'Hauptseite 2')
      );
   #
   # --- Entries gemaess Navigationstyp 1/2 ausduennen
   if($navtyp<=2):
     $entneu=array();
     $m=0;
     for($i=1;$i<=count($entries);$i=$i+1):
        $entr=$entries[$i];
        if(intval($entr['navtyp'])>$navtyp) continue;
        $m=$m+1;
        $entneu[$m]=$entries[$i];
        endfor;
     $entries=$entneu;
     endif;
   #
   return $entries;
   }
public static function print_example($ntyp=0) {
   #   Ausgabe des HTML-Codes einer Beispiel-Navigation gemaess den konfigurierten
   #   Daten/Stylesheet unter Beruecksichtigung des konfigurierten Navigationstyps.
   #   $ntyp             Navigationstyp (=1/2/3) (ggf. zu Testzwecken nicht
   #                     gemaess Konfiguration, falls 0: Typ gemaess Konfig.)
   #   benutzte functions:
   #      self::example_entries($navtyp)
   #      self::generate_lines($entries,$increment)
   #
   # --- konfigurierte Daten
   $navtyp=$ntyp;
   if($ntyp<=0) $navtyp=rex_config::get(self::this_addon,self::this_addon::NAV_TYP);
   $increment=rex_config::get(self::this_addon,self::this_addon::NAV_INDENT);
   $url=$_SERVER['REQUEST_URI'];
   #
   # --- Entries (Navigationszeilen) definieren
   $entries=self::example_entries($navtyp);
   #
   # --- inkl./ohne Navigationsbasis-Artikel
   $basis='no';
   if(!empty($_GET['basis'])) $basis=$_GET['basis'];
   $nba='Navigationsbasis-Artikel';
   if($basis=='yes'):
     $tit='(inkl. '.$nba.')';
     $neu='ohne '.$nba;
     $sto='<a href="'.$url.'&basis=no"  title="'.$neu.'">'.$neu.'</a>';
     $neu='<div class="nav_hand">'.$sto.'</div>';
     else:
     $tit='(ohne '.$nba.')';
     $neu='inkl. '.$nba;
     $stm='<a href="'.$url.'&basis=yes" title="'.$neu.'">'.$neu.'</a>';
     $neu='<div class="nav_hand">'.$stm.'</div>';
     $ent=array();
     for($i=2;$i<=count($entries);$i=$i+1):
        $k=$i-1;
        $ent[$k]=$entries[$i];
        $ent[$k]['level']=$ent[$k]['level']-1;
        endfor;
     $entries=$ent;
     endif;
   #
   # --- Darstellung der Beispiel-Navigation
   echo '
<h4>Darstellung gemäß Konfiguration '.$tit.'</h4>
'.$neu.'
<br><table class="nav_table">
    <tr valign="top">
        <td class="xmp_pad">
            <div class="xmp_nav">
'.self::generate_lines($entries,$increment).'</div>
        </td>
        <td class="xmp_pad">
            Abhängig vom Navigationstyp (1/2/3) werden<br>
            Onkelkategorien bzw. -artikel aller Generationen<br>
            des aktuellen Artikels angezeigt (+) oder nicht (-):<br>
            <table class="nav_table">';
   for($n=1;$n<=3;$n=$n+1):
      $type='Typ '.$n;
      $shka='+';
      if($n==1) $shka='-';
      $shar='+';
      if($n==1 or $n==2) $shar='-';
      $class="nav_table";
      if($n==$navtyp) $class="xmp_bgcol";
      echo '
                <tr><td class="xmp_pad '.$class.'">'.$type.':</td>
                    <td class="xmp_pad '.$class.'">Onkelkategorien</td>
                    <td class="xmp_pad '.$class.'">'.$shka.'</td></tr>
                <tr><td class="xmp_pad '.$class.'"></td>
                    <td class="xmp_pad '.$class.'">Onkelartikel</td>
                    <td class="xmp_pad '.$class.'">'.$shar.'</td></tr>';
      endfor;
   echo '
            </table></td></tr>
</table>';   
   }
}
?>
