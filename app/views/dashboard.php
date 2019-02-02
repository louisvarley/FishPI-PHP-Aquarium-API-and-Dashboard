<?php
/**
 * root.php
 *
 * view class for root page
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage Views
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */


class viewDashboard extends view  {
	

	public function beforeRender(){
		
		$this->pageTitle = "FishPI Dashboard";

		$this->addIncludeJS('https://code.highcharts.com/highcharts.js');
		$this->addIncludeJS('https://rawgithub.com/highcharts/draggable-points/master/draggable-points.js');
		
		$this->addIncludeJS('/static/js/fishpi.widget.class.js');	
		$this->addIncludeJS('/static/js/fishpi.dashboard.js');
		$this->addIncludeJS('/static/js/fishpi.light.scheduler.js');
		$this->addIncludeJS('/static/js/fishpi.light.weather.js?v=' . rand(1,99));	
		$this->addIncludeJS('/static/js/fishpi.temperature.log.js?v=' . rand(1,99));	
		$this->addIncludeJS('/static/js/fishpi.light.cron.js');					
		$this->addIncludeJS('/static/js/fishpi.water.scheduler.js');		
		$this->addIncludeJS('/static/js/fishpi.water.flow.log.js');
		
	}	
	
	public function pageContent(){
		
		?>
		
		<div id="content">
			<div class="topbar">
				<div class="date">2015-01-01</div>		
				<div class="refresh reload-icon fas fa-redo-alt" onclick="refresh();"></div>
				<div class="clock">12:00</div>
			</div>
			<div class="tiles">
				<div id="left">
				</div>
				<div id="right">
				</div>
			</div>
			
			<div class="modal fade" id="box" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-xl" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title"></h4>
				  </div>
				  <div class="modal-body">
					...
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				  </div>
				</div>
			  </div>
			</div>		
		</div>
		<video id="video" autoplay></video>
		
		<?php
		
	}
	

}
