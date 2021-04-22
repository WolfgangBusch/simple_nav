<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version April 2021
 */
#   print_navigation($bas_id,$b_line)
#      redaxo_path($article)
#      get_path($article,$bas_id)
#         redaxo_path($article)
#      articles_of_category($cat,$act_article,$bas_id,$navtyp)
#         get_path($article,$bas_id)
#            redaxo_path($article)
#      subcats_of_category($cat,$act_article,$bas_id,$navtyp)
#         get_path($article,$bas_id)
#            redaxo_path($article)
#         vorfahre($article1,$article2)
#            redaxo_path($article)
#      set_artnavpar($article,$nr,$act_id,$act_first,$bas_id,$b_line)
#         get_path($article,$bas_id)
#            redaxo_path($article)
#      sort($entries)
#      print_line($lines,$increment)
#      hamburger_icon()
#
#     simple_nav-Klassennamen fuer die div-Container
define ('DIV_TYP',       'typ');         // da wird noch angehaengt: '0', '1' oder '2'
define ('DIV_BORDER',    'border');
define ('DIV_BORDER1',   'border1');
define ('DIV_FORMAT',    'format');
#     Zeilenparameter-Namen
define ('LINE_ID',       'id');
define ('LINE_PARENT_ID','parent_id');
define ('LINE_LEVEL',    'level');
define ('LINE_NR',       'nr');
define ('LINE_NAME',     'name');
define ('LINE_URL',      'url');
define ('LINE_STATUS',   'status');
define ('LINE_TYP',      'typ');
#
class simple_nav {
#
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
public static function get_path($article,$bas_id) {
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
public static function vorfahre($article1,$article2) {
   #   Ermittlung, ob ein Artikel 'Vorfahre' eines zweiten Artikels ist.
   #   $article1         gegebener Artikel (Objekt), moeglicher Vorfahre
   #   $article2         gegebener Artikel (Objekt), moeglicher Nachkomme
   #   benutzte functions:
   #      self::redaxo_path($article)
   #
   $artpath1=self::redaxo_path($article1);
   $artpath2=self::redaxo_path($article2);
   if($article2->isStartArticle()) $artpath2=$artpath2.$article2->getId().'|';
   $arr1=explode('|',$artpath1);
   $arr2=explode('|',$artpath2);
   for($k=1;$k<count($arr2)-1;$k=$k+1)
      if($arr1[$k]!=$arr2[$k]) return FALSE;
   return TRUE;
   }
public static function articles_of_category($cat,$act_article,$bas_id,$navtyp) {
   #   Rueckgabe der Artikel (Objekte) einer gegebenen Kategorie.
   #   Die Kategorie ist entweder
   #   - Startartikel des aktuellen Artikels (liefert 'Kindartikel') oder
   #   - Elternartikel des aktuellen Artikels (liefert 'Geschwisterartikel') oder
   #   - der Navigations-Basisartikel (liefert 'Hauptartikel', unabhaengig vom
   #     aktuellen Artikel) oder
   #   - Kategorie im Pfad des aktuellen Artikels (liefert 'Onkelartikel',
   #     'Grossonkelartikel', ...) im Falle von Navigationstyp 3.
   #   Randbedingungen an die zurueck gegebenen Artikel:
   #   - nur Artikel (keine Unterkategorien)
   #   - der Startartikel der Kategorie wird nicht mit zurueck gegeben
   #   - Offline-Artikel werden nicht mit zurueck gegeben
   #   - von einer Offline-Kategorie wird nur der aktuelle Artikel zurueck gegeben
   #   Die Rueckgabe erfolgt als nummeriertes Array (Nummerierung ab 1).
   #   Die Reihenfolge entspricht offenbar der 'rex_article.priority'.
   #   $cat              die Kategorie (Objekt)
   #   $act_article      der aktuelle Artikel (Objekt)
   #   $bas_id           Id des Navigations-Basisartikels
   #   $navtyp           Navigationstyp
   #   benutzte functions:
   #      self::get_path($article,$bas_id)
   #
   $act_id=$act_article->getId();
   #
   # --- weder Hauptartikel noch Kindartikel noch Geschwisterartikel (Navigationstyp < 3)
   if($navtyp==1 or $navtyp==2):
     $cat_path=self::get_path($cat,$bas_id);
     $cat_id  =$cat->getId();
     $par_id  =$act_article->getParentId();
     if($cat_path!='|'         // Kategorie keine Unterkategorie der Navigationsbasis
        and $cat_id!=$act_id   // Kategorie-Startartikel nicht der aktuelle Artikel
        and $cat_id!=$par_id)  // Kategorie-Startartikel nicht Elternartikel des Startartikels
       return array();
     endif;
   #
   # --- Artikel bestimmen
   $articles=$cat->getArticles();
   $m=0;
   $artic=array();
   for($i=0;$i<count($articles);$i=$i+1):
      $article=$articles[$i];
      if($article->isStartArticle()) continue;  // nicht den Startartikel
      if(!$article->isOnline())      continue;  // keine Offline-Artikel
      if(!$cat->isOnline() and
         $article->getId()!=$act_id) continue;  // in Offline-Kat. nur der aktuelle Artikel
      $m=$m+1;
      $artic[$m]=$article;
      endfor;
   return $artic;
   }
public static function subcats_of_category($cat,$act_article,$bas_id,$navtyp) {
   #   Rueckgabe der Unterkategorien (Objekte) einer gegebenen Kategorie.
   #   Die Kategorie ist entweder
   #   - Startartikel des aktuellen Artikels ('liefert Unterkategorien') oder
   #   - Elternartikel des aktuellen Artikels (liefert 'Geschwisterkategorien') oder
   #   - der Navigations-Basisartikel (liefert 'Hauptkategorien', unabhaengig vom
   #     aktuellen Artikel) oder
   #   - Kategorie im Pfad des aktuellen Artikels (liefert 'Onkelkategorien',
   #     'Grossonkelkategorien', ...).
   #   Randbedingungen an die zurueck gegebenen Unterkategorien:
   #   - nur Kategorien (keine Artikel)
   #   - die Kategorie selbst wird nicht mit zurueck gegeben
   #   - Offline-Unterkategorien muessen mit zurueck gegeben werden
   #   - Falls die gegebene Kategorie selbst offline ist,
   #     werden keine Unterkategorien zurueck gegeben ???ausser dem direkten Vorfahren???
   #   Die Rueckgabe erfolgt als nummeriertes Array (Nummerierung ab 1).
   #   Die Reihenfolge entspricht offenbar der 'rex_article.priority'.
   #   $cat              die Kategorie (Objekt)
   #   $act_article      aktueller Artikel (Objekt)
   #   $bas_id           Id des Navigations-Basisartikels
   #   $navtyp           Navigationstyp
   #   benutzte functions:
   #      self::get_path($article,$bas_id)
   #      self::vorfahre($article,$art)
   #
   # --- alle Unterkategorien bestimmen
   $act_id =$act_article->getId();
   $catpath=self::get_path($cat,$bas_id);
   $cat_id =$cat->getId();
   if($act_article->isStartArticle()):
     $par_id=$act_id;
     else:
     $par_id=$act_article->getParentId();
     endif;
   $children=$cat->getChildren();
   #
   # --- die richtigen Unterkategorien auswaehlen
   $m=0;
   $subcat=array();
   for($i=0;$i<count($children);$i=$i+1):
      $child=$children[$i];
      $id=$child->getId();
      $eintrag=0;
      #     die aktuelle Kategorie immer
      if($id==$act_id) $eintrag=1;
      if(self::vorfahre($act_article,$child)):
        #     Kategorien im Ahnenpfad (wichtig fuer die Sortierung: auch Offline-Kategorien)
        $eintrag=1;
        else:
        #     ansonsten nur Online-Unterkategorien in Online-Kategorien
        if($cat->isOnline() and $child->isOnline()):
          #     Onkelkategorien (Navigationstyp = 2/3)
          if($navtyp==2 or $navtyp==3) $eintrag=1;
          #     Unterkategorien bzw. Geschwisterkategorien oder
          #     Unterkategorien des Navigations-Basisartikels (Navigationstyp = 1)
          if($navtyp==1 and ($cat_id==$par_id or $catpath=='|')) $eintrag=1;
          endif;
        endif;
      #
      # --- die ausgewaehlten Unterkategorien speichern
      if($eintrag==1):
         $m=$m+1;
         $subcat[$m]=$child;
         endif;
      endfor;
   return $subcat;
   }
public static function set_artnavpar($article,$nr,$act_id,$act_first,$bas_id,$b_line) {
   #   Rueckgabe der fuer die Navigation noetigen Parameter eines Artikels
   #   als assoziatives Array mit diesen Keys:
   #      [LINE_ID]         Artikel-Id
   #      [LINE_PARENT_ID]  Id der Eltern-Kategorie
   #      [LINE_NAME]       Name des Artikels (rex_article.name)
   #      [LINE_URL]        URL des Artikels (mittels rex_getUrl(id))
   #      [LINE_STATUS]     Status des Artikels (Online/Offline: 1/0)
   #      [LINE_LEVEL]      Level der Navigation (=1,2,3,...)
   #      [LINE_NR]         anfaengliche Nummer der Navigationszeile
   #      [LINE_TYP]        Typ der Navigationszelle:
   #                        =2: aktueller Artikel
   #                        =1: aeltester Ahne
   #                        =0: sonst
   #   $article          gegebener Artikel (Objekt)
   #   $nr               Nummer der Navigationszeile
   #   $act_id           Id des aktuellen Artikels
   #   $act_first        Id des aeltesten Ahnen
   #   $bas_id           Id des Navigations-Basisartikels
   #   $b_line           =TRUE:  Navigations-Basisartikel wird mit angezeigt
   #                     =FALSE: Navigations-Basisartikel wird nicht mit angezeigt
   #   benutzte functions:
   #      self::get_path($article,$bas_id)
   #
   $id=$article->getId();
   $st=$article->isStartArticle();
   $entry=array();
   $entry[LINE_ID]       =$id;
   $entry[LINE_PARENT_ID]=$article->getParentId();
   $entry[LINE_NAME]     =htmlspecialchars($article->getName()); // fuer & < > im Namen
   $entry[LINE_URL]      =rex_getUrl($id);
   $entry[LINE_STATUS]   =$article->isOnline();
   $entry[LINE_LEVEL]    =substr_count(self::get_path($article,$bas_id),'|');
   if(!$b_line and $id!=$bas_id)
     $entry[LINE_LEVEL]  =$entry[LINE_LEVEL]-1;
   $entry[LINE_NR]       =$nr;
   $typ=0;
   if($id==$act_first) $typ=1;
   if($id==$act_id)    $typ=2;
   $entry[LINE_TYP]      =$typ;
   return $entry;
   }
public static function sort($entries) {
   #   Die eingegebenen unsortierten Navigationszeilen werden sortiert und
   #   zurueck gegeben. Die Sortierung erfolgt in 2 Durchlaeufen:
   #   Teil 1:
   #      nach Level aufsteigend und
   #      1) Kindartikel der Startseite bis Urahne des aktuellen Artikels
   #      2) Ahnen des akt. Artikels inkl. akt. Artikel, ggf. inkl. Kindartikel
   #      3) Rest bleibt unsortiert
   #   Teil 2 (der unsortierte Rest):
   #      nach Level absteigend
   #      bei gleichem Level: urspruengliche Reihenfolge (nur hierfuer wird [LINE_NR]
   #                          gebraucht)
   #   $entries          nummeriertes Array der unsortierten Navigationseintraege
   #                     (Nummerierung ab 1); jeder Navigationseintrag ist ein
   #                     assoziatives Array mit diesen Schluesseln:
   #      [LINE_ID]         Artikel-Id
   #      [LINE_PARENT_ID]  Id der Eltern-Kategorie
   #      [LINE_LEVEL]      Level der Navigation (=1,2,3,...)
   #      [LINE_NR]         anfaengliche Nummer der Navigationszeile
   #      [LINE_NAME]       Name des Artikels          (hier nicht benutzt)
   #      [LINE_URL]        URL des Artikels           (hier nicht benutzt)
   #      [LINE_STATUS]     Status des Artikels        (hier nicht benutzt)
   #      [LINE_TYP]        Typ der Navigationszeile   (hier nicht benutzt)
   #
   $entr=$entries;
   #
   # --- Teil 1
   $levmax=0;
   for($i=1;$i<=count($entr);$i=$i+1):
      $lev=$entr[$i][LINE_LEVEL];
      if($lev>$levmax) $levmax=$lev;
      $id=$entr[$i][LINE_ID];
      $m=$i;
      for($k=$i+1;$k<=count($entr);$k=$k+1):
         if($entr[$k][LINE_PARENT_ID]==$id):  // Offline-Kategorien nicht auslassen!!!
           $m=$m+1;
           $ent=$entr[$m];
           $entr[$m]=$entr[$k];
           $entr[$k]=$ent;
           endif;
         endfor;
      endfor;
   #
   # --- Teil 2
   $start=0;
   $level=0;
   for($i=1;$i<=count($entr);$i=$i+1):
      $lev=$entr[$i][LINE_LEVEL];
      if($start<=0 and $lev>=$level):
        $level=$lev;
        if($lev==$levmax) $start=1;
        if($start<=0) continue;
        endif;
      for($k=$i+1;$k<=count($entr);$k=$k+1):
         $lev=$entr[$i][LINE_LEVEL];
         $nu=$entr[$i][LINE_NR];
         $level=$entr[$k][LINE_LEVEL];
         $numm=$entr[$k][LINE_NR];
         if($level>$lev or ($level==$lev and $numm<$nu)):
           $ent=$entr[$i];
           $entr[$i]=$entr[$k];
           $entr[$k]=$ent;
           endif;
         endfor;
      endfor;
   return $entr;
   }
public static function print_line($lines,$increment) {
   #   Rueckgabe aller Navigationszeilen
   #   $lines            nummeriertes Array der Daten der Navigationszeilen, jede
   #                     Zeile ist ein assoziatives Array mit diesen Schluesseln:
   #      [LINE_NAME]       Name des Artikels (rex_article.name)
   #      [LINE_URL]        URL des Artikels
   #      [LINE_LEVEL]      Level der Navigation (=1,2,3,...)
   #      [LINE_TYP]        Typ der Navigationszeile:
   #                        =2: aktueller Artikel
   #                        =1: Ahne der ersten Generation (Hauptkategorie)
   #                        =0: sonst
   #      [LINE_ID]         Artikel-Id                    (hier nicht benutzt)
   #      [LINE_PARENT_ID]  Id der Eltern-Kategorie       (hier nicht benutzt)
   #      [LINE_STATUS]     Status des Artikels           (hier nicht benutzt)
   #      [LINE_NR]         anf. Nr. der Navigationszeile (hier nicht benutzt)
   #   $increment        Einrueckung pro Level in Anzahl Pixel
   #   benutzte functions:
   #      self::hamburger_icon()
   #
   # --- Ausgabestring
   $ausgabe='';
   for($i=1;$i<=count($lines);$i=$i+1):
      $name  =$lines[$i][LINE_NAME];
      $url   =$lines[$i][LINE_URL];
      $level =$lines[$i][LINE_LEVEL];
      $typ   =$lines[$i][LINE_TYP];
      $did   =DIV_TYP.strval($typ);
      $indent=intval(($level-1)*$increment);
      $text  =$name;
      if($typ!=2) $text='<a href="'.$url.'">'.$name.'</a>';
      $style ='margin-left:'.$indent.'px;';
      $class =DIV_BORDER;
      if($i==1) $class=DIV_BORDER1;
      $class =$class.' '.DIV_FORMAT.' '.$did;
      $ausgabe=$ausgabe.'
<div class="'.$class.'"><div style="'.$style.'">'.$text.'</div></div>';
      endfor;
   #
   # --- Zeilen in div-Container mit id NAVIGATION packen
   $ausgabe='<div id="'.NAVIGATION.'">'.$ausgabe.'
</div>';
   #
   # --- Hamburger-/Kreuz-Icon vorne anfuegen
   $ausgabe='
<!----- Start Navigation -------------------------->'.
   self::hamburger_icon().
   $ausgabe.'
<!----- Ende Navigation --------------------------->
';
   return $ausgabe;
   }
public static function print_navigation($basid=0,$bline=FALSE,$actid=0) {
   #   Berechnung und Ausgabe einer automatischen vertikalen Navigation sowie
   #   Rueckgabe der Anzahl der Navigationszeilen.
   #   $basid            Id des Navigations-Basisartikels
   #                     (falls leer: Id des Site-Startartikels)
   #   $bline            =TRUE/FALSE: Basisartikel-Zeile wird angezeigt
   #                     / nicht angezeigt (Default: FALSE)
   #   $actid            Id des aktuellen Artikels (ggf. zu Testzwecken ein
   #                     anderer Artikel, falls 0: Id des aktuellen Artikels)
   #   benutzte functions:
   #      self::redaxo_path($article)
   #      self::get_path($article,$bas_id)
   #      self::articles_of_category($cat,$act_article,$bas_id,$navtyp)
   #      self::subcats_of_category($cat,$act_article,$bas_id,$navtyp)
   #      self::set_artnavpar($article,$nr,$act_id,$act_first,$bas_id,$b_line)
   #      self::sort($entries)
   #      self::print_line($lines,$increment)
   #
   # --- Ueberpruefung der Eingabeparameter
   $bas_id=$basid;
   $b_line=$bline;
   $act_id=$actid;
   #
   #     $basid
   $bas_article=rex_article::get($bas_id);
   #          falls kein Artikel: stattdessen der Site-Startartikel
   if($bas_article==NULL):
     $bas_article=rex_article::getSiteStartArticle();
     $bas_id=$bas_article->getId();
     endif;
   #          falls kein Startartikel: stattdessen sein Elternartikel
   if(!$bas_article->isStartArticle()) $bas_id=$bas_article->getParentId();
   #
   #     $b_line
   if(!empty($b_line)) $b_line=TRUE;
   #
   #     $actid
   if(intval($act_id)<=0) $act_id=rex_article::getCurrentId();
   $act_article=rex_article::get($act_id);
   #          falls kein Artikel: stattdessen der aktuelle Artikel
   if($act_article==NULL):
     $act_id=rex_article::getCurrentId();
     $act_article=rex_article::get($act_id);
     endif;
   #
   # --- konfigurierte Daten: Navigationstyp, Einrueckung in Pixel
   $navtyp   =rex_config::get(NAVIGATION,NAV_TYP);
   $increment=rex_config::get(NAVIGATION,NAV_INDENT);
   #
   # --- weitere Daten zum aktuellen Artikel
   $act_stp =$act_article->isStartArticle();
   $act_path=self::redaxo_path($act_article);
   #
   # --- Elternartikel des aktuellen Artikels
   #     (= Artikel selbst, falls Navigations-Basisartikel)
   $act_pid=$act_article->getParentId();
   if($act_id==$bas_id) $act_pid=$act_id;
   #
   # --- Id-Liste ueber den Pfad des aktuellen Artikels
   #     (bezogen auf den Navigations-Basisartikel)
   $act_baspath=self::get_path($act_article,$bas_id);
   $pathid=explode('|',$act_baspath);
   $act_anzid=count($pathid)-1;
   #
   # --- Id des aeltesten Ahnen des aktuellen Artikels
   $act_first='';
   if($act_anzid>1) $act_first=$pathid[2];
   #
   # --- falls Navigations-Basisartikel mit ausgegeben wird
   $m=0;
   $entries=array();
   if($b_line):
     $m=1;
     $entries[$m]=self::set_artnavpar($bas_article,$m,$act_id,$act_first,$bas_id,$b_line);
     endif;
   #
   # --- Ahnenartikel/-kategorien, Geschwisterartikel/-kategorien, Kindartikel/-kategorien
   #     (inkl. Offline-Artikel/-kategorien)
   #     Start-Sortierung: nach Level aufsteigend
   #     (bei gleichem Level: Artikel vor Geschwisterkategorien)
   for($i=1;$i<=$act_anzid;$i=$i+1):
      $parid=$pathid[$i];
      if($i==$act_anzid and $act_stp==1) $parid=$act_id;
      $cat=rex_category::get($parid);
      if($i<$act_anzid or ($i==$act_anzid and $act_stp==1)):
        #     Geschwisterartikel der Ahnenkategorien bzw. Kindartikel
        $arts=self::articles_of_category($cat,$act_article,$bas_id,$navtyp);
        for($k=1;$k<=count($arts);$k=$k+1):
           $m=$m+1;
           $entries[$m]=self::set_artnavpar($arts[$k],$m,$act_id,$act_first,$bas_id,$b_line);
           endfor;
        #     Ahnenkategorien bzw. Geschwisterkategorien bzw. Kindkategorien
        $siscat=self::subcats_of_category($cat,$act_article,$bas_id,$navtyp);
        for($k=1;$k<=count($siscat);$k=$k+1):
           $m=$m+1;
           $entries[$m]=self::set_artnavpar($siscat[$k],$m,$act_id,$act_first,$bas_id,$b_line);
           endfor;
        endif;
      endfor;
   #
   # --- Sortieren
   $entries=self::sort($entries);
   #
   # --- Herausloeschen der Offline-Pfadkategorien (Letztere notwendig fuer das Sortieren!)
   $m=0;
   $zeilen=array();
   for($i=1;$i<=count($entries);$i=$i+1)
      if($entries[$i][LINE_STATUS]==1):
        $m=$m+1;
        $zeilen[$m]=$entries[$i];
        endif;
   #
   # --- Ausgabe
   echo self::print_line($zeilen,$increment);
   return count($zeilen);
   }
public static function hamburger_icon() {
   #   Rueckgabe des HTML-Codes zur Ausgabe eines Hamburger-Icons/Kreuz-Icons
   #   als Button fuer einen Schalter zum Anzeigen bzw. Verbergen der Navigation.
   #   Der Schalter ist realisiert auf der Basis einer Javascript-function.
   #   Er wird nur auf (schmalen) Smartphone-Displays angezeigt (div-Container
   #   mit Id HAMBURGER bzw. Id KREUZ) und wird mittels CSS-Codes dargestellt.
   #
   return '
<div id="'.HAMBURGER.'">
    <a href="#" title="Navigation anzeigen / verbergen"
       onClick="show_hide(\''.NAVIGATION.'\',\''.HAMBURGER.'\',\''.KREUZ.'\');">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div></a>
</div>
<div id="'.KREUZ.'">
    <a href="#" title="Navigation anzeigen / verbergen"
       onClick="show_hide(\''.NAVIGATION.'\',\''.HAMBURGER.'\',\''.KREUZ.'\');">
        <div class="cross1"></div>
        <div class="cross2"></div>
        <div class="cross3"></div></a>
</div>
';
   }
}
?>
