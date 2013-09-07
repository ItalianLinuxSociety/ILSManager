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

function html_pagehead($titolo, $links = array ()) {
	global $PROGRAMMA;

  header('Content-type: text/html; charset=utf-8');

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?php echo $PROGRAMMA["nome"] ?></title>

	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">

	<?php foreach ($PROGRAMMA["css"] as $css): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $css ?>">
	<?php endforeach; ?>

  <script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.js"></script>
  <script type="text/javascript" src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

	<?php foreach ($PROGRAMMA["js"] as $js): ?>
	<script type="text/javascript" src="<?php echo $js ?>"></script>
	<?php endforeach; ?>
</head>
<body bgcolor="#ffffff">
  <div class="container">
    <p>&nbsp;</p>

	  <?php if (array_key_exists ('user', $_SESSION) && is_array($_SESSION["user"])): ?>

    <ul class="breadcrumb">
      <li><a href=".">Indice</a> <span class="divider">/</span></li>
      <?php foreach ($links as $name => $url): ?>
        <li><a href="?function=<?php echo $url ?>"> <?php echo $name ?></a> <span class="divider">/</span></li>
      <?php endforeach; ?>
      <li><?php echo $titolo ?></li>
    </ul>

    <div class="row">
	    <div class="span12">
        <p class="pull-right">
          ciao,&nbsp;<?php echo ereg_replace(" ","&nbsp;",$_SESSION["user"]["nome"]) ?>&nbsp;<a class="label label-info" href="?logout=ok">logout</a>
        </p>
      </div>
    </div>

    <hr />

	  <?php endif; ?>

<?php

}

function html_pagetail()
{
  global $PROGRAMMA;

  ?>

    <hr />

    <div class="row">
      <div class="span4">
        Il codice sorgente di ILSManager è <a href="https://github.com/ItalianLinuxSociety/ILSManager">disponibile qui</a>, insieme ad una nutrita lista di <a href="https://github.com/ItalianLinuxSociety/ILSManager/issues">cose da fare</a> per migliorarlo!
      </div>

      <div class="span4 pull-right">
        Per segnalazioni, domande e consigli, manda una mail a <a href="mailto:<?php echo $PROGRAMMA ['mail'] ?>"><?php echo $PROGRAMMA ['mail'] ?></a>
      </div>
    </div>
  </div>

  <br />

  </body>
  </html>

  <?php
}


function html_opentable($sortable = false, $forcebody = false, $id = null)
{
  ?>

  <table<?php if ($id != null) echo ' id="' . $id . '"' ?> class="table table-hover<?php if ($sortable == true) echo ' tablesorter' ?>">
  <?php if ($forcebody == true): ?>
  <tbody>
  <?php endif; ?>

  <?php
}

function html_closetable()
{
  ?>
  </tbody>
  </table>
  <?php
}

function html_tablefieldrow($descr,$valore)
{
  global $PROGRAMMA;

  if (is_numeric($valore))
    $align = ' align="right"';
  else
    $align = '';

  if ($valore!="" && $descr!="")
    echo " <TR>\n  <TD BGCOLOR=\"#d0d0d0\">$descr</TD>\n".
         "  <TD$align>".htmlentities($valore)."</TD>\n </TR>\n";
}

function html_tablefieldrownl($descr,$valore)
{
  global $PROGRAMMA;
  if (is_numeric($valore))
    $align = ' align="right"';
  else
    $align = '';

  if ($valore!="" && $descr!="")
    echo " <TR>\n  <TD BGCOLOR=\"#d0d0d0\">$descr</TD>\n".
         "  <TD$align>".nl2br(htmlentities($valore))."</TD>\n </TR>\n";
}

function html_tablerow($celle, $class = '')
{
  ?>

  <tr class="<?php echo $class ?>">
    <?php

    for ($i=0; $i<count($celle); $i++)
    {
      if ($celle[$i]=="")
        $celle[$i]="&nbsp;";

      if (is_numeric(strip_tags($celle[$i]))) {
        ?>
        <td align="right"><?php echo $celle[$i] ?></td>
        <?php
      }
      else {
        ?>
        <td><?php echo $celle[$i] ?></td>
        <?php
      }
    }

    ?>
  </tr>

  <?php
}

function html_tableintest($celle)
{
  ?>

  <thead>
    <tr>
      <?php

      for ($i = 0; $i < count($celle); $i++)
      {
        if ($celle[$i]=="")
          $celle[$i]="&nbsp;";

        if (is_numeric(strip_tags($celle[$i]))) {
          ?>
          <th align="right"><?php echo $celle[$i] ?></th>
          <?php
        }
        else {
          ?>
          <th><?php echo $celle[$i] ?></th>
          <?php
        }
      }

      ?>
    </tr>
  </thead>
  <tbody>

  <?php
}

function html_tabledata($celle, $class = '')
{
  global $PROGRAMMA;
  html_tablerow($celle, $class);
}

function html_openform($action, $hidden = array (), $inline = false)
{
  global $current_form_inline;
  $current_form_inline = $inline;

  ?>

  <form method="post" action="<?php echo $action ?>" class="<?php if ($inline == false) echo 'form-horizontal'; else echo 'form-inline' ?>">
    <?php foreach ($hidden as $n=>$v): ?>
    <input type="hidden" name="<?php echo $n ?>" value="<?php echo $v ?>">
    <?php endforeach; ?>

  <?
}

function html_openformfile($action,$hidden)
{
  ?>

  <form enctype="multipart/form-data" method="POST" action="<?php echo $action ?>">
    <?php foreach ($hidden as $n=>$v): ?>
    <input type="hidden" name="<?php echo $n ?>" value="<?php echo $v ?>" />
    <?php endforeach; ?>

  <?php
}

function html_closeform()
{
  ?>
  </form>
  <?php
}

function html_infobox ($contents)
{
  ?>

  <div class="well">

  <?php foreach ($contents as $key => $value): ?>
  <p><strong><?php echo $key ?></strong>: <?php echo $value ?></p>
  <?php endforeach; ?>

  </div>

  <?php
}

function html_tableformstatic($descr,$text)
{
  ?>

  <div class="control-group">
    <label class="control-label"><?php echo $descr ?></label>
    <div class="controls">
      <div class="plain"><?php echo $text ?></div>
    </div>
  </div>

  <?php
}

function html_tableformtext ($my, $descr, $name, $size = 0, $help = '')
{
  global $current_form_inline;

  if ($my != null)
    $value = htmlspecialchars (array_key_exists ($name, $my) ? $value = $my[$name] : '');
  else
    $value = '';

  if ($current_form_inline == false) {
    ?>

    <div class="control-group">
      <label class="control-label" for="<?php echo $name ?>"><?php echo $descr ?></label>
      <div class="controls">
        <input type="text" id="<?php echo $name ?>" name="<?php echo $name ?>" value="<?php echo $value ?>" class="input-block-level" />
        <?php if ($help != ''): ?>
        <span class="help-block"><?php echo $help ?></span>
        <?php endif; ?>
      </div>
    </div>

    <?php
  }
  else {
    ?>

    <input placeholder="<?php echo $descr ?>" type="text" id="<?php echo $name ?>" name="<?php echo $name ?>" value="<?php echo $value ?>" />

    <?php
  }
}

function html_tableformpassw($my,$descr,$name,$size)
{
  ?>

  <div class="control-group">
    <label class="control-label" for="<?php echo $name ?>"><?php echo $descr ?></label>
    <div class="controls">
      <input type="password" id="<?php echo $name ?>" name="<?php echo $name ?>" class="input-block-level" />
    </div>
  </div>

  <?php
}

function html_tableformtextarea($my,$descr,$name,$cols=60,$rows=5)
{
  ?>

  <div class="control-group">
    <label class="control-label" for="<?php echo $name ?>"><?php echo $descr ?></label>
    <div class="controls">
      <textarea name="<?php echo $name ?>" class="input-block-level"><?php if (array_key_exists ($name, $my)) echo htmlspecialchars ($my [$name]) ?></textarea>
    </div>
  </div>

  <?php
}

/*
  Follia ereditata dalla gestione delle select, da correggere
  dappertutto gestendola con array indicizzati
*/
function test_into_select ($my, $name, $d)
{
  if (array_key_exists ($name, $my))
    return ($d [1] == $my [$name]);
  else
    return (array_key_exists (2, $d) && $d[2] == "S");
}

function html_tableformselect($my,$descr,$name,$sel)
{

  ?>

  <div class="control-group">
    <label class="control-label" for="<?php echo $name ?>"><?php echo $descr ?></label>
    <div class="controls">
      <select name="<?php echo $name ?>">
        <?php foreach ($sel as $d): ?>
        <option value="<?php echo $d[0] ?>"<?php if (test_into_select ($my, $name, $d)) echo ' selected="selected"'; ?>><?php echo $d[1] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <?php
}

/*
  Il parametro "opts" deve essere un array di array associativi formattati come:

  [
    ['val' => 'valore della opzione', 'text' => 'testo da visualizzare'],
    ['val' => 'valore della opzione', 'text' => 'testo da visualizzare'],
    ['val' => 'valore della opzione', 'text' => 'testo da visualizzare']
  ]
*/
function html_tableformradio ($my, $descr, $name, $opts, $alternatives = null)
{
  ?>

  <div class="control-group radioselector">
    <label class="control-label" for="<?php echo $name ?>"><?php echo $descr ?></label>
    <div class="controls">
      <?php foreach ($opts as $opt): ?>
      <input type="radio" name="<?php echo $name ?>" value="<?php echo $opt ['val'] ?>" <?php echo ($my [$name] == $opt ['val'] ? ' checked="checked"' : '') ?>/> <?php echo $opt ['text'] ?>
      <?php endforeach; ?>

      <?php
        if ($alternatives != null) {
          foreach ($alternatives as $altkey => $altcontents) {
            ?>
            <p class="selectable_<?php echo $altkey ?>">
              <?php echo $altcontents ?>
            </p>
            <?php
          }
        }
      ?>
    </div>
  </div>

  <?php
}

function html_tableformcheck($my,$descr,$name)
{
  ?>

  <div class="control-group">
    <label class="control-label" for="<?php echo $name ?>"><?php echo $descr ?></label>
    <div class="controls">
      <input type="checkbox" name="<?php echo $name ?>" value="<?php echo $name ?>"<?php if (isset($my[$name])) echo ' checked="checked"' ?> />
    </div>
  </div>

  <?php
}

function html_tableformfile($descr,$name,$maxsize)
{
  ?>

  <div class="control-group">
    <label class="control-label" for="<?php echo $name ?>"><?php echo $descr ?></label>
    <div class="controls">
      <input type="hidden" name="max_file_size" value="<?php echo $maxsize ?>" />
      <input type="file" name="<?php echo $name ?>" />
    </div>
  </div>

  <?php
}

function html_tableformsubmit($text)
{
  global $current_form_inline;

  if ($current_form_inline == false) {
    ?>

    <div class="control-group">
      <div class="controls">
        <button type="submit" class="btn btn-primary"><?php echo $text ?></button>
      </div>
    </div>

    <?php
  }
  else {
    ?>

    <button type="submit" class="btn btn-primary"><?php echo $text ?></button>

    <?php
  }
}

function html_tableformerrormsg($text)
{
  if ($text!="") {
    ?>

    <div class="alert alert-error">
      <?php echo $text ?>
    </div>

    <?php
  }
}


function html_tableforminfomsg($text)
{
  if ($text!="") {
    ?>

    <div class="alert alert-info">
      <?php echo $text ?>
    </div>

    <?php
  }
}


function http_getparm($parm)
{
  if (array_key_exists ($parm, $_REQUEST))
    return trim($_REQUEST[$parm]);
  else
    return '';
}

function get_request_param ($name)
{
  if (array_key_exists ($name, $_REQUEST))
    return $_REQUEST [$name];
  else if (array_key_exists ($name, $_POST))
    return $_POST [$name];
  else
    return '';
}

function test_request_param ($name, $value)
{
  if (array_key_exists ($name, $_REQUEST))
    return $_REQUEST [$name] == $value;
  else
    return false;
}

?>
