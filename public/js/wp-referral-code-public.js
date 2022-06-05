// deleting tooltip
var btns = document.querySelectorAll( '#copy-btn' );
for (var i = 0; i < btns.length; i++) {
	btns[i].addEventListener( 'mouseleave', clearTooltip );
	btns[i].addEventListener( 'blur', clearTooltip );
}

function clearTooltip(e) {
	// JQuery( ".tooltiptext" ).html( msg )
	setTimeout(
		function () {
			jQuery( ".tooltiptext" ).removeClass( 'tooltip-visible' );
			jQuery( ".tooltiptext" ).html( '' );
		},
		1000
	);

	// e.currentTarget.removeAttribute('aria-label');
}

function showTooltip(elem, msg) {
	jQuery( ".tooltiptext" ).html( msg );
	jQuery( ".tooltiptext" ).addClass( 'tooltip-visible' );
}

function fallbackMessage(action) {
	var actionMsg = '';
	var actionKey = (action === 'cut' ? 'X' : 'C');
	if (/iPhone|iPad/i.test( navigator.userAgent )) {
		actionMsg = 'No support :(';
	} else if (/Mac/i.test( navigator.userAgent )) {
		actionMsg = 'Press ⌘-' + actionKey + ' to ' + action;
	} else {
		actionMsg = 'Press Ctrl-' + actionKey + ' to ' + action;
	}
	return actionMsg;
}


var clipboard = new ClipboardJS( '#copy-btn' );
clipboard.on(
	'success',
	function (e) {
		showTooltip( e.trigger, 'Copied!' );
	}
);
clipboard.on(
	'error',
	function (e) {
		showTooltip( e.trigger, fallbackMessage( e.action ) );
	}
);
