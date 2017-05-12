<?php
if (!defined('IN_SITE'))
	die('Vous ne pouvez accéder a ce fichier directement');
	
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
	$tpl->set_filenames(array('faq' => 'faq.tpl'));
	
	$tpl->assign_var_from_handle('MAIN_CONTENT', 'faq');
}
?>
