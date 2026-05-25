# MASSZ v2.0 - Especificação Técnica do Módulo / Technical Specification

[Português] | [English](#english-technical-specification)

---

## Especificação Técnica (Português)

Esta documentação foi elaborada para desenvolvedores e arquitetos que necessitam dar manutenção, propor melhorias ou auditar o funcionamento técnico do módulo MASSZ v2.0 no Zabbix.

### 1. Visão Geral da Arquitetura

O MASSZ v2.0 opera como um módulo nativo do Zabbix, estendendo a classe base `CModule`. O módulo utiliza um modelo híbrido onde as configurações são persistidas em um arquivo JSON e as rotas/menus são injetados dinamicamente no Zabbix a cada ciclo de carregamento (bootstrap).

```
+-------------------------------------------------------------+
|                     Zabbix Core Bootstrap                   |
+-------------------------------------------------------------+
                               |
                               v
                     +-------------------+
                     |    Module::init() |
                     +-------------------+
                               |
            +------------------+------------------+
            |                                     |
            v                                     v
   +------------------+                  +-------------------+
   |  Passo 1: Novas  |                  | Passo 2: Inserir  |
   |  Bases de Menu   |                  | em Menus Exist.   |
   |  (findOrAdd)     |                  | (insertAfter/add) |
   +------------------+                  +-------------------+
            |                                     |
            +------------------+------------------+
                               |
                               v
                 +---------------------------+
                 | Carrega Zabbix Sidebar UI |
                 +---------------------------+
```

---

### 2. Ciclo de Vida e Fluxo de Execução

#### 2.1 Fase de Inicialização (Bootstrapping)
Durante o carregamento do framework do Zabbix, o método `init()` da classe `Modules\MASSZ\Module` é acionado se o usuário estiver logado (`CWebUser::isLoggedIn()`). 

1. **Configuração Geral**: O módulo lê `conf/config.json` para carregar as diretivas.
2. **Autorização**: Valida se o grupo de usuários do usuário atual bate com o parâmetro `allowed_user_group`.
3. **Injeção de Menus (Passo 1 - Criar)**: Cria novas instâncias de `CMenuItem` e as injeta no contêiner `menu.main` usando `findOrAdd()`.
4. **Injeção de Submenus/Links (Passo 2 - Inserir)**: Para itens com tipo `existing`, localiza o contêiner alvo via `$main_menu->find()`. Se um item de ancoragem estiver definido em `insert_after`, utiliza `$submenu->insertAfter()`; caso contrário, insere ao final usando `$submenu->add()`.

#### 2.2 Gerenciamento Dinâmico de Rotas (manifest.json)
Zabbix exige que todas as rotas (actions) sejam pré-declaradas no `manifest.json`. O MASSZ resolve essa restrição reescrevendo o `manifest.json` dinamicamente no backend toda vez que a configuração é salva.
1. O controlador `CControllerMasszSave` decodifica a nova configuração.
2. Varre todos os links definidos em todos os menus.
3. Para cada link, gera uma entrada no array de `actions` com o nome `massz.link.<id_do_link>`, apontando para o controlador genérico `CControllerMasszIframe` e a view `massz.iframe`.
4. Salva o `manifest.json` de volta no disco.

---

### 3. Esquema do Banco de Dados JSON (conf/config.json)

As configurações são persistidas em formato JSON estruturado no arquivo `conf/config.json`. Abaixo está a especificação técnica do esquema:

```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "type": "OBJECT",
  "properties": {
    "allowed_user_group": { "type": "STRING", "description": "Nome do grupo do Zabbix permitido." },
    "default_icon": { "type": "STRING", "description": "Ícone padrão para novos menus." },
    "menus": {
      "type": "ARRAY",
      "items": {
        "type": "OBJECT",
        "properties": {
          "id": { "type": "STRING", "description": "ID exclusivo gerado em JS." },
          "type": { "type": "STRING", "enum": ["new", "existing"] },
          "label_pt": { "type": "STRING" },
          "label_en": { "type": "STRING" },
          "icon": { "type": "STRING" },
          "target_menu_pt": { "type": "STRING", "description": "Menu pai em PT se type = existing." },
          "target_menu_en": { "type": "STRING", "description": "Menu pai em EN se type = existing." },
          "insert_after_pt": { "type": "STRING", "description": "Ancoragem do submenu em PT." },
          "insert_after_en": { "type": "STRING", "description": "Ancoragem do submenu em EN." },
          "links": {
            "type": "ARRAY",
            "items": {
              "type": "OBJECT",
              "properties": {
                "id": { "type": "STRING", "description": "ID alfanumérico do link (chave de action)." },
                "label_pt": { "type": "STRING" },
                "label_en": { "type": "STRING" },
                "url": { "type": "STRING", "description": "URL de destino com macros permitidas." },
                "link_type": { "type": "STRING", "enum": ["iframe", "newtab", "redirect"] },
                "sandbox": { "type": "STRING", "description": "Tokens de sandbox do iframe." },
                "allow_permissions": { "type": "STRING", "description": "Diretiva Permissions-Policy." },
                "referrer_policy": { "type": "STRING", "description": "Diretiva Referrer-Policy." },
                "url_params": {
                  "type": "ARRAY",
                  "items": {
                    "type": "OBJECT",
                    "properties": {
                      "param_name": { "type": "STRING", "description": "Variável GET na URL." },
                      "source": { "type": "STRING", "description": "Chave em CWebUser::$data ou 'custom'." },
                      "value": { "type": "STRING", "description": "Preenchido apenas se source = custom." }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }
}
```

---

### 4. Controladores e Endpoints

#### 4.1 CControllerMasszManage
- **Namespace**: `Modules\MASSZ\Actions`
- **Função**: Prepara a página de administração.
- **Mapeamento Técnico**:
  - Executa `APP::Component()->get('menu.main')` em tempo de execução para inspecionar a barra lateral nativa do Zabbix, extraindo rótulos e submenus para injetar como dados brutos no array Javascript global (`window.masszMenuTree`).
  - Restringe o acesso apenas para `USER_TYPE_SUPER_ADMIN`.

#### 4.2 CControllerMasszSave
- **Namespace**: `Modules\MASSZ\Actions`
- **Função**: Executa validação de escrita, persiste `config.json` e reconstrói o `manifest.json`.
- **Validação de Entrada**: Recebe parâmetro POST `config` contendo string JSON.
- **Ciclo de Escrita**:
  1. Escreve em `conf/config.json`.
  2. Regenera as chaves `actions` do `manifest.json`.
  3. Aciona o recarregamento do módulo enviando sinalizadores de sucesso de volta por Ajax.

#### 4.3 CControllerMasszIframe
- **Namespace**: `Modules\MASSZ\Actions`
- **Função**: Resolver parâmetros e renderizar o contêiner do iframe.
- **Roteamento Dinâmico**: É invocado por todas as ações do tipo `massz.link.<id>`. O método `getAction()` captura o link ID correspondente.
- **Processamento de Cabeçalhos e Sandbox**: Extrai as propriedades de sandbox de segurança e as injeta no template da View do Iframe (`views/massz.iframe.php`).

---

### 5. Resolução de Parâmetros e Macros

O módulo executa uma substituição em tempo de execução das strings de URL através do método `resolveUrl` em `Module.php` e `CControllerMasszIframe.php`:

1. **Substituição do {HOST}**:
   ```php
   $url = str_replace('{HOST}', $_SERVER['HTTP_HOST'] ?? 'localhost', $url);
   ```
2. **Injeção de Sessão**:
   O array de parâmetros do link (`url_params`) é processado. Se `source` for diferente de `'custom'`, o valor é extraído de `CWebUser::$data[$source]`, que mapeia dados de sessão do banco de dados do Zabbix, tais como:
   - `sessionid`: Token md5 de autenticação ativa da API/Interface.
   - `userid`: ID numérico sequencial da tabela de usuários.
   - `theme`: Tema ativo no perfil do usuário (`blue-theme`, `dark-theme`, etc).

Os parâmetros resolvidos são serializados via `http_build_query()` e concatenados à URL.

---

### 6. Presets de Segurança

Os presets de segurança facilitam a configuração do sandbox do iframe mapeando regras estáticas:

| Preset | Sandbox Tokens | Permissions-Policy | Referrer-Policy |
| :--- | :--- | :--- | :--- |
| **Básico (Basic)** | `allow-scripts allow-same-origin allow-forms` | `fullscreen` | `no-referrer` |
| **Restrito (Restricted)** | `allow-same-origin` | *(Vazio)* | `strict-origin` |
| **Permissivo (Permissive)**| `allow-scripts allow-same-origin allow-forms allow-popups allow-downloads` | `fullscreen` | `no-referrer` |
| **Sem restrições (None)** | *(Vazio / Sem sandbox)* | *(Vazio)* | `no-referrer` |

---

### 7. Guia de Extensão de Código

#### Adicionando Novos Ícones Customizados
Para estender os ícones disponíveis no seletor do gerenciador:
1. Abra o arquivo [massz.manage.js.php] e adicione o nome da classe do ícone (ex: `'massz-icon-novofavorito'`) na constante `CUSTOM_ICONS`.
2. Abra o arquivo [massz.css] e mapeie o SVG correspondente à máscara do ícone:
   ```css
   .massz-icon-novofavorito::before, .massz-icon.massz-icon-novofavorito {
       mask-image: url('data:image/svg+xml;utf8,<svg ...></svg>');
       -webkit-mask-image: url('data:image/svg+xml;utf8,<svg ...></svg>');
   }
   ```

---
---

## English Technical Specification

This documentation is tailored for developers and system architects aiming to maintain, extend, or audit the inner technical workings of the MASSZ v2.0 module in Zabbix.

### 1. Architectural Overview

MASSZ v2.0 operates as a native Zabbix module, extending the base `CModule` class. It utilizes a hybrid model where settings are persisted in a JSON file and routes/sidebar items are dynamically injected into Zabbix on every HTTP request cycle (bootstrap).

```
+-------------------------------------------------------------+
|                     Zabbix Core Bootstrap                   |
+-------------------------------------------------------------+
                               |
                               v
                     +-------------------+
                     |    Module::init() |
                     +-------------------+
                               |
            +------------------+------------------+
            |                                     |
            v                                     v
   +------------------+                  +-------------------+
   |  Pass 1: New     |                  | Pass 2: Insert    |
   |  Base Menus      |                  | into Exist. Menus |
   |  (findOrAdd)     |                  | (insertAfter/add) |
   +------------------+                  +-------------------+
            |                                     |
            +------------------+------------------+
                               |
                               v
                 +---------------------------+
                 | Load Zabbix Sidebar UI    |
                 +---------------------------+
```

---

### 2. Lifecycle and Execution Flow

#### 2.1 Initialization Phase (Bootstrapping)
During the Zabbix framework boot cycle, the `init()` method in `Modules\MASSZ\Module` is triggered if the user is authenticated (`CWebUser::isLoggedIn()`).

1. **Config Loading**: The module reads configurations from `conf/config.json`.
2. **Authorization**: Validates if the active user belongs to the group defined under `allowed_user_group`.
3. **Menu Injection (Pass 1 - Create)**: Instantiates `CMenuItem` objects and injects them into the Zabbix `menu.main` component using `findOrAdd()`.
4. **Submenu/Link Injection (Pass 2 - Insert)**: For items with type `existing`, it locates the target menu container using `$main_menu->find()`. If a submenu item is set under `insert_after`, it inserts the link using `$submenu->insertAfter()`; otherwise, it appends it to the end using `$submenu->add()`.

#### 2.2 Dynamic Route Management (manifest.json)
Zabbix requires all action routes to be pre-registered inside `manifest.json`. MASSZ bypasses this limitation by dynamically rewriting `manifest.json` in the backend whenever changes are saved.
1. The `CControllerMasszSave` controller decodes the configuration POST request.
2. It loops through all links declared under every menu.
3. For each link, it appends a dynamic action named `massz.link.<link_id>` mapping to `CControllerMasszIframe` controller and `massz.iframe` view.
4. Saves `manifest.json` back to disk.

---

### 3. JSON Database Schema (conf/config.json)

Settings are persisted in a structured JSON file at `conf/config.json`. Below is the schema specification:

```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "type": "OBJECT",
  "properties": {
    "allowed_user_group": { "type": "STRING", "description": "Allowed Zabbix group name." },
    "default_icon": { "type": "STRING", "description": "Default icon for new menus." },
    "menus": {
      "type": "ARRAY",
      "items": {
        "type": "OBJECT",
        "properties": {
          "id": { "type": "STRING", "description": "JS-generated unique ID." },
          "type": { "type": "STRING", "enum": ["new", "existing"] },
          "label_pt": { "type": "STRING" },
          "label_en": { "type": "STRING" },
          "icon": { "type": "STRING" },
          "target_menu_pt": { "type": "STRING", "description": "Parent menu in PT if type = existing." },
          "target_menu_en": { "type": "STRING", "description": "Parent menu in EN if type = existing." },
          "insert_after_pt": { "type": "STRING", "description": "Anchor submenu label in PT." },
          "insert_after_en": { "type": "STRING", "description": "Anchor submenu label in EN." },
          "links": {
            "type": "ARRAY",
            "items": {
              "type": "OBJECT",
              "properties": {
                "id": { "type": "STRING", "description": "Alphanumeric link ID (action key)." },
                "label_pt": { "type": "STRING" },
                "label_en": { "type": "STRING" },
                "url": { "type": "STRING", "description": "Target URL with macro placeholders." },
                "link_type": { "type": "STRING", "enum": ["iframe", "newtab", "redirect"] },
                "sandbox": { "type": "STRING", "description": "Iframe sandbox tokens." },
                "allow_permissions": { "type": "STRING", "description": "Permissions-Policy directive." },
                "referrer_policy": { "type": "STRING", "description": "Referrer-Policy directive." },
                "url_params": {
                  "type": "ARRAY",
                  "items": {
                    "type": "OBJECT",
                    "properties": {
                      "param_name": { "type": "STRING", "description": "URL query GET parameter." },
                      "source": { "type": "STRING", "description": "Key in CWebUser::$data or 'custom'." },
                      "value": { "type": "STRING", "description": "Only used if source = custom." }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }
}
```

---

### 4. Controllers and Endpoints

#### 4.1 CControllerMasszManage
- **Namespace**: `Modules\MASSZ\Actions`
- **Role**: Prepares and loads the configuration admin UI view.
- **Technical Inner Workings**:
  - Evaluates `APP::Component()->get('menu.main')` at runtime to inspect the active Zabbix sidebar menu tree, extracting labels and submenus to pass to the JS frontend as `window.masszMenuTree`.
  - Rejects access for non-`USER_TYPE_SUPER_ADMIN` accounts.

#### 4.2 CControllerMasszSave
- **Namespace**: `Modules\MASSZ\Actions`
- **Role**: Validates input data, writes `config.json` and updates `manifest.json`.
- **Validation**: Accepts a JSON string inside the POST parameter `config`.
- **Execution Flow**:
  1. Writes to `conf/config.json`.
  2. Rebuilds `actions` list inside `manifest.json`.
  3. Returns a success indicator to trigger AJAX UI refresh.

#### 4.3 CControllerMasszIframe
- **Namespace**: `Modules\MASSZ\Actions`
- **Role**: Resolves dynamic URL macros and parameters, loading the iframe container view.
- **Dynamic Routing**: Injected as a handler for all `massz.link.<id>` actions. The method `getAction()` extracts the specific link ID.
- **Iframe Processing**: Resolves sandbox, referrer-policy, and allow headers, rendering the result into `views/massz.iframe.php`.

---

### 5. URL Query & Macro Resolution

The module performs runtime URL replacements inside `Module.php` and `CControllerMasszIframe.php` via `resolveUrl()`:

1. **{HOST} Macro Replacement**:
   ```php
   $url = str_replace('{HOST}', $_SERVER['HTTP_HOST'] ?? 'localhost', $url);
   ```
2. **Session Parameter Injection**:
   The `url_params` list is processed. If a parameter source is not `'custom'`, the value is dynamically retrieved from `CWebUser::$data[$source]`, allowing variables such as:
   - `sessionid`: User's active MD5 auth token.
   - `userid`: Sequential ID from users database table.
   - `theme`: Active theme inside user profile (`blue-theme`, `dark-theme`, etc).

The resolved variables are serialised via `http_build_query()` and appended to the URL string.

---

### 6. Security Presets Mappings

Presets simplify iframe sandbox options mapping by defining static rules:

| Preset | Sandbox Tokens | Permissions-Policy | Referrer-Policy |
| :--- | :--- | :--- | :--- |
| **Basic** | `allow-scripts allow-same-origin allow-forms` | `fullscreen` | `no-referrer` |
| **Restricted** | `allow-same-origin` | *(Empty)* | `strict-origin` |
| **Permissive**| `allow-scripts allow-same-origin allow-forms allow-popups allow-downloads` | `fullscreen` | `no-referrer` |
| **None** | *(Empty / No sandbox)* | *(Empty)* | `no-referrer` |

---

### 7. Code Extension Guide

#### Registering New Custom Icons
To extend available icons inside the manager icon picker:
1. Edit [massz.manage.js.php] and append the icon class name (e.g. `'massz-icon-newfavorite'`) to the `CUSTOM_ICONS` constant.
2. Edit [massz.css] to define the SVG mask rule:
   ```css
   .massz-icon-newfavorite::before, .massz-icon.massz-icon-newfavorite {
       mask-image: url('data:image/svg+xml;utf8,<svg ...></svg>');
       -webkit-mask-image: url('data:image/svg+xml;utf8,<svg ...></svg>');
   }
   ```
