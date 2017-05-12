<?php
$globalVariables = array();
global $globalVariables;

$globalVariables['section'] = array(
	'ar' => array('AR', 'Architecture'),
	'ch' => array('CH', 'Chimie'),
	'cms' => array('CMS', 'Cours de mathématiques spéciales'),
	'gc' => array('GC', 'Génie Civil'),
	'el' => array('EL', 'Génie électrique et électronique'),
	'gm' => array('GM', 'Génie Mécanique'),
	'in' => array('IN', 'Informatique'),
	'sie' => array('SIE', 'Ingénierie de l\'environnement'),
	'ma' => array('MA', 'Mathématiques'),
	'mi' => array('MT', 'Microtechnique'),
	'ph' => array('PH', 'Physique'),
	'mx' => array('MX', 'Science et génie des matériaux'),
	'sc' => array('SC', 'Systèmes de communication'),
	'sv' => array('SV', 'Sciences de la vie'));
	

$globalVariables['levelStudies'] = array(
	'0' => array('CMS', 'CMS'), 
	'1' => array('BA1', 'Première'),
	'2' => array('BA2', 'Deuxième'),
	'3' => array('BA3', 'Troisième'),
	'4' => array('MA1', 'Première année de master'),
	'5' => array('MA2', 'Deuxième année de master'),
	'6' => array('MA', 'Master obtenu'),
	'7' => array('PHD', 'Doctorat obtenu'));
	
$globalVariables['fee'] = array('<25' => array(0, 24, 1), '25 - 35' => array(25, 35, 2), '36 - 45' => array(36, 45, 3), '>45' => array(45, 255, 4));
	
$globalVariables['place'] = array('dom' => 'Domicile de l\'élève', 'epfl' => 'A l\'EPFL', 'autre' => 'Autre');

$globalVariables['language'] = array('de' => 'Allemand', 'en' => 'Anglais', 'fr' => 'Français', 'it' => 'Italien');

$globalVariables['day'] = array(0 => array('lu', 'Lundi'), 
					1 => array('ma', 'Mardi'), 
					2 => array('me', 'Mercredi'), 
					3 => array('je', 'Jeudi'), 
					4 => array('ve', 'Vendredi'), 
					5 => array('sa', 'Samedi'), 
					6 => array('di', 'Dimanche'));

$globalVariables['teachingsubject1'] = array('1app' => 'Application des maths',
	'1bio' => 'Biologie',
	'1ch' => 'Chimie',
	'1ma' => 'Maths',
	'1ph' => 'Physique');
					
$globalVariables['teachingsubject2'] = array('2alg' => 'Algèbre',
	'2alglin' => 'Algèbre linéaire',
	'2an1' => 'Analyse I & II',
	'2an2' => 'Analyse III & IV',
	'2anum' => 'Analyse numérique',
	'2bio' => 'Biologie',
	'2chim' => 'Chimie',
	'2geom' => 'Géométrie',
	'2inf' => 'Informatique',
	'2madis' => 'Mathématiques discrètes',
	'2opt' => 'Optimisation',
	'2phys' => 'Physique',
	'2prob' => 'Probabilités',
	'2stat' => 'Statistiques',
	'2topo' => 'Topologie');
?>
