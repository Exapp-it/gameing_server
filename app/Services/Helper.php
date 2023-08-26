<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

use App\Models\Rate;

class Helper {
    public static function preparePage($params)
    {
        $template = "<div>
            <script src=\"%sClientUtils.js\" type=\"text/javascript\"></script>
            <script type=\"text/javascript\">
            var params = %s;
            renderClient(params, \"gameClientPlaceholder\");
            </script>
        </div>";

        $base = array_filter($params, function($item) {
            return $item["Key"] === 'param:base';
        });
        $base = current($base)['Value'];

        $dict = "{";
        foreach ($params as $item) {
            $dict .= "\"".$item['Key']."\": \"".$item['Value']."\",\n";
        }
        $dict = rtrim($dict, ",\n").'}';

        return sprintf($template, $base, $dict);
    }

    public static function convertToCurrency($curr_from, $curr_to, $amount) {
        /*$api_key = 'rT45XfUIttrfDvhRCaqqGEroMFbtqRJf';
        $url = "https://api.apilayer.com/exchangerates_data/convert?to=$curr_to&from=$curr_from&amount=$amount";

        $request = Http::withHeaders([
            'apikey' => $api_key
        ])->acceptJson();

        $response = $request->get($url);
        
        if (!$response->successful()) {
            return -1;
        }

        return $response['result'];*/
        $rate_from = Rate::where("currency", "=", $curr_from)->first();
        $rate_to = Rate::where("currency", "=", $curr_to)->first();
        $res = $amount * $rate_from->rate / $rate_to->rate;
        return $res;
    }

    public static function getRate($curr)
    {
        $rates = [
            "RUB" => 1,
            "USD" => 80.55,
            "EUR" => 87.17,
            "KZT" => 0.18,
            "SOM" => 0.007
        ];
        return $rates[$curr];
    }
}
