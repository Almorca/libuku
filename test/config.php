<?
// The source code packaged with this file is Free Software, Copyright (C) 2008 by
// Alejandro Moreno Calvo < almorca@gmail.com >
// It's licensed under the GNU AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.fsf.org/licensing/licenses/agpl-3.0.html
// GNU AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

define('lbkpath', dirname(__FILE__));
define('lbkinclude', dirname(__FILE__).'/include/');
ini_set('include_path', '.:'.lbkinclude.':'.lbkpath);

$server_name = $_SERVER['SERVER_NAME'];

$globals['db_server'] = 'localhost';
$globals['db_name'] = 'jaroixjg_libuku';
$globals['db_user'] = 'jaroixjg_libuku0';
$globals['db_password'] = 'L9kZWMfZ4E';

// language site
$globals['language'] = 'es';

// Specify you base URL, "/" if is the root document
$globals['base_directory'] = '/home/jaroixjg/public_html';
// Specify you base URL, "/" if is the root document
$globals['base_url'] = '/';
// Specify you URL for static content
$globals['base_static'] = '/static/';
// Specify you URL for book images
$globals['book_images'] = '/static/books/images/';
// Specify you URL for ebooks files
$globals['book_files'] = '/static/books/files/';

// Specify others URLs
$sections['author'] = 'autor/';
$sections['book'] = 'libro/';
$sections['category'] = 'genero/';
$sections['language'] = 'idioma/';
$sections['tag'] = 'etiqueta/';

// Metadata
$globals['slogan'] = 'La web de descarga de libros';
$globals['description'] = 'Librería online donde descargar libros electrónicos gratis';
$globals['keywords'] = 'libros, literatura, libros gratis, descargar libros, bajar libros, comprar libros, comprar por internet, buscar libros, libros electronicos, ebooks, libreria online, libuku';
$globals['author'] = 'Alejandro Moreno Calvo';

// Don't touch behind this
$globals['mysql_persistent'] = true;
$globals['mysql_master_persistent'] = false;

mb_internal_encoding("UTF-8");

include lbkinclude.'db.php';
// For production servers
//$db->hide_errors();

include lbkinclude.'class.error.php';

/* Config the template engine */
require lbkinclude.'/Haanga/Haanga.php';

Haanga::configure(array(
    'template_dir' => lbkpath.'/templates/',
    'cache_dir' => lbkpath.'/cache/',
	'compiler' => array (
		'allow_exec' => TRUE,
		/* global $global for all templates */
		'global' => array('globals')
	)
));

?>
