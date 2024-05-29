<?php

namespace App\Repository;

use App\Entity\Tax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tax>
 */
class TaxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tax::class);
    }

    public function getByTaxFormat($taxFormat): Tax
    {
        $tax = $this->findOneBy(['format' => $taxFormat]);
        
        if (!$tax) {
            throw new \Exception(
                'Tax format is incorrect.'
            );
        }

        return $tax;
    }
}
