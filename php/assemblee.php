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

function assemblee_arr()
{
  return array('tipo' => array("ordinaria","straordinaria"),
               'stato' => array("preparazione","convocata","aperta","conclusa"));
}

function assemblee_askactionsocio($id)
{
  $d=mysql_fetch_assoc(mysql_query("select * from assemblee_soci where id_assemblea=$id && id_socio=".$_SESSION["user"]["idsocio"]));
  if (is_numeric($d["id_socio"]) && $d["id_socio"]==$d["id_delega"])
    return "<a href=\"?function=assemblee&assente=$id\" class=\"btn btn-large\">Ritira Partecipazione</a>\n";
  if (is_numeric($d["id_socio"]))
    return "<a href=\"?function=assemblee&assente=$id\" class=\"btn btn-large\">Ritira Delega</a>\n";
  return "<a href=\"?function=assemblee&partecipa=$id\" class=\"btn btn-primary btn-large\">Segnala Partecipazione</a>\n";
}


function assemblee_partecipa($id)
{
  $d=mysql_fetch_assoc(mysql_query("select * from assemblee_soci where id_assemblea=$id && id_socio=".$_SESSION["user"]["idsocio"]));
  if (!is_numeric($d["id_socio"]))
    my_insert("assemblee_soci",array("id_assemblea"=>$id,"id_socio"=>$_SESSION["user"]["idsocio"],"id_delega"=>$_SESSION["user"]["idsocio"]));
  header("Location: ?function=assemblee&id=$id");
}


function assemblee_assente($id)
{
  mysql_query("delete from assemblee_soci where id_assemblea=$id && id_socio=".$_SESSION["user"]["idsocio"]);
  mysql_query("delete from assemblee_soci where id_assemblea=$id && id_delega=".$_SESSION["user"]["idsocio"]);
  header("Location: ?function=assemblee&id=$id");
}


function assemblee_delega($id,$socio)
{
  $a=mysql_fetch_assoc(mysql_query("select * from assemblee_elenco where id=$id"));
  $s=mysql_fetch_assoc(mysql_query("select * from assemblee_soci where id_assemblea=$id && id_socio=".$_SESSION["user"]["idsocio"]));
  $d=mysql_fetch_assoc(mysql_query("select count(*) as qt from assemblee_soci where id_assemblea=$id && id_delega=".$socio));
  if (!is_numeric($s["id_socio"]) && $d["qt"]>0 && $d["qt"]<4 && $a["stato"]=="convocata")
  {
    $d=mysql_fetch_assoc(mysql_query("select * from soci_iscritti where id=".$socio));
    my_insert("assemblee_soci",array("id_assemblea"=>$id,"id_socio"=>$_SESSION["user"]["idsocio"],"id_delega"=>$socio));
  }
  header("Location: ?function=assemblee&id=$id");
}

function assemblee_presenza($a,$s)
{
  mysql_query("create temporary table tmp_ultimaquota select max(anno) as regola,id_socio from soci_quote group by id_socio");
  $d=mysql_fetch_assoc(mysql_query(
    "select * from assemblee_soci as a ".
    "left join soci_iscritti as i on i.id=a.id_socio ".
    "left join assemblee_elenco as e on e.id=a.id_assemblea ".
    "left join tmp_ultimaquota as q on q.id_socio=i.id ".
    "where id_assemblea=$a and a.id_socio=$s and e.stato=\"convocata\""));
  if (is_numeric($d["id"]))
  {
    if ($d["presenza"]=="")
    {
      mysql_query("update assemblee_soci set presenza=\"si\" where id_assemblea=$a and (id_socio=$s or id_delega=$s)");
      if (($d["regola"]==date("Y")) &&
        $r=mysql_query("select * from assemblee_soci as a left join soci_iscritti as i on i.id=a.id_socio left join tmp_ultimaquota as q on q.id_socio=i.id ".
          "where (a.id_socio=$s or id_delega=$s) and (voto is null or voto=\"\") and data_ammissione!=00000000 and regola=".date("Y")))
        while ($d=mysql_fetch_array($r))
        {
          mysql_query("update assemblee_soci set voto=\"si\" where id_assemblea=$a and id_socio=".$d["id_socio"]);
        }
    }
    else
    {
      mysql_query("update assemblee_soci set presenza=\"\",voto=\"\" where id_assemblea=$a and (id_socio=$s or id_delega=$s)");
      my_update("assemblee_soci",array("presenza"=>"","voto"=>""),"id",$d["id"]);
    }
  }
  header("Location: ?function=assemblee&id=$a");
}

function assemblee_voto($a,$s)
{
  $d=mysql_fetch_assoc(mysql_query("select * from assemblee_soci as a left join soci_iscritti as i on i.id=a.id_socio where id_assemblea=$a and id_socio=$s"));
  if (is_numeric($d["id"]))
  {
    if ($d["voto"]=="")
    {
      mysql_query("update assemblee_soci set voto=\"si\" where id_assemblea=$a and id_socio=$s");
    }
    else
    {
      mysql_query("update assemblee_soci set voto=\"\" where id_assemblea=$a and id_socio=$s");
    }
  }
  header("Location: ?function=assemblee&id=$a");
}

function assemblee_changestatus($old, $new, $id)
{
  $d=mysql_fetch_assoc(mysql_query("select * from assemblee_elenco where id=$id"));
  if ($d["stato"]==$old)
  {
    $d["stato"]=$new;
    my_update("assemblee_elenco",array("stato"=>$new),"id",$id);
  }
  header("Location: ?function=assemblee");
}

function assemblee_test_availability()
{
  $d = mysql_query("select * from assemblee_elenco where stato='convocata' or stato='aperta'");
  if (mysql_num_rows($d) > 0)
    echo " &lt; accedi qui per registrarti e partecipare all'assemblea";
}

function assemblee_confermaconvoca($id)
{
  assemblee_changestatus('preparazione', 'convocata', $id);
}

function assemblee_apri($id)
{
  assemblee_changestatus('convocata', 'aperta', $id);
}

function assemblee_askconvoca($id)
{
  $d=mysql_fetch_assoc(mysql_query("select * from assemblee_elenco where id=$id"));
  if ($d["stato"]=="preparazione")
  {
    html_pagehead("Assemblee Soci", array ('Assemblee' => 'assemblee'));

    echo "<H2>Conferma invio convocazione</H2>\n";
    html_openform(".",array("function"=>"assemblee","convoca"=>$id,"conferma"=>"ok"));
    html_tableformstatic("Data",$d["data"]);
    html_tableformstatic("Tipo",$d["tipo"]);
    html_tableformstatic("Convocazione",nl2br($d["convocazione"]));
    html_tableformsubmit("Conferma");
    html_closeform();
  }
  else
    header("Location: ?function=assemblee");
}


function assemblee_edit($id)
{
  $a=assemblee_arr();
  html_pagehead("Assemblee Soci", array ('Assemblee' => 'assemblee'));

  echo "<H2>Modifica assemblea</H2>\n";
  $d=mysql_fetch_assoc(mysql_query("select * from assemblee_elenco where id=$id"));

  foreach($a["tipo"] as $k=>$v)
    $a2[]=array($k,$v);

  html_openform(".", array("function"=>"assemblee","edit"=>$id,"conferma"=>"ok"));
  html_tableformtext($d,"Data","data",12, true);
  html_tableformselect($d,"Tipo","tipo",$a2);
  html_tableformstatic("Stato",$a["stato"][array_search($d["stato"],$a["stato"])]);
  html_tableformtextarea($d,"Convocazione","convocazione",60,20);
  html_tableformsubmit("Aggiorna");
  html_closeform();
  html_pagetail();
}

function assemblee_new()
{
  $a=assemblee_arr();
  html_pagehead("Assemblee Soci", array ('Assemblee' => 'assemblee'));

  echo "<H2>Nuova assemblea</H2>\n";
  $d["data"]=date("Y-m-d");
  $d["stato"]=0;
  foreach($a["tipo"] as $k=>$v)
    $a2[]=array($k,$v);
  html_openform(".",
    array("function"=>"assemblee","action"=>"new","conferma"=>"ok","stato"=>1));
  html_tableformtext($d,"Data","data",12);
  html_tableformselect($d,"Tipo","tipo",$a2);
  html_tableformstatic("Stato",$a["stato"][0]);
  html_tableformtextarea($d,"Convocazione","convocazione",60,20);
  html_tableformsubmit("Crea");
  html_closeform();
  html_pagetail();
}

function assemblee_confermaedit($id)
{
  $a=assemblee_arr();
  $d["data"]=http_getparm("data");
  $d["tipo"]=$a["tipo"][http_getparm("tipo")];
  $d["convocazione"]=http_getparm("convocazione");
  my_update("assemblee_elenco",$d,"id",$id);
  header("Location: ?function=assemblee&id=".$id);
}

function assemblee_confermanew()
{
  $a=assemblee_arr();
  $d["data"]=http_getparm("data");
  $d["tipo"]=$a["tipo"][http_getparm("tipo")];
  $d["stato"]="preparazione";
  $d["convocazione"]=http_getparm("convocazione");
  $a=my_insert("assemblee_elenco",$d);
  header("Location: ?function=assemblee");
}

function assemblee_mostra($id)
{
  html_pagehead("Assemblee Soci", array ('Assemblee' => 'assemblee'));

  if ($d=mysql_fetch_assoc(mysql_query("select * from assemblee_elenco where id=$id")))
  {
    $stato=$d["stato"];

    ?>

    <h2>Assemblea del <?php echo $d["data"] ?></h2>

    <div class="row">
      <div class="span5">

      <p>
        <?php if ($d["stato"]=="convocata"): ?>
          <?php echo assemblee_askactionsocio($id); ?>
        <?php else: ?>
          <a class="btn btn-primary btn-large" href="?function=assemblee&votazioni=<?php echo $id ?>">Votazioni</a>

          <?php
            if ($d["stato"]=="aperta")
              echo assemblee_askactionsocio($id);
          ?>
        <?php endif; ?>
      </p>

      <?php

      if (userperm("admin")) {
        switch($d["stato"]) {
          case 'preparazione':
            echo "<A HREF=\"?function=assemblee&edit=".$d["id"]."\">[modifica]</A>\n";
            echo "<A HREF=\"?function=assemblee&convoca=".$d["id"]."\">[invia convocazione]</A>\n";
            break;
          case 'convocata':
            echo "<A HREF=\"?function=assemblee&apri=".$d["id"]."\">[apri assemblea]</A>\n";
            break;
          case 'aperta':
            echo "<A HREF=\"?function=assemblee&chiudi=".$d["id"]."\">[chiudi assemblea]</A>\n";
            break;
        }
      }

      $n = mysql_fetch_array(mysql_query("select count(*) as prenotati, sum(if(presenza=\"si\",1,0)) as presenti, sum(if(voto=\"si\",1,0)) as votanti from assemblee_soci where id_assemblea=$id"));

      html_infobox (array (
        'Tipo' => $d ["tipo"],
        'Stato' => $d ["stato"],
        'Soci prenotati' => $n ["prenotati"],
        'Soci presenti' => $n ["presenti"],
        'Soci votanti' => $n ["votanti"],
        'Convocazione' => nl2br ("\n" . $d ["convocazione"])
      ));

      ?>

      </div>

      <div class="span7">
        <?php

        if ($r=mysql_query("select *,count(*) as qt from assemblee_soci as a left join soci_iscritti as s on s.id=a.id_delega where a.id_assemblea=".$id." group by id_delega")) {
          while ($d=mysql_fetch_assoc($r)) {
            $delega[$d["id_delega"]]=$d["qt"];
            $socio[$d["id_delega"]]=$d["cognome"]." ".$d["nome"]." (".$d["nickname"].")";
          }
        }

        $a=mysql_fetch_assoc(mysql_query("select * from assemblee_soci where id_assemblea=$id && id_socio=".$_SESSION["user"]["idsocio"]));
        mysql_query("create temporary table tmp_ultimaquota select max(anno) as regola,id_socio from soci_quote group by id_socio");

        if ($r=mysql_query("select * from assemblee_soci as a left join soci_iscritti as s on s.id=a.id_socio left join tmp_ultimaquota as u on u.id_socio=s.id where a.id_assemblea=".$id." order by cognome,nome,nickname")) {
          html_opentable();

          if (userperm("admin")) {
            html_tableintest(array("Socio","Delega","Presente","Voto"));
            while ($d=mysql_fetch_assoc($r)) {
              $s="<A HREF=\"?function=sociils&action=iscritti&show=".$d["id_socio"]."\" TARGET=\"_blank\">".$d["cognome"]." ".$d["nome"]." (".$d["nickname"].")</A>";

              if ($d["id_delega"]!=$d["id_socio"])
                $dd=$socio[$d["id_delega"]];
              else if (!is_numeric($a["id_socio"]) && !is_numeric($delega[$_SESSION["user"]["idsocio"]]) && $delega[$d["id_socio"]]<4)
                $dd="<A HREF=\"?function=assemblee&delega=$id&socio=".$d["id_socio"]."\">[delega]</A>";
              else
                $dd="";

              if ($d["id_socio"]==$d["id_delega"])
                $p=(($d["presenza"]=="")?("No"):("Si"))." <A HREF=\"?function=assemblee&assemb=$id&presente=".$d["id_socio"]."\">[cambia]</A>";
              else
                $p=(($d["presenza"]=="")?("No"):("Si"));

              $v=(($d["voto"]=="")?("No"):("Si"))." <A HREF=\"?function=assemblee&assemb=$id&voto=".$d["id_socio"]."\">[cambia]</A>";

              html_tabledata(array($s,$dd,$p,$v));
            }
          }
          else {
            html_tableintest(array("Socio","Delega"));

            while ($d=mysql_fetch_assoc($r)) {
              $s="<A HREF=\"?function=sociils&action=iscritti&show=".$d["id_socio"]."\" TARGET=\"_blank\">".$d["cognome"]." ".$d["nome"]." (".$d["nickname"].")</A>";

              if ($d["id_delega"]!=$d["id_socio"])
                $dd=$socio[$d["id_delega"]];
              else if ($stato=="convocata" && !is_numeric($a["id_socio"]) && array_key_exists ($_SESSION["user"]["idsocio"], $delega) == false && $delega[$d["id_socio"]]<4)
                $dd="<A HREF=\"?function=assemblee&delega=$id&socio=".$d["id_socio"]."\">[delega]</A>";
              else
                $dd="";

              html_tabledata(array($s,$dd));
            }
          }

          html_closetable();
        }

        ?>

      </div>
    </div>

    <?php
  }

  html_pagetail();
}

function assemblee_votazioni_editform($v,$desc,$maxi)
{
  $d["descrizione"]=$desc;
  $d["maxitem"]=$maxi;
  $d["testo"]=http_getparm("testo");
  my_update("votazioni",$d,"id",$v);
  header("Location: ?function=assemblee&votazione=$v");
}

function assemblee_votazioni_edit($id)
{
  $d=mysql_fetch_assoc(mysql_query("select * from votazioni as v left join assemblee_elenco as a on a.id=v.id_assemblea where v.id=$id"));
  html_pagehead("Assemblee Soci", array ('Assemblee' => 'assemblee', 'Assemblea' => "assemblee&id=$id"));

  echo "<H2>Assemblea del ".$d["data"]."</H2>\n";
  html_opentable();
  html_tablefieldrow("Tipo",$d["tipo"]);
  html_tablefieldrow("Stato",$d["stato"]);
  html_closetable();
  echo "<H2>Modifica votazione</H2>\n";
  html_openform("",array("editvotazioni"=>$id));
  html_tableformtext($d,"Descrizione","descrizione",30);
  html_tableformtext($d,"Max scelte","maxitem",30);
  html_tableformtextarea($d,"Testo","testo",50,6);
  html_tableformsubmit("Modifica");
  html_closeform();
  html_pagetail();
}

function assemblee_votazioni_addform($assem,$desc,$maxi)
{
  $d["id_assemblea"]=$assem;
  $d["descrizione"]=$desc;
  $d["maxitem"]=$maxi;
  $d["testo"]=http_getparm("testo");
  $i=my_insert("votazioni",$d);
  header("Location: ?function=assemblee&votazioni=$assem&votazione=$i");
}

function assemblee_votazioni_add($id)
{
  html_pagehead("Assemblee Soci", array ('Assemblee' => 'assemblee', 'Assemblea' => "assemblee&id=$id"));

  if ($d=mysql_fetch_assoc(mysql_query("select * from assemblee_elenco where id=$id")))
  {
    ?>

    <h2>Assemblea del <?php echo $d["data"] ?></h2>

    <?php html_infobox (array ('Tipo' => $d ['tipo'], 'Stato' => $d ['stato'])); ?>

    <h2>Nuova votazione</h2>

    <?php

    html_openform("",array("addvotazioni"=>$id));
    html_tableformtext(array(),"Descrizione","descrizione",30);
    html_tableformtext(array("maxitem"=>1),"Max scelte","maxitem",30);
    html_tableformtextarea(array(),"Testo","testo",50,6);
    html_tableformsubmit("Crea");
    html_closeform();
  }
  html_pagetail();
}

function assemblee_votazioni($id)
{
  html_pagehead("Assemblee Soci", array ('Assemblee' => 'assemblee', 'Assemblea' => "assemblee&id=$id"));

  if ($d=mysql_fetch_assoc(mysql_query("select * from assemblee_elenco where id=$id")))
  {
    ?>

    <h2>Assemblea del <?php echo $d["data"] ?></h2>

    <?php

    html_opentable();
    html_tableintest(array("Votazione", "Stato"));

    if ($r=mysql_query("select * from votazioni where id_assemblea=$id order by id"))
      while ($d=mysql_fetch_assoc($r)) {
        if ($d["apertura"]=="" || $d["apertura"]==0)
          $s="in attesa";
        else if ($d["chiusura"]=="" || $d["chiusura"]==0)
          $s="in votazione";
        else
          $s="chiusa";

        html_tabledata (array ("<a href=\"?function=assemblee&votazione=".$d["id"]."\">".$d["descrizione"]."</a>",$s));
      }

    html_closetable();

    if (userperm("admin")) {
      ?>

      <p>
        <a href="?function=assemblee&addvotazioni=<?php echo $id ?>" class="btn btn-primary">Aggiungi</a>
      </p>

      <?php
    }
  }

  html_pagetail();
}

function assemblee_delvotoitem($id)
{
  $d=mysql_fetch_assoc(mysql_query("select * from votazioni_voci where id=$id"));
  $v=mysql_fetch_assoc(mysql_query("select * from votazioni where id=".$d["id_votazione"]));
  if (is_numeric($v["id"]) && $v["apertura"]=="")
    mysql_query("delete from votazioni_voci where id=$id");
  header("Location: ?function=assemblee&votazione=".$v["id"]);
}

function assemblee_newvotoitem($id,$lab)
{
  my_insert("votazioni_voci",array("id_votazione"=>$id,"voti"=>0,"label"=>$lab));
  header("Location: ?function=assemblee&votazione=".$id);
}

function assemblee_startvotazione($id)
{
  $v=mysql_fetch_assoc(mysql_query("select * from votazioni where id=".$id));
  if ($v["apertura"]=="")
  {
    my_update("votazioni",array("apertura"=>time(),"chiusura"=>""),"id",$id);
    $r=mysql_query("select * from assemblee_soci where voto=\"si\" && id_assemblea=".$v["id_assemblea"]);
    while ($d=mysql_fetch_assoc($r))
      my_insert("votazioni_soci",array("id_socio"=>$d["id_delega"],"id_votazione"=>$id,"votato"=>"N"));
  }
  header("Location: ?function=assemblee&votazione=".$id);
}

function assemblee_stopvotazione($id)
{
  $v=mysql_fetch_assoc(mysql_query("select * from votazioni where id=".$id));
  if ($v["chiusura"]=="0")
    my_update("votazioni",array("chiusura"=>time()),"id",$id);
  header("Location: ?function=assemblee&votazione=".$id);
}

function assemblee_votazionesociook($id)
{
  $v=mysql_fetch_assoc(mysql_query("select * from votazioni as v left join assemblee_elenco as a on a.id=v.id_assemblea where v.id=$id"));
  if ($v["chiusura"]>0)
    header("Location: ?function=assemblee&votazione=$id");
  else
  if (mysql_num_rows(mysql_query("select * from votazioni_soci where id_votazione=$id and votato=\"N\" and id_socio=".$_SESSION["user"]["idsocio"]))==0)
    header("Location: ?function=assemblee&votazione=$id");
  else
  {
    $s="";
    if ($r=mysql_query("select * from votazioni_voci where id_votazione=$id"))
      while ($d=mysql_fetch_assoc($r))
        if (http_getparm("item".$d["id"])!="")
        {
          $voto[]=$d["id"];
          $s.=$d["label"]."\n";
        }
    if (($n=count($voto))<=$v["maxitem"])
    {
      mysql_query("update votazioni_soci set votato=\"$n\",scheda=\"".ereg_replace("\\\\\\\\'","'",mysql_real_escape_string($s))."\" ".
        "where votato=\"N\" and id_votazione=$id and id_socio=".$_SESSION["user"]["idsocio"]." limit 1");
      foreach($voto as $i)
        mysql_query("update votazioni_voci set voti=voti+1 where id=$i");
    }
  }
  header("Location: ?function=assemblee&votazione=$id");
}

function assemblee_votazionesocio($id)
{
  $v=mysql_fetch_assoc(mysql_query("select * from votazioni as v left join assemblee_elenco as a on a.id=v.id_assemblea where v.id=$id"));
  if ($v["chiusura"]>0)
    header("Location: ?function=assemblee&votazione=$id");
  else
  if (mysql_num_rows(mysql_query("select * from votazioni_soci where id_votazione=$id and votato=\"N\" and id_socio=".$_SESSION["user"]["idsocio"]))==0)
    header("Location: ?function=assemblee&votazione=$id");
  else
  {
    html_pagehead("Assemblee Soci", array ('Assemblee' => 'assemblee', 'Assemblea' => 'assemblee&id=' . $v["id_assemblea"], 'Votazioni' => 'assemblee&votazioni=' . $v["id_assemblea"]));

    ?>

    <div class="row">
      <div class="span6">

        <?php

        if ($v["apertura"]=="" || $v["apertura"]==0)
          $s="in attesa";
        else if ($v["chiusura"]=="" || $v["chiusura"]==0)
          $s="in votazione";
        else
          $s="chiusa";

        html_infobox (array (
          'Votazione' => $v["descrizione"],
          'Stato' => $s,
          'Numero Scelte' => $v["maxitem"],
          'Dettagli' => $v['testo']
        ));

        ?>

      </div>
      <div class="span6">

        <?php

        html_opentable();
        html_tableintest(array("Scelte effettuate"));
        if ($r=mysql_query("select * from votazioni_voci where id_votazione=$id"))
          while ($d=mysql_fetch_assoc($r))
            if (http_getparm("item".$d["id"])!="")
            {
              html_tabledata(array($d["label"]));
              $voto[]=$d["id"];
            }
        html_closetable();
        if (count($voto)>$v["maxitem"])
          echo "<P><FONT COLOR=\"#ff0000\">Hai effettuato troppe scelte!</FONT>\n";
        else
        {
          $f=array("function"=>"assemblee","votazionesocio"=>$id,"conferma"=>"SI");
          foreach($voto as $i)
            $f["item".$i]="item".$i;
          html_openform("",$f);
          html_tableformsubmit("Conferma voto");
          html_closeform();
        }
        echo "<P><A HREF=\"?function=assemblee&votazione=$id\">[Ritorna al voto]</A>\n";

        ?>

      </div>
    </div>

    <?php

    html_pagetail();
  }
}

function assemblee_votazione($id)
{
  $v=mysql_fetch_assoc(mysql_query("select * from votazioni as v left join assemblee_elenco as a on a.id=v.id_assemblea where v.id=$id"));
  html_pagehead("Assemblee Soci", array ('Assemblee' => 'assemblee', 'Assemblea' => 'assemblee&id=' . $v["id_assemblea"], 'Votazioni' => 'assemblee&votazioni=' . $v["id_assemblea"]));

  ?>

  <h2>Assemblea del <?php echo $v["data"] ?></h2>

  <div class="row">
    <div class="span6">

    <?php
      if ($v["apertura"]=="" || $v["apertura"]==0)
        $s="in attesa";
      else if ($v["chiusura"]=="" || $v["chiusura"]==0)
        $s="in votazione";
      else
        $s="chiusa";

      html_infobox (array (
        'Votazione' => $v["descrizione"],
        'Stato' => $s,
        'Numero Scelte' => $v["maxitem"],
        'Dettagli' => $v['testo']
      ));

      if (userperm("admin")){
        if ($s=="in attesa") {
          ?>

          <a href="?function=assemblee&editvotazioni=<?php echo $id ?>" class="btn">Modifica</a>
          <a href="?function=assemblee&startvotazione=<?php echo $id ?>" class="btn btn-primary">Avvia votazione</a>

          <?php
        }
        else if ($s=="in votazione") {
          ?>

          <a href="?function=assemblee&stopvotazione=<?php echo $id ?>" class="btn btn-primary">Chiudi votazione</a>

          <?php
        }
      }
    ?>

    </div>
    <div class="span6">

      <?php

      $i=1;

      if ($r=mysql_query("select * from votazioni_voci where id_votazione=$id"))
      {
        ?>

        <h3>Opzioni</h3>

        <?php

        html_opentable(true);
        html_tableintest(array("#","Voce","Voti"));

        while ($d=mysql_fetch_assoc($r))
        {
          if ($s=="chiusa")
            $info=$d["voti"];
          else
          if ($s=="in attesa" && userperm("admin"))
            $info='<a href=\"?function=assemblee&delvotoitem=' . $d["id"] . '" class="btn">elimina</a>';
          else
            $info="";

          html_tabledata(array($i++,$d["label"],$info));
        }

        html_closetable();
      }

      if ($s!="in attesa" && $d=mysql_fetch_assoc(mysql_query("select count(*) as votanti, sum(if(votato=\"N\",0,1)) as fatto from votazioni_soci where id_votazione=$id"))) {
        ?>

        <p>Hanno votato <?php echo $d["fatto"] ?> soci su <?php echo $d["votanti"] ?> votanti</p>

        <?php
      }

      if (userperm("admin") && $s=="in attesa")
      {
        html_openform("",array("function"=>"assemblee","newvotoitem"=>$id), true);
        html_tableformtext(array(),"Aggiungi scelta votazione","item",60);
        html_tableformsubmit("Aggiungi");
        html_closeform();
      }

      if ($s=="in votazione" && userperm("admin"))
      {
        if ($r=mysql_query("select * from votazioni_soci as v left join soci_iscritti as s on s.id=v.id_socio where votato=\"N\" and id_votazione=$id")): ?>

          <p>Devono votare:</p>
          <ul>
            <?php while ($d=mysql_fetch_assoc($r)): ?>
            <li><?php echo $d["cognome"]." ".$d["nome"]." (".$d["nickname"].")" ?></li>
            <?php endwhile; ?>
          </ul>

        <?php endif;
      }
      if ($s=="in votazione" and ($n=mysql_num_rows(mysql_query(
        "select * from votazioni_soci where id_votazione=$id and votato=\"N\" and id_socio=".$_SESSION["user"]["idsocio"])))>0)
      {
        html_openform("",array("function"=>"assemblee","votazionesocio"=>$id));
        html_tableforminfomsg((($n==1)?("Devi votare 1 volta. "):("Devi votare $n volte. ")). "Puoi scegliere al massimo ".(($v["maxitem"]==1)?("1 opzione."):($v["maxitem"]." opzioni.")));
        if ($r=mysql_query("select * from votazioni_voci where id_votazione=$id"))
          while ($d=mysql_fetch_assoc($r))
            html_tableformcheck(array(),$d["label"],"item".$d["id"]);
        html_tableformsubmit("Vota");
        html_closeform();
      }

      ?>

    </div>
  </div>

  <?php

  html_pagetail();
}

function assemblee_lista()
{
  html_pagehead("Assemblee Soci");

  if ($r=mysql_query("select * from assemblee_elenco order by data desc"))
  {
    html_opentable();
    html_tableintest(array("Data","Tipo","Stato"));
    while ($d=mysql_fetch_assoc($r)) {
        $class= '';

        switch ($d["stato"]) {
          case 'convocata':
            $class = 'info';
            break;

          case '???':
            $class = 'success';
            break;
        }

        html_tabledata(array(
          "<a href=\"?&function=assemblee&id=".$d["id"]."\">".printable_date ($d["data"])."</a>",
          $d["tipo"],$d["stato"]), $class);
    }
    html_closetable();
  }
  if (userperm("admin")) {
    ?>
    <a href="?function=assemblee&action=new" class="btn btn-primary">Nuova</a>
    <?php
  }
  html_pagetail();
}

function assemblee()
{
  if (userperm("admin") && http_getparm("conferma")=="ok" && is_numeric($i=http_getparm("convoca")))
    assemblee_confermaconvoca($i);
  else
  if (userperm("admin") && is_numeric($i=http_getparm("convoca")))
    assemblee_askconvoca($i);
  else
  if (userperm("admin") && is_numeric($i=http_getparm("apri")))
    assemblee_apri($i);
  else
  if (userperm("admin") && http_getparm("action")=="new" && http_getparm("conferma")=="ok")
    assemblee_confermanew();
  else
  if (userperm("admin") && http_getparm("action")=="new")
    assemblee_new();
  else
  if (userperm("admin") && is_numeric($i=http_getparm("edit")) && http_getparm("conferma")=="ok")
    assemblee_confermaedit($i);
  else
  if (userperm("admin") && is_numeric($i=http_getparm("edit")))
    assemblee_edit($i);
  else
  if (is_numeric($i=http_getparm("partecipa")))
    assemblee_partecipa($i);
  else
  if (is_numeric($i=http_getparm("assente")))
    assemblee_assente($i);
  else
  if (is_numeric($i=http_getparm("delega")) && is_numeric($s=http_getparm("socio")))
    assemblee_delega($i,$s);
  else
  if (userperm("admin") && is_numeric($s=http_getparm("presente")) && is_numeric($a=http_getparm("assemb")))
    assemblee_presenza($a,$s);
  else
  if (userperm("admin") && is_numeric($s=http_getparm("voto")) && is_numeric($a=http_getparm("assemb")))
    assemblee_voto($a,$s);
  else
  if (userperm("admin") && is_numeric($i=http_getparm("startvotazione")))
    assemblee_startvotazione($i);
  else
  if (userperm("admin") && is_numeric($i=http_getparm("stopvotazione")))
    assemblee_stopvotazione($i);
  else
  if (userperm("admin") && is_numeric($i=http_getparm("newvotoitem")) && ($l=http_getparm("item"))!="")
    assemblee_newvotoitem($i,$l);
  else
  if (userperm("admin") && is_numeric($i=http_getparm("delvotoitem")))
    assemblee_delvotoitem($i,$l);
  else
  if (is_numeric($i=http_getparm("votazionesocio")) && http_getparm("conferma")=="SI")
    assemblee_votazionesociook($i);
  else
  if (is_numeric($i=http_getparm("votazionesocio")))
    assemblee_votazionesocio($i);
  else
  if (is_numeric($i=http_getparm("votazione")))
    assemblee_votazione($i);
  else
  if (userperm("admin") && is_numeric($i=http_getparm("addvotazioni")) && ($d=http_getparm("descrizione"))!="" && is_numeric($m=http_getparm("maxitem")))
    assemblee_votazioni_addform($i,$d,$m);
  else
  if (userperm("admin") && is_numeric($i=http_getparm("addvotazioni")))
    assemblee_votazioni_add($i);
  else
  if (userperm("admin") && is_numeric($i=http_getparm("editvotazioni")) && ($d=http_getparm("descrizione"))!="" && is_numeric($m=http_getparm("maxitem")))
    assemblee_votazioni_editform($i,$d,$m);
  else
  if (userperm("admin") && is_numeric($i=http_getparm("editvotazioni")))
    assemblee_votazioni_edit($i);
  else
  if (is_numeric($i=http_getparm("votazioni")))
    assemblee_votazioni($i);
  else
  if (is_numeric($i=http_getparm("id")))
    assemblee_mostra($i);
  else
    assemblee_lista();
}

?>
