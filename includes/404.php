<?php
if (!defined('IN_SITE'))
	die('Vous ne pouvez acc�der a ce fichier directement');
	
main();

/*
		main()
		Fonction principale
		Parametres:
			void
		Retour:
			contenu (string)
*/
function main()
{
	global $tpl;
	$tpl->set_filenames(array('404' => '404.tpl'));
	
	$tpl->assign_var_from_handle('MAIN_CONTENT', '404');
}
?>
