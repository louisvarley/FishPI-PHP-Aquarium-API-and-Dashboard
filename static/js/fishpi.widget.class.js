
/* Class for building widgets using an object returned from API */

class widget{

	constructor(widgetSetup) {
		
		this.widgetSetup = widgetSetup
		this.widget = this.generateWidget()
		this.insertWidgetIntoDOM();
		this.pollingSetup();
		
	}

	insertWidgetIntoDOM(){
		
		var widget = this.widget

		if(widget.attr('data-size') == 'large'){
			var appendTarget = jQuery('#right')
			$(widget).appendTo($('#right'));
		}
		
		if(widget.attr('data-size') == 'small'){
			$(widget).appendTo($('#left'));	
		}	
		
	}	
		
	/* Creates the Widgets Element */
	generateWidget(){
				
		var widgetSetup = this.widgetSetup		
		var dataString = ""
		var thisType
		
		for (var key in widgetSetup) {
			dataString = dataString + key + '="' + widgetSetup[key] + '" '
		}
		
		if(widgetSetup['data-size']=='large'){
			var widgetElement = $('<div id="'+widgetSetup['data-id']+'" class="widget widget-large col-sm-12 col-xs-6" '+dataString+'></div>')
		}
		
		if(widgetSetup['data-size']=='small'){
			var widgetElement = $('<div id="'+widgetSetup['data-id']+'" class="widget widget-small col-sm-6 col-xs-6" '+dataString+'></div>')
		}		
		
		var tile = $('<div class="tile"></div>').appendTo(widgetElement);
		
		if(widgetElement.attr("data-colour")){
			$(tile).css("background-color",widgetElement.attr("data-colour"))
		}
		
		tile.append('<h1>' + widgetElement.attr("data-title") + ' <i id="' + widgetElement.attr("data-id") + '-widget-icon" class="fas fa-' + widgetElement.attr("data-icon") + '"></i></h1>');
		tile.append('<div class="widget-bg-icon"><i id="' + widgetElement.attr("data-id") + '-widget-bg-icon" class="fas fa-' + widgetElement.attr("data-icon") + '"></i></div>');	
		
		var value = $('<div class="widget-value"></div>').appendTo(tile);
		value.append(widgetElement.attr("data-value").toString=="" ? '' : '<span id="' + widgetElement.attr("data-id") + '-widget-value" class="widget-value" >'+widgetElement.attr("data-value")+'</span>')
		
		
		if(!Object.is(widgetElement.attr("data-onclick"), undefined) && widgetElement.attr("data-onclick") != ""){
			this.setupClick(widgetElement);
		}		
		
		return widgetElement

	}


	pollingSetup(widget){
		
		widget = this.widget
		
		if(widget.attr('data-value-endpoint') !="" && widget.attr('data-poll-interval') !=""){

			var pollEvery = widget.attr("data-poll-interval")*1000
			
			var polljob = function(){ 
				
			jQuery.ajax({
					type: 'GET',
					url: api + widget.attr("data-value-endpoint"),
					dataType: 'json', 
					success: function(data) {
			
						var r = data;
			
						/* Grab New Value */
						var entity = widget.attr("data-value-endpoint-entity").replace(/^\/|\/$/g, '');
						var splitEntities = entity.split("/")						
						if(splitEntities.length > 1){
							for (var key in splitEntities) {
								data = (data[splitEntities[key]])
							}
						}
						
						var newVal = data;
						
						data = r;
						
						if(widget.attr("data-value-endpoint-colour")){
						
							/* Grab New Colour */
							var entity = widget.attr("data-value-endpoint-colour").replace(/^\/|\/$/g, '');
							var splitEntities = entity.split("/")						
							if(splitEntities.length > 1){
								for (var key in splitEntities) {
									data = (data[splitEntities[key]])
								}
							}	

							var newColour = data;	
							
				
							jQuery(widget).find(">:first-child").css("background-color",newColour)
							
						
						}
						
						if(widget.attr("data-round-value")){
							newVal = parseFloat(newVal).toFixed(widget.attr("data-round-value"));
							//newVal = Math.round((newVal),widget.attr("data-round-value"))
						}
						
						newVal = widget.attr('data-prefix') + newVal + widget.attr('data-suffix')
						
						$(widget).find('#'+widget.attr('data-id')+'-widget-value').html(newVal)
						
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
					
					}
				});
				
			}
			setInterval(polljob, pollEvery);	
			/* Also Run now */
			polljob();

			
		}
	}

	setupClick(widget){

		$(widget).click(function() {
			var onClickFunction = widget.attr('data-onclick')
			eval(onClickFunction);
		});
			
	}

	
}