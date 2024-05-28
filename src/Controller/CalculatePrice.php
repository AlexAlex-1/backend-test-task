<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class CalculatePrice
 */
class CalculatePrice extends AbstractController
{
    /**
     * @return JsonResponse
     */
    #[Route('/calculate-price', name: 'app_calculate_price')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CalculatePriceController.php',
        ]);
    }
}
