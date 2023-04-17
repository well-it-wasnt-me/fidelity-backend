<?php
namespace App\Moebius;

use App\Factory\QueryFactory;

final class Push {
    private QueryFactory $queryFactory;
    var $payload;
    function __construct(QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->payload =  [
            'to' => '',
            'notifications' => [
                'title' => 'Titolo Notifica',
                'body'  => 'Body Richiesta',
                'mutable_content' => true,
                'sound' => 'Tri-tone'
            ],
            'data' => [
                'url' => 'https://app.mentalspace.care/app-assets/images/mental-space-logo/png/logo-no-background.png'
            ]
        ];

        return $this;
    }

    function sendNotification($title, $body, $to){
        $this->payload['notifications']['title'] = $title;
        $this->payload['notifications']['body'] = $body;
        $this->payload['to'] = $to;

        $curl = curl_init();

        $header = array();
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: key='.getenv('GOOGLE_FCM_KEY');
        curl_setopt($curl, CURLOPT_URL, getenv('GOOGLE_FCM_URL'));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->payload));
        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result, true);
    }

    public function sendToAll($title, $body){
        $users = $this->queryFactory->newSelect('notification_devices')
            ->distinct(['token'])
            ->execute()
            ->fetchAll('assoc');
        $result = [];
        foreach ($users AS $key=>$val){
            array_push($result, $this->sendNotification($title, $body, $val['token']));
        }

        return $result;
    }

    public function sendNotificationTo($title, $body, $uid){
        $users = $this->queryFactory->newSelect('notification_devices')
            ->distinct(['token'])
            ->where("uid = $uid")
            ->execute()
            ->fetchAll('assoc');
        $result = [];
        foreach ($users AS $key=>$val){
            array_push($result, $this->sendNotification($title, $body, $val['token']));
        }

        return $result;
    }
}