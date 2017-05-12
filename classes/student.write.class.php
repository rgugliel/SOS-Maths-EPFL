<?php
if (!defined('IN_SITE'))
	die('Vous ne pouvez accéder a ce fichier directement.');

class studentWrite
{
	// id du répétiteur à qui écrire
	public $id = 0;

	// code de l'erreur
	public $error = NULL;
	public $fieldsError = array();

	// données membre
	public $data = NULL;
	
	public function __construct($id)
	{
		global $pdo;
		
		$q = $pdo->prepare('SELECT forename, name, email FROM users WHERE id=?');
		if (!$q->execute(array($id)))
		{
			$this->error = 'GET_DATA';
			return false;
		}
		
		$ret = $q->fetchAll(PDO::FETCH_ASSOC);
		
		if (!count($ret))
		{
			$this->error = 'REPETITEUR_NEXISTS';
			return false;
		}
		
		$this->data = $ret[0];
		$this->id = $id;
	}
	
	public function setDefaultValues()
	{
		$this->data['emailText'] = '';
		$this->data['emailContact'] = '';
		$this->data['telContact'] = '';
		$this->data['conditions'] = false;
	}
	
	public function getData()
	{
		global $page;
		
		$this->data['emailText'] = isset($_POST['emailText']) ? trim($_POST['emailText']) : '';
		$this->data['conditions'] = isset($_POST['conditions']) && $_POST['conditions'] ? true : false;
		$texts = array('emailContact', 'telContact');
		foreach($texts as $key => $value)
			$this->data[ $value ] = isset($_POST[ $value ]) ? trim($_POST[ $value ]) : '';
			
		if ($this->id && !$this->data['conditions'])
		{
			$this->error = 'REPETITEUR_CONDITIONS';
			return false;
		}
		
		if (empty($this->data['emailText'] )) 
		{
			$this->error = 'EMAIL_TEXT_EMPTY';
			return false;
		}
		
		require_once 'resources/securimage/securimage.php';
		$securimage = new Securimage();
		
		if (!isset($_POST['captcha_code']) || $securimage->check($_POST['captcha_code']) == false)
		{
			$this->error = 'REPETITEUR_RECAPTCHA';
			return false;
		}
		
		return true;
	}
}
?>
