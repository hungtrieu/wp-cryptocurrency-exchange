(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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

	var currentPrice = {};
	var socket = io.connect('https://streamer.cryptocompare.com/');
	//Format: {SubscriptionId}~{ExchangeName}~{FromSymbol}~{ToSymbol}
	//Use SubscriptionId 0 for TRADE, 2 for CURRENT, 5 for CURRENTAGG eg use key '5~CCCAGG~BTC~USD' to get aggregated data from the CCCAGG exchange 
	//Full Volume Format: 11~{FromSymbol} eg use '11~BTC' to get the full volume of BTC against all coin pairs
	//For aggregate quote updates use CCCAGG ags market

	var dataUnpack = function(message) {
		var data = CCC.CURRENT.unpack(message);

		var from = data['FROMSYMBOL'];
		var to = data['TOSYMBOL'];
		var fsym = CCC.STATIC.CURRENCY.getSymbol(from);
		var tsym = CCC.STATIC.CURRENCY.getSymbol(to);
		var pair = from + to;

		if (!currentPrice.hasOwnProperty(pair)) {
			currentPrice[pair] = {};
		}

		for (var key in data) {
			currentPrice[pair][key] = data[key];
		}

		if (currentPrice[pair]['LASTTRADEID']) {
			currentPrice[pair]['LASTTRADEID'] = parseInt(currentPrice[pair]['LASTTRADEID']).toFixed(0);
		}
		currentPrice[pair]['CHANGE24HOUR'] = CCC.convertValueToDisplay(tsym, (currentPrice[pair]['PRICE'] - currentPrice[pair]['OPEN24HOUR']));
		currentPrice[pair]['CHANGE24HOURPCT'] = ((currentPrice[pair]['PRICE'] - currentPrice[pair]['OPEN24HOUR']) / currentPrice[pair]['OPEN24HOUR'] * 100).toFixed(2) + "%";
		displayData(currentPrice[pair], from, tsym, fsym);
	};

	// var decorateWithFullVolume = function(message) {
	// 	var volData = CCC.FULLVOLUME.unpack(message);
	// 	var from = volData['SYMBOL'];
	// 	var to = 'USD';
	// 	var fsym = CCC.STATIC.CURRENCY.getSymbol(from);
	// 	var tsym = CCC.STATIC.CURRENCY.getSymbol(to);
	// 	var pair = from + to;

	// 	if (!currentPrice.hasOwnProperty(pair)) {
	// 		currentPrice[pair] = {};
	// 	}

	// 	currentPrice[pair]['FULLVOLUMEFROM'] = parseFloat(volData['FULLVOLUME']);
	// 	currentPrice[pair]['FULLVOLUMETO'] = ((currentPrice[pair]['FULLVOLUMEFROM'] - currentPrice[pair]['VOLUME24HOUR']) * currentPrice[pair]['PRICE']) + currentPrice[pair]['VOLUME24HOURTO'];
	// 	displayData(currentPrice[pair], from, tsym, fsym);
	// };

	var displayData = function(messageToDisplay, from, tsym, fsym) {
		var priceDirection = messageToDisplay.FLAGS;
		var fields = CCC.CURRENT.DISPLAY.FIELDS;
		var messageDisplay = '';

		for (var key in fields) {
			if (messageToDisplay[key]) {
				if (fields[key].Show) {
					
					switch (fields[key].Filter) {
						case 'String':
							messageDisplay = messageToDisplay[key];
							if( key == 'CHANGE24HOURPCT') {
								messageDisplay = '(' + messageDisplay + ')';
							} 
							$('.cryptocurrency-ticker__cur-' + key + '_' + from).text(messageDisplay);
							break;
						case 'Number':
							var symbol = fields[key].Symbol == 'TOSYMBOL' ? tsym : fsym;
							messageDisplay =  CCC.convertValueToDisplay(symbol, messageToDisplay[key]);
							if( key == 'CHANGE24HOURPCT') {
								messageDisplay = '(' + messageDisplay + ')';
							} 
							$('.cryptocurrency-ticker__cur-' + key + '_' + from).text(messageDisplay);
							
							break;
					}
				}
			}
		}

		if (priceDirection & 1) {
			$('.cryptocurrency-ticker__cur-PRICE_' + from).removeClass('price-down').addClass("price-up");
		}
		else if (priceDirection & 2) {
			$('.cryptocurrency-ticker__cur-PRICE_' + from).removeClass('price-up').addClass("price-down");
		}

		if (messageToDisplay['PRICE'] > messageToDisplay['OPEN24HOUR']) {
			$('.cryptocurrency-ticker__cur-CHANGE24HOURPCT_' + from).removeClass('daypct-down').addClass("daypct-up");
		}
		else if (messageToDisplay['PRICE'] < messageToDisplay['OPEN24HOUR']) {
			$('.cryptocurrency-ticker__cur-CHANGE24HOURPCT_' + from).removeClass('daypct-up').addClass("daypct-down");
		}
	};

	$(window).load(function(){
		var subscription = [];
		$('.cryptocurrency-ticker__subscribe').each( function() {
			var subscribe_ids = $(this).val().split(',');
			if( subscribe_ids.length ) {
				for( var i = 0; i < subscribe_ids.length; i++ ) {
					subscription.push( '5~CCCAGG~' + subscribe_ids[i] + '~USD' );
				}
			}
		});

		socket.emit('SubAdd', { subs: subscription });
		socket.on("m", function(message) {
			var messageType = message.substring(0, message.indexOf("~"));
			if (messageType == CCC.STATIC.TYPE.CURRENTAGG) {
				dataUnpack(message);
				return true;
			}
		});

		$('.cryptocurrency-ticker__data').webTicker({
			duplicate: true,
			startEmpty: false
		});

		$( '.cryptocurrency-ticker__item' ).fadeIn();
	});
	
})( jQuery );
