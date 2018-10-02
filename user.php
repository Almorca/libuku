<?php
// The source code packaged with this file is Free Software, Copyright (C) 2008 by
// Alejandro Moreno Calvo < almorca@gmail.com >
// It's licensed under the GNU AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.fsf.org/licensing/licenses/agpl-3.0.html
// GNU AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

include('config.php');
include(lbkinclude.'class.user.php');

$op = 'default';

if ( isset($_POST['op']) ) {
    $op = trim($_POST['op']);
} elseif ( isset($_GET['op']) ) {
    $op = trim($_GET['op']);
}

if ($op == 'default') { // Show login form
	$lang = 'es';
	require('theme/'.$lang.'/login_form.php');
	exit();
}

if ($op == 'login') {
	
}

if ($op == 'logout') {
	
}

?>
