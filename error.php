<?php
/**
 *
 * PHP version 5
 *
 * LICENCE: This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author     Alejandro Moreno Calvo <almorca@almorca.es>
 * @copyright  &copy; 2009 Alejandro Moreno Calvo
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @version    Release: @package_version@
 * @since      0.1
 */

include('config.php');

/** GLOBALS variables */
$book = NULL;
$code = @$_GET['code'];

function error404() {
	$url = $_SERVER['REQUEST_URI'];

	echo 'La página ' . $url . ' no ha sido encontrada en nuestra web.';
}

function error500() {
	echo 'Error interno';
}

if (is_null($code) || ( ! is_numeric($code) ) ) {
	error500();
} else {
	switch($code) {
		case 404:
			error404();
			break;
		case 500: default:
			error500();
			break;
	}
}

?>
