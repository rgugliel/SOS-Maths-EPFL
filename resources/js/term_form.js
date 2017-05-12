/*
 * Fonctions communes à student et inscription semestrielle
 */

function availabilityDay_rowOnClick( rowId )
{
	var checkboxs = $( rowId ).getElementsByTagName( 'input' );
	var checkboxsCount = checkboxs.length;
	
	var checkedCount = 0;
	for( var i = 0; i < checkboxsCount; i++ )
	{
		if( checkboxs[i].checked )
			checkedCount++;
	}
	
	var toCheck = true;
	if( checkedCount == checkboxsCount ) // si tous els checkos de la ligne sont checkés --> on décoche
		toCheck = false;
		
	for( var i = 0; i < checkboxsCount; i++ )
		checkboxs[i].checked = toCheck;
}

function availabilityDay_colOnClick( period )
{
	var days = new Array( 'lu', 'ma', 'me', 'je', 've', 'sa', 'di' );
	
	var checkedCount = 0;
	for( var i = 0; i < 7; i++ )
	{
		if( $( days[i] + '-' + period ).checked )
			checkedCount++;
	}
	
	var toCheck = true;
	if( checkedCount == 7 ) // si tous els checkos de la colonne sont checkés --> on décoche
		toCheck = false;
		
	for( var i = 0; i < 7; i++ )
		$( days[i] + '-' + period ).checked = toCheck;
}

function fee_onClick( )
{
	var checkboxs = $( 'feeRow' ).getElementsByTagName( 'input' );
	var checkboxsCount = checkboxs.length;
	
	var checkboxsMin = -1;
	var checkboxsMax = checkboxsCount;
	
	for( var i = 0; i < checkboxsCount; i++ )
	{
		if( checkboxs[i].checked )
		{
			if( checkboxsMin == -1 )
				checkboxsMin = i;
				
			checkboxsMax = i;
		}
	}
	
	if( checkboxsMin != -1 )
	{
		for( var i = checkboxsMin; i <= checkboxsMax; i++ )
			checkboxs[i].checked = true;
	}
}

/*
 * Vérifie que si l'on a choisit une des matières de gymnase (ou uni), on ne décoche pas gymnase (ou uni)
 */
function checkLevel( index )
{
	var opts = $( 'teachingLevel-' + index + 'Opts' ).getElementsByTagName( 'input' );
	var optsCount = opts.length;
	var checked = false;
	
	for( var i = 0; i < optsCount; i++ )
	{
		if( opts[i].checked )
		{
			checked = true;
			break;
		}
	}
	
	if( checked )
		$( 'teachingLevel-' + index ).checked = true;
}
