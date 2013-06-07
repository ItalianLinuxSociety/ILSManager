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

function showaccessinickname($id)
{
}

function showaccessiutenti()
{
  html_pagehead("Registro accessi", array ('Accessi' => 'accessi'));


  ?>

  <div class="row">
    <div class="span12">

      <?php

      if ($r=mysql_query("select max(ora) as last,ip,a.nickname as nick,i.id as id_socio from users_accesslog as a ".
        "left join soci_iscritti as i on i.nickname=a.nickname group by nick"))
      {
        html_opentable(true);
        html_tableintest(array("Login","Ora","IP"));
        while ($d=mysql_fetch_array($r))
          html_tabledata(array("<a href=\"?function=sociils&action=iscritti&show=".$d["id_socio"]."\">".$d["nick"]."</a>",
            date("Y-m-d H:i",$d["last"]),long2ip($d["ip"])));
        html_closetable();
      }

      ?>

    </div>
  </div>

  <?php

  html_pagetail();
}

function showaccessitime()
{
  html_pagehead("Registro accessi", array ('Accessi' => 'accessi'));

  ?>

  <div class="row">
    <div class="span12">

      <?php

      if ($r=mysql_query("select ora,ip,a.nickname as nick,i.id as id_socio from users_accesslog as a ".
          "left join soci_iscritti as i on i.nickname=a.nickname order by ora desc limit 30"))
      {
        html_opentable(true);
        html_tableintest(array("Login","Ora","IP"));
        while ($d=mysql_fetch_array($r))
          html_tabledata(array("<a href=\"?function=sociils&action=iscritti&show=".$d["id_socio"]."\">".$d["nick"]."</a>",
            date("Y-m-d H:i",$d["ora"]),long2ip($d["ip"])));
        html_closetable();
      }

      ?>

    </div>
  </div>

  <?php

  html_pagetail();
}

function showaccessi()
{
  html_pagehead("Registro accessi");

  ?>

  <div class="row">
    <div class="span6">
      <ul class="nav nav-pills nav-stacked">
        <li><a href="?function=accessi&list=users">Ultimi Accessi per Utente</a>
        <li><a href="?function=accessi&list=time">Accessi Recenti</a>
      <ul>
    </div>
  </div>

  <?php

  html_pagetail();
}



function accessi()
{
  if ($_SESSION["user"]["rules"]["admin"]!="S")
    header("Location: .");
  else
  if (test_value_array ($_REQUEST, 'action', 'show') && is_numeric($id=$_REQUEST["id"]))
    showaccessinickname($id);
  else
  if (test_value_array ($_REQUEST, 'list', 'users'))
    showaccessiutenti();
  else
  if (test_value_array ($_REQUEST, 'list', 'time'))
    showaccessitime();
  else
    showaccessi();
}


?>
