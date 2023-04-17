<?php
namespace App\Moebius;

use App\Factory\QueryFactory;
final class Payments {
    private QueryFactory $queryFactory;

    function __construct(QueryFactory $queryFactory){
        $this->queryFactory = $queryFactory;
    }

    /**
     * Controlla che un utente abbia o meno
     * una sottoscrizione
     * @return bool
     */
    public function hasActiveSubscription($uid): bool {
        if(!$uid){
            return false;
        }

        $result = $this->queryFactory->newSelect('payments')
            ->select(['payment_id', 'DATE(ts) AS ts'])
            ->where("user_id = " . $uid)
            ->orderDesc('payment_id')
            ->limit(1)
            ->execute()
            ->fetchAll('assoc');

        if(empty($result[0])){
            return false;
        }

        $now = time();
        $your_date = strtotime($result[0]['ts']);
        $datediff = $now - $your_date;

        if(round($datediff / (60 * 60 * 24)) >= 30) {
            return false;
        }

        return true;
    }

    public function addPayment($payment_intent, $raw_logs){
       return $this->queryFactory->newInsert('payments',[
            'user_id' => $payment_intent['client_reference_id'],
            'stripe_customer' => $payment_intent['customer'],
            'stripe_logs' => $raw_logs,
            'stripe_address' => $payment_intent['customer_details']['address']['line1'] . "," .$payment_intent['customer_details']['address']['city'] .",". $payment_intent['customer_details']['address']['postal_code'],
            'name_card' => $payment_intent['customer_details']['name'],
            'phone' => $payment_intent['customer_details']['phone'],
            'invoice' => $payment_intent['invoice'],
            'payment_status' => $payment_intent['payment_status'],
            'subscription' => $payment_intent['subscription'],
            'tax' => $payment_intent['total_details']['amount_tax'],
            'amount_received' => $payment_intent['amount_received']
        ])
            ->execute();
    }
}
