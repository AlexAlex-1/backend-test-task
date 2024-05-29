<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getById($id): Product
    {
        $product = $this->findOneBy(['id' => $id]);
        
        if (!$product) {
            throw new \Exception(
                'Product with ID ' . $id . ' is not found.'
            );
        }

        return $product;
    }
}
