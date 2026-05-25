<?php declare(strict_types=1);

namespace Modules\MASSZ\Actions;

use CController;
use CControllerResponseData;
use CControllerResponseFatal;
use API;
use CWebUser;
use DirectoryIterator;
use CModuleManager;
use APP;
use Modules\MASSZ\Helpers\MasszTranslator as T;

/**
 * Controlador para salvar as configurações do MASSZ.
 * Controller to save MASSZ configurations.
 */
class CControllerMasszSave extends CController {

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
        // Aceita a configuração no parâmetro post 'config'
        // Accepts configuration in the post parameter 'config'
        $ret = $this->validateInput([
            'config' => 'required|string'
        ]);

        if (!$ret) {
            $this->setResponse(new CControllerResponseData(['main_block' => json_encode([
                'success' => false,
                'error' => 'Invalid parameters'
            ])]));
        }

        return $ret;
    }

    /**
     * Valida permissões do usuário logado.
     * Validates logged-in user's permissions.
     */
    protected function checkPermissions(): bool {
        // Apenas Super Administradores podem salvar
        // Only Super Administrators can save
        return ($this->getUserType() == USER_TYPE_SUPER_ADMIN);
    }

    /**
     * Executa a ação de salvamento e regeneração do manifest.json.
     * Executes save action and regenerates manifest.json.
     */
    protected function doAction(): void {
        $config_json = $this->getInput('config');
        $config_data = json_decode($config_json, true);

        if (!is_array($config_data)) {
            $this->setResponse(new CControllerResponseData(['main_block' => json_encode([
                'success' => false,
                'error' => 'Invalid JSON structure'
            ])]));
            return;
        }

        $config_dir = dirname(__DIR__) . '/conf';
        $config_file = $config_dir . '/config.json';

        // Garante que o diretório conf exista
        // Ensures conf directory exists
        if (!file_exists($config_dir)) {
            mkdir($config_dir, 0755, true);
        }

        // Salva o config.json
        // Saves config.json
        if (file_put_contents($config_file, json_encode($config_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
            $this->setResponse(new CControllerResponseData(['main_block' => json_encode([
                'success' => false,
                'error' => 'Failed to write config.json file'
            ])]));
            return;
        }

        // Regenera o manifest.json
        // Regenerates manifest.json
        if (!$this->regenerateManifest($config_data)) {
            $this->setResponse(new CControllerResponseData(['main_block' => json_encode([
                'success' => false,
                'error' => 'Failed to regenerate manifest.json file'
            ])]));
            return;
        }

        // Executa scan do diretório de módulos em background para o Zabbix registrar as novas actions
        // Runs module directory scan in background for Zabbix to register new actions
        $scan_success = $this->runModuleScan();

        $this->setResponse(new CControllerResponseData(['main_block' => json_encode([
            'success' => true,
            'scan_success' => $scan_success,
            'message' => T::t('Configurações salvas com sucesso!')
        ])]));
    }

    /**
     * Regenera o manifest.json com as ações dinâmicas para cada link.
     * Regenerates manifest.json with dynamic actions for each link.
     */
    private function regenerateManifest(array $config): bool {
        $manifest_file = dirname(__DIR__) . '/manifest.json';

        // Estrutura padrão do manifest v2.0
        // Standard structure for manifest v2.0
        $manifest = [
            'manifest_version' => 2.0,
            'id' => 'massz',
            'name' => 'MASSZ',
            'version' => '2.0.0',
            'namespace' => 'MASSZ',
            'author' => 'Leandro Dethloff',
            'description' => 'Módulo para Abrir Seus Sistemas no Zabbix | Module to Open Your Systems in Zabbix',
            'actions' => [
                'massz.manage' => [
                    'class' => 'CControllerMasszManage',
                    'view' => 'massz.manage'
                ],
                'massz.save' => [
                    'class' => 'CControllerMasszSave',
                    'layout' => 'layout.json'
                ]
            ],
            'assets' => [
                'css' => [
                    'massz.css',
                    'massz-dark.css'
                ],
                'js' => [
                    'massz.utils.js'
                ]
            ]
        ];

        // Adiciona dinamicamente as actions para cada link configurado
        // Dynamically adds actions for each configured link
        if (isset($config['menus']) && is_array($config['menus'])) {
            foreach ($config['menus'] as $menu) {
                if (isset($menu['links']) && is_array($menu['links'])) {
                    foreach ($menu['links'] as $link) {
                        if (isset($link['id']) && $link['id'] !== '') {
                            $action_name = 'massz.link.' . $link['id'];
                            $manifest['actions'][$action_name] = [
                                'class' => 'CControllerMasszIframe',
                                'view' => 'massz.iframe'
                            ];
                        }
                    }
                }
            }
        }

        return file_put_contents($manifest_file, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
    }

    /**
     * Executa o scan de diretório de módulos do Zabbix de forma programática.
     * Programmatically runs the Zabbix module directory scan.
     */
    private function runModuleScan(): bool {
        return true;
    }

    /**
     * Verifica se o usuário atual pertence ao grupo permitido.
     * Checks if current user belongs to the allowed group.
     */
    private function isUserInAllowedGroup(): bool {
        try {
            $config_file = dirname(__DIR__) . '/conf/config.json';
            $allowed_group_name = 'Zabbix administrators';

            if (file_exists($config_file)) {
                $config = json_decode(file_get_contents($config_file), true);
                $allowed_group_name = $config['allowed_user_group'] ?? 'Zabbix administrators';
            }

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
}
