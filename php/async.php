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

function async ()
{
  if (userperm("admin") == false)
    return;

  if (array_key_exists ('action', $_GET) == false)
    return;

  switch ($_GET ['action']) {
    case 'socio':
      $ret = array ();
      $name = $_GET ['term'];

      $r = mysql_query ("SELECT * FROM soci_iscritti WHERE (cognome LIKE '%${name}%' OR nome LIKE '%${name}%' OR nickname LIKE '%${name}%') AND data_espulsione=00000000");

      while ($d=mysql_fetch_array($r)) {
        $o = new stdClass ();
        $o->id = $d ['id'];
        $o->label = $o->value = $d ['cognome'] . ' ' . $d ['nome'] . ' (' . $d ['nickname'] . ')';
        $o->form = '<a class="btn" href="?function=contabilita&action=contabilita1&quotasocio=' . $d ['id'] . '">Saldo Quota</a>';
        $ret [] = $o;
      }

      $r = mysql_query ("SELECT * FROM soci_domande WHERE cognome LIKE '%${name}%' OR nome LIKE '%${name}%' OR nickname LIKE '%${name}%'");
      while ($d=mysql_fetch_array($r)) {
        $o = new stdClass ();
        $o->id = $d ['id'];
        $o->label = $o->value = $d ['cognome'] . ' ' . $d ['nome'] . ' (CANDIDATO)';
        $o->form = '<form method="POST"><input type="hidden" name="function" value="contabilita">
                      <input type="hidden" name="action" value="contabilita1">
                      <input type="hidden" name="form" value="approvasocio">
                      <input type="hidden" name="id_socio" value="' . $d ["id"] . '">
                      Anno iscriz.:&nbsp;<input type="text" name="anno" value="' . date("Y") . '" size="8">&nbsp;
                      Quota:&nbsp;<input type="text" name="quota" value="25.00" size="8">&nbsp;
                      <input type="submit" value="Approva">
                    </form>';
        $ret [] = $o;
      }

      echo json_encode ($ret);
      break;

    case 'conto':
      $ret = array ();
      $name = $_GET ['term'];

      $r = mysql_query ("SELECT *, s.id AS id_sottoconto FROM conti_sottoconti AS s LEFT JOIN conti_conti AS c ON c.id = s.id_conto WHERE conto LIKE '%${name}%' OR sottoconto LIKE '%${name}%'");
      while ($d=mysql_fetch_array($r))
      {
        $o = new stdClass ();
        $o->id = $d ['id'];
        $o->label = $o->value = $d ['conto'] . ' / ' . $d ['sottoconto'];
        $o->form = '<form method="POST">
                      <input type="hidden" name="function" value="contabilita">
                      <input type="hidden" name="action" value="contabilita1">
                      <input type="hidden" name="form" value="sottoconti">
                      <input type="hidden" name="sottoconto" value="' . $d["id_sottoconto"] . '">
                      Dare:&nbsp;<input type="text" name="dare" value="" size="8">&nbsp;
                      Avere:&nbsp;<input type="text" name="avere" value="" size="8">&nbsp;
                      <input type="submit" value="Inserisci">
                    </form>';
        $ret [] = $o;
      }

      echo json_encode ($ret);
      break;

    case 'notifiche':
      $today = date ('m-d');

      /*
        Invito pagamento quote anno nuovo
      */
      if ($today == '11-01') {
        $count = 0;
        $year = date ('Y') + 1;
        $q = "SELECT * FROM soci_iscritti
                WHERE id NOT IN (SELECT id_socio FROM soci_quote WHERE YEAR(data_versamento) = $year) AND
                data_espulsione != '0000-00-00'";
        $r = mysql_query ($q);

        while ($d = mysql_fetch_array($r)) {
          $count++;
          $user_name = $s["nome"];
          $user_surname = $s["cognome"];

          $text =<<<TEXT
Gentile $user_surname $user_name,
ti ricordiamo che da ora è possibile procedere con il rinnovo della tua
iscrizione ad Italian Linux Society per l'anno ${year}.


Per completare la procedura e' possibile versare la tua quota, pari a 25 euro

- via bonifico sul conto corrente Unicredit
IT 74 G 02008 12609 000100129899
intestato a
ILS ITALIAN LINUX SOCIETY
specificando nella causale nome e cognome

oppure

- sul conto PayPal ( http://www.paypal.com/ ) intestato a
direttore@linux.it


Segui le attivita' dell'associazione sul sito http://www.ils.org/ e sugli altri
canali di promozione!
http://www.ils.org/contatti

Per qualsiasi domanda contatta la Direzione ILS all'indirizzo mail
direttore@linux.it

Cordiali saluti,
        Il Direttivo ILS

TEXT;

          mail($d["email"],"rinnovo iscrizione ad Italian Linux Society",
            $text, "From: Direttore ILS <direttore@linux.it>\nCc: " . $d['nickname'] . "@linux.it");
        }

        echo "Inviate $count mail di reminder per il pagamento della quota ${year}.";
      }

      break;
  }
}

?>
