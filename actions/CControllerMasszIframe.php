<?php declare(strict_types=1);

namespace Modules\MASSZ\Actions;

use CController;
use CControllerResponseData;
use CControllerResponseFatal;
use API;
use CWebUser;
use Modules\MASSZ\Helpers\MasszTranslator as T;

/**
 * Controlador genérico para carregar os links em iframe dentro do Zabbix.
 * Generic controller to load links in an iframe within Zabbix.
 */
class CControllerMasszIframe extends CController {

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
        // O usuário precisa estar logado
        // User must be logged in
        if (!CWebUser::isLoggedIn()) {
            return false;
        }

        return $this->isUserInAllowedGroup();
    }

    /**
     * Executa a ação montando a URL e parâmetros de segurança.
     * Executes action by building URL and security parameters.
     */
    protected function doAction(): void {
        $action = $this->getAction();
        $link_id = str_replace('massz.link.', '', $action);

        $config = $this->loadConfig();
        $target_link = null;

        // Busca o link correspondente no config
        // Finds corresponding link in config
        if (isset($config['menus']) && is_array($config['menus'])) {
            foreach ($config['menus'] as $menu) {
                if (isset($menu['links']) && is_array($menu['links'])) {
                    foreach ($menu['links'] as $link) {
                        if (isset($link['id']) && $link['id'] === $link_id) {
                            $target_link = $link;
                            break 2;
                        }
                    }
                }
            }
        }

        if ($target_link === null) {
            $this->setResponse(new CControllerResponseFatal());
            return;
        }

        // Resolve a URL substituindo {HOST} e adicionando os dados de sessão
        // Resolves URL by replacing {HOST} and adding session data
        $resolved_url = $this->resolveUrl($target_link);

        $data = [
            'title' => T::t($target_link['label_pt']), // Usamos o tradutor se necessário, ou passamos direto
            'label_pt' => $target_link['label_pt'],
            'label_en' => $target_link['label_en'] ?? $target_link['label_pt'],
            'url' => $resolved_url,
            'sandbox' => $target_link['sandbox'] ?? 'allow-scripts allow-same-origin allow-forms',
            'allow_permissions' => $target_link['allow_permissions'] ?? 'fullscreen',
            'referrer_policy' => $target_link['referrer_policy'] ?? 'no-referrer'
        ];

        // Traduz o título com base no idioma do usuário
        // Translates title based on user language
        $lang = CWebUser::$data['lang'] ?? 'pt_BR';
        $title = '';
        if (str_starts_with($lang, 'pt_')) {
            $title = trim($data['label_pt'] ?? '');
            if ($title === '') {
                $title = trim($data['label_en'] ?? '');
            }
        } else {
            $title = trim($data['label_en'] ?? '');
            if ($title === '') {
                $title = trim($data['label_pt'] ?? '');
            }
        }
        if ($title === '') {
            $title = 'MASSZ';
        }

        $response = new CControllerResponseData($data);
        $response->setTitle($title);
        $this->setResponse($response);
    }

    /**
     * Resolve a URL substituindo {HOST} e injetando parâmetros de sessão.
     * Resolves URL by replacing {HOST} and injecting session parameters.
     */
    private function resolveUrl(array $link): string {
        $url = $link['url'];

        // Substitui {HOST} pelo host do servidor Zabbix
        // Replaces {HOST} with Zabbix server host
        $url = str_replace('{HOST}', $_SERVER['HTTP_HOST'], $url);

        // Processa parâmetros de URL configurados
        // Processes configured URL parameters
        if (isset($link['url_params']) && is_array($link['url_params']) && !empty($link['url_params'])) {
            $params = [];
            foreach ($link['url_params'] as $param) {
                $name = $param['param_name'];
                $source = $param['source'];

                $value = '';
                if ($source === 'custom') {
                    $value = (string) ($param['value'] ?? '');
                } elseif (isset(CWebUser::$data[$source])) {
                    $value = (string) CWebUser::$data[$source];
                }

                $params[$name] = $value;
            }

            if (!empty($params)) {
                $connector = (strpos($url, '?') === false) ? '?' : '&';
                $url .= $connector . http_build_query($params);
            }
        }

        return $url;
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
