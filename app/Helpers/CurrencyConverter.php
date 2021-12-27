<?php

namespace App\Helpers;

use App\Models\Currency;
use Illuminate\Support\Carbon;

class CurrencyConverter
{
    public static function convert($value, $to, $from = null, $date = null)
    {
        $from = $from ?? config('app.currency');
        $date = $date ?: today()->format('Y-m-d');

        if (Carbon::parse($date)->lt(today())) {
            return self::historical($value, $from, $to, $date);
        }

        $currencies = Currency::whereIn('name', [$from, $to])->get();

        $from = $currencies->where('name', $from)->first()->value;
        $to = $currencies->where('name', $to)->first()->value;

        return self::calculate($value, $from, $to);
    }

    public static function historical($value, $from, $to, $date)
    {
        $file = storage_path('app/historical-currencies/' . $date . '.json');
        $currencies = file_get_contents($file);

        $currencies = json_decode($currencies);

        return self::calculate($value, $currencies->rates->{$from}, $currencies->rates->{$to});
    }

    //  1 EUR ($from)      2 EGP ($to)
    // --------------- = -------------------
    //  100 ($value)      $result => 200EGP
    public static function calculate($value, $from, $to, $decimals = 2)
    {
        $result = ($value * $to) / $from;

        return round($result, $decimals ?: 2);
    }
}
