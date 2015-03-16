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

include $UNIXDIR["etc"]."config.php";

include $UNIXDIR["php"]."utils.php";
include $UNIXDIR["php"]."html.php";
include $UNIXDIR["php"]."mysql.php";
include $UNIXDIR["php"]."archivio.php";
include $UNIXDIR["php"]."accessi.php";
include $UNIXDIR["php"]."banche.php";
include $UNIXDIR["php"]."contabilita.php";
include $UNIXDIR["php"]."assemblee.php";
include $UNIXDIR["php"]."sociils.php";
include $UNIXDIR["php"]."domini.php";
include $UNIXDIR["php"]."users.php";
include $UNIXDIR["php"]."async.php";

function authpage()
{
  unset($_SESSION["user"]);
  html_pagehead("Autenticazione");
  ?>

  <div class="row">
    <div class="span4">
      <a href="http://www.linux.it">
        <img src="logo.png" alt="Italian Linux Society" class="pull-right" />
      </a>
    </div>
    <div class="span8">
      <?php
      html_openform(".",array("auth"=>"normale"));
      html_tableformtext(array(),"utente","auth_user",20);
      html_tableformpassw(array(),"password","AUTH_PW",20);
      html_tableformsubmit("Login");
      html_tableformstatic ('', '<a href="?function=rememberpw">Password dimenticata</a>');
      html_closeform();
      ?>
    </div>
  </div>

  <?php html_pagetail() ?>

  <?php
  exit;
}

function menuilsmanager()
{
  html_pagehead('Indice');

  ?>

  <div class="row">
    <div class="span6">
      <ul class="nav nav-pills nav-stacked">

        <?php if (is_numeric($_SESSION["user"]["idsocio"])): ?>
          <li><a href="?function=sociils&action=iscritti&myedit=<?php echo $_SESSION["user"]["login"] ?>">Dati Personali</a></li>
          <li><a href="?function=sociils">Soci ILS</a></li>
          <li><a href="?function=domini">Anagrafe Domini</a></li>
          <li><a href="?function=assemblee">Assemblee Soci<?php assemblee_test_availability() ?></a></li>
        <?php endif; ?>
        <?php if (userperm("banche")): ?>
          <li><a href="?function=banche">Banche</a></li>
        <?php endif; ?>
        <?php if (userperm("banche")): ?>
          <li><a href="?function=contabilita">Contabilita</a></li>
        <?php endif; ?>
        <?php if (userperm("archivio")): ?>
          <li><a href="?function=archivio">Archivio Documenti</a></li>
        <?php endif; ?>

      </ul>
    </div>

    <div class="span6">
      <ul class="nav nav-pills nav-stacked">

      <?php if (userperm("admin")): ?>
        <li><a href="?function=users&action=managerules">Regole Utenti</a></li>
        <li><a href="?function=accessi">Accessi Utenti</a></li>
      <?php endif; ?>
      <?php if (userperm("anagrafe")): ?>
        <li><a href="?function=picardsocidb">Database Soci Picard</a></li>
      <?php endif; ?>
      </ul>
    </div>
  </div>

  <?php
  html_pagetail();
}

function ilsmanager()
{
  if (!isset($_REQUEST["function"]))
    menuilsmanager();
  else if ($_REQUEST["function"]=="sociils")
    sociils();
  else if ($_REQUEST["function"]=="domini")
    domini();
  else if ($_REQUEST["function"]=="assemblee" && is_numeric($_SESSION["user"]["idsocio"]))
    assemblee();
  else if ($_REQUEST["function"]=="banche")
    banche();
  else if ($_REQUEST["function"]=="contabilita")
    contabilita();
  else if ($_REQUEST["function"]=="ricevutepdf" && is_numeric($id=http_getparm("id")))
    contabilita2_ricevutepdf($id);
  else if ($_REQUEST["function"]=="archivio")
    archivio();
  else if ($_REQUEST["function"]=="users")
    usermanager();
  else if ($_REQUEST["function"]=="accessi")
    accessi();
  else if ($_REQUEST["function"]=="picardsocidb")
    sendsocifile();
  else if ($_REQUEST["function"]=="async")
    async();
  else
    menuilsmanager();
}

?>
