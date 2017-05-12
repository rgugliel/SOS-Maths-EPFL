<?php
//session_name( 'sosmaths' );
session_start( ); // ca, c'est fait

if( isset( $_POST['login'] ) )
	login( );
else
	logoff( );
	
$page = array( );



/*
	login( )
	Login - Vérification des données et création des variables de session
	Parametres:
		void
	Retour:
		void (header: location)
*/
function login( )
{
	include 'resources/conf.inc.php';
	
	try 
	{
		$pdo = new PDO( 'pgsql:host=' . $config['db_host'] . ';dbname=' . $config['db_db'], $config['db_user'], $config['db_psw'] );
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) 
	{
		header( 'location: index.php?p=acc&err=DB_CONNECT' );
		exit ;
	}

	// récupération des données
	$pseudo = isset( $_POST['pseudo'] ) ? $_POST['pseudo'] : '';
	$password = isset( $_POST['password'] ) ? sha1( $_POST['password'] ) : '';
	
	$q = $pdo->prepare( 'SELECT id, pseudo, active, forename, levelweb, password, passwordsalt FROM users WHERE pseudo=?' );
	$q->bindValue( 1, $pseudo, PDO::PARAM_STR );
	if( !$q->execute( ) )
	{
		header( 'location: index.php?p=rep&error=GET_DATA' );
		exit ;
	}
	
	$ret = $q->fetchAll( PDO::FETCH_ASSOC );
	
	// user existe pas
	if( !count( $ret ) )
	{
		header( 'location: index.php?p=rep&error=REPETITEUR_NEXISTS' );
		exit ;
	}

	$row = $ret[0];

	if( $row['password'] != sha1( $row['passwordsalt'] . $password ) )
	{
		header( 'location: index.php?p=rep&error=REPETITEUR_PASSWORD' );
		exit ;
	}
	
	if( !$row['active'] )
	{
		header( 'location: index.php?p=rep&error=REPETITEUR_ACTIVE' );
		exit ;
	}
	
	$q = $pdo->prepare( 'UPDATE users SET timeLastConnection=cast(extract(epoch from current_timestamp) as integer) WHERE id=?' );
	$q->bindValue( 1, $row['id'], PDO::PARAM_INT );
	$q->execute( );
	
	$_SESSION['m_id'] = $row['id'];
	$_SESSION['m_pseudo'] = $row['pseudo'];
	$_SESSION['m_level'] = $row['level'];
	$_SESSION['m_forename'] = utf8_decode( $row['forename'] );

	if( isset( $_POST['query'] ) && !empty( $_POST['query'] ) )
		header( 'location: index.php?' . $_POST['query'] );
	else
		header( 'location: index.php?p=rep' );
		
	exit;
}


/*
	logoff( )
	Logoff - Destruction des variables de sessions
	Parametres:
		void
	Retour:
		void (header: location)
*/
function logoff( )
{
	$_SESSION = array( );

	header( 'location: index.php' );
	exit;	
}
?>
