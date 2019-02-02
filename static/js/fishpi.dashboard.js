var api = location.protocol.concat("//").concat(window.location.hostname).concat("/api");
var OriginalWidth;
var OriginalHeight;
var widgets = [];
var timer = 0
var serviceStatus = "offline"

/* Document Ready */
$( document ).ready(function() {
	setupClock();
	fetchWidgets()	
	reloadOnIdle();
});


function refresh(){
	
	jQuery('.reload-icon').removeClass('fa-redo-alt');
	jQuery('.reload-icon').addClass('fa-spinner fa-spin');
	
	history.go(0)
	
	
}

function reloadOnIdle(){
	
	$( "body" ).mousemove(function( event ) {
	 timer=0
	});
	
	window.setInterval(function(){
		timer=timer+1;
		if(timer>3600){
			location.reload(); 
		}
	}, 1000);
}

/* Fetch all Widgets from API */
function fetchWidgets(){
	
	jQuery.ajax({
		type: 'GET',
		url: api + '/widgets/getWidgets/',
		dataType: 'json', 
		success: function(data) {
			jQuery.each(data, function(index, widgetSetup) {
				i = new widget(widgetSetup)
				widgets.push(i);
			});
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
		}
	});	
}




var socket = $.atmosphere;
var secondsSinceLoad = 0;



function ToDoubleDigits(n, width, z) {
  z = z || '0';
  n = n + '';
  return n.length >= 2 ? n : new Array(2 - n.length + 1).join(z) + n;
}

function setupClock() {
	var today = new Date();

	$("div.clock").html(today.getHours() + ":" + ToDoubleDigits(today.getMinutes()));
	$("div.date").html(today.toDateString());
	
	secondsSinceLoad++;

	setTimeout(function(){ setupClock() }, 1000);
}


