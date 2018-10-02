<?php
// The source code packaged with this file is Free Software, Copyright (C) 2008 by
// Alejandro Moreno Calvo < almorca@gmail.com >
// It's licensed under the GNU AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.fsf.org/licensing/licenses/agpl-3.0.html
// GNU AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".


/** This file test class Category */
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
include(lbkinclude.'class.category.php');


$category = new Category('Novela');

/* test store() */
if ($category->store() === true) {
	echo '<p class="ok">Método store(): test correcto</p>';
} else {
	echo '<p class="error">Método store(): test incorrecto</p>';
}

/* test getTitle() */
if ($category->getTitle() === 'Novela') {
	echo '<p class="ok">Método getTitle(): test correcto</p>';
} else {
	echo '<p class="error">Método getTitle(): test incorrecto</p>';
}

/* test setFather(), getFather() */
$categorySon = new Category('NovelaHija');
$categorySon->setFather('Novela');
$categorySon->store();
if ($categorySon->getFather() === 'Novela') {
	echo '<p class="ok">Método setFather() y getFather(): test correcto</p>';
} else {
	echo '<p class="error">Método setFather() y getFather(): test incorrecto</p>';
}

$categorySon = new Category('NovelaHija2');
$categorySon->setFather('Novela');
$categorySon->store();

/* test getFather() */
if (is_null($category->getFather())) {
	echo '<p class="ok">Método getFather(): test 1 correcto</p>';
} else {
	echo '<p class="error">Método getTitle(): test 1 incorrecto</p>';
}

/* test getChildren() */
if ($categories = $category->getChildren()) {
	$i = 0;
	foreach( $categories as $cAux ) {
		echo $cAux->category_title;
		echo ' ';
		++$i;
	}
	if ($i === 2) {
		echo '<p class="ok">Método getChildren(): test correcto</p>';
	} else {
		echo '<p class="error">Método getChildren(): test incorrecto</p>';
	}
} else {
	echo '<p class="error">Método getChildren(): test incorrecto</p>';
}

/* test store() */
$categorySon->setFather('NovelaHija');
$categorySon->store();
$categoryAux = new Category('NovelaHija');
if ($categories = $categoryAux->getChildren()) {
	$i = 0;
	foreach( $categories as $cAux ) {
		echo $cAux->category_title;
		echo ' ';
		++$i;
	}
	if ($i === 1) {
		echo '<p class="ok">Método store(): test correcto</p>';
	} else {
		echo '<p class="error">Método store(): test incorrecto en el paso 1.</p>';
	}
} else {
	echo '<p class="error">Método store(): test incorrecto en el paso 2.</p>';
}

?>

</body>
</html>
