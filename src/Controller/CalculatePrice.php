<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\CalculationService;

/**
 * Class CalculatePrice
 */
class CalculatePrice extends AbstractActionController
{
    /**
     * @return JsonResponse
     */
    #[Route('/calculate-price', name: 'app_calculate_price', methods: ['POST', 'GET'])]
    public function index(
        Request $request,
        CalculationService $calculationService
    ): JsonResponse {

        try {
            $data = $request->query->all() ?? [];

            $constraints = new Assert\Collection([
                'product' => array(new Assert\NotBlank(), new Assert\Regex([
                    'pattern' => '/^\d+$/',
                    'message' => 'This value must be integer.',
                ])),
                'taxNumber' => array(new Assert\NotNull(), new Assert\NotBlank(), new Assert\Type('string')),
                'couponCode' => array(new Assert\Optional([new Assert\Type('string')]))
            ]);

            $errors = $this->validateRequest($data, $constraints);

            if (count($errors) > 0) {
                return new JsonResponse(
                    ['errors' => $errors],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $price = $calculationService->calculatePrice(
                $data['product'],
                $data['taxNumber'],
                $data['couponCode'] ?? null
            );

            return new JsonResponse(['status' => 'success', 'price' => $price], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
