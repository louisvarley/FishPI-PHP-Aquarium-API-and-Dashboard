//default Storm Minutes
var minutes = 10
var mode = "storm"

function goStorm(){
	
	intensity = jQuery('#value-intensity').val()
	time = jQuery('#value-time').val()	
	
	if(mode=="rain"){
		intensity = 0;
	}
	
	jQuery.ajax({
		type: 'GET',
		url: api + '/weather/doStorm/?minutes='+time+'&intensity='+intensity,
		dataType: 'json', 
		success: function(data) {
			$('#box').modal('hide');
		}
	})
	
	$('#box').modal('hide');	
	
}

function stormMode(){
	
	mode = "storm"
	
	jQuery('.storm-intensity').show();
	jQuery("#rainMode").removeAttr("disabled");
	jQuery("#stormMode").attr("disabled","true");	
	jQuery('.row5').show();
	
}

function rainMode(){
	
	mode = "rain"	
	jQuery('.storm-intensity').hide();	
	jQuery("#rainMode").attr("disabled","true");
	jQuery("#stormMode").removeAttr("disabled");
	jQuery('#intensity').val(0);
	jQuery("#stormMode").removeAttr("disabled");	
	jQuery('.row5').hide();
	
}
	
function stormEditor(){
	$('#box').modal('show');
	
	$('.modal-body').empty();
	$('.modal-title').html('Create Weather Event')
	
	storm = $('<div style="margin:10px" id="stormeditor"></div>').appendTo($('.modal-body')) 
	container = $('<div class="container"></div>').appendTo(storm)
	
	row1 = $('<div class="row row1"></div>').appendTo(container)
	row2 = $('<div class="row row2"></div>').appendTo(container)	
	row3 = $('<div class="row row3"></div>').appendTo(container)	
	row4 = $('<div class="row row4"></div>').appendTo(container)	
	row5 = $('<div class="row row5"></div>').appendTo(container)	
	row6 = $('<div class="row row6"></div>').appendTo(container)	
	row7 = $('<div style="margin-top:40px" class="row row7"></div>').appendTo(container)	
	
	$('<button id="stormMode" onclick="stormMode()" type="button" style="margin-right:10px" disabled class="btn btn-weather btn-lg btn-info"><i class="fas fa-thunderstorm"></i> Storm</button>').appendTo(row1)
	$('<button id="rainMode" onclick="rainMode()" type="button" class="btn btn-weather  btn-lg btn-info"><i class="fas fa-cloud-rain"></i> Rain</button>').appendTo(row1)		
	
	$('<h2>Duration</h2>').appendTo(row2);
	$('<h3 id="label-time">10 Minutes</h3>').appendTo(row3);
	$('<h3 id="label-intensity">50%</h3>').appendTo(row5);
	$('<input id="time" data-slider-tooltip="hide" data-slider-ticks="[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]" data-slider-id="ex1Slider" type="text" data-slider-min="0" data-slider-max="20" data-slider-step="1" data-slider-value="14"/>').appendTo(row3);
	$('<h2 class="storm-intensity">Storm Intensity</h2>').appendTo(row4);
	$('<input id="intensity" data-slider-tooltip="hide" data-slider-ticks="[0,10,20,30,40,50,60,70,80,90,100]" data-slider-id="ex1Slider" type="text" data-slider-min="0" data-slider-max="20" data-slider-step="1" data-slider-value="14"/>').appendTo(row5);
	$('<button onclick="goStorm()" type="button" class="btn btn-weather  btn-lg btn-info"><i class="fas fa-bolt"></i> Go</button>').appendTo(row7)
	$('<input id="value-intensity" type="hidden" name="intensity">').appendTo(row7)
	$('<input id="value-time" type="hidden" name="time">').appendTo(row7)
	
	$('#time').slider({
		tooltip: 'never',
		min: 1,
		max: 20,
		value: 10,
		formatter: function(value) {
			return value + ' minutes';
		}
	}).on("slide", function(slider) {
		timeChange(slider.value);
	}).click("slide", function(slider) {
		timeChange(slider.value)
	});

	$('#intensity').slider({
		tooltip: 'never',
		min: 1,
		max: 100,
		value: 50,
		formatter: function(value) {
			return value + '%';
		}
	}).on("slide", function(slider) {
		intensityChange(slider.value);
	}).click("slide", function(slider) {
		intensityChange(slider.value)
	});

	function timeChange(val){
		jQuery('#value-time').val(val);
		jQuery('#label-time').html(val + " Minutes");
	}

	function intensityChange(val){
		jQuery('#value-intensity').val(val);
		jQuery('#label-intensity').html(val + "%");
	}

	$('.slider-handle').css('height','40px').css('width','40px').css('top','-10px');
	$('.slider.slider-horizontal').css('width','90%');
	$('.tooltip').css('display','none');

}
