<?php
if (!defined('IN_SITE'))
	die('Vous ne pouvez accéder a ce fichier directement.');

class repetiteurTerm
{
	// que faire? 'add', 'del', 'upt'
	public $do = NULL;

	// id de l'user a modifier/supprimer, 0 si ajout
	public $id = 0;
	public $userId = 0;

	// code de l'erreur
	public $error = NULL;
	public $fieldsError = array();

	// données membre
	public $data = NULL;
	
	public $term = array(NULL, NULL); // [ 0 => année, 1 => a ou p ]

	/*
		public __construct()
		Constructeur, initialise les variables qui doivent l'être
		Parametres:
			- $do (string): que faire 'add', 'del', 'upt'
			- $userId (unsigned int): id de l'user
	*/
	function __construct($do, $termText, $userId = null)
	{
		global $pdo;
		
		$this->do = $do;
		$this->userId = isset($userId) ? $userId : $_SESSION['m_id'];
		
		if ($do !== 'null')
		{
			if (strlen($termText) !== 5)
			{
				$this->error = 'TERM_INVALID_DATE';
				return;
			}
			
			$y = substr($termText, 0, 4);
			if (!filter_var($y, FILTER_VALIDATE_INT, array('min_range' => 2000, 'max_range' => 2100)))
			{
				$this->error = 'TERM_INVALID_DATE';
				return;
			}
			
			$y = (int) $y;
			if ($y < (date('Y ') - 1) || $y > date('Y'))
			{
				$this->error = 'TERM_INVALID_DATE';
				return;
			}
			
			if ($termText[4] != 'a' && $termText[4] != 'p')
			{
				$this->error = 'TERM_INVALID_DATE';
				return;
			}
			
			$this->term = array($y, $termText[4]);	
		}

		// ---------------------------------
		// add ou upt?
		$q = $pdo->prepare('SELECT id FROM repetiteur_term WHERE userId=? AND annee=? AND term=?');
		if (!$q->execute(array($this->userId, $this->term[0], $this->term[1])))
		{
			$this->error = 'GET_DATA';
			return;
		}
		
		$ret = $q->fetchAll(PDO::FETCH_NUM);
		
		if (count($ret))
		{
			$this->id = $ret[0][0];
			$this->do = 'upt';
		}
		else
			$this->do = 'add';
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
		global $globalVariables, $pdo;
		
		$q = $pdo->prepare('SELECT levelstudies FROM users WHERE id=? ');
		if (!$q->execute(array($this->userId)))
		{
			$this->error = 'GET_DATA';
			return false;
		}
		
		$ret = $q->fetchAll(PDO::FETCH_NUM);
		
		if (!count($ret))
		{
			$this->error = 'REPETITEUR_NEXISTS';
			return false;
		}
		
		$this->data['levelStudies'] = $ret[0][0];
		
		// ------------------------------------
		// valeurs par défaut
		$this->data['fee'] = array();
		$this->data['available'] = true;
		$this->data['placeComment'] = '';
		$this->data['comment'] = '';
		$this->data['place'] = array();
		$this->data['language'] = array();
		$this->data['teachingSubjects'] = array();
		$this->data['availibilityDay'] = array();
		$this->data['teachingLevel'] = array();
		
		if ($this->do == 'upt')
		{
			// ------------------------------------
			// information de base
			$q = $pdo->prepare('SELECT feeMin, feeMax, available, commentaire FROM repetiteur_term WHERE id=?');
			if (!$q->execute(array($this->id)))
			{
				$this->error = 'GET_DATA';
				return false;
			}
			
			$ret = $q->fetchAll(PDO::FETCH_NUM);
			if (!count($ret))
			{
				$this->error = 'REPETITEUR_NEXISTS';
				return false;
			}
			
			$row = $ret[0];
			$this->data['available'] = $row[2];
			$this->data['comment'] = utf8_decode($row[3]);
			
			foreach($globalVariables['fee'] as $text => $data)
			{
				if ($data[0] >= $row[0] && $data[1] <= $row[1])
					$this->data['fee'][ ] = $data[0] . '_' . $data[1];
			}
			
			// ----------------------------------
			// langues
			$q = $pdo->prepare('SELECT language FROM repetiteur_term_language WHERE repetiteur_term_id=?');
			if (!$q->execute(array($this->id)))
			{
				$this->error = 'GET_DATA';
				return false;
			}
			
			$ret = $q->fetchAll(PDO::FETCH_NUM);
			foreach($ret as $row)
				$this->data['language'][ ] = $row[0];
			
			// ----------------------------------
			// dispos
			$q = $pdo->prepare('SELECT day FROM repetiteur_term_availibilityDay WHERE repetiteur_term_id=?');
			if (!$q->execute(array($this->id)))
			{
				$this->error = 'GET_DATA';
				return false;
			}
			
			$ret = $q->fetchAll(PDO::FETCH_NUM);
			foreach($ret as $row)
				$this->data['availibilityDay'][ ] = $row[0];
				
			// ----------------------------------
			// lieux
			$q = $pdo->prepare('SELECT place FROM repetiteur_term_place WHERE repetiteur_term_id=?');
			if (!$q->execute(array($this->id)))
			{
				$this->error = 'GET_DATA';
				return false;
			}
			
			$ret = $q->fetchAll(PDO::FETCH_NUM);
			foreach($ret as $row)
				$this->data['place'][ ] = $row[0];
			
			// ----------------------------------
			// lieu comment
			$q = $pdo->prepare('SELECT commentaire FROM repetiteur_term_placeComment WHERE repetiteur_term_id=?');
			if (!$q->execute(array($this->id)))
			{
				$this->error = 'GET_DATA';
				return false;
			}
			
			$ret = $q->fetchAll(PDO::FETCH_NUM); // y'a un index donc au plus un seul résultat retourné
			$this->data['placeComment'] = count($ret) ? utf8_decode($ret[0][0]) : '';
			
			// ----------------------------------
			// matières
			$q = $pdo->prepare('SELECT sujet FROM repetiteur_term_subject WHERE repetiteur_term_id=?');
			if (!$q->execute(array($this->id)))
			{
				$this->error = 'GET_DATA';
				return false;
			}
		
			$ret = $q->fetchAll(PDO::FETCH_NUM);
			foreach($ret as $row)
			{
				if (filter_var($row[0], FILTER_VALIDATE_INT) || $row[0] == '0' ) // si entier
					$this->data['teachingLevel'][ ] = (int)$row[0];
				else
					$this->data['teachingSubjects'][ ] = $row[0];
			}	
		}
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
		global $globalVariables, $page;
		
		$this->data['available'] = isset($_POST['available']) && $_POST['available'] ? true : false;
		
		// ------------------------------------
		// levelStudies
		$this->data['levelStudies'] = isset($_POST['levelStudies']) ? $_POST['levelStudies'] : 0;	
		if (!array_key_exists($this->data['levelStudies'], $globalVariables['levelStudies']))
		{
			$this->error = 'DATA_BAD';
			$this->fieldsError[ ] = 'levelStudies';
		}
		
		// ------------------------------------
		// tarifs
		$feeMin = -1;
		$feeMax = 0;
		foreach($globalVariables['fee'] as $text => $data)
		{
			if (isset($_POST[ 'fee-' . $data[0] . '_' . $data[1] ]) && $_POST[ 'fee-' . $data[0] . '_' . $data[1] ])
			{
				if (-1 == $feeMin)
				{
					$this->data['feeIndex'] = $data[2]; // index de la fee
					$feeMin = $data[0];
				}
				
				$feeMax = $data[1];
				
				$this->data['fee'][ ] = $data[0] . '_' . $data[1];
			}
		}

		if ($feeMin != -1) // si on a eu quelque chose
		{
			foreach($globalVariables['fee'] as $text => $data)
			{
				if ($data[0] >= $feeMin && $data[1] <= $feeMax)
					$this->data['fee'][ ] = $data[0] . '_' . $data[1];
			}
			
			$this->data['fee'] = array_unique($this->data['fee']);
		}
		else
			$this->data['fee'] = array();
			
		$this->data['feeMin'] = $feeMin;
		$this->data['feeMax'] = $feeMax;
		
		// --------------------------------
		// texts
		$texts = array('placeComment', 'comment');
		foreach($texts as $value)
			$this->data[ $value ] = isset($_POST[ $value ]) ? str_replace(array('<![CDATA[', ']]>'), array('', ''), trim($_POST[ $value ])) : '';
		
		// --------------------------------
		// checkboxs
		$checkboxs = array('place' => 'place', 'language' => 'language', 'teachingsubject1' => 'teachingSubjects', 'teachingsubject2' => 'teachingSubjects');
		foreach($checkboxs as $key => $value)
		{
			if (!isset($this->data[ $value ]))
				$this->data[ $value ] = array();
			
			foreach($globalVariables[ $key ] as $optName => $optCaption)
			{
				if (isset($_POST[ $value . '-' . $optName ]) && $_POST[ $value . '-' . $optName ]) 
					$this->data[ $value ][ ] = $optName;
			}	
		}
		
		// --------------------------------
		// jours
		$periods = array('am', 'm', 'pm', 'e');
		foreach($globalVariables['day'] as $key => $value)
		{
			$periods = array('am', 'm', 'pm', 'e');
			
			foreach($periods as $p)
			{
				if (isset($_POST[ $value[0] . '-' . $p ]) && $_POST[ $value[0] . '-' . $p ]) 
					$this->data['availibilityDay'][ ] = $value[0] . '-' . $p;
			}
		}
		
		if (!isset($this->data['availibilityDay']) || !count($this->data['availibilityDay']))
		{
			$this->fieldsError[ ] = 'availability';
			$this->error = 'REPETITEUR_TERM_AVAILIBILITY';
			$this->data['availibilityDay'] = array();
		}
		
		// --------------------------------
		// niveaux
		for ($i = 0; $i <= 2; $i++)
		{
			if (isset($_POST['teachingLevel-' . $i ]) && $_POST['teachingLevel-' . $i ])
				$this->data['teachingLevel'][ ] = $i;
		}
		if (!isset($this->data['teachingLevel']))
			$this->data['teachingLevel'] = array();
		
		// -----------------------------------------------------------
		// vérifications
		if (!count($this->data['language']))
		{
			$this->error = 'REPETITEUR_TERM_LANGUAGE_EMPTY';
			$this->fieldsError[ ] = 'language';
		}
		
		if (!count($this->data['place']))
		{
			$this->error = 'REPETITEUR_TERM_PLACE_EMPTY';
			$this->fieldsError[ ] = 'place';
		}
		
		if (!array_key_exists($this->data['levelStudies'], $globalVariables['levelStudies']))
		{
			$this->fieldsError[ ] = 'levelStudies';
			$this->error = 'LEVEL_STUDIES_BAD';
		}
		
		if (!count($this->data['fee']))
		{
			$this->error = 'REPETITEUR_TERM_FEE_EMPTY';
			$this->fieldsError[ ] = 'fee';
		}
		
		$subjects = implode(',', $this->data['teachingSubjects']);
		
		if (preg_match_all('/1([[:alpha:]]+)/', $subjects))
			$this->data['teachingLevel'][ ] = 1;
		
		if (preg_match_all('/2([[:alpha:]]+)/', $subjects))
			$this->data['teachingLevel'][ ] = 2;
		
		if (!count($this->data['teachingLevel']))
		{
			$this->error = 'REPETITEUR_TERM_SUBJECT_EMPTY';
			$this->fieldsError[ ] = 'teachingLevel';
		}

		return !isset($this->error);
	}

	private function preProcess()
	{
		$this->data['language'] = array_unique($this->data['language']);
		$this->data['place'] = array_unique($this->data['place']);
		$this->data['teachingSubjects'] = array_unique($this->data['teachingSubjects']);
		$this->data['availibilityDay'] = array_unique($this->data['availibilityDay']);
		$this->data['teachingLevel'] = array_unique($this->data['teachingLevel']);
	}

	public function doDB()
	{
		global $pdo;
		
		if (!$pdo->beginTransaction())
		{
			$this->error = 'GET_DATA';
			return false;
		}
		
		$this->preProcess();

		if ($this->do == 'add')
		{
			// -------------------------------------------------------------------
			// requête de base
			$queryFields = 'userId, annee, term, fee, feeMin, feeMax, available, commentaire';
			$q = $pdo->prepare('INSERT INTO repetiteur_term (userId, annee, term, fee, feemin, feemax, available, commentaire) VALUES (:userId, :annee, :term, :fee, :feeMin, :feeMax, :available, :commentaire)');
			
			if (!$q->execute(array('userId' => $this->userId, 'annee' => $this->term[0], 'term' => $this->term[1], 'fee' => $this->data['feeIndex'], 'feeMin' => $this->data['feeMin'], 'feeMax' => $this->data['feeMax'], 'available' => ($this->data['available'] ? '1' : '0'), 'commentaire' =>  utf8_encode($this->data['comment']))))
			{
				$this->error = 'GET_DATA';
				return false;
			}
			
			if (($this->id = $pdo->lastInsertId()) === false) // TODO: bug bizarre; à étudier
			{
				$q = $pdo->prepare('SELECT id FROM repetiteur_term ORDER BY id DESC LIMIT 1');
				if (!$q->execute())
				{
					$this->error = 'GET_DATA';
					return false;
				}
				
				$ret = $q->fetchAll(PDO::FETCH_NUM);
				$this->id = $ret[0][0];
			}
		}
		else if ($this->do == 'upt')
		{
			if (($q = $pdo->prepare('UPDATE repetiteur_term SET feemin=?, feemax=?, available=?, commentaire=? WHERE id=?')) === false)
			{
				$pdo->rollBack();
				$this->error = 'GET_DATA';
				return false;
			}
			
			if (!$q->execute(array($this->data['feeMin'], $this->data['feeMax'], ($this->data['available'] ? '1' : '0'), utf8_encode($this->data['comment']), $this->id)))
			{
				$pdo->rollBack();
				$this->error = 'GET_DATA';
				return false;
			}
			
			$tables = array('repetiteur_term_availibilityDay', 'repetiteur_term_language', 'repetiteur_term_place', 'repetiteur_term_placeComment', 'repetiteur_term_subject');
			foreach($tables as $table)
			{
				if (($q = $pdo->prepare('DELETE FROM ' . $table . ' WHERE repetiteur_term_id=?')) === false)
				{
					$pdo->rollBack();
					$this->error = 'GET_DATA';
					return false;
				}
				
				if (!$q->execute(array($this->id)))
				{
					$pdo->rollBack();
					$this->error = 'GET_DATA';
					return false;
				}
			}
		}
		
		// -------------------------------------------------------------------
		// langues
		$queryFields = 'repetiteur_term_id, language';
		$queryValues = '';
		$queryData = array();
		foreach($this->data['language'] as $value)
		{
			$queryValues .= (empty($queryValues) ? '' : ', ') . '(?,?)';
			$queryData[ ] = $this->id;
			$queryData[ ] = $value;
		}
		
		if (($q = $pdo->prepare('INSERT INTO repetiteur_term_language (' . $queryFields . ') VALUES ' . $queryValues)) === false)
		{
			$pdo->rollBack();
			$this->error = 'GET_DATA';
			return false;
		}
		
		if (!$q->execute($queryData))
		{
			$pdo->rollBack();
			$this->error = 'GET_DATA';
			return false;
		}
		
		// -------------------------------------------------------------------
		// disponibilités
		$queryFields = 'repetiteur_term_id, day';
		$queryValues = '';
		$queryData = array();
		foreach($this->data['availibilityDay'] as $value)
		{
			$queryValues .= (empty($queryValues) ? '' : ', ') . '(?,?)';
			$queryData[ ] = $this->id;
			$queryData[ ] = $value;
		}
		
		if (($q = $pdo->prepare('INSERT INTO repetiteur_term_availibilityDay (' . $queryFields . ') VALUES ' . $queryValues)) === false)
		{
			$pdo->rollBack();
			$this->error = 'GET_DATA';
			return false;
		}
		
		if (!$q->execute($queryData))
		{
			$pdo->rollBack();
			$this->error = 'GET_DATA';
			return false;
		}
		
		// -------------------------------------------------------------------
		// sujets
		$queryFields = 'repetiteur_term_id, sujet';
		$queryValues = '';
		$queryData = array();
		foreach($this->data['teachingSubjects'] as $value)
		{
			$queryValues .= (empty($queryValues) ? '' : ', ') . '(?,?)';
			$queryData[ ] = $this->id;
			$queryData[ ] = $value;
		}
		
		foreach($this->data['teachingLevel'] as $value)
		{
			$queryValues .= (empty($queryValues) ? '' : ', ') . '(?,?)';
			$queryData[ ] = $this->id;
			$queryData[ ] = $value;
		}
		
		if (($q = $pdo->prepare('INSERT INTO repetiteur_term_subject (' . $queryFields . ') VALUES ' . $queryValues)) === false)
		{
			$pdo->rollBack();
			$this->error = 'GET_DATA';
			return false;
		}
		
		if (!$q->execute($queryData))
		{
			$pdo->rollBack();
			$this->error = 'GET_DATA';
			return false;
		}
		
		// -------------------------------------------------------------------
		// endroits
		$queryFields = 'repetiteur_term_id, place';
		$queryValues = '';
		$queryData = array();
		foreach($this->data['place'] as $value)
		{
			$queryValues .= (empty($queryValues) ? '' : ', ') . '(?,?)';
			$queryData[ ] = $this->id;
			$queryData[ ] = $value;
		}
		
		if (($q = $pdo->prepare('INSERT INTO repetiteur_term_place (' . $queryFields . ') VALUES ' . $queryValues)) === false)
		{
			$pdo->rollBack();
			$this->error = 'GET_DATA';
			return false;
		}
		
		if (!$q->execute($queryData))
		{
			$pdo->rollBack();
			$this->error = 'GET_DATA';
			return false;
		}
		
		if (!empty($this->data['placeComment']))
		{
			if (($q = $pdo->prepare('INSERT INTO repetiteur_term_placeComment (repetiteur_term_id, commentaire) VALUES (?,?)')) === false)
			{
				$pdo->rollBack();
				$this->error = 'GET_DATA';
				return false;
			}
			
			if (!$q->execute(array($this->id, utf8_encode($this->data['placeComment']))))
			{
				$pdo->rollBack();
				$this->error = 'GET_DATA';
				return false;
			}
		}
		
		// -------------------------------------------------------------------
		// semestre d'étude
		if (($q = $pdo->prepare('UPDATE users SET levelstudies=? WHERE id=?')) === false)
		{
			$pdo->rollBack();
			$this->error = 'GET_DATA';
			return false;
		}
		
		if (!$q->execute(array($this->data['levelStudies'], $this->userId)))
		{
			$pdo->rollBack();
			$this->error = 'GET_DATA';
			return false;
		}
		
		if (!$pdo->commit())
		{
			$this->error = 'GET_DATA';
			return false;
		}
		
		return true;
	}

	public static function computeTerms()
	{
		$termCur = $termNext = 'a';
		$termCurYear = $termNextYear = date('Y');
		
		if (date('n') >= 1 && date('n') <= 6) // entre février et juin
		{
			$termCur = 'p';
			
			if (date('n') <= 6)
				$termCurYear --;
		}
		if ($termCur == 'a')
			$termNext = 'p';
		
		return array($termCurYear, $termCur, $termNextYear, $termNext);
	}
}
?>
