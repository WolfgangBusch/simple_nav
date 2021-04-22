function show_hide(nav,h_icon,x_icon) {
   /*   Schalter zum Anzeigen/Verbergen des Navigations-Containers id="nav"   */
   var display=document.getElementById(nav).style.display;
   if(display=='' || display=='none') {
     document.getElementById(nav).style.display='block';
     document.getElementById(x_icon).style.display='block';
     document.getElementById(h_icon).style.display='none';
     } else {
     document.getElementById(nav).style.display='none';
     document.getElementById(x_icon).style.display='none';
     document.getElementById(h_icon).style.display='block';
     }
   }