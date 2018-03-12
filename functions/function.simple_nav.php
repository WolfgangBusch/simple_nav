<?php
/**
 * simple Navigation AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Maerz 2018
 */
#
# --- simple_nav-Klassennamen fuer die div-Container
define("DIV_TYP",    "typ");
define("DIV_CLASS",  "simple_nav");
define("DIV_CLASS1", "simple_nav1");
#
function simple_nav($art_id,$bas_id="",$b_line="") {
   #   Berechnung und Ausgabe einer automatischen vertikalen Navigation
   #   $art_id           Id des angezeigten Artikels
   #   $bas_id           Id des Navigations-Basisartikels
   #                     (Default: Site-Startartikel)
   #   $b_line           =TRUE/FALSE: Basisartikel-Zeile wird angezeigt
   #                     / nicht angezeigt (Default)
   #   Konfigurationsparameter Navigationstyp:
   #      Werte =1/2/3 (Default: 2)
   #            =1 (Minimalkonfiguration):
   #               - alle Kindartikel des Startartikels
   #               - alle Kindkategorien des Startartikels ("Hauptkategorien")
   #               - alle Kategorien im Pfad des aktuellen Artikels
   #               - der aktuelle Artikel
   #               - seine Geschwisterartikel/-kategorien [bei online-Elternkategorie] (*)
   #               - seine Kindartikel (*)
   #            =2 (Normalkonfiguration):
   #               - Minimalkonfiguration und zusaetzlich:
   #               - alle Geschwisterartikel im Pfad des aktuellen Artikels (*)
   #            =3 (Maximalkonfiguration):
   #               - Minimalkonfiguration und zusaetzlich:
   #               - alle Geschwisterartikel im Pfad des aktuellen Artikels
   #            (*)  nur in Online-Kategorien
   #   benutzte functions:
   #      simple_nav_default_data()
   #      simple_nav_getpath(&$act_article,$bas_id)
   #      simple_nav_articles_of_category(&$cat,&$act_article,$navtyp)
   #      simple_nav_subcats_of_category(&$cat,&$act_article,$navtyp)
   #      simple_nav_set_artnavpar(&$article,$nr,$art_id,$act_first,$bas_id,$b_line)
   #      simple_nav_sort($entries)
   #      simple_nav_print_line(&$lines,$incr,$increment)
   #
   # --- Ueberpruefung Navigations-Basisartikel
   $article=rex_article::get($bas_id);
   #     falls kein Artikel: stattdessen der Site-Startartikel
   if($article==NULL):
     $article=rex_article::getSiteStartArticle();
     $bas_id=$article->getId();
     endif;
   #     falls kein Startartikel, stattdessen sein Elternartikel
   if(!$article->isStartArticle()) $bas_id=$article->getParentId();
   #
   # --- Ueberpruefung Parameter $b_line
   if(!empty($b_line)) $b_line=TRUE;
   if(empty($b_line))  $b_line=FALSE;
   #
   # --- Konfigurations-Parameter Navigationstyp und Einrueckung
   $data=simple_nav_default_data();
   $keys=array_keys($data);
   $navtyp=rex_config::get(NAVIGATION,$keys[0]);
   if(intval($navtyp)<1 or intval($navtyp)>3) $navtyp=2;
   $increment=rex_config::get(NAVIGATION,$keys[1]);
   if(intval($increment)<=0) $increment=$data[$keys[1]];
   #
   # --- aktueller Artikel (Objekt)
   $act_article=rex_article::get($art_id);
   $act_stp=$act_article->isStartArticle();
   $path   =$act_article->getValue("path");
   #
   # --- Elternartikel des aktuellen Artikels (= Artikel selbst,
   #     falls dieser Basis-Startartikel oder Site-Startartikel)
   $act_pid=$act_article->getParentId();
   if($art_id==$bas_id or $act_id==rex_article::getSiteStartArticleId())
     $act_pid=$act_id;
   #
   # --- Id-Liste ueber den Pfad (Navigations-Basisartikel oder Site-Startartikel)
   $baspath=simple_nav_getpath($act_article,$bas_id);
   $pathid=explode("|",$baspath);
   if(empty($baspath)) $pathid[1]="";
   $act_first=$pathid[2];
   #
   # --- keine Navigation, falls aktueller Elternartikel ausserhalb des Basis-Pfades
   $outside=TRUE;
   for($i=1;$i<count($pathid);$i=$i+1):
     if($pathid[$i]==$act_pid or (empty($pathid[$i]) and $act_pid==$bas_id)) $outside=FALSE;
     if(!$outside) break;
     endfor;
   if($outside) return;
   #
   # --- Basis-/Site-Startartikel mit ausgeben
   $m=0;
   if($b_line):
     $m=1;
     $article=rex_article::get($bas_id);
     # --- falls kein Startartikel, dann dessen Elternartikel
     if(!$article->isStartArticle()) $bas_id=$article->getParentId();
     $entries[$m]=simple_nav_set_artnavpar($article,1,$art_id,$act_first,$bas_id,$b_line);
     if($art_id<>$bas_id)    $entries[$m][typ]=0;
     if($act_first==$bas_id) $entries[$m][typ]=1;
     endif;
   #
   # --- Ahnenkategorien samt Geschwisterkategorien
   #     Sortierung: nach Level aufsteigend
   #     bei gleichem Level: Artikel vor Geschwisterkategorien
   #     inkl. Offline-Artikel/-kategorien
   $anzid=count($pathid)-1;
   for($i=1;$i<=$anzid;$i=$i+1):
      $parid=$pathid[$i];
      if($i==$anzid and $act_stp==1) $parid=$art_id;
      # --- Geschwisterartikel der Ahnenkategorien
      if($i<$anzid or $act_stp==1):
        $cat=rex_category::get($parid);
        $arts=simple_nav_articles_of_category($cat,$act_article,$navtyp);
        for($k=1;$k<=count($arts);$k=$k+1):
           $child=$arts[$k];
           $m=$m+1;
           $entries[$m]=simple_nav_set_artnavpar($child,$m,$art_id,$act_first,$bas_id,$b_line);
           endfor;
        endif;
      # --- Ahnenkategorien samt Geschwisterkategorien
      if($i<$anzid or                       // normale Ahnenkategorie
         ($i==$anzid and $act_stp==1)):     // Kindkategorie der aktuellen Kategorie
        $cat=rex_category::get($parid);
        $siscat=simple_nav_subcats_of_category($cat,$act_article,$navtyp);
        for($k=1;$k<=count($siscat);$k=$k+1):
           $child=$siscat[$k];
           $m=$m+1;
           $entries[$m]=simple_nav_set_artnavpar($child,$m,$art_id,$act_first,$bas_id,$b_line);
           endfor;
        endif;
      endfor;
   #
   # --- Sortieren
   simple_nav_sort($entries);
   #
   # --- Herausloeschen der Offline-Artikel/-Kategorien
   #     (wegen der obigen Sortierlogik erst jetzt)
   $m=0;
   for($i=1;$i<=count($entries);$i=$i+1):
      $status=$entries[$i][status];
      if($status==1):
        $m=$m+1;
        $zeilen[$m]=$entries[$i];
        endif;
      endfor;
   #
   # --- Rueckgabe der Zeilen im Ausgabeformat
   return simple_nav_print_line($zeilen,"",$increment);
   }
function simple_nav_getpath(&$act_article,$bas_id) {
   #   Rueckgabe des Pfads eines Artikels unterhalb des Basis-Startartikels,
   #   falls Letzterer nicht dem Site-Startartikel entspricht
   #   $act_article      der aktuelle Artikel (Objekt)
   #   $bas_id           Id des Basis-Startartikels
   #
   $path=$act_article->getValue("path");
   $ids=explode("|",$path);
   if($bas_id<>rex_article::getSiteStartArticleId()):
     $path="";
     for($i=1;$i<count($ids);$i=$i+1):
        if($ids[$i]==$bas_id) $path="|";
        if(!empty($path)):
          $strid=strval($ids[$i]);
          if(!empty($strid)) $strid=$strid."|";
          $path=$path.$strid;
          endif;
        endfor;
     if(empty($path)) $path="|";
     endif;
   return $path;
   }
function simple_nav_articles_of_category(&$cat,&$act_article,$navtyp) {
   #   Rueckgabe der Kind-Artikel (Objekt) einer Kategorie
   #   (ohne den Startartikel, ohne offline-Artikel)
   #   als nummeriertes Array (Nummerierung ab 1),
   #   Reihenfolge offenbar gemaess 'rex_article.priority'
   #   $cat              die Kategorie (Objekt)
   #   $act_article      der aktuelle Artikel (Objekt)
   #   $navtyp           Navigationstyp
   #
   $catstat=$cat->isOnline();
   $art_id=$act_article->getId();
   #
   # --- $navtyp=1/2: in einigen Faellen sofort return
   if($navtyp==1 or $navtyp==2):
     #     nur Hauptkategorien (Eltern-Pfad == "|")
     #     nur Kindartikel des aktuellen Artikels
     #     nur Geschwisterartikel des aktuellen Artikels
     $catpath=$cat->getValue("path");
     $art_parid=$act_article->getParentId();
     $catid=$cat->getId();
     if($catpath!="|" and $catid!=$art_id and $catid!=$art_parid) return;
     endif;
   #
   # --- Artikel bestimmen
   $articles=$cat->getArticles();
   $m=0;
   for($i=0;$i<count($articles);$i=$i+1):
      $article=$articles[$i];
      $id=$article->getId();
      #
      # --- Startartikel werden anderswo herausgefischt
      if($article->isStartArticle()) continue;
      $stat=$article->isOnline();
      #
      # --- Online-Artikel in Online-Kateg., in Offline-Artikeln nur der aktuelle Artikel
      $eintrag=0;
      if($catstat==1 and $stat==1) $eintrag=1;
      if($catstat==0 and $id==$art_id) $eintrag=1;
      if($eintrag==1):
        $m=$m+1;
        $art[$m]=$article;
        endif;
      endfor;
   return $art;
   }
function simple_nav_subcats_of_category(&$cat,&$act_article,$navtyp) {
   #   Rueckgabe der Unterkategorien (Objekte) einer Kategorie
   #   (ohne Offline-Kategorien, ausser den Offline-Kategorien
   #   in der direkten Ahnenreihe des aktiven Artikels)
   #   als nummeriertes Array (Nummerierung ab 1),
   #   Reihenfolge offenbar gemaess 'rex_article.priority'
   #   $cat              die Kategorie (Objekt)
   #   $act_article      aktueller Artikel (Objekt)
   #   $navtyp           Navigationstyp
   #
   $act_path=$act_article->getValue("path");
   $catstat=$cat->isOnline();
   $catpath=$cat->getValue("path");
   $catid=$cat->getId();
   #
   $art_id=$act_article->getId();
   if($act_article->isStartArticle()):
     $parid=$art_id;
     else:
     $parid=$act_article->getParentId();
     endif;
   #
   $children=$cat->getChildren();
   $m=0;
   for($i=0;$i<count($children);$i=$i+1):
      $article=$children[$i];
      $id=$article->getId();
      $status=$article->isOnline();
      #
      # --- Feststellung, ob der Artikel direkter Vorfahre (ancestor) des
      #     aktuellen Artikels (oder der aktuelle Artikel selbst) ist
      $pathid=$article->getValue("path");
      if($article->isStartArticle()) $pathid=$pathid.$article->getId()."|";
      if($pathid==substr($act_path,0,strlen($pathid))):
        $ancestor=TRUE;
        else:
        $ancestor=FALSE;
        endif;
      #
      # --- Eintraege, abhaengig vom Navigationstyp
      $eintrag=0;
      if($navtyp==1):
        $ein=0;
        #     Online-Hauptkategorien oder Kindkategorien des akt. Artikels
               if($catstat==1 and $status==1 and
                  ($catpath=="|" or $catid==$parid)) $ein=1;
        #     im Ahnenpfad auch Offline-Kategorien
               if($ancestor) $ein=1;
        #     die aktuelle Kategorie immer
               if($id==$art_id) $ein=1;
        $eintrag=$ein;
        endif;
      if($navtyp==2 or $navtyp==3):
        $ein=0;
        #     Online-Unterkategorien in Online-Kategorien
               if($catstat==1 and $status==1) $ein=1;
        #     im Ahnenpfad auch Offline-Kategorien
               if($ancestor) $ein=1;
        #     die aktuelle Kategorie immer
               if($id==$art_id) $ein=1;
        $eintrag=$ein;
        endif;
      #
      # --- Eintrag speichern
      if($eintrag==1):
         $m=$m+1;
         $child[$m]=$article;
         endif;
      endfor;
   return $child;
   }
function simple_nav_set_artnavpar(&$article,$nr,$art_id,$act_first,$bas_id,$b_line) {
   #   Rueckgabe der fuer die Navigation noetigen Parameter eines Artikels:
   #      [id]           Artikel-Id
   #      [re_id]        Id der Eltern-Kategorie
   #      [name]         Name des Artikels (rex_article.name)
   #      [url]          URL des Artikels (mittels rex_getUrl(id))
   #      [status]       Status des Artikels (Online/Offline: 1/0)
   #      [level]        Level der Navigation (=1,2,3,...)
   #      [nr]           anfaengliche Nummer der Navigationszeile
   #      [typ]          Typ der Navigationszelle:
   #                     =2: aktueller Artikel
   #                     =1: Ahne der ersten Generation (Hauptkategorie)
   #                     =0: sonst
   #   $article          gegebener Artikel (Objekt)
   #   $nr               Nummer der Navigationszeile
   #   $art_id           Id des aktuellen Artikels
   #   $act_first        Id des Ahnen der ersten Generation (Hauptkategorie)
   #   $bas_id           Id des Basis-Startartikels
   #   $b_line           =TRUE:  Basis-Startartikel wird mit angezeigt
   #                     =FALSE: Basis-Startartikel wird nicht mit angezeigt
   #   benutzte functions:
   #      simple_nav_getpath($act_article,$bas_id)
   #
   $id=$article->getId();
   $st=$article->isStartArticle();
   $entry[id]    =$id;
   $entry[re_id] =$article->getParentId();
   $entry[name]  =htmlspecialchars($article->getName()); // fuer & < > im Namen
   $entry[url]   =rex_getUrl($id);
   $entry[status]=$article->isOnline();
   $entry[level] =substr_count(simple_nav_getpath($article,$bas_id),"|");
   if(!$b_line and $id!=rex_article::getSiteStartArticleId())
    $entry[level]=$entry[level]-1;
   $entry[nr]    =$nr;
   $typ=0;
   if($id==$act_first) $typ=1;
   if($id==$art_id) $typ=2;
   $entry[typ]=$typ;
   return $entry;
   }
function simple_nav_sort(&$entries) {
   #   Sortieren der Navigationszeilen in 2 Durchlaeufen:
   #   Teil 1:
   #      nach Level aufsteigend und
   #      1) Kindartikel der Startseite bis Urahne des aktuellen Artikels
   #      2) Ahnen des akt. Artikels inkl. akt. Artikel, ggf. inkl. Kindartikel
   #      3) Rest bleibt unsortiert
   #   Teil 2 (der unsortierte Rest):
   #      nach Level absteigend
   #      bei gleichem Level: urspruengliche Reihenfolge
   #   $entries          Array der Navigationseintraege
   #
   # --- Teil 1
   $levmax=0;
   for($i=1;$i<=count($entries);$i=$i+1):
      $lev=$entries[$i][level];
      if($lev>$levmax) $levmax=$lev;
      $id=$entries[$i][id];
      $m=$i;
      for($k=$i+1;$k<=count($entries);$k=$k+1):
         $re_id=$entries[$k][re_id];
         if($entries[$k][re_id]==$id):
           $m=$m+1;
           $ent=$entries[$m];
           $entries[$m]=$entries[$k];
           $entries[$k]=$ent;
           endif;
         endfor;
      endfor;
   #
   # --- Teil 2
   $start=0;
   $level=0;
   for($i=1;$i<=count($entries);$i=$i+1):
      $lev=$entries[$i][level];
      if($start<=0 and $lev>=$level):
        $level=$lev;
        if($lev==$levmax) $start=1;
        if($start<=0) continue;
        endif;
      for($k=$i+1;$k<=count($entries);$k=$k+1):
         $lev=$entries[$i][level];
         $nu=$entries[$i][nr];
         $level=$entries[$k][level];
         $numm=$entries[$k][nr];
         if($level>$lev or ($level==$lev and $numm<$nu)):
           $ent=$entries[$i];
           $entries[$i]=$entries[$k];
           $entries[$k]=$ent;
           endif;
         endfor;
      endfor;
   }
function simple_nav_print_line(&$lines,$incr,$increment) {
   #   Rueckgabe aller Navigationszeilen
   #   $lines            nummeriertes Array der Daten der Navigationszeilen
   #                     jede Zeile ist ein assoziatives Array,
   #                     von denen diese Parameter benutzt werden:
   #      [name]         Name des Artikels (rex_article.name)
   #      [url]          URL des Artikels
   #      [level]        Level der Navigation (=1,2,3,...)
   #      [typ]          Typ der Navigationszeile:
   #                     =2: aktueller Artikel
   #                     =1: Ahne der ersten Generation (Hauptkategorie)
   #                     =0: sonst
   #   $incr             Namenszusatz zu Stylesheet-Klassennamen
   #   $increment        Einrueckung pro Level in Anzahl Pixel
   #
   # --- simple_nav-Klassennamen fuer die div-Container
   $divclass =DIV_CLASS.$incr;
   $divclass1=DIV_CLASS1.$incr;
   #
   # --- Ausgabestring
   $ausgabe="";
   for($i=1;$i<=count($lines);$i=$i+1):
      $name =$lines[$i][name];
      $url  =$lines[$i][url];
      $level=$lines[$i][level];
      $typ  =$lines[$i][typ];
      $did  =DIV_TYP.strval($typ).$incr;
      $indent=intval(($level-1)*$increment);
      $text=$name;
      if($typ!=2) $text="<a href=\"$url\">$name</a>";
      $style="margin-left:".$indent."px;";
      $class=$divclass;
      if($i==1) $class=$divclass1;
      $class=$class." ".$did;
      $ausgabe=$ausgabe."<div class=\"$class\">".
         "<div style=\"$style\">$text</div></div>\n";
      endfor;
   return $ausgabe;
   }
?>
