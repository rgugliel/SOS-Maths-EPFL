<?php
if (!defined('IN_SITE'))
	die('Vous ne pouvez accéder a ce fichier directement');
	
main();

function main()
{
	include 'resources/definedTexts.inc.php';

	global $tpl;
	global $page;

	$tpl->set_filenames(array('error' => 'error.tpl'));

	$tpl->assign_var('ERROR_MSG', isset($definedTexts[ $page['error'] ]) ? $definedTexts[ $page['error'] ] : $definedTexts['DEFAULT'] );

	$tpl->assign_var_from_handle('MAIN_CONTENT', 'error');
}
?>
