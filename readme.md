

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
    data.intencaoVenda.id=23822
    data.intencaoVenda.formaPagamento.id=21
    data.intencaoVenda.formaPagamento.nome=TEF
    data.intencaoVenda.formaPagamento.modalidade=Crédito
    data.intencaoVenda.terminal.id=116
    data.intencaoVenda.terminal.nome=Terminal 1
    data.intencaoVenda.intencaoVendaStatus.id=10
    data.intencaoVenda.intencaoVendaStatus.nome=Creditado
    data.intencaoVenda.referencia=361622_218
    data.intencaoVenda.token=028953
    data.intencaoVenda.data=2016-12-06T18:22:26+0000
    data.intencaoVenda.hora=18:22:26
    data.intencaoVenda.valorOriginal=12
    data.intencaoVenda.valorAcrescimo=0
    data.intencaoVenda.valorDesconto=0
    data.intencaoVenda.valorFinal=12
    data.intencaoVenda.valorOriginalFormat=12,00
    data.intencaoVenda.valorDescontoFormat=0,00
    data.intencaoVenda.valorAcrescimoFormat=0,00
    data.intencaoVenda.valorFinalFormat=12,00
    data.intencaoVenda.latitude=0
    data.intencaoVenda.longitude=0
    data.intencaoVenda.quantidade=1
    data.intencaoVenda.formaPagamento.id=21
    data.intencaoVenda.formaPagamento.nome=TEF
    data.intencaoVenda.formaPagamento.modalidade=Crédito
    data.intencaoVenda.formaPagamento.fluxoPagamento.id=21
    data.intencaoVenda.formaPagamento.fluxoPagamento.nome=TEF
	data.intencaoVenda.pagamentosExternos.0.id=1997
    data.intencaoVenda.pagamentosExternos.0.tipo=5
    data.intencaoVenda.pagamentosExternos.0.origem=5
    data.intencaoVenda.pagamentosExternos.0.tipoParcelamento=2
    data.intencaoVenda.pagamentosExternos.0.pagamentoExternoStatus.id=15
    data.intencaoVenda.pagamentosExternos.0.pagamentoExternoStatus.nome=Finalizado
    data.intencaoVenda.pagamentosExternos.0.nsuTid=99999999999
    data.intencaoVenda.pagamentosExternos.0.autorizacao=999999
    data.intencaoVenda.pagamentosExternos.0.adquirente=REDECARD
    data.intencaoVenda.pagamentosExternos.0.codigoRespostaAdquirente=0
    data.intencaoVenda.pagamentosExternos.0.mensagemRespostaAdquirente=AUTORIZADA 999999
    data.intencaoVenda.pagamentosExternos.0.dataAdquirente=2016-12-06T18:22:49
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.0=000-000 = CRT
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.1=001-000 = 361622_218
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.2=002-000 = 361622_218
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.3=003-000 = 9999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.4=009-000 = 0
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.5=010-000 = REDECARD
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.6=011-000 = 10
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.7=012-000 = 99999999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.8=013-000 = 000999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.9=015-000 = 0999999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.10=016-000 = 0999999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.11=022-000 = 09999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.12=023-000 = 999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.13=027-000 = 99999999999999999999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.14=028-000 = 25
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.15=029-001 = ****************************************
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.16=029-002 = ********* DEMONSTRACAO  PAYGO *********
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.17=029-003 = ****************************************
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.18=029-004 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.19=029-005 =            COMPROVANTE DE TEF
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.20=029-006 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.21=029-007 =         ESTABELECIMENTO DE TESTE
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.22=029-008 =     999999999999999/99999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.23=029-009 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.24=029-010 =     06/12/2016              18:22:49
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.25=029-011 =     REF.FISCAL:361622_218
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.26=029-012 =     DOC:999999        AUTORIZ:999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.27=029-013 =     REF.HOST:99999999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.28=029-014 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.29=029-015 =     DEMOCARD        ************9999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.30=029-016 =     VENDA CREDITO A VISTA
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.31=029-017 =     VALOR FINAL: R$ 12,00
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.32=029-018 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.33=029-019 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.34=029-020 =     ________________________________
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.35=029-021 =                     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.36=029-022 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.37=029-023 = ****************************************
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.38=029-024 = ********* DEMONSTRACAO  PAYGO *********
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.39=029-025 = ****************************************
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.40=030-000 = AUTORIZADA 999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.41=040-000 = DEMOCARD
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.42=710-000 = 4
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.43=711-001 = VENDA CREDITO A VISTA
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.44=711-002 = DEMOCARD                ************1231
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.45=711-003 = POS:99999999  DOC:999999  AUTORIZ:999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.46=711-004 = VALOR FINAL: R$ 12,00
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.47=712-000 = 22
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.48=713-001 = ****************************************
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.49=713-002 = ********* DEMONSTRACAO  PAYGO *********
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.50=713-003 = ****************************************
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.51=713-004 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.52=713-005 =            COMPROVANTE DE TEF
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.53=713-006 =               VIA: CLIENTE
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.54=713-007 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.55=713-008 =         ESTABELECIMENTO DE TESTE
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.56=713-009 =     999999999999999/99999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.57=713-010 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.58=713-011 =     06/12/2016              18:22:49
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.59=713-012 =     REF.FISCAL:361622_218
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.60=713-013 =     DOC:999999        AUTORIZ:999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.61=713-014 =     REF.HOST:99999999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.62=713-015 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.63=713-016 =     DEMOCARD        ************9999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.64=713-017 =     VENDA CREDITO A VISTA
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.65=713-018 =     VALOR FINAL: R$ 12,00
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.66=713-019 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.67=713-020 = ****************************************
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.68=713-021 = ********* DEMONSTRACAO  PAYGO *********
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.69=713-022 = ****************************************
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.70=714-000 = 26
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.71=715-001 = ****************************************
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.72=715-002 = ********* DEMONSTRACAO  PAYGO *********
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.73=715-003 = ****************************************
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.74=715-004 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.75=715-005 =            COMPROVANTE DE TEF
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.76=715-006 =           VIA: ESTABELECIMENTO
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.77=715-007 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.78=715-008 =         ESTABELECIMENTO DE TESTE
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.79=715-009 =     999999999999999/99999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.80=715-010 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.81=715-011 =     06/12/2016              18:22:49
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.82=715-012 =     REF.FISCAL:361622_218
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.83=715-013 =     DOC:999999        AUTORIZ:999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.84=715-014 =     REF.HOST:99999999999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.85=715-015 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.86=715-016 =     DEMOCARD        ************9999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.87=715-017 =     VENDA CREDITO A VISTA
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.88=715-018 =     VALOR FINAL: R$ 12,00
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.89=715-019 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.90=715-020 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.91=715-021 =     ________________________________
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.92=715-022 =                     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.93=715-023 =     
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.94=715-024 = ****************************************
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.95=715-025 = ********* DEMONSTRACAO  PAYGO *********
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.96=715-026 = ****************************************
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.97=718-000 = DEMO
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.98=719-000 = ESTAB 42
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.99=729-000 = 2
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.100=730-000 = 1
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.101=731-000 = 1
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.102=732-000 = 1
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.103=737-000 = 3
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.104=739-000 = 001
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.105=740-000 = 999999*****99999
    data.intencaoVenda.pagamentosExternos.0.respostaAdquirente.106=999-999 = 0
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.0=****************************************
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.1= ********* DEMONSTRACAO  PAYGO *********
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.2= ****************************************
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.3=     
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.4=            COMPROVANTE DE TEF
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.5=     
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.6=         ESTABELECIMENTO DE TESTE
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.7=     999999999999999/99999999
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.8=     
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.9=     06/12/2016              18:22:49
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.10=     REF.FISCAL:361622_218
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.11=     DOC:003820        AUTORIZ:999999
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.12=     REF.HOST:99999999999
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.13=     
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.14=     DEMOCARD        ************9999
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.15=     VENDA CREDITO A VISTA
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.16=     VALOR FINAL: R$ 12,00
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.17=     
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.18=     
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.19=     ________________________________
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.20=                     
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.21=     
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.22= ****************************************
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.23= ********* DEMONSTRACAO  PAYGO *********
    data.intencaoVenda.pagamentosExternos.0.comprovanteAdquirente.24= ****************************************



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


**Obs: O arquivo de cancelamento sempre será o mesmo nome do arquivo de callback da transação original**

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

**Obs: Antes de executar os scripts de configuraçao do ambiente, certifique-se de que esteja com privilégios de root**