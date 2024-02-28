<?php
# This example is like Link 1 example
namespace Modules\MASSZ\Actions;
use CControllerResponseData;
use CController;

class Link4 extends CController
{
	public function init(): void {$this->disableCsrfValidation();}
	protected function checkInput(): bool {return true;}
	protected function checkPermissions(): bool {return true;}
	protected function doAction(): void
	{
	  require_once '/usr/share/zabbix/include/config.inc.php';
	  require_once '/usr/share/zabbix/include/page_header.php';
	  $url = $_SERVER['HTTP_HOST'];
	  $link = 'https://'. $url;  
      echo '
		<div class="container">
			<style>
				.container {
				position: relative;
				width: 100%;
				overflow: hidden;
				padding-top: 100%; /* 1:1 Aspect Ratio */
				} 
				.responsive-iframe {
					position: absolute;
					top: 0;
					left: 0;
					bottom: 0;
					right: 0;
					width: 100%;
					height: 100%;
					border: none;
				}
			</style>
			<iframe id="indicadores" class="responsive-iframe" src="'.$link.'"> </iframe>
		</div>
	  ';
	  exit();
	}
}
