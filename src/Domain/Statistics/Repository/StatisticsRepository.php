<?php
namespace App\Domain\Statistics\Repository;

use App\Factory\QueryFactory;
use App\Database\Transaction;
use App\Moebius\Definition;

/**
 * Repository
 */
final class StatisticsRepository
{

    private QueryFactory $queryFactory;
    private Transaction $transaction;

    /**
     * Class Constructor
     * @param QueryFactory $queryFactory Query Factory
     * @param Transaction $transaction Database Transactions
     */
    public function __construct(QueryFactory $queryFactory, Transaction $transaction)
    {
        $this->queryFactory = $queryFactory;
        $this->transaction = $transaction;
    }

    /**
     * Return today's registered users
     * @return array|false
     */
    public function todaysUser()
    {
        return $this->queryFactory->rawQuery(
            "SELECT COUNT(*) AS total_user FROM users WHERE DATE(creation_date) = DATE(NOW()) AND account_role = " . Definition::USER
        );
    }

    /**
     * Return today's money
     * @return array|false
     */
    public function todaysMoney()
    {
        return $this->queryFactory->rawQuery(
            "SELECT SUM(recipt_amount) AS total_money FROM receipts WHERE DATE(recipt_date) = DATE(NOW())"
        );
    }

    /**
     * Return total registered user
     * @return array|false
     */
    public function totalUsers()
    {
        return $this->queryFactory->rawQuery(
            "SELECT COUNT(*) AS total_user FROM users WHERE account_role = " . Definition::USER
        );
    }

    /**
     * Count Total Sales
     * @return array|false
     */
    public function totalSales()
    {
        return $this->queryFactory->rawQuery(
            "SELECT COUNT(*) AS total_sale FROM receipts"
        );
    }

    /**
     * Return Latest 10 claims
     * @return array|false
     */
    public function latest10Claims()
    {
        return $this->queryFactory->newSelect('claims')
            ->innerJoin('users', 'users.user_id = claims.user_id')
            ->innerJoin('prizes_product', 'prizes_product.p_prod_id = claims.prize_id')
            ->select([
                'claims.claim_id',
                'users.f_name',
                'users.l_name',
                'claims.claim_date',
                'prizes_product.prize_name'
            ])
            ->orderDesc('claims.claim_id')
            ->limit(10)
            ->execute()
            ->fetchAll('assoc');
    }

    /**
     * Return latest 10 products
     * @return array|false
     */
    public function latest10Products()
    {
        return $this->queryFactory->newSelect('products')
            ->innerJoin('categories', 'categories.cat_id = products.cat_id')
            ->select([
                'products.prod_id',
                'products.prod_name',
                'products.prod_picture',
                'categories.cat_name',
                'products.prod_price',
                '(SELECT COUNT(*) FROM receipt_elements WHERE product_id = products.prod_id) AS total_sales'
            ])->orderDesc('products.prod_id')
            ->limit(10)
            ->execute()
            ->fetchAll('assoc');
    }
}
