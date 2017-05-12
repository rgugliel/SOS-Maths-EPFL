<?php
//session_name( 'sosmaths' );
session_start( ); // ca, c'est fait

if( !isset( $_SESSION['m_level'] ) )
	$_SESSION['m_level'] = 0;

define( 'IN_SITE', 1 ); // pour empÃªcher l'acces direct aux fichiers inclus

require 'resources/conf.inc.php'; // fichier de configuration
require 'resources/global.vars.inc.php';
require 'resources/funcs.inc.php';
require 'resources/templates.php'; // moteur de templates

$tpl = new Template( 'templates/' ); // chargement du template principal
$tpl->set_filenames( array( 'main' => 'index.tpl' ) );

$page['error'] = NULL;

// -----------------------------------------------------------						 
// PAGE EN COURS
$page['current'] = isset( $_GET['p'] ) ? $_GET['p'] : 'acc';

try 
{
    $pdo = new PDO( 'pgsql:host=' . $config['db_host'] . ';dbname=' . $config['db_db'], $config['db_user'], $config['db_psw'] );
}
catch(PDOException $e) 
{
    $page['error'] = 'DB_CONNECT';
}

$listePages = array( 
			'acc'		=>	array( 'displayItem' => true, 'file' => 'accueil.php', 'level' => 0, 'state' => 'Off', 'text' => 'Accueil' ),
			'cdt'		=>	array( 'displayItem' => false, 'file' => 'conditions.php', 'level' => 0, 'state' => 'Off', 'text' => 'Conditions' ),
			'stu' 		=>	array( 'displayItem' => true, 'file' => 'student.php', 'level' => 0, 'state' => 'Off', 'text' => 'Elève' ),	
			'rep' 		=>	array( 'displayItem' => true, 'file' => 'repetiteur.php', 'level' => 0, 'state' => 'Off', 'text' => 'Répétiteur' ),
			'faq'		=> array( 'displayItem' => true, 'file' => 'faq.php', 'level' => 0, 'state' => 'Off', 'text' => 'FAQ' ),
			'about'		=>	array( 'displayItem' => true, 'file' => 'about.php', 'level' => 0, 'state' => 'Off', 'text' => 'A propos' ),
			'offline'	=>	array( 'displayItem' => false, 'file' => 'offline.php', 'level' => 0, 'state' => 'Off', 'text' => '' ),
			'error'		=>	array( 'displayItem' => false, 'file' => 'error.php', 'level' => 0, 'state' => 'Off', 'text' => ''  ),
			'404'		=>	array( 'displayItem' => false, 'file' => '404.php', 'level' => 0, 'state' => 'Off', 'text' => '' )	
		);

if( !isset( $listePages[ $page['current'] ] ) ) // si la page qu'on aimerait afficher n'existe pas
	$page['current'] = '404'; // hop, 404

if( isset( $listePages[ $page['current'] ]['current'] ) )
	$listePages[ $listePages[ $page['current'] ]['current'] ]['state'] = 'On'; // pour changer l'image, :)
else
	$listePages[ $page['current'] ]['state'] = 'On'; // pour changer l'image, :)

if( $config['offline'] )
	$page['current'] = 'offline';
	
if( isset( $page['error'] ) )
	$page['current'] = 'error';
	
foreach( $listePages as $key => $value )
{
	if( $value['displayItem'] && $_SESSION['m_level'] >= $value['level'] ) // si on doit afficher l'onglet (onglet affichable et level OK)
	{
		$tpl->assign_block_vars( 'menuItem', array( 'libelle' => $value['text'], 'link' => $key, 'class' => ( $value['state'] == 'On' ? 'class="current_page_item"' : '' ) ) );
	}
}

// ---------------------------------
// page pour de vrai	
include( 'includes/' . $listePages[ $page['current'] ]['file'] ); 

$tpl->assign_var( 'javascript', isset( $page['javascript'] ) && !empty( $page['javascript'] ) ? $page['javascript'] : '' );
$tpl->assign_var( 'css', isset( $page['css'] ) && !empty( $page['css'] ) ? $page['css'] : '' );

// ----------------------------------
// boîte login ou logout
if( isset( $_SESSION['m_id'] ) )
{
	$tpl->set_filenames( array( 'loginoutbox' => 'logged.tpl') );
	$tpl->assign_var( 'M_FORENAME', $_SESSION['m_forename'] );
	$tpl->assign_var_from_handle( 'LOGINOUTBOX', 'loginoutbox' );
}


header('Content-Type: text/html; charset=iso-8859-1');

// Affichage
$tpl->pparse( 'main' );
?>
