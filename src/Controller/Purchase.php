<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\CalculationService;
use App\Service\PaymentService;

/**
 * Class Purchase
 */
class Purchase extends AbstractActionController
{
    /**
     * @return JsonResponse
     */
    #[Route('/purchase', name: 'app_purchase')]
    public function index(
        Request $request,
        CalculationService $calculationService,
        PaymentService $paymentService
    ): JsonResponse {

        try {
            $data = $request->query->all() ?? [];

            $constraints = new Assert\Collection([
                'product' => array(new Assert\NotBlank(), new Assert\Regex([
                    'pattern' => '/^\d+$/',
                    'message' => 'This value must be integer.',
                ])),
                'taxNumber' => array(new Assert\NotNull(), new Assert\NotBlank(), new Assert\Type('string')),
                'couponCode' => array(new Assert\Optional([new Assert\Type('string')])),
                'paymentProcessor' => array(new Assert\NotBlank(), new Assert\Type('string'))
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

            $paymentStatus = $paymentService->processPayment(
                $data['paymentProcessor'],
                $price
            );

            return new JsonResponse(
                [
                    'status' => 'success',
                    'paymentStatus' => $paymentStatus,
                    'price' => $price
                ], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
