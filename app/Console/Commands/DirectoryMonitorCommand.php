<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
    protected $signature = 'file-tef-worker:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializa worker para monitorar diretório com arquivos para acionar o tef';

    /**
     * @var string
     */
    private $pathReq = '';

    /**
     * @var string
     */
    private $pathResp = '';

    /**
     * @var string
     */
    private $pathError = '';

    /**
     * DirectoryMonitorCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pathReq = env('DIRECTORY_MONITOR_PATH_REQ');
        $this->pathResp = env('DIRECTORY_MONITOR_PATH_RESP');
        $this->pathError = env('DIRECTORY_MONITOR_PATH_ERROR');
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
        $files = Storage::disk('paygo')->files($this->pathReq);
        foreach ($files as $file)
        {
            Log::info(sprintf('Processando arquivo: %s', basename($file)));
            try{
                Storage::disk('paygo')->move($file, sprintf("%s/%s", $this->pathResp, basename($file)));
                Log::info(sprintf('Processando e movido p/ %s/%s', $this->pathResp, basename($file)));
            }catch (\Exception $ex){
                Log::error(sprintf('Falha ao processar arquivo %s/%s', $this->pathReq, basename($file)));
            }
        }
        Log::info("monitor end");
    }
}