<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class QueryIpGeolocalisation
{

    /**
     * @var QueryCurl
     */
    protected $queryCurl;

    /**
     * QueryIpGeolocalisation constructor.
     * @param QueryCurl $queryCurl
     */
    public function __construct(QueryCurl $queryCurl)
    {
        $this->queryCurl = $queryCurl;
    }

    const LOCATOR_URI = "http://ip-api.com/json/";
    /**
     * @param string $ip
     * @return mixed
     */
    public function getIpLocator(string $ip)
    {
        $url = self::LOCATOR_URI.$ip;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output=curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true);
    }

    /**
     * @param $ipAddresse
     * @return array
     */
    public function userGeoLocator($ipAddresse): array
    {
        $geoplugin = $this->queryCurl->getQuery(self::LOCATOR_URI.$ipAddresse);

        if ($geoplugin['status'] == "success") {
            $city = $geoplugin['city'];
            $cp = $geoplugin['zip'];
            $country = $geoplugin['country'];
            $countryCode = $geoplugin['countryCode'];
            $regionName = $geoplugin['regionName'];
            $lat = $geoplugin['lat'];
            $long = $geoplugin['lon'];
        } else {
            $city = "-";
            $cp = "-";
            $country = "-";
            $countryCode = "-";
            $regionName = "-";
            $lat = "-";
            $long = "-";
        }

        return [
            'geodata_ip' => $ipAddresse,
            'geodata_lat' => $lat,
            'geodata_long' => $long,
            'geodata_city' => $city,
            'geodata_cp' => $cp,
            'geodata_country' => $country,
            'geodata_countryCode' => $countryCode,
            'geodata_regionName' => $regionName
        ];
    }
}
