/**
 * edit-timestamp.js
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package groups-woocommerce
 * @since groups-woocommerce 1.7.1
 */
jQuery(document).ready( function($) {

	/**
	 * Uncover eternity buttons.
	 */
	$('button.eternal').show();

	/**
	 * Void all timestamp fields on click.
	 */
	$('button.eternal').on('click',function(e){
		var id_prefix = $(this).val();
		$('#'+id_prefix+'_year').val('');
		$('#'+id_prefix+'_month').val('');
		$('#'+id_prefix+'_day').val('');
		$('#'+id_prefix+'_hour').val('');
		$('#'+id_prefix+'_minute').val('');
		$('#'+id_prefix+'_second').val('');
		e.preventDefault();
	});

	/**
	* Inhibit form submission when enter is pressed on any timestamp input
	* field or the eternal button.
	*/
	jQuery(".timestamp-field, button.eternal").keydown(function(e){
		if ( e.keyCode == 13 ) { // enter
			e.preventDefault();
			return false;
		}
	});
});
