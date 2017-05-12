<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--
Design by Free CSS Templates
http://www.freecsstemplates.org
Released for free under a Creative Commons Attribution 2.5 License

Name       : Breakeven 
Description: A two-column, fixed-width design with dark color scheme.
Version    : 1.0
Released   : 20130509

-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="keywords" content="" />
<meta name="description" content="" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>SOS-Maths</title>
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Abel|Satisfy' rel='stylesheet' type='text/css'>
<link href="resources/css/theme.css" rel="stylesheet" type="text/css" media="screen" />
<link href="resources/css/style.css" rel="stylesheet" type="text/css" media="screen" />
{css}

<script src="resources/scriptaculous/lib/prototype.js" type="text/javascript"></script>
<script src="resources/scriptaculous/src/scriptaculous.js?load=builder,effects,dragdrop,controls" type="text/javascript"></script>
<script src="resources/js/lib.js" type="text/javascript"></script>
{javascript}

</head>
<body>
<div id="wrapper">
	<div id="header-wrapper">
		<div id="header" class="container">
			<div id="logo">
				<h1><a href="index.php">SOS-Maths</a></h1>
			</div>
			<div id="menu">
				<ul>
					<!-- BEGIN menuItem -->
					<li {menuItem.class}><a href="index.php?p={menuItem.link}">{menuItem.libelle}</a></li>
					<!-- END menuItem -->
				</ul>
				{LOGINOUTBOX}
			</div>
		</div>
	</div>
	<!-- end #header -->
	<div id="page">
		<div id="content">			
			{MAIN_CONTENT}
		</div>
		<!-- end #content -->
		

		<div style="clear: both;">&nbsp;</div>
	</div>
	<!-- end #page --> 
</div>
<div id="footer">
	<p>SOS-Maths<br />Images by <a href="http://fotogrph.com/" target="_blank">Fotogrph</a>. Design by <a href="http://www.freecsstemplates.org/" rel="nofollow" target="_blank">FreeCSSTemplates.org</a>.</p>
</div>
<!-- end #footer -->
</body>
</html>
