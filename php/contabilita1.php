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

function contabilita1_sottocontoquota($conto,$sottoconto,$id_socio)
{
  $d=mysql_fetch_assoc(mysql_query("select * from soci_iscritti where id=".$id_socio));
  $s=mysql_fetch_assoc(mysql_query("select * from conti_sottoconti where sottoconto like \"$sottoconto %\" and id_socio=".$id_socio));
  if (!isset($s["id"]))
  {
    $c=mysql_fetch_assoc(mysql_query("select * from conti_conti where conto=\"$conto\""));
    if (!isset($c["id"]))
    {
      $c["id_mastro"]=3;
      $c["conto"]=$conto;
      $cid=my_insert("conti_conti",$c);
      $r["id_conto"]=$cid;
      my_insert("ricevute_conti",$r);
      $c["id"]=$cid;
    }
    $s["id_conto"]=$c["id"];
    $s["id_socio"]=$id_socio;
    $s["sottoconto"]=$sottoconto." ".$d["cognome"]." ".$d["nome"];
    my_insert("conti_sottoconti",$s);
    $s["id"]=mysql_insert_id();
  }
  return $s;
}

function contabilita1_handleparam()
{
  if (http_getparm("azzera")=="ok")
  {
    unset($_SESSION["contab1"]);
    return "?function=contabilita";
  }
  else if ((($data=http_getparm("data"))!="") && !isset($_SESSION["contab1"]["banca"]) && (http_getparm("form")!="main"))
  {
    $_SESSION["contab1"]["data"]=$data;
    return "ok";
  }
  else if (is_numeric($rb=http_getparm("remove")))
  {
    mysql_query('delete from banche_righe where id = ' . $rb);
    return "?function=contabilita&action=incomplete";
  }
  else if (is_numeric($rb=http_getparm("rigabanca")))
  {
    $d=mysql_fetch_assoc(mysql_query("select * from banche_righe as r left join banche as b on b.id=r.id_banca ".
      "left join conti_sottoconti as s on s.id=b.id_sottoconto where r.id=$rb"));
    $b["id_rigabanca"]=$rb;
    $b["sottoconto"]=$d["sottoconto"];
    $b["id_sottoconto"]=$d["id_sottoconto"];
    $b["valuta"]=$d["valuta"];
    $b["dare"]=$b["avere"]=0;
    if ($d["importo"]>0)
      $b["dare"]=$d["importo"];
    else
      $b["avere"]=-$d["importo"];
    $_SESSION["contab1"]["banca"][$rb]="sel";
    $_SESSION["contab1"]["righe"][]=$b;
    return "ok";
  }
  else if (is_numeric($q=http_getparm("id_quota")))
  {
    $d=mysql_fetch_assoc(mysql_query("select *,q.id as id_quota from soci_quote as q left join soci_iscritti as i on i.id=q.id_socio where q.id=$q"));
    if ($d["anno"]<=substr($d["data_versamento"],0,4))
      $s=contabilita1_sottocontoquota("Crediti conferimento nostri associati","Conferimenti socio",$d["id_socio"]);
    else
      $s=contabilita1_sottocontoquota("Anticipi quote ".$d["anno"],"Anticipo conferimenti ".$d["anno"],$d["id_socio"]);
    $b["id_rigaquota"]=$q;
    $b["sottoconto"]=$s["sottoconto"];
    $b["id_sottoconto"]=$s["id"];
    $b["valuta"]=$d["data_versamento"];
    $b["avere"] = annual_fee_amount ();
    $b["dare"]=0;
    $_SESSION["contab1"]["quote"][$q]="sel";
    $_SESSION["contab1"]["righe"][]=$b;
    return "ok";
  }
  else if (is_numeric($s=http_getparm("quotasocio")))
  {
    $d=mysql_fetch_assoc(mysql_query("select max(anno) as a from soci_quote where id_socio=$s"));
    $i=mysql_fetch_assoc(mysql_query("select * from soci_iscritti as i left join soci_tipi as t on t.id=i.id_tipo where i.id=$s"));

    if (is_numeric($_SESSION["contab1"]["anno"][$s]))
      $a=$_SESSION["contab1"]["anno"][$s]+1;
    else if ($d["a"]>0)
      $a=$d["a"]+1;
    else
      $a=$i["anno_iscrizione"];

    $_SESSION["contab1"]["anno"][$s]=$a;
    if ($a<=substr($_SESSION["contab1"]["data"],0,4))
    {
      $b["contoconf"]="Crediti conferimento nostri associati";
      $b["sottocconf"]="Conferimenti socio";
    }
    else
    {
      $b["contoconf"]="Anticipi quote ".$a;
      $b["sottocconf"]="Anticipo conferimenti ".$a;
    }
    $b["socioconf"]=$s;
    $b["sottoconto"]=$b["sottocconf"]." ".$d["cognome"]." ".$d["nome"];
    $b["valuta"]=$_SESSION["contab1"]["data"];
    $b["avere"]=$i["quota"];
    $b["dare"]=0;
    $b["extra"]="quota ".$a;
    $b["quota_socio"]=$s;
    $b["quota_anno"]=$a;
    $_SESSION["contab1"]["righe"][]=$b;

    if ($_SESSION["contab1"]["descrizione"]=="")
      $_SESSION["contab1"]["descrizione"]="Saldo quota ".$i["cognome"]." ".$i["nome"]." ".$a;
    else
      $_SESSION["contab1"]["descrizione"].=", ".$i["cognome"]." ".$i["nome"]." ".$a;

    return "ok";
  }
  else if (is_numeric($r=http_getparm("rem")))
  {
    if (is_numeric($_SESSION["contab1"]["righe"][$r]["id_rigabanca"]))
      unset($_SESSION["contab1"]["banca"][$_SESSION["contab1"]["righe"][$r]["id_rigabanca"]]);
    if (is_numeric($_SESSION["contab1"]["righe"][$r]["id_rigaquota"]))
      unset($_SESSION["contab1"]["quota"][$_SESSION["contab1"]["righe"][$r]["id_rigaquota"]]);

    unset($_SESSION["contab1"]["righe"][$r]);
    return "ok";
  }
  else if ((http_getparm("form")=="approvasocio") && is_numeric($id=http_getparm("id_socio"))
    && is_numeric($a=http_getparm("anno")) && is_numeric($q=http_getparm("quota")))
  {
    $d=mysql_fetch_assoc(mysql_query("select * from soci_domande where id=".$id));
    $b["approvasocio"]=$id;
    $b["approvaanno"]=$a;
    $b["approvaquota"]=$q;
    if ($a<=substr($_SESSION["contab1"]["data"],0,4))
    {
      $b["contoconf"]="Crediti conferimento nostri associati";
      $b["sottocconf"]="Conferimenti socio";
    }
    else
    {
      $b["contoconf"]="Anticipi quote ".$a;
      $b["sottocconf"]="Anticipo conferimenti ".$a;
    }
    $b["nome"]=$d["cognome"]." ".$d["nome"];
    $b["sottoconto"]=$b["sottocconf"]." ".$b["nome"];
    $b["valuta"]=$_SESSION["contab1"]["data"];
    $b["avere"]=$q;
    $b["dare"]=0;
    $b["extra"]="quota ".$a;
    $b["quota_socio"]=$s;
    $b["quota_anno"]=$a;
    $_SESSION["contab1"]["righe"][]=$b;
    if (isset($_SESSION["contab1"]["descrizione"]) == false || $_SESSION["contab1"]["descrizione"]=="")
      $_SESSION["contab1"]["descrizione"]="Saldo quota ".$d["cognome"]." ".$d["nome"]." ".$a;
    else
      $_SESSION["contab1"]["descrizione"].=", ".$i["cognome"]." ".$a;
    return "ok";
  }
  else if ((http_getparm("form")=="sottoconti") && is_numeric($i=http_getparm("sottoconto")))
  {
    $s=mysql_fetch_array(mysql_query("select * from conti_sottoconti where id=$i"));
    $b["sottoconto"]=$s["sottoconto"];
    $b["id_sottoconto"]=$i;
    $b["valuta"]=$_SESSION["contab1"]["data"];
    $b["avere"]=http_getparm("avere");
    $b["dare"]=http_getparm("dare");
    if (is_numeric($b["avere"]) && is_numeric($b["dare"]))
      $_SESSION["contab1"]["righe"][]=$b;
    return "ok";
  }
  else if (http_getparm("form")=="main")
  {
    if ((($data=http_getparm("data"))!="") && !isset($_SESSION["contab1"]["banca"]))
      $_SESSION["contab1"]["data"]=$data;
    $_SESSION["contab1"]["descrizione"]=http_getparm("descrizione");
    $_SESSION["contab1"]["note"]=http_getparm("note");
    return "ok";
  }
  else if (http_getparm("form")=="registra")
  {
    $m["data"]=$_SESSION["contab1"]["data"];
    $m["descrizione"]=$_SESSION["contab1"]["descrizione"];
    $m["note"]=$_SESSION["contab1"]["note"];
    my_insert("conti_movimenti",$m);
    $m["id"]=mysql_insert_id();

    foreach($_SESSION["contab1"]["righe"] as $r)
    {
      if (is_numeric($r["approvasocio"]))
      {
        $d=sociils_candidatosocioapprovasql($r["approvasocio"],$_SESSION["contab1"]["data"],$r["approvaanno"],$r["approvaquota"]);
        $r["quota_socio"]=$d["id"];
        $sc=contabilita1_sottocontoquota($r["contoconf"],$r["sottocconf"],$d["id"]);
        $nr["id_sottoconto"]=$sc["id"];
      }
      else if (is_numeric($r["socioconf"]))
      {
        $sc=contabilita1_sottocontoquota($r["contoconf"],$r["sottocconf"],$r["socioconf"]);
        $nr["id_sottoconto"]=$sc["id"];
      }
      else
        $nr["id_sottoconto"]=$r["id_sottoconto"];

      $nr["id_movimento"]=$m["id"];
      $nr["valuta"]=$r["valuta"];
      $nr["dare"]=$r["dare"];
      $nr["avere"]=$r["avere"];
      my_insert("conti_righe",$nr);
      $id=mysql_insert_id();
      if (is_numeric($r["id_rigabanca"]))
        mysql_query("update banche_righe set id_riga=$id where id=".$r["id_rigabanca"]);
      if (is_numeric($r["id_rigaquota"]))
        mysql_query("update soci_quote set id_riga=$id where id=".$r["id_rigaquota"]);

      if (is_numeric($r["quota_anno"]))
      {
        $q["id_socio"]=$r["quota_socio"];
        $q["anno"]=$r["quota_anno"];
        $q["data_versamento"]=$r["valuta"];
        $q["id_riga"]=$id;
        my_insert("soci_quote",$q);
        $mail = socio_mail($q["id_socio"]);

        $data = array(
          'id_movimento' => $m["id"],
          'id_socio' => $q["id_socio"],
          'importo' => $r["avere"],
          'email' => $mail,
          'intestazione' => '',
          'indirizzo' => '',
          'causale' => $m["descrizione"],
          'note' => $m["note"],
        );

        contabilita2_ricevute_make_internal($data);

        $text =<<<TEXT
Abbiamo ricevuto e registrato il tuo versamento per la quota di iscrizione ad
Italian Linux Society. Puoi scaricare la ricevuta dal tuo pannello personale su
https://ilsmanager.linux.it/

Grazie per aver rinnovato la tua partecipazione!

Cordiali saluti,
        Il Direttivo ILS

TEXT;

        mail($mail, "Ricevuta Quota ILS", $text, "From: Direttore ILS <direttore@linux.it>");
      }
    }
    unset($_SESSION["contab1"]);
    return "?function=contabilita";
  }
  else
    return "";
}

function contabilita1_show()
{
  html_pagehead("Crea scrittura contabile", array ('Contabilità' => 'contabilita', 'Conti' => 'contabilita&action=conti'));

  ?>

  <h2>Nuovo movimento</h2> <a class="btn" href="?function=contabilita&action=contabilita1&azzera=ok">Azzera</a>

  <?php

  html_openform("",array("function"=>"contabilita","action"=>"contabilita1","form"=>"main"));
  if (!isset($_SESSION["contab1"]["data"]))
    $_SESSION["contab1"]["data"]=date("Y-m-d");
  html_tableformstatic("Data movimento",$_SESSION["contab1"]["data"]);
  if (!isset($_SESSION["contab1"]["banca"]))
    html_tableformtext($_SESSION["contab1"],"Data","data",12);
  html_tableformtext($_SESSION["contab1"],"Descrizione","descrizione",60);
  html_tableformtextarea($_SESSION["contab1"],"Note","note");
  html_tableformsubmit("Imposta");
  html_closeform();
  if (array_key_exists ('righe', $_SESSION["contab1"]) && is_array($_SESSION["contab1"]["righe"]))
  {
    echo "<H2>Righe contabili</H2>\n";
    html_opentable();
    $totdare=$totavere=0;
    html_tableintest(array("Sottoconto","Valuta","Dare","Avere","",""));
    foreach($_SESSION["contab1"]["righe"] as $i=>$c)
    {
      $c=$_SESSION["contab1"]["righe"][$i];
      html_tabledata(array($c["sottoconto"],$c["valuta"],sprintf("%.2f",$c["dare"]),sprintf("%.2f",$c["avere"]),array_key_exists ('extra', $c) ? $c["extra"] : '',
        "<A HREF=\"?function=contabilita&action=contabilita1&rem=".$i."\">[elimina]</A>"));
      $totdare+=$c["dare"];
      $totavere+=$c["avere"];
    }
    html_tabledata(array("TOTALE","",sprintf("%.2f",$totdare),sprintf("%.2f",$totavere),"",""));
    html_closetable();
    if ((($totdare-$totavere)<0.01) && (($totdare-$totavere)>-0.01))
    {
      if (array_key_exists ('descrizione', $_SESSION["contab1"]) && $_SESSION["contab1"]["descrizione"]!="") {
        html_openform("",array("function"=>"contabilita","action"=>"contabilita1","form"=>"registra"));
        html_tableformsubmit("Registra");
        html_closeform();
      }
      else {
        ?>

        <div class="alert">Nota bene: devi specificare una "descrizione" per il movimento.</div>

        <?php
      }
    }
  }

  $data=$_SESSION["contab1"]["data"];

  if (($r=mysql_query("select *,q.id as id_quota,concat(cognome,\" \",nome) as socio,q.note from soci_quote as q ".
      "left join soci_iscritti as i on i.id=q.id_socio where isnull(id_riga) and ".
      "data_versamento<=adddate(\"$data\",16) and data_versamento>=subdate(\"$data\",16) order by data_versamento,cognome,nome"))
    && (mysql_num_rows($r)>0))
  {
    echo "<H2>Quote incomplete</H2>\n";
    html_opentable();
    html_tableintest(array("Data","Anno","Socio","Note"));
    while ($d=mysql_fetch_assoc($r))
    {
      if ($_SESSION["contab1"]["quote"][$d["id_quota"]]!="sel")
        $l="<a href=\"?function=contabilita&action=contabilita1&id_quota=".$d["id_quota"]."\">".$d["anno"]."</a>";
      else
        $l=$d["anno"];

      html_tabledata(array($d["data_versamento"],$l,
        "<a href=\"?function=sociils&action=iscritti&show=".$d["id_socio"]."\">".$d["cognome"]." ".$d["nome"]."</a>",
        $d["note"]));
    }
    html_closetable();
  }

  html_openform("");
  html_tableformtext(null,"Ricerca Socio","socio");
  html_closeform();
  html_opentable (false, false, 'listmembers');
  html_tableintest (array("Socio", ""));
  html_closetable ();

  $nd = $na = "0.00";
  if ($totdare > $totavere)
    $na=sprintf("%.2f",$totdare-$totavere);
  else
    $nd=sprintf("%.2f",$totavere-$totdare);

  html_openform("", array ('nd' => $nd, 'na' => $na));
  html_tableformtext(null,"Ricerca Conto","conto");
  html_closeform();
  html_opentable (false, false, 'listaccounts');
  html_tableintest (array("Conto / Sottoconto", ""));
  html_closetable ();

  html_pagetail();
}


function contabilita1()
{
  if (($l=contabilita1_handleparam())=="")
    contabilita1_show();
  else if ($l=="ok")
    header("Location: ?function=contabilita&action=contabilita1");
  else
    header("Location: $l");
}

?>
