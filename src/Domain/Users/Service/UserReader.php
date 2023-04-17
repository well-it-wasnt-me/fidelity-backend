<?php

namespace App\Domain\Users\Service;

use App\Domain\Users\Data\UserDataDoc;
use App\Domain\Users\Repository\UserRepository;

/**
 * Service.
 */
final class UserReader
{
    private UserRepository $repository;

    /**
     * The constructor.
     *
     * @param UserRepository $repository The repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Read a user.
     *
     * @param int $userId The user id
     *
     * @return UserDataDoc The user data
     */
    public function getUserData(int $userId): UserDataDoc
    {
        // Input validation
        // ...

        // Fetch data from the database
        $user = $this->repository->getUserById($userId);

        // Optional: Add or invoke your complex business logic here
        // ...

        // Optional: Map result
        // ...

        return $user;
    }

    public function trackUser(int $user_id, $coord, $addr){
        return $this->repository->addTracking($user_id, $coord, $addr);
    }
    public function trackUserList(int $user_id){
        return $this->repository->trackUserList($user_id);
    }

    public function getUserCalendar(int $user_id){
        return $this->repository->retrieveCalendar($user_id);
    }

    public function listHistoryAccess($uid){
        return $this->repository->historyAccess($uid);
    }
}