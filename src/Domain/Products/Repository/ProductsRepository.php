<?php
namespace App\Domain\Products\Repository;
use App\Factory\QueryFactory;
use App\Database\Transaction;
use App\Support\Hydrator;


final class ProductsRepository
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

    public function listProdByCategory(int $category_id){
        return $this->queryFactory->newSelect('products')
            ->select(['*'])
            ->where("cat_id = $category_id")
            ->execute()
            ->fetchAll('assoc') ?? [];
    }
    public function listLatestProducts(){
        return $this->queryFactory->newSelect('products')
            ->select(['*'])
            ->orderDesc('prod_id')
            ->limit(10)
            ->execute()
            ->fetchAll('assoc') ?? [];
    }

    public function productDetail(int $prod_id){
        return $this->queryFactory->newSelect('products')
            ->select(['*'])
            ->where("prod_id = $prod_id")
            ->execute()
            ->fetchAll('assoc') ?? [];
    }

    public function listAllProducts(){
        return $this->queryFactory->newSelect('products')
            ->innerJoin('categories', 'categories.cat_id = products.cat_id')
            ->select(['products.*', 'categories.cat_name'])
            ->execute()
            ->fetchAll('assoc');
    }

    public function updateProduct($product){
        $prod_id = $product['prod_id'];
        $cat_name = $product['cat_name'];
        unset($product['prod_id']);
        unset($product['cat_name']);

        if($this->queryFactory->newUpdate('products', $product)->where("prod_id = $prod_id")->execute()){
            $product['prod_id'] = $prod_id;
            $product['cat_name'] = $cat_name;
            return $product;
        }
        return [];
    }

    /**
     * Add new product
     * @param $product
     * @return array
     */
    public function addProduct($product){
        $product['cat_id'] = $product['cat_name'];
        $cat_name = $product['cat_name'];
        unset($product['prod_id']);
        unset($product['cat_name']);

        $last_id = $this->queryFactory->newInsert('products' , $product)
            ->execute()
            ->lastInsertId();

        if($last_id){
            $product['prod_id'] = $last_id;
            $product['cat_name'] = $cat_name;
            return $product;
        }

        return [];
    }

    public function deleteProduct($product){
        $this->queryFactory->newDelete('products')
            ->where(
                'prod_id = ' . $product['prod_id']
            )->execute();
        return [];
    }
}
