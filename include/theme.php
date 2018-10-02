<?
// The source code packaged with this file is Free Software, Copyright (C) 2008 by
// Alejandro Moreno Calvo < almorca@gmail.com >
// It's licensed under the GNU AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.fsf.org/licensing/licenses/agpl-3.0.html
// GNU AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

/**
	Print the html of header
	@param	$meta	Array with metadata for the head
*/
function do_header($title, $id_body = NULL, $meta = NULL, $rdfa = array()) {
     global $globals;
	 $default_meta = array('description' => $globals['description'],
		'DC.title' => $globals['slogan'],
		'keywords' => $globals['keywords'],
		'DC.creator' => $globals['author'],
		'DC.subject' =>	"Libros");

	 if (! $meta) { // default metadata
		$meta = $default_meta;
	 } else if (is_array($meta)) { // some metadata defined
		foreach($default_meta as $var => $value) {
			if (! isset($meta[$var]) ) {
				$meta[$var] = $default_meta[$var];
			}
		}
	 }

     header('Content-Type: text/html; charset=utf-8');

     $vars = compact('title', 'id_body', 'meta', 'rdfa');
     return Haanga::Load('header.html', $vars);
}

/**
	Print the html of header
*/
function do_footer() {
     global $globals;

     return Haanga::Load('footer.html');
}