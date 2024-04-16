<?php

namespace App\Utils\Validator;

use App\Exception\ApiException;
use App\StatusCodes;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * The model for entity validation.
 *
 * @author Fondative <devteam@fondative.com>
 */
class EntityValidation implements ValidationInterface
{
    /**
     * Object to validate.
     *
     * @var mixed
     */
    private $object;

    /**
     * List of constraints.
     *
     * @var ConstraintViolationListInterface
     */
    private $constraintViolationList;

    /**
     * @var bool
     */
    private $isValid;

    /**
     * @var array
     */
    private $errors;

    /**
     * EntityValidation constructor.
     *
     * @param $object
     * @param ConstraintViolationListInterface $constraintViolationList
     * @param bool $throwErrors
     * @throws ApiException
     */
    public function __construct($object, ConstraintViolationListInterface $constraintViolationList, $throwErrors = true)
    {
        $this->object = $object;
        $this->constraintViolationList = $constraintViolationList;
        $this->isValid = $this->constraintViolationList->count() === 0;
        if ($this->constraintViolationList->count()) {
            foreach ($this->constraintViolationList as $constraintViolation) {
                $this->errors[$constraintViolation->getPropertyPath()] = $constraintViolation->getMessage();
            }
        }

        if ($throwErrors) $this->throwErrorsIfInvalid();
    }

    /**
     * Throws exception if entity is invalid.
     *
     * @return void
     * @throws ApiException
     */
    public function throwErrorsIfInvalid()
    {
        if (!$this->isValid) {
            throw new ApiException(StatusCodes::FAILED_DATA_VALIDATION, ['errors' => $this->errors]);
        }
    }

    /**
     * Checks if object is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * Gets validated data.
     *
     * @param bool $check
     * @return mixed
     */
    public function getData($check = true)
    {
        if ($check) $this->throwErrorsIfInvalid();

        return $this->object;
    }

    /**
     * Gets error messages.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Throws exception if property is invalid.
     *
     * @param $property
     * @throws ApiException
     */
    public function throwErrorsIfPropertyInvalid($property)
    {
        if (!$this->isPropertyValid($property)) {
            throw new ApiException(StatusCodes::FAILED_DATA_VALIDATION, ['errors' => $this->getPropertyErrors($property)]);
        }
    }

    /**
     * Checks if property is valid.
     *
     * @param $property
     * @return bool
     */
    public function isPropertyValid($property)
    {
        return !isset($this->errors[$property]);
    }

    /**
     * Gets property error messages.
     *
     * @param $property
     * @return array|null
     */
    public function getPropertyErrors($property)
    {
        if (isset($this->errors[$property])) {
            return [$property => $this->errors[$property]];
        }

        return null;
    }
}
