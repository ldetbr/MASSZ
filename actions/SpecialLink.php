<?php

namespace Modules\MASSZ\Actions;
use CControllerResponseData;
use CController;
# O uso dessa classe é opcional, esta sendo usada nesse exemplo
# Using this class is optional, it is being used in this example
use CWebUser;
class SpecialLink extends CController
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
	  # Pegando os dados do usuário logado
      # Getting the logged in user's data
	  # Requer o uso da classe CWebUser
      # Requires the use of the CWebUser class
	  $usuario = CWebUser::$data;
	  $link = 'https://'. $url . '/';
	  # Verifica se o usuário é superadmin para que libere o acesso ao sistema
	  # Pode ser alterado para 2 o valor caso queira reduzir para administrator o acesso ao sistema
	  # Check if the user is a superadmin to allow access to the system
      # Can be changed to value 2 if you want to reduce access to administrator instead of superadmin
	  if ($usuario['type'] != 3) {
		  echo 'Você não tem permissão para acessar esse sistema';
	  }
	  else {	  
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
        }  
	   exit();
	}
}
