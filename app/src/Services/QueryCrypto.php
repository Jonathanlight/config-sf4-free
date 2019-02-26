<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\JsonResponse;

class QueryCrypto
{
    public function getTickers($param)
    {
        $url = "https://api.kraken.com/0/public/Ticker?pair=".$param."eur";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //  curl_setopt($ch,CURLOPT_HEADER, false);
        $output=curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }
}
