<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Coupon;
use App\Entity\Tax;
use Doctrine\ORM\EntityManagerInterface;

class CalculationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function calculatePrice($productId, $taxNumber, $couponCode = null)
    {
        $product = $this->entityManager->getRepository(Product::class)->getById($productId);

        $price = $product->getPrice();

        if ($couponCode) {
            $coupon = $this->entityManager->getRepository(Coupon::class)->getByCouponCode($couponCode);
            $price = $this->applyCoupon($price, $coupon);
        }

        $price = $this->applyTax($price, $taxNumber);

        return $price;
    }

    private function applyCoupon($price, $coupon)
    {
        if ($coupon) {
            switch ($coupon->getDiscountType()) {
                case Coupon::DISCOUNT_TYPE_FIXED:
                    $price = $price - $coupon->getDiscount();
                    break;
                case Coupon::DISCOUNT_TYPE_PERCENTAGE:
                    $price = $price - ($price * ($coupon->getDiscount() / 100));
                    break;
            }
        }

        if ($price < 0) {
            $price = 0;
        }

        return $price;
    }

    private function applyTax($price, $taxNumber)
    {
        $countryCode = substr($taxNumber, 0, 2);

        $taxFormat = substr($taxNumber, 2);
        $taxFormat = preg_replace('/[A-Z]/', 'Y', $taxFormat);
        $taxFormat = preg_replace('/\d/', 'X', $taxFormat);
        $taxFormat = $countryCode . $taxFormat;

        $tax = $this->entityManager->getRepository(Tax::class)->getByTaxFormat($taxFormat);

        $taxTotal = ($tax->getPercentage() / 100) * $price;

        return $price + $taxTotal;
    }
}
