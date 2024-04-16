<?php

namespace App\Utils\Validator;

use App\Exception\ApiException;
use App\Utils\Validator\EntityValidation;
use App\Utils\Validator\FormValidation;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Custom implementation for the validator.
 *
 * @author Fondative <devteam@fondative.com>
 */
class Validator
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Validator constructor.
     * @param FormFactoryInterface $formFactory
     * @param ValidatorInterface $validator
     */
    public function __construct(FormFactoryInterface $formFactory, ValidatorInterface $validator)
    {
        $this->formFactory = $formFactory;
        $this->validator = $validator;
    }

    /**
     * Validates entity with form type.
     *
     * @param $formType
     * @param $data
     * @param null $object
     * @param array $options
     * @param bool $throwErrors throw exception when the form is invalid
     * @return FormValidation
     * @throws ApiException
     */
    public function validateForm($formType, $data, $object = null, $options = [], $throwErrors = true)
    {
        $options['csrf_protection'] = false;
        $form = $this->formFactory->create($formType, $object, $options);
        $data = array_intersect_key($data, $form->all());
        $form->submit($data);

        // Validate the form data
        $errors = $this->validator->validate($object);

        return new FormValidation($form, $errors, $throwErrors);
    }

    /**
     * Checks property constraints
     *
     * @param $object
     * @param $property
     * @param array|null groups The validation groups to validate. If none is given, "Default" is assumed
     * @param bool $throwErrors throw exception when the entity is invalid
     * @return bool
     * @throws ApiException
     */
    public function validateProperty($object, $property, $groups = null, $throwErrors = true)
    {
        $entityValidation = $this->validateEntity($object, null, $groups, false);

        if ($throwErrors) {
            $entityValidation->throwErrorsIfPropertyInvalid($property);
        }

        return $entityValidation->isPropertyValid($property);
    }

    /**
     * Checks entity constraints
     *
     * @param $object
     * @param Constraint|Constraint[] $constraints The constraint(s) to validate against
     * @param array|null groups The validation groups to validate. If none is given, "Default" is assumed
     * @param bool $throwErrors throw exception when the entity is invalid
     * @return EntityValidation
     * @throws ApiException
     */
    public function validateEntity($object, $constraints = null, $groups = null, $throwErrors = true)
    {
        $constraintViolations = $this->validator->validate($object, $constraints, $groups);
        return new EntityValidation($object, $constraintViolations, $throwErrors);
    }

    /**
     * @param $objectOrClass
     * @param $propertyName
     * @param $value
     * @param null $groups
     */
    public function validatePropertyValue($objectOrClass, $propertyName, $value, $groups = null)
    {
        // TODO
    }
}
