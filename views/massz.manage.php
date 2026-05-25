<?php declare(strict_types=1);

/**
 * MASSZ v2.0 - Management Page View
 *
 * @var CView $this
 * @var array $data
 */

use Modules\MASSZ\Helpers\MasszTranslator as T;

?>
<!-- Envia as configurações de variáveis para o Javascript / Sends variable configurations to Javascript -->
<script type="text/javascript">
    window.masszConfig = <?= json_encode($data['config']) ?>;
    window.masszSessionSources = [
        { value: 'custom', label: <?= json_encode(T::t('Valor Fixo / Texto Livre')) ?> },
        { value: 'userid', label: <?= json_encode(T::t('ID numérico do usuário')) ?> },
        { value: 'username', label: <?= json_encode(T::t('Nome de login do usuário')) ?> },
        { value: 'name', label: <?= json_encode(T::t('Primeiro nome do usuário')) ?> },
        { value: 'surname', label: <?= json_encode(T::t('Sobrenome do usuário')) ?> },
        { value: 'type', label: <?= json_encode(T::t('Tipo de usuário (1=User, 2=Admin, 3=SuperAdmin)')) ?> },
        { value: 'sessionid', label: <?= json_encode(T::t('ID da sessão ativa do Zabbix')) ?> },
        { value: 'userip', label: <?= json_encode(T::t('Endereço IP do usuário')) ?> },
        { value: 'lang', label: <?= json_encode(T::t('Idioma configurado (ex: pt_BR)')) ?> },
        { value: 'theme', label: <?= json_encode(T::t('Tema ativo no Zabbix')) ?> },
        { value: 'attempt_ip', label: <?= json_encode(T::t('Endereço IP do último login')) ?> }
    ];
    window.masszUrls = {
        save: <?= json_encode((new CUrl('zabbix.php'))->setArgument('action', 'massz.save')->getUrl()) ?>
    };
    window.masszMenuTree = <?= json_encode($data['menu_tree'] ?? []) ?>;
    window.masszLabels = {
        removeMenu: <?= json_encode(T::t('Remover Menu')) ?>,
        removeLink: <?= json_encode(T::t('Remover Link')) ?>,
        addLink: <?= json_encode(T::t('Adicionar Link')) ?>,
        menuType: <?= json_encode(T::t('Tipo de Menu')) ?>,
        newMenuBase: <?= json_encode(T::t('Novo Menu Base')) ?>,
        insertExistingMenu: <?= json_encode(T::t('Inserir em Menu Existente')) ?>,
        titlePt:  <?= json_encode(T::t('Título (Português)')) ?>,
        titleEn:  <?= json_encode(T::t('Título (Inglês)')) ?>,
        menuIcon: <?= json_encode(T::t('Ícone do Menu')) ?>,
        targetMenu: <?= json_encode(T::t('Menu de Destino')) ?>,
        targetMenuHelp: <?= json_encode(T::t('Ex: Monitoramento, Serviços, Inventário')) ?>,
        insertAfter: <?= json_encode(T::t('Inserir Após o Item')) ?>,
        insertAfterHelp: <?= json_encode(T::t('Ex: Dashboard, Problemas, Hosts')) ?>,
        choose: <?= json_encode(T::t('Escolher')) ?>,
        linkSettings: <?= json_encode(T::t('Configurações do Link')) ?>,
        linkId: <?= json_encode(T::t('Identificador do Link (Único, apenas letras/números)')) ?>,
        linkTitlePt: <?= json_encode(T::t('Título do Link (Português)')) ?>,
        linkTitleEn: <?= json_encode(T::t('Título do Link (Inglês)')) ?>,
        linkUrl: <?= json_encode(T::t('URL do Link')) ?>,
        linkUrlHelp: <?= json_encode(T::t('Use {HOST} para apontar dinamicamente para o host do Zabbix')) ?>,
        openingType: <?= json_encode(T::t('Tipo de Abertura')) ?>,
        iframeSecure: <?= json_encode(T::t('Iframe Seguro (Interno)')) ?>,
        newTab: <?= json_encode(T::t('Nova Aba')) ?>,
        redirect: <?= json_encode(T::t('Redirecionamento')) ?>,
        iframeSecurity: <?= json_encode(T::t('Segurança do Iframe (Sandbox)')) ?>,
        iframeSecurityHelp: <?= json_encode(T::t('Deixe vazio para sem restrições ou marque as permissões necessárias')) ?>,
        permissionsPolicy: <?= json_encode(T::t('Permissions-Policy')) ?>,
        permissionsPolicyHelp: <?= json_encode(T::t('Ex: fullscreen; camera \'none\'; microphone \'none\'')) ?>,
        referrerPolicy: <?= json_encode(T::t('Referrer-Policy')) ?>,
        referrerPolicyHelp: <?= json_encode(T::t('Controle de envio de cabeçalho Referer')) ?>,
        urlParams: <?= json_encode(T::t('Parâmetros de URL (Dados de Sessão)')) ?>,
        urlParamsHelp: <?= json_encode(T::t('Envie variáveis da sessão atual do Zabbix como parâmetros GET na URL da aplicação')) ?>,
        urlParamsExamples: <?= json_encode(T::t('Exemplos: token = 123456 (Valor Fixo) | user = username (Dado de Sessão)')) ?>,
        addParameter: <?= json_encode(T::t('Adicionar Parâmetro')) ?>,
        paramName: <?= json_encode(T::t('Nome da Variável na URL')) ?>,
        sessionData: <?= json_encode(T::t('Dado de Sessão do Zabbix')) ?>,
        removeParameter: <?= json_encode(T::t('Remover Parâmetro')) ?>,
        linkPreview: <?= json_encode(T::t('Visualização prévia do Link')) ?>,
        linkPreviewHelp: <?= json_encode(T::t('URL Completa Resolvida (Exemplo):')) ?>,
        securityPreset: <?= json_encode(T::t('Preset de Segurança')) ?>,
        presetCustom: <?= json_encode(T::t('Personalizado')) ?>,
        presetBasic: <?= json_encode(T::t('Básico (Permite scripts e mesma origem)')) ?>,
        presetRestricted: <?= json_encode(T::t('Restrito (Apenas exibir, sem scripts)')) ?>,
        presetPermissive: <?= json_encode(T::t('Permissivo (Permite formulários, popups, downloads)')) ?>,
        presetNone: <?= json_encode(T::t('Sem restrições')) ?>,
        addMenuToList: <?= json_encode(T::t('Adicionar Menu à Lista')) ?>,
        updateMenuInList: <?= json_encode(T::t('Atualizar Menu na Lista')) ?>,
        clearForm: <?= json_encode(T::t('Limpar Formulário')) ?>,
        cancelEdit: <?= json_encode(T::t('Cancelar Edição')) ?>,
        addNewMenuTitle: <?= json_encode(T::t('Adicionar Novo Menu')) ?>,
        editMenuTitle: <?= json_encode(T::t('Editar Menu')) ?>,
        menuDraft: <?= json_encode(T::t('Novo Menu (Rascunho)')) ?>,
        editingMenuLabel: <?= json_encode(T::t('Editando Menu')) ?>,
        noMenusConfigured: <?= json_encode(T::t('Nenhum menu configurado. Crie um novo menu para começar.')) ?>,
        saveGeneralSettings: <?= json_encode(T::t('Salvar')) ?>,
        saveMenu: <?= json_encode(T::t('Salvar Menu')) ?>,
        saveChanges: <?= json_encode(T::t('Salvar Alterações')) ?>,
        advancedSettings: <?= json_encode(T::t('Configurações Avançadas de Segurança & Parâmetros')) ?>,
        noParamsConfigured: <?= json_encode(T::t('Nenhum parâmetro configurado.')) ?>
    };
    window.masszLang = <?= json_encode(CWebUser::$data['lang'] ?? 'pt_BR') ?>;
</script>
<?php

$this->includeJsFile('massz.manage.js.php');

// Create Zabbix HTML Page
$html_page = (new CHtmlPage());

?>

<div class="massz-container">
    <!-- Banner do Cabeçalho / Header Banner -->
    <div class="massz-header-banner">
        <div class="massz-header-title">
            <h2><?= T::t('MASSZ v2.0 - Abrir Seus Sistemas no Zabbix') ?></h2>
        </div>
    </div>

    <!-- Formulário de Configuração Principal / Main Configuration Form -->
    <form id="massz-config-form" autocomplete="off">
        
        <!-- Card de Configurações Gerais / General Settings Card -->
        <div class="massz-general-card">
            <h3 class="massz-card-title"><?= T::t('Configurações Gerais') ?></h3>
            <div class="massz-form-row">
                <div class="massz-form-group massz-col-12">
                    <label for="allowed_user_group"><?= T::t('Grupo de Usuários Permitido') ?></label>
                    <select id="allowed_user_group" name="allowed_user_group" class="massz-select">
                        <option value=""><?= T::t('-- Selecione o Grupo --') ?></option>
                        <?php foreach ($data['user_groups'] as $group): ?>
                            <?php 
                            $selected = (isset($data['config']['allowed_user_group']) && $data['config']['allowed_user_group'] === $group['name']) ? 'selected' : '';
                            ?>
                            <option value="<?= htmlspecialchars($group['name']) ?>" <?= $selected ?>><?= htmlspecialchars($group['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-input-help" style="margin-top: 4px; display: block; font-size: 11px; color: var(--massz-text-muted);">
                        <?= T::t('Grupo do Zabbix que terá acesso aos links e menu de gestão') ?>
                    </small>
                </div>
                
                <input type="hidden" id="default_icon" name="default_icon" value="<?= htmlspecialchars($data['config']['default_icon'] ?? 'zi-monitoring') ?>">
            </div>
            
            <!-- Botão de Salvar / Save Button -->
            <div style="display: flex; justify-content: flex-end; margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--massz-gray-border);">
                <button type="button" class="massz-btn massz-btn-success massz-btn-save-action" onclick="saveConfiguration()">
                    <span class="zi-check"></span> <?= T::t('Salvar') ?>
                </button>
            </div>
        </div>

        <!-- Card de Menus Configurados / Configured Menus Card -->
        <div class="massz-general-card">
            <h3 class="massz-card-title">
                <span><?= T::t('Menus Configurados') ?></span>
            </h3>
            
            <div id="massz-existing-menus-container">
                <!-- Rendered dynamically by JS -->
            </div>
            
            <div id="massz-empty-state" style="display: none; text-align: center; padding: 40px 20px; color: var(--massz-text-muted);">
                <p><?= T::t('Nenhum menu configurado. Crie um novo menu para começar.') ?></p>
            </div>
        </div>

        <!-- Card de Adicionar / Editar Menu / Add or Edit Menu Card -->
        <div class="massz-general-card">
            <h3 class="massz-card-title">
                <span id="massz-form-title"><?= T::t('Adicionar Novo Menu') ?></span>
            </h3>
            
            <div id="massz-menus-container" class="massz-menu-list">
                <!-- Rendered dynamically by JS -->
            </div>
        </div>
    </form>
</div>

<!-- Modal de Seleção de Ícone / Icon Picker Modal -->
<div id="massz-icon-modal" class="massz-modal">
    <div class="massz-modal-content">
        <div class="massz-modal-header">
            <h3><?= T::t('Selecione um Ícone') ?></h3>
            <button type="button" class="massz-modal-close" onclick="closeIconPicker()">&times;</button>
        </div>
        <div class="massz-modal-tabs">
            <div class="massz-modal-tab active" onclick="switchIconTab('native')"><?= T::t('Ícones Nativos') ?></div>
            <div class="massz-modal-tab" onclick="switchIconTab('custom')"><?= T::t('Ícones Customizados') ?></div>
        </div>
        <div class="massz-modal-body">
            <div class="massz-search-wrapper">
                <input type="text" id="massz-icon-search" class="massz-search-input" placeholder="<?= T::t('Pesquisar ícones...') ?>" onkeyup="filterIcons()">
            </div>
            
            <div id="massz-icon-grid-container" class="massz-icon-grid">
                <!-- Populated dynamically by JS -->
            </div>
        </div>
        <div class="massz-modal-footer">
            <button type="button" class="massz-btn massz-btn-secondary" onclick="closeIconPicker()"><?= T::t('Fechar') ?></button>
        </div>
    </div>
</div>

<?php

$html_page->show();
