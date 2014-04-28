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

require("/usr/share/fpdf/fpdf.php");


function contabilita2_ricevute_crea($data)
{
  unset($_SESSION["ricevuta"]);
  html_pagehead("Ricevute incassi", array ('Contabilità' => 'contabilita', 'Ricevute' => 'contabilita&action=ricevute'));

  echo "<H2>Nuova ricevuta</H2>\n";
  $q="select * from ricevute as r left join soci_iscritti as s on s.id=r.id_socio ".
    "left join conti_movimenti as m on m.id=r.id_movimento where data=\"$data\"";
  if ($r=mysql_query($q))
    while ($d=mysql_fetch_array($r))
    {
      if (!is_array($ric[$d["id_movimento"]]))
        $ric[$d["id_movimento"]]=array("em"=>0,"inc"=>0);
      $ric[$d["id_movimento"]]["em"]+=$d["importo"];
    }
  $q="select data,sum(avere) as importo,id_movimento,m.descrizione as movimento from conti_righe as r ".
    "left join conti_movimenti as m on m.id=r.id_movimento ".
    "left join conti_sottoconti as s on s.id=r.id_sottoconto ".
    "left join conti_conti as c on c.id=s.id_conto ".
    "left join ricevute_conti as rc on rc.id_conto=c.id ".
    "where data=\"$data\" and avere>0 and rc.id>0 group by id_movimento order by data";
  if ($r=mysql_query($q))
  {
    while ($d=mysql_fetch_array($r))
    {
      if (!is_array($ric[$d["id_movimento"]]))
        $ric[$d["id_movimento"]]=array("em"=>0,"inc"=>0);
      $ric[$d["id_movimento"]]["inc"]+=$d["importo"];
      $i[]=$d;
    }
    echo "<H3>Movimenti senza ricevuta</H3>\n";
    html_opentable();
    html_tableintest(array("Data","Importo","Movimento"));
    foreach($i as $d)
      if (($dif=($ric[$d["id_movimento"]]["inc"]-$ric[$d["id_movimento"]]["em"]))>0)
        html_tabledata(array("<A HREF=\"?function=contabilita&action=ricevute&creamov=".$d["id_movimento"]."\">".$d["data"]."</A>",
          sprintf("%.2f",$dif),$d["movimento"]));
    html_closetable();
  }
  html_pagetail();
}


function contabilita2_ricevute_creamov($id)
{
  html_pagehead("Ricevute incassi", array ('Contabilità' => 'contabilita', 'Ricevute' => 'contabilita&action=ricevute'));

  $d=mysql_fetch_assoc(mysql_query("select * from conti_movimenti where id=$id"));
  $f["id_movimento"]=$id;
  $f["data"]=$d["data"];
  $f["causale"]=$d["descrizione"];
  echo "<H2>Nuova ricevuta - ".$d["descrizione"]."</H2>\n";
  if (is_numeric($s=http_getparm("id_socio")))
  {
    $d=mysql_fetch_assoc(mysql_query("select * from soci_iscritti where id=".$s));
    $f["id_socio"]=$d["id"];
    $f["intestazione"]=$d["cognome"]." ".$d["nome"];
    $f["indirizzo"]=$d["indirizzo_resid"]."\n".$d["cap_resid"]." ".$d["comune_resid"]." ".$d["prov_resid"];
    if ($d["nickname"]=="")
      $f["email"]=$d["email"];
    else
      $f["email"]=$d["nickname"]."@linux.it";
  }
  $q="select sum(importo) as totale from ricevute as r ".
    "left join conti_movimenti as m on m.id=r.id_movimento where id_movimento=$id";
  $d=mysql_fetch_assoc(mysql_query($q));
  $totalericevute=$d["totale"];
  $q="select valuta,dare,avere,sottoconto,id_socio,rc.id as contook from conti_righe as r ".
    "left join conti_movimenti as m on m.id=r.id_movimento ".
    "left join conti_sottoconti as s on s.id=r.id_sottoconto ".
    "left join conti_conti as c on c.id=s.id_conto ".
    "left join ricevute_conti as rc on rc.id_conto=c.id ".
    "left join soci_iscritti as i on i.id=s.id_socio ".
    "where id_movimento=$id";
  if ($r=mysql_query($q))
  {
    $a=0;
    html_opentable();
    html_tableintest(array("valuta","dare","avere","sottoconto"));
    while ($d=mysql_fetch_array($r))
    {
      if ($d["contook"]>0)
        $a+=$d["avere"];
      if (is_numeric($d["id_socio"]))
        $sc="<A HREF=\"?function=contabilita&action=ricevute&creamov=".$id."&id_socio=".$d["id_socio"]."\">".$d["sottoconto"]."</A>";
      else
        $sc=$d["sottoconto"];
      html_tabledata(array($d["valuta"],$d["dare"],$d["avere"],$sc));
    }
    html_closetable();
  }
  $f["maximp"]=sprintf("%.2f",$a-$totalericevute);
  $f["importo"]=$f["maximp"];
  $_SESSION["ricevuta"]=$f;
  echo "<P>Importo da registrare: <STRONG>".$_SESSION["ricevuta"]["maximp"]."</STRONG>\n";
  html_openform(".",array("function"=>"contabilita","action"=>"ricevute","step"=>"form"));
  html_tableformstatic("Data",$f["data"]);
  html_tableformtext($f,"Importo","importo",12);
  html_tableformtext($f,"Email","email",80);
  html_tableformtext($f,"Intestazione","intestazione",80);
  html_tableformtextarea($f,"Indirizzo","indirizzo",60,4);
  html_tableformtextarea($f,"Causale","causale",60,4);
  html_tableformtextarea($f,"Note","note",60,4);
  html_tableformsubmit("Procedi");
  html_closeform();
  html_pagetail();
}


function contabilita2_ricevute_check()
{
  if (!is_array($f=$_SESSION["ricevuta"]))
  {
    header("Location: ?function=contabilita&action=ricevute");
    exit(0);
  }

  html_pagehead("Ricevute incassi", array ('Contabilità' => 'contabilita', 'Ricevute' => 'contabilita&action=ricevute'));

  echo "<H2>Conferma creazione ricevuta</H2>\n";
  $f["importo"]=http_getparm("importo");
  $f["email"]=http_getparm("email");
  $f["intestazione"]=http_getparm("intestazione");
  $f["indirizzo"]=http_getparm("indirizzo");
  $f["causale"]=http_getparm("causale");
  $f["note"]=http_getparm("note");
  /*
  if ($f["importo"]>$f["maximp"])
    $f["importo"]=$f["maximp"];
  */
  unset($_SESSION["ricevuta"]);
  $_SESSION["ricevuta"]=$f;
  html_openform(".",array("function"=>"contabilita","action"=>"ricevute","step"=>"ok"));
  html_tableformstatic("Data",$f["data"]);
  html_tableformstatic("Importo",$f["importo"]);
  html_tableformstatic("Email",$f["email"]);
  html_tableformstatic("Intestazione",$f["intestazione"]);
  html_tableformstatic("Indirizzo",nl2br($f["indirizzo"]));
  html_tableformstatic("Causale",nl2br($f["causale"]));
  html_tableformsubmit("Registra");
  html_closeform();
  html_pagetail();
}


function contabilita2_ricevute_make()
{
  if (!is_array($f=$_SESSION["ricevuta"]))
  {
    header("Location: ?function=contabilita&action=ricevute");
    exit(0);
  }
  $d["id_socio"]=$f["id_socio"];
  $d["id_movimento"]=$f["id_movimento"];
  $d["importo"]=$f["importo"];
  $d["email"]=$f["email"];
  $d["intestazione"]=$f["intestazione"];
  $d["indirizzo"]=$f["indirizzo"];
  $d["causale"]=$f["causale"];
  $d["note"]=$f["note"];
  $g=mysql_fetch_assoc(mysql_query("select * from conti_movimenti where id=".$d["id_movimento"]));
  $n=mysql_fetch_assoc(mysql_query("select max(numero) as ultima from ricevute as r left join conti_movimenti as m on m.id=r.id_movimento where year(\"".$g["data"]."\")=year(data)"));
  $d["numero"]=$n["ultima"]+1;
  $id=my_insert("ricevute",$d);
  $d["data"]=$g["data"];
  contabilita2_ricevute_pdf($d);
  header("Location: ?function=contabilita&action=ricevute");
}

class ricevutaPDF extends FPDF {
function ricevuta($data,$num,$importo,$intestazione,$indirizzo,$causale)
{
  $this->Image("logo2.png",10,10,27);
  $this->SetFont("Arial","B",18);
  $this->SetXY(45,10);
  $this->Cell(80,9,"ILS - Italian Linux Society",0,1,"",0);
  $this->SetFont("Arial","B",15);
  $this->SetXY(45,17);
  $this->Cell(80,8,"http://www.linux.it",0,1,"",0);
  $this->SetFont("Arial","",10);
  $this->SetXY(45,25);
  $this->MultiCell(80,4,"via Aldo Moro, 223\n92026 Favara AG\ncod. fiscale 92043980090\np. iva 02438840841",0,1,"",0);
  $this->SetFont("Arial","B",24);
  $this->SetXY(150,10);
  $this->Cell(50,10,"RICEVUTA",0,1,"R",0);
  $this->SetFont("Arial","",16);
  $this->SetXY(150,25);
  $this->Cell(20,10,"data",0,1,"",0);
  $this->SetFont("Arial","B",18);
  $this->SetXY(160,25);
  $this->Cell(40,10,$data,0,1,"R",0);
  $this->SetFont("Arial","",16);
  $this->SetXY(150,32);
  $this->Cell(20,10,"numero",0,1,"",0);
  $this->SetFont("Arial","B",18);
  $this->SetXY(170,32);
  $this->Cell(30,10,$num,0,1,"R",0);
  $this->SetFont("Arial","",12);
  $this->SetXY(10,55);
  $this->Cell(0,10,"E' stata ricevuta la somma di euro",0,1,"",0);
  $this->SetFont("Arial","B",16);
  $this->SetXY(75,55);
  $this->Cell(25,10,$importo,0,1,"R",0);
  $this->SetFont("Arial","",12);
  $this->SetXY(100,55);
  $this->Cell(0,10,"da",0,1,"",0);
  $this->SetFont("Arial","B",16);
  $this->SetXY(106,55);
  $this->Cell(0,10,$intestazione,0,1,"",0);
  $this->SetFont("Arial","",13);
  $this->SetXY(106,63);
  $this->MultiCell(0,5,"$indirizzo",0,"",0);
  $this->SetFont("Arial","",13);
  $this->SetXY(10,95);
  $this->Cell(0,9,"Causale:",0,1,"",0);
  $this->SetFont("Arial","B",14);
  $this->SetXY(35,96);
  $this->MultiCell(0,7,"$causale",0,"",0);
  $this->SetFont("Arial","BI",11);
#  $this->SetXY(130,105);
#  $this->Cell(40,10,"Dalla Silvestra Michele",0,1,"C",0);
  $this->SetXY(130,120);
  $this->Cell(40,10,"Direzione ILS",0,1,"C",0);
  $this->SetFont("Arial","B",11);
  $this->SetXY(10,130);
  $this->Cell(0,10,"Documento non fiscale, a quietanza del pagamento.",0,1,"",0);
#  $this->Line(10,137,200,137);
}}
function contabilita2_ricevute_pdf($d)
{
  global $UNIXDIR;
  $pdf=new ricevutaPDF();
  $pdf->AliasNbPages();
  $pdf->AddPage();
  $pdf->ricevuta(date("d/m/Y",strtotime($d["data"])),$d["numero"],
    $d["importo"],$d["intestazione"],$d["indirizzo"],$d["causale"]);
  $DIR=$UNIXDIR["archivio"]."ricevute/".substr($d["data"],0,4);
  $FILE=sprintf("ricevuta-%s-%05d.pdf",str_replace("-","",$d["data"]),$d["numero"]);
  system("mkdir ".$DIR);
  $pdf->Output($DIR."/".$FILE,"F");
}

function contabilita2_ricevutepdf($id)
{
  global $UNIXDIR;
  $d=mysql_fetch_assoc(mysql_query("select *,r.id as idr from ricevute as r left join conti_movimenti as m on m.id=r.id_movimento where r.id=".$id));
  if (($d["idr"]==$id) && (userperm("banche") || (is_numeric($_SESSION["user"]["idsocio"]) && ($d["id_socio"]==$_SESSION["user"]["idsocio"]))))
  {
    $DIR=$UNIXDIR["archivio"]."ricevute/".substr($d["data"],0,4);
    $FILE=sprintf("ricevuta-%s-%05d.pdf",str_replace("-","",$d["data"]),$d["numero"]);
    header("Content-Type: application/gzip");
    header("Content-Disposition: attachment; filename=\"".$FILE."\"");
    readfile($DIR."/".$FILE);
  }
  else
  {
    header("Location: .");
    exit(0);
  }
}


function contabilita2_ricevute_show($id)
{
  html_pagehead("Ricevute incassi", array ('Contabilità' => 'contabilita', 'Ricevute' => 'contabilita&action=ricevute'));

  $q="select *,r.id as idr from ricevute as r left join soci_iscritti as s on s.id=r.id_socio ".
    "left join conti_movimenti as m on m.id=r.id_movimento where r.id=$id";
  if ($d=mysql_fetch_assoc(mysql_query($q)))
  {
    echo "<H2>Ricevuta ".$d["numero"]." del ".$d["data"]."</H2>\n";
    echo "<A HREF=\"?function=ricevutepdf&id=".$d["idr"]."\">[PDF]</A>\n";
    html_opentable();
    html_tablefieldrow("Intestazione",$d["intestazione"]);
    html_tablefieldrownl("Indirizzo",$d["indirizzo"]);
    html_tablefieldrownl("Causale",$d["causale"]);
    html_closetable();
  }
  html_pagetail();
}


function contabilita2_ricevute_anno($a)
{
  html_pagehead("Ricevute incassi", array ('Contabilità' => 'contabilita', 'Ricevute' => 'contabilita&action=ricevute'));

  $q="select *,r.id as idr from ricevute as r left join soci_iscritti as s on s.id=r.id_socio ".
    "left join conti_movimenti as m on m.id=r.id_movimento where year(data)=$a order by data";
  if ($r=mysql_query($q))
  {
    echo "<H2>Ricevute anno $a</H2>\n";
    $t=0;
    html_opentable();
    html_tableintest(array("Data","Numero","Intestazione","Importo"));
    while ($d=mysql_fetch_array($r))
    {
      if ($d["id_socio"]=="")
        $int=$d["intestazione"];
      else
        $int="<A HREF=\"?function=sociils&action=iscritti&show=".$d["id_socio"]."\">".$d["intestazione"]."</A>";
      html_tabledata(array($d["data"],"<A HREF=\"?function=contabilita&action=ricevute&show=".$d["idr"]."\">".$d["numero"]."</A>",
        $int,$d["importo"]));
      $t+=$d["importo"];
    }
    html_closetable();
    echo "<P>Totale incasso: <STRONG>".sprintf("%.2f",$t)."</STRONG>\n";
  }
  html_pagetail();
}


function contabilita2_ricevute_menu()
{
  html_pagehead("Ricevute incassi", array ('Contabilità' => 'contabilita'));

  $q="select year(data) as anno, count(*) as qt,sum(importo) as incasso from ricevute as r ".
    "left join conti_movimenti as m on m.id=r.id_movimento group by anno";
  if ($r=mysql_query($q))
  {
    html_opentable();
    html_tableintest(array("Anno","Quantit&agrave;","Importo totale"));
    while ($d=mysql_fetch_array($r))
      html_tabledata(array("<A HREF=\"?function=contabilita&action=ricevute&anno=".$d["anno"]."\">".$d["anno"]."</A>",
        $d["qt"],$d["incasso"]));
    html_closetable();
  }
  if (userperm("admin"))
  {
    echo "<H2>Ricevute da fare</H2>\n";
    $l=mysql_fetch_assoc(mysql_query("select max(data) as ultima from ricevute as r ".
      "left join conti_movimenti as m on m.id=r.id_movimento"));
    $q="select sum(importo) as totale,id_movimento from ricevute as r ".
      "left join conti_movimenti as m on m.id=r.id_movimento where data=\"".$l["ultima"]."\" group by id_movimento";
    if ($r=mysql_query($q))
      while ($d=mysql_fetch_assoc($r))
        $emesse[$d["id_movimento"]]=$d["totale"];
    $q="select data,sum(avere) as importo,id_movimento,m.descrizione as movimento from conti_righe as r ".
      "left join conti_movimenti as m on m.id=r.id_movimento ".
      "left join conti_sottoconti as s on s.id=r.id_sottoconto ".
      "left join conti_conti as c on c.id=s.id_conto ".
      "left join ricevute_conti as rc on rc.id_conto=c.id ".
      "where data>=\"".$l["ultima"]."\" and avere>0 and rc.id>0 group by id_movimento order by data";
    if ($r=mysql_query($q))
    {
      html_opentable();
      html_tableintest(array("Data","Importo","Movimento"));
      while ($d=mysql_fetch_array($r))
      {
        if (array_key_exists ($d["id_movimento"], $emesse) && is_numeric($emesse[$d["id_movimento"]]))
          $d["importo"]-=$emesse[$d["id_movimento"]];
        if ($d["importo"]>0)
          html_tabledata(array("<A HREF=\"?function=contabilita&action=ricevute&crea=".$d["data"]."\">".$d["data"]."</A>",
            sprintf("%.2f",$d["importo"]),$d["movimento"]));
      }
      html_closetable();
    }
  }
  html_pagetail();
}

function contabilita2_ricevute()
{
  if (http_getparm("step")=="form" && userperm("admin"))
    contabilita2_ricevute_check();
  else
  if (http_getparm("step")=="ok" && userperm("admin"))
    contabilita2_ricevute_make();
  else
  if (is_numeric($i=http_getparm("creamov")) && userperm("admin"))
    contabilita2_ricevute_creamov($i);
  else
  if (($d=http_getparm("crea"))!="" && userperm("admin"))
    contabilita2_ricevute_crea($d);
  else
  if (is_numeric($i=http_getparm("show")))
    contabilita2_ricevute_show($i);
  else
  if (is_numeric($a=http_getparm("anno")))
    contabilita2_ricevute_anno($a);
  else
    contabilita2_ricevute_menu();
}



?>
