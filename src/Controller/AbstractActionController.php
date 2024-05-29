<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractActionController
 */
class AbstractActionController extends AbstractController
{
    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(
        private readonly ValidatorInterface $validator
    ) {
    }

    /**
     * array $data
     * @return void
     */
    protected function validateRequest(
        array $data,
        Collection $constraints
    ): array {
        $violations = $this->validator->validate($data, $constraints);
        $errors = [];

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors[] = array(
                    trim($violation->getPropertyPath(), '[]') => $violation->getMessage()
                );
            }
        }

        return $errors;
    }
}
