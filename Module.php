<?php

# Pode ser alterado o nome MASSZ por algo da sua preferencia em todo o codigo abaixo
# The name "MASSZ" can be changed to anything you prefer throughout the code below
namespace Modules\MASSZ;

use Zabbix\Core\CModule;
use APP;
use CMenuItem;
use CMenu;
use CWebuser;
class Module extends CModule
{
	public function init(): void {
		# Pegando os dados do usuário logado
	        # Requer o uso da classe CWebUser
                # Getting the logged in user's data
                # Requires the use of the CWebUser class
		$usuario = CWebUser::$data;
		# verifica se o usuario é supeadmin se trocar para 2 pode ser usado para filtrar admin
                # Check if the user is a superadmin (can be changed to 2 to filter admins)
                if ($usuario['type'] != 3) {
                  APP::Component()->get('menu.main')
                          ->findOrAdd(_('MASSZ'))
                          ->setIcon('icon-reports')
                          ->getSubmenu()
                          ->insertAfter(_('Link 1'), (new CMenuItem('Link 2'))
                                  ->setAction('link.2')
                          )        
                          ->insertAfter(_('Link 2'), (new CMenuItem('Link 3'))
                                  ->setAction('link.3')
                          )
                          ->insertAfter(_('Link 3'), (new CMenuItem('Link 4'))
                                  ->setAction('link.4')
                          )
                          ->insertBefore(_('Link 2'), (new CMenuItem('Link 1'))
                                  ->setAction('link.1')
                          );
		}
		else {
	             # irá adicionar um menu a mais somente para os superadmin	
                     # Will add an extra menu only for superadmins
		     APP::Component()->get('menu.main')
                          ->findOrAdd(_('MASSZ'))
                          ->setIcon('icon-reports')
                          ->getSubmenu()
                          ->insertAfter(_('Link 1'), (new CMenuItem('Link 2'))
                                  ->setAction('link.2')
                          )
                          ->insertAfter(_('Link 2'), (new CMenuItem('Link 3'))
                                  ->setAction('link.3')
                          )
                          ->insertAfter(_('Link 3'), (new CMenuItem('Link 4'))
                                  ->setAction('link.4')
                          )
                          ->insertBefore(_('Link 2'), (new CMenuItem('Link 1'))
                                  ->setAction('link.1')
			  )
			  # Esse link só irá aparecer quando o usuário logado for superadmin
                          # This link will only appear when the logged in user is a superadmin
                          ->insertAfter(_('Link 4'), (new CMenuItem('Special Link'))
                                  ->setAction('special.link')
                          );

		}	
        }
}

