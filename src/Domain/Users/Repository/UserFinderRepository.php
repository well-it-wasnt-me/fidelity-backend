<?php

namespace App\Domain\Users\Repository;

use App\Domain\Users\Data\UserDataDoc;
use App\Factory\QueryFactory;
use App\Support\Hydrator;

/**
 * Repository.
 */
final class UserFinderRepository
{
    private QueryFactory $queryFactory;

    private Hydrator $hydrator;

    /**
     * The constructor.
     *
     * @param QueryFactory $queryFactory The query factory
     * @param Hydrator $hydrator The hydrator
     */
    public function __construct(QueryFactory $queryFactory, Hydrator $hydrator)
    {
        $this->queryFactory = $queryFactory;
        $this->hydrator = $hydrator;
    }

    /**
     * Find users.
     *
     * @return UserDataDoc[] A list of users
     */
    public function findUsers(): array
    {
        $query = $this->queryFactory->newSelect('users');

        $query->select(
            [
                'user_id',
                'f_name',
                'l_name',
                'dob',
                'email',
                'user_role',
                'account_status',
                'locale',
                'created_at',
                'updated_at',
            ]
        );

        // Add more "use case specific" conditions to the query
        // ...

        $rows = $query->execute()->fetchAll('assoc') ?: [];

        // Convert to list of objects
        return $this->hydrator->hydrate($rows, UserDataDoc::class);
    }
}
