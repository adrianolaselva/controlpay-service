# Modelo Integração com Paygo atravéz do ControlPay

##Configuração de cron

```shell
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```