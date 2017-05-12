<?php
if (!defined('IN_SITE'))
	die('Vous ne pouvez accéder a ce fichier directement');
	
function main()
{
	global $tpl;
	
	$tpl->set_filenames(array('acc' => 'accueil.tpl'));
 	
	$tpl->assign_var_from_handle('MAIN_CONTENT', 'acc');
}

main();
?>
