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

function my_connect()
{
  global $DB;

  if (!(($link=mysql_connect($DB["host"],$DB["login"],$DB["password"])) && mysql_select_db($DB["db"])))
    exit('<span style="color: #FF0000"><b>Errore connessione database</b></span></body></html>');
}

function my_insert($table,$record)
{
  $c="";
  $v="";

  foreach($record as $k=>$d)
  {
    if ($c!="")
    {
      $c.=",";
      $v.=",";
    }
    $c.=$k;
    $v.="\"".ereg_replace("\\\\\\\\'","'",mysql_real_escape_string($d))."\"";
  }

  mysql_query("insert into $table ($c) values($v)");
  return mysql_insert_id();
}

function my_update($table,$record,$key,$keyval)
{
  $q="";
  foreach($record as $k=>$d)
  {
    if ($q!="") $q.=",";
    $q.=$k."=\"".ereg_replace("\\\\\\\\'","'",mysql_real_escape_string($d))."\"";
  }
  mysql_query("update $table set $q where $key=$keyval");
}

function my_checklogintable($login,$password,$table,$loginfield,$passwordfield,$where)
{
  $q="select * from $table where $loginfield=\"$login\"";
  if ($where!="") $q.=" and $where";
  if (mysql_num_rows($r=mysql_query($q))==1)
  {
    $d=mysql_fetch_array($r);
    if ($d[$passwordfield]==crypt($password,$d[$passwordfield]))
      return $d;
  }
  return FALSE;
}

function access_session($l)
{
  mysql_query("insert into users_accesslog (nickname,ip,ora,browser) ".
    "values (\"".$l."\",".ip2long($_SERVER["REMOTE_ADDR"]).",".time().",\"".$_SERVER["HTTP_USER_AGENT"]."\")");
  if ($r=mysql_query("select * from users_perm as p left join users_rules as r on r.id=p.id_rules where username=\"$l\""))
    while ($d=mysql_fetch_array($r))
    {
      $_SESSION["user"]["rules"][$d["rule"]]="S";
    }
}

function my_checklogin($login,$password)
{
  unset($_SESSION["user"]);
  $l=mysql_real_escape_string($login);
  $p=mysql_real_escape_string($password);
  if ((is_array($d=my_checklogintable($l,$p,"users_picard","nickname","password","attivo=\"S\"")))
    && ($d=mysql_fetch_array(mysql_query("select * from soci_iscritti where nickname=\"".$l."\" and data_espulsione=00000000"))))
  {
    $_SESSION["user"]["login"]=$l;
    $_SESSION["user"]["passw"]=$p;
    $_SESSION["user"]["nome"]=$d["nome"]." ".$d["cognome"];
    $_SESSION["user"]["idsocio"]=$d["id"];
    if ($d["data_ammissione"]!="0000-00-00")
      $_SESSION["user"]["tipo"]="iscritti";
    else
      $_SESSION["user"]["tipo"]="candidato";
    $d=mysql_fetch_assoc(mysql_query("select max(anno) as ultima from soci_quote where id_socio=".$_SESSION["user"]["idsocio"]));
    $_SESSION["user"]["quota"]=$d["ultima"];
    $d=mysql_fetch_assoc(mysql_query("select * from users_chat where nickname=\"".$l."\""));
    $_SESSION["user"]["chatmd5"]=$d["chatmd5"];
    access_session($l);
  }
  else
  if (is_array($d=my_checklogintable($l,$p,"users_extra","nickname","password","attivo=\"S\"")))
  {
    $_SESSION["user"]["login"]=$l;
    $_SESSION["user"]["passw"]=$p;
    $_SESSION["user"]["nome"]=$d["nome"];
    access_session($l);
  }
}

function userperm($p)
{
  if (array_key_exists ('rules', $_SESSION["user"]) && ($_SESSION["user"]["rules"]["admin"]=="S" || $_SESSION["user"]["rules"][$p]=="S"))
    return TRUE;
  else
    return FALSE;
}

function socifile_format_rows ($r)
{
  while ($d=mysql_fetch_assoc($r))
  {
    /*
      Capita che alcuni soci optino per "nome.cognome" come proprio nickname, ma questo va poi
      in conflitto con gli script di creazione degli alias su Picard (che gia' creano comunque
      sempre nome.cognome). In tal caso andiamo qui ad alterare artificiosamente il nome onde
      evitare la collisione
    */
    if (strtolower ($d ["nome"]) . '.' . strtolower ($d ["cognome"]) == strtolower ($d ["nickname"]))
      $d ["nome"] = 'XXX' . $d ["nome"];

    $surname = $d ["cognome"];
    $name = ereg_replace ("'","\\'",$d ["nome"]);
    $n = "$name $surname";

    $pass = $d ["pw_picard"];
    if ($pass == '')
      $pass = '.';

    if ($d ["fw_email"]=="")
      echo $n . " " . $d ["nickname"] . " " . $pass . "\n";
    else
      echo $n . " " . $d ["nickname"] . " " . $pass . " " . $d ["fw_email"] . "\n";
  }
}

function sendsocifile($trust = false)
{
  if (userperm("admin") || $trust == true)
  {
    header("Content-Type: application/txt");
    header("Content-Disposition: attachment; filename=\"soci\"");

    echo "# Nome Cognome nick password\n".
         "# Nome Cognome nick . indirizzo@di.destinazione\n".
         "# la password può essere sostituita da un . punto\n".
         "\n";

    if ($r=mysql_query("select * from users_picard where attivo=\"S\" order by nickname"))
      socifile_format_rows ($r);

    /*
      TODO  Pannello grafico di amministrazione della tabella users_picard_hardwire,
            simile a users_picard ma usata per indicizzare gli account di servizio
    */
    if ($r=mysql_query("select * from users_picard_hardwire order by nickname"))
      socifile_format_rows ($r);
  }
  else
    header("Location: .");
}

function sendsocifeeds($trust = false)
{
  if (userperm("admin") || $trust == true)
  {
    header("Content-Type: application/txt");
    header("Content-Disposition: attachment; filename=\"feeds\"");

    echo "# nick url\n";

    if ($r=mysql_query("select * from users_picard where attivo=\"S\" and blog_feed is not null and blog_feed != '' order by nickname"))
      while ($d=mysql_fetch_assoc($r))
        echo $d["nickname"]." ".$d["blog_feed"]."\n";
  }
  else {
    header("Location: .");
  }
}

function is_email($email)
{
  return eregi("^([-a-z0-9_\.\+])+@(([a-z0-9_-])+\\.)+[a-z]{2,6}$",$email);
}

?>
