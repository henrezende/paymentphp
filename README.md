Este projeto consiste em uma API Laravel para gerenciamento de pagamentos e um aplicativo frontend construído com React.

## Recursos da API Laravel

- Operações CRUD para pagamentos
- Documentação Swagger para endpoints da API
- Ambiente Dockerizado para configuração fácil

## Recursos do Frontend

- Formulário de novo pagamento
- Integração com o SDK do MercadoPago para processamento de pagamentos
- Tailwind CSS para estilização

## Pré-requisitos

Antes de começar, certifique-se de atender aos seguintes requisitos:

- Docker instalado em sua máquina local

## Como Começar

Para começar com este projeto, siga estes passos:

- Pelo exclusivo uso do projeto, não será necessário alterações no arquivo .env.

1. Certifique-se de que está na raiz do projeto, onde temos o arquivo `docker-compose.yml`
2. Construa os contêineres Docker:

```
docker-compose up --build -d
ou
docker compose up --build -d
```

Essa etapa pode levar algum tempo.

3. Execute as migrations da API:

```
docker-compose exec api php artisan migrate
ou
docker compose exec api php artisan migrate
```

Obs: Na primeira vez em que os containers são construídos, pode levar alguns segundos até que seja possível executar as migrations, pois o MySQL irá realizar algmas configurações iniciais.

## Uso

Uma vez que a configuração estiver completa, você poderá acessar os endpoints da API e interagir com a aplicação.

#### Acessar a documentação da API (Swagger):

Após iniciar os contêineres, você pode acessar a documentação Swagger em http://localhost:8000/api/documentation.

#### Acessar o formulário de pagamento:

O formulário ficará disponível em http://localhost:3000/

#### Executar testes:

```
docker-compose exec api php artisan test
ou
docker compose exec api php artisan test
```
