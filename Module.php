<?php declare(strict_types=1);

/**
 * Módulo MASSZ v2.0 - Bootstrapping & Dynamic Menu Integration
 * Conceived by Leandro Dethloff, presented at Zabbix Conference LATAM 2024.
 * We thank God and our Savior Jesus Christ.
 */

namespace Modules\MASSZ;

use Zabbix\Core\CModule;
use APP;
use CMenu;
use CMenuItem;
use CWebUser;
use API;
use CUrl;
use Modules\MASSZ\Helpers\MasszTranslator as T;

class Module extends CModule {

    /**
     * Inicializa o módulo e injeta os menus/links dinamicamente.
     * Initializes the module and injects menus/links dynamically.
     */
    public function init(): void {
        // O usuário deve estar logado
        // User must be logged in
        if (!CWebUser::isLoggedIn()) {
            return;
        }

        $main_menu = APP::Component()->get('menu.main');
        $is_super_admin = (CWebUser::getType() == USER_TYPE_SUPER_ADMIN);

        // Adiciona atalho padrão de Gestão na seção Administração do Zabbix para SuperAdmins
        // Adds default management shortcut in Zabbix Administration section for SuperAdmins
        if ($is_super_admin) {
            $admin_menu = $main_menu->find(_('Administration'));
            if ($admin_menu) {
                $admin_menu->getSubmenu()->add(
                    (new CMenuItem(T::t('Gestão do MASSZ')))
                        ->setAction('massz.manage')
                );
            }
        }

        $config = $this->loadConfig();

        // Verifica se o usuário atual pertence ao grupo permitido no config.json
        // Check if the current user belongs to the allowed user group
        if (!$this->isUserInAllowedGroup($config)) {
            return;
        }

        $lang = CWebUser::$data['lang'] ?? 'pt_BR';

        // Dicionário de tradução dos menus padrão do Zabbix
        // Translation dictionary for standard Zabbix menus
        $mapped_menu_names = [
            'monitoramento' => _('Monitoring'),
            'monitoring' => _('Monitoring'),
            'serviços' => _('Services'),
            'services' => _('Services'),
            'inventário' => _('Inventory'),
            'inventory' => _('Inventory'),
            'relatórios' => _('Reports'),
            'reports' => _('Reports'),
            'coleta de dados' => _('Data collection'),
            'data collection' => _('Data collection'),
            'configuração' => _('Data collection'),
            'configuration' => _('Data collection'),
            'alertas' => _('Alerts'),
            'alerts' => _('Alerts'),
            'usuários' => _('Users'),
            'users' => _('Users'),
            'administração' => _('Administration'),
            'administration' => _('Administration')
        ];

        // Dicionário de tradução dos submenus padrão do Zabbix
        // Translation dictionary for standard Zabbix submenus
        $mapped_submenu_names = [
            'dashboard' => _('Dashboards'),
            'dashboards' => _('Dashboards'),
            'problemas' => _('Problems'),
            'problems' => _('Problems'),
            'hosts' => _('Hosts'),
            'dados recentes' => _('Latest data'),
            'latest data' => _('Latest data'),
            'mapas' => _('Maps'),
            'maps' => _('Maps'),
            'descoberta' => _('Discovery'),
            'discovery' => _('Discovery'),
            'serviços' => _('Services'),
            'services' => _('Services'),
            'sla' => _('SLA'),
            'relatório sla' => _('SLA report'),
            'sla report' => _('SLA report'),
            'visão geral' => _('Overview'),
            'overview' => _('Overview'),
            'templates' => _('Templates'),
            'grupos de templates' => _('Template groups'),
            'template groups' => _('Template groups'),
            'grupos de hosts' => _('Host groups'),
            'host groups' => _('Host groups'),
            'manutenção' => _('Maintenance'),
            'maintenance' => _('Maintenance'),
            'ações' => _('Actions'),
            'actions' => _('Actions'),
            'tipos de mídia' => _('Media types'),
            'media types' => _('Media types'),
            'scripts' => _('Scripts'),
            'grupos de usuários' => _('User groups'),
            'user groups' => _('User groups'),
            'funções de usuários' => _('User roles'),
            'user roles' => _('User roles'),
            'geral' => _('General'),
            'general' => _('General'),
            'módulos' => _('Modules'),
            'modules' => _('Modules'),
            'log de auditoria' => _('Audit log'),
            'audit log' => _('Audit log'),
            'action log' => _('Action log')
        ];

        $main_menu = APP::Component()->get('menu.main');

        // Adiciona menus e links configurados dinamicamente
        // Adds dynamically configured menus and links
        if (isset($config['menus']) && is_array($config['menus'])) {
            // Pass 1: Criar novos menus base
            // Pass 1: Create new base menus
            foreach ($config['menus'] as $menu_config) {
                if (!isset($menu_config['type']) || $menu_config['type'] !== 'existing') {
                    $menu_label = '';
                    if ($lang === 'pt_BR') {
                        $menu_label = trim($menu_config['label_pt'] ?? '');
                        if ($menu_label === '') {
                            $menu_label = trim($menu_config['label_en'] ?? '');
                        }
                    } else {
                        $menu_label = trim($menu_config['label_en'] ?? '');
                        if ($menu_label === '') {
                            $menu_label = trim($menu_config['label_pt'] ?? '');
                        }
                    }
                    if ($menu_label === '') {
                        $menu_label = 'MASSZ';
                    }
                    $menu_item = $main_menu->findOrAdd($menu_label);

                    // Configura o ícone
                    // Configures icon
                    $icon = $menu_config['icon'] ?? ($config['default_icon'] ?? 'zi-monitoring');
                    $menu_item->setIcon($icon);

                    $submenu = $menu_item->getSubmenu();

                    if (isset($menu_config['links']) && is_array($menu_config['links'])) {
                        foreach ($menu_config['links'] as $link) {
                            $link_label = '';
                            if ($lang === 'pt_BR') {
                                $link_label = trim($link['label_pt'] ?? '');
                                if ($link_label === '') {
                                    $link_label = trim($link['label_en'] ?? '');
                                }
                            } else {
                                $link_label = trim($link['label_en'] ?? '');
                                if ($link_label === '') {
                                    $link_label = trim($link['label_pt'] ?? '');
                                }
                            }
                            if ($link_label === '') {
                                $link_label = 'Link';
                            }
                            $submenu_item = new CMenuItem($link_label);

                            if ($link['link_type'] === 'iframe') {
                                $submenu_item->setAction('massz.link.' . $link['id']);
                            } else {
                                $resolved_url = $this->resolveUrl($link);
                                $submenu_item->setUrl(new CUrl($resolved_url));
                                if ($link['link_type'] === 'newtab') {
                                    $submenu_item->setTarget('_blank');
                                }
                            }

                            $submenu->add($submenu_item);
                        }
                    }
                }
            }

            // Pass 2: Inserir links em menus existentes (nativos ou criados no Pass 1)
            // Pass 2: Insert links into existing menus (native or custom created in Pass 1)
            foreach ($config['menus'] as $menu_config) {
                if (isset($menu_config['type']) && $menu_config['type'] === 'existing') {
                    $target_menu_label = $lang === 'pt_BR' ? ($menu_config['target_menu_pt'] ?? '') : ($menu_config['target_menu_en'] ?? ($menu_config['target_menu_pt'] ?? ''));
                    $key = mb_strtolower(trim($target_menu_label));

                    if (isset($mapped_menu_names[$key])) {
                        $target_menu_label = $mapped_menu_names[$key];
                    }

                    $target_item = $main_menu->find($target_menu_label);
                    if (!$target_item) {
                        // Se não encontrou, cria um novo menu no final como fallback
                        // If not found, creates a new menu at the end as a fallback
                        $target_item = $main_menu->findOrAdd($target_menu_label);
                        $target_item->setIcon($config['default_icon'] ?? 'zi-monitoring');
                    }

                    $submenu = $target_item->getSubmenu();
                    $insert_after_label = $lang === 'pt_BR' ? ($menu_config['insert_after_pt'] ?? '') : ($menu_config['insert_after_en'] ?? ($menu_config['insert_after_pt'] ?? ''));

                    if ($insert_after_label !== '') {
                        $sub_key = mb_strtolower(trim($insert_after_label));
                        if (isset($mapped_submenu_names[$sub_key])) {
                            $insert_after_label = $mapped_submenu_names[$sub_key];
                        }

                        $last_inserted = $insert_after_label;
                        if (isset($menu_config['links']) && is_array($menu_config['links'])) {
                            foreach ($menu_config['links'] as $link) {
                                $link_label = '';
                                if ($lang === 'pt_BR') {
                                    $link_label = trim($link['label_pt'] ?? '');
                                    if ($link_label === '') {
                                        $link_label = trim($link['label_en'] ?? '');
                                    }
                                } else {
                                    $link_label = trim($link['label_en'] ?? '');
                                    if ($link_label === '') {
                                        $link_label = trim($link['label_pt'] ?? '');
                                    }
                                }
                                if ($link_label === '') {
                                    $link_label = 'Link';
                                }
                                $menu_item = new CMenuItem($link_label);

                                if ($link['link_type'] === 'iframe') {
                                    $menu_item->setAction('massz.link.' . $link['id']);
                                } else {
                                    $resolved_url = $this->resolveUrl($link);
                                    $menu_item->setUrl(new CUrl($resolved_url));
                                    if ($link['link_type'] === 'newtab') {
                                        $menu_item->setTarget('_blank');
                                    }
                                }

                                $submenu->insertAfter($last_inserted, $menu_item);
                                $last_inserted = $link_label;
                            }
                        }
                    } else {
                        // Sem item de inserção específico: apenas adiciona no final do submenu
                        // No specific insertion item: just appends to the submenu
                        if (isset($menu_config['links']) && is_array($menu_config['links'])) {
                            foreach ($menu_config['links'] as $link) {
                                $link_label = '';
                                if ($lang === 'pt_BR') {
                                    $link_label = trim($link['label_pt'] ?? '');
                                    if ($link_label === '') {
                                        $link_label = trim($link['label_en'] ?? '');
                                    }
                                } else {
                                    $link_label = trim($link['label_en'] ?? '');
                                    if ($link_label === '') {
                                        $link_label = trim($link['label_pt'] ?? '');
                                    }
                                }
                                if ($link_label === '') {
                                    $link_label = 'Link';
                                }
                                $menu_item = new CMenuItem($link_label);

                                if ($link['link_type'] === 'iframe') {
                                    $menu_item->setAction('massz.link.' . $link['id']);
                                } else {
                                    $resolved_url = $this->resolveUrl($link);
                                    $menu_item->setUrl(new CUrl($resolved_url));
                                    if ($link['link_type'] === 'newtab') {
                                        $menu_item->setTarget('_blank');
                                    }
                                }

                                $submenu->add($menu_item);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Resolve a URL substituindo {HOST} e injetando parâmetros de sessão.
     * Resolves URL by replacing {HOST} and injecting session parameters.
     */
    private function resolveUrl(array $link): string {
        $url = $link['url'];

        // Substitui {HOST} pelo host do servidor Zabbix
        // Replaces {HOST} with Zabbix server host
        $url = str_replace('{HOST}', $_SERVER['HTTP_HOST'] ?? 'localhost', $url);

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
    private function isUserInAllowedGroup(array $config): bool {
        try {
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
        $config_file = __DIR__ . '/conf/config.json';

        if (!file_exists($config_file)) {
            return [];
        }

        $content = file_get_contents($config_file);
        $config = json_decode($content, true);

        return is_array($config) ? $config : [];
    }
}
