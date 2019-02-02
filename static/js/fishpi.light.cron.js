
function lightCron(){
	
	jQuery.ajax({
		type: 'GET',
		url: api + '/lighting/setLightingCronState/?state=switch',
		dataType: 'json', 
		success: function(data) {
			state = data['response']['state'];
			jQuery('#widget-lighting-scheduler-widget-value').html(state);
			if(state=='on'){
				jQuery('#widget-lighting-scheduler').removeClass('widget-off').addClass('widget-on')
			}else{
				jQuery('#widget-lighting-scheduler').removeClass('widget-on').addClass('widget-off')
			}
			
			
		}
		
	})
	
}



