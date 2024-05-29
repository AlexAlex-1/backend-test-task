<?php

namespace App\Repository;

use App\Entity\Coupon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coupon>
 */
class CouponRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coupon::class);
    }

    public function getByCouponCode($code): Coupon
    {
        $coupon = $this->findOneBy(['code' => $code]);

        if (!$coupon) {
            throw new \Exception(
                'Coupon with code ' . $code . ' is not found.'
            );
        }

        return $coupon;
    }
}
