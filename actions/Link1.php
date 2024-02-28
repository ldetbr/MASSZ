<?php

namespace Modules\MASSZ\Actions;


use CControllerResponseData;
use CController;
# O nome da classe pode ser alterado conforme necessidade
# Class name can be changed as needed
class Link1 extends CController
{
	public function init(): void {$this->disableCsrfValidation();}
	protected function checkInput(): bool {return true;}
	protected function checkPermissions(): bool {return true;}
	protected function doAction(): void
	{
		require_once '/usr/share/zabbix/include/config.inc.php';
		require_once '/usr/share/zabbix/include/page_header.php';
		# Caso sua aplicação não fique hospedada no mesmo servidor que o frontend do zabbix alterar o valor dessa variavel
		# If your application is not hosted on the same server as the Zabbix frontend, change the value of this variable
		$url = $_SERVER['HTTP_HOST'];
		# Essa variavel aponta para a url da sua aplicação
		# This variable points to the URL of your application
		$link = 'https://'. $url . '/';
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
