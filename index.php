<?php
// The source code packaged with this file is Free Software, Copyright (C) 2008 by
// Alejandro Moreno Calvo < almorca@gmail.com >
// It's licensed under the GNU AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.fsf.org/licensing/licenses/agpl-3.0.html
// GNU AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

include('config.php');
include(lbkinclude.'utils.php');
include(lbkinclude.'theme.php');
include(lbkinclude.'class.book.php');
include(lbkinclude.'class.genre.php');

function printBooks($limit = 12) {
	global $db;

	try {
		if($books = $db->get_results("SELECT".Book::SQL."ORDER BY Books.book_date_submitted desc LIMIT $limit")) {
			foreach( $books as $book_id ) {
				$book = new Book($book_id);
				$book_url = getURL('book', $book->getURL());
				$writers = array();
				if ($authors = $book->getWriters()) {
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


/**
 * Show the main genres
 */
function printGenres() {
	$genre = array();

	if ($parentGenres = Genre::getParentsGenres()) {
		foreach( $parentGenres as $genreData ) {
			$genre = new Genre($genreData);
			// Se crea el enlace a la web interna del autor y si no se consigue se continua al siguiente autor
			if ( ! $urlText = getLink($genre->getTitle(), 'category', $genre->getURL(), array('title' => $genre->getTitle()))) {
				continue;
			}
			$genres[] = $urlText;
		}
	}
	$vars = compact('genres');

	if ( $vars ) {
	  Haanga::Load('index.html', $vars);
	}
}

do_header("Descarga de libros electrónicos", 'home');
printGenres();
echo '<div id="maincolumn">';
printBooks();
echo '</div>';
do_footer();

?>
