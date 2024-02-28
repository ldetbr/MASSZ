<?php

namespace Modules\MASSZ\Actions;
use CControllerResponseData;
use CController;
# O uso dessa classe é opcional, esta sendo usada nesse exemplo
# Using this class is optional, it is being used in this example
use CWebUser;
# O uso dessa classe é opcional, esta sendo usada nesse exemplo
# Using this class is optional, it is being used in this example
use Redis;

# O nome da classe pode ser alterado conforme necessidade
# Class name can be changed as needed
class Link3 extends CController
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
	  # Passando dois parametros de dados da sessão do usuário para o sistema
	  # Passing two parameters of user session data to the system
	  $link = 'https://'. $url . '/?session=' . $usuario['name'] .' '. $usuario['surname'];
      # Gravar os dados no banco de dados do Redis rodando no mesmo servidor da aplicação para uso posterior
	  # Requer a declaração da classe Redis
	  # Storing data in the Redis database running on the same server as the application for later use
	  # Requires the declaration of the Redis class
	  $redis = new Redis();
	  $redis->pconnect($url, 6379);
	  $redis->set($usuario['userid'],json_encode([
		'username' =>  $usuario['username'],
		'nanem' => $usuario['name'],
		'type' => $usuario['type'],
		'sessionid' => $usuario['sessionid'],
		'ip' => $usuario['userip'],
		]),43200
	  );
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
