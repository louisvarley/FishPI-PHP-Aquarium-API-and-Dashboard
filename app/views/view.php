<?php
/**
 * view.php
 *
 * Standard view class for sending requested routes, to a view and loading a controller
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage Views
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */


class view {
	
	/* Before Content Hook */
	public function pageBeforeContent(){}
	
	/* After Content Hook */
	public function pageAfterContent(){}
	
	/* Before Render Event */
	public function beforeRender(){}

	/* After Render Event */
	public function afterRender(){}
	
	/* Renders the view to user using this order of which page components are rendered */
	public function render(){
		
		/* Before any Rendering */
		$this->beforeRender();
	
		/* Load the Default Include Files */
		$this->defaultIncludes();
		
		/* Load the Default Inline */
		$this->defaultInlines();
		
		/* Load the Head */
		$this->pageHead();
		
		/* Load the Body */
		$this->pageBody();	

		/* Load the Footer */
		$this->pagefoot();
		
		/* After any Rendering */
		$this->afterRender();		
		
	}
	
	/* Default Included CSS and JS Files, saved to $this->includeCSS or $this->includeJS */
	/* Change the Priority to a lower number to have them loaded later */
	public function defaultIncludes(){
		
		$this->addIncludeJS('https://code.jquery.com/jquery-3.3.1.min.js','jQuery',1);

		$this->addIncludeJS('//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js','Bootstrap',2);

		$this->addIncludeJS('https://cdnjs.cloudflare.com/ajax/libs/howler/2.0.15/howler.min.js','Howler',1);

		$this->addIncludeCSS('/static/css/fontAwesome/css/all.min.css');
		
		$this->addIncludeCSS('//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css');

		$this->addIncludeJS('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.2.3/bootstrap-slider.js');				
		
		$this->addIncludeCSS('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.2.3/css/bootstrap-slider.min.css',null,1);
				
		$this->addIncludeCSS('/static/css/style.css');
	}
	
	/* Default Inline CSS and JS Files, extend this function to remove these or access $this->inlineCSS or $this->inlineJS to remove or includeInlineJS, includeInlineCSS to add */
	public function defaultInlines(){}
	
	/* Include Inline JS */
	function addInlineJS($script,$comment=null,$priority=9999){$this->inlineJS[] = array('script'=>$script,'comment'=>$comment,'priority'=>$priority);}
	
	/* Include Inline CSS */
	function addInlineCSS($style,$comment=null,$priority=9999){$this->inlineCSS[] = array('style'=>$style,'comment'=>$comment,'priority'=>$priority);}
	
	/* Include a JS file */
	function addIncludeJS($uri,$comment=null,$priority=9999){$this->includeJS[] = array('file'=>$uri,'comment'=>$comment,'priority'=>$priority);}
	
	/* Include a CSS file */
	function addIncludeCSS($uri,$comment=null,$priority=9999){$this->includeCSS[] = array('file'=>$uri,'comment'=>$comment,'priority'=>$priority);}
	
	/* Pulls a route title from route and uses as page title */
	public function pageTitle(){
		if($this->pageTitle){
			return $this->pageTitle;
		}else{
			return _DEFAULT_PAGE_TITLE;
		}
		
	}

	/* Products the Body Contents using Page Content */
	public function pageBody(){
		?> <div id="page-content page-<?php echo route::uri() ?>"> <?php
		echo $this->pageContent();
		?> </div> <?php	
	}
	
	/* A default page contents, this should be overwritten as a minimum by extending views */
	public function pageContent(){
		?>No Content Found<?php
	}
		
	/* Head of this Standard View */
	public function pageHead(){
		
		?>
		<!DOCTYPE html>
		<html lang="en">
			<head>
				<meta charset="UTF-8">
				<title><?php echo $this->pageTitle(); ?></title>
				<meta name="viewport" content="width=device-width, initial-scale=1.0"><?php 
					if(!empty($this->includeCSS)){
						echo "\n" . "\t"."\t"."\t"."\t". '<!-- Stylesheet Includes -->' . "\n";
						array_multisort(array_column($this->includeCSS, 'priority'), SORT_ASC, $this->includeCSS);
						foreach($this->includeCSS as $include){
							$priority='';
							if(strlen($include['comment'])>0){echo "\t"."\t"."\t"."\t". '<!-- ' . $include['comment'] . ' -->' . "\n";}
							if(isset($include['file'])){echo "\t"."\t"."\t"."\t".'<link' . $priority .' rel="stylesheet" href="' . $include['file'] .'?v=' . rand(0,1000) . '">' . "\n";}
						}
					}

					if(!empty($this->includeJS)){
						array_multisort(array_column($this->includeJS, 'priority'), SORT_ASC, $this->includeJS);
						echo "\n" . "\t"."\t"."\t"."\t". '<!-- Javascript Includes -->' . "\n";
						foreach($this->includeJS as $include){
							$priority='';	
							if(strlen($include['comment'])>0){echo "\t"."\t"."\t"."\t". '<!-- ' . $include['comment'] . ' -->' . "\n";}
							if(isset($include['file'])){echo "\t"."\t"."\t"."\t". '<script' . $priority .' src="' . $include['file'] . '"></script>' . "\n";}
						}
					}
				
					if(!empty($this->inlineCSS)){
						array_multisort(array_column($this->inlineCSS, 'priority'), SORT_ASC, $this->inlineCSS);
						echo "\n";
						foreach(array_reverse($this->inlineCSS) as $inline){
							if(isset($inline['comment'])){echo "\t"."\t"."\t"."\t".'<!-- ' . $inline['comment'] . ' -->' . "\n";}
							if(isset($inline['style'])){echo "\t"."\t"."\t"."\t".'<style>'.$inline['style']. '</style>' . "\n";}
							
						}
					}
				
					if(!empty($this->inlineJS)){
						echo "\n";
						foreach(array_reverse($this->inlineJS) as $inline){
							if(isset($inline['comment'])){echo "\t"."\t"."\t"."\t".'<!-- ' . $inline['comment'] . ' -->' . "\n";}
							if(isset($inline['script'])){echo "\t"."\t"."\t"."\t <script>".$inline['script']. "</script>\n";}
						}
					}
			?>			</head>
		<body>
		<?php
		
	}


	/* Bottom of this Standard View */
	public function pageFoot(){
				?>
			</body>
		</html>
		<?php
		
	}
	
	/* Throw an error (red) message on page load */
	public function throwError($error){$this->errorMessage = $error;}

	/* Throw a notice(blue) message on page load */	
	public function throwNotice($notice){$this->noticeMessage = $notice;}

	/* Throw a warning (yellow) message on page load */	
	public function throwWarning($warning){$this->warningMessage = $warning;}

	/* Throw an success (green) message on page load */	
	public function throwSuccess($success){$this->successMessage = $success;}	
	
	/* Displays Any Notices or Errors */
	public function pageNoticeError(){
	
		if(!empty($this->errorMessage)){
			?>
			<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><?php echo $this->errorMessage; ?></div>
			<?php }

		if(!empty($this->noticeMessage)){
			?>
			<div class="alert alert-info"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><?php echo $this->noticeMessage; ?></div>
			<?php }
	
		if(!empty($this->warningMessage)){
			?>
			<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><?php echo $this->warningMessage; ?></div>
			<?php }
	
		if(!empty($this->successMessage)){
			?>
			<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><?php echo $this->successMessage; ?></div>
			<?php }
	}	
	
	
}
