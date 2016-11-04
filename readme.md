# Modelo Integração com Paygo atravéz do ControlPay

##Configuração de cron

```shell
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```


##Fluxo 1: Efetuar venda

layout arquivo de request:

	identificador=47116498000116
	referencia=66
	api=/venda/vender
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
	data.intencaoVenda.id=22790
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
	data.intencaoVenda.id=22790
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

	

##Fluxo 2: Consulta de intenção de venda

layout arquivo de request:
	identificador=47116498000116
	referencia=99
	api=/intencaovenda/getbyid
	param.intencaoVendaId=22744

layout arquivo de response:
	response.status=0
	response.message=Dados processados com sucesso
	data.intencaoVenda.id=22790
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

Obs: O nome de cada arquivo deve ser único, assim como a referência	
	
	
	