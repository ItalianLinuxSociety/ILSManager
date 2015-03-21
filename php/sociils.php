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

function sociils_sociodrawform($d,$subm,$errmsg)
{
  html_tableformerrormsg($errmsg);
  html_tableformtext($d,"Nome","nome",50);
  html_tableformtext($d,"Cognome","cognome",50);
  html_tableformtext($d,"Comune di nascita","comune_nasc",50);
  html_tableformtext($d,"Provincia di nascita","prov_nasc",50);
  html_tableformtext($d,"Data di nascita","data_nasc",12);
  html_tableformtext($d,"Indirizzo di residenza","indirizzo_resid",50);
  html_tableformtext($d,"Comune di residenza","comune_resid",50);
  html_tableformtext($d,"Provincia di residenza","prov_resid",5);
  html_tableformtext($d,"CAP di residenza","cap_resid",50);
  html_tableformtext($d,"Email","email",50);
  html_tableformtext($d,"Codice fiscale","codfis",50);
  html_tableformtext($d,"Data domanda ammissione","data_domanda",12);
  if (array_key_exists ('data_approvazione', $d) && $d["data_approvazione"]!="" && $d["data_approvazione"]!="0000-00-00")
    html_tableformtext($d,"Approvazione (pagam. quota)","data_approvazione",12);
  if (array_key_exists ('data_ammissione', $d) && $d["data_ammissione"]!="" && $d["data_ammissione"]!="0000-00-00")
    html_tableformtext($d,"Ammissione","data_ammissione",12);
  if (array_key_exists ('anno_iscrizione', $d) && $d["anno_iscrizione"]!="")
    html_tableformtext($d,"Anno iscrizione","anno_iscrizione",5);
  if (array_key_exists ('data_espulsione', $d) && $d["data_espulsione"]!="" && $d["data_espulsione"]!="0000-00-00")
    html_tableformtext($d,"Data Espulsione","data_espulsione",12);
  html_tableformtext($d,"Nickname","nickname",50);
  html_tableformselect($d,"Tipo","type", array (array ('privato', 'privato'), array ('associazione', 'associazione'), array ('sostenitore', 'sostenitore')));
  html_tableformtext ($d, "Soci", "members");
  html_tableformtextarea($d,"Note","note");
  html_tableformsubmit($subm);
  html_closeform();
  html_pagetail();
}

function sociils_sociogetform()
{
  $s["nome"]=http_getparm("nome");
  $s["cognome"]=http_getparm("cognome");
  $s["comune_nasc"]=http_getparm("comune_nasc");
  $s["prov_nasc"]=http_getparm("prov_nasc");
  $s["data_nasc"]=http_getparm("data_nasc");
  $s["indirizzo_resid"]=http_getparm("indirizzo_resid");
  $s["comune_resid"]=http_getparm("comune_resid");
  $s["prov_resid"]=http_getparm("prov_resid");
  $s["cap_resid"]=http_getparm("cap_resid");
  $s["email"]=http_getparm("email");
  $s["codfis"]=http_getparm("codfis");
  $s["data_domanda"]=http_getparm("data_domanda");

  if (isset($_REQUEST["data_approvazione"]))
    $s["data_approvazione"]=http_getparm("data_approvazione");

  $s["note"]=http_getparm("note");
  $s["nickname"]=http_getparm("nickname");
  $s["type"]=http_getparm("type");

  if ($s["type"] == 'associazione')
    $s["members"] = http_getparm ("members");
  else
    $s["members"] = 0;

  if (isset($_REQUEST["data_ammissione"]))
    $s["data_ammissione"]=http_getparm("data_ammissione");

  if (isset($_REQUEST["data_espulsione"]))
    $s["data_ammissione"]=http_getparm("data_espulsione");

  if (isset($_REQUEST["anno_iscrizione"]))
    $s["anno_iscrizione"]=http_getparm("anno_iscrizione");

  return $s;
}

function sociils_sociocheckform($s)
{
  if ($s["nome"].$s["cognome"]=="")
    return "Nome e cognome non possono essere entrambi vuoti";
  if ($s["email"]!="" && !is_email($s["email"]))
    return "Indirizzo email non corretto";
  return "";
}

function sociils_sociodrawtable($d)
{
  $p=($_SESSION["user"]["login"]==$d["nickname"] || userperm("anagrafica"));
  html_openform ('');
  html_tableformstatic ("Cognome", $d["cognome"]);
  html_tableformstatic ("Nome", $d["nome"]);

  if ($p) {
    html_tableformstatic ("Comune di nascita",$d["comune_nasc"]);
    html_tableformstatic ("Provincia di nascita",$d["prov_nasc"]);
    html_tableformstatic ("Data di nascita",$d["data_nasc"]);
    html_tableformstatic ("Indirizzo resideza",$d["indirizzo_resid"]);
  }

  html_tableformstatic ("Comune residenza",$d["comune_resid"]);
  html_tableformstatic ("Provincia residenza",$d["prov_resid"]);

  if ($p) {
    if ($d["cap_resid"] != "")
      html_tableformstatic ("CAP residenza",$d["cap_resid"]." ");
    html_tableformstatic ("Email",$d["email"]);
    if ($d["codfis"]!="")
      html_tableformstatic("Codice fiscale",$d["codfis"]." ");
    html_tableformstatic("Data domanda",$d["data_domanda"]);

    if (array_key_exists ('data_approvazione', $d))
      html_tableformstatic("Data approvazione",$d["data_approvazione"]);

    if (array_key_exists ('data_espulsione', $d) && $d["data_espulsione"]!="0000-00-00")
      html_tableformstatic("Data espulsione",$d["data_espulsione"]);

    if (array_key_exists ('anno_iscrizione', $d))
      html_tableformstatic("Anno di iscrizione",$d["anno_iscrizione"]." ");
  }

  if (userperm("anagrafica"))
    html_tableformstatic ("Nickname","<A HREF=\"?function=sociils&action=picard&show=".$d["nickname"]."\">".$d["nickname"]."</A>");
  else
    html_tableformstatic ("Nickname",$d["nickname"]);

  html_tableformstatic ("Tipo", $d["type"]);

  if ($p && $u=mysql_fetch_array(mysql_query("select * from users_picard where nickname=\"".$d["nickname"]."\"")))
    html_tableformstatic("Inoltro @linux.it",$u["fw_email"]);

  if (userperm("anagrafica"))
  {
    if (array_key_exists ('ip_remoto', $d) && $d["ip_remoto"]!=0)
      html_tableformstatic ("IP richiesta",long2ip($d["ip_remoto"]));
    html_tableformstatic ("Note",$d["note"]);
  }

  html_closeform ();
}

function sociils_sociodrawtable_editable($e = '')
{
  $d = mysql_fetch_array(mysql_query("select * from soci_iscritti where id=" . $_SESSION["user"]["idsocio"]));

  ?>

  <div class="row">
    <div class="span8">
      <div id="changePassword" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h3 id="myModalLabel">Cambia Password</h3>
        </div>
        <div class="modal-body">
          <?php

          html_openform(".",array("function"=>"users","action"=>"postchangepw"));
          html_tableformpassw(array(),"Password attuale","old_pw",20);
          html_tableformpassw(array(),"Nuova password","new_pw1",20);
          html_tableformpassw(array(),"Conferma password","new_pw2",20);
          html_closeform();

          ?>
        </div>
        <div class="modal-footer">
          <button class="btn" data-dismiss="modal" aria-hidden="true">Chiudi</button>
          <button class="btn btn-primary save-button">Salva</button>
        </div>
      </div>

      <?php

      html_openform (".", array ("function" => "sociils", "action" => "iscritti", "myedit" => $d["nickname"], "confermadati" => "ok"));
      html_tableformerrormsg ($e);
      html_tableformstatic ("Cognome", $d["cognome"]);
      html_tableformstatic ("Nome", $d["nome"]);
      html_tableformstatic ("Nickname", $d["nickname"]);
      html_tableformstatic ("Comune di Nascita", $d["comune_nasc"] . ' (' . $d["prov_nasc"] . ')');
      html_tableformstatic ("Data di Nascita", printable_date ($d["data_nasc"]));
      html_tableformstatic ("Residenza", $d["indirizzo_resid"] . ', ' . $d["cap_resid"] . ', ' . $d["comune_resid"] . ' (' . $d["prov_resid"] . ')');
      html_tableformstatic ("Codice Fiscale", $d["codfis"]);
      html_tableformstatic ("Anno di Iscrizione", $d["anno_iscrizione"]);
      html_tableformstatic ("Password ILSManager", '<a href="#changePassword" role="button" class="btn" data-toggle="modal">Clicca Qui</a>');

      $p = mysql_fetch_array (mysql_query ("select * from users_picard where nickname=\"".$d["nickname"]."\""));
      if ($p ['fw_email'] != '')
        $p ['mail_mode'] = 'forward';
      else
        $p ['mail_mode'] = 'inbox';

      html_tableformradio ($p, "Modalit&agrave; Mail", "mail_mode",
        array (
          array ('val' => 'forward', 'text' => 'Inoltra'),
          array ('val' => 'inbox', 'text' => 'Casella IMAP')
        ),

        array (
          'forward' => '<span class="help-block">Indirizzo Mail di Destinazione: <input type="text" name="fw_email" value="' . $p ['fw_email'] . '" /></span><span class="help-block">L\'indirizzo @linux.it funge da mail alias, e tutte le mail in ingresso vengono spedite a quest\'altro indirizzo.</span>',
          'inbox' => '<span class="help-block"><a href="?function=users&action=formchangepwpicard">Cambia Password POP3/IMAP</a></span><span class="help-block">Le mail destinate all\'indirizzo @linux.it verranno conservate in una inbox sul server ILS, accessibile via <a href="http://www.linux.it/posta">webmail</a>, o POP3 e IMAP sul server picard.linux.it</span>'
        )
      );

      html_tableformtext ($p, "Feed blog", "blog_feed", 50, 'URL del feed RSS del tuo sito, da includere in <a href="http://planet.linux.it/">Planet ILS</a>');
      html_tableformsubmit ('Salva');
      html_closeform ();
      ?>
    </div>

    <div class="span4">
      <?php

      $re = array ();
      $r = mysql_query("select * from soci_quote as q left join conti_righe as r on r.id=q.id_riga where id_socio=" . $_SESSION["user"]["idsocio"] . " order by anno");
      if ($r2 = mysql_query("select *,r.id as idr from ricevute as r left join conti_movimenti as m on m.id=r.id_movimento where id_socio=" . $_SESSION["user"]["idsocio"] . " order by data,numero"))
        while ($d2 = mysql_fetch_assoc($r2))
          $re[$d2["id_movimento"]] = "<A HREF=\"?function=ricevutepdf&id=" . $d2["idr"] . "\">ric. " . $d2["numero"] . "</A>";

      $last_year = 0;

      html_opentable ();
      html_tableintest (array ("Anno","Data versamento","Ricevute"));

      while ($d1 = mysql_fetch_array ($r)) {
        if (array_key_exists ($d1 ["id_movimento"], $re))
          $mov = $re[$d1["id_movimento"]];
        else
          $mov = '&nbsp;';

        html_tabledata (array ($d1["anno"], printable_date ($d1["data_versamento"]), $mov));

        if ($d1["anno"] > $last_year)
          $last_year = $d1["anno"];
      }

      html_closetable ();
      ?>

      <?php if ($last_year < current_social_year ()): ?>

      <div class="well">
        <p>
          Per rinnovare la tua quota puoi provvedere al versamento di <?php echo annual_fee_amount () ?> euro per mezzo di:
        </p>

        <hr />

        <p>
          Bonifico bancario:
          <pre>Banca: Unicredit Banca
IT 74 G 02008 12609 000100129899
ILS ITALIAN LINUX SOCIETY</pre>
        </p>

        <hr />

        <p>
            PayPal:
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
              <input type="hidden" name="cmd" value="_s-xclick">
              <input type="hidden" name="hosted_button_id" value="V4RCCPUGTJNWQ">
              <input style="width: auto" type="image" src="https://www.paypalobjects.com/it_IT/IT/i/btn/btn_subscribe_LG.gif" border="0" name="submit" alt="PayPal - Il metodo rapido, affidabile e innovativo per pagare e farsi pagare.">
              <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
            </form>
            (questa opzione abilita la sottoscrizione automatica annuale)
        </p>
      </div>

      <?php endif; ?>
    </div>
  </div>

  <?php
}

#===========================================================================


function sociils_nuovadomanda1()
{
  html_pagehead(" nda di ammissione", array ('Soci ILS' => 'sociils', 'Domande ammissione' => 'sociils&action=domande'));

  ?>

  <h2>Incollare la domanda di ammissione</h2>

  <?php

  html_openform(".",array("function"=>"sociils","action"=>"nuovadomanda"));
  html_tableformtextarea(array(),"Incolla il testo della domanda ricevuto via mail","domanda",80,20);
  html_tableformsubmit("Invia");
  html_closeform();
  html_pagetail();
}

function sociils_formdomanda($d,$e)
{
  html_pagehead("Nuova domanda di ammissione", array ('Soci ILS' => 'sociils', 'Domande ammissione' => 'sociils&action=domande'));

  echo "<H2>Verifica dati</H2>\n";
  html_openform(".",array("function"=>"sociils","action"=>"nuovadomanda","domanda"=>"ok","confermadati"=>"ok"));
  sociils_sociodrawform($d,"Conferma dati",$e);
}

function sociils_nuovadomanda2()
{
  $map = array (
    'Nome' => 'nome',
    'Cognome' => 'cognome',
    'Comune di Nascita' => 'comune_nasc',
    'Provincia di Nascita' => 'prov_nasc',
    'Data di Nascita' => 'data_nasc',
    'Indirizzo di Residenza' => 'indirizzo_resid',
    'Comune di Residenza' => 'comune_resid',
    'Provincia di Residenza' => 'prov_resid',
    'CAP di Residenza' => 'cap_resid',
    'Indirizzo Mail' => 'email',
    'Codice Fiscale' => 'codfis',
    'Nickname' => 'nickname'
  );

  $d = array ();

  foreach (explode ("\n", $_REQUEST["domanda"]) as $r)
  {
    if (strstr ($r, ':') == false)
      continue;

    list ($field, $value) = explode (':', $r, 2);

    if (array_key_exists ($field, $map) == false)
      continue;

    $value = trim ($value, "\r ");
    $index = $map [$field];
    $value = mysql_real_escape_string ($value);

    switch ($index) {
      case 'email':
      case 'data_nasc':
      case 'indirizzo_resid':
      case 'cap_resid':
      case 'nickname':
        $value = $value;
        break;

      case 'prov_nasc':
      case 'prov_resid':
        $query = "SELECT sigla_prov FROM it_province WHERE provincia = '$value'";
        $res = mysql_query ($query);
        if (mysql_num_rows ($res) == 1) {
          $p = mysql_fetch_array ($res);
          $value = $p [0];
        }
        break;

      case 'codfis':
        $value = mb_convert_case ($value, MB_CASE_UPPER);
        break;

      default:
        $value = mb_convert_case ($value, MB_CASE_TITLE);
        break;
    }

    $d[$index] = $value;
  }

  if (preg_match ('/\d{2}\/\d{2}\/\d{4}/', $_REQUEST["domanda"], $matches) == 1) {
    list ($m, $da, $y) = explode ('/', $matches [0]);
    $d["data_domanda"] = "$y-$m-$da";
  }

  $d['data_approvazione'] = "0000-00-00";
  $d['data_ammissione'] = "0000-00-00";

  sociils_formdomanda ($d, "");
}

function sociils_nuovadomanda3()
{
  $s=sociils_sociogetform();
  $e=sociils_sociocheckform($s);

  if ($e == '') {
    $query = "SELECT * FROM users_picard WHERE nickname = '" . $s['nickname'] . "'";
    $res = mysql_query ($query);
    if (mysql_num_rows ($res) != 0)
      $e = 'Nickname registrato';
  }

  if ($e!="")
  {
    sociils_formdomanda($s,$e);
  }
  else
  {
    $id = my_insert("soci_domande",$s);

    $user_name = $s["nome"];
    $user_surname = $s["cognome"];
    $fee = annual_fee_amount ();

    $text =<<<TEXT
Gentile $user_surname $user_name,
la tua domanda di iscrizione e' stata registrata sul server di Italian Linux
Society.

Per completare la procedura e' possibile ora versare via bonifico bancario la
quota di iscrizione, pari a $fee euro

- sul conto corrente Unicredit
IT 74 G 02008 12609 000100129899
intestato a
ILS ITALIAN LINUX SOCIETY
specificando nella causale nome, cognome e anno di riferimento

oppure

- sul conto PayPal ( http://www.paypal.com/ ) intestato a
direttore@linux.it

Se hai gia' provveduto, riceverai presto una mail di notifica di avvenuto
versamento e le istruzioni per l'accesso ai servizi a te riservati.

Maggiori informazioni sono disponibili sulla pagina web
http://www.ils.org/iscrizione
o contattando la Direzione ILS all'indirizzo mail
direttore@linux.it

Grazie per esserti unito a noi :-)

Cordiali saluti,
        Il Direttivo ILS

TEXT;

    mail($s["email"],"Registrazione domanda ammissione ILS",
      $text, "From: Direttore ILS <direttore@linux.it>\nBcc: ils-cd@linux.it");

    header("Location: ?function=sociils&action=candidato&show=".$id);
  }
}

function sociils_nuovadomanda()
{
  if (array_key_exists ('domanda', $_REQUEST) == false || $_REQUEST["domanda"] == "")
    sociils_nuovadomanda1();
  else
  if (array_key_exists ('confermadati', $_REQUEST) == false || $_REQUEST["confermadati"]!="ok")
    sociils_nuovadomanda2();
  else
    sociils_nuovadomanda3();
}


#===========================================================


function sociils_candidatosocioform($id)
{
  html_pagehead("Scheda candidato socio", array ('Soci ILS' => 'sociils',
                                                 'Domande ammissione' => 'sociils&action=domande',
                                                 'Scheda' => "sociils&action=candidato&show=$id"));

  $d=mysql_fetch_array(mysql_query("select * from soci_domande where id=$id"));
  html_openform(".",array("function"=>"sociils","action"=>"candidato","edit"=>"$id","confermadati"=>"ok"));
  sociils_sociodrawform($d,"Modifica","");
}

function sociils_candidatosociogetform($id)
{
  $s=sociils_sociogetform();
  $e=sociils_sociocheckform($s);
  if ($e!="")
  {
  html_pagehead("Scheda candidato socio", array ('Soci ILS' => 'sociils',
                                                 'Domande ammissione' => 'sociils&action=domande',
                                                 'Scheda' => "sociils&action=candidato&show=$id"));

    html_openform(".",array("function"=>"sociils","action"=>"candidato","edit"=>$id,"confermadati"=>"ok"));
    sociils_sociodrawform($s,"Modifica",$e);
  }
  else
  {
    $i=my_update("soci_domande",$s,"id",$id);
    header("Location: ?function=sociils&action=candidato&show=".$id);
  }
}

function sociils_candidatosocioaskremove($id)
{
  html_pagehead("Scheda candidato socio", array ('Soci ILS' => 'sociils',
                                                 'Domande ammissione' => 'sociils&action=domande',
                                                 'Scheda' => "sociils&action=candidato&show=$id"));

  $d=mysql_fetch_array(mysql_query("select * from soci_domande where id=$id"));
  html_openform(".",array("function"=>"sociils","action"=>"candidato","remove"=>"$id","confermadati"=>"ok"));
  html_tableformstatic("Nome",$d["cognome"]." ".$d["nome"]);
  html_tableformstatic("Nato","a ".$d["comune_nasc"]." (".$d["prov_nasc"].") il ".$d["data_nasc"]);
  html_tableformstatic("Residente a",$d["comune_resid"]." (".$d["prov_resid"].")");
  html_tableformsubmit("Conferma elminazione");
  html_closeform();
  html_pagetail();
}

function sociils_candidatosocioremove($id)
{
  mysql_query("delete from soci_domande where id=".$id);
  header("Location: ?function=sociils&action=domande");
}

function sociils_candidatosocioaskapprova($id,$f,$err)
{
  html_pagehead("Approvazione candidato socio", array ('Soci ILS' => 'sociils',
                                                       'Domande ammissione' => 'sociils&action=domande',
                                                       'Scheda' => "sociils&action=candidato&show=$id"));

  $d=mysql_fetch_array(mysql_query("select * from soci_domande where id=$id"));
  if (!isset($f["data_approvazione"]))
  {
    $f["data_approvazione"]=date("Y-m-d");
    $f["anno_iscrizione"]=date("Y");
    $f["importo_quota"] = annual_fee_amount ();
  }
  html_openform(".",array("function"=>"sociils","action"=>"candidato","approva"=>"$id","confermadati"=>"ok"));
  html_tableformerrormsg($err);
  html_tableformstatic("Nome",$d["cognome"]." ".$d["nome"]);
  html_tableformstatic("Nato","a ".$d["comune_nasc"]." (".$d["prov_nasc"].") il ".$d["data_nasc"]);
  html_tableformstatic("Residente a",$d["comune_resid"]." (".$d["prov_resid"].")");
  html_tableformtext($f,"Approvazione (pagam. quota)","data_approvazione",12);
  html_tableformtext($f,"Anno iscrizione","anno_iscrizione",12);
  html_tableformtext($f,"Importo quota","importo_quota",12);
  html_tableformsubmit("Conferma approvazione");
  html_closeform();
  html_pagetail();
}

function sociils_candidatosocioapprovasql($id,$dataappr,$anno,$quota)
{
  $d=mysql_fetch_assoc(mysql_query("select * from soci_domande where id=$id"));
  $d["data_approvazione"]=$dataappr;
  $d["anno_iscrizione"]=$anno;
  $q=$quota;
  mysql_query("delete from soci_domande where id=".$id);
  unset($d["ip_remoto"]);
  unset($d["id"]);
  $new_id = my_insert("soci_iscritti",$d);

  $user_name = $d["nome"];
  $user_surname = $d["cognome"];
  $nickname = $d["nickname"];
  $email = $d["email"];

  $nc["nickname"] = $nickname;
  $nc["cognome"] = $user_surname;
  $nc["nome"] = $user_name;
  $nc["attivo"] = 'S';
  $nc["fw_email"] = $email;
  $nc["password"] = mdspwgen(random_string ());
  $nc["pw_picard"] = $d["password"];
  $nc["cambiopw"] = date ("Y-m-d");
  $nc = sociils_picardnormalizedata ($nc);
  sociils_picardcreateaccount ($new_id, $nc);

  $final_name = $nc ['nome'];
  $final_surname = $nc ['cognome'];

  $text =<<<TEXT
Gentile $user_surname $user_name,
e' stato registrato il versamento della quota di iscrizione ad Italian Linux
Society e quindi adesso sei un candidato socio a tutti gli effetti.

L'ammissione definitiva avverra' nel corso della prossima assemblea dei soci,
presso cui sarai invitato a partecipare, ma nel frattempo nelle prossime ore
saranno abilitati i tuoi indirizzi mail @linux.it e sei stato iscritto alla
nostra mailing list privata.

Entrambi gli indirizzi
${final_name}.${final_surname}@linux.it e
${nickname}@linux.it
verranno settati entro poche ore come alias all'indirizzo
${email}
E' possibile modificare tale impostazione per mezzo di ILSManager, il nostro
gestionale interno, raggiungibile all'indirizzo https://ilsmanager.linux.it/
(cui si accede sempre con lo username "${nickname}", effettua il
Recupero Password per definire la tua password).

Come sempre, per qualsiasi dubbio o domanda rivolgiti a direttore@linux.it

Cordiali saluti,
        Il Direttivo ILS

TEXT;

  mail($d["email"],"Approvazione domanda ammissione ILS",
    $text, "From: Direttore ILS <direttore@linux.it>\nBcc: ils-cd@linux.it");
  $d["id"]=mysql_insert_id();
  if (substr($d["data_approvazione"],0,4)==$d["anno_iscrizione"])
  {
    $m["data"]=$d["data_approvazione"];
    $m["descrizione"]="Iscrizione nuovo socio ".$d["cognome"]." ".$d["nome"];
    my_insert("conti_movimenti",$m);
    $m["id"]=mysql_insert_id();
    $s1=mysql_fetch_assoc(mysql_query("select * from conti_sottoconti where sottoconto like \"Conferimenti socio %\" and id_socio=".$d["id"]));
    if ($s1["sottoconto"]=="")
    {
      $c=mysql_fetch_assoc(mysql_query("select * from conti_conti where conto=\"Crediti conferimento nostri associati\""));
      unset($s1);
      $s1["id_conto"]=$c["id"];
      $s1["id_socio"]=$d["id"];
      $s1["sottoconto"]="Conferimenti socio ".$d["cognome"]." ".$d["nome"];
      my_insert("conti_sottoconti",$s1);
      $s1["id"]=mysql_insert_id();
    }
    $s2=mysql_fetch_assoc(mysql_query("select * from conti_sottoconti where sottoconto=\"Contributi associativi ".$d["anno_iscrizione"]."\""));
    $r1["id_sottoconto"]=$s1["id"];
    $r1["id_movimento"]=$m["id"];
    $r1["valuta"]=$m["data"];
    $r1["dare"]=$q;
    $r1["avere"]=0;
    $r2["id_sottoconto"]=$s2["id"];
    $r2["id_movimento"]=$m["id"];
    $r2["valuta"]=$m["data"];
    $r2["dare"]=0;
    $r2["avere"]=$q;
    my_insert("conti_righe",$r1);
    my_insert("conti_righe",$r2);
  }
  return $d;
}

function sociils_candidatosocioapprova($id)
{
  $dataappr=http_getparm("data_approvazione");
  $anno=http_getparm("anno_iscrizione");
  $quota=http_getparm("importo_quota");
  sociils_candidatosocioapprovasql($id,$dataappr,$anno,$quota);
  header("Location: ?function=sociils&action=domande");
}

function sociils_candidatosocio($id)
{
  html_pagehead("Scheda candidato socio", array ('Soci ILS' => 'sociils',
                                                 'Domande ammissione' => 'sociils&action=domande'));

  $q = '';
  if (!userperm("anagrafica"))
    $q=" and data_approvazione>00000000";

  if ($d=mysql_fetch_array(mysql_query("select * from soci_domande where id=".$id.$q)))
  {
    sociils_sociodrawtable($d);

    if (userperm("anagrafica")) {
      ?>

      <a href="?function=sociils&action=candidato&edit="<?php echo $d["id"] ?>" class="btn btn-primary">Modifica</a>
      <a href="?function=sociils&action=candidato&remove="<?php echo $d["id"] ?>" class="btn btn-primary">Elimina</a>

      <?php
    }
  }

  html_pagetail ();
}

function sociils_domandeammissione()
{
  html_pagehead("Domande di ammissione", array ('Soci ILS' => 'sociils'));

  ?>

  <div class="row">
    <div class="span6">

    <?php

    if ($r=mysql_query("select * from soci_iscritti where data_ammissione=00000000 order by data_approvazione"))
    {
      echo "<H2>Soci da ammettere</H2>\n";
      html_opentable(true);
      html_tableintest(array("Nome","Citt&agrave;","Data approvazione"));
      while ($d=mysql_fetch_array($r))
        html_tabledata(array(
          "<A HREF=\"?function=sociils&action=iscritti&show=".$d["id"]."\">".$d["cognome"]." ".$d["nome"]."</A>",
          $d["comune_resid"]." (".$d["prov_resid"].")",$d["data_approvazione"]));
      html_closetable();
    }

    ?>

    </div>
    <div class="span6">

    <?php

    if (userperm("anagrafica") && $r=mysql_query("select * from soci_domande order by data_domanda,cognome,nome"))
    {
      echo "<H2>Domande in attesa di approvazione</H2>\n";
      html_opentable(true);
      html_tableintest(array("Nome","Citt&agrave;","Data domanda"));
      while ($d=mysql_fetch_array($r))
        html_tabledata(array(
          "<A HREF=\"?function=sociils&action=candidato&show=".$d["id"]."\">".$d["cognome"]." ".$d["nome"]."</A>",
          $d["comune_resid"]." (".$d["prov_resid"].")",$d["data_domanda"]));
      html_closetable();
    }
    if (userperm("anagrafica")) {
      ?>
      <a class="btn pull-right" href="?function=sociils&action=nuovadomanda">Nuova domanda di ammissione</a>
      <?php
    }

  ?>

    </div>
  </div>

  <?php
  html_pagetail();
}


#===========================================================


function sociils_espulsioneform($id,$d,$err)
{
  html_pagehead("Espulsione socio", array ('Soci ILS' => 'sociils', 'Scheda' => "sociils&action=iscritti&show=$id"));

  if ($d["data_espulsione"]=="0000-00-00")
    $d["data_espulsione"]=sprintf("%d",date("Y")-1)."-12-31";
  $q=mysql_fetch_assoc(mysql_query("select max(anno) as ultimo from soci_quote where id_socio=".$id));
  html_openform(".",array("function"=>"sociils","action"=>"espulsione","id"=>"$id","confermadati"=>"ok"));
  html_tableformerrormsg($err);
  html_tableformstatic("Nome","<A HREF=\"?function=sociils&action=iscritti&show=$id\">".$d["cognome"]." ".$d["nome"]."</A>");
  html_tableformstatic("Anno iscrizione",$d["anno_iscrizione"]);
  html_tableformstatic("Ultima quota",$q["ultimo"]);
  html_tableformtext($d,"Data espulsione","data_espulsione",12);
  html_tableformsubmit("Conferma espulsione");
  html_closeform();
  html_pagetail();
}


function sociils_espulsioneok($id)
{
  if (($d=mysql_fetch_assoc(mysql_query("select * from soci_iscritti where id=$id"))) &&
    ($d["data_espulsione"]=="0000-00-00") && $e=http_getparm("data_espulsione"))
  {
    if (date("Y-m-d",strtotime($e))!=$e)
      sociils_espulsioneform($id,$d,"data non valida");
    else
    {
      mysql_query("update soci_iscritti set data_espulsione=\"$e\" where id=$id");
      header("Location: ?function=sociils&action=iscritti&show=$id");
    }
  }
  else
    header("Location: ?function=sociils&action=iscritti&show=$id");
}

function sociils_espulsioneask($id)
{
  if (($d=mysql_fetch_assoc(mysql_query("select * from soci_iscritti where id=$id"))) &&
    ($d["data_espulsione"]=="0000-00-00"))
    sociils_espulsioneform($id,$d,"");
  else
    header("Location: ?function=sociils&action=iscritti&show=$id");
}


#===============================================================


function sociils_iscrittoform($id)
{
  html_pagehead("Scheda socio", array ('Soci ILS' => 'sociils',
                                       'Elenco Soci' => 'sociils&action=elenco',
                                       'Scheda' => "sociils&action=iscritti&show=$id"));

  $d=mysql_fetch_array(mysql_query("select * from soci_iscritti where id=$id"));
  html_openform(".",array("function"=>"sociils","action"=>"iscritti","edit"=>"$id","confermadati"=>"ok"));
  sociils_sociodrawform($d,"Modifica","");
}

function sociils_iscrittogetform($id)
{
  $s=sociils_sociogetform();
  $e=sociils_sociocheckform($s);
  if ($e!="")
  {
    html_pagehead("Scheda socio", array ('Soci ILS' => 'sociils',
                                         'Elenco Soci' => 'sociils&action=elenco',
                                         'Scheda' => "sociils&action=iscritti&show=$id"));

    html_openform(".",array("function"=>"sociils","action"=>"iscritti","edit"=>$id,"confermadati"=>"ok"));
    sociils_sociodrawform($s,"Modifica",$e);
  }
  else
  {
    $i=my_update("soci_iscritti",$s,"id",$id);
    header("Location: ?function=sociils&action=iscritti&show=".$id);
  }
}

function sociils_iscrittoaskammetti($id,$f,$err)
{
  html_pagehead("Ammissione Socio", array ('Soci ILS' => 'sociils',
                                           'Domande Ammissione' => 'sociils&action=domande',
                                           'Scheda' => "sociils&action=iscritti&show=$id"));

  $m=mysql_fetch_array(mysql_query("select max(data_ammissione) as amm from soci_iscritti"));
  $d=mysql_fetch_array(mysql_query("select * from soci_iscritti where id=$id"));
  if (!isset($f["data_ammissione"]))
    $f["data_ammissione"]=$m["amm"];
  html_openform(".",array("function"=>"sociils","action"=>"iscritti","ammetti"=>"$id","confermadati"=>"ok"));
  html_tableformerrormsg($err);
  html_tableformstatic("Nome",$d["cognome"]." ".$d["nome"]);
  html_tableformstatic("Nato","a ".$d["comune_nasc"]." (".$d["prov_nasc"].") il ".$d["data_nasc"]);
  html_tableformstatic("Residente a",$d["comune_resid"]." (".$d["prov_resid"].")");
  html_tableformstatic("Approvazione",$d["data_approvazione"]);
  html_tableformtext($f,"Ammissione","data_ammissione",12);
  html_tableformsubmit("Conferma ammissione");
  html_closeform();
  html_pagetail();
}

function sociils_iscrittoammetti($id)
{
  $d["data_ammissione"]=http_getparm("data_ammissione");
  my_update("soci_iscritti",$d,"id",$id);
  $d=mysql_fetch_assoc(mysql_query("select * from soci_iscritti where id=$id"));
  header("Location: ?function=sociils&action=domande");
}

function sociils_iscritto($id)
{
  html_pagehead("Scheda socio", array ('Soci ILS' => 'sociils', 'Elenco Soci' => 'sociils&action=elenco'));

  if (!userperm("anagrafica"))
    $q=" and data_espulsione=00000000";
  else
    $q = '';

  if ($d=mysql_fetch_array(mysql_query("select * from soci_iscritti where id=".$id.$q)))
  {
    ?>

    <div class="row">
      <div class="span6">

        <?php
          sociils_sociodrawtable($d);
        ?>

        <?php if (userperm("anagrafica")): ?>
          <a href="?function=sociils&action=iscritti&edit=<?php echo $d['id'] ?>" class="btn btn-primary">Modifica</a>
          <?php if ($d["data_ammissione"]=="0000-00-00"): ?>
            <a href="?function=sociils&action=iscritti&ammetti=<?php echo $d['id'] ?>" class="btn">Ammetti</a>
          <?php endif; ?>

          <?php $q = "select * from conti_sottoconti as s left join conti_conti as c on c.id=s.id_conto where id_socio=".$id." and conto like \"Debiti vs soci%\"";
          if (userperm("banche") && mysql_num_rows(mysql_query($q))==0): ?>
            <a href="?function=contabilita&action=newscrimborsospese&id_socio=<?php echo $id ?>" class="btn">Conto Rimborso Spese</a>
          <?php endif; ?>
        <?php endif; ?>

      </div>

      <div class="span6">
        <div class="well">

          <?php if (mysql_num_rows($r=mysql_query("select * from lug_soci as s left join lug_anagrafe as l on l.id=s.id_lug where s.id_socio=$id"))>0): ?>
            <?php while ($d=mysql_fetch_assoc($r)): ?>
              <p>
                <a href="?function=lug&show=<?php echo $d['id_lug'] ?>"><?php echo $d["nome"] ?></a>

                <?php if ($id==$_SESSION["user"]["idsocio"]): ?>
                  <a href="?function=lug&partecipa=del&id_lug=<?php echo $d['id_lug'] ?>">[rimuovi]</a>
                <?php endif; ?>
              </p>
            <?php endwhile; ?>
          <?php else: ?>
            <p>
              Nessun LUG associato
            </p>
          <?php endif; ?>

          <?php if (userperm("anagrafica") && array_key_exists ('lug', $_SESSION) && is_numeric($_SESSION["lug"]["socioils"]) && is_numeric($d["codfis"])): ?>
            <a href="?function=lug&action=setlug&id_socio=<?php echo $id ?>">associa LUG</a>
          <?php endif; ?>

        </div>

        <?php if (userperm("admin") && array_key_exists ('rules', $_SESSION) && is_numeric($rid=$_SESSION["rules"]["addto"]) && mysql_num_rows(mysql_query("select * from users_perm where id_rules=$rid && username=\"".$d["nickname"]."\""))==0 && $r=mysql_fetch_array(mysql_query("select * from users_rules where id=$rid"))): ?>
          <a href="?function=users&action=managerules&addto=<?php echo $rid ?>&user=<?php echo $d['nickname'] ?>">Permetti <?php echo $r["rule"] ?></a>
        <?php endif; ?>

        <?php if ((userperm("anagrafica") || userperm("banche") || $_SESSION["user"]["login"]==$d["nickname"]) && $r=mysql_query("select * from soci_quote as q left join conti_righe as r on r.id=q.id_riga where id_socio=".$d["id"]." order by anno")): ?>
          <?php

            $re = array ();

            if ($r2=mysql_query("select *,r.id as idr from ricevute as r left join conti_movimenti as m on m.id=r.id_movimento where id_socio=$id order by data,numero"))
              while ($d2=mysql_fetch_assoc($r2))
                $re[$d2["id_movimento"]]="<a href=\"?function=ricevutepdf&id=".$d2["idr"]."\">ric. ".$d2["numero"]."</a>";

            html_opentable();

            if (userperm("anagrafica") || userperm("banche")) {
              html_tableintest(array("Anno","Data versamento","Ricevute","Note"));
              while ($d1=mysql_fetch_array($r))
              {
                if (($d1["id_riga"]>0) && userperm("banche"))
                  $data="<a href=\"?function=contabilita&action=movimenti&show=".$d1["id_movimento"]."\">".$d1["data_versamento"]."</a>";
                else
                  $data=$d1["data_versamento"];

                if (array_key_exists ($d1["id_movimento"], $re))
                  $ricevuta = $re [$d1["id_movimento"]];
                else
                  $ricevuta = '';

                html_tabledata(array($d1["anno"], $data,$ricevuta, $d1["note"]));
              }
            }
            else {
              html_tableintest(array("Anno","Data versamento","Ricevute"));

              while ($d1=mysql_fetch_array($r)) {
                if (array_key_exists ($d1["id_movimento"], $re))
                  $link = $re[$d1["id_movimento"]];
                else
                  $link = '';

                html_tabledata(array($d1["anno"],$d1["data_versamento"],$link));
              }
            }
            html_closetable();

          ?>

          <?php if (userperm("anagrafica") && ($d["data_espulsione"]=="0000-00-00")): ?>
          <a href="?function=sociils&action=espulsione&id=<?php echo $d['id'] ?>">Espulsione</a>
          <?php endif; ?>
        <?php endif; ?>

      </div>
    </div>

    <?php
  }
  html_pagetail();
}

function sociils_iscritto_myedit ()
{
  html_pagehead("Scheda socio");

  if (test_request_param ('confermadati', 'ok') == false)
  {
    sociils_sociodrawtable_editable();
  }
  else
  {
    $e = "";
    $mail_mode = http_getparm ("mail_mode");

    if ($mail_mode == 'forward') {
      $d["fw_email"] = http_getparm ("fw_email");
    }
    else {
      $pw = http_getparm ("passw1");

      if ($pw != "") {
        if ($pw != http_getparm ("passw2")) {
          $e = "Password non coincidenti";
        }
        else {
          $d["pw_picard"] = mdspwgen ($pw);
          $d["cambiopw"] = date ("Y-m-d");
        }
      }

      /*
        Nel file dei soci esportato verso Picard, la casella di posta
        viene abilitata se manca un indirizzo di forward
      */
      $d["fw_email"] = '';
    }

    $d["blog_feed"] = http_getparm ("blog_feed");

    if ($e != "") {
      sociils_sociodrawtable_editable ($e);
    }
    else
    {
      my_update ("users_picard", $d, "nickname", "\"" . $_SESSION["user"]["login"] . "\"");
      exec ('sudo /usr/local/bin/syncpicard.sh');
      header ("Location: /");
    }
  }

  html_pagetail();
}

function sociils_elencosoci()
{
  html_pagehead("Elenco soci ammessi", array ('Soci ILS' => 'sociils'));

  ?>

  <div class="row">
    <?php

    mysql_query("create temporary table tmp_ultimaquota select max(anno) as regola,id_socio from soci_quote group by id_socio");
    if (!userperm("anagrafica") && !userperm("banche")) {
      $q=" where data_espulsione=00000000";
    }
    else {
      $q = '';

      ?>
      <div class="span12">
        <a class="btn pull-right showallhidden">Mostra/Nascondi Soci Espulsi</a>
      </div>
      <?php
    }

    ?>

    <div class="span12">

    <?php

    if ($r=mysql_query("select * from soci_iscritti as s left join tmp_ultimaquota as u on u.id_socio=s.id $q order by cognome,nome,nickname"))
    {
      html_opentable(true);

      if (userperm("anagrafica"))
        html_tableintest(array("Nome","Citt&agrave;","Nickname","Data ammissione","Data espulsione","Quota"));
      else
        html_tableintest(array("Nome","Citt&agrave;","Nickname"));

      while ($d=mysql_fetch_array($r))
      {
        if ($d["data_espulsione"]=="0000-00-00") {
          $d["data_espulsione"]="&nbsp;";
          $class = '';
        }
        else
          $class = 'hide';

        if (userperm("anagrafica"))
          html_tabledata(array(
            "<a href=\"?function=sociils&action=iscritti&show=".$d["id"]."\">".$d["cognome"]." ".$d["nome"]."</a>",
            $d["comune_resid"]." (".$d["prov_resid"].")",
            $d["nickname"], $d["data_ammissione"], $d["data_espulsione"], $d["regola"]), $class);
        else
          html_tabledata(array(
            "<a href=\"?function=sociils&action=iscritti&show=".$d["id"]."\">".$d["cognome"]." ".$d["nome"]."</a>",
            $d["comune_resid"]." (".$d["prov_resid"].")",$d["nickname"]), $class);
      }
      html_closetable();
    }

    ?>

    </div>
  </div>

  <?php
  html_pagetail();
}


#===============================================================


function sociils_picarddrawform($d,$s,$e)
{
  html_pagehead("Elenco Utenti Picard", array ('Soci ILS' => 'sociils',
                                               'Utenti Picard' => 'sociils&action=picard',
                                               'Scheda Account' => "sociils&action=picard&show=" . $d["nickname"]));

  html_openform(".",array("function"=>"sociils","action"=>"picard","edit"=>$d["nickname"],"confermadati"=>"ok"));
  html_tableformerrormsg($e);
  html_tableformtext($d,"Nickname","nickname",50);
  html_tableformtext($d,"Nome","nome",50);
  html_tableformtext($d,"Cognome","cognome",50);
  html_tableformtext($d,"Attivo","attivo",1);
  html_tableformtext($d,"Sospeso","sospeso",12);
  html_tableformtext($d,"Inoltro email","fw_email",50);
  html_tableformtext($d,"Homepage","homepage",50);
  html_tableformtext($d,"Feed blog","blog_feed",50);
  html_tableformtextarea($d,"Note","note");
  html_tableformsubmit($s);
  html_closeform();
  html_pagetail();
}

function sociils_picardgetform()
{
  $s["nickname"]=http_getparm("nickname");
  $s["nome"]=http_getparm("nome");
  $s["cognome"]=http_getparm("cognome");
  $s["attivo"]=http_getparm("attivo");
  $s["sospeso"]=http_getparm("sospeso");
  $s["fw_email"]=http_getparm("fw_email");
  $s["homepage"]=http_getparm("homepage");
  $s["blog_feed"]=http_getparm("blog_feed");
  $s["note"]=http_getparm("note");
  return $s;
}

function picard_aliasexist($search,$exclude)
{
  $r=mysql_query("select * from users_picard where (nickname=\"".$search."\" or cognome=\"".$search."\" or concat(nome,\".\",cognome)=\"".$search."\")".
    " and nickname!=\"".$exclude."\"");
  return (mysql_num_rows($r)>0);
}

function picard_aliasexist2($search)
{
  $r=mysql_query("select * from users_picard where (nickname=\"".$search."\" or cognome=\"".$search."\" or concat(nome,\".\",cognome)=\"".$search."\")");
  return (mysql_num_rows($r)>0);
}

function picard_aliasexist3($search)
{
  $r=mysql_query("select * from users_picard where (nickname=\"".$search."\" or concat(nome,\".\",cognome)=\"".$search."\")");
  return (mysql_num_rows($r)>0);
}

function sociils_picardcheckform($s,$n)
{
  if (picard_aliasexist($s["nickname"],$n))
    return "Nickname \"".$s["nickname"]."\" esistente";
/*
  if ($s["cognome"]!="XXX" && picard_aliasexist($s["cognome"],$n))
    return "Conflitto tra cognome e altri alias di posta";
*/
  if ($s["nome"]!="XXX" && picard_aliasexist($s["nome"].".".$s["cognome"],$n))
    return "Conflitto tra nome.cognome e altri alias di posta";
  if ($s["fw_email"]!="" && !is_email($s["fw_email"]))
    return "Email per l'inoltro non valida";
  return "";
}

function sociils_picardedit($n)
{
  if ($_REQUEST["confermadati"]!="ok")
  {
    if ($d=mysql_fetch_array(mysql_query("select * from users_picard where nickname=\"".$n."\"")))
      sociils_picarddrawform($d,"Modifica","");
  }
  else
  {
    $n=http_getparm("edit");
    $s=sociils_picardgetform();
    $e=sociils_picardcheckform($s,$n);
    if ($e=="")
    {
      my_update("users_picard",$s,"nickname","\"".$n."\"");
      my_update("soci_iscritti",array("nickname"=>$s["nickname"]),"nickname","\"".$n."\"");
      if ($_SESSION["user"]["login"]==$n)
        $_SESSION["user"]["login"]=$s["nickname"];
      header("Location: ?function=sociils&action=picard&show=".$s["nickname"]);
    }
    else
    {
      $s["nickname"]=$n;
      sociils_picarddrawform($s,"Modifica",$e);
    }
  }
}

function sociils_picardpwform($n,$s,$e)
{
  html_pagehead("Cambio Password Utente", array ('Soci ILS' => 'sociils',
                                                 'Utenti Picard' => 'sociils&action=picard',
                                                 'Scheda account' => "sociils&action=picard&show=$n"));

  html_openform(".",array("function"=>"sociils","action"=>"picard","chpw"=>$n,"confermadati"=>"ok"));
  html_tableformerrormsg($e);
  html_tableformstatic("Nickname",$n);
  html_tableformpassw($d,"Password","passw1",50);
  html_tableformpassw($d,"Verifica password","passw2",50);
  html_tableformsubmit($s);
  html_closeform();
  html_pagetail();
}

function sociils_picardchpw($n)
{
  if (http_getparm("confermadati")=="")
    sociils_picardpwform($n,"Invia","");
  else
  {
    if (($pw=http_getparm("passw1"))=="")
      $e="Password vuota";
    if ($pw!=http_getparm("passw2"))
      $e="Password non coincidenti";
    $d["password"]=mdspwgen($pw);
    $d["pw_picard"]=$d["password"];
    $d["cambiopw"]=date("Y-m-d");
    if ($e!="")
      sociils_picardpwform($n,"Invia",$e);
    else
    {
      my_update("users_picard",$d,"nickname","\"".$n."\"");
      header("Location: ?function=sociils&action=picard&show=".$n);
    }
  }
}

function sociils_picardshowexist($d)
{
  html_pagehead("Utente Picard", array ('Soci ILS' => 'sociils', 'Utenti Picard' => 'sociils&action=picard'));

  $s=mysql_fetch_array(mysql_query("select * from soci_iscritti where nickname=\"".$d["nickname"]."\""));

  html_openform ('');
  html_tableformstatic("Nickname",$d["nickname"]);
  html_tableformstatic("Nome",$d["nome"]);
  html_tableformstatic("Cognome",$d["cognome"]);
  html_tableformstatic("Password (Hash)",$d["password"]);
  html_tableformstatic("Attivo",$d["attivo"]);
  html_tableformstatic("Cambio Password",$d["cambiopw"]);
  html_tableformstatic("Sospeso",$d["sospeso"]);
  html_tableformstatic("Inoltro email",$d["fw_email"]);
  html_tableformstatic("Homepage",$d["homepage"]);
  html_tableformstatic("Feed Blog",$d["blog_feed"]);
  html_tableformstatic("Note",$d["note"]);
  html_closeform ();

  ?>

  <a href="?function=sociils&action=picard&edit=<?php echo $d['nickname'] ?>" class="btn btn-primary">Modifica</a>
  <a href="?function=sociils&action=picard&chpw=<?php echo $d['nickname'] ?>" class="btn">Cambia Password</a>

  <?php
  html_pagetail();
}

function sociils_picardshow($n)
{
  if ($d=mysql_fetch_assoc(mysql_query("select * from users_picard where nickname=\"".$n."\"")))
    sociils_picardshowexist($d);
  else
  {
    $d=mysql_fetch_assoc(mysql_query("select * from soci_iscritti where nickname=\"".$n."\""));
    header("Location: ?function=sociils&action=picard&new=".$d["id"]);
  }
}

function sociils_picardelenco()
{
  html_pagehead("Elenco Utenti Picard", array ('Soci ILS' => 'sociils'));

  if ($r=mysql_query("select * from users_picard order by nickname"))
  {
    html_opentable();
    html_tableintest(array("Nickname","Nome","Cognome","Attivo","Sospeso"));
    while ($d=mysql_fetch_array($r))
    {
      if ($d["sospeso"]=="0000-00-00")
        $d["sospeso"]="&nbsp;";

      html_tabledata(array(
        "<a href=\"?function=sociils&action=picard&show=".$d["nickname"]."\">".$d["nickname"]."</a>",
        $d["nome"],$d["cognome"],$d["attivo"],$d["sospeso"]));
    }
    html_closetable();
  }
  html_pagetail();
}

function sociils_picardnewaccountform($s,$d,$e,$sub)
{
  html_pagehead("Nuovo Account Picard", array ('Soci ILS' => 'sociils',
                                               'Scheda socio' => "sociils&action=iscritti&show=" . $s["id"]));

  html_openform(".",array("function"=>"sociils","action"=>"picard","new"=>$s["id"],"confermadati"=>"ok"));
  html_tableformerrormsg($e);
  html_tableformstatic("Cognome",$s["cognome"]);
  html_tableformstatic("Nome",$s["nome"]);
  html_tableformstatic("Email",$s["email"]);
  html_tableformtext($d,"Nickname","nickname",50);
  html_tableformpassw($d,"Password","passw1",50);
  html_tableformpassw($d,"Verifica password","passw2",50);
  html_tableformtext($d,"Nome","nome",50);
  html_tableformtext($d,"Cognome","cognome",50);
  html_tableformtext($d,"Attivo","attivo",1);
  html_tableformtext($d,"Inoltro email","fw_email",50);
  html_tableformtext($d,"Homepage","homepage",50);
  html_tableformtext($d,"Feed blog","blog_feed",50);
  html_tableformtextarea($d,"Note","note");
  html_tableformsubmit($sub);
  html_closeform();
  html_pagetail();
}

function sociils_picardnormalizedata ($d)
{
  $d["cognome"] = ereg_replace (" ", "", $d["cognome"]);
  $d["cognome"] = ereg_replace ("'", "", $d["cognome"]);
  $d["nome"] = ereg_replace (" ", "", $d["nome"]);

  if (picard_aliasexist2 ($d["cognome"])) {
    $d["cognome"] = "XXX";
    $d["nome"] = ereg_replace (" ", "", $s["nome"] . "." . $s["cognome"]);
  }

  return $d;
}

function sociils_picardcreateaccount ($id, $d)
{
  my_insert ("users_picard", $d);
  $n["nickname"] = $d["nickname"];
  my_update ("soci_iscritti", $n, "id", $id);
}

function sociils_picardnewaccount($id)
{
  $s=mysql_fetch_assoc(mysql_query("select * from soci_iscritti where id=$id"));
  if (http_getparm("confermadati")!="ok")
  {
    $d = sociils_picardnormalizedata ($s);
    $d["nickname"]=$s["nickname"];
    $d["attivo"]="S";
    sociils_picardnewaccountform($s,$d,"","Invia");
  }
  else
  {
    $d["nickname"]=http_getparm("nickname");
    $d["cognome"]=http_getparm("cognome");
    $d["nome"]=http_getparm("nome");
    $d["attivo"]=http_getparm("attivo");
    $d["fw_email"]=http_getparm("fw_email");
    $d["homepage"]=http_getparm("homepage");
    $d["blog_feed"]=http_getparm("blog_feed");
    $d["note"]=http_getparm("note");
    $e="";
    if (($pw=http_getparm("passw1"))=="")
      $e="Password vuota";
    if ($pw!=http_getparm("passw2"))
      $e="Password non coincidenti";
    $d["password"]=mdspwgen($pw);
    $d["pw_picard"]=$d["password"];
    $d["cambiopw"]=date("Y-m-d");
    if (picard_aliasexist2($d["nome"].".".$d["cognome"]))
      $e="Alias ".$d["nome"].".".$d["cognome"]." esistente";
    if (picard_aliasexist3($d["cognome"]))
      $e="Alias ".$d["cognome"]." esistente";
    if (picard_aliasexist2($d["nickname"]))
      $e="Nickname ".$d["nickname"]." non disponibile";
    if ($e!="")
      sociils_picardnewaccountform($s,$d,$e,"Invia");
    else
    {
      sociils_picardcreateaccount ($id, $d);
      header("Location: ?function=sociils&action=iscritti&show=$id");
    }
  }
}

function sociils_picardcheck()
{
  html_pagehead("Controlli account", array ('Soci ILS' => 'sociils'));

  mysql_query("create temporary table tmp_ultimaquota select max(anno) as regola,id_socio from soci_quote group by id_socio");
  mysql_query("create temporary table tmp_accountcheck1 select id,i.cognome,i.nome,i.nickname,password,regola ".
    "from soci_iscritti as i left join users_picard as p on p.nickname=i.nickname ".
    "left join tmp_ultimaquota as q on i.id=q.id_socio where i.data_espulsione=00000000");
  mysql_query("create temporary table tmp_accountcheck2 select p.cognome,p.nome,p.nickname,id ".
    "from users_picard as p left join soci_iscritti as i on p.nickname=i.nickname ");
  if ($r=mysql_query("select * from tmp_accountcheck1 where isnull(password) order by cognome,nome,nickname"))
  {
    ?>

    <h2>Soci senza account</h2>

    <?php
    html_opentable();
    html_tableintest(array("Socio","Nickname","Quota",""));
    while ($d=mysql_fetch_array($r))
      html_tabledata(array(
        '<a href="?function=sociils&action=iscritti&show='.$d["id"].'">'.$d["cognome"] . ' ' . $d["nome"] . '</a>',
        $d["nickname"],$d["regola"],'<a href=\"?function=sociils&action=picard&new='.$d["id"].'" class="btn">Crea Nuovo</a>'));
    html_closetable();
  }

  if (($r=mysql_query("select * from tmp_accountcheck2 where isnull(id) order by cognome,nome,nickname")) && mysql_num_rows ($r) != 0)
  {
    ?>

    <h2>Account senza socio</h2>

    <?php

    html_opentable();
    html_tableintest(array("Nominativo","Nickname"));
    while ($d=mysql_fetch_array($r))
      html_tabledata(array($d["cognome"]." ".$d["nome"], "<a href=\"?function=sociils&action=picard&show=".$d["nickname"]."\">".$d["nickname"]."</a>"));
    html_closetable();
  }
  html_pagetail();
}

function sociils_picard()
{
  if (array_key_exists ('show', $_REQUEST) && $_REQUEST["show"]!="")
    sociils_picardshow($_REQUEST["show"]);
  else
  if (array_key_exists ('edit', $_REQUEST) && $_REQUEST["edit"]!="")
    sociils_picardedit($_REQUEST["edit"]);
  else
  if (array_key_exists ('check', $_REQUEST) && $_REQUEST["check"]!="")
    sociils_picardcheck($_REQUEST["check"]);
  else
  if (is_numeric($id=http_getparm("new")))
    sociils_picardnewaccount($id);
  else
  if (($n=http_getparm("chpw"))!="")
    sociils_picardchpw($n);
  else
    sociils_picardelenco();
}


#===============================================================

function map_color_region ($count, $max)
{
  $tint = 255 - ((255 * $count) / $max);
  $color = "rgb($tint, $tint, $tint)";
  return '{fill: "' . $color . '", stroke: "#fff", "stroke-width": 1, "stroke-linejoin": "round"}';
}

function sociils_listasociregione($p)
{
  if (is_numeric($p))
  {
    html_pagehead("Dislocazione regionale", array ('Soci ILS' => 'sociils', 'Regioni' => 'sociils&regione=tutto'));

    if ($p=="0") $w="isnull(r.id)"; else $w="r.id=$p";
    $q="select cognome,nome,nickname,prov_resid,s.id as id from soci_iscritti as s ".
      "left join it_province as p on s.prov_resid=p.sigla_prov ".
      "left join it_regioni as r on r.id=p.id_regione ".
      "where $w and data_espulsione=00000000 order by cognome,nome";

    if ($p=="0") {
      ?>

      <h2>Soci con residenza non italiana</h2>

      <?php
    }
    else {
      $d=mysql_fetch_assoc(mysql_query("select * from it_regioni where id=$p"));
      ?>

      <h2>Soci in <?php echo $d["regione"] ?></h2>

      <?php
    }

    if ($r=mysql_query($q)) {
      html_opentable(true);
      html_tableintest(array("Nome","Nickname","Provincia"));

      while ($d=mysql_fetch_array($r)) {
        $nome="<a href=\"?function=sociils&action=iscritti&show=".$d["id"]."\">".$d["cognome"]." ".$d["nome"]."</a>";
        html_tabledata(array($nome,$d["nickname"],$d["prov_resid"]));
      }

      html_closetable();
    }
  }
  else
  {
    html_pagehead("Dislocazione regionale", array ('Soci ILS' => 'sociils'));

    ?>

    <div class="row">
      <div class="span6">

      <?php

      $q="select id_regione,r.regione as regione,zona,prov_resid,count(*) as qt from soci_iscritti as s ".
        "left join it_province as p on s.prov_resid=p.sigla_prov ".
        "left join it_regioni as r on r.id=p.id_regione ".
        "left join it_zone as z on z.id=p.id_zona where data_espulsione=00000000 group by z.id,r.id";

      if ($r=mysql_query($q))
      {
        $max_count = 0;
        $attrs = array ();

        html_opentable (true);
        html_tableintest(array("Zona","Regione","Numero Soci"));

        while ($d=mysql_fetch_array($r))
        {
          if (($reg=$d["regione"])!="") {
            html_tabledata(array(ucwords ($d["zona"]),"<a href=\"?function=sociils&regione=".$d["id_regione"]."\">".ucwords($reg)."</a>",$d["qt"]));

            if ($d ['qt'] > $max_count)
              $max_count = $d ['qt'];

            $attrs [str_replace ("'", '', str_replace (' ', '_', strtolower ($reg)))] = $d ['qt'];
          }
          else {
            html_tabledata(array("&nbsp;","<a href=\"?function=sociils&regione=0\"><em>Ignota</em></a>",$d["qt"]));
          }
        }

        html_closetable();
      }

      ?>

      </div>

      <div class="span6">
        <script src="js/raphael-min.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" charset="utf-8">

          window.onload = function () {
            var R = Raphael("paper", 350, 400);

            var ita = {};

            ita.basilicata = R.path("m 255.33046,234.2801 c -17.03102,2.4697 -10.50285,21.5498 -2.96978,29.0026 -7.81434,22.1981 36.31617,5.0472 24.2478,-11.6132 -6.47989,-6.4485 -16.19318,-7.3276 -21.27802,-17.3894 z").attr(<?php echo map_color_region ($attrs ['basilicata'], $max_count) ?>);
            ita.trentino_alto_adige = R.path("m 162.10744,23.2837 c -10.86451,4.555 -27.93798,4.5246 -34.24471,10.7018 -9.69534,-7.746 -16.99051,11.212 -5.85,14.2673 -4.14327,7.2175 -7.97766,27.1858 6.35107,22.3229 2.61246,13.272 12.73446,-10.4879 21.02335,-8.8391 8.92327,-4.6001 -4.09074,-20.5387 11.24789,-21.7822 17.89178,2.9016 -7.98187,-12.2234 1.4724,-16.6707 z").attr(<?php echo map_color_region ($attrs ['trentino_alto_adige'], $max_count) ?>);
            ita.lombardia = R.path("m 111.79494,40.5199 c -8.90804,0.5389 1.68637,22.7354 -7.29613,10.0306 -10.17189,8.8461 -17.11546,-13.4099 -18.90931,4.3536 -4.54489,8.5795 -4.83099,20.527 -10.86925,6.4459 -12.11682,6.0791 7.87186,29.9237 -7.33335,29.206 2.39036,10.3368 16.82141,10.4553 19.26933,20.7701 -2.22612,-23.5562 23.46903,-13.1848 35.52823,-8.386 6.09923,0.1445 25.96805,-0.7686 9.6485,-7.4358 -14.06324,-4.7946 -8.45766,-22.5431 -8.83106,-24.4196 -15.01014,-2.1631 7.69468,-25.22 -11.20696,-30.5648 z").attr(<?php echo map_color_region ($attrs ['lombardia'], $max_count) ?>);
            ita.piemonte = R.path("M 65.62684,46.9209 C 49.077227,57.6738 59.263302,85.431 34.733721,83.4656 c -0.59896,8.4664 -22.636955,14.04 -6.870353,21.6976 0.408977,11.7726 -1.332397,30.2576 17.435042,26.0489 13.907351,0.936 13.656298,-18.2386 29.83964,-14.726 8.02198,-2.5612 12.48146,-4.1166 4.61825,-13.0116 C 71.36622,100.5586 59.986263,87.9496 75.86494,86.0558 66.37306,74.5731 72.78945,59.413 65.62684,46.9209 z").attr(<?php echo map_color_region ($attrs ['piemonte'], $max_count) ?>);
            ita.valle_daosta = R.path("M 43.166216,64.2976 C 32.058274,65.7427 16.932549,76.9528 34.732552,82.4108 48.431282,83.6903 61.443625,69.1342 43.166216,64.2976 z").attr(<?php echo map_color_region ($attrs ['valle_daosta'], $max_count) ?>);
            ita.emilia_romagna = R.path("m 102.39975,95.1875 c -11.58941,0.6158 -17.12145,8.3513 -16.00059,18.7899 6.41538,6.3969 10.08194,8.8451 18.36305,5.8778 10.87409,11.8937 26.56261,12.6392 40.22166,10.0868 5.54789,4.9105 5.77571,21.4029 16.31281,9.8445 4.56268,-9.1274 18.19027,7.1663 9.95428,-7.3528 -13.89897,-8.5598 0.0498,-36.85 -23.11181,-29.5852 -15.09861,-0.6183 -33.4977,3.8208 -45.7394,-7.661 z").attr(<?php echo map_color_region ($attrs ['emilia_romagna'], $max_count) ?>);
            ita.liguria = R.path("m 79.05229,112.7223 c -8.97863,7.1457 -24.124072,7.7819 -27.762631,18.6487 -12.735107,4.3189 -7.091292,18.248 3.655198,8.4019 8.983843,-13.6651 23.500273,-26.6014 38.654683,-11.7686 7.18928,8.6922 18.21774,3.9897 5.0029,-2.6243 -7.00972,-8.8281 -9.24728,-8.2102 -19.55015,-12.6577 z").attr(<?php echo map_color_region ($attrs ['liguria'], $max_count) ?>);
            ita.toscana = R.path("m 103.28659,118.7984 c -9.83304,10.9737 16.05079,19.2038 11.13009,33.5681 5.02959,12.6818 4.5364,22.4723 17.41138,29.1893 -0.42811,10.3303 11.29282,14.0661 14.19841,3.4796 7.04426,-9.1023 9.77384,-20.3988 14.12422,-26.7927 -6.92633,-4.1298 11.89988,-17.7617 -2.80411,-13.456 -13.51025,-1.3957 -6.31512,-22.3956 -22.46253,-13.171 -11.78581,3.1159 -23.43072,-5.194 -31.59746,-12.8173 z m 30.52116,66.3897 0.0258,0.1746 -0.0258,-0.1746 z m -32.65483,-17.3592 c -3.59851,2.4261 2.50903,2.8682 0,0 z m 15.01473,7.3142 c -9.91671,1.5846 -5.9216,7.1404 0.35662,3.659 0.62788,-1.0531 0.8448,-2.8711 -0.35662,-3.659 z m -8.3942,8.3767 c -3.25185,1.6778 2.38944,2.7497 0,0 z m 20.35329,8.122 c -2.30264,2.4513 3.65888,2.2284 0,0 z m -14.53179,0.9571 c -3.39557,1.7286 3.02783,1.8801 0,0 z").attr(<?php echo map_color_region ($attrs ['toscana'], $max_count) ?>);
            ita.veneto = R.path("m 168.18358,38.7725 c -11.47114,0.7783 -19.39428,8.9106 -14.41137,20.3011 -8.04935,4.6674 -18.98536,9.2312 -22.51141,17.5616 -11.42901,-13.6584 -12.32148,18.5197 2.0877,19.1758 8.26558,11.1708 28.3223,3.8424 35.02677,9.6088 -2.07461,-4.9138 -4.13168,-26.8583 -4.51259,-13.5906 -8.1305,-6.1883 10.98355,-15.1035 4.8386,-7.9454 14.14882,-0.6301 17.90558,-18.0782 0.82616,-14.4828 -6.51491,-10.9576 -2.40955,-21.3058 5.55723,-28.5965 -1.94904,-1.61 -4.55351,-1.6433 -6.90109,-2.032 z").attr(<?php echo map_color_region ($attrs ['veneto'], $max_count) ?>);
            ita.friuli_venezia_giulia = R.path("m 175.88411,39.9842 c -6.86454,8.1811 -13.73321,20.5217 -5.47026,28.9323 11.30109,-4.8417 11.4062,12.5238 18.2963,3.1971 5.11739,0.5056 21.10412,12.1308 11.22115,-1.742 -11.18543,-5.6952 3.68808,-12.8547 -8.08839,-17.3711 7.81192,-5.0531 10.12218,-10.9238 -2.13947,-10.2297 -4.56343,-0.7319 -9.89924,0.1155 -13.81933,-2.7866 z m 11.06349,34.0334 c -1.49132,1.7336 3.30859,-0.8798 0,0 z m 2.95026,0.4039 c -1.43185,1.0615 1.87857,0.5658 0,0 z").attr(<?php echo map_color_region ($attrs ['friuli_venezia_giulia'], $max_count) ?>);
            ita.marche = R.path("m 176.19143,136.7634 c -1.37137,8.4039 -21.26538,-2.3285 -14.31473,6.4381 7.0988,2.2791 -4.89513,8.7052 6.86662,9.8413 12.80433,4.0395 7.09331,20.872 20.33116,25.1408 7.22668,6.1005 22.0568,-10.7631 11.35859,-19.5457 -3.1931,-11.5664 -16.39624,-14.4637 -24.24164,-21.8745 z").attr(<?php echo map_color_region ($attrs ['marche'], $max_count) ?>);
            ita.umbria = R.path("m 165.14551,149.6971 c -12.50665,2.6436 2.18688,13.7477 -9.03092,15.2634 -7.79797,13.3233 6.72353,20.2619 14.86819,26.4421 8.07229,-6.0304 22.4577,-15.1836 8.43642,-21.0371 1.26598,-11.1243 -11.31782,-15.1263 -14.27369,-20.6684 z").attr(<?php echo map_color_region ($attrs ['umbria'], $max_count) ?>);
            ita.abruzzo = R.path("m 206.68612,215.9287 c 11.48627,-12.8621 13.82475,-0.6206 16.93897,-6.4088 7.46283,-14.7109 -17.37424,-21.6698 -20.38645,-33.866 -12.70939,2.31 -21.50216,13.9628 -13.417,24.6556 -18.0962,2.096 14.78804,14.1078 16.86448,15.6192").attr(<?php echo map_color_region ($attrs ['abruzzo'], $max_count) ?>);
            ita.lazio = R.path("m 152.82641,176.5217 c -2.89231,7.2458 -15.42882,16.4063 -2.12453,23.2373 12.39478,10.9109 22.82087,31.2309 40.84267,30.7505 16.2352,3.3025 21.57181,-19.1752 3.11963,-19.2386 -7.79867,-3.3942 -16.94842,-10.3801 -4.11922,-12.9878 -9.00659,-5.0426 -4.50824,-12.1865 -0.88148,-16.9453 -9.50025,-2.2183 -21.35602,21.0554 -27.79068,2.864 -3.14398,-2.4831 -8.99137,-2.2459 -9.04639,-7.6801 z m 28.72993,64.177 c -3.90043,1.2331 1.49997,2.9843 0,0 z").attr(<?php echo map_color_region ($attrs ['lazio'], $max_count) ?>);
            ita.puglia = R.path("m 258.41243,206.358 c -15.55161,-2.7886 -35.56138,10.407 -19.54093,24.835 7.48638,7.4705 21.54225,1.2614 27.37003,13.4798 11.98265,1.3983 8.4357,16.1685 21.67843,11.8146 9.72344,4.8638 21.86352,8.3108 25.75039,20.1371 16.33431,-1.0795 2.05861,-25.6755 -8.75073,-27.5715 -13.54676,-13.5737 -38.459,-15.6298 -49.24103,-29.1854 2.45891,-4.334 13.06089,-10.6616 2.73384,-13.5096 z").attr(<?php echo map_color_region ($attrs ['puglia'], $max_count) ?>);
            ita.campania = R.path("m 210.90092,221.9083 c -10.74579,3.1672 -9.28709,24.5577 6.31479,24.9115 -2.05394,11.6849 16.36722,-2.7789 14.09442,12.1156 -1.01012,21.4952 34.86123,8.7972 14.5869,-4.8224 -9.26887,-7.3754 6.76436,-17.192 -5.62627,-19.8262 -7.86998,-20.2696 -16.15777,-1.8629 -29.36984,-12.3785 z m -3.35417,24.3572 c -3.43347,1.9363 2.39434,0.6372 0,0 z m -3.30148,0.36 c -2.5748,4.9615 5.83426,-0.2886 0,0 z m 9.93079,6.5854 c -3.6768,-1.3038 -1.01904,3.0077 0,0 z").attr(<?php echo map_color_region ($attrs ['campania'], $max_count) ?>);
            ita.calabria = R.path("m 272.91789,266.8647 c -5.41653,13.446 -25.28609,-1.1595 -19.04546,15.613 4.47111,11.9833 15.13078,28.3293 0.95284,36.0988 1.31958,8.1582 -13.80466,21.6256 2.23855,20.7487 16.72088,-5.99 12.13231,-34.0559 29.6989,-33.7788 9.75236,-16.7309 -21.4739,-19.7085 -13.50974,-31.9167 1.02163,-1.8826 3.10114,-5.827 -0.33509,-6.765 z").attr(<?php echo map_color_region ($attrs ['calabria'], $max_count) ?>);
            ita.sicilia = R.path("m 165.385,337.5462 c -2.23526,0.1161 0.82371,-2.0957 0,0 z m -0.67358,1.5921 c -10.42722,-3.282 4.29229,2.8717 0,0 z m -6.64391,-1.8064 c -0.18882,1.8355 -0.13157,1.279 0,0 z m -1.13283,37.0773 c -6.87797,0.4703 4.8058,5.4217 0,0 z m 14.54312,41.2412 c -5.32764,-1.039 3.18889,1.4986 0,0 z m 7.25624,-11.0528 c -2.48532,-0.3247 1.40534,1.8603 0,0 z m 59.58458,-93.6698 c -3.99438,1.3307 2.91491,1.2982 0,0 z m -3.85362,5.3691 c -1.70054,0.2053 0.96115,1.2706 0,0 z m -5.1526,1.9052 c -4.90948,-0.8541 1.60608,3.8776 0,0 z m 1.55877,2.1216 c -2.59238,3.7839 3.67284,0.8191 0,0 z m 0.99588,3.5939 -0.0825,0.5176 0.0825,-0.5176 z m -45.47873,-10.2527 c 1.0954,1.6234 0.90778,1.3454 0,0 z m 45.80316,18.554 c -14.83507,3.7578 -32.8806,8.3505 -44.86946,-0.8657 -7.10005,8.2744 -18.88469,-4.394 -18.97714,10.9814 9.45124,14.3045 29.5726,16.8284 44.29698,25.7809 7.81013,7.6658 25.18168,18.8413 26.4027,0.434 -6.23878,-14.1894 -1.68653,-25.9611 7.96975,-37.458 -1.77475,-11.9279 -10.29857,5.2857 -14.88407,1.0049 m -10.22611,-13.5328 c -2.54748,-0.6223 2.0534,2.0219 0.15309,-0.031 m -5.41922,0.9185 c -1.34605,-0.7208 0.32176,1.4675 0,0 z").attr(<?php echo map_color_region ($attrs ['sicilia'], $max_count) ?>);
            ita.sardegna = R.path("m 91.01524,229.2878 c -3.69261,2.3548 2.23224,2.9002 0,0 z m -5.01369,0.5532 c -5.27638,9.9645 -19.51598,14.8237 -26.807211,14.3641 4.489891,14.7506 11.810411,28.4607 6.789151,44.394 -6.114257,13.0618 9.97904,28.9483 17.24925,10.6977 6.51302,-4.155 12.56184,5.7969 12.86968,-6.812 -0.36042,-18.5188 7.71551,-38.686 -1.83472,-55.5947 -1.74875,-3.8302 -6.81212,-3.9989 -8.26615,-7.0491 z m 6.20784,0.36 c -3.21683,2.9236 2.67124,2.2096 0,0 z m -29.36212,4.3727 c -4.521564,0.9755 -0.59143,9.0047 0,0 z m 33.96314,3.6351 c -1.83375,1.2834 2.31397,0.9118 0,0 z m 2.11611,2.8186 c -3.64386,0.2862 0.9682,2.4246 0,0 z m -36.43049,57.5477 c -5.250943,0.7893 3.20207,4.9481 0,0 z m 1.95806,2.3003 c -3.202553,7.299 6.39446,2.9834 0,0 z").attr(<?php echo map_color_region ($attrs ['sardegna'], $max_count) ?>);
            ita.molise = R.path("m 216.65203,223.6029 c 17.24042,11.7063 26.53055,-30.6406 8.46456,-16.4484 -4.77044,10.9615 -13.46232,-6.7485 -14.90188,8.8804 -2.4979,10.3907 -4.61576,13.6787 -2.69836,1.0531 -0.9795,17.9471 2.48734,-1.4213 9.13568,6.5149 z").attr(<?php echo map_color_region ($attrs ['molise'], $max_count) ?>);
            ita.sardegna = R.path("m 91.01524,229.2878 c -3.69261,2.3548 2.23224,2.9002 0,0 z m -5.01369,0.5532 c -5.27638,9.9645 -19.51598,14.8237 -26.807211,14.3641 4.489891,14.7506 11.810411,28.4607 6.789151,44.394 -6.114257,13.0618 9.97904,28.9483 17.24925,10.6977 6.51302,-4.155 12.56184,5.7969 12.86968,-6.812 -0.36042,-18.5188 7.71551,-38.686 -1.83472,-55.5947 -1.74875,-3.8302 -6.81212,-3.9989 -8.26615,-7.0491 z m 6.20784,0.36 c -3.21683,2.9236 2.67124,2.2096 0,0 z m -29.36212,4.3727 c -4.521564,0.9755 -0.59143,9.0047 0,0 z m 33.96314,3.6351 c -1.83375,1.2834 2.31397,0.9118 0,0 z m 2.11611,2.8186 c -3.64386,0.2862 0.9682,2.4246 0,0 z m -36.43049,57.5477 c -5.250943,0.7893 3.20207,4.9481 0,0 z m 1.95806,2.3003 c -3.202553,7.299 6.39446,2.9834 0,0 z").attr(<?php echo map_color_region ($attrs ['sardegna'], $max_count) ?>);
            ita.sardegna = R.path("m 91.01524,229.2878 c -3.69261,2.3548 2.23224,2.9002 0,0 z m -5.01369,0.5532 c -5.27638,9.9645 -19.51598,14.8237 -26.807211,14.3641 4.489891,14.7506 11.810411,28.4607 6.789151,44.394 -6.114257,13.0618 9.97904,28.9483 17.24925,10.6977 6.51302,-4.155 12.56184,5.7969 12.86968,-6.812 -0.36042,-18.5188 7.71551,-38.686 -1.83472,-55.5947 -1.74875,-3.8302 -6.81212,-3.9989 -8.26615,-7.0491 z m 6.20784,0.36 c -3.21683,2.9236 2.67124,2.2096 0,0 z m -29.36212,4.3727 c -4.521564,0.9755 -0.59143,9.0047 0,0 z m 33.96314,3.6351 c -1.83375,1.2834 2.31397,0.9118 0,0 z m 2.11611,2.8186 c -3.64386,0.2862 0.9682,2.4246 0,0 z m -36.43049,57.5477 c -5.250943,0.7893 3.20207,4.9481 0,0 z m 1.95806,2.3003 c -3.202553,7.299 6.39446,2.9834 0,0 z").attr(<?php echo map_color_region ($attrs ['sardegna'], $max_count) ?>);
          };
        </script>

        <div id="paper"></div>
      </div>
    </div>

    <?php
  }
  html_pagetail();
}


#===============================================================


function sociils_quoteanno($a)
{
  html_pagehead("Controlli Pagamenti Quote", array ('Soci ILS' => 'sociils', 'Quote' => 'sociils&action=quote&anno=lista'));

  if ($r=mysql_query("select *,q.note as noteq from soci_quote as q left join soci_iscritti as i on i.id=q.id_socio ".
    "left join conti_righe as r on r.id=q.id_riga where year(data_versamento)=$a order by data_versamento"))
  {
    ?>

    <h2>Pagamenti quota anno <?php echo $a ?></h2>

    <?php

    html_opentable();
    html_tableintest(array("Data versamento","Anno","Socio","Note"));
    while ($d=mysql_fetch_array($r))
    {
      if ($d["id_movimento"]>0)
        $data="<a href=\"?function=contabilita&action=movimenti&show=".$d["id_movimento"]."\" target=\"_blank\">".$d["data_versamento"]."</a>";
      else
        $data=$d["data_versamento"];

      html_tabledata(array($data,$d["anno"],
        "<a href=\"?function=sociils&action=iscritti&show=".$d["id_socio"]."\">".$d["cognome"]." ".$d["nome"]."</a>",
        $d["noteq"]));
    }
    html_closetable();
  }
  html_pagetail();
}

function sociils_quoteregola($a)
{
  html_pagehead("Controlli Pagamenti Quote", array ('Soci ILS' => 'sociils', 'Quote' => 'sociils&action=quote&anno=lista'));

  mysql_query("create temporary table tmp_quotesoci1 select id_socio,max(anno) as regola from soci_quote group by id_socio");
  mysql_query("insert into tmp_quotesoci1 select id,0 from soci_iscritti");
  mysql_query("create temporary table tmp_quotesoci2 select id_socio,sum(dare-avere) as credito from conti_righe as r ".
    "left join conti_sottoconti as s on s.id=r.id_sottoconto ".
    "left join conti_conti as c on c.id=s.id_conto where conto like \"Crediti conferimento nostri associati\" group by id_socio");
  mysql_query("create temporary table tmp_quotesoci select t.id_socio,max(regola) as anno,credito from tmp_quotesoci1 as t ".
    "left join tmp_quotesoci2 as c on c.id_socio=t.id_socio group by t.id_socio");
  if ($r=mysql_query("select * from tmp_quotesoci as q left join soci_iscritti as i on i.id=q.id_socio ".
    "where anno=$a order by data_espulsione,cognome,nome"))
  {
    echo "<H2>Soci in regola anno $a</H2>\n";
    html_opentable();
    html_tableintest(array("Socio","Nickname","Anno di iscrizione","Credito quote","Espulsione"));
    while ($d=mysql_fetch_array($r))
      html_tabledata(array(
        "<a href=\"?function=sociils&action=iscritti&show=".$d["id_socio"]."\">".$d["cognome"]." ".$d["nome"]."</a>",
        $d["nickname"],$d["anno_iscrizione"],$d["credito"],$d["data_espulsione"]));
    html_closetable();
  }
  html_pagetail();
}

function sociils_quoteelenco()
{
  html_pagehead("Controlli Pagamenti Quote", array ('Soci ILS' => 'sociils'));
  ?>

  <div class="row">
    <div class="span6">

      <?php

      mysql_query("create temporary table tmp_quotesoci1 select id_socio,max(anno) as regola from soci_quote group by id_socio");
      mysql_query("insert into tmp_quotesoci1 select id,0 from soci_iscritti");
      mysql_query("create temporary table tmp_quotesoci2 select id_socio,sum(dare-avere) as credito from conti_righe as r ".
        "left join conti_sottoconti as s on s.id=r.id_sottoconto ".
        "left join conti_conti as c on c.id=s.id_conto where conto like \"Crediti conferimento nostri associati\" group by id_socio");

      mysql_query("create temporary table tmp_quotesoci select t.id_socio,max(regola) as anno,credito from tmp_quotesoci1 as t ".
        "left join tmp_quotesoci2 as c on c.id_socio=t.id_socio group by t.id_socio");
      if ($r=mysql_query("select anno,count(*) as soci,sum(if(data_espulsione=00000000,1,0)) as attivi,sum(credito) as crediti from tmp_quotesoci as q ".
        "left join soci_iscritti as i on i.id=q.id_socio group by anno order by anno desc"))
      {
        ?>

        <h2>Soci in regola per anno</h2>

        <?php

        $t=0;
        html_opentable();
        html_tableintest(array("Anno in regola","Quantit&agrave;","Iscritti","Crediti"));
        while ($d=mysql_fetch_array($r))
        {
          html_tabledata(array("<a href=\"?function=sociils&action=quote&regola=".$d["anno"]."\">".$d["anno"]."</a>",$d["soci"],$d["attivi"],$d["crediti"]));
          $t+=$d["crediti"];
        }
        html_tabledata(array("Totale crediti","","",sprintf("%.2f",$t)));
        html_closetable();
      }

      ?>

    </div>
    <div class="span6">

      <?php

      if ($r=mysql_query("select year(data_versamento) as y,count(*) as q,sum(if(data_espulsione=00000000,1,0)) as attivi from soci_quote as sq ".
        "left join soci_iscritti as i on i.id=sq.id_socio group by y order by y desc"))
      {
        ?>

        <h2>Tutti i pagamenti</h2>

        <?php
        html_opentable();
        html_tableintest(array("Anno versamento","Quantit&agrave;","Iscritti"));
        while ($d=mysql_fetch_array($r))
          html_tabledata(array("<A HREF=\"?function=sociils&action=quote&anno=".$d["y"]."\">".$d["y"]."</A>",$d["q"],$d["attivi"]));
        html_closetable();
      }

      ?>

    </div>
  </div>

  <?php

  html_pagetail();
}

function sociils_quote()
{
  if (is_numeric($a=http_getparm("anno")))
    sociils_quoteanno($a);
  else
  if (is_numeric($a=http_getparm("regola")))
    sociils_quoteregola($a);
  else
    sociils_quoteelenco();
}


#===============================================================


function sociils_menu()
{
  html_pagehead("Soci ILS");

  $iscr=mysql_num_rows(mysql_query("select * from soci_iscritti where data_espulsione=00000000"));
  $dom=mysql_num_rows(mysql_query("select * from soci_iscritti where data_ammissione=00000000"));

  $res = mysql_query ("SELECT SUM(members) FROM soci_iscritti WHERE data_espulsione = 00000000 AND type = 'associazione'");
  $row = mysql_fetch_array ($res);
  $extra = $row [0];

  ?>

  <div class="row">
    <div class="span6">
      <ul class="nav nav-pills nav-stacked">
        <li><a href="?function=sociils&action=elenco">Elenco soci (<?php echo $iscr ?> + <?php echo $extra ?>)</a>
        <li><a href="?function=sociils&regione=tutte">Dislocazione regionale</a>
        <?php if (userperm("anagrafica")): ?>
          <li><a href="?function=sociils&action=domande">Domande di ammissione (<?php echo $dom ?>)</a>
          <li><a href="?function=sociils&action=picard&check=all">Controllo account Picard</a>
        <?php endif; ?>
        <?php if (userperm("anagrafica") || userperm("banche")): ?>
          <li><a href="?function=sociils&action=quote">Controllo pagamenti quote</a>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <?php
  html_pagetail();
}


function sociils()
{
  if (!is_numeric($_SESSION["user"]["idsocio"])) {
    header("Location: .");
  }
  else
  {
    if (array_key_exists ('action', $_REQUEST)) {
      if ($_REQUEST["action"]=="nuovadomanda" && userperm("anagrafica"))
        sociils_nuovadomanda();
      else if ($_REQUEST["action"]=="domande")
        sociils_domandeammissione();
      else if ($_REQUEST["action"]=="elenco")
        sociils_elencosoci();
      else if ($_REQUEST["action"]=="quote" && (userperm("anagrafica") || userperm("banche")))
        sociils_quote();
      else if ($_REQUEST["action"]=="iscritti" && array_key_exists ('show', $_REQUEST) && is_numeric($id=$_REQUEST["show"]))
        sociils_iscritto($id);
      else if ($_REQUEST["action"]=="iscritti" && array_key_exists ('myedit', $_REQUEST) && is_string($id=$_REQUEST["myedit"]) && $id == $_SESSION["user"]["login"])
        sociils_iscritto_myedit();
      else if ($_REQUEST["action"]=="iscritti" && array_key_exists ('edit', $_REQUEST) && is_numeric($id=$_REQUEST["edit"]) && userperm("anagrafica") && array_key_exists ('confermadati', $_REQUEST) && $_REQUEST["confermadati"]=="ok")
        sociils_iscrittogetform($id);
      else if ($_REQUEST["action"]=="iscritti" && array_key_exists ('edit', $_REQUEST) && is_numeric($id=$_REQUEST["edit"]) && userperm("anagrafica"))
        sociils_iscrittoform($id);
      else if ($_REQUEST["action"]=="iscritti" && array_key_exists ('ammetti', $_REQUEST) && is_numeric($id=$_REQUEST["ammetti"]) && userperm("anagrafica") && $_REQUEST["confermadati"]=="ok")
        sociils_iscrittoammetti($id);
      else if ($_REQUEST["action"]=="iscritti" && array_key_exists ('ammetti', $_REQUEST) && is_numeric($id=$_REQUEST["ammetti"]) && userperm("anagrafica"))
        sociils_iscrittoaskammetti($id,array(),"");
      else if ($_REQUEST["action"]=="candidato" && is_numeric($id=$_REQUEST["show"]))
        sociils_candidatosocio($id);
      else if ($_REQUEST["action"]=="candidato" && is_numeric($id=$_REQUEST["edit"]) && userperm("anagrafica") && $_REQUEST["confermadati"]=="ok")
        sociils_candidatosociogetform($id);
      else if ($_REQUEST["action"]=="candidato" && is_numeric($id=$_REQUEST["edit"]) && userperm("anagrafica"))
        sociils_candidatosocioform($id);
      else if ($_REQUEST["action"]=="candidato" && is_numeric($id=$_REQUEST["remove"]) && userperm("anagrafica") && $_REQUEST["confermadati"]=="ok")
        sociils_candidatosocioremove($id);
      else if ($_REQUEST["action"]=="candidato" && is_numeric($id=$_REQUEST["remove"]) && userperm("anagrafica"))
        sociils_candidatosocioaskremove($id);
      else if ($_REQUEST["action"]=="candidato" && is_numeric($id=$_REQUEST["approva"]) && userperm("anagrafica") && $_REQUEST["confermadati"]=="ok")
        sociils_candidatosocioapprova($id);
      else if ($_REQUEST["action"]=="candidato" && is_numeric($id=$_REQUEST["approva"]) && userperm("anagrafica"))
        sociils_candidatosocioaskapprova($id,array(),"");
      else if ($_REQUEST["action"]=="espulsione" && is_numeric($id=$_REQUEST["id"]) && userperm("anagrafica") && $_REQUEST["confermadati"]=="ok")
        sociils_espulsioneok($id);
      else if ($_REQUEST["action"]=="espulsione" && is_numeric($id=$_REQUEST["id"]) && userperm("anagrafica"))
        sociils_espulsioneask($id);
      else if ($_REQUEST["action"]=="picard" && userperm("anagrafica"))
        sociils_picard();
    }
    else if (array_key_exists ('regione', $_REQUEST) && $_REQUEST["regione"]!="")
      sociils_listasociregione($_REQUEST["regione"]);
    else
      sociils_menu();
  }
}


?>
