<?php

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * @var string
     */
    protected static $user;

    /**
     * TestCase constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->getInit();
    }


    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * Gera arquivo de configurações para ser usado nos testes
     *
     * @throws Exception
     */
    protected function getInit()
    {
        if(!env('TEST_CONTROLPAY_USER'))
            throw new Exception("Necessário configurar variáveis de ambiente para executar os testes");

        self::$user = env('TEST_CONTROLPAY_USER');

        $sdkFileConfig =  sprintf('%s=%s%s',
            \Integracao\ControlPay\Constants\ControlPayParameterConst::CONTROLPAY_HOST,
            env('TEST_CONTROLPAY_HOST'),
            PHP_EOL
        );
        $sdkFileConfig .=  sprintf('%s=%s%s',
            \Integracao\ControlPay\Constants\ControlPayParameterConst::CONTROLPAY_USER,
            env('TEST_CONTROLPAY_USER'),
            PHP_EOL
        );
        $sdkFileConfig .=  sprintf('%s=%s%s',
            \Integracao\ControlPay\Constants\ControlPayParameterConst::CONTROLPAY_PWD,
            env('TEST_CONTROLPAY_PWD'),
            PHP_EOL
        );
        $sdkFileConfig .=  sprintf('%s=%s%s',
            \Integracao\ControlPay\Constants\ControlPayParameterConst::CONTROLPAY_DEFAULT_TERMINAL_ID,
            env('TEST_CONTROLPAY_DEFAULT_TERMINAL_ID'),
            PHP_EOL
        );
        $sdkFileConfig .=  sprintf('%s=%s%s',
            \Integracao\ControlPay\Constants\ControlPayParameterConst::CONTROLPAY_DEFAULT_PRODUTO_ID,
            env('TEST_CONTROLPAY_DEFAULT_PRODUTO_ID'),
            PHP_EOL
        );
        $sdkFileConfig .=  sprintf('%s=%s%s',
            \Integracao\ControlPay\Constants\ControlPayParameterConst::CONTROLPAY_DEFAULT_FORMA_PAGAMENTO_ID,
            env('TEST_CONTROLPAY_DEFAULT_FORMA_PAGAMENTO_ID'),
            PHP_EOL
        );
        $sdkFileConfig .=  sprintf('%s=%s%s',
            \Integracao\ControlPay\Constants\ControlPayParameterConst::CONTROLPAY_DEFAULT_FORMA_AGUARDA_TEF,
            env('TEST_CONTROLPAY_DEFAULT_FORMA_AGUARDA_TEF'),
            PHP_EOL
        );

        $pathConf = sprintf("%s/test_%s", \App\Helpers\CPayFileHelper::PATH_CONFIG, env('TEST_CONTROLPAY_USER'));
        \Illuminate\Support\Facades\Storage::disk(env('STORAGE_CONFIG'))->put($pathConf, $sdkFileConfig);
    }
}
