<?php
if (!defined('IN_SITE'))
	die('Vous ne pouvez accéder a ce fichier directement.');

class studentTerm
{
	// que faire? 'add', 'del', 'upt'
	public $do = NULL;

	// id de l'user a modifier/supprimer, 0 si ajout
	public $id = 0;
	
	public $uniqId = '';

	// code de l'erreur
	public $error = NULL;
	public $fieldsError = array();

	// données membre
	public $data = NULL;
	
	public $y = 0;
	public $t = 'a';
	
	public $orderField = '';
	public $orderDirection = '';
	
	static $orderFields = array('name' => 'u.email', 'section' => 'u.section', 'level' => 'u.levelstudies', 'fee' => 't.feeMin');
	
	public function __construct($termText = '')
	{
		$this->uniqId = uniqid();
		$this->data['teachingSubjects'] = array();
		
		if (empty($termText))
		{
			$termDates = repetiteurTerm::computeTerms();
			$this->y = $termDates[0];
			$this->t = $termDates[1];
		}
		else
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
			
			$this->y = $y;
			$this->t = $termText[4];
		}
		
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
		global $globalVariables;
		
		$this->data['availibilityDay'] = array();
		
		/*
		foreach($globalVariables['day'] as $key => $value)
		{
			$this->data['availibilityDay'][ ] = $value[0] . '-am';
			$this->data['availibilityDay'][ ] = $value[0] . '-m';
			$this->data['availibilityDay'][ ] = $value[0] . '-pm';
			$this->data['availibilityDay'][ ] = $value[0] . '-e';
		}*/
		
		$this->data['language'] = array('fr');
		$this->data['place'] = array_keys($globalVariables['place']);
		$this->data['section'] = array_keys($globalVariables['section']);
		$this->data['levelStudies'] = array_keys($globalVariables['levelStudies']);
	}
	
	public function getPostValues()
	{
		global $globalVariables;
		
		$query = implode(',', array_keys($_POST));

		// --------------------------------------
		// dispos
		$matchs = array();
		preg_match_all('/(lu|ma|me|je|ve|sa|di)-(am|m|pm|e)/', $query, $matchs);
		$this->data['availibilityDay'] = $matchs[0];
		
		// --------------------------------------
		// langues
		$matchs = array();
		preg_match_all('/language-(' . implode('|', array_keys($globalVariables['language'])) . ')/', $query, $matchs);
		$this->data['language'] = $matchs[1];
		
		// --------------------------------------
		// branches & co	
		$matchs = array();
		preg_match_all('/teachingLevel-(0|1|2)/', $query, $matchs);
		$this->data['teachingSubjects'] = $matchs[1];
		
		$matchs = array();
		preg_match_all('/teachingSubjects-(' . implode('|', array_keys($globalVariables['teachingsubject1'])) . ')(,|\z)/', $query, $matchs);
		if (count($matchs[1]))
			$this->data['teachingSubjects'][ ] = 1;
		$this->data['teachingSubjects']  = array_merge($this->data['teachingSubjects'], $matchs[1]);
		
		$matchs = array();
		preg_match_all('/teachingSubjects-(' . implode('|', array_keys($globalVariables['teachingsubject2'])) . ')(,|\z)/', $query, $matchs);
		if (count($matchs[1]))
			$this->data['teachingSubjects'][ ] = 2;
		$this->data['teachingSubjects']  = array_merge($this->data['teachingSubjects'], $matchs[1]);
		
		$this->data['teachingSubjects'] = array_unique($this->data['teachingSubjects']);
		
		// --------------------------------------
		// emplacement
		$matchs = array();
		preg_match_all('/place-(' . implode('|', array_keys($globalVariables['place'])) . ')/', $query, $matchs);
		$this->data['place'] = $matchs[1];
		
		// --------------------------------------
		// section
		$matchs = array();
		preg_match_all('/section-(' . implode('|', array_keys($globalVariables['section'])) . ')/', $query, $matchs);
		$this->data['section'] = $matchs[1];
		
		// --------------------------------------
		// niveau
		$matchs = array();
		preg_match_all('/levelStudies-(' . implode('|', array_keys($globalVariables['levelStudies'])) . ')/', $query, $matchs);
		$this->data['levelStudies'] = $matchs[1];
	}
	
	public function getValuesAJAX()
	{
		global $globalVariables;
		
		// --------------------------------------
		// langues
		$this->data['language'] = explode(',', isset($_POST['language']) ? $_POST['language'] : '');
		$this->data['language'] = array_intersect($this->data['language'] , array_keys($globalVariables['language']));
		
		// --------------------------------------
		// dispos
		$matchs = array();
		preg_match_all('/(lu|ma|me|je|ve|sa|di)-(am|m|pm|e)/', isset($_POST['availibilityDay']) ? $_POST['availibilityDay'] : '', $matchs);
		$this->data['availibilityDay'] = $matchs[0];
		
		// --------------------------------------
		// branches & co
		$matches = array();
		$teachingSubject = isset($_POST['teachingSubjects']) ? $_POST['teachingSubjects'] : '';
		
		preg_match_all("/(\A|,)([[:digit:]]+)/", $teachingSubject, $matches);
		$this->data['teachingSubjects'] = $matches[2];
		
		$temp = array_intersect(explode(',',  $teachingSubject) , array_keys($globalVariables['teachingsubject1']));
		if (count($temp))
			$this->data['teachingSubjects'][ ] = 1;
		$this->data['teachingSubjects']  = array_merge($this->data['teachingSubjects'], $temp);
		
		$temp = array_intersect(explode(',',  $teachingSubject) , array_keys($globalVariables['teachingsubject2']));
		if (count($temp))
			$this->data['teachingSubjects'][ ] = 2;
		$this->data['teachingSubjects']  = array_merge($this->data['teachingSubjects'], $temp);
		
		$this->data['teachingSubjects'] = array_unique($this->data['teachingSubjects']);
		
		// --------------------------------------
		// section
		$this->data['section'] = explode(',', isset($_POST['section']) ? $_POST['section'] : '');
		$this->data['section'] = array_intersect($this->data['section'] , array_keys($globalVariables['section']));
		
		// --------------------------------------
		// levelStudies
		$this->data['levelStudies'] = explode(',', isset($_POST['levelStudies']) ? $_POST['levelStudies'] : '');
		$this->data['levelStudies'] = array_intersect($this->data['levelStudies'] , array_keys($globalVariables['levelStudies']));
	}
	
	/*
	 * createCountQuery
	 * 		Crée la requête de comptage
	 * 		Retour: requête (string) ou 0 si pas de résultats
	 */
	public function createCountQuery()
	{
		if (($r = $this->createQueryBody()) === 0)
			return 0;
		
		return array('SELECT DISTINCT t.id ' . $r[0], $r[1]);
	}
	
	/*
	 * createCountQuery
	 * 		Crée la requête de comptage
	 * 		Retour: requête (string) ou 0 si pas de résultats
	 */
	public function createGetQuery()
	{
		if (($r = $this->createQueryBody()) === 0)
			return 0;
				
		return array('SELECT DISTINCT u.id, u.name, u.email, u.forename, u.section, u.levelstudies, t.feemin, t.feemax ' . $r[0] . ' ORDER BY ' . self::$orderFields[ $this->orderField ] . ' ' . $this->orderDirection, $r[1]);
	}
	
	public function getGetValues()
	{
		$this->orderField = isset($_GET['order']) ? $_GET['order'] : 'name';
		$this->orderDirection = isset($_GET['orderD']) && ($_GET['orderD'] == 'ASC' || $_GET['orderD'] == 'DESC') ? $_GET['orderD'] : 'ASC';
		
		if (!array_key_exists($this->orderField, self::$orderFields)) 
			$this->orderField = 'name';
	}
	
	private function createQueryBody()
	{
		global $pdo;
		
		if (!count($this->data['availibilityDay']))
			return 0;
			
		if (!count($this->data['language']))
			return 0;
			
		if (!count($this->data['teachingSubjects']))
			return 0;
			
		if (!count($this->data['section']))
			return 0;
			
		if (!count($this->data['levelStudies']))
			return 0;
			
		// todo: gérer available
		
		// TODO: vérifier: pdo, prepare et IN(text ici avec des ' et ,)
		
		$params = array('y' => $this->y, 't' => $this->t);
		
		$query = ' FROM repetiteur_term t INNER JOIN users u ON u.id=t.userId INNER JOIN repetiteur_term_availibilityDay av ON t.id=av.repetiteur_term_id INNER JOIN repetiteur_term_language l ON t.id=l.repetiteur_term_id WHERE t.annee=:y AND t.term=:t AND t.available=1 ';
		
		// disponibilités
		$query .= ' AND av.day IN (' . implode(',', array_map(array($pdo, 'quote'), $this->data['availibilityDay'])) . ') ';
		
		// langue
		$query .= ' AND l.language IN (' . implode(',', array_map(array($pdo, 'quote'), $this->data['language'])) . ') ';
		
		// sujets
		$query .= ' AND t.id IN (SELECT repetiteur_term_id FROM repetiteur_term_subject WHERE sujet IN (' . implode(',', array_map(array($pdo, 'quote'), $this->data['teachingSubjects'])) . ') GROUP BY repetiteur_term_id HAVING count(*)=:teachingSubjectsCount)';
		$params['teachingSubjectsCount'] = count($this->data['teachingSubjects']);
		
		// section
		$query .= ' AND u.section IN (' . implode(',', array_map(array($pdo, 'quote'), $this->data['section'])) . ') ';
		
		// levelStudies
		$query .= ' AND u.levelstudies IN (' . implode(',', array_map(array($pdo, 'quote'), $this->data['levelStudies'])) . ') ';
		
		return array($query, $params);
	}
}
?>
