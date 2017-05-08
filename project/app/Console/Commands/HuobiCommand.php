<?php

namespace App\Console\Commands;

use App\Events\LastPriceEvent;
use App\Model\Price;
use App\Module\Huobi;
use Illuminate\Console\Command;

class HuobiCommand extends Command
{
    private $typeMap = [
        'btc' => Huobi::BTC,
        'ltc' => Huobi::LTC
    ];

    private $typeFlag = 0;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'huobi:run {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $prevPrice = 0;

        $type = $this->argument('type');
        if (!in_array($type, ['btc', 'ltc'])) {
            throw new \Exception('invalid type');
        }
        $this->typeFlag = $this->typeMap[$type];
        
        $debug = env('APP_ENV') == 'local';

        while (1) {
            $lastPrice = Huobi::getLastPrice($this->typeFlag);

            if ($lastPrice == $prevPrice) {
                sleep(1);
                continue;
            }

            // Save To DB
            $price = Price::forceCreate([
                'price' => $lastPrice,
                'type' => $this->typeFlag,
                'range' => ($prevPrice == 0 ? 0 : $lastPrice - $prevPrice),
                'created_at' => time()
            ]);

            // Trigger LastPriceEvent Event
            event(new LastPriceEvent($price));

            if ($debug) {
                echo printf('%s %s', strtoupper($type), $lastPrice), PHP_EOL;
            }

            $prevPrice = $lastPrice;
            sleep(1);
        }
    }
}
