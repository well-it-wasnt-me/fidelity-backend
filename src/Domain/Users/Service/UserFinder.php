<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Domain\Users\Service;

use App\Domain\Users\Data\UserDataDoc;
use App\Domain\Users\Repository\UserFinderRepository;

/**
 * Service.
 */
final class UserFinder
{
    private UserFinderRepository $repository;

    /**
     * The constructor.
     *
     * @param UserFinderRepository $repository The repository
     */
    public function __construct(UserFinderRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find users.
     *
     * @return UserDataDoc[] A list of users
     */
    public function findUsers(): array
    {
        // Input validation
        // ...

        return $this->repository->findUsers();
    }
}
