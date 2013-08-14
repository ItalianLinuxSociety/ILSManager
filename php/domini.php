<?php

/*
 *  Copyright (C) 2013 Roberto Guido <bob@linux.it>
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

function handle_input_data ()
{
  $special = http_getparm ('special_owner');
  if ((int) $special <= 0)
    $owner = $special;
  else
    $owner = http_getparm ('owner');

  $data = array (
    'name' => http_getparm ('name'),
    'owner' => $owner
  );

  return $data;
}

function domains_update ()
{
  $id = http_getparm ('id');

  if (array_key_exists ('confermadati', $_REQUEST) == false || $_REQUEST["confermadati"]!="ok") {
    $r = mysql_query ("SELECT * FROM domains WHERE id = $id");
    $d = mysql_fetch_array ($r);

    domains_addform ($d);
  }
  else {
    $data = handle_input_data ();
    my_update ('domains', $data, 'id', $id);
    header ("Location: ?function=domini&action=list");
  }
}

function domains_addform ($d = null)
{
  if (array_key_exists ('confermadati', $_REQUEST) == false || $_REQUEST["confermadati"]!="ok") {
    html_pagehead ("Edita Dominio", array ('Domini' => 'domini'));

    if ($d != null) {
      $action = 'update';
      $owner = $d ['owner'];
      $id = $d ['id'];

      if ($d ['owner'] > 0) {
        $r = mysql_query ('SELECT nome, cognome, nickname FROM soci_iscritti WHERE id = ' . $d ['owner']);
        $u = mysql_fetch_array ($r);
        $d ['ownername'] = $u ['nome'] . ' ' . $u ['cognome'] . ' (' . $u ['nickname'] . ')';
        $d ['special_owner'] = -2;
      }
      else {
        $d ['ownername'] = '';
        $d ['special_owner'] = $d ['owner'];
      }
    }
    else {
      $action = 'add';

      $d = array (
        'owner' => 0,
        'ownername' => '',
        'special_owner' => -2
      );
    }

    ?>

    <h2>Edita Dominio</h2>

    <?php

    html_openform (".", array ("function" => "domini", "action" => $action, "confermadati" => "ok", 'owner' => $d ['owner'], 'id' => $id));
    html_tableformtext ($d, "Nome (senza .linux.it)", "name", 50);
    html_tableformtext ($d, "Proprietario", "ownername", 50);

    $opts = array (
      array ('val' => '-2', 'text' => 'No'),
      array ('val' => '0', 'text' => 'Uso Interno ILS'),
      array ('val' => '-1', 'text' => '???'),
    );
    html_tableformradio ($d, 'Speciale', 'special_owner', $opts);

    html_tableformsubmit ('Salva');
    html_closeform ();
    html_pagetail ();
  }
  else {
    $data = handle_input_data ();
    my_insert ('domains', $data);
    header ("Location: ?function=domini&action=list");
  }
}

function domain_row ($d)
{
  if ($d ['ownerid'] == 0)
    $owner = 'Uso Interno ILS';
  else if ($d ['ownerid'] == -1)
    $owner = '???';
  else
    $owner = '<a href="?function=sociils&action=iscritti&show=' . $d ["ownerid"] . '">' . $d["owner"] . '</a>';

  $data = array ($d ["name"], $owner);

  if (userperm("anagrafica"))
    $data [] = '<a class="btn" href="?function=domini&action=update&id=' . $d ["domainid"] . '">Modifica</a>';

  html_tabledata ($data);
}

function domains_list ()
{
  html_pagehead("Elenco Completo Domini", array ('Domini' => 'domini'));

  ?>

  <div class="row">
    <div class="span12">

    <?php

    html_opentable(true);

    if (userperm("anagrafica"))
      html_tableintest(array("Nome", "Intestatario", "Azioni"));
    else
      html_tableintest (array ("Nome", "Intestatario"));

    if ($r = mysql_query ("SELECT domains.id as domainid, domains.name AS name, soci_iscritti.id AS ownerid, soci_iscritti.nickname AS owner
	      FROM domains, soci_iscritti
	      WHERE domains.owner = soci_iscritti.id ORDER BY domains.name ASC")) {
      while ($d = mysql_fetch_array ($r))
        domain_row ($d);
    }

    if ($r = mysql_query ("SELECT id as domainid, name, 0 as ownerid FROM domains WHERE owner = 0 ORDER BY name ASC")) {
      while ($d = mysql_fetch_array ($r))
        domain_row ($d);
    }

    if ($r = mysql_query ("SELECT id as domainid, name, -1 as ownerid FROM domains WHERE owner = -1 ORDER BY name ASC")) {
      while ($d = mysql_fetch_array ($r))
        domain_row ($d);
    }

    html_closetable();

    ?>

    </div>
  </div>

  <?php
  html_pagetail();
}

function domains_menu ()
{
  html_pagehead("Domini");

  ?>

  <div class="row">
    <div class="span6">
      <ul class="nav nav-pills nav-stacked">
        <li><a href="?function=domini&action=list">Elenco Completo</a>
        <?php if (userperm("anagrafica")): ?>
          <li><a href="?function=domini&action=add">Aggiungi Nuovo</a>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <?php
  html_pagetail();
}

function domini ()
{
  if (!is_numeric($_SESSION["user"]["idsocio"])) {
    header("Location: .");
  }
  else
  {
    if (array_key_exists ('action', $_REQUEST)) {
      if ($_REQUEST["action"]=="list")
        domains_list ();
      else if ($_REQUEST["action"]=="add" && userperm("anagrafica"))
        domains_addform ();
      else if ($_REQUEST["action"]=="update" && userperm("anagrafica"))
        domains_update ();
    }
    else if (array_key_exists ('regione', $_REQUEST) && $_REQUEST["regione"]!="")
      sociils_listasociregione($_REQUEST["regione"]);
    else
      domains_menu();
  }
}

