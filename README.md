# MASSZ v2.0 - Módulo para Abrir Seus Sistemas no Zabbix
### (Module to Open Your Systems in Zabbix)

---

## 🇧🇷 Português

### 💡 Sobre o Projeto
O **MASSZ** é um módulo de extensão para o frontend do Zabbix, idealizado e desenvolvido por **Leandro Dethloff**. O projeto nasceu em 2024 e foi apresentado oficialmente na **Zabbix Conference LATAM 2024** como o **primeiro módulo para Zabbix entregue como software livre para a comunidade**.

* 📺 **Assista à apresentação no YouTube:** [Zabbix Conference LATAM 2024 - MASSZ](https://youtu.be/bIT3MIrQge8?si=xCf971FOG11YsHNH)
* 🙏 *Agradecemos a Deus e ao nosso Salvador Jesus Cristo por ter dado capacidade e conhecimento para desenvolver esse projeto.*

Com o **MASSZ v2.0**, os administradores do Zabbix podem integrar de forma 100% visual qualquer aplicação web externa (como Grafana, Netbox, GLPI, dashboards customizados, etc.) diretamente nos menus de navegação do Zabbix, sem precisar editar uma única linha de código.

---

### ✨ Novidades da Versão 2.0
* **Interface Gráfica de Gestão (MASSZ Management):** Um painel visual completo para criar, ordenar e gerenciar menus e links.
* **Auto-geração de manifest.json & Scan Automático:** Ao salvar as configurações, o módulo reconstrói o `manifest.json` e notifica o Zabbix programaticamente (`CModuleManager->scan()`), aplicando as alterações na hora.
* **Inserção Flexível de Menus:**
  * Criar novos menus principais no menu lateral com ícones selecionáveis.
  * Inserir links diretamente em submenus existentes do Zabbix (ex: abaixo de *Dashboard* no menu *Monitoramento*).
* **Interface Inteligente & Localizada (Smart & Localized UI):**
  * O formulário detecta o idioma ativo do Zabbix e solicita o Título apenas no idioma correspondente (Português para `pt_BR`, Inglês para outros), sincronizando os valores automaticamente debaixo dos panos.
* **Parâmetros Dinâmicos de Sessão:** Envie dados da sessão ativa do Zabbix (username, userid, sessionid, idioma, etc.) e o IP da sessão como parâmetros GET na URL da aplicação de destino.
* **Tipos de Abertura:**
  * **Iframe Seguro (Interno):** Abre a aplicação dentro do layout do Zabbix.
  * **Nova Aba:** Abre o link externo em um novo separador do navegador.
  * **Redirecionamento:** Redireciona a aba ativa para a URL.
* **Segurança Cross-Origin Avançada:** Controle total sobre os atributos de `sandbox` do iframe, diretivas de `Referrer-Policy` e cabeçalhos de `Permissions-Policy` por link individual, com presets rápidos de segurança (Básico, Restrito, Permissivo e Sem Restrições).
* **Icon Picker Integrado:** Seletor visual contendo os ícones nativos do Zabbix (`zi-*`), ícones legado e uma coleção exclusiva de **24 ícones customizados do MASSZ** em formato SVG dinâmico (que herdam a cor do tema ativo do Zabbix).

---

### 🚀 Instalação e Configuração

1. **Copiar os arquivos:**
   Baixe ou clone este repositório e extraia a pasta `MASSZ` no diretório de módulos do seu frontend do Zabbix:
   ```bash
   # Geralmente localizado em:
   /usr/share/zabbix/modules/massz
   # ou no diretório customizado configurado no seu Zabbix
   ```
2. **Definir permissões de escrita:**
   Certifique-se de que o usuário do servidor web (ex: `www-data` ou `apache`) possui permissão de leitura e escrita na pasta `conf/` e no arquivo `manifest.json`:
   ```bash
   chown -R www-data:www-data /usr/share/zabbix/modules/massz
   chmod -R 755 /usr/share/zabbix/modules/massz
   ```
3. **Ativar o módulo no Zabbix:**
   * Acesse o Zabbix como **Super Administrador**.
   * Vá em **Administração** ➔ **Geral** ➔ **Módulos**.
   * Clique em **Scan directory** (se o módulo não aparecer na lista) e ative o módulo **MASSZ**.
4. **Configurar os Acessos:**
   * Uma vez ativado, o menu **Gestão do MASSZ** aparecerá na seção **Administração** do Zabbix.
   * Selecione o **Grupo de Usuários Permitido** que terá acesso à visualização dos menus e links integrados (padrão: `Zabbix administrators`).

---

### 📋 Variáveis de Sessão Disponíveis
Você pode configurar os links para enviarem os seguintes dados da sessão atual do Zabbix como parâmetros GET:

| Chave (Source) | Descrição do Dado |
| :--- | :--- |
| `userid` | ID numérico do usuário logado |
| `username` | Nome de login do usuário |
| `name` | Primeiro nome do usuário |
| `surname` | Sobrenome do usuário |
| `type` | Tipo de perfil do usuário (1=User, 2=Admin, 3=SuperAdmin) |
| `sessionid` | ID da sessão ativa (token de autenticação do Zabbix) |
| `userip` | Endereço IP do cliente conectado |
| `lang` | Idioma ativo no perfil do usuário (ex: `pt_BR`, `en_US`) |
| `theme` | Tema de cores atual do Zabbix (ex: `blue-theme`, `dark-theme`) |
| `attempt_ip` | IP utilizado no último login do usuário |

Use `{HOST}` diretamente na URL (ex: `https://{HOST}/grafana`) para que o módulo o substitua automaticamente pelo domínio/host que está servindo o Zabbix no momento da requisição.

---

## 🇺🇸 English

### 💡 About the Project
**MASSZ** is a frontend extension module for Zabbix, conceived and developed by **Leandro Dethloff**. The project was born in 2024 and officially presented at **Zabbix Conference LATAM 2024** as the **first Zabbix module delivered as open-source software to the community**.

* 📺 **Watch the talk on YouTube:** [Zabbix Conference LATAM 2024 - MASSZ](https://youtu.be/bIT3MIrQge8?si=xCf971FOG11YsHNH)
* 🙏 *We thank God and our Savior Jesus Christ for providing the capability and knowledge to develop this project.*

With **MASSZ v2.0**, Zabbix administrators can integrate any external web application (such as Grafana, Netbox, GLPI, custom dashboards, etc.) 100% visually into Zabbix navigation menus, without writing a single line of code.

---

### ✨ What's New in Version 2.0
* **Visual Management Dashboard (MASSZ Management):** A complete visual panel to create, order, and manage menus and links.
* **Auto-generated manifest.json & Automatic Directory Scan:** When saving configurations, the module automatically rebuilds `manifest.json` and programmatically runs a directory scan (`CModuleManager->scan()`), applying changes immediately.
* **Flexible Menu Injections:**
  * Create new base menus in the sidebar with customizable icons.
  * Inject links directly into existing Zabbix submenus (e.g., underneath *Dashboards* in the *Monitoring* menu).
* **Smart & Localized UI:**
  * The form automatically detects Zabbix's active language, asking for the Title only in the user's language (Portuguese for `pt_BR`, English for others) and syncing values under the hood.
  * Convenient "Save" buttons placed next to active actions for faster publication.
  * High-contrast styling for the active editing row, keeping text and icons fully readable in both light and dark themes.
  * URL parameters and advanced security settings panel collapsed by default for new links, offering a cleaner, more focused setup.
* **Dynamic Session Parameters:** Send active Zabbix session details (username, userid, sessionid, language, etc.) and connection IP as GET parameters to the target URL.
* **Opening Modes:**
  * **Secure Iframe (Internal):** Opens the application inside the Zabbix template frame.
  * **New Tab:** Opens the external application in a new browser tab.
  * **Redirect:** Redirects the active page window to the URL.
* **Advanced Cross-Origin Security:** Full control over iframe `sandbox` attributes, `Referrer-Policy`, and `Permissions-Policy` headers per link, with friendly security presets (Basic, Restricted, Permissive, and No Restrictions).
* **Integrated Icon Picker:** Visual modal selector displaying native Zabbix icons (`zi-*`), legacy icons, and a custom collection of **24 MASSZ SVG icons** that automatically inherit the active Zabbix theme colors.

---

### 🚀 Installation & Setup

1. **Copying the Files:**
   Download or clone this repository and place the `MASSZ` folder into your Zabbix frontend modules directory:
   ```bash
   # Usually located at:
   /usr/share/zabbix/modules/massz
   # or your custom module folder configured in Zabbix
   ```
2. **Configure write permissions:**
   Ensure that your web server user (e.g. `www-data` or `apache`) has read and write permissions to the `conf/` directory and the `manifest.json` file:
   ```bash
   chown -R www-data:www-data /usr/share/zabbix/modules/massz
   chmod -R 755 /usr/share/zabbix/modules/massz
   ```
3. **Enable the module in Zabbix:**
   * Log into Zabbix as a **Super Administrator**.
   * Navigate to **Administration** ➔ **General** ➔ **Modules**.
   * Click **Scan directory** (if the module is not listed) and enable **MASSZ**.
4. **Access Management:**
   * Once enabled, the **MASSZ Management** link will appear under Zabbix's **Administration** menu.
   * Choose the **Allowed User Group** authorized to view the custom menus and links (default: `Zabbix administrators`).

---

### 📋 Available Session Variables
You can configure links to send the following active Zabbix session details as GET variables:

| Source Key | Data Description |
| :--- | :--- |
| `userid` | Numeric ID of the logged-in user |
| `username` | User login name |
| `name` | User first name |
| `surname` | User last name |
| `type` | User role profile type (1=User, 2=Admin, 3=SuperAdmin) |
| `sessionid` | Active session token (Zabbix API auth token) |
| `userip` | Connected client's IP address |
| `lang` | Active language profile (e.g., `en_US`, `pt_BR`) |
| `theme` | Current active Zabbix color theme (e.g., `blue-theme`, `dark-theme`) |
| `attempt_ip` | IP address used during the last login |

Use `{HOST}` inside the URL (e.g., `https://{HOST}/grafana`) to automatically substitute it with the host/domain currently serving the Zabbix frontend.

---

### 📁 File Structure / Estrutura de Arquivos
```
MASSZ/
├── Module.php                              # Main Zabbix Module wrapper
├── manifest.json                           # Zabbix manifest configuration (Auto-generated)
├── README.md                               # This documentation (PT/EN)
├── .gitignore                              # Git exclusion rules
├── conf/
│   └── config.json                         # Configuration store (Dynamically updated via UI)
├── helpers/
│   └── MasszTranslator.php                 # Bilingual translator utility
├── actions/
│   ├── CControllerMasszManage.php          # Management view action controller
│   ├── CControllerMasszSave.php            # AJAX save action controller
│   └── CControllerMasszIframe.php          # Generic link resolver & secure iframe controller
├── views/
│   ├── massz.manage.php                    # Management page dashboard layout
│   ├── massz.iframe.php                    # Iframe container view
│   └── js/
│       └── massz.manage.js.php             # Core UI state builder script
└── assets/
    ├── css/
    │   ├── massz.css                       # Light theme styles & custom SVG icon mask library
    │   └── massz-dark.css                  # Dark theme color overrides
    └── js/
        └── massz.utils.js                  # Shared toast notification utilities
```

### 🤝 Contribution / Contribuição
Sinta-se livre para enviar Pull Requests ou abrir Issues.
Feel free to open issues or submit Pull Requests for bug fixes or new features.

---

