
function epochHour(t) {
  const dt = new Date(t);
  const hr = dt.getUTCHours();
  const m = "0" + dt.getUTCMinutes();
  
  return hr
}

function temperaturelog(){
	$('#box').modal('show');
	$('.modal-body').empty();
	chart = $('<div id="temperature_log"></div>').appendTo($('.modal-body'))
	$('<h2 class="storm-intensity">Temperature</h2>').appendTo($('.modal-body'));
	$('<input id="temperature" data-slider-tooltip="hide" data-slider-ticks="[23,24,25,26,27,28,29,30,31]" data-slider-id="ex1Slider" type="text" data-slider-min="0" data-slider-max="20" data-slider-step="1" data-slider-value="14"/>').appendTo($('.modal-body'));
		
	
	$('#temperature').slider({
		tooltip: 'never',
		min: 23,
		max: 31,
		value: 24,
		formatter: function(value) {
			return value + ' C';
		}
	}).on("slide", function(slider) {
		timeChange(slider.value);
	}).click("slide", function(slider) {
		timeChange(slider.value)
	});
	
	
	$('.modal-title').html('Temperature Log')
	
	jQuery.ajax({
		type: 'GET',
		url: api + '/temperature/getTemperatureLog',
		dataType: 'json', 
		success: function(data) {

			Highcharts.theme = {
				colors: ['#2076b0', '#2076b0', '#2076b0', '#2076b0', '#2076b0', '#2076b0', 
						 '#2076b0', '#2076b0', '#2076b0'],
				chart: {
					backgroundColor: '#2076b0',
				},
				title: {
					style: {
						color: '#666666',
						font: 'bold 30px "Trebuchet MS", Verdana, sans-serif'
					}
				},
				subtitle: {
					style: {
						color: '#666666',
						font: 'bold 12px "Trebuchet MS", Verdana, sans-serif'
					}
				},

				legend: {
					itemStyle: {
						color: '#666666'
					},
					itemHoverStyle:{
						color: '#666666'
					}   
				}
			};

			// Apply the theme
			Highcharts.setOptions(Highcharts.theme);
		
			var times = []
			var temperature = []
			var d = new Date();
			var nowHour = d.getHours();
			
			jQuery.each(data['day'], function(i, log) {

				temp_c = log['temp_c']
				time = i;
				
				if(i <= nowHour){
					times.push(time)
					temperature.push(temp_c)
				}else{
					times.push(time)
					temperature.push(0)
				}
			})
			


			var chart = new Highcharts.Chart({

				chart: {
					renderTo: 'temperature_log',
					type: 'area',
					animation: true,
				},
				
				title: {
					text: 'Aquarium Temperature',
					style: {
						color: '#666666',
						fontSize: '20px'
					}
				},

				xAxis: {
					categories: times,
					labels: {
						style: {
							color: '#666666',
							fontSize: '20px',
						}
					},
					plotLines: [{
					  color: '#2076b0',
					  width: 7,
					  value: nowHour // Need to set this probably as a var.
					}],					
					title:{
						text: 'Hour',
						style: {
							color: '#666666',
							fontSize: '20px',
						}						
					}
				},
				yAxis: {
					min: 20,
					max: 32,					
					gridLineWidth: 1,
					
					labels: {
						style: {
							color: '#666666',
							fontSize: '20px',
						}
					},
					title:{
						text: 'Degree C',
						style: {
							color: '#666666',
							fontSize: '20px',
						}						
					}
				},
				credits: {
					enabled: false
				},


				series: [{
					data: temperature,
					type: 'area',
					showInLegend: false,
					name: "",
					style: {
							color: '#efefef',
							fontSize: '20px',
						}	,
				}]

			});	 
			
				
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(errorThrown);
		}
	});			

}




var element, scale, datasetIndex, index, value


function getElement () {
	var e = d3.event.sourceEvent
	element = chartInstance.getElementAtEvent(e)[0]
  scale = element['_yScale'].id
}


function updateData () {
	var e = d3.event.sourceEvent
  datasetIndex = element['_datasetIndex']
  index = element['_index']
  value = chartInstance.scales[scale].getValueForPixel(e.clientY)
  chartInstance.data.datasets[datasetIndex].data[index] = value
  chartInstance.update(0)
}

function callback () {
  return alert('The new value is: ' + value)
}

function saveChanges(data,channel){
		var schedule = new Object();
		jQuery.each(data, function(key,d) {
			schedule[d['category']] = Math.round((d['y']),2)
			})
			
		saveBack = JSON.stringify(schedule);
		$.post( api + '/lighting/setLightingSchedule/?channel='+channel, { schedule: saveBack })
		  .done(function( data ) {
			
		 });
	
}

