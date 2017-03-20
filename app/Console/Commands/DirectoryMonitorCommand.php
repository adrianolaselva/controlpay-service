<?php

namespace App\Console\Commands;

use App\Business\CPayIntencaoVenda;
use App\Business\CPayVender;
use App\Helpers\CPayFileHelper;
use App\Models\File;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Integracao\ControlPay;
use League\Flysystem\Exception;

/**
 * Class DirectoryMonitorCommand
 * @package App\Console\Commands
 */
class DirectoryMonitorCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'controlpay-service:start {minutes?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializa worker para monitorar diretório com arquivos para acionar o tef';

    /**
     * @var array
     */
    private $cPayclient;

    /**
     * DirectoryMonitorCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $files = Storage::disk(env('STORAGE_CONFIG'))->files(CPayFileHelper::PATH_CONFIG);

        if(!is_null($files))
            foreach ($files as $file)
            {
                $params = parse_ini_string(Storage::disk(env('STORAGE_CONFIG'))->get($file));
                $this->cPayclient[basename($file)] = new ControlPay\Client($params);
            }

    }

    /**
     *
     */
    public function handle()
    {

        $arguments = $this->arguments();

        $minutes = isset($arguments['minutes']) ? $arguments['minutes'] : null;

        if(empty($minutes))
            $minutes = 60;

        /**
         * Adaptação para rodar command a cada 2 segundos
         */
        $inverval = 2;

        for($i = 0; $i < ceil($minutes/$inverval); $i++)
        {
            $this->readFiles();
            sleep($inverval);
        }
    }

    /**
     * Efetua leitura do diretório
     */
    private function readFiles()
    {
        $files = Storage::disk(env('STORAGE_CONFIG'))->files(CPayFileHelper::PATH_REQ);

        foreach ($files as $file)
        {
            if(strpos($file, '.wrk') !== false)
                continue;

            $file = CPayFileHelper::fileToWork($file);

            if(!Storage::disk(env('STORAGE_CONFIG'))->has($file))
                continue;

            $this->process($file);
        }
    }

    /**
     * @param $file
     */
    private function process($file)
    {
        try{
            $data = CPayFileHelper::loadFileContent($file);

            $requireParams = [
                'identificador',
                'api',
                'referencia'
            ];

            foreach ($requireParams as $param)
                if(!in_array($param, $requireParams))
                    throw new \Exception("Parâmetro '$param' não informado no arquivo!!");

            if(!isset($this->cPayclient[$data['identificador']]))
                throw new Exception(sprintf("Arquivo de configurações [conf/%s] não encontrado", $data['identificador']));

            if(!empty(File::where('name', basename($file))->first()))
                throw new \Exception(sprintf("Arquivo nome %s já utilizada",
                    str_replace('.wrk', '', basename($file))));

            if($data['api'] == CPayVender::API_VENDA_VENDER)
                if(!empty(File::where('reference', $data['referencia'])->first()))
                    throw new \Exception(sprintf("Referência %s já utilizada",
                        str_replace('.wrk', '', $data['referencia'])));

            Log::info(sprintf("Processando arquivo %s", basename($file)));

            $fileModel = File::create([
                'identifier' => $data['identificador'],
                'api' => $data['api'],
                'reference' => $data['referencia'],
                'name' => basename($file),
                'content' => json_encode($data, JSON_PRETTY_PRINT),
                'created_at' => Carbon::now()
            ]);

            $responseContent = null;

            switch (strtolower($data['api']))
            {
                case CPayVender::API_VENDA_VENDER:
                    $responseContent = (new CPayVender($this->cPayclient[$data['identificador']], $fileModel))
                        ->vender($data);
                    break;
                case CPayIntencaoVenda::API_INTENCAO_VENDA_GET_BY_ID:
                    $responseContent = (new CPayIntencaoVenda($this->cPayclient[$data['identificador']], $fileModel))
                        ->carregar($data);
                    break;
                case CPayVender::API_VENDA_CANCELAR:
                    $responseContent = (new CPayVender($this->cPayclient[$data['identificador']], $fileModel))
                        ->cancelarVenda($data);
                    break;
                case CPayIntencaoVenda::API_INTENCAO_VENDA_GET_BY_FILTROS:
                    $responseContent = (new CPayIntencaoVenda($this->cPayclient[$data['identificador']], $fileModel))
                        ->getByFiltros($data);
                    break;
                default:
                    throw new \Exception("Método {$data['api']} não implementado");
            }

            CPayFileHelper::fileProccessed($file, $responseContent);
        }catch (\Exception $ex){
            Log::error($ex->getMessage());
            CPayFileHelper::fileProccessedError($ex, $file);
        }
    }

}