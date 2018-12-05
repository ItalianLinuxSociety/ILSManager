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

function bank_info_box ($d)
{
  $s=mysql_fetch_array(mysql_query("select sum(importo) as s from banche_righe where id_banca=".$d['id']));

  html_infobox (array (
    'Banca' => $d['nomebanca'],
    'IBAN' => $d['iban'],
    'Saldo' => $s['s'],
    'Note' => $d['note']
  ));
}

function banche_listamovimenti()
{
  html_pagehead("Banche");

  if ($r=mysql_query("select id_movimento,contabile,b.descrizione,importo from banche_righe as b ".
    "left join conti_righe as r on r.id=b.id_riga ".
    "left join conti_movimenti as m on m.id=r.id_movimento ".
    "where contabile>=20080101 order by contabile,b.valuta,causale,descrizione"))
  {
    html_opentable();
    html_tableintest(array("Contabile","Descrizione","Importo"));
    while ($d=mysql_fetch_array($r))
    {
      html_tabledata(array($d["contabile"],strtolower($d["descrizione"]),$d["importo"]));
    }
    html_closetable();
  }
  html_pagetail();
}


function banche_bancaestratto($id,$y)
{
  html_pagehead("Banche E/C", array ('Banche' => 'banche', 'Banca' => "banche&id_banca=$id"));

  $d=mysql_fetch_array(mysql_query("select * from banche where id=$id"));
  bank_info_box ($d);

  if ($r=mysql_query("select *,b.valuta as valuta from banche_righe as b left join conti_righe as c on b.id_riga=c.id ".
    "where id_banca=$id and year(contabile)=$y order by contabile,b.valuta"))
  {
    html_opentable();
    html_tableintest(array("Contabile","Valuta","Descrizione","Importo"));
    $d=mysql_fetch_array(mysql_query("select sum(importo) as s from banche_righe where id_banca=$id and year(contabile)<$y"));
    html_tabledata(array(($y-1)."-12-31","","SALDO ".($y-1),$d["s"]));
    while ($d=mysql_fetch_array($r))
    {
      if ($d["id_riga"]>0)
        $c="<A HREF=\"?function=contabilita&action=movimenti&show=".$d["id_movimento"]."\" TARGET=\"_blanc\">".$d["contabile"]."</A>";
      else
        $c=$d["contabile"];
      html_tabledata(array($c,$d["valuta"],strtolower($d["descrizione"]),$d["importo"]));
    }
    if (date("Y")>$y)
      $f=$y."-12-31";
    else
    {
      $d=mysql_fetch_array(mysql_query("select max(contabile) as d from banche_righe where id_banca=$id and year(contabile)=$y"));
      $f=$d["d"];
    }
    $d=mysql_fetch_array(mysql_query("select sum(importo) as s from banche_righe where id_banca=$id and year(contabile)<=$y"));
    html_tabledata(array($f,"","SALDO ".$y,$d["s"]));
    html_closetable();
  }
  html_pagetail();
}

function banche_bancaform($d,$err,$subm)
{
  html_pagehead("Banche", array ('Banche' => 'banche', 'Banca' => "banche&id_banca=$id"));

  html_openform(".",array("function"=>"banche","id_banca"=>$d["id"],"edit"=>"ok"));
  html_tableformerrormsg($err);
  html_tableformtext($d,"Nome banca","nomebanca",20);
  html_tableformtext($d,"IBAN","iban",27);
  html_tableformtextarea($d,"Note","note");
  html_tableformsubmit($subm);
  html_closeform();
  html_pagetail();
}

function banche_bancaget($id)
{
  $d["nomebanca"]=http_getparm("nomebanca");
  $d["iban"]=http_getparm("iban");
  $d["note"]=http_getparm("note");
  $d["id"]=$id;
  if ($d["iban"]=="")
    $e="L'iban della banca non pu&ograve; essere vuoto";
  if ($d["nomebanca"]=="")
    $e="Il nome della banca non pu&ograve; essere vuoto";
  if ($e!="")
    banche_bancaform($d,$e,"modifica");
  else
  {
    my_update("banche",$d,"id",$id);
    header("Location: ?function=banche&id_banca=".$id);
  }
}

function banche_bancaedit($id)
{
  if ($d=mysql_fetch_array(mysql_query("select * from banche where id=".$id)))
    banche_bancaform($d,"","modifica");
}

function banche_bancashow($id)
{
  html_pagehead("Banche", array ('Banche' => 'banche'));

  if ($d=mysql_fetch_array(mysql_query("select * from banche where id=".$id)))
  {
    bank_info_box ($d);

    ?>

    <a href="?function=banche&id_banca=<?php echo $id ?>&edit=form" class="btn">Modifica</a>
    <a href="?function=banche&id_banca=<?php echo $id ?>&upload=1" class="btn btn-primary">Nuove Contabili</a>

    <hr />

    <?php

    if ($r=mysql_query("select distinct year(contabile) as y from banche_righe where id_banca=$id"))
    {
      ?>

      <p>Mostra estratti conto:</p>

      <?php

      $d=mysql_fetch_array($r);

      while ($d=mysql_fetch_assoc($r)) {
        ?>
        <a class="btn" href="?function=banche&id_banca<?php echo $id ?>&ec=<?php echo $d["y"] ?>"><?php echo $d["y"] ?></a>
        <?php
      }
    }

    if ($r=mysql_query("select * from banche_righe where id_banca=$id order by contabile desc ,valuta desc limit 15"))
    {
      html_opentable();
      html_tableintest(array("Contabile","Valuta","Descrizione","Importo"));
      while ($d=mysql_fetch_array($r))
        html_tabledata(array($d["contabile"],$d["valuta"],strtolower($d["descrizione"]),$d["importo"]));
      html_closetable();
    }
  }
  html_pagetail();
}

function banche_bancanuoverighe1($id)
{
  html_pagehead("Banche", array ('Banche' => 'banche'));

  if ($d=mysql_fetch_array(mysql_query("select * from banche where id=".$id)))
  {
    bank_info_box ($d);
    html_openformfile(".",array("function"=>"banche","id_banca"=>$d["id"],"upload"=>"2"));
    html_tableformfile("File estratto conto","ec",50000);
    html_tableformsubmit("Invia");
    html_closeform();
  }
  html_pagetail();
}

function banche_bancanuoverighe2($id)
{
  html_pagehead("Banche", array ('Banche' => 'banche'));

  if ($d=mysql_fetch_array(mysql_query("select * from banche where id=".$id)))
  {
    bank_info_box ($d);
    $s=mysql_fetch_array(mysql_query("select max(contabile) as d from banche_righe where id_banca=".$id));

    $b=file($_FILES["ec"]["tmp_name"],FILE_IGNORE_NEW_LINES);
    unset($_SESSION["nuovecontabili"]);
    $ok=0;
    $nr["id_banca"]=$id;

    if ($d['type'] == 'unicredit') {
      foreach($b as $r)
      {
        if ($ok==1)
        {
          $c=split(";",$r);
          $nr["contabile"]=implode("-",array_reverse(split("/",$c[0])));
          $nr["valuta"]=implode("-",array_reverse(split("/",$c[1])));
          $nr["descrizione"]=$c[2];
          $nr["importo"]=ereg_replace(",","",$c[3]);
          $nr["commissioni"]=0;
          $nr["causale"]=$c[4];
          if ($nr["contabile"]>=$s["d"])
            $_SESSION["nuovecontabili"][]=$nr;
        }
        if ($r=="Data;Valuta;Descrizione;Euro;Caus.")
          $ok=1;
      }
    }
    else if ($d['type'] == 'paypal') {
      foreach ($b as $r)
      {
        if ($ok == 1)
        {
          $c = str_getcsv ($r);
          $nr['contabile']   = implode( '-', array_reverse( explode( '/', trim( $c[0], '"' ) ) ) );
          $nr['valuta']      = trim( $c[6], '"' );
          $nr['descrizione'] = trim( $c[3], '"' ) . ' - ' . trim( $c[12], '"' );
          $nr['importo']     = str_replace( ',', '.',       trim( $c[7], '"'  ) );
          $nr['commissioni'] = ( str_replace( ',', '.',     trim( $c[8], '"'  ) ) ) * -1;

          switch ($c[4]) {
            case 'Payment Received':
              $nr["causale"] = 0;
              break;

            case 'Recurring Payment Received':
              $nr["causale"] = 1;
              break;

            default:
              $nr["causale"] = 2;
              break;
          }

          if ($nr["contabile"]>=$s["d"])
            $_SESSION["nuovecontabili"][]=$nr;
        }
        if (strncmp ($r, 'Date, Time', strlen ('Date, Time')) == 0)
          $ok=1;
      }
    }

    ?>

    <h2>Nuove righe</h2>

    <form method="POST">
      <input type="hidden" name="function" value="banche">
      <input type="hidden" name="id_banca" value="<?php echo $id ?>">
      <input type="hidden" name="upload" value="3">

      <?php

      html_opentable();
      html_tableintest(array("Contabile","Valuta","Descrizione","Importo","aggiungi"));
      for ($i=0; $i<count($_SESSION["nuovecontabili"]); $i++)
      {
        $d=$_SESSION["nuovecontabili"][$i];
        if ($d["contabile"]>$s["d"])
          $sel="<INPUT TYPE=\"checkbox\" NAME=\"contabile".$i."\" CHECKED>";
        else
          $sel="<INPUT TYPE=\"checkbox\" NAME=\"contabile".$i."\">";
        html_tabledata(array($d["contabile"],$d["valuta"],strtolower($d["descrizione"]),$d["importo"],"<CENTER>".$sel."</CENTER>"));
      }
      html_closetable();

      ?>

      <input type="submit" value="Conferma" class="btn btn-primary" />
    </form>

    <h2>Ultima contabile</h2>

    <?php

    html_opentable();
    html_tableintest(array("Contabile","Valuta","Descrizione","Importo"));
    if ($r=mysql_query("select * from banche_righe where id_banca=$id and contabile=\"".$s["d"]."\""))
      while ($d=mysql_fetch_assoc($r))
        html_tabledata(array($d["contabile"],$d["valuta"],strtolower($d["descrizione"]),$d["importo"]));
    html_closetable();
  }
  html_pagetail();
}

function banche_bancanuoverighe3($id)
{
  for ($i=0; $i<count($_SESSION["nuovecontabili"]); $i++)
    if (http_getparm("contabile".$i)=="on")
      my_insert("banche_righe",$_SESSION["nuovecontabili"][$i]);
  unset($_SESSION["nuovecontabili"]);
  header("Location: ?function=banche&id_banca=$id");
}

function banche_banca($id)
{
  if (is_numeric($y=http_getparm("ec")))
    banche_bancaestratto($id,$y);
  else
  if (http_getparm("edit")=="ok")
    banche_bancaget($id);
  else
  if (http_getparm("edit")=="form")
    banche_bancaedit($id);
  else
  if (http_getparm("upload")=="1")
    banche_bancanuoverighe1($id);
  else
  if (http_getparm("upload")=="2")
    banche_bancanuoverighe2($id);
  else
  if (http_getparm("upload")=="3")
    banche_bancanuoverighe3($id);
  else
    banche_bancashow($id);
}


function banche_listabanche()
{
  html_pagehead("Banche");

  mysql_query("create temporary table tmp_banche select * from banche");
  mysql_query("alter table tmp_banche add saldo decimal(10,2) default 0");
  mysql_query("alter table tmp_banche add ultima date default 00000000");
  mysql_query("insert into tmp_banche (id,saldo,ultima) ".
    "select id_banca,sum(importo),max(contabile) from banche_righe group by id_banca");
  if ($r=mysql_query("select id,max(nomebanca) as n,max(iban) as i,max(saldo) as s,max(ultima) as d ".
    "from tmp_banche group by id order by n"))
  {
    html_opentable();
    html_tableintest(array("Banca","Iban","Ultimo movimento","Saldo"));
    while ($d=mysql_fetch_array($r))
    {
      html_tabledata(array("<A HREF=\"?function=banche&id_banca=".$d["id"]."\">".$d["n"]."</A>",$d["i"],$d["d"],$d["s"]));
    }
    html_closetable();
  }
  html_pagetail();
}


function banche()
{
  if (!userperm("banche"))
    header ("Location: .");
  else
  if (is_numeric($i=http_getparm("id_banca")))
    banche_banca($i);
  else
    banche_listabanche();
}


?>
