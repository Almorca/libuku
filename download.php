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
include(lbkinclude.'class.book.php');
include(lbkinclude.'class.download.php');
include(lbkinclude.'utils.php');

/** GLOBALS variables */
global $globals;
$book = NULL;
$url = @$_GET['url'];

if (is_null($url)) {
	// error 404
	echo 'error 404';
} else {
	if ($id = Book::getBookId($url)) {
		$book = new Book($id);
		try {
			$download = new Download($id . '.epub', $globals['base_directory'] . $globals['book_files']);
			$download->sendFile($book->getURL() . '.epub');
		} catch (FileNotFoundException $e) {
			echo 'Error: archivo no encontrado.';
		}
		$book->addDownload();
	} else {
		// error 404
		echo 'Error 404';
	}
}

?>
