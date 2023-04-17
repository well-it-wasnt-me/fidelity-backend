<?php
namespace App\Domain\Categories\Repository;

use App\Factory\QueryFactory;
use App\Database\Transaction;
use App\Support\Hydrator;

final class CategoriesRepository
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

    public function listAllCategories()
    {
        return $this->queryFactory->newSelect('categories')
            ->select(['*'])
            ->execute()
            ->fetchAll('assoc') ?? [];
    }

    public function selectListCategories()
    {
        return $this->queryFactory->newSelect('categories')
            ->select(['cat_id AS id', 'cat_name AS text'])
            ->execute()
            ->fetchAll('assoc') ?? [];
    }

    /**
     * Add new Category
     * @param $data
     * @return array|mixed
     */
    public function addCategory($data)
    {
        $id = $data['cat_id'];
        unset($data['cat_id']);
        $last_id = $this->queryFactory->newInsert('categories', $data)
        ->execute()->lastInsertId();

        if ($last_id) {
            $data['cat_id'] = $last_id;
            return $data;
        } else {
            return [];
        }
    }

    /**
     * Update Category
     * @param $data
     * @return array
     */
    public function updateCategory($data)
    {
        $id = $data['cat_id'];
        unset($data['cat_id']);
        if ($this->queryFactory->newUpdate('categories', $data)
            ->where("cat_id = $id")
        ->execute()) {
            $data['cat_id'] = $id;
            return $data;
        } else {
            return [];
        }
    }

    public function deleteCategory($data){
        return $this->queryFactory->newDelete('categories')->where("cat_id = " . $data['cat_id'])->execute();
    }
}
