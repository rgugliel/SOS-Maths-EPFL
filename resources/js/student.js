function positionFixedDiv( )
{
    $( 'studentFixed' ).style.left = ( getElementTopLeft( 'studentContent' ).left + 510 ) + 'px';
}

window.onload = function( )
{
    positionFixedDiv( );
}

window.onresize = function( )
{
    positionFixedDiv( );
}

function formOnChange( )
{
	sendFormValues( getFormValues( ) );
}

function getFormValues( )
{
	var queryPost = '';
	var queryPostTemp = '';
	
	// ---------------------------------------------
	// sujets d'enseignement
	teachingSubject = new Array( );
	var divs = $('teachingSubjects').getElementsByTagName( 'input' );
	var divsCount = divs.length;
	queryPostTemp = '';
	for( var i = 0; i < divsCount; i++ )
	{
		if( divs[i].checked )
		{
			teachingSubject.push( divs[i].value );
			queryPostTemp += ( queryPostTemp == '' ? '' : ',' ) + divs[i].value;
		}
	}
	queryPost += 'teachingSubjects=' + queryPostTemp;
	$('subjectInfo').innerHTML = teachingSubject.length == 0 ? 'Choisissez au moins un cours' : ( 'Cours choisis: '  + teachingSubject.length );
		
	// ---------------------------------------------
	// langue
	languages = new Array( );
	var divs = $('languages').getElementsByTagName( 'input' );
	var divsCount = divs.length;
	queryPostTemp = '';
	for( var i = 0; i < divsCount; i++ )
	{
		if( divs[i].checked )
		{
			languages.push( divs[i].value );
			queryPostTemp += ( queryPostTemp == '' ? '' : ',' ) + divs[i].value;
		}
	}
	queryPost += '&language=' + queryPostTemp;
	$('languageInfo').innerHTML = languages.length == 0 ? 'Choisissez au moins une langue' : languages.join( ', ' );
	
	// ---------------------------------------------
	// dispos
	availabilities = new Array( );
	var divs = $('availabilities').getElementsByTagName( 'input' );
	var divsCount = divs.length;
	queryPostTemp = '';
	for( var i = 0; i < divsCount; i++ )
	{
		if( divs[i].checked )
		{
			availabilities.push( divs[i].value );
			queryPostTemp += ( queryPostTemp == '' ? '' : ',' ) + divs[i].name;
		}
	}
	queryPost += '&availibilityDay=' + queryPostTemp;
	$('availabilitiesInfo').innerHTML = availabilities.length == 0 ? 'Choisissez au moins une période' : availabilities.length;
	
	// ---------------------------------------------
	// levelStudies
	levelStudies = new Array( );
	var divs = $('levelStudies').getElementsByTagName( 'input' );
	var divsCount = divs.length;
	queryPostTemp = '';
	for( var i = 0; i < divsCount; i++ )
	{
		if( divs[i].checked )
		{
			levelStudies.push( divs[i].value );
			queryPostTemp += ( queryPostTemp == '' ? '' : ',' ) + divs[i].value;
		}
	}
	queryPost += '&levelStudies=' + queryPostTemp;
	
	// ---------------------------------------------
	// section
	section = new Array( );
	var divs = $('section').getElementsByTagName( 'input' );
	var divsCount = divs.length;
	queryPostTemp = '';
	for( var i = 0; i < divsCount; i++ )
	{
		if( divs[i].checked )
		{
			section.push( divs[i].value );
			queryPostTemp += ( queryPostTemp == '' ? '' : ',' ) + divs[i].value;
		}
	}
	queryPost += '&section=' + queryPostTemp;
	
	// ---------------------------------------------
	// semestre
	$( 'termInfo' ).innerHTML = $( 'term' ).options[ $( 'term' ).selectedIndex ].innerHTML;
	queryPost += '&termText=' + $( 'term' ).options[ $( 'term' ).selectedIndex ].value;
	
	return queryPost;
}

function sendFormValues( queryPost )
{
	new Ajax.Request( 'resources/ajax/get.php?rdm=' + Math.random( ) + '&do=repetiteurTermCount',
	{
		method : 'post',
		parameters: queryPost,
		onSuccess: function( xhr ){
			var error = xhr.responseXML.getElementsByTagName( 'error' );
			if( error.length ) // erreur
			{
				 $( 'countInfos' ).innerHTML = '?';
				return displayError( error[ 0 ].firstChild.data );
			}
			
			var count = xhr.responseXML.getElementsByTagName( 'count' );
			if( count.length ) // erreur
				$( 'countInfos' ).innerHTML = count[ 0 ].firstChild.data;
			else
				 $( 'countInfos' ).innerHTML = '?';
				
	},
	
	onFailure: function( ) {  $( 'countInfos' ).innerHTML = '?'; displayError( 'Erreur lors de la récupération des données.<br />' ); }
	} );
}
