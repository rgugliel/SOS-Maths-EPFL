function positionFixedDiv( )
{
    $( 'studentFixed' ).style.left = ( getElementTopLeft( 'student_searchResults' ).left + 460 ) + 'px';
}

window.onload = function( )
{
    positionFixedDiv( );
}

window.onresize = function( )
{
    positionFixedDiv( );
}

function repetiteurGetInfos( userId )
{
	new Ajax.Request( 'resources/ajax/get.php?rdm=' + Math.random( ) + '&do=repetiteurInfos&userId=' + userId + '&y=' + year + '&t=' + term,
	{
		method : 'get',
		onSuccess: function( xhr ){
			var error = xhr.responseXML.getElementsByTagName( 'error' );
			if( error.length ) // erreur
			{
				 $( 'studentFixed' ).innerHTML = error[ 0 ].firstChild.data
				return ;
			}
			
			var responseXML = xhr.responseXML;
			$( 'studentFixed' ).innerHTML = '';
			
			// ------------------------------------------------
			// pas d'erreurs ici, on traite
			var userName = responseXML.getElementsByTagName('name')[0].firstChild.data;
			var userId = responseXML.getElementsByTagName('id')[0].firstChild.data;
			
			// nom du répétiteur
			$( 'studentFixed' ).appendChild( Builder.node( 'div', { style: 'font-weight: bold;' }, userName ) );
			
			// --------------------------------------------------------
			// endroits
			$( 'studentFixed' ).appendChild( Builder.node( 'div', { style: 'font-weight: bold; margin-top: 10px;' }, 'Cours enseignés' ) );
			
			var levels = responseXML.getElementsByTagName( 'level' );
			var levelsCount = levels.length;
			
			var div = Builder.node( 'div', { style: 'overflow: auto; max-height: 200px;' } );
			
			for( var i = 0; i < levelsCount; i++ )
			{
				div.appendChild( Builder.node( 'span', { }, levels[i].getElementsByTagName( 'title' )[0].firstChild.data ) );
				
				var subjects = levels[i].getElementsByTagName( 'subject' );
				var subjectsCount = subjects.length;
				
				var ul = Builder.node( 'ul' );
				for( var j = 0; j < subjectsCount; j++ )
					ul.appendChild( Builder.node( 'li', { }, subjects[j].firstChild.data ) );
				
				div.appendChild( ul );
			}
			$( 'studentFixed' ).appendChild( div );
			
			// --------------------------------------------------------
			// endroits
			
			var places = responseXML.getElementsByTagName( 'place' );
			var placesCount = places.length;
			
			if( placesCount == 1 )
			{
				$( 'studentFixed' ).appendChild( Builder.node( 'div', { style: 'font-weight: bold; margin-top: 20px;' }, [ 'Lieu d\'enseignement: ', Builder.node( 'span', { style: 'font-weight: normal;' }, places[0].firstChild.data )] ) );
			}
			else
			{
				$( 'studentFixed' ).appendChild( Builder.node( 'div', { style: 'font-weight: bold; margin-top: 20px;' }, 'Lieux d\'enseignement' ) );
				
				var ul = Builder.node( 'ul' );
				for( var j = 0; j < placesCount; j++ )
					ul.appendChild( Builder.node( 'li', { }, places[j].firstChild.data ) );
				$( 'studentFixed' ).appendChild( ul );
			}
			
			// lien pour écrire
			$( 'studentFixed' ).appendChild( Builder.node( 'a', { href: 'index.php?p=stu&do=write&id=' + userId, target: '_blank' }, 'Ecrire à ' + userName ) );
	},
	
	onFailure: function( ) {  $( 'studentFixed' ).innerHTML = 'Erreur lors de la récupération des données.'; }
	} );
}
