

[![Build Status](https://travis-ci.org/adrianolaselva/controlpay-service.svg?branch=master)](https://travis-ci.org/adrianolaselva/controlpay-service)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/adrianolaselva/controlpay-service/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/adrianolaselva/controlpay-service/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/adrianolaselva/controlpay-service/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/adrianolaselva/controlpay-service/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/adrianolaselva/controlpay-service/badges/build.png?b=master)](https://scrutinizer-ci.com/g/adrianolaselva/controlpay-service/build-status/master)


# Modelo Integração com Paygo atravéz do ControlPay

##Apis

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/f88f36b67c49582c4fd2)

##Configuração de cron

```shell
* * * * * php /var/www/api.controlpay-service.com.br/artisan schedule:run >> /dev/null 2>&1
```

##Diretórios


Diretório de requisição:

	/var/www/api.controlpay-service.com.br/storage/app/req/

Diretório de resposta:

	/var/www/api.controlpay-service.com.br/storage/app/req/

Diretório de configuração:

	/var/www/api.controlpay-service.com.br/storage/app/conf/

Diretório de erros:

	/var/www/api.controlpay-service.com.br/storage/app/error/


Diretório de arquivos processados:

	/var/www/api.controlpay-service.com.br/storage/app/proccessed/


##Configurações

Configuração de parâmetros

	[CONTROLPAY]
	CONTROLPAY_HOST=
	CONTROLPAY_USER=
	CONTROLPAY_PWD=
	CONTROLPAY_DEFAULT_TERMINAL_ID=
	CONTROLPAY_DEFAULT_PRODUTO_ID=
	CONTROLPAY_DEFAULT_FORMA_PAGAMENTO_ID=
	CONTROLPAY_DEFAULT_FORMA_AGUARDA_TEF=true
	CONTROLPAY_DEFAULT_SENHA_TECNICA=

Obs 1: O nome do arquivo deve ser o mesmo do parâmetro 'CONTROLPAY_USER'

Obs 2: Este arquivo deve ser colocado no diretório /conf

##Fluxo 1: Efetuar venda

layout arquivo de request:

	identificador=99999999999999
	api=/venda/vender
	param.referencia=
	param.operadorId=
	param.pessoaClienteId=
	param.formaPagamentoId=21
	param.pedidoId=
	param.terminalId=59
	param.integracaoId=
	param.valorTotalVendido=
	param.valorAcrescimo=
	param.valorDesconto=
	param.observacao=
	param.aguardarTefIniciarTransacao=true
	param.parcelamentoAdmin=
	param.quantidadeParcelas=
	param.produtosVendidos.000.id=41
	param.produtosVendidos.000.quantidade=1
	param.produtosVendidos.000.valor=12.00
	param.produtosVendidos.001.id=41
	param.produtosVendidos.001.quantidade=1
	param.produtosVendidos.001.valor=18.80

layout arquivo de response:

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

layout arquivo de response callback:

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

	
##Fluxo 2: Consulta de intenção de venda

layout arquivo de request:
        
	identificador=99999999999999
	api=/intencaovenda/getbyid
	param.intencaoVendaId=99999

layout arquivo de response:

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
    
layout arquivo de request:
    
	identificador=99999999999999
	api=/intencaovenda/getbyfiltros
	param.referencia=99999

layout arquivo de response:

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

layout arquivo de request:
    
	identificador=99999999999999
	api=/venda/cancelarvenda
	param.intencaoVendaId=99999
        
layout arquivo de response:

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


Obs: O nome de cada arquivo deve ser único para cada identificador
	
Obs 2: Sempre que for um processo assíncrono será retornado um arquivo seguindo o seguinte padrão
	callback_(nome do arquivo de requisição)
