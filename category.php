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

/**
 * Mostramos las categorías principales y secundarias
 */
if (!is_null($arrayCategories = Category::getParentsCategories())) {
	echo '<ul>';
	foreach ($arrayCategories as $categoryAux) {
		echo '<li>' . $categoryAux->category_title . '</li>';
		$category = new Category($categoryAux->category_title);
		if (!is_null($childrenCategories = $category->getChildren())) {
			echo '<ul>';
			foreach ($childrenCategories as $categoryChildren) {
				echo '<li>' . $categoryChildren->category_title . '</li>';
			}
			echo '</ul>';
		}
	}	
	echo '</ul>';
}

?>

</body>
</html>
