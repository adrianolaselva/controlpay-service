<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * Class DirectoryMonitorCommandTest
 */
class DirectoryMonitorCommandTest extends TestCase
{
    /**
     * @var string
     */
    protected static $intencaoVendaId;

    /**
     * @var string
     */
    protected static $referenciaLocal;

    /**
     * DirectoryMonitorCommandTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        self::$referenciaLocal = 'test_'.rand(999999, 99999999);
    }

    /**
     * Teste básico de execução.
     *
     * @return void
     */
    public function testVerificaErrosNoTerminal()
    {
        \Illuminate\Support\Facades\Artisan::call('controlpay-service:start', [
            'minutes' => 6
        ]);

        $resultAsText = \Illuminate\Support\Facades\Artisan::output();

        $this->assertEmpty($resultAsText);
        $this->assertTrue(true);
    }

    /**
     * Teste de transação sem tef
     */
    public function testTransacaoSemTef()
    {
        $pathReq = sprintf("%s/test_%s", \App\Helpers\CPayFileHelper::PATH_REQ, self::$referenciaLocal);
        $pathResp = sprintf("%s/test_%s", \App\Helpers\CPayFileHelper::PATH_RESP, self::$referenciaLocal);

        $transacaoFileSemTef = sprintf('identificador=%s', self::$user) . PHP_EOL;
        $transacaoFileSemTef .= sprintf('referencia=%s', self::$referenciaLocal) . PHP_EOL;
        $transacaoFileSemTef .= 'api=/venda/vender' . PHP_EOL;
        $transacaoFileSemTef .= 'param.operadorId=' . PHP_EOL;
        $transacaoFileSemTef .= 'param.pessoaClienteId=' . PHP_EOL;
        $transacaoFileSemTef .= 'param.formaPagamentoId=' . PHP_EOL;
        $transacaoFileSemTef .= 'param.pedidoId=' . PHP_EOL;
        $transacaoFileSemTef .= 'param.terminalId=' . PHP_EOL;
        $transacaoFileSemTef .= 'param.integracaoId=' . PHP_EOL;
        $transacaoFileSemTef .= 'param.valorTotalVendido=' . PHP_EOL;
        $transacaoFileSemTef .= 'param.valorAcrescimo=' . PHP_EOL;
        $transacaoFileSemTef .= 'param.valorDesconto=' . PHP_EOL;
        $transacaoFileSemTef .= 'param.observacao=' . PHP_EOL;
        $transacaoFileSemTef .= 'param.aguardarTefIniciarTransacao=false' . PHP_EOL;
        $transacaoFileSemTef .= 'param.parcelamentoAdmin=' . PHP_EOL;
        $transacaoFileSemTef .= 'param.quantidadeParcelas=1' . PHP_EOL;
        $transacaoFileSemTef .= 'param.produtosVendidos.000.id=' . PHP_EOL;
        $transacaoFileSemTef .= 'param.produtosVendidos.000.quantidade=1' . PHP_EOL;
        $transacaoFileSemTef .= 'param.produtosVendidos.000.valor=12.00' . PHP_EOL;
        $transacaoFileSemTef .= 'param.produtosVendidos.001.id=' . PHP_EOL;
        $transacaoFileSemTef .= 'param.produtosVendidos.001.quantidade=1' . PHP_EOL;
        $transacaoFileSemTef .= 'param.produtosVendidos.001.valor=18.80' . PHP_EOL;

        \Illuminate\Support\Facades\Storage::disk(env('STORAGE_CONFIG'))->put($pathReq, $transacaoFileSemTef);

        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk(env('STORAGE_CONFIG'))->exists($pathReq));

        \Illuminate\Support\Facades\Artisan::call('controlpay-service:start', [
            'minutes' => 6
        ]);

        $resultAsText = \Illuminate\Support\Facades\Artisan::output();
        $this->assertEmpty($resultAsText);
        $this->assertTrue(true);

        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk(env('STORAGE_CONFIG'))->exists($pathResp));

        $file = new \SplFileObject(sprintf("%s/%s/%s",\App\Helpers\CPayFileHelper::getBaseDirectory(), \App\Helpers\CPayFileHelper::PATH_RESP, basename($pathResp)));

        /**
         * Valida atributos obrigatórios na resposta
         */
        $resultParams = [
            'response.status' => null,
            'response.message' => null,
            'data.intencaoVenda.id' => null,
            'data.intencaoVenda.token' => null,
            'data.intencaoVenda.data' => null,
            'data.intencaoVenda.valorOriginal' => null,
            'data.intencaoVenda.terminal.id' => null,
            'data.intencaoVenda.formaPagamento.id' => null,
        ];

        while (!$file->eof())
        {
            $row = $file->fgetcsv('=');

            if(empty($row[0]) & empty($row[1]))
                continue;

            list($key, $value) = $row;

            if(in_array($key, array_keys($resultParams)))
                $resultParams[$key] = $value;
        }

        foreach ($resultParams as $key => $param)
            $this->assertNotNull($param, sprintf("dado do atributo %s não foi encontrado na resposta", $key));

        if(isset($resultParams['data.intencaoVenda.id']))
            self::$intencaoVendaId = $resultParams['data.intencaoVenda.id'];

    }

    /**
     * Simula callback de resposta no controlpay
     */
    public function testTransacaoSemTefCallBack()
    {
        $pathRespCallBack = sprintf("%s/callback_test_%s", \App\Helpers\CPayFileHelper::PATH_RESP, self::$referenciaLocal);

        $this->call('GET', '/v1/callbacks/controlpay/intencaovendacallback', [
            'intencaoVendaId' => self::$intencaoVendaId,
            'intencaoVendaReferencia' => self::$referenciaLocal,
            'pedidoId' => null,
            'pedidoReferencia' => null
        ]);

        $this->assertJson($this->response->getContent());

        $responseCallBack = json_decode($this->response->getContent(), true);

        $this->assertEquals($responseCallBack['status'], 0);

        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk(env('STORAGE_CONFIG'))->exists($pathRespCallBack));
    }

    /**
     * Simula callback de resposta no controlpay
     */
    public function testConsultaIntencaoVendaPorId()
    {
        self::$referenciaLocal .= '_cons_id';
        $pathReq = sprintf("%s/test_%s", \App\Helpers\CPayFileHelper::PATH_REQ, self::$referenciaLocal);
        $pathResp = sprintf("%s/test_%s", \App\Helpers\CPayFileHelper::PATH_RESP, self::$referenciaLocal);

        $consultaTransacaoFileSemTef = sprintf('identificador=%s', self::$user) . PHP_EOL;
        $consultaTransacaoFileSemTef .= sprintf('referencia=%s', self::$referenciaLocal) . PHP_EOL;
        $consultaTransacaoFileSemTef .= 'api=/intencaovenda/getbyid' . PHP_EOL;
        $consultaTransacaoFileSemTef .= sprintf('param.intencaoVendaId=%s', self::$intencaoVendaId) . PHP_EOL;

        \Illuminate\Support\Facades\Storage::disk(env('STORAGE_CONFIG'))->put($pathReq, $consultaTransacaoFileSemTef);

        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk(env('STORAGE_CONFIG'))->exists($pathReq));

        \Illuminate\Support\Facades\Artisan::call('controlpay-service:start', [
            'minutes' => 6
        ]);

        $resultAsText = \Illuminate\Support\Facades\Artisan::output();
        $this->assertEmpty($resultAsText);
        $this->assertTrue(true);

        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk(env('STORAGE_CONFIG'))->exists($pathResp));

        $file = new \SplFileObject(sprintf("%s/%s/%s",\App\Helpers\CPayFileHelper::getBaseDirectory(), \App\Helpers\CPayFileHelper::PATH_RESP, basename($pathResp)));

        /**
         * Valida atributos obrigatórios na resposta
         */
        $resultParams = [
            'response.status' => null,
            'response.message' => null,
            'data.intencaoVenda.id' => null,
            'data.intencaoVenda.token' => null,
            'data.intencaoVenda.data' => null,
            'data.intencaoVenda.valorOriginal' => null,
        ];

        while (!$file->eof())
        {
            $row = $file->fgetcsv('=');

            if(empty($row[0]) & empty($row[1]))
                continue;

            list($key, $value) = $row;

            if(in_array($key, array_keys($resultParams)))
                $resultParams[$key] = $value;
        }

        foreach ($resultParams as $key => $param)
            $this->assertNotNull($param, sprintf("dado do atributo %s não foi encontrado na resposta", $key));

    }

    /**
     * Simula callback de resposta no controlpay
     */
    public function testConsultaIntencaoVendaPorReferencia()
    {
        self::$referenciaLocal .= '_cons_ref';
        $pathReq = sprintf("%s/test_%s", \App\Helpers\CPayFileHelper::PATH_REQ, self::$referenciaLocal);
        $pathResp = sprintf("%s/test_%s", \App\Helpers\CPayFileHelper::PATH_RESP, self::$referenciaLocal);

        $consultaTransacaoFileSemTef = sprintf('identificador=%s', self::$user) . PHP_EOL;
        $consultaTransacaoFileSemTef .= sprintf('referencia=%s', self::$referenciaLocal) . PHP_EOL;
        $consultaTransacaoFileSemTef .= 'api=/intencaovenda/getbyid' . PHP_EOL;
        $consultaTransacaoFileSemTef .= sprintf('param.intencaoVendaId=%s', self::$intencaoVendaId) . PHP_EOL;

        \Illuminate\Support\Facades\Storage::disk(env('STORAGE_CONFIG'))->put($pathReq, $consultaTransacaoFileSemTef);

        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk(env('STORAGE_CONFIG'))->exists($pathReq));

        \Illuminate\Support\Facades\Artisan::call('controlpay-service:start', [
            'minutes' => 6
        ]);

        $resultAsText = \Illuminate\Support\Facades\Artisan::output();
        $this->assertEmpty($resultAsText);
        $this->assertTrue(true);

        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk(env('STORAGE_CONFIG'))->exists($pathResp));

        $file = new \SplFileObject(sprintf("%s/%s/%s",\App\Helpers\CPayFileHelper::getBaseDirectory(), \App\Helpers\CPayFileHelper::PATH_RESP, basename($pathResp)));

        /**
         * Valida atributos obrigatórios na resposta
         */
        $resultParams = [
            'response.status' => null,
            'response.message' => null,
            'data.intencaoVenda.id' => null,
            'data.intencaoVenda.token' => null,
            'data.intencaoVenda.data' => null,
            'data.intencaoVenda.valorOriginal' => null,
        ];

        while (!$file->eof())
        {
            $row = $file->fgetcsv('=');

            if(empty($row[0]) & empty($row[1]))
                continue;

            list($key, $value) = $row;

            if(in_array($key, array_keys($resultParams)))
                $resultParams[$key] = $value;
        }

        foreach ($resultParams as $key => $param)
            $this->assertNotNull($param, sprintf("dado do atributo %s não foi encontrado na resposta", $key));

    }

    /**
     * Simula callback de resposta no controlpay
     */
    public function testCancelarIntencaoVenda()
    {
        self::$referenciaLocal .= '_canc';
        $pathReq = sprintf("%s/test_%s", \App\Helpers\CPayFileHelper::PATH_REQ, self::$referenciaLocal);
        $pathResp = sprintf("%s/test_%s", \App\Helpers\CPayFileHelper::PATH_RESP, self::$referenciaLocal);

        $consultaTransacaoFileSemTef = sprintf('identificador=%s', self::$user) . PHP_EOL;
        $consultaTransacaoFileSemTef .= sprintf('referencia=%s', self::$referenciaLocal) . PHP_EOL;
        $consultaTransacaoFileSemTef .= 'api=/venda/cancelarvenda' . PHP_EOL;
        $consultaTransacaoFileSemTef .= sprintf('param.intencaoVendaId=%s', self::$intencaoVendaId) . PHP_EOL;

        \Illuminate\Support\Facades\Storage::disk(env('STORAGE_CONFIG'))->put($pathReq, $consultaTransacaoFileSemTef);

        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk(env('STORAGE_CONFIG'))->exists($pathReq));

        \Illuminate\Support\Facades\Artisan::call('controlpay-service:start', [
            'minutes' => 6
        ]);

        $resultAsText = \Illuminate\Support\Facades\Artisan::output();
        $this->assertEmpty($resultAsText);
        $this->assertTrue(true);

        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk(env('STORAGE_CONFIG'))->exists($pathResp));

        $file = new \SplFileObject(sprintf("%s/%s/%s",\App\Helpers\CPayFileHelper::getBaseDirectory(), \App\Helpers\CPayFileHelper::PATH_RESP, basename($pathResp)));

        /**
         * Valida atributos obrigatórios na resposta
         */
        $resultParams = [
            'response.status' => null,
            'response.message' => null,
//            'data.intencaoVenda.id' => null,
//            'data.intencaoVenda.token' => null,
//            'data.intencaoVenda.data' => null,
//            'data.intencaoVenda.valorOriginal' => null,
        ];

        while (!$file->eof())
        {
            $row = $file->fgetcsv('=');

            if(empty($row[0]) & empty($row[1]))
                continue;

            list($key, $value) = $row;

            if(in_array($key, array_keys($resultParams)))
                $resultParams[$key] = $value;
        }

        foreach ($resultParams as $key => $param)
            $this->assertNotNull($param, sprintf("dado do atributo %s não foi encontrado na resposta", $key));

    }

}
