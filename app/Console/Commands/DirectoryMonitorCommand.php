<?php

namespace App\Console\Commands;

use App\Business\CPayVender;
use App\Helpers\CPayFileHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Integracao\ControlPay;

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
    protected $signature = 'controlpay-service:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializa worker para monitorar diretório com arquivos para acionar o tef';

    /**
     * @var string
     */
    private $disk = 'local';

    /**
     * @var string
     */
    private $pathConfig = '/conf';

    /**
     * @var string
     */
    private $pathReq = '/req';

    /**
     * @var string
     */
    private $pathResp = '/resp';

    /**
     * @var string
     */
    private $pathError = '/error';

    /**
     * @var string
     */
    private $pathProccessed = '/proccessed';

    /**
     * @var array
     */
    private $cPayVender;

    /**
     * DirectoryMonitorCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $cPayclient = new ControlPay\Client([
            ControlPay\Constants\ControlPayParameter::CONTROLPAY_HOST => "http://pay2alldemo.azurewebsites.net/webapi/",
            ControlPay\Constants\ControlPayParameter::CONTROLPAY_TIMEOUT => 30,
            ControlPay\Constants\ControlPayParameter::CONTROLPAY_KEY => "hnNEn7Q7JapOgJ64qb6fzxex9IkIO%2bxLCGgiPXKjg8gkzR8rsrO9kVK%2fF2PeWNrN7fOOW9%2brCs48luNNPQT30qalcuCrqBP8A2kcgf1fIow%3d"
        ]);
        $this->cPayVender = new CPayVender($cPayclient);
    }

    private function loadConfig()
    {
        $files = Storage::disk($this->disk)->files($this->pathConfig);

        foreach ($files as $file)
        {

        }
    }

    /**
     *
     */
    public function handle()
    {
        /**
         * Adaptação para rodar command a cada 2 segundos
         */
        $inverval = 2;
        for($i = 0; $i < ceil(60/$inverval); $i++)
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
        Log::info("monitor start");
        $files = Storage::disk($this->disk)->files($this->pathReq);

        foreach ($files as $file)
        {
            $this->process($file);
        }
        Log::info("monitor end");
    }

    /**
     * @param $file
     */
    private function process($file)
    {
        printf("Arquivo: %s %s", basename($file), PHP_EOL);

        try{
            $data = $this->loadFileContent($file);

            if(!isset($data['api']))
                throw new \Exception("Função não encontrada!!");

            $responseContent = '';
            switch (strtolower($data['api']))
            {
                case CPayVender::VENDERAPI_VENDER:
                    $responseContent = $this->cPayVender->vender($data);
                    break;
                default:
                    throw new \Exception("Método {$data['api']} não implementado");
            }

            $this->fileProccessed($file, $responseContent);
        }catch (\Exception $ex){
            $this->fileProccessedError($ex, $file);
        }
    }

    /**
     * @param $file
     * @param $responseContent
     */
    private function fileProccessed($file, $responseContent)
    {
        try{
            $responseStatus = sprintf("response.status=%s%s", 0, PHP_EOL);
            $responseStatus .= sprintf("response.message=%s", "Dados processados com sucesso", PHP_EOL);

            Storage::disk($this->disk)->put(
                sprintf("%s/%s", $this->pathResp, basename($file)),
                $responseStatus . PHP_EOL . $responseContent
            );

            Storage::disk($this->disk)->move(
                $file,
                sprintf("%s/%s_%s", $this->pathProccessed, date('Y-m-d_His'), basename($file))
            );
        }catch (\Exception $ex){
            Log::error("Falha ao mover arquivos");
        }
    }

    /**
     * @param \Exception $ex
     * @param $file
     */
    private function fileProccessedError(\Exception $ex, $file)
    {
        try{
            $resposeContent = sprintf("response.status=%s%s", -1, PHP_EOL);
            $resposeContent .= sprintf("response.message=%s", "Falha ao processar arquivo", PHP_EOL);

            Storage::disk($this->disk)->put(
                sprintf("%s/%s", $this->pathResp, basename($file)),
                $resposeContent
            );

            Storage::disk($this->disk)->delete($file);

            Storage::disk($this->disk)->move(
                $file,
                sprintf("%s/%s_%s", $this->pathError, date('Y-m-d_His'),basename($file))
            );

        }catch (\Exception $ex){
            Log::error("Falha ao mover arquivos");
        }
    }

    /**
     * @param $file
     * @return array
     */
    private function loadFileContent($file)
    {
        $data = [];
        Log::info(sprintf('Processando arquivo: %s', basename($file)));
        try{

            $data = CPayFileHelper::convertFileContentToArray(
                Storage::disk($this->disk)->getAdapter()->getPathPrefix(),
                basename($file)
            );

            Log::info(sprintf('Processando e movido p/ %s/%s', $this->pathProccessed, basename($file)));
        }catch (\Exception $ex){
            Log::error(sprintf('Falha ao processar arquivo %s/%s', $this->pathReq, basename($file)));
        }

        return $data;
    }

}