<?php declare(strict_types=1);

namespace Modules\MASSZ\Helpers;

use CWebUser;

/**
 * Tradutor de Strings para o MASSZ v2.0.
 * String Translator for MASSZ v2.0.
 */
class MasszTranslator {

    /**
     * Dicionário de traduções (pt_BR e en_US)
     * Translation dictionary (pt_BR and en_US)
     */
    private static array $dictionary = [
        'Gestão do MASSZ' => [
            'en_US' => 'MASSZ Management'
        ],
        'MASSZ v2.0 - Abrir Seus Sistemas no Zabbix' => [
            'en_US' => 'MASSZ v2.0 - Open Your Systems in Zabbix'
        ],
        'Configurações Gerais' => [
            'en_US' => 'General Settings'
        ],
        'Grupo de Usuários Permitido' => [
            'en_US' => 'Allowed User Group'
        ],
        'Grupo do Zabbix que terá acesso aos links e menu de gestão' => [
            'en_US' => 'Zabbix group that will have access to the links and management menu'
        ],
        'Ícone Padrão' => [
            'en_US' => 'Default Icon'
        ],
        'Ícone padrão para novos menus se nenhum for selecionado' => [
            'en_US' => 'Default icon for new menus if none is selected'
        ],
        'Menus e Links' => [
            'en_US' => 'Menus and Links'
        ],
        'Adicionar Novo Menu' => [
            'en_US' => 'Add New Menu'
        ],
        'Salvar Configurações' => [
            'en_US' => 'Save Settings'
        ],
        'Tipo de Menu' => [
            'en_US' => 'Menu Type'
        ],
        'Novo Menu Base' => [
            'en_US' => 'New Base Menu'
        ],
        'Inserir em Menu Existente' => [
            'en_US' => 'Insert into Existing Menu'
        ],
        'Título (Português)' => [
            'en_US' => 'Title (Portuguese)'
        ],
        'Título (Inglês)' => [
            'en_US' => 'Title (English)'
        ],
        'Ícone do Menu' => [
            'en_US' => 'Menu Icon'
        ],
        'Menu de Destino' => [
            'en_US' => 'Target Menu'
        ],
        'Ex: Monitoramento, Serviços, Inventário' => [
            'en_US' => 'E.g., Monitoring, Services, Inventory'
        ],
        'Inserir Após o Item' => [
            'en_US' => 'Insert After Item'
        ],
        'Ex: Dashboard, Problemas, Hosts' => [
            'en_US' => 'E.g., Dashboard, Problems, Hosts'
        ],
        'Remover Menu' => [
            'en_US' => 'Remove Menu'
        ],
        'Adicionar Link' => [
            'en_US' => 'Add Link'
        ],
        'Configurações do Link' => [
            'en_US' => 'Link Settings'
        ],
        'Identificador do Link (Único, apenas letras/números)' => [
            'en_US' => 'Link Identifier (Unique, alphanumeric only)'
        ],
        'Título do Link (Português)' => [
            'en_US' => 'Link Title (Portuguese)'
        ],
        'Título do Link (Inglês)' => [
            'en_US' => 'Link Title (English)'
        ],
        'URL do Link' => [
            'en_US' => 'Link URL'
        ],
        'Use {HOST} para apontar dinamicamente para o host do Zabbix' => [
            'en_US' => 'Use {HOST} to dynamically point to the Zabbix host'
        ],
        'Tipo de Abertura' => [
            'en_US' => 'Opening Type'
        ],
        'Iframe Seguro (Interno)' => [
            'en_US' => 'Secure Iframe (Internal)'
        ],
        'Nova Aba' => [
            'en_US' => 'New Tab'
        ],
        'Redirecionamento' => [
            'en_US' => 'Redirect'
        ],
        'Segurança do Iframe (Sandbox)' => [
            'en_US' => 'Iframe Security (Sandbox)'
        ],
        'Deixe vazio para sem restrições ou marque as permissões necessárias' => [
            'en_US' => 'Leave empty for no restrictions or select the required permissions'
        ],
        'Permissions-Policy' => [
            'en_US' => 'Permissions-Policy'
        ],
        'Ex: fullscreen; camera \'none\'; microphone \'none\'' => [
            'en_US' => 'E.g., fullscreen; camera \'none\'; microphone \'none\''
        ],
        'Referrer-Policy' => [
            'en_US' => 'Referrer-Policy'
        ],
        'Controle de envio de cabeçalho Referer' => [
            'en_US' => 'Referer header sending control'
        ],
        'Parâmetros de URL (Dados de Sessão)' => [
            'en_US' => 'URL Parameters (Session Data)'
        ],
        'Envie variáveis da sessão atual do Zabbix como parâmetros GET na URL da aplicação' => [
            'en_US' => 'Send variables from the current Zabbix session as GET parameters in the application URL'
        ],
        'Adicionar Parâmetro' => [
            'en_US' => 'Add Parameter'
        ],
        'Nome da Variável na URL' => [
            'en_US' => 'URL Variable Name'
        ],
        'Dado de Sessão do Zabbix' => [
            'en_US' => 'Zabbix Session Data'
        ],
        'Remover Parâmetro' => [
            'en_US' => 'Remove Parameter'
        ],
        'Visualização prévia do Link' => [
            'en_US' => 'Link Preview'
        ],
        'URL Completa Resolvida (Exemplo):' => [
            'en_US' => 'Resolved Full URL (Example):'
        ],
        'Remover Link' => [
            'en_US' => 'Remove Link'
        ],
        'Salvar Alterações' => [
            'en_US' => 'Save Changes'
        ],
        'Selecione um Ícone' => [
            'en_US' => 'Select an Icon'
        ],
        'Pesquisar ícones...' => [
            'en_US' => 'Search icons...'
        ],
        'Ícones Nativos' => [
            'en_US' => 'Native Icons'
        ],
        'Ícones Customizados' => [
            'en_US' => 'Custom Icons'
        ],
        'Ícones Legado' => [
            'en_US' => 'Legacy Icons'
        ],
        'Escolher Ícone' => [
            'en_US' => 'Choose Icon'
        ],
        'Fechar' => [
            'en_US' => 'Close'
        ],
        'Configurações salvas com sucesso!' => [
            'en_US' => 'Settings saved successfully!'
        ],
        'Erro ao salvar configurações:' => [
            'en_US' => 'Error saving settings:'
        ],
        'Carregando aplicação externa...' => [
            'en_US' => 'Loading external application...'
        ],
        'Se o sistema não carregar corretamente, clique aqui para abrir em uma nova aba.' => [
            'en_US' => 'If the system does not load correctly, click here to open in a new tab.'
        ],
        'Você não tem permissão para acessar esta página.' => [
            'en_US' => 'You do not have permission to access this page.'
        ],
        'Apresentado na Zabbix Conference LATAM 2024 como o primeiro módulo para Zabbix entregue como software livre para a comunidade.' => [
            'en_US' => 'Presented at Zabbix Conference LATAM 2024 as the first Zabbix module delivered as free software to the community.'
        ],
        'Agradecemos a Deus e ao nosso Salvador Jesus Cristo por ter dado capacidade e conhecimento para desenvolver esse projeto.' => [
            'en_US' => 'We thank God and our Savior Jesus Christ for providing the capability and knowledge to develop this project.'
        ],
        'Idealizado e desenvolvido por Leandro Dethloff.' => [
            'en_US' => 'Conceived and developed by Leandro Dethloff.'
        ],
        'Não há links configurados para este menu.' => [
            'en_US' => 'There are no links configured for this menu.'
        ],
        'Nome de login do usuário' => [
            'en_US' => 'User login name'
        ],
        'Primeiro nome do usuário' => [
            'en_US' => 'User first name'
        ],
        'Sobrenome do usuário' => [
            'en_US' => 'User last name'
        ],
        'ID numérico do usuário' => [
            'en_US' => 'User numeric ID'
        ],
        'Tipo de usuário (1=User, 2=Admin, 3=SuperAdmin)' => [
            'en_US' => 'User type (1=User, 2=Admin, 3=SuperAdmin)'
        ],
        'ID da sessão ativa do Zabbix' => [
            'en_US' => 'Active Zabbix session ID'
        ],
        'Endereço IP do usuário' => [
            'en_US' => 'User IP address'
        ],
        'Idioma configurado (ex: pt_BR)' => [
            'en_US' => 'Configured language (e.g., pt_BR)'
        ],
        'Tema ativo no Zabbix' => [
            'en_US' => 'Active Zabbix theme'
        ],
        'Endereço IP do último login' => [
            'en_US' => 'Last login IP address'
        ],
        'Configurações Gerais Salvas!' => [
            'en_US' => 'General Settings Saved!'
        ],
        'Nenhum menu configurado. Crie um novo menu para começar.' => [
            'en_US' => 'No menus configured. Create a new menu to start.'
        ],
        'Preset de Segurança' => [
            'en_US' => 'Security Preset'
        ],
        'Personalizado' => [
            'en_US' => 'Custom'
        ],
        'Básico (Permite scripts e mesma origem)' => [
            'en_US' => 'Basic (Allows scripts and same origin)'
        ],
        'Restrito (Apenas exibir, sem scripts)' => [
            'en_US' => 'Restricted (Display only, no scripts)'
        ],
        'Permissivo (Permite formulários, popups, downloads)' => [
            'en_US' => 'Permissive (Allows forms, popups, downloads)'
        ],
        'Sem restrições' => [
            'en_US' => 'No restrictions'
        ],
        'Créditos & Histórico' => [
            'en_US' => 'Credits & History'
        ],
        'Menus Configurados' => [
            'en_US' => 'Configured Menus'
        ],
        '-- Selecione o Grupo --' => [
            'en_US' => '-- Select Group --'
        ],
        'Salvar' => [
            'en_US' => 'Save'
        ],
        'Adicionar Menu à Lista' => [
            'en_US' => 'Add Menu to List'
        ],
        'Atualizar Menu na Lista' => [
            'en_US' => 'Update Menu in List'
        ],
        'Limpar Formulário' => [
            'en_US' => 'Clear Form'
        ],
        'Cancelar Edição' => [
            'en_US' => 'Cancel Edit'
        ],
        'Editar Menu' => [
            'en_US' => 'Edit Menu'
        ],
        'Novo Menu (Rascunho)' => [
            'en_US' => 'New Menu (Draft)'
        ],
        'Editando Menu' => [
            'en_US' => 'Editing Menu'
        ],
        'Salvar Menu' => [
            'en_US' => 'Save Menu'
        ],
        'Novo Menu' => [
            'en_US' => 'New Menu'
        ],
        'Editar' => [
            'en_US' => 'Edit'
        ],
        'Excluir' => [
            'en_US' => 'Delete'
        ],
        'Subir' => [
            'en_US' => 'Move Up'
        ],
        'Descer' => [
            'en_US' => 'Move Down'
        ],
        'Não há links neste menu.' => [
            'en_US' => 'There are no links in this menu.'
        ],
        'Configurações Avançadas de Segurança & Parâmetros' => [
            'en_US' => 'Advanced Security Settings & Parameters'
        ],
        'Nenhum parâmetro configurado.' => [
            'en_US' => 'No parameters configured.'
        ],
        'Escolher' => [
            'en_US' => 'Choose'
        ]
    ];

    /**
     * Traduz uma string / Translates a string
     *
     * @param string $key String em português (pt-BR) a ser traduzida / String in portuguese to be translated
     * @return string String traduzida ou a original se não encontrada / Translated string or original if not found
     */
    public static function t(string $key): string {
        $lang = CWebUser::$data['lang'] ?? 'pt_BR';

        if (str_starts_with($lang, 'pt_')) {
            return $key;
        }

        if (isset(self::$dictionary[$key]['en_US'])) {
            return self::$dictionary[$key]['en_US'];
        }

        return $key;
    }
}
