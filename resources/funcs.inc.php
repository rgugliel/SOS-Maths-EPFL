<?php
if (!defined('__FUNCS_INC_PHP__'))
{
	define('__FUNCS_INC_PHP__', 1);
	
	/*
			displayErrorBox()
			Affiche la boîte de message d'erreur
			Parametres:
				$errorCode (string): code du message d'erreur
			Retour:
				contenu (string)
	*/
	function displayErrorBox($errorCode, $logFile = '', $link = '')
	{
		include 'resources/definedTexts.inc.php';
	
		global $tpl;
		$tpl->set_filenames(array('error' => 'error.tpl'));
		
		$log = '';
		if (!empty($logFile) && is_file($logFile))
			$log = '<br /><br />Le fichier d\'erreur vous donnera peut être plus d\'informations: <a href="' . $logFile . '" class="none">accéder a ' . basename($logFile) . '</a>';
	
		if (isset($errorCode) && isset($definedTexts[ $errorCode ]))
			$tpl->assign_var('ERROR_MSG', $definedTexts[ $errorCode ] . (!empty($link) ? '<br /><br />' . $link : '') . $log);
		else
			$tpl->assign_var('ERROR_MSG', $definedTexts['DEFAULT']);
	
		$tpl->assign_var_from_handle('MAIN_CONTENT', 'error');
	}
	
	/*
			displayConfirmBox()
			Affiche une demande de confirmation
			Parametres:
				$data (array): infos
			Retour:
				contenu (string)
	*/
	function displayConfirmBox($data)
	{
		global $tpl;
		$tpl->set_filenames(array('confirm' => 'confirm_form.tpl'));
	
		$tpl->assign_var('PAGE_TITLE', $data['PAGE_TITLE']);
		$tpl->assign_var('CONFIRMATION_TEXT', $data['CONFIRMATION_TEXT']);
		$tpl->assign_var('LINK_OUI', $data['LINK_OUI']);
		$tpl->assign_var('LINK_NON', $data['LINK_NON']);
	
		return $tpl->assign_var_from_handle('MAIN_CONTENT', 'confirm');
	}
	
	/*
			displaySuccessBox()
			Affiche la boîte de message d'erreur
			Parametres:
				$errorCode (string): code du message d'erreur
			Retour:
				contenu (string)
	*/
	function displaySuccessBox($errorCode, $supp = '')
	{
		include 'resources/definedTexts.inc.php';
	
		global $tpl;
		$tpl->set_filenames(array('success' => 'success.tpl'));
	
		if (isset($definedTexts[ $errorCode ]))
			$tpl->assign_var('ERROR_MSG', $definedTexts[ $errorCode ] . $supp);
		else
			$tpl->assign_var('ERROR_MSG', $definedTexts['DEFAULT_SUCCESS']);
	
		$tpl->assign_var_from_handle('MAIN_CONTENT', 'success');
	}
	
	/*
			dateValid()
			Une date est-elle valide?
			Parmetres:
				$data (array): [0] -> jour, [1] -> mois, [2] -> année
			Retour:
				bool
	*/
	function dateValid($date)
	{
		$daysMonths = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		
		if ($date[2] < 2000)
			$date[2] += 2000;
			
		if ($date < 0)
			return false ;
			
		$daysMonths[1] = isBiss($date[2]) ? 29 : 28;
		
		if ($date[0] < 1 || $date[1] < 1 || $date[1] > 12 || $date[0] > $daysMonths[ $date[1] - 1])
			return false ;
			
		return true ;
	}
	
	/*
	 * @params:
	 * 	$mail: nom de l'email (pour le template à utiliser)
	 *  $subject:
	 * 	$vars: [ nom de la variable => valeurs ], variables du template
	 * 	$blocs: [ nom du bloc, [ nom de la variable => valeurs ] ] 
	 * 	$dest:
	 * */
	function mailSend($mail, $subject, $vars, $blocs, $dest)
	{
		global $page;
		
		$baseDir = isset($GLOBALS['relativePath']) ? $GLOBALS['relativePath'] : '';
	
		if (empty($dest))
			return false;
		
		include $baseDir . 'resources/conf.inc.php';
		include $baseDir . 'classes/class.phpmailer.php';
		
		$template = new Template($baseDir . 'templates/mails/');
		
		// ---------------------------------------------
		// mail
		// ---------------------------------------------
		$email = new phpmailer();
		$email->SetFrom($page['config']['mailFrom']);
		$email->Subject = $subject;
		$email->IsHTML();
		$email->AddAddress($dest);
		
		// ---------------------------------------------
		// smtp
		// ---------------------------------------------
		if (isset($page['config']['mailSMTP']) && $page['config']['mailSMTP'])
		{
			$email->IsSMTP();
			
			if (!empty($page['config']['mailSMTPUser']))
			{
				$email->SMTPAuth = true;
				$email->Username = $page['config']['mailSMTPUser'];
				$email->Password = $page['config']['mailSMTPPsw'];
			}
											
			$email->Host = $page['config']['mailSMTPHost'];
			$email->Port = $page['config']['mailSMTPPort'];
		}

		// ----------------------------------------
		// NON HTML
		// ----------------------------------------
		if (is_file($baseDir . 'templates/mails/' . $mail . '_text.tpl'))
		{
			$template->set_filenames(array('emailText' => $mail . '_text.tpl'));
		
			foreach($vars as $key => $value)
				$template->assign_var($key, $value);
			
			$max = count($blocs);
			for ($i = 0; $i < $max; $i++)
				$template->assign_block_vars($blocs[$i][0], $blocs[$i][1]);

			ob_start();
			$template->pparse('emailText');
			$email->AltBody = ob_get_contents();
			ob_clean();
			
			$template->destroy();
		}
		
		// ----------------------------------------
		// HTML
		// ----------------------------------------
		$template->set_filenames(array('email' => $mail . '.tpl'));
		foreach($vars as $key => $value)
			$template->assign_var($key, $value);
		
		$max = count($blocs);
		for ($i = 0; $i < $max; $i++)
			$template->assign_block_vars($blocs[$i][0], $blocs[$i][1]);

		ob_start();
		$template->pparse('email');
		$email->MsgHTML(ob_get_contents());
		ob_end_clean();

		return (@$email->Send() ? true : false);
	}
}
?>
