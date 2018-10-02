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
include(lbkinclude.'class.author.php');
include(lbkinclude.'class.book.php');
include(lbkinclude.'theme.php');
include(lbkinclude.'utils.php');
include(lbkinclude.'language_' . $globals['language'] . '.php');

/** GLOBALS variables */
global $globals;
$book = NULL;
$url = @$_GET['url'];


function printBooks($author) {
	global $db;

	try {
		if($books = $db->get_results("SELECT".Book::SQL."CROSS JOIN Books_Authors ON Books_Authors.book_id = Books.book_id AND author_id = $author ORDER BY Books.book_id")) {
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
		}
	} catch (Exception $e) {
		Error::printError($e->getMessage());
	}
}


if (is_null($url)) {
	// error 404
	echo 'error 404';
} else {
	if ($id = Author::getAuthorId($url)) {
		$author = new Author($id);
		$author_name = $author->getName();
		do_header($author_name, 'author', array('DC.title' => $author_name,
		'keywords' => "$author_name, descargar libros de $author_name",
		'description' => "Página donde descargar libros de $author_name"));
		$menu[] = '<a href="http://' . getBaseUrl() . '">Inicio</a>';
		$menu[] = getLink('Autores', 'author');
		$menu[] = $author_name;
		$vars = compact('author', 'menu');
		if ( $vars ) {
			Haanga::Load('author.html', $vars);
		}
		printBooks($id);
		do_footer();
	} else {
		// Mostrar lista de autores
		do_header(_('Libuku'));
		echo "<p>La página que busca no está disponible</p>";
		do_footer();
	}
}

?>
