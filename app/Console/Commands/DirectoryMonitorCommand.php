<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
     * DirectoryMonitorCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function handle()
    {
        printf("monitor start" . PHP_EOL);
        while(true)
        {
            $files = Storage::disk('local')->files('tef');
            foreach ($files as $file)
            {
                printf('Processando arquivo: %s %s', $file, PHP_EOL);
                Storage::disk('local')->move($file, sprintf("processado/%s", $file));
                printf('Processando e movido p/ %s %s %s', sprintf("processado/%s", $file), PHP_EOL, PHP_EOL);
                sleep(1.5);
            }
        }
    }
}