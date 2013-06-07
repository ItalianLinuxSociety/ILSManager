<?php

/*  Copyright (C) Michele Dalla Silvestra
 *  Copyright (C) 2012 - 2013 Roberto Guido <bob@linux.it>
 *
 *  This is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

function archivio_lista($p,$d)
{
  global $UNIXDIR;
  html_pagehead("Archivio documenti",
    "<A HREF=\".\">[Indice]</A>\n");
  if ($dl=scandir($d))
  {
    html_opentable();
    html_tableintest(array("T","Nome file","Dim. (KB)","Ora"));
    if ($p!="" && $p!="/")
      html_tabledata(array("dir","<A HREF=\".?function=archivio&path=".dirname($p)."\">..</A>","",""));
    foreach($dl as $f)
      if ($f!="." && $f!="..")
      {
        $t=filetype($d."/".$f);
        $l="<A HREF=\".?function=archivio&path=".$p."/".$f."\">".$f."</A>&nbsp;";
        $m=date("Y-m-d H:i",filemtime($d."/".$f));
        $s=ceil(filesize($d."/".$f)/1024);
        html_tabledata(array($t,$l,$s,$m));
      }
    html_closetable();
  }
  html_pagetail();
}

function archivio_sendfile($f)
{
#  header("Content-Type: application/gzip");
  header("Content-Disposition: attachment; filename=\"".basename($f)."\"");
  readfile($f);
}


function archivio()
{
  if (!userperm("archivio"))
    header ("Location: .");
  else
  {
    global $UNIXDIR;
    $p1=explode("/",http_getparm("path"));
    while ($k=array_search("..",$p1))
      unset($p1[$k]);
    $p=implode("/",$p1);
    $f=$UNIXDIR["archivio"]."/".$p;
    if (is_dir($f))
      archivio_lista($p,$f);
    else
    if (is_file($f))
      archivio_sendfile($f);
  }
}


?>
