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

global $globals;

include('config.php');
include(lbkinclude.'class.genre.php');
include(lbkinclude.'class.book.php');
include(lbkinclude.'theme.php');
include(lbkinclude.'utils.php');
include(lbkinclude.'language_' . $globals['language'] . '.php');

/** GLOBALS variables */
$genre = @$_GET['genre'];


function printBooks($genre) {
	global $db;

	try {
		if($books = $db->get_results("SELECT".Book::SQL."WHERE Books.book_genre = '$genre' ORDER BY Books.book_id")) {
			foreach( $books as $book_id ) {
				$book = new Book($book_id);
				$book_url = getURL('book', $book->getURL());
				if ($authors = $book->getWriters()) {
					$writers = array();
					foreach( $authors as $author ) {
						// Se crea el enlace a la web interna del autor y si no se consigue se continua al siguiente autor
						if ( ! $urlText = getLink($author['author_name'], 'author', $author['author_url'], array('class' => 'fn')) ) {
							continue;
						}
						$writers[] = $urlText;
					}
				}
				$vars = compact('book', 'writers', 'book_url');

				if ( $vars ) {
				  Haanga::Load('home_book.html', $vars);
				}
			}
		} else {
			echo '<p>No hay libros en esta categoría.</p>';
		}
	} catch (Exception $e) {
		Error::printError($e->getMessage());
	}
}

function printSubgenres($genre) {
	$mainGenre = new Genre($genre);
	if ( $children = $mainGenre->getChildren() ) {
		foreach ($children as $child ) {
			echo '<h3 style="clear: left;">'. getLink($child['_title'], 'genre', $child['_url']) .'</h3>';
			printBooks($child['_url']);
		}
	}
}


if (is_null($genre)) {
	// error 404
	echo 'Error 404';
} else {
	$title = Genre::getGenreTitle($genre);
	do_header(_('Libuku'), 'genre', array('DC.title' => "Género $title",
		'keywords' => "$title, Género $title, libros electrónicos de $title, ebooks de $title",
		'description' => "Libros del género $title."));
	echo "<h1>$title</h1>";
	printBooks($genre);
	echo "<h2>Subgéneros</h2>";
	printSubgenres($genre);
	do_footer();
}

?>
