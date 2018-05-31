(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(window).load(function(){
		$.getJSON( plugin_dir + "/includes/all_coins_data.json", function( data ) {
			$.each( data, function( key, val ) {
				$("#wprs_cce_coin_list").append( $('<option>', {value: key + ':' + val['FullName'], text: val['FullName']}) );
			});

			var values= $('#wprs_cce_coin_selected');
			if( values.length ) {
				var coin_list = values.val();
				if(coin_list) {
					$.each(coin_list.split(","), function(i,e){
						$("#wprs_cce_coin_list option[value='" + e + "']").prop("selected", true);
					});
				}
			}
			
			$("#wprs_cce_coin_list").chosen({
				width: '98%',
				no_results_text: "Unrecognized Coin!"
			}); 
		});
	});

})( jQuery );
