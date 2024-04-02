<?php

namespace App\Helper;

use App\Exception\ValidationException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait ValidatorTrait
{
    /** @var ValidatorInterface */
    protected ValidatorInterface $validator;

    #[Required]
    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    /**
     * @param mixed $value
     * @param Constraint|Constraint[]|null $constraints
     * @param string|GroupSequence|array<string|GroupSequence>|null $groups
     */
    private function validateOrFail(
        mixed $value,
        Constraint|array $constraints = null,
        string|GroupSequence|array $groups = null
    ): void {
        $violations = $this->validator->validate($value, $constraints, $groups);

        if (count($violations)) {
            throw new ValidationException($violations);
        }
    }
}
