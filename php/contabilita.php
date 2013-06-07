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

include $UNIXDIR["php"]."contabilita1.php";
include $UNIXDIR["php"]."contabilita2.php";

function contabilita_mastrilista()
{
  html_pagehead("Contabilità", array ('Contabilità' => 'contabilita', 'Conti' => 'contabilita&action=conti'));

  if ($r=mysql_query("select tipo,mastro,sum(dare) as d,sum(avere) as a from conti_righe as r ".
    "left join conti_sottoconti as s on s.id=r.id_sottoconto ".
    "left join conti_conti as c on c.id=s.id_conto ".
    "left join conti_mastri as m on m.id=c.id_mastro group by mastro order by ordine,id_mastro;"))
  {
    echo "<H2>Mastri contabili</H2>\n";
    html_opentable();
    html_tableintest(array("Tipo","Mastro","Dare","Avere"));
    while ($d=mysql_fetch_assoc($r))
      html_tabledata(array(htmlentities($d["tipo"],ENT_COMPAT,"UTF-8"),$d["mastro"],$d["d"],$d["a"]));
    html_closetable();
  }
  html_pagetail();
}

function contabilita_mastri()
{
  contabilita_mastrilista();
}


#=====================================


function contabilita_contilista()
{
  html_pagehead("Contabilità", array ('Contabilità' => 'contabilita', 'Mastri' => 'contabilita&action=mastri'));

  if ($r=mysql_query("select tipo,mastro,conto,id_conto,sum(dare) as d,sum(avere) as a from conti_righe as r ".
    "left join conti_sottoconti as s on s.id=r.id_sottoconto ".
    "left join conti_conti as c on c.id=s.id_conto ".
    "left join conti_mastri as m on m.id=c.id_mastro group by conto order by ordine,id_mastro,conto;"))
  {
    echo "<H2>Conti</H2>\n";
    html_opentable();
    $m="";
    html_tableintest(array("Mastro","Conto","Dare","Avere"));

    while ($d=mysql_fetch_assoc($r))
    {
      if ($m!=$d["tipo"])
      {
        $m=$d["tipo"];
        html_tabledata(array("<EM>".htmlentities($d["tipo"],ENT_COMPAT,"UTF-8")."</EM>","","",""));
      }

      html_tabledata(array($d["mastro"],
        "<A HREF=\"?function=contabilita&action=sottoconti&id_conto=".$d["id_conto"]."\">".$d["conto"]."</A>",
        $d["d"],$d["a"]));
    }

    html_closetable();
  }
  html_pagetail();
}


function contabilita_contibilancio($r,$a)
{
  html_opentable();
  $m="";
  html_tableintest(array("Mastro","Conto",""));
  while ($d=mysql_fetch_assoc($r))
  {
    if ($m!=$d["tipo"])
    {
      if ($m!="")
        html_tabledata(array("<STRONG>TOTALE ".htmlentities($m,ENT_COMPAT,"UTF-8")."</STRONG>","","<STRONG>".sprintf("%.2f",$t)."</STRONG>"));
      $m=$d["tipo"];
      $t=0;
      html_tabledata(array("<EM>".htmlentities($d["tipo"],ENT_COMPAT,"UTF-8")."</EM>","",""));
    }
    if ((substr($m,0,4)=="atti") || ($m=="costi"))
      $i=$d["d"]-$d["a"];
    else
      $i=$d["a"]-$d["d"];
    $t+=$i;
    if ($i>=0)
      $i1=sprintf("%.2f",$i);
    else
      $i1=sprintf("<FONT COLOR=\"#ff0000\">%.2f</FONT>",$i);
    if ($i!=0)
      html_tabledata(array($d["mastro"],
        "<A HREF=\"?function=contabilita&action=sottoconti&id_conto=".$d["id_conto"].$a."\">".$d["conto"]."</A>",$i1));
  }
  html_tabledata(array("<STRONG>TOTALE ".htmlentities($m,ENT_COMPAT,"UTF-8")."</STRONG>","","<STRONG>".sprintf("%.2f",$t)."</STRONG>"));
  html_closetable();
}


function contabilita_bilancioanno($a)
{
  html_pagehead("Contabilità", array ('Contabilità' => 'contabilita', 'Mastri' => 'contabilita&action=mastri'));

  if ($r=mysql_query("select distinct year(data) as d from conti_movimenti order by d"))
  {
    $d=mysql_fetch_assoc($r);
    echo "<H2>Bilanci passati</H2>\n";
    while ($d=mysql_fetch_assoc($r))
      echo "<A HREF=\"?function=contabilita&action=testbilancio&anno=".$d["d"]."\">[".$d["d"]."]</A>\n";
  }
  if ($r=mysql_query("select tipo,mastro,conto,id_conto,sum(dare) as d,sum(avere) as a from conti_righe as r ".
    "left join conti_sottoconti as s on s.id=r.id_sottoconto ".
    "left join conti_movimenti as v on v.id=r.id_movimento ".
    "left join conti_conti as c on c.id=s.id_conto ".
    "left join conti_mastri as m on m.id=c.id_mastro ".
    "where year(v.data)<=$a and m.ordine!=0 group by conto order by ordine,id_mastro,conto;"))
  {
    echo "<H2>Bilancio $a</H2>\n";
    contabilita_contibilancio($r,"&anno=$a");
  }
  html_pagetail();
}


function contabilita_testbilancio()
{
  html_pagehead("Contabilità", array ('Contabilità' => 'contabilita', 'Mastri' => 'contabilita&action=mastri'));

  if ($r=mysql_query("select distinct year(data) as d from conti_movimenti order by d"))
  {
    $d=mysql_fetch_assoc($r);
    ?>

    <h2>Bilanci Passati</h2>

    <?php while ($d=mysql_fetch_assoc($r)): ?>
    <a class="btn" href="?function=contabilita&action=testbilancio&anno=<?php echo $d["d"] ?>"><?php echo $d["d"] ?></a>
    <?php endwhile; ?>

    <?php
  }
  if ($r=mysql_query("select tipo,mastro,conto,id_conto,sum(dare) as d,sum(avere) as a from conti_righe as r ".
    "left join conti_sottoconti as s on s.id=r.id_sottoconto ".
    "left join conti_conti as c on c.id=s.id_conto ".
    "left join conti_mastri as m on m.id=c.id_mastro ".
    "where m.ordine!=0 group by conto order by ordine,id_mastro,conto;"))
  {
    echo "<H2>Bilancio provvisorio</H2>\n";
    contabilita_contibilancio($r,"");
  }
  html_pagetail();
}

function contabilita_conti()
{
  contabilita_contilista();
}


#=====================================


function contabilita_sottocontielenco($id_conto)
{
  html_pagehead("Contabilità", array ('Contabilità' => 'contabilita', 'Conti' => 'contabilita&action=conti'));

  if (is_numeric($a=http_getparm("anno")))
    $l="&anno=$a";
  if ($r=mysql_query("select mastro,conto,sottoconto,id_sottoconto,sum(dare) as d,sum(avere) as a from conti_righe as r ".
    "left join conti_sottoconti as s on s.id=r.id_sottoconto ".
    "left join conti_conti as c on c.id=s.id_conto ".
    "left join conti_mastri as m on m.id=c.id_mastro ".
    "where id_conto=$id_conto group by sottoconto;"))
  {
    echo "<H2>Sottoconti</H2>\n";
    html_opentable();
    $d=mysql_fetch_assoc(mysql_query("select * from conti_conti as c left join conti_mastri as m on m.id=c.id_mastro where c.id=$id_conto"));
    html_tablefieldrow("Mastro",$d["mastro"]);
    html_tablefieldrow("Conto",$d["conto"]);
    $d=mysql_fetch_assoc(mysql_query("select sum(dare) as d,sum(avere) as a from conti_righe as r ".
      "left join conti_sottoconti as s on s.id=r.id_sottoconto ".
      "left join conti_conti as c on c.id=s.id_conto ".
      "left join conti_mastri as m on m.id=c.id_mastro where c.id=$id_conto"));
    html_tablefieldrow("Totale dare",$d["d"]);
    html_tablefieldrow("Totale avere",$d["a"]);
    html_closetable();
    html_opentable();
    html_tableintest(array("Sottoconto","Dare","Avere"));
    while ($d=mysql_fetch_assoc($r))
      html_tabledata(array(
        "<A HREF=\"?function=contabilita&action=sottoconti&id_sottoconto=".$d["id_sottoconto"].$l."\">".$d["sottoconto"],
        $d["d"],$d["a"]));
    html_closetable();

    if (userperm("admin"))
    {
      html_openform("",array("function"=>"contabilita","action"=>"newsottoconto","id_conto"=>$id_conto));
      html_tableformtext(array(),"Nuovo sottoconto","sottoconto",30);
      html_tableformsubmit("Crea");
      html_closeform();
    }
  }
  html_pagetail();
}

function contabilita_newsottoconto()
{
  $d["id_conto"]=http_getparm("id_conto");
  $d["sottoconto"]=http_getparm("sottoconto");
  my_insert("conti_sottoconti",$d);
  header("Location: ?function=contabilita&action=sottoconti&id_conto=".$d["id_conto"]);
}

function contabilita_newscrimborsospese()
{
  if (is_numeric($d["id_socio"]=http_getparm("id_socio")))
  {
    $c=mysql_fetch_assoc(mysql_query("select * from conti_conti where conto like \"Debiti vs soci%\""));
    $s=mysql_fetch_assoc(mysql_query("select * from soci_iscritti where id=".$d["id_socio"]));
    $d["id_conto"]=$c["id"];
    $d["sottoconto"]="Debiti rimborso spese ".$s["cognome"]." ".$s["nome"];
    my_insert("conti_sottoconti",$d);
  }
  header("Location: ?function=sociils&action=iscritti&show=".$d["id_socio"]);
}

function contabilita_sottocontimovimenti($id_sottoconto)
{
  $d=mysql_fetch_assoc(mysql_query("select * from conti_sottoconti where id=$id_sottoconto"));
  $c=mysql_fetch_assoc(mysql_query("select * from conti_conti as c left join conti_mastri as m on m.id=c.id_mastro where c.id=".$d["id_conto"]));
  $s=mysql_fetch_assoc(mysql_query("select sum(dare) as d,sum(avere) as a from conti_righe as r ".
    "left join conti_sottoconti as s on s.id=r.id_sottoconto where s.id=$id_sottoconto"));

  html_pagehead("Contabilità", array ('Contabilità' => 'contabilita', 'Conti' => 'contabilita&action=sottoconti&id_conto=' . $d["id_conto"]));

  if (is_numeric($a=http_getparm("anno")))
    $w="and year(m.data)=$a";
  if ($r=mysql_query("select id_movimento,data,descrizione,sum(dare) as d,sum(avere) as a from conti_righe as r ".
    "left join conti_sottoconti as s on s.id=r.id_sottoconto ".
    "left join conti_movimenti as m on m.id=r.id_movimento ".
    "where id_sottoconto=$id_sottoconto $w group by id_movimento order by data,descrizione;"))
  {
    echo "<H2>Sottoconti</H2>\n";
    html_opentable();
    html_tablefieldrow("Mastro",$c["mastro"]);
    html_tablefieldrow("Conto",$c["conto"]);
    html_tablefieldrow("Sottoconto",$d["sottoconto"]);
    html_tablefieldrow("Totale dare",$s["d"]);
    html_tablefieldrow("Totale avere",$s["a"]);
    html_closetable();
    html_opentable();
    html_tableintest(array("Data","Movimento","Dare","Avere"));
    while ($d=mysql_fetch_assoc($r))
      html_tabledata(array($d["data"],
        "<A HREF=\"?function=contabilita&action=movimenti&show=".$d["id_movimento"]."\">".$d["descrizione"]."</A>",
        $d["d"],$d["a"]));
    html_closetable();
  }
  html_pagetail();
}

function contabilita_sottoconti()
{
  if (is_numeric($id_conto=http_getparm("id_conto")))
    contabilita_sottocontielenco($id_conto);
  else
  if (is_numeric($id_sottoconto=http_getparm("id_sottoconto")))
    contabilita_sottocontimovimenti($id_sottoconto);
  else
    contabilita_menu();
}


#=====================================


function contabilita_movimentodrawform($d,$subm,$errmsg)
{
  html_tableformerrormsg($errmsg);
  html_tableformstatic("Data",$d["data"]);
  html_tableformtext($d,"Descrizione","descrizione",50);
  html_tableformtextarea($d,"Note","note");
  html_tableformsubmit($subm);
  html_closeform();
}

function contabilita_movimentopost()
{
  $s["descrizione"]=http_getparm("descrizione");
  $s["note"]=http_getparm("note");
  return $s;
}

function contabilita_movimentocheckform($s)
{
  if ($s["descrizione"]=="")
    return "Descrizione vuota non accettabile";
  return "";
}

function contabilita_movimentoform($id)
{
  html_pagehead("Crea scrittura contabile", array ('Contabilità' => 'contabilita',
                                                   'Conti' => 'contabilita&action=conti',
                                                   'Movimento' => "contabilita&action=movimenti&show=".$id));

  if ($d=mysql_fetch_assoc(mysql_query("select * from conti_movimenti where id=$id")))
  {
    html_openform(".",array("function"=>"contabilita","action"=>"movimenti","edit"=>"$id","confermadati"=>"ok"));
    contabilita_movimentodrawform($d,"Modifica","");
    contabilita_movimentoshow2($d);
  }
  html_pagetail();
}

function contabilita_movimentogetform($id)
{
  $s=contabilita_movimentopost();
  $e=contabilita_movimentocheckform($s);
  if ($e!="")
  {
    html_pagehead("Crea scrittura contabile", array ('Contabilità' => 'contabilita',
                                                     'Conti' => 'contabilita&action=conti',
                                                     'Movimento' => "contabilita&action=movimenti&show=".$id));

    $d=mysql_fetch_assoc(mysql_query("select * from conti_movimenti where id=$id"));
    $s["descrizione"]=$d["descrizione"];
    $s["data"]=$d["data"];
    html_openform(".",array("function"=>"contabilita","action"=>"movimenti","edit"=>"$id","confermadati"=>"ok"));
    contabilita_movimentodrawform($s,"Modifica",$e);
    contabilita_movimentoshow2($d);
    html_pagetail();
  }
  else
  {
    $i=my_update("conti_movimenti",$s,"id",$id);
    header("Location: ?function=contabilita&action=movimenti&show=".$id);
  }
}

function contabilita_movimentoshow1($d1)
{
  html_opentable();
  html_tablefieldrow("Data",$d1["data"]);
  html_tablefieldrow("Descrizione",$d1["descrizione"]);
  html_tablefieldrownl("Note",$d1["note"]);
  html_closetable();
}

function contabilita_movimentoshow2($d1)
{
  if ($r=mysql_query("select * from conti_righe as r left join conti_sottoconti as s ".
    "on s.id=r.id_sottoconto where id_movimento=".$d1["id"]))
  {
    $dare=0;
    $avere=0;
    html_opentable();
    html_tableintest(array("Conto","Dare","Avere"));
    while ($d=mysql_fetch_array($r))
    {
      html_tabledata(array(
        "<A HREF=\"?function=contabilita&action=sottoconti&id_sottoconto=".$d["id_sottoconto"]."\">".$d["sottoconto"]."</A>",
        $d["dare"],$d["avere"]));
      $dare+=$d["dare"];
      $avere+=$d["avere"];
    }
    html_tabledata(array(sprintf("diff. dare-avere (%.2f)",$dare-$avere),sprintf("%.2f",$dare),sprintf("%.2f",$avere)));
    html_closetable();
  }
  if (mysql_num_rows($r=mysql_query("select *,year(contabile) as ycont from banche_righe as r ".
    "left join banche as b on b.id=r.id_banca ".
    "left join conti_righe as c on c.id=r.id_riga where id_movimento=".$d1["id"]))>0)
  {
    echo "<H3>Dettagli banca</H3>\n";
    html_opentable();
    html_tableintest(array("Banca","Importo","Descrizione"));
    while ($d=mysql_fetch_assoc($r))
      html_tabledata(array(
        "<A HREF=\"?function=banche&id_banca=".$d["id_banca"]."&ec=".$d["ycont"]."\">".$d["nomebanca"]."</A>",
        $d["importo"],strtolower($d["descrizione"])));
    html_closetable();
  }
  if (mysql_num_rows($r=mysql_query("select *,i.note as nsocio from soci_quote as q ".
    "left join soci_iscritti as i on i.id=q.id_socio ".
    "left join conti_righe as c on c.id=q.id_riga where id_movimento=".$d1["id"]))>0)
  {
    echo "<H3>Dettagli quota soci</H3>\n";
    html_opentable();
    html_tableintest(array("Data vesamento","Anno","Socio","Note"));
    while ($d=mysql_fetch_assoc($r))
      html_tabledata(array($d["data_versamento"],$d["anno"],
        "<A HREF=\"?function=sociils&action=iscritti&show=".$d["id_socio"]."\">".$d["cognome"]." ".$d["nome"]."</A>",
        $d["nsocio"]));
    html_closetable();
  }
}

function contabilita_movimentoshow($id)
{
  html_pagehead("Crea scrittura contabile", array ('Contabilità' => 'contabilita', 'Conti' => 'contabilita&action=conti'));

  if ($d=mysql_fetch_array(mysql_query("select * from conti_movimenti where id=".$id)))
  {
    contabilita_movimentoshow1($d);
    echo "<P><A HREF=\"?function=contabilita&action=movimenti&edit=".$d["id"]."\">[Modifica]</A>\n";
    contabilita_movimentoshow2($d);
  }
  html_pagetail();
}


#=====================================


function contabilita_movimentianno($a)
{
  html_pagehead("Contabilità", array ('Contabilità' => 'contabilita',
                                      'Conti' => 'contabilita&action=conti',
                                      'Movimento' => "contabilita&action=movimenti&show=".$id));

  if ($r1=mysql_query("select * from conti_movimenti where year(data)=$a order by data"))
  {
    html_opentable();
    html_tableintest(array("Data","Movimento"));
    while ($d1=mysql_fetch_array($r1))
      html_tabledata(array("<A HREF=\"?function=contabilita&action=movimenti&show=".$d1["id"]."\">".$d1["data"]."</A>",$d1["descrizione"]));
    html_closetable();
  }
  html_pagetail();
}

function contabilita_movimentianni()
{
  html_pagehead("Contabilità", array ('Contabilità' => 'contabilita', 'Conti' => 'contabilita&action=conti'));

  if ($r1=mysql_query("select year(data) as a, count(*) as m from conti_movimenti group by a order by a"))
  {
    html_opentable();
    html_tableintest(array("Anno","Numero movimento"));
    while ($d1=mysql_fetch_array($r1))
      html_tabledata(array("<A HREF=\"?function=contabilita&action=movimenti&anno=".$d1["a"]."\">".$d1["a"]."</A>",$d1["m"]));
    html_closetable();
  }
  html_pagetail();
}

function contabilita_movimenti()
{
  if (is_numeric($id=http_getparm("edit")))
  {
    if (http_getparm("confermadati")=="ok")
      contabilita_movimentogetform($id);
    else
      contabilita_movimentoform($id);
  }
  else
  if (is_numeric($id=http_getparm("show")))
    contabilita_movimentoshow($id);
  else
  if (is_numeric($a=http_getparm("anno")))
    contabilita_movimentianno($a);
  else
    contabilita_movimentianni();
}

function contabilita_incompleta()
{
  html_pagehead("Contabilità", array ('Contabilità' => 'contabilita'));

  mysql_query("create temporary table tmp_checkq select \"banca\" as tipo,id,contabile as data,0 as anno,importo,\"\" as socio,descrizione ".
    "from banche_righe where isnull(id_riga)");
  mysql_query("alter table tmp_checkq change socio socio varchar(100)");
  mysql_query("insert into tmp_checkq select \"quota\",data_versamento,anno,0,concat(cognome,\" \",nome),q.note ".
    "from soci_quote as q left join soci_iscritti as i on i.id=q.id_socio where isnull(id_riga)");
  if ($r=mysql_query("select * from tmp_checkq order by data,tipo"))
  {
    html_opentable();
    html_tableintest(array("data","a/imp","note"));
    while ($d=mysql_fetch_assoc($r))
    {
      if ($d["anno"]>0)
        $e=$d["anno"];
      else
        $e=$d["importo"];
      if (userperm("admin"))
        $l="<a href=\"?function=contabilita&action=contabilita1&rigabanca=".$d["id"]."\">".$d["data"]."</a>";
      else
        $l=$d["data"];
      html_tabledata(array($l,$e,strtolower($d["descrizione"])));
    }
    html_closetable();
  }
  html_pagetail();
}


#=====================================


function contabilita_bilanciodachiudere()
{
  return (mysql_num_rows(mysql_query(
    "select * from conti_movimenti where data=".date("Y")."0101 and descrizione like \"Conti a bilancio%\""))==0);
}

function contabilita_calcolachiusura()
{
  $a=date("Y")-1;
  $c["anno"]=$a;
  $c["saldo"]=0;
  $q="select sum(dare) as sumd, sum(avere) as suma, r.id_sottoconto as sc, ".
    "conto, sottoconto, mastro, tipo from conti_righe as r ".
    "left join conti_movimenti as m on m.id=r.id_movimento ".
    "left join conti_sottoconti as s on s.id=r.id_sottoconto ".
    "left join conti_conti as c on c.id=s.id_conto ".
    "left join conti_mastri as ms on ms.id=c.id_mastro ".
    "where year(data)=$a and (data!=".$a."0101 or descrizione not like \"Conti a bilancio%\") ".
    "and (tipo=\"costi\" or tipo=\"ricavi\") ".
    "group by sc order by suma desc ,sumd desc";
  $c["saldo"]=0;
  if ($r=mysql_query($q))
    while ($d=mysql_fetch_assoc($r))
    {
      $c["chiusura"][$d["sc"]]=$d;
      $c["saldo"]+=$d["suma"]-$d["sumd"];
    }
  $c["rigachiusura"]["movimento"]["descrizione"]="Conti a bilancio ".$c["anno"];
  $c["rigachiusura"]["movimento"]["data"]=date("Y")."0101";
  if ($c["saldo"]>0)
  {
    $d=mysql_fetch_array(mysql_query("select * from conti_conti where conto=\"Avanzi di gestione\""));
    $c["rigachiusura"]["sottoconto"]["id_conto"]=$d["id"];
    $c["rigachiusura"]["sottoconto"]["sottoconto"]="Avanzo gestione $a";
    $c["rigachiusura"]["riga"]["avere"]=$c["saldo"];
    $c["rigachiusura"]["riga"]["dare"]="0.00";
  }
  if ($c["saldo"]<0)
  {
    $d=mysql_fetch_array(mysql_query("select * from conti_conti where conto=\"Disavanzi di gestione\""));
    $c["rigachiusura"]["sottoconto"]["id_conto"]=$d["id"];
    $c["rigachiusura"]["sottoconto"]["sottoconto"]="Disavanzo gestione $a";
    $c["rigachiusura"]["riga"]["dare"]=$c["saldo"];
    $c["rigachiusura"]["riga"]["avere"]="0.00";
  }
  $c["sociiscritti"]=mysql_fetch_assoc(
    mysql_query("select sum(quota) as ricavo,count(*) as num from soci_iscritti as i ".
      "left join soci_tipi as t on t.id=i.id_tipo where data_espulsione=00000000"));
  $d=mysql_fetch_assoc(mysql_query("select * from conti_conti where conto=\"Anticipi quote ".date("Y")."\""));
  $c["anticipi"]=mysql_fetch_assoc(
    mysql_query("select sum(avere) as debito,count(*) as num from conti_righe as r ".
      "left join conti_sottoconti as s on s.id=r.id_sottoconto where s.id_conto=".$d["id"]));
  return $c;
}

function contabilita_chiudibilancio1()
{
  html_pagehead("Contabilità", array ('Contabilità' => 'contabilita'));

  $c=contabilita_calcolachiusura();
  echo "<H1>Costi/Ricavi/Avanzo/Disavanzo</H1>\n";
  html_opentable();
  html_tableintest(array("tipo","sottoconto","dare","avere"));
  foreach($c["chiusura"] as $s)
    html_tabledata(array($s["tipo"],$s["sottoconto"],$s["sumd"],$s["suma"]));
  html_tabledata(array("",$c["rigachiusura"]["sottoconto"]["sottoconto"],
    $c["rigachiusura"]["riga"]["dare"],$c["rigachiusura"]["riga"]["avere"]));
  html_closetable();
  echo "<H2>Situazione soci ".date("Y")."</H2>\n";
  html_opentable();
  html_tablefieldrow("Soci iscritti",$c["sociiscritti"]["num"]);
  html_tablefieldrow("Ricavo quote",$c["sociiscritti"]["ricavo"]);
  html_tablefieldrow("Quote anticipate",$c["anticipi"]["num"]);
  html_tablefieldrow("Debiti anticipi",$c["anticipi"]["debito"]);
  html_closetable();
  echo "<P><A HREF=\"?function=contabilita&action=chiudibilancio&conferma=ok\">[Conferma chiusura]</A>\n";
  html_pagetail();
}

function contabilita_chiudibilancio2()
{
  $c=contabilita_calcolachiusura();
##### CHIUSURA BILANCIO
  $idm=my_insert("conti_movimenti",$c["rigachiusura"]["movimento"]);
  $ids=my_insert("conti_sottoconti",$c["rigachiusura"]["sottoconto"]);
  foreach($c["chiusura"] as $s)
  {
    $r["id_sottoconto"]=$s["sc"];
    $r["id_movimento"]=$idm;
    $r["valuta"]=$c["anno"]."1231";
    $r["dare"]=-$s["sumd"];
    $r["avere"]=-$s["suma"];
    my_insert("conti_righe",$r);
  }
  $r["id_sottoconto"]=$ids;
  $r["dare"]=$c["rigachiusura"]["riga"]["dare"];
  $r["avere"]=$c["rigachiusura"]["riga"]["avere"];
  my_insert("conti_righe",$r);
  $q="select s.id as id_socio,quota,sc.id as id_sottoconto,cognome,nome from soci_iscritti as s ".
    "left join soci_tipi as t on t.id=s.id_tipo ".
    "left join conti_sottoconti as sc on s.id=sc.id_socio and sottoconto like \"Conferimenti socio %\" ".
    "where data_espulsione=00000000";
##### RICAVI DA QUOTE SOCI
  $contoconf=mysql_fetch_assoc(mysql_query("select * from conti_conti where conto=\"Crediti conferimento nostri associati\""));
  if ($r=mysql_query($q))
    while ($d=mysql_fetch_assoc($r))
    {
      if ($d["id_sottoconto"]=="")
      {
        $nsc["id_conto"]=$contoconf["id"];
        $nsc["id_socio"]=$d["id_socio"];
        $nsc["sottoconto"]="Conferimenti socio ".$d["cognome"]." ".$d["nome"];
        $d["id_sottoconto"]=my_insert("conti_sottoconti",$nsc);
      }
      $conf[$d["id_socio"]]=$d;
    }
  $m["descrizione"]="Generazione ricavi quote soci anno ".date("Y");
  $m["data"]=date("Y")."0101";
  $idm=my_insert("conti_movimenti",$m);  
  unset($r);
  $r["id_movimento"]=$idm;
  $r["valuta"]=$m["data"];
  $r["avere"]=0;
  foreach($conf as $d)
  {
    $r["id_sottoconto"]=$d["id_sottoconto"];
    $r["dare"]=$d["quota"];
    my_insert("conti_righe",$r);
  }
  $c_ricavi=mysql_fetch_assoc(mysql_query("select * from conti_conti where conto=\"Contributi ns associati correnti e arret\""));
  $nsc["id_conto"]=$c_ricavi["id"];
  $nsc["sottoconto"]="Contributi associativi ".date("Y");
  $r["id_sottoconto"]=my_insert("conti_sottoconti",$nsc);
  $r["avere"]=$c["sociiscritti"]["ricavo"];
  $r["dare"]=0;
  my_insert("conti_righe",$r);
##### AZZERAMENTO ANTICIPI
  $q="select sum(avere-dare) as anticipo,id_socio,id_sottoconto,sottoconto from conti_righe as r ".
    "left join conti_sottoconti as s on s.id=r.id_sottoconto ".
    "where sottoconto like \"Anticipo conferimenti %\" group by id_sottoconto having anticipo>0";
  if (($r=mysql_query($q)) && (mysql_num_rows($r)>0))
  {
    unset($m);
    $m["descrizione"]="Azzeramento anticipo quote anno ".date("Y");
    $m["data"]=date("Y")."0101";
    $idm=my_insert("conti_movimenti",$m);
    $nr["id_movimento"]=$idm;
    $nr["valuta"]=$m["data"];
    while ($d=mysql_fetch_assoc($r))
    {
      $nr["id_sottoconto"]=$d["id_sottoconto"];
      $nr["dare"]=$d["anticipo"];
      $nr["avere"]=0;
      my_insert("conti_righe",$nr);
      $nr["id_sottoconto"]=$conf[$d["id_socio"]]["id_sottoconto"];
      $nr["dare"]=0;
      $nr["avere"]=$d["anticipo"];
      my_insert("conti_righe",$nr);
    }
  }
  header("Location: ?function=contabilita&action=testbilancio");
}

function contabilita_chiudibilancio()
{
  if (http_getparm("conferma")=="ok")
    contabilita_chiudibilancio2();
  else
    contabilita_chiudibilancio1();
}


#=====================================



function contabilita_menu()
{
  html_pagehead("Contabilità");
  ?>

  <div class="row">
    <div class="span6">
      <ul class="nav nav-pills nav-stacked">
        <li><a href="?function=contabilita&action=mastri">Mastri contabili</a><li>
        <li><a href="?function=contabilita&action=conti">Conti</a></li>
        <li><a href="?function=contabilita&action=movimenti">Movimenti contabili</a></li>
        <li><a href="?function=contabilita&action=testbilancio">Bilancio provvisorio</a></li>
        <li><a href="?function=contabilita&action=incomplete">Eventi da contabilizzare</a></li>

        <?php if (array_key_exists ('contab1', $_SESSION) && is_array($_SESSION["contab1"])): ?>
          <li><a href="?function=contabilita&action=contabilita1">Completa scrittura contabile</a></li>
        <?php else: ?>
          <li><a href="?function=contabilita&action=contabilita1">Nuova scrittura contabile</a></li>
        <?php endif; ?>

        <li><a href="?function=contabilita&action=ricevute">Emissione ricevute</a></li>
        <?php if (contabilita_bilanciodachiudere()): ?>
        <li><a href="?function=contabilita&action=chiudibilancio">Chiusura bilancio <?php echo (date("Y") - 1) ?></a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <?php
  html_pagetail();
}

function contabilita()
{
  if (!userperm("banche"))
    header ("Location: .");
  else
  if (http_getparm("action")=="movimenti")
    contabilita_movimenti();
  else
  if (http_getparm("action")=="mastri")
    contabilita_mastri();
  else
  if (http_getparm("action")=="conti")
    contabilita_conti();
  else
  if ((http_getparm("action")=="testbilancio") && is_numeric($a=http_getparm("anno")))
    contabilita_bilancioanno($a);
  else
  if (http_getparm("action")=="testbilancio")
    contabilita_testbilancio();
  else
  if (http_getparm("action")=="sottoconti")
    contabilita_sottoconti();
  else
  if (http_getparm("action")=="newsottoconto" && userperm("admin"))
    contabilita_newsottoconto();
  else
  if (http_getparm("action")=="newscrimborsospese" && userperm("admin"))
    contabilita_newscrimborsospese();
  else
  if (http_getparm("action")=="incomplete" && userperm("admin"))
    contabilita_incompleta();
  else
  if (http_getparm("action")=="contabilita1" && userperm("admin"))
    contabilita1();
  else
  if (http_getparm("action")=="chiudibilancio")
    contabilita_chiudibilancio();
  else
  if (http_getparm("action")=="ricevute")
    contabilita2_ricevute();
  else
    contabilita_menu();
}

?>
