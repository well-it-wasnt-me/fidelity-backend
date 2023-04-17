<?php

namespace App\Domain\Users\Repository;

use App\Domain\Users\Data\UserDataDoc;
use App\Factory\QueryFactory;
use App\Moebius\Definition;
use Cake\Chronos\Chronos;
use DomainException;

/**
 * Repository.
 */
final class UserRepository
{
    private QueryFactory $queryFactory;

    /**
     * The constructor.
     *
     * @param QueryFactory $queryFactory The query factory
     */
    public function __construct(QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
    }


    /**
     * Get user by id.
     *
     * @param int $userId The user id
     *
     * @return Array The user
     *@throws DomainException
     *
     */
    public function getUserById(int $userId): array
    {
        $query = $this->queryFactory->newSelect('patients');
        $query->select(
            [
                'users.user_id',
                'users.f_name',
                'users.l_name',
                'users.email',
                'users.account_status',
                'users.creation_date',
                'users.locale',
                'users.full_addr',
                'users.phone_number',
            ]
        );

        $query->andWhere(['users.user_id' => $userId]);

        $row = $query->execute()->fetch('assoc');

        if (!$row) {
            $row = [];
        }

        return $row;
    }

    /**
     * Check user id.
     *
     * @param int $userId The user id
     *
     * @return bool True if exists
     */
    public function existsUserId(int $userId): bool
    {
        $query = $this->queryFactory->newSelect('users');
        $query->select('id')->andWhere(['id' => $userId]);

        return (bool)$query->execute()->fetch('assoc');
    }

    /**
     * Delete user row.
     *
     * @param int $userId The user id
     *
     * @return void
     */
    public function deleteUserById(int $userId): void
    {
        $this->queryFactory->newDelete('users')
            ->andWhere(['user_id' => $userId])
            ->execute();
    }



    /**
     * Convert to array.
     *
     * @param UserDataDoc $user The user data
     *
     * @return array The array
     */
    private function toRow(UserDataDoc $user): array
    {
        return [
            'first_name' => $user->f_name,
            'last_name' => $user->l_name,
            'email' => $user->email,
            'password' => $user->passwd,
            'locale' => $user->locale,
            'user_type' => (int)$user->user_role,
        ];
    }

    /**
     * Return all access to the web site
     * @param $uid
     * @return array|false
     */
    public function historyAccess($uid)
    {
        return $this->queryFactory->newSelect('access_log')
            ->select(['ip', 'browser', 'os','location', 'ts'])
            ->where('user_id = ' . $uid)
            ->orderDesc('ts')
            ->execute()
            ->fetchAll('assoc');
    }

    /**
     * Update User Profile
     * @param $uid User ID
     * @param $payload Array field_name => value
     * @return array
     */
    public function updateProfile(int $uid, array $payload)
    {
        if (isset($payload['password'])) {
            $payload['password'] = hash('sha512', $payload['password']);
        }

        $update = $this->queryFactory->newUpdate('users', $payload)
            ->where("user_id = $uid")
            ->execute();
        if ($update) {
            return ['status' => 'success', 'message' => __("Profile Updated")];
        }

        return ['status' => 'error', 'message' => __("Problem during profile update")];
    }

    /**
     * List all users
     * @return array|false
     */
    public function ListUsers(){
        return $this->queryFactory->newSelect('users')
            ->select(['*'])
            ->execute()
            ->fetchAll('assoc');
    }

    public function addUser($data){
        $data['password'] = hash("sha512", $data['password']);
        $this->queryFactory->newInsert('users', $data);
        return $data;
    }

    public function updateUser($data){
        $id = $data['user_id'];
        unset($data['user_id']);

        if(strlen($data['password']) === 128){
            unset($data['password']);
        }

        $this->queryFactory->newUpdate('users', $data)
        ->where("user_id = $id")
        ->execute();
    }
}
