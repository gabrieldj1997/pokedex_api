🚀 Como Colocar o Projeto Para Rodar
Esta seção detalha todos os requisitos e os passos de instalação necessários para ter o projeto funcionando em seu ambiente de desenvolvimento local.

📋 Pré-Requisitos
Certifique-se de que os seguintes softwares estão instalados em sua máquina:

PHP: Versão 8.2 ou superior.

Composer: O gerenciador de dependências oficial do PHP.

Node.js & npm/Yarn: Necessários para as dependências de front-end e para o compilador de assets (Vite).

Banco de Dados:

Padrão: O projeto utiliza SQLite (armazenado em um arquivo) para a configuração de desenvolvimento mais rápida.

Alternativa: Você pode usar MySQL ou outro SGBD editando as variáveis de conexão no arquivo .env.

🛠️ Configuração e Instalação (Passo a Passo)
Siga os passos abaixo na pasta raiz do projeto após clonar o repositório:

1. Instalar as Dependências
Primeiro, instale as dependências do back-end (PHP) e do front-end (JavaScript):

Bash

# 1. Instala as dependências do Laravel via Composer
composer install

# 2. Instala as dependências do front-end (Vite, Livewire, etc.)
npm install

2. Configurar o Ambiente
Copie o arquivo de configuração de exemplo e gere a chave única de segurança da sua aplicação:

Bash

# Cria o arquivo .env a partir do modelo
cp .env.example .env

# Gera a chave de segurança da aplicação
php artisan key:generate

3. Preparar o Banco de Dados
Seu projeto usa o SQLite por padrão. O arquivo necessário será criado automaticamente no próximo passo, mas você pode garantir sua existência com: touch database/database.sqlite (opcional).

Execute as migrations para criar a estrutura das tabelas no banco de dados:

Bash

# Cria as tabelas do banco de dados (users, migrations, etc.)
php artisan migrate
4. Iniciar a Aplicação
Para iniciar o desenvolvimento, você precisará de dois processos rodando em terminais separados: um para o servidor PHP e outro para compilar os assets.

Terminal 1: Servidor Laravel

Bash

php artisan serve
Terminal 2: Compilador de Assets (Vite)

Bash

npm run dev
Seu projeto estará acessível no endereço fornecido pelo php artisan serve (geralmente http://127.0.0.1:8000).

👾 Kanto Discovery: A Pokedex da 1ª Geração
🌟 Visão Geral do Projeto
Este projeto é uma Pokedex nostálgica focada exclusivamente na primeira geração de Pokémon (os originais 151).

Ele combina a funcionalidade de um catálogo com um divertido desafio: "Qual é esse Pokémon?". Ao iniciar a aplicação, sua Pokedex estará completamente vazia. Apenas à medida que você descobre novos Pokémon no jogo, eles se tornam disponíveis para visualização completa na Pokedex.

É um projeto ideal para testar suas habilidades como Treinador Pokémon e para contribuir com uma base de código Laravel limpa e focada em API externa.

🛠️ Primeiros Passos: Configuração e Instalação
Siga os passos de [Configuração e Instalação do Guia anterior] para configurar o ambiente (dependências PHP e Node, arquivo .env, e migrations).

1. Inicialização da Pokedex (Primeiro Uso)
Ao rodar a aplicação pela primeira vez, sua Pokedex estará vazia. Você precisa capturar os dados essenciais da PokeAPI.co.

Atenção: Você deve executar estas rotas apenas uma vez para popular o banco de dados.

Passo	Rota (Acessar no navegador ou via API)	Ação
1. Capturar Lista	/pokemon	Preenche sua lista inicial de 151 Pokémon com nome e ID.
2. Capturar Sprites	/pokemon/sprites	Captura os IDs para buscar as imagens de cada Pokémon (mas não baixa as imagens em si).

Exportar para as Planilhas
Após rodar a segunda rota, sua Pokedex estará pronta para o jogo!

2. Customização das Sprites (Opcional)
Por padrão, a aplicação está configurada para exibir as sprites no estilo Home (modernas e coloridas). Você pode facilmente trocar o estilo para reviver a nostalgia da Geração I ou III.

Altere as seguintes variáveis no seu arquivo .env:

Variável	
Padrão	
Descrição

POKEMON_SPRITE_URL	
https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/	
A URL de onde o arquivo será buscado.

POKEMON_SPRITE_TYPE
.png	
O tipo de arquivo (extensão) que será anexado ao ID do Pokémon.

Exemplos de Sprites que você pode usar:

Estilo de Imagem	POKEMON_SPRITE_URL	POKEMON_SPRITE_TYPE
Official Artwork (Desenho oficial)	https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/	.png
Black/White Animated (GIFs)	https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-v/black-white/animated/	.gif
Geração I - Red/Blue (Pixel)	https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-i/red-blue/	.png

Exportar para as Planilhas
🔄 Rotas de Manutenção (Reset)
Existem duas rotas úteis para resetar o estado do seu jogo e experimentar as customizações:

Rota (Acessar no navegador ou via API)	Função
/reset/list	Reseta a Lista de Descoberta. Apaga todos os Pokémon que você descobriu, permitindo que você jogue o desafio "Qual é esse Pokémon?" novamente do zero.
/reset/img	Apaga o Cache de Imagens. Útil se você mudar a variável POKEMON_SPRITE_URL no .env e quiser que o sistema baixe o novo conjunto de sprites.

🤝 Contribuições
Contribuições são muito bem-vindas! Sinta-se à vontade para adicionar novas funcionalidades, refatorar código ou corrigir bugs.

Crie um fork do projeto.

Crie sua branch de funcionalidade (git checkout -b feature/minha-nova-funcionalidade).

Faça seu commit (git commit -m 'feat: adiciona nova funcionalidade X').

Faça o push para a branch (git push origin feature/minha-nova-funcionalidade).

Abra um Pull Request.

Assim que possível, revisarei e farei o merge das suas contribuições!

🔗 Informações Adicionais
API Consumida: PokeAPI

Desenvolvedor: Gabriel Costa [LinkedIn](https://www.linkedin.com/in/gabriel-costa-578a9719b/)

📄 Licença
Este projeto está licenciado sob a Licença Pública Geral GNU (GPL).