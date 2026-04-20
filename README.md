# Letudo.pt - Livraria Online (Projeto Final)

Projeto desenvolvido para a unidade curricular **Design e Programacao Avancada de Websites**.
Livraria online em **PHP + MariaDB/MySQL** inspirada na almedina.pt.

---

## Requisitos

- PHP 8.0+ (com extensao PDO MySQL)
- MariaDB 10+ ou MySQL 5.7+
- Servidor web: Apache (XAMPP/WAMP/MAMP) ou servidor embutido do PHP

## Instalacao (XAMPP / WAMP)

### 1. Copiar ficheiros
Copia a pasta `site/` inteira para dentro de `htdocs/` (XAMPP) ou `www/` (WAMP).
Renomeia a pasta para o nome que quiseres, por ex. `letudo`.

### 2. Criar a base de dados
1. Abre o phpMyAdmin em `http://localhost/phpmyadmin`
2. Importa o ficheiro `site/sql/estrutura.sql` (cria a BD `letudo`, tabelas e dados iniciais)
3. A BD fica com 16 livros, 1 admin e 1 cliente.

### 3. Configurar ligacao a BD
Edita `site/config/db.php` e coloca as tuas credenciais MySQL:
```php
$host     = '127.0.0.1';
$dbname   = 'letudo';
$username = 'root';        // o teu user MySQL
$password = '';            // a tua password MySQL
```

### 4. Aceder ao site
`http://localhost/letudo/` (ou o nome que deste a pasta)

---

## Credenciais de acesso

| Tipo     | Username | Password     | Email              |
|----------|----------|--------------|--------------------|
| Admin    | admin    | admin123     | admin@letudo.pt    |
| Cliente  | cliente  | cliente123   | cliente@letudo.pt  |

O login aceita **email OU username** no mesmo campo.

---

## Funcionalidades implementadas (conforme enunciado do PDF)

### Homepage (`index.php`)
- Lista de livros com imagem, titulo, autor, preco, stock
- Pesquisa por titulo, autor, descricao (`?q=`)
- Filtros por categoria (`?cat=`)
- Ordenacao por preco / nome / data
- Contador de itens no carrinho no header
- Link para a pagina de admin (visivel se estiver logado como admin)
- Mensagens para "sem stock" e "esgotado"

### Carrinho (`pages/checkout.php`)
- Adicionar, aumentar, diminuir, remover, limpar
- Subtotal, portes (gratis > 30€), total dinamico
- Pre-preenchimento dos dados se utilizador logado

### Finalizacao de compra
- Formulario: Nome, Data de Nascimento, Morada
- **Validacao idade >= 18 anos** (server-side)
- **Protecao XSS** com `htmlspecialchars()` + `strip_tags()`
- Campos obrigatorios validados em PHP
- Insercao em tabela `encomendas` + `encomenda_itens`
- Decremento automatico de stock
- Pagina de sucesso

### Autenticacao
- Registo com validacao forte de password (>=8, 1 maiuscula, 1 numero)
- Login com `password_verify()` e `session_regenerate_id()`
- Logout

### Painel Admin (`admin/index.php`)
Requer login como admin.
- **Dashboard**: numero de produtos, encomendas, utilizadores, receita total, stock baixo
- **Tab Produtos**: editar stock e preco, eliminar
- **Tab Adicionar**: criar novo livro (com upload de imagem)
- **Tab Encomendas**: lista completa de encomendas
- **Tab Utilizadores**: listagem de contas registadas

### Design e UX
- Paleta inspirada na Almedina: bordo #8b0e0e + branco + dourado
- Fontes: DM Serif Display (titulos) + Libre Franklin (corpo)
- Topbar, header sticky com pesquisa, nav de categorias, footer 4 colunas com newsletter
- **Responsivo mobile-first** (breakpoints 960px e 600px)
- **SEO**: meta description, keywords, lang=pt, HTML semantico, titulos dinamicos

---

## Estrutura de ficheiros

```
site/
├── index.php              # Homepage (catalogo + hero + filtros)
├── perfil.php             # Perfil do cliente
├── logout.php
├── config/db.php          # Conexao PDO
├── includes/
│   ├── header.php         # Topbar + header + nav
│   └── footer.php         # Footer + newsletter + redes sociais
├── css/style.css          # Estilo Almedina-inspired
├── js/scripts.js          # Animacoes
├── pages/
│   ├── login.php
│   ├── registo.php
│   ├── checkout.php       # Carrinho + checkout com validacoes
│   ├── processar_carrinho.php
│   └── sucesso.php
├── admin/
│   └── index.php          # Painel de administracao
├── img/                   # 22 imagens de capas de livros
└── sql/
    └── estrutura.sql      # Schema MySQL + dados iniciais
```

---

## Tecnologias usadas

- **PHP 8** - logica server-side e templating
- **MariaDB/MySQL** - base de dados relacional (InnoDB, chaves estrangeiras)
- **PDO** - acesso a BD com prepared statements (protecao SQL injection)
- **HTML5 semantico** + **CSS3** com custom properties, grid e flexbox
- **JavaScript vanilla** - animacoes de entrada dos cards

## Seguranca

- Passwords com `password_hash(PASSWORD_DEFAULT)` (bcrypt)
- Sessoes com regeneracao de ID no login
- Protecao XSS em todos os inputs visiveis com `htmlspecialchars() + strip_tags()`
- Prepared statements PDO em todas as queries
- Controlo de acesso a /admin (redireciona se nao for admin)
- Validacao server-side para campos obrigatorios e idade 18+

---

Desenvolvido em 2026 por EuricoSuper - letudo.pt
