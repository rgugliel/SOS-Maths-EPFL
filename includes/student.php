<?php
if (!defined('IN_SITE'))
	die('Vous ne pouvez accéder a ce fichier directement');

main();

function main()
{
	$dos = array('main', 'write');
	$do = isset($_GET['do']) ? $_GET['do'] : 'main';
	
	if (!in_array($do, $dos)) 
		$do = 'main';
	$do .= 'Main';
	
	return $do();
}

function writeMain()
{
	include 'classes/student.write.class.php'; 
	
	$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	
	$stu = new studentWrite($id);
	if (isset($stu->error)) 
		return displayErrorBox($stu->error);
	
	if (!isset($_POST['submit']))
	{
		$stu->setDefaultValues();
		return writeDisplayForm($stu);
	}
	
	if (!$stu->getData())
		return writeDisplayForm($stu);
	
	// ----------------------------------------------
	// envoi de l'email
	$email = str_replace('<script', '', $stu->data['emailText']);
	
	if (!mailSend('student_write' , 'SOS-Maths - Contact d\'un étudiant', array('EMAIL_CONTENT_HTML' => nl2br(str_replace('<', '&lt;', $email)), 
			'EMAIL' => str_replace('<', '&lt;',  $stu->data['emailContact']),
			'TEL' => str_replace('<', '&lt;',  $stu->data['telContact']),
			'EMAIL_CONTENT' => $email), array(), $stu->data['email']))
	{
		$stu->error = 'STUDENT_EMAIL_SEND';
		return writeDisplayForm($stu);
	}
	
	global $tpl;
	$tpl->assign_var('MAIN_CONTENT', '<br />Email envoyé avec succès.<br />');
}

function writeDisplayForm($stu)
{
	global $tpl, $page;
	
	require 'resources/recaptcha/recaptchalib.php';
	include 'resources/definedTexts.inc.php';
	
	$tpl->set_filenames(array('student' => 'student_write.tpl'));
	
	if (isset($stu->error))
	{
		if (isset($definedTexts[ $stu->error ]))
			$tpl->assign_var('ERROR', $definedTexts[ $stu->error ] . '<br /><br />');
		else
			$tpl->assign_var('ERROR', $definedTexts['DEFAULT'] . '<br /><br />');
	}
	else
		$tpl->assign_var('ERROR', '');
	
	$tpl->assign_var('conditionsChecked', $stu->data['conditions'] ? 'checked="checked"' : '');
	$tpl->assign_var('conditionsDisplay', $stu->id ? 'block' : 'none');
	$tpl->assign_var('EMAIL', htmlentities($stu->data['emailText'], ENT_QUOTES));
	$tpl->assign_var('ID', $stu->id);
	$tpl->assign_var('NAME', utf8_decode($stu->data['forename'] . ' ' . $stu->data['name']));
	
	$texts = array('emailContact', 'telContact');
	foreach($texts as $key => $value)
		$tpl->assign_var($value, htmlentities($stu->data[ $value ], ENT_QUOTES, 'ISO-8859-15'));
	
	require_once 'resources/securimage/securimage.php';
	$options = array('input_text' => 'Recopiez les mots ci-dessus');

	if (!empty($_SESSION['ctform']['captcha_error'])) {
		// error html to show in captcha output
		$options['error_html'] = 'TODO';
	}
	$tpl->assign_var('CAPTCHA', Securimage::getCaptchaHtml($options));
	
	$tpl->assign_var('DEST', $stu->id ? 'le répétiteur' : 'SOS-Maths');
	
	$tpl->assign_var_from_handle('MAIN_CONTENT', 'student');
}

/*
 * Création de l'object pour la recherche, avec récupération des informations de base
 */
function mainSearch_createObject()
{
	if (isset($_GET['searchId']) && isset($_SESSION['studentSearchObjects'][ $_GET['searchId'] ]))
	{
		$obj = unserialize($_SESSION['studentSearchObjects'][ $_GET['searchId'] ]);
		if (is_object($obj) && is_a($obj, 'studentTerm'))
		{
			$obj->getGetValues();
			$_SESSION['studentSearchObjects'][ $obj->uniqId ] = serialize($obj);
			return $obj;
		}
	}
	
	$termText = isset($_POST['term']) ? trim($_POST['term']) : '';
	$stu = new studentTerm($termText);
	
	// ------------------------------------------
	// le formulaire a été submit
	$stu->getPostValues();
	$stu->getGetValues();
	
	$_SESSION['studentSearchObjects'][ $stu->uniqId ] = serialize($stu);
	
	return $stu;
}

function mainMain()
{
	include 'classes/student.term.class.php';
	include 'classes/repetiteur.term.class.php';
	
	global $pdo;
	
	if (!isset($_POST['submit']) && !isset($_GET['searchId']))
	{
		$termText = isset($_POST['term']) ? trim($_POST['term']) : '';
		$stu = new studentTerm($termText);
		$stu->setDefaultValues();
		return mainPreSearch($stu);
	}
	else
		$stu = mainSearch_createObject();
	
	$query = $stu->createGetQuery();

	if ($query === 0)
	{
		$stu->error = 'REPETITEUR_SEARCH_0';
		return mainPreSearch($stu);
	}
	
	$q = $pdo->prepare($query[0]);
	if (!$q->execute($query[1]))
		return displayErrorBox('GET_DATA');
		
	$ret = $q->fetchAll(PDO::FETCH_ASSOC);
	
	if (!count($ret))
	{
		$stu->error = 'REPETITEUR_SEARCH_0';
		return mainPreSearch($stu);
	}
	
	// ------------------------------------------
	// affichage des résultats
	global $tpl, $globalVariables, $page;
	
	$page['javascript'] = '<script src="resources/js/student_searchResults.js" type="text/javascript"></script>';
	
	$tpl->set_filenames(array('results' => 'student_searchResults.tpl'));
	
	$tpl->assign_var('SEARCH_ID', $stu->uniqId);
	$tpl->assign_var('YEAR', $stu->y);
	$tpl->assign_var('TERM', $stu->t);
	$tpl->assign_var('TERM_LIBELLE', $stu->y . ' - ' . ($stu->y + 1) . ': ' . ($stu->t == 'a' ? 'automne (août - janvier)' : 'printemps (février - juillet)'));
	
	$orders = array('name', 'level', 'fee', 'section');
	foreach($orders as $order)
	{
		$tpl->assign_var('SEARCH_ORDER_' . $order . '_UP', $stu->orderField == $order && $stu->orderDirection == 'ASC' ? 'On' : 'Off');
		$tpl->assign_var('SEARCH_ORDER_' . $order . '_DOWN', $stu->orderField == $order && $stu->orderDirection == 'DESC' ? 'On' : 'Off');
	}
	
	$no = 0;
	foreach($ret as $row)
	{
		$fees = '';
		if ($row['feemin'] == 0)
		{
			if ($row['feemax'] == 255)
				$fees = 'Discutable';
			else
				$fees = 'Jusqu\'à ' . $row['feemax'];
		}
		else if ($row['feemax'] == 255)
			$fees = 'Dès ' . $row['feemin'];
		else
			$fees = $row['feemin'] . ' - ' . $row['feemax'];
		
		$tpl->assign_block_vars('row', array('NAME' => utf8_decode($row['forename'] . ' ' . $row['name']),
			'ID' => $row['id'],
			'FEES' => $fees,
			'SECTION' => $globalVariables['section'][ $row['section'] ][0],
			'SECTION_F' => $globalVariables['section'][ $row['section'] ][1],
			'LEVEL' => $globalVariables['levelStudies'][ $row['levelstudies'] ][0],
			'LEVEL_F' => $globalVariables['levelStudies'][ $row['levelstudies'] ][1],
			'NO' => $no));
		
		$no = $no ? 0 : 1;
	}

	$tpl->assign_var_from_handle('MAIN_CONTENT', 'results');
}

function mainPreSearch($stu)
{
	global $tpl, $globalVariables, $page;
	
	include 'resources/definedTexts.inc.php';
	
	$page['javascript'] = '<script src="resources/js/student.js" type="text/javascript"></script><script src="resources/js/term_form.js" type="text/javascript"></script>';
	$tpl->set_filenames(array('repetiteur' => 'student_main.tpl'));
	
	if (isset($stu->error))
	{
		if (isset($definedTexts[ $stu->error ]))
			$tpl->assign_var('INFOS', $definedTexts[ $stu->error ] . '<br /><br />');
		else
			$tpl->assign_var('INFOS', $definedTexts['DEFAULT'] . '<br /><br />');
	}
	else
		$tpl->assign_var('INFOS', '');
	
	$termDates = repetiteurTerm::computeTerms();
	$tpl->assign_var('termInfo', $termDates[0] . ' - ' . ($termDates[0] + 1) . ': ' . ($termDates[1] == 'a' ? 'automne (août - janvier)' : 'printemps (février - juillet)'));
	
	$tpl->assign_block_vars('termOpt', array('value' => $termDates[0] . $termDates[1], 'selected' => ($termDates[0] == $stu->y && $termDates[1] == $stu->t  ? 'selected="selected"' : ''), 'caption' => $termDates[0] . ' - ' . ($termDates[0] + 1) . ': ' . ($termDates[1] == 'a' ? 'automne (août - janvier)' : 'printemps (février - juillet)')));
	$tpl->assign_block_vars('termOpt', array('value' => $termDates[2] . $termDates[3], 'selected' => ($termDates[2] == $stu->y && $termDates[3] == $stu->t  ? 'selected="selected"' : ''), 'caption' => $termDates[2] . ' - ' . ($termDates[2] + 1) . ': ' . ($termDates[3] == 'a' ? 'automne (août - janvier)' : 'printemps (février - juillet)')));
	
	// -------------------------------------------------
	// checkboxs
	$checkboxs = array('place' => 'place', 'language' => 'language', 'teachingsubject1' => 'teachingSubjects', 'teachingsubject2' => 'teachingSubjects');
	foreach($checkboxs as $globalVar => $selKey)
	{
		$blocName = $globalVar . 'Opt';
			
		foreach($globalVariables[ $globalVar ] as $key => $data)
		{
			$tpl->assign_block_vars($blocName, array('value' => $key, 'checked' => in_array($key, $stu->data[ $selKey ]) ? 'checked="checked"' : '', 'caption' => $data));
		}
	}

	for ($i = 0; $i < 3; $i++)
	{
		$tpl->assign_var('teachingLevel-' . $i . 'Checked', in_array($i, $stu->data['teachingSubjects']) ? 'checked="checked"' : '');
	}
	
	$checkboxs = array('section', 'levelStudies');
	foreach($checkboxs as $selKey)
	{
		$blocName = $selKey . 'Opt';
			
		foreach($globalVariables[ $selKey ] as $key => $data)
		{
			$tpl->assign_block_vars($blocName, array('value' => $key, 'checked' => in_array($key, $stu->data[ $selKey ]) ? 'checked="checked"' : '', 'caption' => $data[1]));
		}
	}
	
	$tpl->assign_var('languagesChecked', implode(', ', $stu->data['language']));
	
	// -------------------------------------------------
	// disponibilités
	$no = 0;
	//$availabilities = '';
	foreach($globalVariables['day'] as $key => $value)
	{
		$tpl->assign_block_vars('availabilityDay', array('dayLibelle' => $value[1], 
		'dayIndex' => $value[0], 
		'tdClass' => $no,
		'amChecked' => in_array($value[0] . '-am', $stu->data['availibilityDay']) ? 'checked="checked"' : '',
		'mChecked' => in_array($value[0] . '-m', $stu->data['availibilityDay']) ? 'checked="checked"' : '',
		'pmChecked' => in_array($value[0] . '-pm', $stu->data['availibilityDay']) ? 'checked="checked"' : '',
		'eChecked' => in_array($value[0] . '-e', $stu->data['availibilityDay']) ? 'checked="checked"' : ''));
		
		//$availabilities .= (empty($availabilities) ? '' : ', ') . '\'' . $value[0] . '-am' . '\', \'' . $value[0] . '-m' . '\', \'' . $value[0] . '-pm' . '\', \'' . $value[0] . '-e' . '\'';
		
		$no = $no ? 0 : 1;
	}
	$tpl->assign_var('availabilityCount', count($stu->data['availibilityDay']));
	
	$tpl->assign_var_from_handle('MAIN_CONTENT', 'repetiteur');
}
?>
