<?php declare(strict_types=1);

namespace Modules\MASSZ\Actions;

use CController;
use CControllerResponseData;
use CControllerResponseFatal;
use API;
use APP;
use CWebUser;
use Modules\MASSZ\Helpers\MasszTranslator as T;

/**
 * Controlador para a página de gestão do MASSZ.
 * Controller for the MASSZ management page.
 */
class CControllerMasszManage extends CController {

    /**
     * Inicializa o controlador desativando validação CSRF.
     * Initializes the controller by disabling CSRF validation.
     */
    protected function init(): void {
        $this->disableCsrfValidation();
    }

    /**
     * Valida os parâmetros de entrada.
     * Validates input parameters.
     */
    protected function checkInput(): bool {
        return true;
    }

    /**
     * Valida permissões do usuário logado.
     * Validates logged-in user's permissions.
     */
    protected function checkPermissions(): bool {
        // Apenas Super Administradores podem acessar a tela de gestão
        // Only Super Administrators can access the management screen
        return ($this->getUserType() == USER_TYPE_SUPER_ADMIN);
    }

    /**
     * Executa a ação principal do controlador.
     * Executes the controller's main action.
     */
    protected function doAction(): void {
        $config = $this->loadConfig();

        $data = [
            'title' => T::t('Gestão do MASSZ'),
            'config' => $config,
            'user_groups' => $this->getAllUserGroups(),
            'menu_tree' => $this->getMenuTree(),
            'user_fullname' => CWebUser::$data['name'] . ' ' . CWebUser::$data['surname'],
            'username' => CWebUser::$data['username']
        ];

        $response = new CControllerResponseData($data);
        $response->setTitle($data['title']);
        $this->setResponse($response);
    }

    /**
     * Busca todos os grupos de usuários do Zabbix.
     * Fetches all user groups from Zabbix.
     */
    private function getAllUserGroups(): array {
        try {
            return API::UserGroup()->get([
                'output' => ['usrgrpid', 'name'],
                'sortfield' => 'name'
            ]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Extrai a árvore real de menus do Zabbix para injetar no JS.
     * Extracts the real Zabbix menu tree to inject into JS.
     */
    private function getMenuTree(): array {
        $tree = [];

        try {
            $main_menu = APP::Component()->get('menu.main');
            if ($main_menu === null) {
                return $tree;
            }

            foreach ($main_menu->getMenuItems() as $menu_item) {
                $entry = [
                    'label' => $menu_item->getLabel(),
                    'submenus' => []
                ];

                if ($menu_item->hasSubMenu()) {
                    foreach ($menu_item->getSubMenu()->getMenuItems() as $sub_item) {
                        $entry['submenus'][] = [
                            'label' => $sub_item->getLabel()
                        ];
                    }
                }

                $tree[] = $entry;
            }
        } catch (\Exception $e) {
            // fail silently
        }

        return $tree;
    }

    /**
     * Verifica se o usuário atual pertence ao grupo permitido.
     * Checks if current user belongs to the allowed group.
     */
    private function isUserInAllowedGroup(): bool {
        try {
            $config = $this->loadConfig();
            $allowed_group_name = $config['allowed_user_group'] ?? 'Zabbix administrators';

            if ($allowed_group_name === '') {
                return false;
            }

            $user_groups = API::UserGroup()->get([
                'output' => ['usrgrpid', 'name'],
                'userids' => CWebUser::$data['userid']
            ]);

            foreach ($user_groups as $group) {
                if ($group['name'] === $allowed_group_name) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Carrega as configurações do arquivo json do módulo.
     * Loads module configuration from json file.
     */
    private function loadConfig(): array {
        $config_file = dirname(__DIR__) . '/conf/config.json';

        if (!file_exists($config_file)) {
            return [];
        }

        $content = file_get_contents($config_file);
        $config = json_decode($content, true);

        return is_array($config) ? $config : [];
    }
}
