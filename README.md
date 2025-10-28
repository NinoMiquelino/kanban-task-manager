## 👨‍💻 Autor

<div align="center">
  <img src="https://avatars.githubusercontent.com/ninomiquelino" width="100" height="100" style="border-radius: 50%">
  <br>
  <strong>Onivaldo Miquelino</strong>
  <br>
  <a href="https://github.com/ninomiquelino">@ninomiquelino</a>
</div>

---

# 📋 Kanban Task Manager

[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6%2B-F7DF1E?logo=javascript&logoColor=black)](https://developer.mozilla.org/javascript)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-38B2AC?logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
![License MIT](https://img.shields.io/badge/License-MIT-green)
![Status Stable](https://img.shields.io/badge/Status-Stable-success)
![Version 1.0.0](https://img.shields.io/badge/Version-1.0.0-blue)
![GitHub stars](https://img.shields.io/github/stars/NinoMiquelino/kanban-task-manager?style=social)
![GitHub forks](https://img.shields.io/github/forks/NinoMiquelino/kanban-task-manager?style=social)
![GitHub issues](https://img.shields.io/github/issues/NinoMiquelino/kanban-task-manager)

> Sistema completo de gestão de tarefas estilo Kanban com interface drag & drop intuitiva, desenvolvido com PHP e JavaScript.

## 🚀 Funcionalidades Principais

### 🎯 Gestão Visual de Tarefas
- **📋 Boards Multiplos** - Crie diferentes boards para projetos
- **🎯 Colunas Customizáveis** - To Do, Doing, Done e muito mais
- **✨ Drag & Drop Nativo** - Arraste tarefas entre colunas
- **📱 Interface Responsiva** - Funciona perfeitamente em mobile e desktop

### 📊 Organização Inteligente
- **⏰ Sistema de Prioridades** - Baixa, Média e Alta com cores visuais
- **📅 Datas de Vencimento** - Controle prazos e deadlines
- **🏷️ Descrições Detalhadas** - Adicione informações completas às tarefas
- **🔍 Visualização Clara** - Interface limpa e intuitiva

### 🔐 Sistema Completo
- **👤 Autenticação de Usuários** - Registro e login seguro
- **🔒 Dados Privados** - Cada usuário vê apenas seus boards
- **💾 Persistência Imediata** - Alterações salvas automaticamente
- **🔄 Tempo Real** - Atualizações instantâneas com JavaScript

### 🛠️ Funcionalidades Técnicas
- **🎨 Design Moderno** - Tailwind CSS com componentes customizados
- **⚡ Performance Otimizada** - Carregamento rápido e suave
- **📈 Escalável** - Arquitetura preparada para crescimento
- **🔧 Fácil Manutenção** - Código organizado e documentado

## 🛠️ Stack Tecnológica

### Backend
- **PHP 8.0+** - Linguagem server-side robusta
- **MySQL** - Banco de dados relacional
- **PDO** - Conexão segura com banco de dados
- **Sessions** - Gerenciamento de autenticação

### Frontend
- **JavaScript ES6+** - Lógica de aplicação moderna
- **HTML5 Drag & Drop API** - Funcionalidade nativa de arrastar/soltar
- **Tailwind CSS 3.x** - Framework CSS utility-first
- **Font Awesome 6** - Ícones e elementos visuais

### Arquitetura
- **MVC Pattern** - Organização clara do código
- **RESTful APIs** - Endpoints bem estruturados
- **Responsive Design** - Mobile-first approach
- **Progressive Enhancement** - Funcionalidade básica sempre disponível

## 📁 Estrutura do Projeto

```

kanban-task-manager/
├──📄 index.php                 # Página inicial - Lista de boards
├──🎯 board.php                 # Board específico com Kanban
├──🔐 login.php                 # Autenticação de usuários
├──📝 register.php              # Registro de novos usuários
├──🔌 api/
│├── tasks.php               # CRUD completo de tarefas
│├── boards.php              # Gerenciamento de boards
│└── columns.php             # Gestão de colunas
├──📦 includes/
│├── config.php              # Configuração e conexão DB
│├── auth.php                # Sistema de autenticação
│├── functions.php           # Funções auxiliares
│└── header.php              # Cabeçalho comum
├──🎨 css/
│└── style.css               # Estilos customizados
├──⚡ js/
│├── app.js                  # Aplicação principal
│├── drag-drop.js            # Lógica de drag & drop
│├── api.js                  # Comunicação com APIs
│└── modal.js                # Gerenciamento de modais
├──🗃️ data/
│└── database.sql            # Estrutura do banco de dados

```

## ⚡ Instalação Rápida

### Pré-requisitos
- PHP 8.0 ou superior
- MySQL 8.0 ou superior
- Servidor web (Apache/Nginx) ou PHP built-in server
- Navegador web moderno

### 🚀 Implementação em 5 Minutos

1. **Clone o repositório**
   ```bash
   git clone https://github.com/NinoMiquelino/kanban-task-manager.git
   cd kanban-task-manager
```

1. Configure o banco de dados
   ```bash
   mysql -u root -p < data/database.sql
   ```
2. Ajuste as configurações (se necessário)
   ```php
   // includes/config.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'kanban_manager');
   define('DB_USER', 'seu_usuario');
   define('DB_PASS', 'sua_senha');
   ```
3. Inicie o servidor
   ```bash
   # PHP built-in server
   php -S localhost:8000
   
   # Ou configure em seu servidor web preferido
   ```
4. Acesse e use!
   ```
   http://localhost:8000
   ```

🐳 Docker (Opcional)

```yaml
# docker-compose.yml
version: '3.8'
services:
  web:
    image: php:8.1-apache
    ports:
      - "8000:80"
    volumes:
      - .:/var/www/html
  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: kanban_manager
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

🎯 Como Usar

Para Novos Usuários

1. Cadastre-se na plataforma
2. Crie seu primeiro board clicando em "Novo Board"
3. Adicione tarefas usando o botão "Nova Tarefa"
4. Organize com drag & drop entre as colunas

Funcionalidades Principais

🎯 Gestão de Boards

· Criar Board: Clique em "Novo Board" na página inicial
· Editar Board: Acesse as configurações do board
· Excluir Board: Disponível no menu de opções

📋 Gestão de Tarefas

· Criar Tarefa: Clique em "Adicionar Tarefa" em qualquer coluna
· Mover Tarefa: Arraste e solte entre colunas
· Editar Tarefa: Clique no ícone de edição
· Excluir Tarefa: Clique no ícone de lixeira

🎨 Personalização

· Prioridades: Selecione entre Baixa, Média e Alta
· Cores: Cada prioridade tem cor distinta
· Datas: Defina prazos de vencimento
· Descrições: Adicione detalhes às tarefas

🗃️ Estrutura do Banco de Dados

Tabelas Principais

```sql
-- Usuários do sistema
users (id, email, password, name, created_at)

-- Boards de cada usuário
boards (id, name, description, user_id, created_at)

-- Colunas de cada board
columns (id, name, board_id, position, created_at)

-- Tarefas do sistema
tasks (id, title, description, column_id, position, priority, due_date, created_at, updated_at)
```

Relacionamentos

· 1:N Usuário → Boards
· 1:N Board → Colunas
· 1:N Coluna → Tarefas
· Cascata Exclusão em cascata para manter integridade

🔧 Desenvolvimento

Estrutura de APIs

```javascript
// Todas as APIs retornam JSON
GET    /api/tasks.php          # Listar tarefas
POST   /api/tasks.php          # Criar tarefa  
PUT    /api/tasks.php          # Atualizar tarefa
DELETE /api/tasks.php          # Excluir tarefa
```

Exemplo de Uso da API

```javascript
// Criar uma nova tarefa
const response = await fetch('/api/tasks.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        title: 'Nova tarefa',
        description: 'Descrição da tarefa',
        column_id: 1,
        priority: 'medium',
        due_date: '2024-12-31'
    })
});
```

Personalização

Modificar Cores das Prioridades

```css
/* css/style.css */
.priority-high {
    border-left-color: #seu_vermelho;
}

.priority-medium {
    border-left-color: #seu_amarelo;
}

.priority-low {
    border-left-color: #seu_verde;
}
```

Adicionar Novas Colunas

```sql
-- Adicionar nova coluna a um board
INSERT INTO columns (name, board_id, position) 
VALUES ('Review', 1, 2);
```

🐛 Troubleshooting

Problemas Comuns e Soluções

Erro de conexão com o banco

```php
// Verifique includes/config.php
define('DB_HOST', 'localhost');        // Seu host MySQL
define('DB_NAME', 'kanban_manager');   // Nome do banco
define('DB_USER', 'root');             // Seu usuário MySQL  
define('DB_PASS', 'password');         // Sua senha MySQL
```

Drag & Drop não funciona

· Verifique se o JavaScript está habilitado
· Confirme o console do navegador para erros
· Teste em diferentes navegadores

Tarefas não são salvas

· Verifique permissões de escrita
· Confirme a conexão com o banco
· Cheque o log de erros do PHP

Logs e Debug

```php
// Ative o modo debug em includes/config.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

📋 Padrões de Código

· Siga PSR-12 para PHP
· Use JavaScript ES6+ moderno
· Mantenha a responsividade
· Documente novas funcionalidades

🏷️ Template para Issues

```markdown
## Descrição do Problema

## Passos para Reproduzir

## Comportamento Esperado

## Screenshots (se aplicável)

## Ambiente
- PHP Version: 
- MySQL Version:
- Browser:
```

🚀 Roadmap

Próximas Funcionalidades

· Colunas Customizáveis - Usuário pode criar suas próprias colunas
· Etiquetas e Tags - Sistema de categorização flexível
· Comentários nas Tarefas - Discussão e colaboração
· Anexos de Arquivos - Upload de documentos e imagens
· Modo Escuro - Tema dark para melhor experiência
· Notificações - Alertas para prazos próximos
· Relatórios - Métricas e analytics dos boards
· Exportação - PDF, Excel dos dados
· API Pública - Integração com outros sistemas
· Templates - Boards pré-configurados

Melhorias Técnicas

· Testes Unitários - PHPUnit para o backend
· Otimização de Performance - Cache e lazy loading
· Internacionalização - Multi-idioma
· PWA - Progressive Web App capabilities
· WebSockets - Atualização em tempo real verdadeiro

🚀 Como Executar

1. Configure o banco:

```bash
mysql -u root -p < data/database.sql
```

1. Acesse o projeto:

```bash
php -S localhost:8000
```

1. Cadastre-se e comece a usar!

✅ Funcionalidades Implementadas:

· Sistema de autenticação completo
· CRUD de boards e colunas
· Drag & Drop funcional entre colunas
· Criação e edição de tarefas
· Sistema de prioridades com cores
· Datas de vencimento
· Design responsivo com Tailwind
· APIs RESTful para todas operações
· Validações de segurança
· Feedback visual durante operações

🙏 Agradecimentos

· Tailwind CSS pelo incrível framework CSS
· Font Awesome pelos ícones de qualidade
· PHP pela linguagem robusta do backend
· Comunidade JavaScript pelas APIs modernas

---

<div align="center">

Desenvolvido com ❤️ usando PHP, JavaScript e Tailwind CSS

Organize suas tarefas de forma visual e eficiente! 🎯

</div>

---

## 🤝 Contribuições
Contribuições são sempre bem-vindas!  
Sinta-se à vontade para abrir uma [*issue*](https://github.com/NinoMiquelino/kanban-task-manager/issues) com sugestões ou enviar um [*pull request*](https://github.com/NinoMiquelino/kanban-task-manager/pulls) com melhorias.

---

## 💬 Contato
📧 [Entre em contato pelo LinkedIn](https://www.linkedin.com/in/onivaldomiquelino/)  
💻 Desenvolvido por **Onivaldo Miquelino**

---
