<?php
namespace App\Domain\Prizes\Repository;

use App\Domain\Points\Repository\PointsRepository;
use App\Factory\QueryFactory;
use App\Database\Transaction;
use App\Support\Hydrator;

final class PrizesRepository
{

    private QueryFactory $queryFactory;
    private Transaction $transaction;
    private Hydrator $hydrator;
    private PointsRepository $pointsRepository;

    /**
     * The constructor.
     *
     * @param QueryFactory $queryFactory The query factory
     */
    public function __construct(QueryFactory $queryFactory, Transaction $transaction, Hydrator $hydrator, PointsRepository $pointsRepository)
    {
        $this->queryFactory = $queryFactory;
        $this->transaction = $transaction;
        $this->hydrator = $hydrator;
        $this->pointsRepository = $pointsRepository;
    }

    /**
     * Return all the prizes categories
     * @return array|false
     */
    public function listCategories()
    {
        return $this->queryFactory->newSelect('prizes_categories')
            ->select(['*'])
            ->where('is_active = 1')
            ->execute()
            ->fetchAll('assoc');
    }

    /**
     * Return latest 10 inserted prizes
     * @return array|false
     */
    public function latestProducts()
    {
        return $this->queryFactory->newSelect('prizes_product')
            ->select(['*'])
            ->where('is_active = 1')
            ->limit(10)
            ->execute()
            ->fetchAll('assoc');
    }

    /**
     * Return detail of a prize
     * @param int $prize_id The Prize ID
     * @return array|false
     */
    public function prizeDetail(int $prize_id)
    {
        return $this->queryFactory->newSelect('prizes_product')
            ->select(['*'])
            ->where("p_prod_id = $prize_id")
            ->execute()
            ->fetchAll('assoc');
    }

    /**
     * @param int $user_id User ID
     * @param int $prize_id Prize ID
     * @return array
     */
    public function claimPrize(int $user_id, int $prize_id)
    {
        $availablePoints = $this->pointsRepository->singlePoints($user_id);
        $prizePoints = $this->prizeDetail($prize_id);

        if ($availablePoints['total_points'] < $prizePoints[0]['prize_points']) {
            return ['status' => 'error', "message" => __("Not enough points")];
        }

        if ($this->queryFactory->newInsert('points', [
            'user_id' => $user_id,
            'amount_point' => -$prizePoints[0]['prize_points'],
            'reason' => __("Claiming Prize")
        ])->execute()) {
            $this->queryFactory->newInsert('claims', [
                'user_id' => $user_id,
                'prize_id' => $prize_id
            ])->execute();

            return ['status' => 'success', 'message' => __("Claimed ! In the next 24/48 hours you will be contacted")];
        }

        return ['status' => 'error', 'message' => __("Sorry, something went wrong")];
    }

    /**
     * Return all the collected prizes
     * @param int $user_id The User ID
     * @return array|false
     */
    public function claimHistory(int $user_id)
    {
        return $this->queryFactory->newSelect('claims')
            ->innerJoin('prizes_product', "prizes_product.p_prod_id = claims.prize_id")
            ->select(
                [
                    'claims.*',
                    'prizes_product.prize_name'
                ]
            )
            ->where("claims.user_id = $user_id")
            ->orderDesc('claims.claim_date')
            ->execute()
            ->fetchAll('assoc');
    }

    public function claimDetail(int $user_id, int $claim_id)
    {
        return $this->queryFactory->newSelect('claims')
            ->innerJoin('prizes_product', "prizes_product.p_prod_id = claims.prize_id")
            ->select(
                [
                    'claims.*',
                    'prizes_product.prize_name',
                    'prizes_product.prize_descr',
                    'prizes_product.prize_picture',
                    'prizes_product.prize_points',
                ]
            )
            ->where("claims.user_id = $user_id AND claims.claim_id = $claim_id")
            ->execute()
            ->fetchAll('assoc');
    }

    /**
     * List all claims
     * @return array
     */
    public function claimList()
    {
        return $this->queryFactory->newSelect('claims')
            ->innerJoin('users', 'users.user_id = claims.user_id')
            ->innerJoin('prizes_product', 'prizes_product.p_prod_id = claims.prize_id')
            ->select([
                'claims.claim_id',
                'claims.claim_date',
                'CONCAT(users.f_name, " ", users.l_name) AS user_full_name',
                'prizes_product.prize_name',
                'claims.is_delivered',
            ])
            ->execute()
            ->fetchAll('assoc');
    }

    public function updateClaim($data){
        $this->queryFactory->newUpdate('claims' , [
            'is_delivered' => $data['is_delivered']
        ])->where("claim_id = " . $data['claim_id'])
            ->execute();
        return $data;
    }
}
