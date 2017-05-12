<?php
//session_name('sosmaths');
session_start();

define('IN_SITE', 1);
$GLOBALS['relativePath'] = '../../';

$pdo = null;

class globalGet
{
	private $buffer = '';
	private $XML = true; // sortie en xml?
	
	function __construct()
	{
		if (!isset($_GET['XMLNone']) || !$_GET['XMLNone'])
		{
			$this->buffer = '<?xml version="1.0" encoding="UTF-8" ?><options>';
			header('Content-Type: text/xml; Charset=utf-8');
		}
		else
		{
			header('Content-Type: text/html; charset=ISO-8859-1');
			$this->XML = false;
		}
	}
	
	function __destruct()
	{
		if ($this->XML)
		{
			$this->buffer .= '</options>';
			echo utf8_encode($this->buffer);
		}
		else
			echo $this->buffer;
	}
	
	private function DBConnect() // TODO: rename
	{
		include '../conf.inc.php';
		global $pdo;
		
		try 
		{
			$pdo = new PDO('pgsql:host=' . $config['db_host'] . ';dbname=' . $config['db_db'], $config['db_user'], $config['db_psw']);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e) 
		{
			return false;
		}
			
		return true;
	}
	
	public function main()
	{
		if (!$this->DBConnect())
			$this->error('DB_CONNECT');
			
		$do = isset($_GET['do']) ? $_GET['do'] : '';
		if (!empty($do) && method_exists($this, $do))
		{
			$this->$do();
		}
	}
	
	private function repetiteurTermCount()
	{
		include '../../classes/student.term.class.php';
		include '../global.vars.inc.php';
		global $pdo;
		
		$termText = isset($_POST['termText']) ? trim($_POST['termText']) : '';
		
		$st = new studentTerm($termText);
		
		if (isset($st->error))
			$this->error($st->error, true);
		
		$st->getValuesAJAX();
		$qData = $st->createCountQuery();
		
		if ($qData === 0)
			$this->buffer .= '<count>0</count>';
		else
		{
			$q = $pdo->prepare($qData[0]);
			$q = $pdo->prepare($qData[0]);
			if (!$q->execute($qData[1])) 
				$this->error( 'GET_DATA', true);
				
			$ret = $q->fetchAll(PDO::FETCH_COLUMN);
			
			$this->buffer .= '<count>' . count($ret) . '</count>';
		}
		
		// getValuesAJAX
	}
	
	private function repetiteurInfos()
	{
		include '../global.vars.inc.php';
		global $globalVariables, $pdo;
		
		$userId = isset($_GET['userId']) ? (int) $_GET['userId'] : 0;
		$y = isset($_GET['y']) ? (int) $_GET['y'] : 0;
		$t = isset($_GET['t']) ? $_GET['t'] : 0;
		
		if ($t != 'a' && $t != 'p')
			$this->error('TERM_INVALID_DATE');

		// NON STD PDO: string_agg

		// ----------------------------------------------------------------------
		// données de base
		$q = $pdo->prepare('SELECT u.name, u.forename, t.available, pc.commentaire AS placecomment FROM repetiteur_term t INNER JOIN users u ON u.id=t.userId LEFT OUTER JOIN repetiteur_term_placeComment pc ON pc.repetiteur_term_id=t.id WHERE t.userId=:userId AND t.annee=:y AND t.term=:t');
		if (!$q->execute(array('t' => $t, 'y' => $y, 'userId' => $userId)))
			$this->error('GET_DATA');
			
		$ret = $q->fetchAll(PDO::FETCH_ASSOC);
			
		if (!count($ret)) 
			$this->error('TERM_USER_NEXISTS');
		
		$row = $ret[0];
		if (!$row['available'])
			$this->error('REPETITEUR_NAVAILABLE');

		$this->buffer .= '<id>' . $userId . '</id>';
		$this->buffer .= '<name>' . $this->prepareText(utf8_decode($row['forename'] . ' ' . $row['name'])) . '</name>';
		$this->buffer .= '<placeComment><![CDATA[' . $this->prepareText(utf8_decode($row['placecomment'])) . ']]></placeComment>'; // commentaire de lieu
		
		// ----------------------------------------------------------------------
		// suite des données
		$q = $pdo->prepare('SELECT string_agg(DISTINCT p.place, \',\') AS places, string_agg(DISTINCT s.sujet, \',\') AS subjects FROM repetiteur_term t INNER JOIN users u ON u.id=t.userId INNER JOIN repetiteur_term_place p ON p.repetiteur_term_id=t.id INNER JOIN repetiteur_term_subject s ON s.repetiteur_term_id=t.id LEFT OUTER JOIN repetiteur_term_placeComment pc ON pc.repetiteur_term_id=t.id WHERE t.userId=:userId AND t.annee=:y AND t.term=:t');
		if (!$q->execute(array('t' => $t, 'y' => $y, 'userId' => $userId)))
			$this->error('GET_DATA');
			
		$ret = $q->fetchAll(PDO::FETCH_ASSOC);

		// lieu
		$places = explode(',', $ret[0]['places']);
		foreach($places as $place)
			$this->buffer .= '<place>' . $this->prepareText($globalVariables['place'][$place]) . '</place>';
		
		// -------------------------------------------
		// sujets
		$this->buffer .= '<subjects>';
		$subjects = explode(',', $ret[0]['subjects']);
		
		// niveau primaire et secondaire
		if (in_array(0, $subjects)) 
			$this->buffer .= '<level><title>' . $this->prepareText('Primaire et secondaire') . '</title></level>';
		
		// Niveau gymnase & uni
		for ($j = 1; $j <= 2; $j++)
		{
			$temp = array_intersect($subjects, array_keys($globalVariables['teachingsubject' . $j ]));

			if (count($temp) || in_array($j, $subjects))
			{
				$this->buffer .= '<level>';
				$this->buffer .= '<title>' . $this->prepareText($j == 1 ? 'Gymnase' : 'Université / HES / ...') . '</title>';
				
				foreach($temp as $key)
					$this->buffer .= '<subject>' . $this->prepareText($globalVariables['teachingsubject' . $j][$key]) . '</subject>';
				
				$this->buffer .= '</level>';
			}
		}

		$this->buffer .= '</subjects>';
	}
	
	private function error($error, $ee = false)
	{
		include '../definedTexts.inc.php';
		$this->buffer .= '<error>' . $this->prepareText((isset($definedTexts[ $error ]) ? $definedTexts[ $error ] : $definedTexts['DEFAULT']) . ($ee ? '<br />Si le problème persiste signalez le à SOS-Maths pour que le coupable soit puni.' : '')) . '</error>';
		exit;
	}
	
	private function prepareText($text)
	{
		return str_replace(array('&', '<', '>'), array('&amp;', '&lt;', '&gt;'), $text);
	}
};

$obj = new globalGet();
$obj->main();
?>
