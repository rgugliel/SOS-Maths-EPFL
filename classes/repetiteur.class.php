<?php
if (!defined('IN_SITE'))
	die('Vous ne pouvez accéder a ce fichier directement.');

if (!isset($_SESSION['m_level']) || $_SESSION['m_level'] < $listePages[ $page['current'] ]['level'])
	die('Vous n\'avez pas les droits suffisants pour accéder a ce fichier. ');

class repetiteur
{
	// que faire? 'add', 'del', 'upt'
	public $do = NULL;

	// id de l'user a modifier/supprimer, 0 si ajout
	public $id = 0;

	// code de l'erreur
	public $error = NULL;
	public $fieldsError = array();

	// données membre
	public $data = NULL;

	/*
		public __construct()
		Constructeur, initialise les variables qui doivent l'être
		Parametres:
			- $do (string): que faire 'add', 'del', 'upt'
			- $id (unsigned int): id de l'user
	*/
	function __construct($do, $id)
	{
		$this->do = $do;
		$this->id = $id;
	}
	
	/*
			public setDefaultValues()
			Initialise les valeurs par défaut (pour ajout d'un membre, p ex)
			Parametres:
				void
			Retour:
				void
	*/
	public function setDefaultValues()
	{
		$this->data['pseudo'] = '';
		$this->data['name'] = '';
		$this->data['forename'] = '';
		$this->data['email'] = '';
		$this->data['password1'] = '';
		$this->data['password2'] = '';
		
		$this->data['conditions'] = false;
		$this->data['section'] = 0;
		$this->data['levelStudies'] = -1;
	}
	
	public function getDBValues()
	{
		global $pdo;
		
		if ($this->do != 'upt' || !$this->id)
		{
			$this->error = 'REPETITEUR_NEXISTS';
			return false;
		}
		
		$q = $pdo->prepare('SELECT pseudo, name, forename, email, section, levelstudies AS levelStudies FROM users WHERE id=?');
		if (!$q->execute(array($this->id)))
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
		$this->data['levelStudies'] = $this->data['levelstudies'];
		unset($this->data['levelstudies']);
		$this->data = array_map('utf8_decode', $this->data);
		return true;
	}

	/*
		public verifyData()
		Récupere et vérifie les données du formulaire
		Parametres:
			void
		Retour:
			bool (false en cas d'erreur)
	*/
	public function verifyData()
	{
		global $globalVariables, $page, $pdo;
		
		$texts = array('pseudo', 'name', 'forename', 'email', 'password1', 'password2');
		foreach($texts as $key => $value)
			$this->data[ $value ] = isset($_POST[ $value ]) ? trim($_POST[ $value ]) : '';
		
		$this->data['section'] = isset($_POST['section']) ? $_POST['section'] : 0;
		$this->data['levelStudies'] = isset($_POST['levelStudies']) ? (int)$_POST['levelStudies'] : -1;
		
		$this->data['conditions'] = isset($_POST['conditions']) && $_POST['conditions'] ? true : false;
		
		// ----------------------------------------------------------
		// vérifications
		$textsChecks = array('pseudo', 'name', 'forename', 'email');
		foreach($textsChecks as $key => $value)
		{
			if (empty($this->data[ $value ]))
			{
				$this->fieldsError[ ] = $value;
				$this->error = 'FIELD_EMPTY';
			}
		}
		
		if (!preg_match('/^([[:alnum:]]+)$/', $this->data['pseudo']))
		{
			$this->error = 'PSEUDO_BAD';
			$this->fieldsError[ ] = 'pseudo';
		}

		if (strlen($this->data['pseudo']) < 3 || strlen($this->data['pseudo']) > 16)
		{
			$this->error = 'PSEUDO_LENGTH';
			$this->fieldsError[ ] = 'pseudo';
		}
		
		if (strlen($this->data['name']) < 3 || strlen($this->data['name']) > 32)
		{
			$this->error = 'NAME_LENGTH';
			$this->fieldsError[ ] = 'name';
		}
		
		if ($this->data['forename'][0] == ' ' || ord($this->data['forename'][0]) < 65 || ord($this->data['forename'][0]) > 90)
		{
			$this->error = 'NAME_FORENAME_FIRSTLETTER';
			$this->fieldsError[ ] = 'forename';
		}
		
		if (strlen($this->data['forename']) < 3 || strlen($this->data['forename']) > 32)
		{
			$this->error = 'NAME_LENGTH';
			$this->fieldsError[ ] = 'forename';
		}
		
		if ($this->data['section'] === 0)
		{
			$this->fieldsError[ ] = 'section';
			$this->error = 'FIELD_EMPTY';
		}
		
		if (!array_key_exists($this->data['section'], $globalVariables['section']))
		{
			$this->fieldsError[ ] = 'section';
			$this->error = 'SECTION_BAD';
		}

		if ($this->data['levelStudies'] === -1)
		{
			$this->fieldsError[ ] = 'levelStudies';
			$this->error = 'FIELD_EMPTY';
		}
		
		if (!array_key_exists($this->data['levelStudies'], $globalVariables['levelStudies']))
		{
			$this->fieldsError[ ] = 'levelStudies';
			$this->error = 'LEVEL_STUDIES_BAD';
		}
		
		$strangeChars = array('name', 'forename', 'email');
		foreach($strangeChars as $key => $value)
		{
			if (preg_match_all('/([;<]+)/', $this->data[ $value ]) > 0)
			{
				$this->fieldsError[ ] = $value;
				$this->error = 'INPUT_STRANGE_CHARS';
			}
		}
		
		if (!empty($this->data['email']) && !preg_match('/^([[:alpha:]\-_.]+)@(a3.|alumni.){0,1}epfl.ch$/', $this->data['email']))
		{
			$this->error = 'EMAIL_BAD';
			$this->fieldsError[ ] = 'email';
		}
		
		if ($this->do == 'add' || !empty($this->data['password1']) || !empty($this->data['password2']))
		{
			if (strlen($this->data['password1']) < 6)
			{
				$this->fieldsError[ ] = 'password';
				$this->error = 'PASSWORD_LENGTH';
			}

			if (empty($this->data['password1']))
			{
				$this->fieldsError[ ] = 'password';
				$this->error = 'FIELD_EMPTY';
			}
	
			if ($this->data['password1'] != $this->data['password2'])
			{
				$this->fieldsError[ ] = 'password';
				$this->error = 'PASSWORDS_NEQUAL';
			}
		}
		
		if ($this->data['levelStudies'] == 0 && $this->data['section'] != 'cms')
		{
			$this->fieldsError[ ] = 'levelStudies';
			$this->fieldsError[ ] = 'section';
			$this->error = 'REPETITEUR_LVL_0_SECTION_CMS';
			return false;
		}

		if ($this->data['section'] == 'cms' && $this->data['levelStudies'] != 0 )
		{
			$this->fieldsError[ ] = 'levelStudies';
			$this->fieldsError[ ] = 'section';
			$this->error = 'REPETITEUR_SECTION_CMS_LEVEL_0';
			return false;
		}
		
		if ($this->do == 'add' && !$this->data['conditions'])
		{
			$this->fieldsError[ ] = 'conditions';
			$this->error = 'REPETITEUR_CONDITIONS';
			return false;
		}
		$q = $pdo->prepare('SELECT id FROM users WHERE pseudo=?');
		if (!$q->execute(array($this->data['pseudo'])))
		{
			$this->error = 'GET_DATA';
			return false;
		}
		
		$ret = $q->fetchAll(PDO::FETCH_NUM);
		
		if (count($ret)) 
		{
			if ($this->do == 'add' || $ret[0][0] != $this->id)
			{
				$this->error = 'PSEUDO_ALREADY_EXISTS';
				return false;
			}
		}
		
		$pdo->query('SET NAMES UTF8');
		if (($q = $pdo->prepare('SELECT id FROM users WHERE name=? AND forename=?')) === false)
		{
			$this->error = 'GET_DATA';
			return false;
		}
		if (!$q->execute(array(utf8_encode($this->data['name']), utf8_encode($this->data['forename']))))
		{
			$this->error = 'GET_DATA';
			return false;
		}
		$ret = $q->fetchAll(PDO::FETCH_NUM);
		
		if (count($ret)) 
		{
			if ($this->do == 'add' || $ret[0][0] != $this->id)
			{
				$this->error = 'NAME_FORENAME_ALREADY_EXISTS';
				return false;
			}
		}
		
		if ($this->do == 'add') // recaptcha
		{
			require_once 'resources/securimage/securimage.php';
			$securimage = new Securimage();
			
			if (!isset($_POST['captcha_code']) || $securimage->check($_POST['captcha_code']) == false)
			{
				$this->error = 'REPETITEUR_RECAPTCHA';
				$this->fieldsError[ ] = 'recaptcha';
			}
			
		}
		
		// todo: tester unicité email
		return !isset($this->error);
	}

	public function verifyData_uptPsw()
	{
		global $pdo;
		
		$texts = array('new1', 'new2', 'old');
		foreach($texts as $key => $value)
			$this->data[ $value ] = isset($_POST[ $value ]) ? trim($_POST[ $value ]) : '';
		
		if (strlen($this->data['new1']) < 6)
		{
			$this->fieldsError[ ] = 'new';
			$this->error = 'PASSWORD_LENGTH';
		}

		if (empty($this->data['new1']))
		{
			$this->fieldsError[ ] = 'new';
			$this->error = 'FIELD_EMPTY';
		}

		if ($this->data['new1'] != $this->data['new2'])
		{
			$this->fieldsError[ ] = 'new';
			$this->error = 'PASSWORDS_NEQUAL';
		}
		
		$q = $pdo->prepare('SELECT passwordsalt, password FROM users WHERE id=?');
		if (!$q->execute(array($this->id)))
		{
			$this->error = 'GET_DATA';
			return false;
		}
		
		$ret = $q->fetchAll(PDO::FETCH_NUM);

		if (!count($ret))
		{
			$this->error = 'GET_DATA';
			return false;
		}
		
		if ($ret[0][1] != sha1($ret[0][0] . sha1($this->data['old'])))
		{
			$this->fieldsError[ ] = 'old';
			$this->error = 'PASSWORD_OLD_BAD';
		}
		
		$this->data['password1'] = $this->data['new1'];
		
		return !isset($this->error);
	}

	private function preProcess()
	{
		$this->data['passwordsalt'] = substr(sha1(openssl_random_pseudo_bytes(10)), 0, 16);
		$this->data['password'] = empty($this->data['password1']) ? '' : sha1($this->data['passwordsalt'] . sha1($this->data['password1']));
	}
	
	public function addDB()
	{
		global $page, $pdo;
		
		$this->preProcess();
		
		$fields = '';
		$values = '';
		$values_data = array();
		$texts = array('pseudo', 'name', 'forename', 'email', 'password', 'passwordsalt', 'section', 'levelStudies');
		foreach($texts as $value)
		{
			$fields .= (empty($fields) ? '' : ',') . strtolower($value);
			$values .= (empty($values) ? '' : ',') . ':' . $value;
			$values_data[ $value ] = utf8_encode($this->data[ $value ]);
		}
		
		$fields .= ', timeRegistered, active';
		$values .= ', cast(extract(epoch from current_timestamp) as integer), 0';
		
		// -----------------------------------------------
		// email d'activation
		if (!$pdo->beginTransaction())
		{
			$this->error = 'REPETITEUR_ADD';
			return false;
		}
		
		$q = $pdo->prepare('INSERT INTO users (' . $fields . ') VALUES (' . $values . ')');
		if (!$q->execute($values_data)) 
		{
			$this->error = 'REPETITEUR_ADD';
			return false;
		}
		
		$lid = 0;
		if (($lid = $pdo->lastInsertId()) === false) // TODO: bug bizarre; à étudier
		{
			$q = $pdo->prepare('SELECT id FROM users ORDER BY id DESC LIMIT 1');
			if (!$q->execute())
			{
				$this->error = 'GET_DATA';
				return false;
			}
			
			$ret = $q->fetchAll(PDO::FETCH_NUM);
			$lid  = $ret[0][0];
		}
		
		$vars = array('forename' => $this->data['forename'], 
				'key' => substr(sha1(openssl_random_pseudo_bytes(10)), 0, 16),
				'baseurl' => $page['config']['baseUrl'],
				'id' => $lid);
		
		if (($q = $pdo->prepare('INSERT INTO users_activation (id, regKey) VALUES (?,?)')) === false)
		{
			$pdo->rollBack();
			$this->error = 'REPETITEUR_ADD';
			return false;
		}
		
		if (!$q->execute(array($vars['id'], $vars['key'])))
		{
			$pdo->rollBack();
			$this->error = 'REPETITEUR_ADD';
			return false;
		}
		
		if (!$pdo->commit())
		{
			$this->error = 'REPETITEUR_ADD';
			return false;
		}
		
		mailSend('account_activation' , 'SOS-Maths - Activation du compte', $vars, array(), $this->data['email']);

		return true;
	}
	
	public function uptDB()
	{
		global $pdo;
		$q_params = array('id' => $this->id);
		$q_text = '';
		
		$texts = array('pseudo', 'name', 'forename', 'email', 'section', 'levelStudies');
		foreach($texts as $key => $value)
		{
			$q_text .= (empty($q_text) ? '' : ', ') .  strtolower($value) . '=:' . $value;
			$q_params[ $value ] = utf8_encode($this->data[ $value  ]);
		}
		
		$q = $pdo->prepare('UPDATE users SET ' . $q_text . ' WHERE id=:id');
		if (!$q->execute($q_params))
		{
			$this->error = 'REPETITEUR_ADD';
			return false;
		}

		return true;
	}

	public function uptPswDB()
	{
		global $pdo;
		
		$this->preProcess();
		
		$q = $pdo->prepare('UPDATE users SET passwordsalt=:saltedPassword, password=:password WHERE id=:id');
		return $q->execute(array('saltedPassword' => $this->data['passwordsalt'], 'password' => $this->data['password'], 'id' => $this->id));
	}
	
	public function password_verifyData()
	{
		global $page, $pdo;
		
		$texts = array('pseudo', 'email');
		foreach($texts as $key => $value)
			$this->data[ $value ] = isset($_POST[ $value ]) ? trim($_POST[ $value ]) : '';
		
		$q = $pdo->prepare('SELECT id, active, forename FROM users WHERE email=? AND pseudo=?');
		if (!$q->execute(array($this->data['email'], $this->data['pseudo'])))
		{
			$this->error = 'GET_DATA';
			return false;
		}
		
		$ret = $q->fetchAll(PDO::FETCH_NUM);
		
		if (!count($ret))
		{
			$this->error = 'REPETITEUR_PASSWORD_RESET_NEXISTS';
			return false;
		}
		
		$row = $ret[0];
		if (!$row[1])
		{
			$this->error = 'REPETITEUR_NACTIVE';
			return false;
		}
		
		require_once 'resources/securimage/securimage.php';
		$securimage = new Securimage();
		
		if (!isset($_POST['captcha_code']) || $securimage->check($_POST['captcha_code']) == false)
		{
			$this->error = 'REPETITEUR_RECAPTCHA';
			return false;
		}
		
		$this->id = $row[0];
		$this->data['forename'] = $row[2];
		
		return true;
	}
	
	public function password_resetDB()
	{
		global $page, $pdo;
		
		$this->data['password1'] = substr(sha1(openssl_random_pseudo_bytes(10)), 0, 16);
		$this->preProcess();
		
		if (!$pdo->beginTransaction())
		{
			$this->error = 'REPETITEUR_PASSWORD_RESET_EMAIL';
			return false;
		}
		
		$q = $pdo->prepare('UPDATE users SET password=:pwd, passwordsalt=:pwdSalt WHERE id=:id');
		
		if (!$q->execute(array('id' => $this->id, 'pwd' => $this->data['password'], 'pwdSalt' => $this->data['passwordsalt'])))
		{
			$this->error = 'REPETITEUR_PASSWORD_RESET_DB';
			return false;
		}
		
		$vars = array('password' => $this->data['password1'], 
				'forename' => $this->data['forename'], 
				'pseudo' => $this->data['pseudo'],
				'baseurl' => $page['config']['baseUrl']);
		
		if (!mailSend('password_reset' , 'SOS-Maths - Ré-initialisation du mot de passe', $vars, array(), $this->data['email']))
		{
			$this->error = 'REPETITEUR_PASSWORD_RESET_EMAIL';
			return false;
		}
		
		if (!$pdo->commit())
		{
			$this->error = 'REPETITEUR_PASSWORD_RESET_DB';
			return false;
		}
		
		return true;
	}
}
?>
