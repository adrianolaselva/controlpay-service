

[![Build Status](https://travis-ci.org/adrianolaselva/controlpay-service.svg?branch=master)](https://travis-ci.org/adrianolaselva/controlpay-service)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/adrianolaselva/controlpay-service/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/adrianolaselva/controlpay-service/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/adrianolaselva/controlpay-service/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/adrianolaselva/controlpay-service/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/adrianolaselva/controlpay-service/badges/build.png?b=master)](https://scrutinizer-ci.com/g/adrianolaselva/controlpay-service/build-status/master)


# Modelo Integração com Pay&go através do ControlPay

##Apis

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/f88f36b67c49582c4fd2)

##Configuração de cron

```shell
* * * * * php /var/www/api.controlpay-service.com.br/artisan schedule:run >> /dev/null 2>&1
```

##Diretórios


Diretório de requisição:

	/var/www/api.controlpay-service.com.br/storage/app/req/

**Obs: Diretório de requisição**

Diretório de resposta:

	/var/www/api.controlpay-service.com.br/storage/app/resp/

**Obs: Diretório de respostas**

Diretório de configuração:

	/var/www/api.controlpay-service.com.br/storage/app/conf/

**Obs: Diretório contendo os arquivos de configuração dos terminais**

Diretório de erros:

	/var/www/api.controlpay-service.com.br/storage/app/error/

**Obs: Quando ocorre algum erro no processamento uma cópia do arquivo é movida para este diretório e uma respoosta é gerada com a exceção**

Diretório de arquivos processados:

	/var/www/api.controlpay-service.com.br/storage/app/proccessed/
	
**Obs: Após o processamento uma cópia é adicionada neste diretório e uma resposta é gerada**


##Configurações

**Configuração de parâmetros do lumen**

	APP_ENV=production
	APP_DEBUG=true
	APP_KEY=
	APP_URL=http://0.0.0.0.0 <= IP da máquina
	APP_LOG_LEVEL=error <= Nível de logs, manter default como error
	
	DB_CONNECTION=mysql
	DB_HOST=localhost
	DB_PORT=3306
	DB_DATABASE=controlpay
	DB_USERNAME=root
	DB_PASSWORD=root
	
	STORAGE_CONFIG=local <= Configuração de storage de arquivos, como padrão é o local, ele esta configurado para usar o diretório dentro do projeto storage/app/*

**Obs: Criar arquivo .env na raiz do projeto caso não exista e configurar os parâmetros**

**Configuração de parâmetros**

	[CONTROLPAY]
	CONTROLPAY_HOST=
	CONTROLPAY_USER=
	CONTROLPAY_PWD=
	CONTROLPAY_DEFAULT_TERMINAL_ID=
	CONTROLPAY_DEFAULT_PRODUTO_ID=
	CONTROLPAY_DEFAULT_FORMA_PAGAMENTO_ID=
	CONTROLPAY_DEFAULT_FORMA_AGUARDA_TEF=true
	CONTROLPAY_DEFAULT_SENHA_TECNICA=

**Obs 1: O nome do arquivo deve ser o mesmo do parâmetro 'CONTROLPAY_USER'**

**Obs 2: Este arquivo deve ser colocado no diretório /conf**

##Fluxo 1: Efetuar venda

**layout arquivo de request:**

	identificador=99999999999999 <= (Obrigatório)
	api=/venda/vender <= (Obrigatório)
	param.referencia= <= (Obrigatório) esta referência deve ser única
	param.operadorId=
	param.pessoaClienteId=
	param.formaPagamentoId=21 <= (Obrigatório) Este parâmetro pode ser definido no arquivo de configurações
	param.pedidoId=
	param.terminalId=59 <= (Obrigatório) Este parâmetro pode ser definido no arquivo de configurações
	param.integracaoId=
	param.valorTotalVendido=
	param.valorAcrescimo=
	param.valorDesconto=
	param.observacao=
	param.aguardarTefIniciarTransacao=true <= (Obrigatório) Este parâmetro pode ser definido no arquivo de configurações
	param.parcelamentoAdmin=
	param.quantidadeParcelas=
	param.produtosVendidos.000.id=41 <= (Obrigatório) Este parâmetro pode ser definido no arquivo de configurações
	param.produtosVendidos.000.quantidade=1 <= (Obrigatório) Este parâmetro pode ser definido no arquivo de configurações
	param.produtosVendidos.000.valor=12.00  <= (Obrigatório)
	param.produtosVendidos.001.id=41
	param.produtosVendidos.001.quantidade=1
	param.produtosVendidos.001.valor=18.80

**layout arquivo de response:**

	response.status=0
	response.message=Dados processados com sucesso
	data.intencaoVenda.id=99999
	data.intencaoVenda.token=375588
	data.intencaoVenda.data=2016-11-03T09:44:18+0000
	data.intencaoVenda.hora=09:44:18
	data.intencaoVenda.valorOriginal=30.8
	data.intencaoVenda.valorAcrescimo=0
	data.intencaoVenda.valorDesconto=0
	data.intencaoVenda.valorFinal=30.8
	data.intencaoVenda.valorOriginalFormat=30,80
	data.intencaoVenda.valorDescontoFormat=0,00
	data.intencaoVenda.valorAcrescimoFormat=0,00
	data.intencaoVenda.valorFinalFormat=30,80
	data.intencaoVenda.quantidade=2
	data.intencaoVenda.intencaoVendaStatus.id=6
	data.intencaoVenda.intencaoVendaStatus.nome=Em Pagamento
	data.intencaoVenda.formaPagamento.id=21
	data.intencaoVenda.formaPagamento.nome=TEF
	data.intencaoVenda.formaPagamento.modalidade=Crédito
	data.intencaoVenda.produtos.0.id=41
	data.intencaoVenda.produtos.0.itemProdutoId=12701
	data.intencaoVenda.produtos.0.nome=Produto sem estoque/valor - refeição-41
	data.intencaoVenda.produtos.0.quantidade=1
	data.intencaoVenda.produtos.0.valor=12,00
	data.intencaoVenda.produtos.1.id=41
	data.intencaoVenda.produtos.1.itemProdutoId=12702
	data.intencaoVenda.produtos.1.nome=Produto sem estoque/valor - refeição-41
	data.intencaoVenda.produtos.1.quantidade=1
	data.intencaoVenda.produtos.1.valor=18,80

**layout arquivo de response callback:**

	response.status=0
	response.message=Dados processados com sucesso
	data.intencaoVenda.id=99999
	data.intencaoVenda.token=375588
	data.intencaoVenda.data=2016-11-03T09:44:18+0000
	data.intencaoVenda.hora=09:44:18
	data.intencaoVenda.valorOriginal=30.8
	data.intencaoVenda.valorAcrescimo=0
	data.intencaoVenda.valorDesconto=0
	data.intencaoVenda.valorFinal=30.8
	data.intencaoVenda.valorOriginalFormat=30,80
	data.intencaoVenda.valorDescontoFormat=0,00
	data.intencaoVenda.valorAcrescimoFormat=0,00
	data.intencaoVenda.valorFinalFormat=30,80
	data.intencaoVenda.quantidade=2
	data.intencaoVenda.intencaoVendaStatus.id=10
	data.intencaoVenda.intencaoVendaStatus.nome=Creditado
	data.intencaoVenda.formaPagamento.id=21
	data.intencaoVenda.formaPagamento.nome=TEF
	data.intencaoVenda.formaPagamento.modalidade=Crédito
	data.intencaoVenda.produtos.0.id=99
	data.intencaoVenda.produtos.0.itemProdutoId=12701
	data.intencaoVenda.produtos.0.nome=Produto sem estoque/valor - refeição-41
	data.intencaoVenda.produtos.0.quantidade=1
	data.intencaoVenda.produtos.0.valor=12,00
	data.intencaoVenda.produtos.1.id=99
	data.intencaoVenda.produtos.1.itemProdutoId=12702
	data.intencaoVenda.produtos.1.nome=Produto sem estoque/valor - refeição-41
	data.intencaoVenda.produtos.1.quantidade=1
	data.intencaoVenda.produtos.1.valor=18,80	



##Fluxo 2: Consulta de intenção por filtros
    
**layout arquivo de request:**
    
        identificador=99999999999999 <= (Obrigatório)
        api=/intencaovenda/getbyfiltros <= (Obrigatório)
        param.referencia=99999 <= (Obrigatório)

**layout arquivo de response:**
    
        response.status=0
        response.message=Dados processados com sucesso
        data.intencaoVenda.0.id=23098
        data.intencaoVenda.0.referencia=361622_18
        data.intencaoVenda.0.token=632239
        data.intencaoVenda.0.data=2016-11-09T11:25:28+0000
        data.intencaoVenda.0.hora=11:25:28
        data.intencaoVenda.0.valorOriginal=12
        data.intencaoVenda.0.valorAcrescimo=0
        data.intencaoVenda.0.valorDesconto=0
        data.intencaoVenda.0.valorFinal=12
        data.intencaoVenda.0.valorOriginalFormat=12,00
        data.intencaoVenda.0.valorDescontoFormat=0,00
        data.intencaoVenda.0.valorAcrescimoFormat=0,00
        data.intencaoVenda.0.valorFinalFormat=12,00
        data.intencaoVenda.0.quantidade=1
        data.intencaoVenda.0.intencaoVendaStatus.id=10
        data.intencaoVenda.0.intencaoVendaStatus.nome=Creditado
        data.intencaoVenda.0.formaPagamento.id=21
        data.intencaoVenda.0.formaPagamento.nome=TEF
        data.intencaoVenda.0.formaPagamento.modalidade=Crédito
        data.intencaoVenda.0.formaPagamento.fluxoPagamento.id=21
        data.intencaoVenda.0.formaPagamento.fluxoPagamento.nome=TEF
        data.intencaoVenda.0.terminal.id=116
        data.intencaoVenda.0.terminal.nome=Terminal 1


##Fluxo 3: Cancelamento de venda

**layout arquivo de request:**
    
        identificador=99999999999999 <= (Obrigatório)
        api=/venda/cancelarvenda <= (Obrigatório)
        param.intencaoVendaId=99999 <= (Obrigatório)
        
**layout arquivo de response:**
    
        response.status=0
        response.message=Dados processados com sucesso
        data.intencaoVenda.id=99999
        data.intencaoVenda.token=375588
        data.intencaoVenda.data=2016-11-03T09:44:18+0000
        data.intencaoVenda.hora=09:44:18
        data.intencaoVenda.valorOriginal=30.8
        data.intencaoVenda.valorAcrescimo=0
        data.intencaoVenda.valorDesconto=0
        data.intencaoVenda.valorFinal=30.8
        data.intencaoVenda.valorOriginalFormat=30,80
        data.intencaoVenda.valorDescontoFormat=0,00
        data.intencaoVenda.valorAcrescimoFormat=0,00
        data.intencaoVenda.valorFinalFormat=30,80
        data.intencaoVenda.quantidade=2
        data.intencaoVenda.intencaoVendaStatus.id=20
        data.intencaoVenda.intencaoVendaStatus.nome=Cancelado
        data.intencaoVenda.formaPagamento.id=21
        data.intencaoVenda.formaPagamento.nome=TEF
        data.intencaoVenda.formaPagamento.modalidade=Crédito
        data.intencaoVenda.produtos.0.id=99
        data.intencaoVenda.produtos.0.itemProdutoId=12701
        data.intencaoVenda.produtos.0.nome=Produto sem estoque/valor - refeição-41
        data.intencaoVenda.produtos.0.quantidade=1
        data.intencaoVenda.produtos.0.valor=12,00
        data.intencaoVenda.produtos.1.id=99
        data.intencaoVenda.produtos.1.itemProdutoId=12702
        data.intencaoVenda.produtos.1.nome=Produto sem estoque/valor - refeição-41
        data.intencaoVenda.produtos.1.quantidade=1
        data.intencaoVenda.produtos.1.valor=18,80	

##Configurações

**Configuração de parâmetros**

    [CONTROLPAY]
    CONTROLPAY_HOST=
    CONTROLPAY_USER=
    CONTROLPAY_PWD=
    CONTROLPAY_DEFAULT_TERMINAL_ID=
    CONTROLPAY_DEFAULT_PRODUTO_ID=
    CONTROLPAY_DEFAULT_FORMA_PAGAMENTO_ID=
    CONTROLPAY_DEFAULT_FORMA_AGUARDA_TEF=true
    CONTROLPAY_DEFAULT_SENHA_TECNICA=

**Obs: O nome de cada arquivo deve ser único para cada identificador**

**Obs 2: Sempre que for um processo assíncrono será retornado um arquivo seguindo o seguinte padrão callback_(nome do arquivo de requisição)**


##Atualização

**Upgrade e downgrade de versão**

```sh
    cd /var/www/api.controlpay-service.com.br/  && git fetch --tags
    cd /var/www/api.controlpay-service.com.br/  && git checkout tags/{VERSAO_DO_COMPONENTE} -f
    cd /var/www/api.controlpay-service.com.br/  && /usr/local/bin/composer update
    php /var/www/api.controlpay-service.com.br/artisan migrate --force
```

##Instalação ambiente no rethat 7.2

```sh
    #Configuração de ambiente RedHat 7.2
    #Configurações de ambiente lumen
    
    yum -y update
    yum install -y wget
    
    #Repositórios
    rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
    rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm
    
    wget http://repo.mysql.com/mysql-community-release-el6-5.noarch.rpm
    rpm -ivh mysql-community-release-el6-5.noarch.rpm
    
    #instalação php
    yum install -y php56w php56w-fpm php56w-opcache 
    yum install -y php56w-cli php56w-common php56w-mbstring php56w-mcrypt php56w-mysql php56w-pdo php56w-pear php56w-xml php56w-soap php56w-odbc php56w-intl
    yum install -y httpd
    service httpd start
    #mysql
    yum install -y mysql-server
    
    #inicia mysql
    service mysqld start
    #configurações do mysql
    mysql -u root -e 'create database controlpay;'
    mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%'"
    mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost'"
    echo "USE mysql;\nUPDATE user SET password=PASSWORD('root') WHERE user='root';\nFLUSH PRIVILEGES;\n" | mysql -u root
    
    #abrir porta 80
    iptables -I INPUT -p tcp --dport 80 -j ACCEPT
    service iptables save
    
    #instalação do git
    yum install -y git
    
    #instalação do composer
    curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
    
    #Baixar projeto do github e instalar
    #cd /var/www/
    git clone --depth=50 --branch=0.1.3 https://github.com/adrianolaselva/controlpay-service.git /var/www/api.controlpay-service.com.br
    #cd api.controlpay-service.com.br
    mkdir -p /var/www/api.controlpay-service.com.br/storage/app/conf
    mkdir -p /var/www/api.controlpay-service.com.br/storage/app/error
    mkdir -p /var/www/api.controlpay-service.com.br/storage/app/proccessed
    mkdir -p /var/www/api.controlpay-service.com.br/storage/app/req
    mkdir -p /var/www/api.controlpay-service.com.br/storage/app/resp
    
    chmod 777 -R /var/www/api.controlpay-service.com.br/storage/
    
    cd /var/www/api.controlpay-service.com.br/  && /usr/local/bin/composer install
    mv /var/www/api.controlpay-service.com.br/.env.example /var/www/api.controlpay-service.com.br/.env
    
    php /var/www/api.controlpay-service.com.br/artisan migrate --force
    php /var/www/api.controlpay-service.com.br/artisan db:seed --force
    
    #configurações do apache
    echo '
    NameVirtualHost *:80
    
    <Directory "/var/www">
        AllowOverride All
        # Allow open access:
        #Require all granted
    </Directory>
    
    <VirtualHost *:80>
            DocumentRoot /var/www/api.controlpay-service.com.br/public
    </VirtualHost>' > /etc/httpd/conf.d/vhost.conf
    
    #inicia apache
    service httpd restart
    
    #configuração de cron
    echo "* * * * * php /var/www/api.controlpay-service.com.br/artisan schedule:run >> /dev/null 2>&1" >> mycron
    crontab mycron
    rm -rf mycron
    
    echo '
    APP_ENV=production
    APP_DEBUG=true
    APP_KEY=
    APP_URL=
    APP_LOG_LEVEL=error
    
    DB_CONNECTION=mysql
    DB_HOST=localhost
    DB_PORT=3306
    DB_DATABASE=controlpay
    DB_USERNAME=root
    DB_PASSWORD=root
    
    STORAGE_CONFIG=local ' >  /var/www/api.controlpay-service.com.br/.env
    
    #Arquivos de configurações do estabelecimento
    echo '
    [CONTROLPAY]
    CONTROLPAY_HOST=http://pay2alldemo.azurewebsites.net/webapi
    CONTROLPAY_USER=99999999999
    CONTROLPAY_PWD=123mudar
    CONTROLPAY_KEY=y%2bLGOLoPhcbZJ0G0XvuL1o9rCrCc7o2%2fzbrPaJm1t8wJus3jOR6htmabgLY4O9wOTC0b82lCyMu1xMrruFO7GfnQRLHt6%2fbwJ9SJ3XFeGUI%3w
    CONTROLPAY_DEFAULT_TERMINAL_ID=116
    CONTROLPAY_DEFAULT_PRODUTO_ID=85
    CONTROLPAY_DEFAULT_PRODUTO_QTDE=1
    CONTROLPAY_DEFAULT_FORMA_PAGAMENTO_ID=21
    CONTROLPAY_DEFAULT_FORMA_AGUARDA_TEF=true
    CONTROLPAY_DEFAULT_SENHA_TECNICA=123mudar' > /var/www/api.controlpay-service.com.br/storage/app/conf/99999999999
    
    #Desativa políticas de segurança default do centos
    setenforce 0

```