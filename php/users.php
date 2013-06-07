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

function mdspwgen($s)
{
  $salt=substr(base64_encode(crypt("aa".date("YmdHi").time())),2,2);
  return crypt($s,$salt);
}


function formpromemoriapw()
{
  $auth_user = get_request_param ('auth_user');
  $ex_cookie = get_request_param ('cookie');

  if ((($l=mysql_real_escape_string($auth_user))!="") && 
    ($d=mysql_fetch_array(mysql_query(
      "select * from users_picard where nickname=\"".$l."\" and attivo=\"S\""))))
    {
      $cookie=md5($l.date("YmdHis").time());

      $address = array ();

      $a = $d ['fw_email'];
      if ($a == '')
        $a = $l . "@linux.it";
      $address [] = $a;

      $query = "SELECT email FROM soci_iscritti WHERE nickname = '" . $d['nickname'] . "'";
      $res = mysql_query ($query);
      if ($res !== false) {
        $e = mysql_fetch_array ($res);
        $address [] = $e ['email'];
      }

      mysql_query("update users_picard set cookie_time=".time().", cookie_pw=\"$cookie\" where nickname=\"$l\"");
      mail(join (', ', $address), "Recupero accesso intranet ILS",
        "E' stato chiesto il recupero della password dall'indirizzo ".
        "IP ".$_SERVER["REMOTE_ADDR"]."\n".
        "(".$_SERVER["HTTP_USER_AGENT"].").\n\n".
        "Per connettersi all'intranet, andare su questo URL:\n".
        "https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].
        "?function=rememberpw&cookie=$cookie\n\n".
        "-- \nILS Italian Linux Society\n","From: Direzione ILS <direttore@linux.it>\n"."Bcc: direttore@linux.it,archivio-ilsmanager@ilsmanager.linux.it");

      html_pagehead("Autenticazione");

      echo "<CENTER>\n";
      echo "<P>&Egrave; stata inviata una mail con un link per entrare nell'intranet.\n";
      echo "</CENTER>\n";
      html_pagetail();
    }
  else
  if ((($l=mysql_real_escape_string($auth_user))!="") && 
    ($d=mysql_fetch_array(mysql_query(
      "select * from users_extra where nickname=\"".$l."\" and attivo=\"S\""))))
    {
      $cookie=md5($l.date("YmdHis").time());
      mysql_query("update users_extra set cookie_time=".time().", cookie_pw=\"$cookie\" where nickname=\"$l\"");
      mail($l,"Recupero accesso intranet ILS",
        "E' stato chiesto il recupero della password dall'indirizzo ".
        "IP ".$_SERVER["REMOTE_ADDR"]."\n".
        "(".$_SERVER["HTTP_USER_AGENT"].").\n\n".
        "Per connettersi all'intranet, andare su questo URL:\n".
        "https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].
        "?function=rememberpw&cookie=$cookie\n\n".
        "-- \nILS Italian Linux Society\n","From: Direzione ILS <direttore@linux.it>\n"."Bcc: direttore@linux.it,archivio-ilsmanager@ilsmanager.linux.it");

      html_pagehead("Autenticazione");

      echo "<CENTER>\n";
      echo "<P>&Egrave; stata inviata una mail con un link per entrare nell'intranet.\n";
      echo "</CENTER>\n";
      html_pagetail();
    }
  else
  if ((($c=mysql_real_escape_string($ex_cookie))!="") && 
    ($d=mysql_fetch_array(mysql_query(
      "select * from users_picard where cookie_pw=\"".$c."\" and attivo=\"S\" ".
      "and cookie_time>".(time()-7200)))))
    {
      if ((($p=$_REQUEST["AUTH_PW1"])!="") && ($p==$_REQUEST["AUTH_PW2"]))
      {
        mysql_query("update users_picard set password=\"".mdspwgen($p)."\",".
          "cookie_pw=null,cookie_time=null,cambiopw=now() ".
          "where cookie_pw=\"".$c."\" and attivo=\"S\" and cookie_time>".(time()-7200));
        $l["nickname"]=$_SESSION["user"]["login"];
        $l["action"]="set new password";
        $l["ora"]=time();
        $l["ip"]=$_SERVER["REMOTE_ADDR"];
        $l["browser"]=$_SERVER["HTTP_USER_AGENT"];
        my_insert("user_passwlog",$l);

        html_pagehead("Autenticazione");

        echo "<CENTER>\n";
        echo "<P>Password impostata!\n";
        echo "<P><A HREF=\".\">Pagina di login</A>\n";
        echo "</CENTER>\n";
        html_pagetail();
      }
      else
      {
        html_pagehead("Autenticazione");

        echo "<CENTER>\n";
        $s=mysql_fetch_array(mysql_query("select * from soci_iscritti where nickname=\"".$d["nickname"]."\""));
        echo "Benvenuto, ".$s["nome"]." ".$s["cognome"]."\n";
        html_openform(".",array("function"=>"rememberpw","cookie"=>$c));
        html_tableformpassw(array(),"nuova password","AUTH_PW1",20);
        html_tableformpassw(array(),"conferma password","AUTH_PW2",20);
        html_tableformsubmit("Imposta");
        html_closeform();
        echo "</CENTER>\n";
        html_pagetail();
      }
    }
  else
  if ((($c=mysql_real_escape_string($ex_cookie))!="") && 
    ($d=mysql_fetch_array(mysql_query(
      "select * from users_extra where cookie_pw=\"".$c."\" and attivo=\"S\" ".
      "and cookie_time>".(time()-7200)))))
    {
      if ((($p=$_REQUEST["AUTH_PW1"])!="") && ($p==$_REQUEST["AUTH_PW2"]))
      {
        mysql_query("update users_extra set password=\"".mdspwgen($p)."\",".
          "cookie_pw=null,cookie_time=null,cambiopw=now() ".
          "where cookie_pw=\"".$c."\" and attivo=\"S\" and cookie_time>".(time()-7200));
        $l["nickname"]=$_SESSION["user"]["login"];
        $l["action"]="set new password";
        $l["ora"]=time();
        $l["ip"]=$_SERVER["REMOTE_ADDR"];
        $l["browser"]=$_SERVER["HTTP_USER_AGENT"];
        my_insert("user_passwlog",$l);

        html_pagehead("Autenticazione");

        echo "<CENTER>\n";
        echo "<P>Password impostata!\n";
        echo "<P><A HREF=\".\">Pagina di login</A>\n";
        echo "</CENTER>\n";
        html_pagetail();
      }
      else
      {
        html_pagehead("Autenticazione");

        echo "<CENTER>\n";
        $s=mysql_fetch_array(mysql_query("select * from users_extra where nickname=\"".$d["nickname"]."\""));
        echo "Benvenuto, ".$s["nome"]."\n";
        html_openform(".",array("function"=>"rememberpw","cookie"=>$c));
        html_tableformpassw(array(),"nuova password","AUTH_PW1",20);
        html_tableformpassw(array(),"conferma password","AUTH_PW2",20);
        html_tableformsubmit("Imposta");
        html_closeform();
        echo "</CENTER>\n";
        html_pagetail();
      }
    }
  else
  {
    html_pagehead("Autenticazione");

    echo "<CENTER>\n";
    html_openform(".",array("function"=>"rememberpw","auth"=>"recovery"));
    html_tableformtext(array(),"utente","auth_user",20);
    html_tableformsubmit("Invia promemoria");
    html_closeform();
    echo "<A HREF=\".\">Login normale</A>\n";
    echo "</CENTER>\n";
    html_pagetail();
  }
  exit;
}

function userchangepwform($msg)
{
  html_pagehead("Cambio password");

  if ($msg != '') {
    ?>

    <div class="alert"><?php echo $msg ?></div>

    <?php
  }

  html_openform(".",array("function"=>"users","action"=>"postchangepw"));
  html_tableformpassw(array(),"Password attuale","old_pw",20);
  html_tableformpassw(array(),"Nuova password","new_pw1",20);
  html_tableformpassw(array(),"Conferma password","new_pw2",20);
  html_tableformsubmit("Cambia");
  html_closeform();

  html_pagetail();
}

function userchangepwpicardform ($msg)
{
  html_pagehead("Cambio password");

  echo "<CENTER>\n";
  html_openform(".",array("function"=>"users","action"=>"postchangepwpicard"));
  html_tableformpassw(array(),"nuova password","new_pw1",20);
  html_tableformpassw(array(),"conferma password","new_pw2",20);
  html_tableformsubmit("Cambia");
  html_closeform();
  echo "<P><FONT COLOR=\"#FF0000\"><BOLD>$msg</BOLD></FONT>\n";
  echo "</CENTER>\n";
  html_pagetail();
}

function userchangepwpost()
{
  $old=mysql_real_escape_string($_REQUEST["old_pw"]);
  $new1=mysql_real_escape_string($_REQUEST["new_pw1"]);
  $new2=mysql_real_escape_string($_REQUEST["new_pw2"]);
  if ($new1!=$new2)
    userchangepwform("Le nuove password non coincidono");
  else
  if ($new1=="")
    userchangepwform("Nuova password vuota");
  else
  if ($old!=$_SESSION["user"]["passw"])
      userchangepwform("Password errata");
  else
  {
    if (strstr($_SESSION["user"]["login"],"@"))
      mysql_query("update users_extra set cambiopw=now(),password=\"".mdspwgen($new1)."\", ".
        "cookie_pw=null,cookie_time=null where nickname=\"".$_SESSION["user"]["login"]."\"");
    else
      mysql_query("update users_picard set cambiopw=now(),password=\"".mdspwgen($new1)."\", ".
        "cookie_pw=null,cookie_time=null where nickname=\"".$_SESSION["user"]["login"]."\"");
    $l["nickname"]=$_SESSION["user"]["login"];
    $l["action"]="change password";
    $l["ora"]=time();
    $l["ip"]=$_SERVER["REMOTE_ADDR"];
    $l["browser"]=$_SERVER["HTTP_USER_AGENT"];
    my_insert("users_passwlog",$l);
    $_SESSION["user"]["passw"]=$new1;
    header("Location: .");
  }
}

function userchangepwpicardpost()
{
  $new1=mysql_real_escape_string($_REQUEST["new_pw1"]);
  $new2=mysql_real_escape_string($_REQUEST["new_pw2"]);

  if ($new1!=$new2) {
    userchangepwpicardform("Le nuove password non coincidono");
  }
  else if ($new1=="") {
    userchangepwpicardform("Nuova password vuota");
  }
  else {
    if (strstr($_SESSION["user"]["login"],"@"))
      mysql_query("update users_extra set cambiopw=now(),pw_picard=\"".mdspwgen($new1)."\", ".
        "cookie_pw=null,cookie_time=null where nickname=\"".$_SESSION["user"]["login"]."\"");
    else
      mysql_query("update users_picard set cambiopw=now(),pw_picard=\"".mdspwgen($new1)."\", ".
        "cookie_pw=null,cookie_time=null where nickname=\"".$_SESSION["user"]["login"]."\"");

    $l["nickname"]=$_SESSION["user"]["login"];
    $l["action"]="change picard password";
    $l["ora"]=time();
    $l["ip"]=$_SERVER["REMOTE_ADDR"];
    $l["browser"]=$_SERVER["HTTP_USER_AGENT"];
    my_insert("users_passwlog",$l);
    header("Location: .");
  }
}



function usermanageruleadduser($rid)
{
  if ($_REQUEST["user"]!="")
  {
    mysql_query("insert into users_perm (id_rules,username) values($rid,\"".$_REQUEST["user"]."\")");
    header("Location: ".$_SERVER["HTTP_REFERER"]);
  }
  else
  {
    $_SESSION["rules"]["addto"]=$rid;
    header("Location: ?function=users&action=managerules&id=".$rid);
  }
}

function usermanageruledeluser($rid)
{
  unset($_SESSION["rules"]["addto"]);
  if ($_REQUEST["user"]!="")
  {
    mysql_query("delete from users_perm where id_rules=$rid and username=\"".$_REQUEST["user"]."\"");
    header("Location: ".$_SERVER["HTTP_REFERER"]);
  }
}

function usermanageruleshow($id)
{
  html_pagehead("Gestione regole", array ('Regole' => 'users&action=managerules'));

  if ($d=mysql_fetch_array(mysql_query("select * from users_rules where id=$id")))
  {
    html_infobox (array ('Regola' => $d["rule"], 'Descrizione' => $d["descrizione"]));

    if ($r=mysql_query("select id_rules,username,concat(p.nome,\" \",p.cognome) as n1,e.nome as n2 ".
      "from users_perm as u left join users_picard as p on u.username=p.nickname ".
      "left join users_extra as e on u.username=e.nickname where id_rules=$id"))
    {
      if (mysql_num_rows ($r) != 0) {
        ?>

        <h2>Utenti abilitati</h2>

        <?php
        html_opentable();
        html_tableintest(array("Utente","Nome","&nbsp;"));
        while ($d=mysql_fetch_array($r))
          html_tabledata(array($d["username"],$d["n2"].$d["n1"],
            "<A HREF=\"?function=users&action=managerules&delfrom=".$id."&user=".$d["username"]."\">[disabilita]</A>"));
        html_closetable();
      }
      else {
        ?>

        <p>Non ci sono utenti in questo gruppo</p>

        <?php
      }
    }

    if (array_key_exists ('rules', $_SESSION) && $_SESSION["rules"]["addto"]!=$id) {
      ?>

      <a class="btn btn-primary" href="?function=users&action=managerules&addto=<?php echo $id ?>">Aggiungi Utenti</a>

      <?php
    }
  }
  html_pagetail();
}


function usermanageruleslist()
{
  html_pagehead("Gestione regole");

  if ($r=mysql_query("select * from users_rules order by rule"))
  {
    $r1=mysql_query("select id_rules,count(*) as tot from users_perm group by id_rules");
    while ($d1=mysql_fetch_array($r1))
      $q[$d1["id_rules"]]=$d1["tot"];
    html_opentable();
    html_tableintest(array("Regola","Utenti"));

    while ($d=mysql_fetch_array($r)) {
      if (array_key_exists ($d["id"], $q))
        html_tabledata (array("<a href=\"?function=users&action=managerules&id=".$d["id"]."\">".$d["rule"]."</a>", $q[$d["id"]]));
      else
        html_tabledata (array("<a href=\"?function=users&action=managerules&id=".$d["id"]."\">".$d["rule"]."</a>", '0'));
    }

    html_closetable();
  }
  html_pagetail();
}


function usermanagerules()
{
  if (test_value_array ($_REQUEST, 'addto', null, 'num'))
    usermanageruleadduser($_REQUEST["addto"]);
  else
  if (test_value_array ($_REQUEST, 'delfrom', null, 'num'))
    usermanageruledeluser($_REQUEST["delfrom"]);
  else
  if (test_value_array ($_REQUEST, 'id', null, 'num'))
    usermanageruleshow($_REQUEST["id"]);
  else
    usermanageruleslist();
}


function usermanager()
{
  if (!is_array($_SESSION["user"]))
    header("Location: .");
  else if ($_REQUEST["action"]=="formchangepw")
    userchangepwform("");
  else if ($_REQUEST["action"]=="formchangepwpicard")
    userchangepwpicardform("");
  else if ($_REQUEST["action"]=="postchangepw")
    userchangepwpost();
  else if ($_REQUEST["action"]=="postchangepwpicard")
    userchangepwpicardpost ();
  else if ($_REQUEST["action"]=="managerules" && $_SESSION["user"]["rules"]["admin"]=="S")
    usermanagerules();
  else
    header("Location: .");
}


?>
