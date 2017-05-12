function expandReduce( id )
{
	if( document.getElementById( id ).style.display == 'none' )
	{
		Effect.BlindDown( id );
		document.getElementById( id + 'Img' ).src = 'images/reduce.gif';
	}
	else
	{
		Effect.BlindUp( id );
		document.getElementById( id + 'Img' ).src = 'images/expand.gif';
	}
}

function getElementTopLeft( id )
{
    var top = 0;
    var left = 0;
   	var ele = $( id );
   	
    while( ele.tagName != "BODY" )
    {
        top += ele.offsetTop;
        left += ele.offsetLeft;
        ele = ele.offsetParent;
    }
   
    return { top: top, left: left };
}

function displayError( text )
{
	$( 'infos' ).innerHTML = text;
	$( 'infos' ).className = 'errorBox';
	//Effect.Appear( 'infos' );
}