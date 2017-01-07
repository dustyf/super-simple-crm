jQuery( document ).ready( function( $ ) {
	$( '#sscrm_form' ).submit( function( e ) {
		e.preventDefault();
		$( '#sscrm_form_container .grayed-out' ).show();
		$( '#sscrm_submit' ).prop( 'disabled', true );
		$.post(
			sscrm_args.ajax_url,
			$( '#sscrm_form' ).serialize(),
			function( data ) {
				$( '#sscrm_form_container .sending-message' ).hide();
				$( '#sscrm_form_container .done-message' ).show();
			}
		).fail( function( data ) {
			$( '#sscrm_form_container .sending-message' ).hide();
			$( '#sscrm_form_container .fail-message' ).show();
			console.log( data );
		} );
	} )
} );