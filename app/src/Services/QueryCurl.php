<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\JsonResponse;

class QueryCurl
{

    /**
     * @var resource
     */
    protected $init;

    /**
     * QueryCurl constructor.
     */
    public function __construct()
    {
        $this->init = curl_init();
    }

    /**
     * @param string $url
     * @return mixed
     */
    public function getQuery(string $url)
    {

        /*On indique à curl quelle url on souhaite télécharger*/
        curl_setopt($this->init, CURLOPT_URL, $url);
        /*On indique à curl de nous retourner le contenu de la requête plutôt que de l'afficher*/
        curl_setopt($this->init, CURLOPT_RETURNTRANSFER, true);
        /*On indique à curl de ne pas retourner les headers http de la réponse dans la chaine de retour*/
        curl_setopt($this->init, CURLOPT_HEADER, false);
        /*On execute la requete*/
        $output = curl_exec($this->init);

        curl_close($this->init);

        return $output;
    }

    /**
     * @param string $url
     * @param array $datas
     * @return mixed
     */
    public function postQuery(string $url, array $datas)
    {
        curl_setopt($this->init, CURLOPT_URL, $url);
        curl_setopt($this->init, CURLOPT_POST, 1);
        curl_setopt($this->init, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->init, CURLOPT_POSTFIELDS, http_build_query($datas));
        $data = curl_exec($this->init);
        curl_close($this->init);

        return $data;
    }
}
