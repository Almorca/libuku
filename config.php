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

$globals['db_server'] = 'mysql51-30.bdb';
$globals['db_name'] = 'libuku'; 
$globals['db_user'] = 'test';
$globals['db_password'] = 'test';

// language site
$globals['language'] = 'es';

// Specify you base directory without "/"
$globals['base_directory'] = '/home/libuku/www';
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
$sections['category'] = 'genero/'; /*deprecated*/
$sections['genre'] = 'genero/';
$sections['language'] = 'idioma/';
$sections['tag'] = 'etiqueta/';

// Metadata
$globals['slogan'] = 'Libuku - Descarga de libros electrónicos';
$globals['description'] = 'Librería online donde descargar libros electrónicos gratis';
$globals['keywords'] = 'libro, libro electrónico, ebook, literatura, libros gratis, descargar libros, bajar libros, comprar libros, buscar libros, libreria online, libuku';
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
