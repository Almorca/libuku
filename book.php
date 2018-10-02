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
include(lbkinclude.'class.genre.php');
include(lbkinclude.'theme.php');
include(lbkinclude.'utils.php');
include(lbkinclude.'language_' . $globals['language'] . '.php');

/** GLOBALS variables */
$book = NULL;
$url = @$_GET['url'];


function printBook($book) {
	global $globals;
	$author = NULL;
	$writers = array();
	$contributors = array();
	$title = $book->getTitle();
	$title_with_authors = $title;
	$rating = $book->getRating();
	$votes = $book->getVotes();
	$description = $book->getDescription();
	$collection = $book->getCollection();
	$genres = array();

	$isfirst_1  = TRUE;
	if ($authors = $book->getWriters()) {
		$title_with_authors .= ' de ';
		foreach( $authors as $author ) {
			// Se crea el enlace a la web interna del autor y si no se consigue se continua al siguiente autor
			if ( ! $urlText = getLink($author['author_name'], 'author', $author['author_url']) ) {
				continue;
			}
			$writers[] = $urlText;
			if (!$isfirst_1) {
                $title_with_authors .= ', ';
            }
			$title_with_authors .= $author['author_name'];
		}
	}

	// Contributors
	if ($authors = $book->getContributors()) {
		foreach( $authors as $author ) {
			// Se crea el enlace a la web interna del autor y si no se consigue se continua al siguiente autor
			if ( ! $urlText = getLink($author['author_name'], 'author', $author['author_url'], array('about' => $author['author_url'], 'property' => 'foaf:name', 'rel' => 'foaf:homepage', 'typeof' => 'foaf:Person')) ) {
				continue;
			}
			$contributors[] = $urlText;
		}
	}

	// Breadcrum
	$genres[] = '<a href="http://' . getBaseUrl() . '" rel="v:url" property="v:title">Inicio</a>';
	if ( $genre_url = $book->getCategory() ) {
		$genre = new Genre($genre_url);
		$pages = $genre->getAncestors();
		foreach( $pages as $page) {
			$g = new Genre($page);
			$genres[] = getLink($g->getTitle(), 'category', $g->getURL(), array('rel' => 'v:url', 'property' => 'v:title'));
		}
		$genres[] = getLink($genre->getTitle(), 'category', $genre->getURL(), array('rel' => 'v:url', 'property' => 'v:title'));
	}

	$vars = compact('book', 'title', 'writers', 'contributors', 'genres', 'rating', 'votes', 'collection');

	do_header($title_with_authors, 'book', array('DC.title' => $title,
		'keywords' => "$title, $title_with_authors, descargar libro $title_with_authors, descargar libro electrnico $title_with_authors, descargar ebook $title_with_authors, $collection",
		'description' => "Página donde descargar el libro $title_with_authors"),
		array('og:title' => $title, 'og:url' => getURL('book', $book->getURL()),
		"og:image" => 'http://'.getServerName().$globals['book_images'].$book->getImage(),
		'og:type' => 'book', 'og:site_name' => 'libuku.com')
	);
	if ( $vars ) {
		Haanga::Load('book.html', $vars);
	}
	do_footer();
}

if (is_null($url)) {
	// error 404
	echo 'error 404';
} else {
	if ($id = Book::getBookId($url)) {
		$book = new Book($id);
		printBook($book);
	} else {
		do_header(_('Libuku'));
		echo "<p>La pgina que busca no est disponible</p>";
		do_footer();
	}
}

?>