<?php declare(strict_types=1);

/**
 * MASSZ v2.0 - Management Page JavaScript
 *
 * @var CView $this
 */
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
    'use strict';

    // Lista de ícones disponíveis / List of available icons
    const NATIVE_ICONS = [
        'zi-administration', 'zi-alert', 'zi-alert-check', 'zi-alert-more', 'zi-alert-x',
        'zi-alerts', 'zi-arrow-back', 'zi-arrow-down', 'zi-bell', 'zi-bell-off',
        'zi-dashboard', 'zi-download', 'zi-monitoring', 'zi-support', 'zi-trash',
        'zi-user', 'zi-users', 'zi-check', 'zi-chevron-down', 'zi-chevron-up',
        'zi-cog', 'zi-copy', 'zi-edit', 'zi-eye', 'zi-eye-off',
        'zi-filter', 'zi-gear', 'zi-home', 'zi-info', 'zi-link',
        'zi-list', 'zi-lock', 'zi-mail', 'zi-map', 'zi-menu',
        'zi-plus', 'zi-refresh', 'zi-search', 'zi-settings', 'zi-star'
    ];

    const CUSTOM_ICONS = [
        'massz-icon-server', 'massz-icon-database', 'massz-icon-cloud', 'massz-icon-network',
        'massz-icon-chart-bar', 'massz-icon-chart-line', 'massz-icon-chart-pie', 'massz-icon-terminal',
        'massz-icon-globe', 'massz-icon-shield', 'massz-icon-key', 'massz-icon-cpu',
        'massz-icon-memory', 'massz-icon-disk', 'massz-icon-wifi', 'massz-icon-code',
        'massz-icon-api', 'massz-icon-docker', 'massz-icon-kubernetes', 'massz-icon-rocket',
        'massz-icon-lightning', 'massz-icon-pulse', 'massz-icon-satellite', 'massz-icon-tools'
    ];

    // Lista de menus nativos e seus submenus para injeção no select / Native menus list and their submenus for select injection
    const NATIVE_MENUS = [
        {
            key: 'monitoring',
            pt: 'Monitoramento',
            en: 'Monitoring',
            submenus: [
                { pt: 'Dashboard', en: 'Dashboard' },
                { pt: 'Problemas', en: 'Problems' },
                { pt: 'Hosts', en: 'Hosts' },
                { pt: 'Web', en: 'Web' },
                { pt: 'Dados recentes', en: 'Latest data' },
                { pt: 'Mapas', en: 'Maps' },
                { pt: 'Descoberta', en: 'Discovery' }
            ]
        },
        {
            key: 'services',
            pt: 'Serviços',
            en: 'Services',
            submenus: [
                { pt: 'Serviços', en: 'Services' },
                { pt: 'SLA', en: 'SLA' },
                { pt: 'Relatórios de SLA', en: 'SLA report' }
            ]
        },
        {
            key: 'inventory',
            pt: 'Inventário',
            en: 'Inventory',
            submenus: [
                { pt: 'Visão geral', en: 'Overview' },
                { pt: 'Hosts', en: 'Hosts' }
            ]
        },
        {
            key: 'reports',
            pt: 'Relatórios',
            en: 'Reports',
            submenus: [
                { pt: 'Informações do sistema', en: 'System information' },
                { pt: 'Relatório de disponibilidade', en: 'Availability report' },
                { pt: 'Três principais triggers', en: 'Triggers top 100' },
                { pt: 'Log de auditoria', en: 'Audit log' },
                { pt: 'Action log', en: 'Action log' },
                { pt: 'Notificações', en: 'Notifications' },
                { pt: 'Detalhes do agendamento', en: 'Scheduled reports' }
            ]
        },
        {
            key: 'data_collection',
            pt: 'Coleta de dados',
            en: 'Data collection',
            submenus: [
                { pt: 'Grupos de templates', en: 'Template groups' },
                { pt: 'Templates', en: 'Templates' },
                { pt: 'Grupos de hosts', en: 'Host groups' },
                { pt: 'Hosts', en: 'Hosts' },
                { pt: 'Manutenção', en: 'Maintenance' },
                { pt: 'Ações', en: 'Actions' },
                { pt: 'Descoberta', en: 'Discovery' }
            ]
        },
        {
            key: 'alerts',
            pt: 'Alertas',
            en: 'Alerts',
            submenus: [
                { pt: 'Ações', en: 'Actions' },
                { pt: 'Tipos de mídia', en: 'Media types' },
                { pt: 'Scripts', en: 'Scripts' }
            ]
        },
        {
            key: 'users',
            pt: 'Usuários',
            en: 'Users',
            submenus: [
                { pt: 'Grupos de usuários', en: 'User groups' },
                { pt: 'Funções de usuários', en: 'User roles' },
                { pt: 'Usuários', en: 'Users' },
                { pt: 'Autenticação', en: 'Authentication' },
                { pt: 'Tokens de API', en: 'API tokens' }
            ]
        },
        {
            key: 'administration',
            pt: 'Administração',
            en: 'Administration',
            submenus: [
                { pt: 'Geral', en: 'General' },
                { pt: 'Módulos', en: 'Modules' }
            ]
        }
    ];

    // Árvore real de menus do Zabbix do PHP (inclui todos os menus na barra lateral) / Real Zabbix menu tree from PHP (includes ALL menus in sidebar)
    const REAL_MENU_TREE = window.masszMenuTree || [];

    // Variáveis de estado para a UI dividida / State variables for split UI
    let activeMenuIndex = -1; // -1 para adicionar um novo menu, número do índice para editar um menu existente / -1 for adding a new menu, index number for editing an existing menu
    let activeMenu = getBlankMenu();

    let currentIconTargetId = '';
    let currentIconTab = 'native';
    let config = window.masszConfig || { allowed_user_group: '', default_icon: 'zi-monitoring', menus: [] };
    const isPt = (window.masszLang || 'pt_BR').startsWith('pt');
    
    // Garante que window.masszLabels está definido com padrões / Ensure window.masszLabels is defined with defaults
    window.masszLabels = Object.assign({
        removeMenu: 'Remover Menu',
        removeLink: 'Remover Link',
        addLink: 'Adicionar Link',
        menuType: 'Tipo de Menu',
        newMenuBase: 'Novo Menu Base',
        insertExistingMenu: 'Inserir em Menu Existente',
        titlePt: 'Título (Português)',
        titleEn: 'Título (Inglês)',
        menuIcon: 'Ícone do Menu',
        targetMenu: 'Menu de Destino',
        targetMenuHelp: 'Ex: Monitoramento, Serviços, Inventário',
        insertAfter: 'Inserir Após o Item',
        insertAfterHelp: 'Ex: Dashboard, Problemas, Hosts',
        choose: 'Escolher',
        linkSettings: 'Configurações do Link',
        linkId: 'Identificador do Link (Único, apenas letras/números)',
        linkTitlePt: 'Título do Link (Português)',
        linkTitleEn: 'Título do Link (Inglês)',
        linkUrl: 'URL do Link',
        linkUrlHelp: 'Use {HOST} para apontar dinamicamente para o host do Zabbix',
        openingType: 'Tipo de Abertura',
        iframeSecure: 'Iframe Seguro (Interno)',
        newTab: 'Nova Aba',
        redirect: 'Redirecionamento',
        iframeSecurity: 'Segurança do Iframe (Sandbox)',
        iframeSecurityHelp: 'Deixe vazio para sem restrições ou marque as permissões necessárias',
        permissionsPolicy: 'Permissions-Policy',
        permissionsPolicyHelp: "Ex: fullscreen; camera 'none'; microphone 'none'",
        referrerPolicy: 'Referrer-Policy',
        referrerPolicyHelp: 'Controle de envio de cabeçalho Referer',
        urlParams: 'Parâmetros de URL (Dados de Sessão)',
        urlParamsHelp: 'Envie variáveis da sessão atual do Zabbix como parâmetros GET na URL da aplicação',
        urlParamsExamples: 'Exemplos: token = 123456 (Valor Fixo) | user = username (Dado de Sessão)',
        addParameter: 'Adicionar Parâmetro',
        paramName: 'Nome da Variável na URL',
        sessionData: 'Dado de Sessão do Zabbix',
        removeParameter: 'Remover Parâmetro',
        linkPreview: 'Visualização prévia do Link',
        linkPreviewHelp: 'URL Completa Resolvida (Exemplo):',
        securityPreset: 'Preset de Segurança',
        presetCustom: 'Personalizado',
        presetBasic: 'Básico (Permite scripts e mesma origem)',
        presetRestricted: 'Restrito (Apenas exibir, sem scripts)',
        presetPermissive: 'Permissivo (Permite formulários, popups, downloads)',
        presetNone: 'Sem restrições',
        saveChanges: 'Salvar Configurações',
        addMenuToList: 'Adicionar Menu à Lista',
        updateMenuInList: 'Atualizar Menu na Lista',
        clearForm: 'Limpar Formulário',
        cancelEdit: 'Cancelar Edição',
        addNewMenuTitle: 'Adicionar Novo Menu',
        editMenuTitle: 'Editar Menu',
        menuDraft: 'Novo Menu (Rascunho)',
        editingMenuLabel: 'Editando Menu',
        noMenusConfigured: 'Nenhum menu configurado. Crie um novo menu para começar.'
    }, window.masszLabels || {});
    
    // Garante que menus é sempre um array / Ensure menus is always an array
    if (!Array.isArray(config.menus)) {
        config.menus = [];
    }

    // Auxiliar de tradução / Translation helper
    function T_t(key, fallback) {
        return window.masszLabels[key] || fallback;
    }

    function getBlankMenu() {
        return {
            id: 'menu_' + Math.random().toString(36).substr(2, 9),
            type: 'new',
            label_pt: '',
            label_en: '',
            icon: 'zi-monitoring',
            links: []
        };
    }

    // Inicializa a renderização da UI / Initialize UI rendering
    renderUI();

    function renderUI() {
        renderConfiguredMenusList();
        renderActiveMenuForm();
    }

    // 1. Renderiza os menus configurados como uma tabela em "#massz-existing-menus-container" / 1. Renders configured menus as a table in "#massz-existing-menus-container"
    function renderConfiguredMenusList() {
        const container = $('#massz-existing-menus-container');
        container.empty();

        if (config.menus.length === 0) {
            $('#massz-empty-state').show();
            return;
        } else {
            $('#massz-empty-state').hide();
        }

        let tableHtml = `
            <table class="massz-table">
                <thead>
                    <tr>
                        <th style="width: 100px; text-align: center;">${isPt ? 'Ordem' : 'Order'}</th>
                        <th>${isPt ? 'Menu' : 'Menu'}</th>
                        <th style="width: 180px;">${isPt ? 'Tipo' : 'Type'}</th>
                        <th style="width: 100px; text-align: center;">${isPt ? 'Links' : 'Links'}</th>
                        <th style="width: 180px; text-align: center;">${isPt ? 'Ações' : 'Actions'}</th>
                    </tr>
                </thead>
                <tbody>
        `;

        config.menus.forEach(function(menu, idx) {
            const isNew = menu.type !== 'existing';
            const typeLabel = isNew ? window.masszLabels.newMenuBase : window.masszLabels.insertExistingMenu;
            const typeClass = isNew ? 'background: #e3f2fd; color: #0d47a1;' : 'background: #fff3e0; color: #e65100;';
            
            let menuLabel = '';
            if (isNew) {
                menuLabel = isPt 
                    ? (menu.label_pt || menu.label_en || 'Sem título') 
                    : (menu.label_en || menu.label_pt || 'No title');
            } else {
                if (menu.links && menu.links.length > 0) {
                    const firstLink = menu.links[0];
                    const linkTitle = isPt 
                        ? (firstLink.label_pt || firstLink.label_en || 'Link sem título') 
                        : (firstLink.label_en || firstLink.label_pt || 'Untitled link');
                    
                    const targetName = isPt 
                        ? (menu.target_menu_pt || menu.target_menu_en || '') 
                        : (menu.target_menu_en || menu.target_menu_pt || '');
                    
                    if (targetName) {
                        menuLabel = isPt 
                            ? `${linkTitle} (em ${targetName})` 
                            : `${linkTitle} (in ${targetName})`;
                    } else {
                        menuLabel = linkTitle;
                    }

                    if (menu.links.length > 1) {
                        menuLabel += ` (+${menu.links.length - 1})`;
                    }
                } else {
                    const targetName = isPt 
                        ? (menu.target_menu_pt || menu.target_menu_en || '') 
                        : (menu.target_menu_en || menu.target_menu_pt || '');
                    menuLabel = isPt 
                        ? `Sem links (em ${targetName || 'Menu Existente'})` 
                        : `No links (in ${targetName || 'Existing Menu'})`;
                }
            }

            const linksCount = (menu.links || []).length;
            const rowClass = '';

            tableHtml += `
                <tr class="${rowClass}">
                    <td style="text-align: center;">
                        <button type="button" class="massz-btn massz-btn-secondary massz-btn-icon massz-btn-small" onclick="moveMenuInTable(${idx}, -1)" ${idx === 0 ? 'disabled' : ''} title="${isPt ? 'Subir' : 'Move Up'}">
                            <span class="zi-chevron-up"></span>
                        </button>
                        <button type="button" class="massz-btn massz-btn-secondary massz-btn-icon massz-btn-small" onclick="moveMenuInTable(${idx}, 1)" ${idx === config.menus.length - 1 ? 'disabled' : ''} title="${isPt ? 'Descer' : 'Move Down'}">
                            <span class="zi-chevron-down"></span>
                        </button>
                    </td>
                    <td style="font-weight: 600; font-size: 13px;">
                        ${isNew ? `<span class="massz-icon ${escapeHtml(menu.icon || 'zi-monitoring')}" style="margin-right: 6px; background-color: #555;"></span>` : ''}
                        ${escapeHtml(menuLabel)}
                    </td>
                    <td>
                        <span class="massz-menu-badge" style="${typeClass}">${escapeHtml(typeLabel)}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="massz-link-count">${linksCount}</span>
                    </td>
                    <td style="text-align: center;">
                        <div style="display: flex; justify-content: center; align-items: center; gap: 6px;">
                            <button type="button" class="massz-btn massz-btn-primary massz-btn-small" onclick="editMenuInForm(${idx})">
                                <span class="zi-edit"></span> ${isPt ? 'Editar' : 'Edit'}
                            </button>
                            <button type="button" class="massz-btn massz-btn-danger massz-btn-small" onclick="deleteMenuInTable(${idx})">
                                <span class="zi-trash"></span> ${isPt ? 'Excluir' : 'Delete'}
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tableHtml += `
                </tbody>
            </table>
        `;

        container.append(tableHtml);
    }

    // Auxiliar para obter a chave de destino ativa com base nos valores do modelo de menu / Helper to get active target key based on menu model values
    // Resolve o valor do menu de destino por ordem: barra lateral do Zabbix primeiro, depois menus customizados, depois fallback / Resolves target menu value in order: Zabbix sidebar first, then custom menus, then fallback.
    function getMenuTargetValue(menu) {
        if (!menu.target_menu_pt && !menu.target_menu_en) {
            return '';
        }
        const targetPt = (menu.target_menu_pt || '').toLowerCase();
        const targetEn = (menu.target_menu_en || '').toLowerCase();

        // 1. Tenta corresponder contra a árvore real de menus do Zabbix primeiro / 1. Try to match against real Zabbix menu tree first
        for (let i = 0; i < REAL_MENU_TREE.length; i++) {
            const label = (REAL_MENU_TREE[i].label || '').toLowerCase();
            if (label === targetPt || label === targetEn) {
                return 'real:' + i;
            }
        }

        // 2. Tenta corresponder contra outros menus customizados do tipo 'new' / 2. Try to match against other custom menus of type 'new'
        for (let i = 0; i < config.menus.length; i++) {
            const m = config.menus[i];
            if (m.type === 'new' && m.id !== menu.id) {
                const labelPt = (m.label_pt || '').toLowerCase();
                const labelEn = (m.label_en || '').toLowerCase();
                if ((labelPt && (labelPt === targetPt || labelPt === targetEn)) ||
                    (labelEn && (labelEn === targetPt || labelEn === targetEn))) {
                    return 'custom:' + m.id;
                }
            }
        }

        // Fallback: valor salvo mas não encontrado na árvore (texto antigo) / Fallback: value saved but not found in tree (old text)
        return 'free:' + (menu.target_menu_pt || menu.target_menu_en);
    }

    function getMenuAfterValue(menu) {
        return menu.insert_after_pt || menu.insert_after_en || '';
    }

    // Busca todos os menus de destino disponíveis / Fetches all available target menus.
    // Para evitar duplicados, adicionamos todos os menus da barra lateral do Zabbix (incluindo os dinâmicos)
    // e depois adicionamos os menus customizados que ainda não foram salvos/carregados no Zabbix.
    // In order to avoid duplicates, we add all Zabbix sidebar menus (including dynamically added ones)
    // and then add custom menus that haven't been saved/loaded to Zabbix sidebar yet.
    function getAvailableTargetMenus() {
        const list = [];

        // Todos os menus atualmente renderizados na barra lateral do Zabbix / All menus currently rendered in the Zabbix sidebar
        REAL_MENU_TREE.forEach(function(m, idx) {
            list.push({
                value: 'real:' + idx,
                label: m.label
            });
        });

        // Adiciona menus customizados do tipo 'new' que ainda não estão carregados na barra lateral do Zabbix (ou no menu ativo) / Add custom menus of type 'new' that are not yet loaded in Zabbix sidebar (or activeMenu)
        config.menus.forEach(function(m, idx) {
            if (m.type === 'new' && m.id !== activeMenu.id) {
                const label = m.label_pt || m.label_en || 'MASSZ (' + (idx + 1) + ')';
                // Verifica se este menu já está na lista (correlacionando por rótulo, insensível a maiúsculas/minúsculas) / Check if this menu is already in the list (matching by label, case-insensitive)
                const alreadyExists = list.some(item => item.label.toLowerCase() === label.toLowerCase());
                if (!alreadyExists) {
                    list.push({
                        value: 'custom:' + m.id,
                        label: label
                    });
                }
            }
        });

        return list;
    }

    function getAvailableSubmenus(targetValue) {
        const submenus = [];
        submenus.push({ value: '', label: isPt ? 'Adicionar ao Final' : 'Add to End' });

        if (!targetValue) return submenus;

        const parts = targetValue.split(':');
        const type = parts[0];

        if (type === 'real') {
            const idx = parseInt(parts[1]);
            if (REAL_MENU_TREE[idx]) {
                const realMenu = REAL_MENU_TREE[idx];
                if (realMenu.submenus && realMenu.submenus.length > 0) {
                    realMenu.submenus.forEach(function(sub) {
                        submenus.push({
                            value: sub.label,
                            label: sub.label
                        });
                    });
                }
            }
        } else if (type === 'custom') {
            const mId = parts[1];
            const targetM = config.menus.find(m => m.id === mId);
            if (targetM && targetM.links && targetM.links.length > 0) {
                targetM.links.forEach(function(link) {
                    const label = link.label_pt || link.label_en || link.id;
                    submenus.push({
                        value: label,
                        label: label
                    });
                });
            }
        }

        return submenus;
    }

    // 2. Renderiza o formulário de menu ativo de adição/edição em "#massz-menus-container" / 2. Renders the active add/edit menu form in "#massz-menus-container"
    function renderActiveMenuForm() {
        const container = $('#massz-menus-container');
        container.empty();

        const menu = activeMenu;
        const isNew = menu.type !== 'existing';
        
        // Atualiza o título e cabeçalho do formulário dependendo do estado de edição ou adição / Update form title and header depending on editing or adding state
        const formTitleSpan = $('#massz-form-title');
        if (activeMenuIndex === -1) {
            formTitleSpan.text(T_t('addNewMenuTitle', 'Adicionar Novo Menu'));
        } else {
            const menuLabel = isNew ? (menu.label_pt || '') : (menu.target_menu_pt || '');
            formTitleSpan.text(T_t('editMenuTitle', 'Editar Menu') + (menuLabel ? ': ' + menuLabel : ''));
        }

        const badgeLabel = isNew ? window.masszLabels.newMenuBase : window.masszLabels.insertExistingMenu;
        const cardClass = isNew ? 'massz-menu-card' : 'massz-menu-card existing-menu';

        let html = `
            <div class="${cardClass}" style="border-left-width: 6px;">
                <div class="massz-menu-header">
                    <div class="massz-menu-title-area">
                        <span class="massz-menu-badge">${badgeLabel}</span>
                        <strong style="font-size: 14px;">
                            ${activeMenuIndex === -1 ? T_t('menuDraft', 'Novo Menu (Rascunho)') : T_t('editingMenuLabel', 'Editando Menu')}
                        </strong>
                    </div>
                </div>

                <!-- Grade de Configuração do Menu / Menu Configuration Grid -->
                <div class="massz-form-row">
                    <div class="massz-form-group massz-col-4">
                        <label>${window.masszLabels.menuType}</label>
                        <select class="massz-select menu-type-select" onchange="changeActiveMenuType(this.value)">
                            <option value="new" ${isNew ? 'selected' : ''}>${window.masszLabels.newMenuBase}</option>
                            <option value="existing" ${!isNew ? 'selected' : ''}>${window.masszLabels.insertExistingMenu}</option>
                        </select>
                    </div>
        `;

        if (isNew) {
            html += `
                    <div class="massz-form-group massz-col-4">
                        <label>${isPt ? 'Título *' : 'Title *'}</label>
                        <input type="text" class="massz-input menu-label" value="${escapeHtml(isPt ? (menu.label_pt || '') : (menu.label_en || ''))}" placeholder="${escapeHtml(isPt ? (menu.label_en || 'Título...') : (menu.label_pt || 'Title...'))}" oninput="updateActiveMenuField('${isPt ? 'label_pt' : 'label_en'}', this.value)">
                    </div>
                    <div class="massz-form-group massz-col-4">
                        <label>${window.masszLabels.menuIcon}</label>
                        <div class="massz-icon-input-wrapper">
                            <span class="massz-selected-icon-preview" id="menu-icon-prev-active">
                                <span class="${escapeHtml(menu.icon || config.default_icon || 'zi-monitoring')}"></span>
                            </span>
                            <input type="text" id="menu-icon-input-active" class="massz-input menu-icon" value="${escapeHtml(menu.icon || '')}" readonly>
                            <button type="button" class="massz-btn massz-btn-secondary" onclick="openIconPicker('menu-icon-input-active')">${window.masszLabels.choose}</button>
                        </div>
                    </div>
            `;
        } else {
            const targetValue = getMenuTargetValue(menu);
            const afterValue = getMenuAfterValue(menu);

            // Gera as opções de menu de destino / Generate target menu options
            let targetOptions = `<option value="">-- Selecione o Menu --</option>`;
            const availableTargets = getAvailableTargetMenus();
            let hasSelectedTarget = false;

            availableTargets.forEach(function(item) {
                const isSelected = item.value === targetValue;
                if (isSelected) hasSelectedTarget = true;
                targetOptions += `<option value="${item.value}" ${isSelected ? 'selected' : ''}>${escapeHtml(item.label)}</option>`;
            });

            if (targetValue && targetValue.startsWith('free:') && !hasSelectedTarget) {
                const freeVal = targetValue.substring(5);
                targetOptions += `<option value="${targetValue}" selected>${escapeHtml(freeVal)} (Texto Livre Antigo)</option>`;
            }

            // Gera as opções de inserção após item / Generate insert after options
            let afterOptions = '';
            const availableAfter = getAvailableSubmenus(targetValue);
            let hasSelectedAfter = false;

            availableAfter.forEach(function(item) {
                const isSelected = (item.value && item.value === afterValue);
                if (isSelected) hasSelectedAfter = true;
                afterOptions += `<option value="${escapeHtml(item.value || '')}" ${isSelected ? 'selected' : ''}>${escapeHtml(item.label)}</option>`;
            });

            if (afterValue && !hasSelectedAfter && afterValue !== '') {
                afterOptions += `<option value="${escapeHtml(afterValue)}" selected>${escapeHtml(afterValue)} (Texto Livre Antigo)</option>`;
            }

            html += `
                    <div class="massz-form-group massz-col-4">
                        <label>${window.masszLabels.targetMenu} *</label>
                        <select class="massz-select menu-target-select" onchange="updateActiveMenuTarget(this.value)">
                            ${targetOptions}
                        </select>
                    </div>
                    <div class="massz-form-group massz-col-4">
                        <label>${window.masszLabels.insertAfter}</label>
                        <select class="massz-select menu-after-select" onchange="updateActiveMenuAfter(this.value)">
                            ${afterOptions}
                        </select>
                    </div>
            `;
        }

        html += `
                </div>

                <!-- Links Config Container -->
                <div class="massz-links-section">
                    <div class="massz-links-header">
                        <span>Links (${(menu.links || []).length})</span>
                        <button type="button" class="massz-btn massz-btn-success massz-btn-small" onclick="addNewActiveLink()">
                            <span class="zi-plus"></span> ${window.masszLabels.addLink}
                        </button>
                    </div>
                    <div class="massz-links-list">
        `;

        if (menu.links && menu.links.length > 0) {
            menu.links.forEach(function(link, lIdx) {
                const isOpenAdvanced = link._openAdvanced === true;
                html += `
                    <div class="massz-link-item" data-lindex="${lIdx}">
                        <div class="massz-link-header">
                            <div class="massz-link-title">
                                <span class="zi-link"></span>
                                <span><strong>${escapeHtml(link.label_pt || 'Link ' + (lIdx+1))}</strong></span>
                                <span class="massz-menu-badge" style="background:#e8f5e9; color:#2e7d32; text-transform:none;">${link.link_type || 'iframe'}</span>
                            </div>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <button type="button" class="massz-btn massz-btn-secondary massz-btn-icon massz-btn-small" onclick="moveActiveLink(${lIdx}, -1)" ${lIdx === 0 ? 'disabled' : ''}>
                                    <span class="zi-chevron-up"></span>
                                </button>
                                <button type="button" class="massz-btn massz-btn-secondary massz-btn-icon massz-btn-small" onclick="moveActiveLink(${lIdx}, 1)" ${lIdx === menu.links.length - 1 ? 'disabled' : ''}>
                                    <span class="zi-chevron-down"></span>
                                </button>
                                <button type="button" class="massz-btn massz-btn-danger massz-btn-icon massz-btn-small" onclick="removeActiveLink(${lIdx})" title="${window.masszLabels.removeLink}">
                                    <span class="zi-trash"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Linha do formulário do link / Link form row -->
                        <div class="massz-form-row">
                            <div class="massz-form-group massz-col-6">
                                <label>${window.masszLabels.linkId} *</label>
                                <input type="text" class="massz-input link-id" value="${escapeHtml(link.id || '')}" placeholder="Ex: grafana" oninput="updateActiveLinkField(${lIdx}, 'id', this.value.replace(/[^a-zA-Z0-9_-]/g, ''))">
                            </div>
                            <div class="massz-form-group massz-col-6">
                                <label>${isPt ? 'Título do Link *' : 'Link Title *'}</label>
                                <input type="text" class="massz-input link-label" value="${escapeHtml(isPt ? (link.label_pt || '') : (link.label_en || ''))}" placeholder="${escapeHtml(isPt ? (link.label_en || 'Título do Link...') : (link.label_pt || 'Link Title...'))}" oninput="updateActiveLinkField(${lIdx}, '${isPt ? 'label_pt' : 'label_en'}', this.value)">
                            </div>
                        </div>
                        <div class="massz-form-row">
                            <div class="massz-form-group massz-col-8">
                                <label>${window.masszLabels.linkUrl} *</label>
                                <input type="text" class="massz-input link-url" value="${escapeHtml(link.url || '')}" placeholder="https://{HOST}/my-app/" oninput="updateActiveLinkField(${lIdx}, 'url', this.value)">
                                <small style="color:var(--massz-text-muted); font-size:11px;">${window.masszLabels.linkUrlHelp}</small>
                            </div>
                            <div class="massz-form-group massz-col-4">
                                <label>${window.masszLabels.openingType}</label>
                                <select class="massz-select link-type-select" onchange="updateActiveLinkField(${lIdx}, 'link_type', this.value)">
                                    <option value="iframe" ${link.link_type === 'iframe' ? 'selected' : ''}>${window.masszLabels.iframeSecure}</option>
                                    <option value="newtab" ${link.link_type === 'newtab' ? 'selected' : ''}>${window.masszLabels.newTab}</option>
                                    <option value="redirect" ${link.link_type === 'redirect' ? 'selected' : ''}>${window.masszLabels.redirect}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Alternador de detalhes avançados / Advanced details toggler -->
                        <div class="massz-details-toggle" onclick="toggleActiveAdvancedSettings(${lIdx})">
                            <span class="${isOpenAdvanced ? 'zi-chevron-up' : 'zi-chevron-down'}"></span>
                            <span>${window.masszLabels.advancedSettings || 'Configurações Avançadas de Segurança & Parâmetros'}</span>
                        </div>

                        <!-- Advanced Settings Box -->
                        <div class="massz-link-details" style="display: ${isOpenAdvanced ? 'block' : 'none'};">
                            <!-- Security presets & policies -->
                            <div class="massz-form-row">
                                <div class="massz-form-group massz-col-6">
                                    <label>${window.masszLabels.securityPreset}</label>
                                    <select class="massz-select security-preset" onchange="applyActiveSecurityPreset(${lIdx}, this.value)">
                                        <option value="custom">${window.masszLabels.presetCustom}</option>
                                        <option value="basic" ${isPresetActive(link, 'basic') ? 'selected' : ''}>${window.masszLabels.presetBasic}</option>
                                        <option value="restricted" ${isPresetActive(link, 'restricted') ? 'selected' : ''}>${window.masszLabels.presetRestricted}</option>
                                        <option value="permissive" ${isPresetActive(link, 'permissive') ? 'selected' : ''}>${window.masszLabels.presetPermissive}</option>
                                        <option value="none" ${isPresetActive(link, 'none') ? 'selected' : ''}>${window.masszLabels.presetNone}</option>
                                    </select>
                                </div>
                                <div class="massz-form-group massz-col-6">
                                    <label>${window.masszLabels.referrerPolicy}</label>
                                    <select class="massz-select referrer-policy-select" onchange="updateActiveLinkField(${lIdx}, 'referrer_policy', this.value)">
                                        <option value="no-referrer" ${link.referrer_policy === 'no-referrer' ? 'selected' : ''}>no-referrer</option>
                                        <option value="strict-origin" ${link.referrer_policy === 'strict-origin' ? 'selected' : ''}>strict-origin</option>
                                        <option value="same-origin" ${link.referrer_policy === 'same-origin' ? 'selected' : ''}>same-origin</option>
                                        <option value="no-referrer-when-downgrade" ${link.referrer_policy === 'no-referrer-when-downgrade' ? 'selected' : ''}>no-referrer-when-downgrade</option>
                                        <option value="origin" ${link.referrer_policy === 'origin' ? 'selected' : ''}>origin</option>
                                        <option value="origin-when-cross-origin" ${link.referrer_policy === 'origin-when-cross-origin' ? 'selected' : ''}>origin-when-cross-origin</option>
                                        <option value="unsafe-url" ${link.referrer_policy === 'unsafe-url' ? 'selected' : ''}>unsafe-url</option>
                                    </select>
                                </div>
                            </div>

                            <div class="massz-form-row">
                                <div class="massz-form-group massz-col-12">
                                    <label>${window.masszLabels.iframeSecurity}</label>
                                    <div style="display:flex; flex-wrap:wrap; gap:12px; margin-top:5px;">
                                        <label style="font-weight:normal; display:flex; align-items:center; gap:4px;">
                                            <input type="checkbox" class="sandbox-chk" value="allow-scripts" ${hasSandboxOption(link.sandbox, 'allow-scripts') ? 'checked' : ''} onchange="updateActiveSandbox(${lIdx})"> allow-scripts
                                        </label>
                                        <label style="font-weight:normal; display:flex; align-items:center; gap:4px;">
                                            <input type="checkbox" class="sandbox-chk" value="allow-same-origin" ${hasSandboxOption(link.sandbox, 'allow-same-origin') ? 'checked' : ''} onchange="updateActiveSandbox(${lIdx})"> allow-same-origin
                                        </label>
                                        <label style="font-weight:normal; display:flex; align-items:center; gap:4px;">
                                            <input type="checkbox" class="sandbox-chk" value="allow-forms" ${hasSandboxOption(link.sandbox, 'allow-forms') ? 'checked' : ''} onchange="updateActiveSandbox(${lIdx})"> allow-forms
                                        </label>
                                        <label style="font-weight:normal; display:flex; align-items:center; gap:4px;">
                                            <input type="checkbox" class="sandbox-chk" value="allow-popups" ${hasSandboxOption(link.sandbox, 'allow-popups') ? 'checked' : ''} onchange="updateActiveSandbox(${lIdx})"> allow-popups
                                        </label>
                                        <label style="font-weight:normal; display:flex; align-items:center; gap:4px;">
                                            <input type="checkbox" class="sandbox-chk" value="allow-downloads" ${hasSandboxOption(link.sandbox, 'allow-downloads') ? 'checked' : ''} onchange="updateActiveSandbox(${lIdx})"> allow-downloads
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="massz-form-row">
                                <div class="massz-form-group massz-col-12">
                                    <label>${window.masszLabels.permissionsPolicy}</label>
                                    <input type="text" class="massz-input permissions-policy-input" value="${escapeHtml(link.allow_permissions || '')}" placeholder="${window.masszLabels.permissionsPolicyHelp}" oninput="updateActiveLinkField(${lIdx}, 'allow_permissions', this.value)">
                                </div>
                            </div>
                            <!-- Session variables params -->
                            <div style="margin-top:12px;">
                                <label style="font-size:12px; font-weight:600; color:var(--massz-text-muted); display:flex; justify-content:space-between; align-items:center;">
                                    <span>${window.masszLabels.urlParams}</span>
                                    <button type="button" class="massz-btn massz-btn-primary massz-btn-small" onclick="addNewActiveParam(${lIdx})">
                                        <span class="zi-plus"></span> ${window.masszLabels.addParameter}
                                    </button>
                                </label>
                                <small style="color:var(--massz-text-muted); font-size:11px; display:block; margin-bottom:5px;">
                                    ${window.masszLabels.urlParamsHelp}<br>
                                    <strong>${window.masszLabels.urlParamsExamples}</strong>
                                </small>
                                
                                <div class="massz-params-list" id="link-params-list-active-${lIdx}">
                                    ${renderActiveLinkParams(lIdx, link.url_params)}
                                </div>
                            </div>

                            <!-- URL Preview Box -->
                            <div style="margin-top:15px; padding:10px; background:#fff8e1; border:1px solid #ffe082; border-radius:4px;">
                                <strong style="font-size:11px; color:#b78103;">${window.masszLabels.linkPreview}</strong>
                                <div style="font-family:monospace; font-size:11px; word-break:break-all; margin-top:4px; color:#5d4037;">
                                    ${escapeHtml(window.masszLabels.linkPreviewHelp)} <span id="link-preview-url-active-${lIdx}">${calculateUrlPreview(link)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html += `<div style="text-align:center; padding:15px; color:var(--massz-text-muted); font-style:italic; font-size:12px;">${isPt ? 'Não há links neste menu.' : 'There are no links in this menu.'}</div>`;
        }

        html += `
                    </div>
                </div>

                <!-- Active Menu Actions -->
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--massz-gray-border);">
        `;

        if (activeMenuIndex === -1) {
            html += `
                <button type="button" class="massz-btn massz-btn-secondary" onclick="cancelMenuEdit()">
                    ${window.masszLabels.clearForm}
                </button>
                <button type="button" class="massz-btn massz-btn-primary" onclick="saveActiveMenuToList()">
                    <span class="zi-plus"></span> ${window.masszLabels.addMenuToList}
                </button>
                <button type="button" class="massz-btn massz-btn-success massz-btn-save-action" onclick="saveConfiguration()">
                    <span class="zi-check"></span> ${window.masszLabels.saveGeneralSettings || 'Salvar Configurações Gerais'}
                </button>
            `;
        } else {
            html += `
                <button type="button" class="massz-btn massz-btn-secondary" onclick="cancelMenuEdit()">
                    ${window.masszLabels.cancelEdit}
                </button>
                <button type="button" class="massz-btn massz-btn-primary" onclick="saveActiveMenuToList()">
                    <span class="zi-check"></span> ${window.masszLabels.updateMenuInList}
                </button>
                <button type="button" class="massz-btn massz-btn-success massz-btn-save-action" onclick="saveConfiguration()">
                    <span class="zi-check"></span> ${window.masszLabels.saveGeneralSettings || 'Salvar Configurações Gerais'}
                </button>
            `;
        }

        html += `
                </div>
            </div>
        `;

        container.append(html);
    }

    // Funções auxiliares para análise de opções do sandbox / Helper functions for sandbox option parsing
    function hasSandboxOption(sandbox, val) {
        if (!sandbox) return false;
        return sandbox.split(' ').indexOf(val) !== -1;
    }

    function isPresetActive(link, preset) {
        const sandbox = link.sandbox || '';
        const allow = link.allow_permissions || '';
        const ref = link.referrer_policy || '';

        if (preset === 'basic') {
            return sandbox === 'allow-scripts allow-same-origin allow-forms' && allow === 'fullscreen' && ref === 'no-referrer';
        }
        if (preset === 'restricted') {
            return sandbox === 'allow-same-origin' && allow === '' && ref === 'strict-origin';
        }
        if (preset === 'permissive') {
            return sandbox === 'allow-scripts allow-same-origin allow-forms allow-popups allow-downloads' && allow === 'fullscreen' && ref === 'no-referrer';
        }
        if (preset === 'none') {
            return sandbox === '' && allow === '' && ref === 'no-referrer';
        }
        return false;
    }

    // Renderiza parâmetros de URL dentro do formulário de menu ativo / Render URL parameters inside the active menu form
    function renderActiveLinkParams(lIdx, params) {
        if (!params || params.length === 0) {
            return `<div style="text-align:center; font-size:11px; color:var(--massz-text-muted); font-style:italic; padding:5px;">${window.masszLabels.noParamsConfigured || 'Nenhum parâmetro configurado.'}</div>`;
        }

        let html = '';
        params.forEach(function(param, pIdx) {
            let optionsHtml = '';
            window.masszSessionSources.forEach(function(src) {
                optionsHtml += `<option value="${src.value}" ${param.source === src.value ? 'selected' : ''}>${escapeHtml(src.label)}</option>`;
            });

            let isCustom = param.source === 'custom';
            let customValueInput = '';
            if (isCustom) {
                customValueInput = `
                    <input type="text" class="massz-input" style="flex:2;" value="${escapeHtml(param.value || '')}" placeholder="${isPt ? 'Valor...' : 'Value...'}" oninput="updateActiveParamField(${lIdx}, ${pIdx}, 'value', this.value)">
                `;
            }

            html += `
                <div class="massz-param-row">
                    <input type="text" class="massz-input" style="flex:1.5;" value="${escapeHtml(param.param_name || '')}" placeholder="${window.masszLabels.paramName}" oninput="updateActiveParamField(${lIdx}, ${pIdx}, 'param_name', this.value.replace(/[^a-zA-Z0-9_-]/g, ''))">
                    <select class="massz-select" style="flex:2;" onchange="updateActiveParamSource(${lIdx}, ${pIdx}, this.value)">
                        ${optionsHtml}
                    </select>
                    ${customValueInput}
                    <button type="button" class="massz-btn massz-btn-danger massz-btn-icon massz-btn-small" onclick="removeActiveParam(${lIdx}, ${pIdx})" title="${window.masszLabels.removeParameter}">
                        <span class="zi-trash"></span>
                    </button>
                </div>
            `;
        });

        return html;
    }

    // Calcula uma pré-visualização de URL fictícia / Calculate a mockup url preview
    function calculateUrlPreview(link) {
        let url = link.url || 'https://example.com/';
        url = url.replace('{HOST}', window.location.hostname || 'zabbix.empresa.com');

        const params = [];
        if (link.url_params && link.url_params.length > 0) {
            link.url_params.forEach(function(p) {
                if (p.param_name) {
                    let fakeVal = 'value';
                    if (p.source === 'custom') fakeVal = p.value || '';
                    else if (p.source === 'username') fakeVal = 'admin';
                    else if (p.source === 'userid') fakeVal = '1';
                    else if (p.source === 'sessionid') fakeVal = 'cb47a28e83b8';
                    else if (p.source === 'lang') fakeVal = 'pt_BR';
                    params.push(encodeURIComponent(p.param_name) + '=' + encodeURIComponent(fakeVal));
                }
            });
        }

        if (params.length > 0) {
            const connector = url.indexOf('?') === -1 ? '?' : '&';
            url += connector + params.join('&');
        }

        return url;
    }

    // Lógica do modal seletor de ícone / Modal Icon Picker Logic
    function openIconPicker(targetId) {
        currentIconTargetId = targetId;
        $('#massz-icon-modal').addClass('active');
        $('#massz-icon-search').val('');
        switchIconTab('native');
    }

    function closeIconPicker() {
        $('#massz-icon-modal').removeClass('active');
        currentIconTargetId = '';
    }

    function switchIconTab(tab) {
        currentIconTab = tab;
        $('.massz-modal-tab').removeClass('active');
        if (tab === 'native') $('.massz-modal-tab').eq(0).addClass('active');
        else if (tab === 'custom') $('.massz-modal-tab').eq(1).addClass('active');

        renderIconsGrid();
    }

    function renderIconsGrid() {
        const grid = $('#massz-icon-grid-container');
        grid.empty();

        let iconList = [];
        if (currentIconTab === 'native') iconList = NATIVE_ICONS;
        else if (currentIconTab === 'custom') iconList = CUSTOM_ICONS;

        const q = $('#massz-icon-search').val().toLowerCase();

        iconList.forEach(function(icon) {
            if (q && icon.toLowerCase().indexOf(q) === -1) {
                return; // search filter
            }

            let iconHtml = '';
            if (currentIconTab === 'custom') {
                iconHtml = `<span class="massz-icon ${icon} icon-lg"></span>`;
            } else {
                iconHtml = `<span class="${icon}" style="font-size: 20px;"></span>`;
            }

            const cell = $(`
                <div class="massz-icon-cell" title="${icon}">
                    <div class="icon-preview">${iconHtml}</div>
                    <div class="icon-name">${icon}</div>
                </div>
            `);

            cell.on('click', function() {
                selectIcon(icon);
            });

            grid.append(cell);
        });
    }

    function filterIcons() {
        renderIconsGrid();
    }

    function selectIcon(iconName) {
        if (!currentIconTargetId) return;

        const input = document.getElementById(currentIconTargetId);
        if (input) {
            input.value = iconName;
            $(input).trigger('input');
            $(input).trigger('change');

            if (currentIconTargetId === 'default_icon') {
                $('#default-icon-preview span').attr('class', iconName);
            } else if (currentIconTargetId === 'menu-icon-input-active') {
                $('#menu-icon-prev-active span').attr('class', iconName);
                activeMenu.icon = iconName;
            }
        }

        closeIconPicker();
    }

    // Ações de estado / State actions
    function startNewMenu() {
        activeMenuIndex = -1;
        activeMenu = getBlankMenu();
        renderUI();
    }

    function editMenuInForm(idx) {
        activeMenuIndex = idx;
        activeMenu = JSON.parse(JSON.stringify(config.menus[idx]));
        renderUI();
    }

    function deleteMenuInTable(idx) {
        const confirmMsg = isPt 
            ? 'Tem certeza que deseja remover este menu e todos os seus links?' 
            : 'Are you sure you want to remove this menu and all its links?';
        if (confirm(confirmMsg)) {
            config.menus.splice(idx, 1);
            if (activeMenuIndex === idx) {
                startNewMenu();
            } else if (activeMenuIndex > idx) {
                activeMenuIndex--;
            }
            renderUI();
            saveConfiguration();
        }
    }

    function moveMenuInTable(idx, dir) {
        const targetIdx = idx + dir;
        if (targetIdx < 0 || targetIdx >= config.menus.length) return;

        const temp = config.menus[idx];
        config.menus[idx] = config.menus[targetIdx];
        config.menus[targetIdx] = temp;

        if (activeMenuIndex === idx) {
            activeMenuIndex = targetIdx;
        } else if (activeMenuIndex === targetIdx) {
            activeMenuIndex = idx;
        }

        renderUI();
        saveConfiguration();
    }

    function changeActiveMenuType(type) {
        activeMenu.type = type;
        if (type === 'existing') {
            activeMenu.target_menu_pt = '';
            activeMenu.target_menu_en = '';
            activeMenu.insert_after_pt = '';
            activeMenu.insert_after_en = '';
            delete activeMenu.label_pt;
            delete activeMenu.label_en;
            delete activeMenu.icon;
        } else {
            activeMenu.label_pt = '';
            activeMenu.label_en = '';
            activeMenu.icon = 'zi-monitoring';
            delete activeMenu.target_menu_pt;
            delete activeMenu.target_menu_en;
            delete activeMenu.insert_after_pt;
            delete activeMenu.insert_after_en;
        }
        renderUI();
    }

    function updateActiveMenuField(field, value) {
        activeMenu[field] = value;
    }



    function updateActiveMenuTarget(value) {
        if (!value) {
            activeMenu.target_menu_pt = '';
            activeMenu.target_menu_en = '';
            activeMenu.insert_after_pt = '';
            activeMenu.insert_after_en = '';
            renderUI();
            return;
        }

        const parts = value.split(':');
        const type = parts[0];

        if (type === 'real') {
            const idx = parseInt(parts[1]);
            const realMenu = REAL_MENU_TREE[idx];
            if (realMenu) {
                const labelLower = realMenu.label.toLowerCase();
                const native = NATIVE_MENUS.find(nm => nm.pt.toLowerCase() === labelLower || nm.en.toLowerCase() === labelLower);
                if (native) {
                    activeMenu.target_menu_pt = native.pt;
                    activeMenu.target_menu_en = native.en;
                } else {
                    activeMenu.target_menu_pt = realMenu.label;
                    activeMenu.target_menu_en = realMenu.label;
                }
            }
        } else if (type === 'custom') {
            const mId = parts[1];
            const targetM = config.menus.find(m => m.id === mId);
            if (targetM) {
                activeMenu.target_menu_pt = targetM.label_pt || '';
                activeMenu.target_menu_en = targetM.label_en || '';
            }
        } else if (type === 'free') {
            const freeVal = value.substring(5);
            activeMenu.target_menu_pt = freeVal;
            activeMenu.target_menu_en = freeVal;
        }

        activeMenu.insert_after_pt = '';
        activeMenu.insert_after_en = '';
        renderUI();
    }

    function updateActiveMenuAfter(value) {
        if (!value) {
            activeMenu.insert_after_pt = '';
            activeMenu.insert_after_en = '';
            renderUI();
            return;
        }

        const targetValue = getMenuTargetValue(activeMenu);
        if (!targetValue) {
            activeMenu.insert_after_pt = value;
            activeMenu.insert_after_en = value;
            renderUI();
            return;
        }

        const parts = targetValue.split(':');
        const targetType = parts[0];

        if (targetType === 'real') {
            const idx = parseInt(parts[1]);
            const realMenu = REAL_MENU_TREE[idx];
            if (realMenu) {
                const labelLower = realMenu.label.toLowerCase();
                const native = NATIVE_MENUS.find(nm => nm.pt.toLowerCase() === labelLower || nm.en.toLowerCase() === labelLower);
                if (native && native.submenus) {
                    const valueLower = value.toLowerCase();
                    const sub = native.submenus.find(s => s.pt.toLowerCase() === valueLower || s.en.toLowerCase() === valueLower);
                    if (sub) {
                        activeMenu.insert_after_pt = sub.pt;
                        activeMenu.insert_after_en = sub.en;
                    } else {
                        activeMenu.insert_after_pt = value;
                        activeMenu.insert_after_en = value;
                    }
                } else {
                    activeMenu.insert_after_pt = value;
                    activeMenu.insert_after_en = value;
                }
            }
        } else if (targetType === 'custom') {
            const mId = parts[1];
            const targetM = config.menus.find(m => m.id === mId);
            if (targetM && targetM.links) {
                const valueLower = value.toLowerCase();
                const link = targetM.links.find(l => (l.label_pt || '').toLowerCase() === valueLower || (l.label_en || '').toLowerCase() === valueLower || (l.id || '').toLowerCase() === valueLower);
                if (link) {
                    activeMenu.insert_after_pt = link.label_pt || link.id;
                    activeMenu.insert_after_en = link.label_en || link.id;
                } else {
                    activeMenu.insert_after_pt = value;
                    activeMenu.insert_after_en = value;
                }
            }
        } else {
            activeMenu.insert_after_pt = value;
            activeMenu.insert_after_en = value;
        }

        renderUI();
    }

    // Ações ativas de link / Link active actions
    function addNewActiveLink() {
        if (!activeMenu.links) {
            activeMenu.links = [];
        }
        activeMenu.links.push({
            id: 'link_' + Math.random().toString(36).substr(2, 9),
            label_pt: '',
            label_en: '',
            url: '',
            link_type: 'iframe',
            sandbox: 'allow-scripts allow-same-origin allow-forms',
            allow_permissions: 'fullscreen',
            referrer_policy: 'no-referrer',
            url_params: [],
            _openAdvanced: false
        });
        renderUI();
    }

    function removeActiveLink(lIdx) {
        const confirmMsg = isPt ? 'Deseja remover este link?' : 'Do you want to remove this link?';
        if (confirm(confirmMsg)) {
            activeMenu.links.splice(lIdx, 1);
            renderUI();
        }
    }

    function updateActiveLinkField(lIdx, field, value) {
        if (!activeMenu.links || !activeMenu.links[lIdx]) return;
        activeMenu.links[lIdx][field] = value;

        if (field === 'url' || field === 'id' || field === 'link_type') {
            $('#link-preview-url-active-' + lIdx).text(calculateUrlPreview(activeMenu.links[lIdx]));
        }
        if (field === 'label_pt' || field === 'label_en') {
            const labelSpan = $(`.massz-link-item[data-lindex="${lIdx}"] .massz-link-title span strong`);
            const labelVal = isPt ? (activeMenu.links[lIdx].label_pt || activeMenu.links[lIdx].label_en) : (activeMenu.links[lIdx].label_en || activeMenu.links[lIdx].label_pt);
            labelSpan.text(labelVal || 'Link ' + (lIdx + 1));
        }
        if (field === 'link_type') {
            const badgeSpan = $(`.massz-link-item[data-lindex="${lIdx}"] .massz-link-title .massz-menu-badge`);
            badgeSpan.text(value);
        }
    }

    function toggleActiveAdvancedSettings(lIdx) {
        if (activeMenu.links && activeMenu.links[lIdx]) {
            activeMenu.links[lIdx]._openAdvanced = !activeMenu.links[lIdx]._openAdvanced;
            renderUI();
        }
    }

    function moveActiveLink(lIdx, dir) {
        const links = activeMenu.links;
        const targetIdx = lIdx + dir;
        if (targetIdx < 0 || targetIdx >= links.length) return;

        const temp = links[lIdx];
        links[lIdx] = links[targetIdx];
        links[targetIdx] = temp;
        renderUI();
    }

    // Ações ativas de parâmetros de URL / URL params active actions
    function addNewActiveParam(lIdx) {
        if (!activeMenu.links[lIdx].url_params) {
            activeMenu.links[lIdx].url_params = [];
        }
        activeMenu.links[lIdx].url_params.push({
            param_name: '',
            source: 'username'
        });
        renderUI();
    }

    function removeActiveParam(lIdx, pIdx) {
        if (activeMenu.links[lIdx] && activeMenu.links[lIdx].url_params) {
            activeMenu.links[lIdx].url_params.splice(pIdx, 1);
            renderUI();
        }
    }

    function updateActiveParamField(lIdx, pIdx, field, value) {
        if (activeMenu.links[lIdx] && activeMenu.links[lIdx].url_params && activeMenu.links[lIdx].url_params[pIdx]) {
            activeMenu.links[lIdx].url_params[pIdx][field] = value;
            if (field === 'param_name') {
                activeMenu.links[lIdx].url_params[pIdx][field] = value.replace(/[^a-zA-Z0-9_-]/g, '');
            }
            $('#link-preview-url-active-' + lIdx).text(calculateUrlPreview(activeMenu.links[lIdx]));
        }
    }

    function updateActiveParamSource(lIdx, pIdx, value) {
        if (activeMenu.links[lIdx] && activeMenu.links[lIdx].url_params && activeMenu.links[lIdx].url_params[pIdx]) {
            activeMenu.links[lIdx].url_params[pIdx].source = value;
            if (value === 'custom') {
                activeMenu.links[lIdx].url_params[pIdx].value = '';
            } else {
                delete activeMenu.links[lIdx].url_params[pIdx].value;
            }
            renderUI();
        }
    }

    // Atualização de opções de sandbox / Sandbox options updating
    function updateActiveSandbox(lIdx) {
        const item = $('.massz-link-item[data-lindex="' + lIdx + '"]');
        const checked = [];
        item.find('.sandbox-chk:checked').each(function() {
            checked.push($(this).val());
        });
        
        activeMenu.links[lIdx].sandbox = checked.join(' ');
        
        const presetSelect = item.find('.security-preset');
        const currentPreset = presetSelect.val();
        
        if (currentPreset !== 'custom' && !isPresetActive(activeMenu.links[lIdx], currentPreset)) {
            presetSelect.val('custom');
        }
    }

    // Lógica de presets de segurança / Security presets logic
    function applyActiveSecurityPreset(lIdx, preset) {
        const link = activeMenu.links[lIdx];
        if (preset === 'basic') {
            link.sandbox = 'allow-scripts allow-same-origin allow-forms';
            link.allow_permissions = 'fullscreen';
            link.referrer_policy = 'no-referrer';
        } else if (preset === 'restricted') {
            link.sandbox = 'allow-same-origin';
            link.allow_permissions = '';
            link.referrer_policy = 'strict-origin';
        } else if (preset === 'permissive') {
            link.sandbox = 'allow-scripts allow-same-origin allow-forms allow-popups allow-downloads';
            link.allow_permissions = 'fullscreen';
            link.referrer_policy = 'no-referrer';
        } else if (preset === 'none') {
            link.sandbox = '';
            link.allow_permissions = '';
            link.referrer_policy = 'no-referrer';
        }
        renderUI();
    }

    // Valida um menu específico / Validate a specific menu
    function validateActiveMenu(menu, skipConfigIndex = -1) {
        if (menu.type === 'existing') {
            if (!menu.target_menu_pt || !menu.target_menu_en) {
                const targetMenuMsg = isPt 
                    ? 'Por favor, informe o menu de destino.' 
                    : 'Please specify the target menu.';
                alert(targetMenuMsg);
                return false;
            }
            if (!menu.links || menu.links.length === 0) {
                const existingMenuNoLinksMsg = isPt
                    ? 'Menus inseridos em existentes precisam conter pelo menos um link.'
                    : 'Menus inserted into existing ones must contain at least one link.';
                alert(existingMenuNoLinksMsg);
                return false;
            }
        } else {
            if (isPt ? !menu.label_pt : !menu.label_en) {
                const titleRequiredMsg = isPt 
                    ? 'Por favor, informe o título do menu.' 
                    : 'Please specify the menu title.';
                alert(titleRequiredMsg);
                return false;
            }
            // Ensure both are set / Garante que ambos estão definidos
            if (!menu.label_pt) menu.label_pt = menu.label_en;
            if (!menu.label_en) menu.label_en = menu.label_pt;
        }

        let isValid = true;
        if (menu.links) {
            for (let lIdx = 0; lIdx < menu.links.length; lIdx++) {
                const link = menu.links[lIdx];
                if (!link.id) {
                    const linkIdMsg = isPt 
                        ? `Link ${lIdx+1}: O Identificador do Link é obrigatório.` 
                        : `Link ${lIdx+1}: The Link Identifier is required.`;
                    alert(linkIdMsg);
                    isValid = false;
                    break;
                }
                if (!/^[a-zA-Z0-9_-]+$/.test(link.id)) {
                    const linkIdPatternMsg = isPt 
                        ? `Link ${lIdx+1}: O Identificador do Link deve conter apenas letras, números, hífen ou underline.` 
                        : `Link ${lIdx+1}: The Link Identifier must contain only letters, numbers, hyphen or underline.`;
                    alert(linkIdPatternMsg);
                    isValid = false;
                    break;
                }
                if (isPt ? !link.label_pt : !link.label_en) {
                    const linkTitleMsg = isPt 
                        ? `Link ${lIdx+1}: O Título do Link é obrigatório.` 
                        : `Link ${lIdx+1}: The Link Title is required.`;
                    alert(linkTitleMsg);
                    isValid = false;
                    break;
                }
                // Ensure both are set / Garante que ambos estão definidos
                if (!link.label_pt) link.label_pt = link.label_en;
                if (!link.label_en) link.label_en = link.label_pt;
                if (!link.url) {
                    const linkUrlMsg = isPt 
                        ? `Link ${lIdx+1}: A URL do Link é obrigatória.` 
                        : `Link ${lIdx+1}: The Link URL is required.`;
                    alert(linkUrlMsg);
                    isValid = false;
                    break;
                }

                // Verifica ID de link duplicado no próprio menu / Check duplicate link ID in the menu itself
                let idCount = 0;
                menu.links.forEach(l2 => {
                    if (l2.id === link.id) idCount++;
                });
                if (idCount > 1) {
                    const linkIdDupMsg = isPt 
                        ? `O Identificador do Link "${link.id}" está duplicado no formulário atual. Deve ser único.` 
                        : `The Link Identifier "${link.id}" is duplicated in the current form. It must be unique.`;
                    alert(linkIdDupMsg);
                    isValid = false;
                    break;
                }

                // Verifica ID de link duplicado em todos os outros menus em config.menus / Check duplicate link ID in all other menus in config.menus
                let isDupInConfig = false;
                for (let mIdx2 = 0; mIdx2 < config.menus.length; mIdx2++) {
                    if (mIdx2 === skipConfigIndex) continue;
                    const m2 = config.menus[mIdx2];
                    if (m2.links) {
                        for (let lIdx2 = 0; lIdx2 < m2.links.length; lIdx2++) {
                            if (m2.links[lIdx2].id === link.id) {
                                isDupInConfig = true;
                                break;
                            }
                        }
                    }
                    if (isDupInConfig) break;
                }

                if (isDupInConfig) {
                    const linkIdDupConfigMsg = isPt 
                        ? `O Identificador do Link "${link.id}" já está em uso em outro menu. Escolha um identificador único.` 
                        : `The Link Identifier "${link.id}" is already in use in another menu. Choose a unique identifier.`;
                    alert(linkIdDupConfigMsg);
                    isValid = false;
                    break;
                }

                // Valida nomes de parâmetros / Validate parameter names
                if (link.url_params) {
                    for (let pIdx = 0; pIdx < link.url_params.length; pIdx++) {
                        const p = link.url_params[pIdx];
                        if (!p.param_name) {
                            const paramNameMsg = isPt 
                                ? `Link ${lIdx+1}, Parâmetro ${pIdx+1}: O nome da variável é obrigatório.` 
                                : `Link ${lIdx+1}, Parameter ${pIdx+1}: The variable name is required.`;
                            alert(paramNameMsg);
                            isValid = false;
                            break;
                        }
                        if (p.source === 'custom' && (p.value === undefined || p.value === '')) {
                            const paramValMsg = isPt 
                                ? `Link ${lIdx+1}, Parâmetro ${pIdx+1}: O valor para o parâmetro fixo é obrigatório.` 
                                : `Link ${lIdx+1}, Parameter ${pIdx+1}: The value for the fixed parameter is required.`;
                            alert(paramValMsg);
                            isValid = false;
                            break;
                        }
                    }
                    if (!isValid) break;
                }
            }
        }

        return isValid;
    }

    function isActiveMenuModified() {
        if (activeMenuIndex === -1) {
            if (activeMenu.type === 'new') {
                return (activeMenu.label_pt !== '' || activeMenu.label_en !== '' || activeMenu.links.length > 0);
            } else {
                return (activeMenu.target_menu_pt || activeMenu.target_menu_en || activeMenu.links.length > 0);
            }
        } else {
            const original = config.menus[activeMenuIndex];
            const cleanActive = JSON.parse(JSON.stringify(activeMenu));
            const cleanOriginal = JSON.parse(JSON.stringify(original));
            
            const removeKeys = (m) => {
                if (m.links) {
                    m.links.forEach(l => delete l._openAdvanced);
                }
            };
            removeKeys(cleanActive);
            removeKeys(cleanOriginal);

            return JSON.stringify(cleanActive) !== JSON.stringify(cleanOriginal);
        }
    }

    // Salva o menu ativo na lista local / Saves the active menu to the local list
    function saveActiveMenuToList() {
        if (!validateActiveMenu(activeMenu, activeMenuIndex)) {
            return false;
        }

        const deepClone = JSON.parse(JSON.stringify(activeMenu));
        if (deepClone.links) {
            deepClone.links.forEach(l => delete l._openAdvanced);
        }

        if (activeMenuIndex === -1) {
            config.menus.push(deepClone);
        } else {
            // Atualiza referências em menus do tipo existing se o nome do menu base (new) foi alterado
            // Updates references in existing menus if the base menu (new) name was changed
            const oldMenu = config.menus[activeMenuIndex];
            if (oldMenu.type === 'new' && deepClone.type === 'new') {
                const oldPt = (oldMenu.label_pt || '').trim().toLowerCase();
                const oldEn = (oldMenu.label_en || '').trim().toLowerCase();
                const newPt = (deepClone.label_pt || '').trim();
                const newEn = (deepClone.label_en || '').toLowerCase();

                if (oldPt !== newPt.toLowerCase() || oldEn !== newEn.toLowerCase()) {
                    config.menus.forEach(function(m) {
                        if (m.type === 'existing') {
                            const targetPt = (m.target_menu_pt || '').trim().toLowerCase();
                            const targetEn = (m.target_menu_en || '').trim().toLowerCase();

                            if ((oldPt && targetPt === oldPt) || (oldEn && targetEn === oldEn)) {
                                m.target_menu_pt = newPt;
                                m.target_menu_en = deepClone.label_en || newPt;
                            }
                        }
                    });
                }
            }

            config.menus[activeMenuIndex] = deepClone;
        }

        activeMenuIndex = -1;
        activeMenu = getBlankMenu();
        renderUI();
        return true;
    }

    function cancelMenuEdit() {
        activeMenuIndex = -1;
        activeMenu = getBlankMenu();
        renderUI();
    }

    // Salvar configuração via Ajax / Ajax Save Configuration
    function saveConfiguration() {
        if (isActiveMenuModified()) {
            if (!saveActiveMenuToList()) {
                return; // Stop saving if the modified active form has validation errors
            }
        }

        if (!$('#allowed_user_group').val()) {
            const selectGroupMsg = isPt 
                ? 'Por favor, selecione o grupo de usuários permitido.' 
                : 'Please select the allowed user group.';
            alert(selectGroupMsg);
            return;
        }

        let allValid = true;
        for (let i = 0; i < config.menus.length; i++) {
            if (!validateActiveMenu(config.menus[i], i)) {
                allValid = false;
                break;
            }
        }
        if (!allValid) return;

        const cleanConfig = {
            allowed_user_group: $('#allowed_user_group').val(),
            default_icon: $('#default_icon').val() || 'zi-monitoring',
            menus: config.menus.map(function(m) {
                const cleanedMenu = JSON.parse(JSON.stringify(m));
                if (cleanedMenu.links) {
                    cleanedMenu.links = cleanedMenu.links.map(function(l) {
                        delete l._openAdvanced;
                        return l;
                    });
                }
                return cleanedMenu;
            })
        };

        const saveBtn = $('.massz-btn-save-action');
        const savingText = isPt ? 'Salvando...' : 'Saving...';
        saveBtn.attr('disabled', 'disabled').text(savingText);

        $.ajax({
            url: window.masszUrls.save,
            method: 'POST',
            dataType: 'json',
            data: { config: JSON.stringify(cleanConfig) },
            success: function(r) {
                saveBtn.removeAttr('disabled').html('<span class="zi-check"></span> ' + (window.masszLabels.saveGeneralSettings || 'Salvar'));
                if (r.success) {
                    if (typeof MasszUtils !== 'undefined') {
                        MasszUtils.showNotification(r.message, 'success');
                    } else {
                        alert(r.message);
                    }
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    const errMsg = r.error || (isPt ? 'Erro desconhecido' : 'Unknown error');
                    if (typeof MasszUtils !== 'undefined') {
                        MasszUtils.showNotification(errMsg, 'error');
                    } else {
                        alert(errMsg);
                    }
                }
            },
            error: function(x, s, e) {
                saveBtn.removeAttr('disabled').html('<span class="zi-check"></span> ' + (window.masszLabels.saveGeneralSettings || 'Salvar'));
                const errMsg = (isPt ? 'Erro de conexão: ' : 'Connection error: ') + e;
                if (typeof MasszUtils !== 'undefined') {
                    MasszUtils.showNotification(errMsg, 'error');
                } else {
                    alert(errMsg);
                }
            }
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(String(text)));
        return d.innerHTML;
    }

    // Expõe as funções globalmente para chamadas HTML / Expose functions globally for HTML calls
    window.startNewMenu = startNewMenu;
    window.editMenuInForm = editMenuInForm;
    window.deleteMenuInTable = deleteMenuInTable;
    window.moveMenuInTable = moveMenuInTable;
    window.changeActiveMenuType = changeActiveMenuType;
    window.updateActiveMenuField = updateActiveMenuField;
    window.updateActiveMenuTarget = updateActiveMenuTarget;
    window.updateActiveMenuAfter = updateActiveMenuAfter;
    window.addNewActiveLink = addNewActiveLink;
    window.removeActiveLink = removeActiveLink;
    window.updateActiveLinkField = updateActiveLinkField;
    window.toggleActiveAdvancedSettings = toggleActiveAdvancedSettings;
    window.moveActiveLink = moveActiveLink;
    window.addNewActiveParam = addNewActiveParam;
    window.removeActiveParam = removeActiveParam;
    window.updateActiveParamField = updateActiveParamField;
    window.updateActiveParamSource = updateActiveParamSource;
    window.updateActiveSandbox = updateActiveSandbox;
    window.applyActiveSecurityPreset = applyActiveSecurityPreset;
    window.openIconPicker = openIconPicker;
    window.closeIconPicker = closeIconPicker;
    window.switchIconTab = switchIconTab;
    window.filterIcons = filterIcons;
    window.saveActiveMenuToList = saveActiveMenuToList;
    window.cancelMenuEdit = cancelMenuEdit;
    window.saveConfiguration = saveConfiguration;
});
</script>
