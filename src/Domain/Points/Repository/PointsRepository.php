<?php
namespace App\Domain\Points\Repository;

use App\Factory\QueryFactory;
use App\Database\Transaction;
use App\Support\Hydrator;

final class PointsRepository
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
     * Return how much points (the sum) the user has.
     *
     * @param int $uid User ID
     * @return array
     */
    public function singlePoints(int $uid): array
    {
        try {
            $points = $this->queryFactory->rawQuery(
                "SELECT IFNULL(SUM(amount_point),0) AS tot_point FROM points WHERE user_id = $uid"
            );
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }

        return ['status' => 'success', 'message' => __('Points Retrieved'), 'total_points' => $points[0]['tot_point'] ];
    }

    /**
     * Return the list of the Points the user has
     * @param int $uid User ID
     * @return array
     */
    public function listPoints(int $uid): array
    {
        $data = $this->queryFactory->newSelect('points')
            ->select(['*'])
            ->where('user_id = ' . $uid)
            ->orderDesc('date_assignation')->execute()->fetchAll('assoc');

        return ['status' => 'success', 'message' => __('Points Retrieved'), "list" => $data];
    }

    /**
     * @param int $user_id The user id
     * @param int $amount The amount spent
     * @param string $reason Reason why we are adding points
     * @return \Cake\Database\StatementInterface
     */
    public function addPoints(int $user_id, int $amount, string $reason)
    {
        if ($amount === 0) {
            return;
        }

        $conversion = $this->queryFactory->newSelect('settings')
            ->select(['money_to_point'])
            ->limit(1)
            ->orderDesc('sett_id')
            ->execute()
            ->fetchAll('assoc');

        $addPoints = $this->queryFactory->newInsert('points', [
            'amount_point' => ($amount / 100) * $conversion[0]['money_to_point'],
            'reason'       => $reason,
            'user_id'      => $user_id
        ])->execute();

        return $addPoints;
    }

    /**
     * Check the given token into the table qrcode
     * return TRUE if token present and active
     * FALSE otherwise
     * @param string $token Token received from app
     * @return bool
     */
    public function checkToken(string $token): bool
    {
        $qr = $this->queryFactory->newSelect('qr_codes')
            ->select(['*'])
            ->where("token = $token AND is_active = 1")
            ->execute()
            ->fetchAll('assoc');

        if (empty($qr) || $qr === false) {
            return false;
        }

        return true;
    }
}
