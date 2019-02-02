

function waterChangeLog(type){
	
	if(type==null)type="HOUR";
	
	if(type=="HOUR") endPoint = "getFlowTodayByHour";
	if(type=="DAY") endPoint = "getFlowWeekByDay";
	
	$('#box').modal('show');
	$('.modal-body').empty();
	chart = $('<div id="temperature_log"></div>').appendTo($('.modal-body')) 
	
	button1 = $('<button ' + (type=="HOUR" ? 'disabled' : '') + ' style="margin-right:10px" class="btn btn-primary" onclick="waterChangeLog(\'HOUR\')">Hourly</button>').appendTo($('.modal-body')) 
	button2 = $('<button ' + (type=="DAY" ? 'disabled' : '') + ' style="margin-right:10px" class="btn btn-primary" onclick="waterChangeLog(\'DAY\')">Daily</button>').appendTo($('.modal-body')) 
	
	$('.modal-title').html('Water Change Log')
	
	jQuery.ajax({
		type: 'GET',
		url: api + '/water/' + endPoint,
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
			var litres = []
			var litresLow = 0;
			var litresHigh = 0;
			var d = new Date();			
			var nowHour = d.getHours();
			
			jQuery.each(data["response"], function(i, log) {
				
				cLitres = log['litres'];
				
				if(litresLow==0 || cLitres < litresLow){
					litresLow = cLitres;
				}

				if(litresHigh==0 || cLitres > litresHigh){
					litresHigh = cLitres;
				}				
				
				thisLitres = Math.round(log['litres'],0);
				if(log['litres'] > 0 && log['litres'] < 1) thisLitres = 1;
				//thisLitres = parseFloat(log['litres']).toFixed(1)
				thisHour = i;
				times.push(thisHour)
				litres.push(thisLitres)
			})
			
			var chart = new Highcharts.Chart({

				chart: {
					renderTo: 'temperature_log',
					type: 'area',
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
					allowDecimals: true,
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
				},
				yAxis: {
					min: 0,
					max: litresHigh + 5,					
					gridLineWidth: 1,
					
					labels: {
						style: {
							color: '#666666',
							fontSize: '20px',
						}
					},
					title:{
						text: 'Litres',
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
					areaspline: {
						fillOpacity: 0.5,
						pointPlacement: 'between', 
					},					
					series: {
						stickyTracking: false,
						marker: {
							enabled: true,
							symbol: 'circle',
							radius: 5
						}
					},

					column: {
						stacking: 'normal'
					},
					line: {
						cursor: 'ns-resize'
					}
				},

				series: [{
					data: litres,
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

