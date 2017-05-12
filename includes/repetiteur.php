<?php
if (!defined('IN_SITE'))
	die('Vous ne pouvez accéder a ce fichier directement');
	
include 'classes/repetiteur.class.php';
include 'classes/repetiteur.term.class.php';	

main();

function main()
{
	$dos = array('main', 'register', 'uptPsw', 'term', 'password', 'uptProfil', 'activate');
	$do = isset($_GET['do']) ? $_GET['do'] : 'main';
	
	if (!in_array($do, $dos)) 
		$do = 'main';
	$do .= 'Main';
	
	return $do();
}

function mainMain()
{
	global $tpl;
	include 'resources/definedTexts.inc.php';
	
	if (isset($_SESSION['m_id']))
		return homepageMain();
	
	$tpl->set_filenames(array('repetiteur' => 'repetiteur_main.tpl'));
	
	$error = isset($_GET['error']) ? $_GET['error'] : null;

	if (isset($error))
	{
		if (isset($definedTexts[ $error ]))
			$tpl->assign_var('ERROR', $definedTexts[ $error ] . '<br /><br />');
		else
			$tpl->assign_var('ERROR', $definedTexts['DEFAULT'] . '<br /><br />');
	}
	else
		$tpl->assign_var('ERROR', '');
	
	$tpl->assign_var_from_handle('MAIN_CONTENT', 'repetiteur');
}

function activateMain()
{
	global $pdo;
	
	$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	$regKey = isset($_GET['key']) ? trim($_GET['key']) : '';
	
	$q = $pdo->prepare('SELECT regKey FROM users_activation WHERE id=?');
	if (!$q->execute(array($id)))
		return displayErrorBox('GET_DATA');
	$ret = $q->fetchAll(PDO::FETCH_NUM);
	
	if (!count($ret))
	{
		$q = $pdo->prepare('SELECT active FROM users WHERE id=?');
		if (!$q->execute(array($id)))
			return displayErrorBox('GET_DATA');
			
		$ret = $q->fetchAll(PDO::FETCH_NUM);
		if (!count($ret))
			return displayErrorBox('REPETITEUR_NEXISTS');

		if ($ret[0][0])
			return displaySuccessBox('REPETITEUR_ALREADY_ACTIVATED');
			
		return displayErrorBox('GET_DATA');
	}
	
	if ($ret[0][0] != $regKey)
		return displayErrorBox('REPETITEUR_REGKEY_BAD');
		
	if (!$pdo->beginTransaction())
		return displayErrorBox('REPETITEUR_ACTIVATION');
		
	$q = $pdo->prepare('UPDATE users SET active=1 WHERE id=?');
	if (!$q->execute(array($id)))
		return displayErrorBox('REPETITEUR_ACTIVATION');
	
	$q = $pdo->prepare('DELETE FROM users_activation WHERE id=?');
	if (!$q->execute(array($id)))
	{
		$pdo->rollBack();
		return displayErrorBox('REPETITEUR_ACTIVATION');
	}
		
	if (!$pdo->commit())
		return displayErrorBox('REPETITEUR_ACTIVATION');
		
	global $tpl;
	$tpl->assign_var('MAIN_CONTENT', '<br />Votre profil a été activé avec succès.<br />Vous pouvez maintenant vous <a href="index.php?p=rep">connecter</a>.<br />');
}

function homepageMain()
{
	global $tpl, $pdo;
	
	if (!isset($_SESSION['m_id']))
		return mainMain();
		
	$rep = new repetiteurTerm('null', 0, '');
	
	$tpl->set_filenames(array('repetiteur' => 'repetiteur_homepage.tpl'));
	
	// -----------------------------------------
	// semestre courant et prochain semestre
	$termDates = $rep->computeTerms();
	
	$q = $pdo->prepare('SELECT CONCAT(annee, term) FROM repetiteur_term WHERE userId=? AND annee IN (?,?) AND term IN (?,?)');
	if (!$q->execute(array($_SESSION['m_id'], $termDates[0], $termDates[2], $termDates[1], $termDates[3])))
		return displayErrorBox('GET_DATA');
	
	$ret = $q->fetchAll(PDO::FETCH_NUM);
	
	$terms = array();
	foreach($ret as $row)
		$terms[ $row[0] ] = 1;
	
	$tpl->assign_var('termCurLib', $termDates[0] . ' - ' . ($termDates[0] + 1) . ': ' . ($termDates[1] == 'a' ? 'automne (août - janvier)' : 'printemps (février - juillet)'));
	$tpl->assign_var('termNextLib', $termDates[2] . ' - ' . ($termDates[2] + 1) . ': ' . ($termDates[3] == 'a' ? 'automne (août - janvier)' : 'printemps (février - juillet)'));
	
	$tpl->assign_var('termCur', $termDates[0] . $termDates[1]);
	$tpl->assign_var('termNext', $termDates[2] . $termDates[3]);
	
	$tpl->assign_var('termCurAction', isset($terms[ $termDates[0] . $termDates[1] ]) ? 'Modifier' : 'Créer');
	$tpl->assign_var('termNextAction', isset($terms[ $termDates[2] . $termDates[3] ]) ? 'Modifier' : 'Créer');
	
	$tpl->assign_var_from_handle('MAIN_CONTENT', 'repetiteur');
}

function registerMain()
{
	if (isset($_SESSION['m_id']))
		return mainMain();
	
	global $tpl;
	$rep = new repetiteur('add', 0);

	if (!isset($_POST['submit']))
	{
		$rep->setDefaultValues();
		return registerUptMain_displayForm($rep);
	}
	
	if (!$rep->verifyData())
		return registerUptMain_displayForm($rep);
	
	if (!$rep->addDB())
		return registerUptMain_displayForm($rep);
	
	$tpl->assign_var('MAIN_CONTENT', '<br />Votre profil a été créé avec succès! Un email vous a été envoyé vous permettant de finaliser l\'inscription.<br />');
}

function uptProfilMain()
{
	if (!isset($_SESSION['m_id']) || !$_SESSION['m_id'])
		return mainMain();
	
	global $tpl;
	$rep = new repetiteur('upt', $_SESSION['m_id']);
	
	if (!isset($_POST['submit']))
	{
		if (!$rep->getDBValues())
			return displayErrorBox($rep->error);
		
		return registerUptMain_displayForm($rep);
	}
	if (!$rep->verifyData())
		return registerUptMain_displayForm($rep);
	
	if (!$rep->uptDB())
		return registerUptMain_displayForm($rep);
	
	$tpl->assign_var('MAIN_CONTENT', '<br />Votre profil a été modifié avec succès!<br />');
}

function registerUptMain_displayForm($rep)
{
	include 'resources/definedTexts.inc.php';
	
	global $tpl, $globalVariables, $page;
	$tpl->set_filenames(array('form' => $rep->do == 'add' ? 'repetiteur_register.tpl' : 'repetiteur_uptProfil.tpl'));

	if ($rep->do == 'add')
	{
		require_once 'resources/securimage/securimage.php';
		$options = array('input_text' => 'Recopiez les mots ci-dessus');

		if (!empty($_SESSION['ctform']['captcha_error'])) {
			// error html to show in captcha output
			$options['error_html'] = 'TODO';
		}
		$tpl->assign_var('CAPTCHA', Securimage::getCaptchaHtml($options));
		
		$tpl->assign_var('conditionsChecked', $rep->data['conditions'] ? 'checked="checked"' : '');
	}

	// en cas d'erreur
	if (isset($rep->error))
	{
		if (isset($definedTexts[ $rep->error ]))
			$tpl->assign_var('ERROR', $definedTexts[ $rep->error ] . '<br /><br />');
		else
			$tpl->assign_var('ERROR', $definedTexts['DEFAULT'] . '<br /><br />');
	}
	else
		$tpl->assign_var('ERROR', '');
	
	$texts = array('pseudo', 'name', 'forename', 'email');
	if ($rep-> do == 'add')
		$texts = array_merge($texts, array('password1', 'password2'));
	
	foreach($texts as $key => $value)
		$tpl->assign_var($value, htmlentities($rep->data[ $value ], ENT_QUOTES, 'ISO-8859-15'));
	
	$tpl->assign_block_vars('sectionOpt', array('value' => 0, 'selected' => $rep->data['section'] == 0 ? 'selected="selected"' : '', 'caption' => 'Choisissez la section'));
	foreach($globalVariables['section'] as $key => $data)
		$tpl->assign_block_vars('sectionOpt', array('value' => $key, 'selected' => ($rep->data['section'] === $key ? 'selected="selected"' : ''), 'caption' => $data[1]));
		
	$tpl->assign_block_vars('levelStudiesOpt', array('value' => -1, 'selected' => $rep->data['levelStudies'] == -1 ? 'selected="selected"' : '', 'caption' => 'Choisissez votre année'));
	foreach($globalVariables['levelStudies'] as $key => $data)
		$tpl->assign_block_vars('levelStudiesOpt', array('value' => $key, 'selected' => $rep->data['levelStudies'] == $key ? 'selected="selected"' : '', 'caption' => $data[1]));
		
	foreach($rep->fieldsError as $key => $value)
		$tpl->assign_var($value . 'Class', 'error');

	$tpl->assign_var_from_handle('MAIN_CONTENT', 'form');
}

function uptPswMain()
{
	if (!isset($_SESSION['m_id']))
		return mainMain();
	
	global $tpl;
	$rep = new repetiteur('upt', $_SESSION['m_id']);
	
	if (!isset($_POST['submit'])) 
		return uptPswMain_displayForm($rep);
	else 
	{
		if (!$rep->verifyData_uptPsw())
			return uptPswMain_displayForm($rep);
		
		if (!$rep->uptPswDB())
			return uptPswMain_displayForm($rep);
		
		$tpl->assign_var('MAIN_CONTENT', '<br />Votre mot de passe a été modifié avec succès.<br />');
	}
}

function uptPswMain_displayForm($rep)
{
	include 'resources/definedTexts.inc.php';

	global $tpl, $globalVariables;
	$tpl->set_filenames(array('form' => 'repetiteur_uptPassword.tpl'));

	// en cas d'erreur
	if (isset($rep->error))
	{
		if (isset($definedTexts[ $rep->error ]))
			$tpl->assign_var('ERROR', $definedTexts[ $rep->error ] . '<br /><br />');
		else
			$tpl->assign_var('ERROR', $definedTexts['DEFAULT'] . '<br /><br />');
	}
	else
		$tpl->assign_var('ERROR', '');
	
	foreach($rep->fieldsError as $key => $value)
		$tpl->assign_var($value . 'Class', 'error');
	
	$tpl->assign_var_from_handle('MAIN_CONTENT', 'form');
}

function termMain()
{
	if (!isset($_SESSION['m_id']))
		return mainMain();
	
	global $tpl;
	
	$term = isset($_GET['term']) ? trim($_GET['term']) : '';
	$rep = new repetiteurTerm('', $term);
		
	if (isset($rep->error)) 
		return displayErrorBox($rep->error);
		
	if (!isset($_POST['submit'])) 
	{
		$rep->setDefaultValues();
		if (isset($rep->error)) 
			return displayErrorBox($rep->error);
		
		return termMain_displayForm($rep);
	}
	
	if (!$rep->verifyData())
		return termMain_displayForm($rep);
		
	if (!$rep->doDB())
		return termMain_displayForm($rep);
		
	if ($rep->do == 'add')
		$tpl->assign_var('MAIN_CONTENT', '<br />Inscription semestrielle réalisée avec succès.<br /><br />Retourner sur <a href="index.php?p=rep">sur la page personnelle</a><br />');
	else if ($rep->do == 'upt')
		$tpl->assign_var('MAIN_CONTENT', '<br />Inscription semestrielle mise à jour avec succès.<br /><br />Retourner sur <a href="index.php?p=rep">sur la page personnelle</a><br />');
}

function termMain_displayForm($rep)
{
	include 'resources/definedTexts.inc.php';

	global $tpl, $globalVariables, $page;
	
	$page['javascript'] = '<script src="resources/js/term_form.js" type="text/javascript"></script>';
	$tpl->set_filenames(array('form' => 'repetiteur_term.tpl'));

	// en cas d'erreur
	if (isset($rep->error))
	{
		if (isset($definedTexts[ $rep->error ]))
			$tpl->assign_var('ERROR', $definedTexts[ $rep->error ] . '<br /><br />');
		else
			$tpl->assign_var('ERROR', $definedTexts['DEFAULT'] . '<br /><br />');
	}
	else
		$tpl->assign_var('ERROR', '');
	
	$tpl->assign_var('submitLib', $rep->do == 'add' ? "S'inscrire" : 'Modifier');
	$tpl->assign_var('termLib', $rep->term[0] . ' - ' . ($rep->term[0] + 1) . ': ' . ($rep->term[1] == 'a' ? 'automne (août - janvier)' : 'printemps (février - juillet)'));
	$tpl->assign_var('term', $rep->term[0] . $rep->term[1]);
	
	foreach($rep->fieldsError as $key => $value)
		$tpl->assign_var($value . 'Class', 'error');
	
	// -------------------------------------------------
	// semestre d'études
	$tpl->assign_block_vars('levelStudiesOpt', array('value' => 0, 'selected' => $rep->data['levelStudies'] == -1 ? 'selected="selected"' : '', 'caption' => 'Choisissez votre année'));
	
	foreach($globalVariables['levelStudies'] as $key => $data)
		$tpl->assign_block_vars('levelStudiesOpt', array('value' => $key, 'selected' => $rep->data['levelStudies'] == $key ? 'selected="selected"' : '', 'caption' => $data[1]));
	
	// -------------------------------------------------
	// fees
	foreach($globalVariables['fee'] as $text => $data) 
		$tpl->assign_block_vars('feeOpt', array('value' => $data[0] . '_' . $data[1], 'checked' => (in_array($data[0] . '_' . $data[1], $rep->data['fee']) ? 'checked="checked"' : ''), 'caption' => htmlentities($text, ENT_QUOTES, 'ISO-8859-15')));

	
	// -------------------------------------------------
	// checkboxs
	$checkboxs = array('place' => 'place', 'language' => 'language', 'teachingsubject1' => 'teachingSubjects', 'teachingsubject2' => 'teachingSubjects');
	foreach($checkboxs as $globalVar => $selKey)
	{
		$blocName = $globalVar . 'Opt';
			
		foreach($globalVariables[ $globalVar ] as $key => $data)
		{
			$tpl->assign_block_vars($blocName, array('value' => $key, 'checked' => in_array($key, $rep->data[ $selKey ], true) ? 'checked="checked"' : '', 'caption' => $data));
		}
	}

	for ($i = 0; $i <= 2; $i++)
	{
		if (in_array($i, $rep->data['teachingLevel']))
			$tpl->assign_var('teachingLevel-' . $i . 'Checked', 'checked="checked"');
	}

	$tpl->assign_var('availableChecked', $rep->data['available'] ? 'checked="checked"' : '');
	
	// -------------------------------------------------
	// texts
	$texts = array('placeComment', 'comment');
	foreach($texts as $key => $value)
		$tpl->assign_var($value, htmlentities($rep->data[ $value ], ENT_QUOTES, 'ISO-8859-15'));
	
	// -------------------------------------------------
	// disponibilités
	$no = 0;
	foreach($globalVariables['day'] as $key => $value)
	{
		$tpl->assign_block_vars('availabilityDay', array('dayLibelle' => $value[1], 
		'dayIndex' => $value[0], 
		'tdClass' => $no,
		'amChecked' => in_array($value[0] . '-am', $rep->data['availibilityDay']) ? 'checked="checked"' : '',
		'mChecked' => in_array($value[0] . '-m', $rep->data['availibilityDay']) ? 'checked="checked"' : '',
		'pmChecked' => in_array($value[0] . '-pm', $rep->data['availibilityDay']) ? 'checked="checked"' : '',
		'eChecked' => in_array($value[0] . '-e', $rep->data['availibilityDay']) ? 'checked="checked"' : ''));
		
		$no = $no ? 0 : 1;
	}
	
	$tpl->assign_var_from_handle('MAIN_CONTENT', 'form');
}

function passwordMain()
{
	global $tpl;
	
	$rep = new repetiteur('password', 0);
	
	if (isset($_SESSION['m_id']))
		return mainMain();
	
	if (!isset($_POST['submit']))
		return passwordMain_displayForm($rep);
		
	if (!$rep->password_verifyData())
		return passwordMain_displayForm($rep);
	
	if (!$rep->password_resetDB())
		return passwordMain_displayForm($rep);
	
	$tpl->assign_var('MAIN_CONTENT', '<br />Un nouveau mot de passe vous a été envoyé par email.<br />');
}

function passwordMain_displayForm($rep)
{
	include 'resources/definedTexts.inc.php';
	require 'resources/recaptcha/recaptchalib.php';
	
	global $tpl, $globalVariables, $page;
	$tpl->set_filenames(array('form' => 'repetiteur_password.tpl'));

	// en cas d'erreur
	if (isset($rep->error))
	{
		if (isset($definedTexts[ $rep->error ]))
			$tpl->assign_var('ERROR', $definedTexts[ $rep->error ] . '<br /><br />');
		else
			$tpl->assign_var('ERROR', $definedTexts['DEFAULT'] . '<br /><br />');
	}
	else
		$tpl->assign_var('ERROR', '');
	
	require_once 'resources/securimage/securimage.php';
	$options = array('input_text' => 'Recopiez les mots ci-dessus');

	if (!empty($_SESSION['ctform']['captcha_error'])) {
		// error html to show in captcha output
		$options['error_html'] = 'TODO';
	}
	$tpl->assign_var('CAPTCHA', Securimage::getCaptchaHtml($options));
	
	$texts = array('pseudo', 'email');
	foreach($texts as $key => $value)
		$tpl->assign_var($value, htmlentities($rep->data[ $value ], ENT_QUOTES, 'ISO-8859-15'));

	$tpl->assign_var_from_handle('MAIN_CONTENT', 'form');
}
?>
