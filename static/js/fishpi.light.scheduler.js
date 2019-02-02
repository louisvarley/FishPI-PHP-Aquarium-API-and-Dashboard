
function lightScheduler(channel){
	$('#box').modal('show');
	$('.modal-body').empty();
	chart = $('<div id="channel_editor"></div>').appendTo($('.modal-body')) 
	
	$('.modal-title').html('Channel ' + channel + ' scheduler')
	
	jQuery.ajax({
		type: 'GET',
		url: api + '/lighting/getLightingSchedule/?channel='+channel,
		dataType: 'json', 
		success: function(data) {
			
			Highcharts.theme = {
				colors: ['#2076b0', '#2076b0', '#2076b0', '#2076b0', '#2076b0', '#2076b0', 
						 '#2076b0', '#2076b0', '#2076b0'],
				chart: {
					backgroundColor: '#fff',
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
			var schedules = []
			
			jQuery.each(data['response'], function(time, schedule) {
				times.push(time)
				schedules.push(schedule)
			})
			

			var chart = new Highcharts.Chart({

				chart: {
					renderTo: 'channel_editor',
					animation: true,
				},
				
				title: {
					text: '',
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
					}
				},
				yAxis: {
					min: 0,
					max: 100,
					gridLineWidth: 0,
					labels: {
						style: {
							color: '#666666',
							fontSize: '20px',
						}
					},
					title:{
						text: 'Intensity (%)',
						style: {
							color: '#666666',
							fontSize: '20px',
						}						
					}
				},
				credits: {
					enabled: false
				},
				plotOptions: {
					series: {
						point: {
							events: {
								drag: function (e) {
									if (e.newY > 100) {
										this.y = 100;
										return false;
									}
									if (e.newY < 0) {
										this.y = 0;
										return false;
									}									

									$('#drag').html(
										'Dragging <b>' + this.series.name + '</b>, <b>' + this.category + '</b> to <b>' + Highcharts.numberFormat(e.y, 2) + '</b>');
								},
								drop: function () {
									saveLight(chart.series[0].data,channel);
								}
							}
						},
						stickyTracking: false,
						marker: {
							enabled: true,
							symbol: 'circle',
							radius: 10
						}
					},

					column: {
						stacking: 'normal'
					},
					line: {
						cursor: 'ns-resize'
					}
				},

				tooltip: {
					pointFormat: "Intensity: {point.y:.0f} %",
					backgroundColor: null,
					borderWidth: 0,
					 crosshairs: true,
					shadow: true,
					useHTML: true,
					style: {
						padding: 0,
						fontSize: "20px",
					    color: "#666666",
					}
				},

				series: [{
					data: schedules,
					type: 'spline',
					showInLegend: false,
					name: "",
					style: {
							color: '#efefef',
							fontSize: '20px',
						}	,
					draggableY: true,
					dragMinY: 0,
					dragMaxY: 100,
				}]

			});	 
			
				
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(errorThrown);
		}
	});			

}




function saveLight(data,channel){
		var schedule = new Object();
		jQuery.each(data, function(key,d) {
			schedule[d['category']] = Math.round((d['y']),2)
		})
			
		saveBack = JSON.stringify(schedule);
		$.ajax( api + '/lighting/setLightingSchedule?channel='+channel+'&schedule='+encodeURI(saveBack))
		  .done(function( data ) {
		 });
	
}

