<?php
// The source code packaged with this file is Free Software, Copyright (C) 2008 by
// Alejandro Moreno Calvo < almorca@gmail.com >
// It's licensed under the GNU AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.fsf.org/licensing/licenses/agpl-3.0.html
// GNU AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".


/** This file test class Book */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>

<?php
include('config.php');
include(lbkinclude.'utils.php');
include(lbkinclude.'class.book.php');

$title = 'la-regenta';

$id = Book::getBookId($title);
if (is_int($id)) {
	echo '<p class="ok">Método estático getBookId(): test correcto</p>';
} else {
	echo '<p class="error">Método stático getBookId(): test incorrecto</p>';
} 
	
$book = new Book($id);

$idAux = $book->getId();
if ($idAux == $id) {
	echo '<p class="ok">Método getId(): test correcto</p>';
} else {
	echo '<p class="error">Método getId(): test incorrecto. Valor devuelto ' + $idAux + '</p>';
}


$title = $book->getTitle();
if (!is_null($title)) {
	echo '<p class="ok">Método getTitle(): test correcto</p>';
} else {
	echo '<p class="error">Método getTitle(): test incorrecto.</p>';
}

if ($tags = $book->getTags()) {
	foreach( $tags as $tag ) {
		echo $tag->tag_word + ' ';
	}
	echo '</ul>';
} else {
	echo '<p class="error">Método getTags(): test incorrecto</p>';
}

?>

</body>
</html>
