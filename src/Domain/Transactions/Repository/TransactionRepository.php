<?php
namespace App\Domain\Transactions\Repository;

use App\Factory\QueryFactory;
use App\Database\Transaction;
use App\Support\Hydrator;
use Cake\Chronos\Chronos;
use Exception;

final class TransactionRepository
{

    private QueryFactory $queryFactory;
    private Transaction $transaction;
    private Hydrator $hydrator;

    /**
     * The constructor.
     *
     * @param QueryFactory $queryFactory The query factory
     */
    public function __construct(QueryFactory $queryFactory, Transaction $transaction, Hydrator $hydrator)
    {
        $this->queryFactory = $queryFactory;
        $this->transaction = $transaction;
        $this->hydrator = $hydrator;
    }

    /**
     * Return the list of what the user bought
     *
     * @param int $uid User ID
     * @param int $limit How many record show
     * @return array
     */
    public function history(int $uid, int $limit = 0): array
    {
        try {
            $history = $this->queryFactory->newSelect('receipts')
            ->select(['*'])
            ->where("user_id = $uid")
            ->limit($limit)
            ->orderDesc('rec_id')
            ->execute()
            ->fetchAll('assoc');

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }

        return ['status' => 'success', 'message' => __('Receipt List Received'), 'list' => $history ];
    }

    /**
     * Add a new transactions
     * @param int $user_id User ID
     * @param array $cart Cart
     * @param int $total_amount Total Amount to pay
     * @return int|string
     */
    public function addTransaction(int $user_id, array $cart, int $total_amount)
    {
        $receipt_id =$this->queryFactory->newInsert('receipts', [
            'recipt_date' => Chronos::now(),
            'recipt_status' => 'paid',
            'recipt_amount' => $total_amount,
            'user_id' => $user_id
        ])->execute()->lastInsertId();
        foreach ($cart as $key => $value) {
            $this->queryFactory->newInsert('receipt_elements', [
                'recipt_id' => $receipt_id,
                'product_id' => $value['prod_id'],
                'quantity' => $value['qt']
            ])->execute();
        }

        return $receipt_id;
    }

    /**
     * Return the elements of a transaction
     * @param int $trx_id Transaction ID
     * @param int $uid User ID
     * @return array|false
     */
    public function transactionDetail(int $trx_id, int $uid){
        $details = $this->queryFactory->newSelect('receipts')
            ->innerJoin('receipt_elements', 'receipt_elements.recipt_id = receipts.rec_id')
            ->innerJoin('products', 'products.prod_id = receipt_elements.product_id')
            ->select([
                'receipts.rec_id',
                'receipts.recipt_date',
                'receipts.recipt_status',
                'receipts.recipt_amount',
                'receipt_elements.quantity',
                'products.prod_name',
                'products.prod_descr',
                'products.prod_price',
                'products.prod_picture',
            ])
            ->where("receipts.rec_id = $trx_id AND receipts.user_id = $uid")
            ->execute()
            ->fetchAll('assoc');

        return $details;
    }
}
