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

function test_value_array ($array, $name, $value = null, $type = '')
{
  if (array_key_exists ($name, $array) == false)
    return false;

  if ($value == null) {
    switch ($type) {
      case 'str':
        return is_string ($array [$name]);
      case 'array':
        return is_array ($array [$name]);
      case 'num':
        return is_numeric ($array [$name]);
    }
  }
  else {
    return ($array [$name] == $value);
  }
}

function printable_date ($date)
{
  $months = array ('gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno',
                   'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre');

  list ($y, $m, $d) = explode ('-', $date);
  $m = $months [$m - 1];
  return "$d $m $y";
}

/*
  Copiato da
  http://stackoverflow.com/questions/4356289/php-random-string-generator
*/
function random_string ($length = 10)
{
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $randomString = '';

  for ($i = 0; $i < $length; $i++)
      $randomString .= $characters [rand (0, strlen ($characters) - 1)];

  return $randomString;
}

?>
