<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class CurrencyFetcher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch currencies convertion rates';

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
     * @return int
     */
    public function handle()
    {
        // TODO: add argument for fetching a specific date
        $currency = Currency::where('name', 'USD')->first();

        if ($currency && Carbon::parse($currency->date)->lt(now()->subDay())) {
            return $this->warn('currencies already fetched this day');
        }

        if (!(file_exists(storage_path('app/historical-currencies/')) && is_dir(storage_path('app/historical-currencies/')))) {
            mkdir(storage_path('app/historical-currencies/'), 0777);
        }

        $apiKey = config('services.exchangerates.api_key');
        $file = storage_path('app/historical-currencies/' . today()->format('Y-m-d') . '.json');

        if (file_exists($file)) {
            $data = file_get_contents($file);
            $data = json_decode($data, true);
        } else {
            //http://api.exchangeratesapi.io/v1/latest?access_key=8e09fd4ed9061cacea5edb1cc569ebbe&format=1
            $response = Http::acceptJson()->get('http://api.exchangeratesapi.io/v1/latest', [
                'access_key' => $apiKey,
                //'base' => 'USD',
                'format' => '1',
            ]);

            $data = $response->json();
        }

        $rates = collect($data['rates']);

        if (!file_exists($file)) {
            file_put_contents($file, json_encode($data));
        }

        $rates->keys()->map(function ($currency) use ($data, $rates) {
            Currency::updateOrCreate([
                'name' => $currency,
                'base' => $data['base'],
            ], [
                'date' => $data['date'],
                'value' => $rates[$currency],
            ]);
        });

        $this->info('[currencies] ' . count($rates) . ' fetched');
    }
}
