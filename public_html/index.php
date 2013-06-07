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

$UNIXDIR=array(
  "archivio"=>"../ARCHIVIO/",
  "etc"=>"../etc/",
  "php"=>"../php/"
);

include $UNIXDIR["php"]."main.php";

session_start();
my_connect();

if (isset($_REQUEST["function"]))
{
  if ($_REQUEST["function"]=="rememberpw")
    formpromemoriapw();
}

if (isset($_REQUEST["auth_user"]) && isset($_REQUEST["AUTH_PW"]))
{
  my_checklogin($_REQUEST["auth_user"],$_REQUEST["AUTH_PW"]);
  header("Location: .");
  exit;
}
if (isset($_REQUEST["logout"]))
{
  unset($_SESSION["user"]);
  header("Location: .");
  exit;
}
if (array_key_exists ('user', $_SESSION) == false || !is_array($_SESSION["user"]))
  authpage();

ilsmanager();


?>
