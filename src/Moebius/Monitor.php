<?php

namespace App\Moebius;

use App\Factory\QueryFactory;

/**
 * Class Definition
 * @package App\Moebius
 */
final class Monitor
{
    private QueryFactory $queryFactory;
    function __construct(QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
    }

    /**
     * Log user access into db
     * @param string $ip IP Address
     * @param string $ua User Agent
     * @param int $uid User ID
     * @return \Cake\Database\StatementInterface
     */
    public function logLogin(string $ip, string $ua, int $uid)
    {
        $os = explode(";", $ua)[1];
        $array = explode(" ", $ua);
        $browser = end($array);
        $location = $this->retrievePosition($ip);

        $loc = $location->geoplugin_continentName . ", " . $location->geoplugin_countryName. ", " . $location->geoplugin_countryCode.", " . $location->geoplugin_city;


        return $this->queryFactory->newInsert('access_log', [
            'user_id' => $uid,
            'ip' => $ip,
            'browser' => $browser,
            'os' => $os,
            'location' => $loc
        ])->execute();
    }

    /**
     * Locate IP Address via geoplugin.net
     * @param string $ip User IP Address
     * @return mixed
     */
    public function retrievePosition($ip)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.geoplugin.net/json.gp?ip=".$ip);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        $params = json_decode($result);
        return $params;
    }
}
