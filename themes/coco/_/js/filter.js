/** ----------- SORTING ACTIONS --------------------------------- */

var delay = 350;

function makeKey( key ) { return key + '-key'; }

function makeValue( key ) { return key + '-value'; }

function makeData( key ) { return 'data-' + key; }

function makeHidden( key ) { return key + '-hidden'; }

function transitionIn( index, set, next ) {
	set.removeClass( makeHidden( index ) );
	if ( next !== undefined ) { next(); }

	// set.fadeIn( delay, function( ) {
	// 	set.removeClass( makeHidden( index ) );
	// 	if ( next !== undefined ) { next(); }
	// });
}

function transitionOut( index, set, next ) {
	set.addClass( makeHidden( index ) );
	if ( next !== undefined ) { next(); }


	// set.fadeOut( delay, function() {
	// 	set.addClass( makeHidden( index ) );
	// 	if ( next !== undefined ) { next(); }
	// });
}

function filter(index, key, set) {
	var inSet = set.filter( function( i, element ) {
		var matches = $( this ).data( makeValue( index ) ).split(',');

		return matches.reduce( function( a,b ) { return a || (key.trim() === b); }, false);
	});

	var outSet = set.filter( function( i, element ) {
		var matches = $( this ).data( makeValue( index ) ).split(',');

		return matches.reduce( function( a,b ) { return a && (key.trim() !== b); }, true);
	});

	if ( outSet.length ) {

		transitionOut( index, outSet, function() {
			transitionIn( index, inSet );
		});

	} else if ( inSet.length ) {

		transitionIn( index, inSet );

	}
}

function activate( index, keyObject ) {
	$('*[' + makeData( makeKey( index ) ) + ']').removeClass('active');
	$('*['+ makeData( makeKey( index ) ) +'="' + ( keyObject.data( makeKey( index ) ) ) + '"]').addClass('active');
	keyObject.addClass('active');
}


$(document).ready( function() {

	[ 'size', 'designer','sort' ].forEach( function( index ) {
		$('*['+ makeData( makeKey( index ) )+']').on('click', function() {

			activate( index, $(this) );

			filter( index, $(this).data( makeKey( index ) ), $('*['+ makeData( makeValue( index ) ) +']') );

		});
	});


	size();

	$( window ).once('resize', size);

});


function size() {

	var max = -Infinity;

	$('.product-card').each( function( ) {

		var l = $( this ).outerHeight();

		max = ( l > max ) ? l : max;

	}).outerHeight( max );

}