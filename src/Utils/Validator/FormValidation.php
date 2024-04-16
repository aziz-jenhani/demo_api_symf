<?php

namespace App\Utils\Validator;

use App\Exception\ApiException;
use App\StatusCodes;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;

/**
 * The model for form validation.
 *
 * @author Fondative <devteam@fondative.com>
 */
class FormValidation implements ValidationInterface
{

    /**
     * Form property.
     *
     * @var FormInterface
     */
    private $form;

    /**
     * Validity check.
     *
     * @var bool
     */
    private $valid;

    /**
     * Form data.
     *
     * @var object
     */
    private $data;

    /**
     * Form errors.
     *
     * @var array
     */
    private $errors = [];

    /**
     * FormValidation constructor.
     *
     * @param FormInterface $form
     * @param bool $throwErrors
     * @throws ApiException
     */
    public function __construct(FormInterface $form, $throwErrors = true)
    {
        if (!$form->isSubmitted()) throw new LogicException();
        $this->form = $form;

        $this->valid = $this->form->isValid();
        $this->data = $this->form->getData();

        if (!$this->valid) $this->errors = $this->loadErrors($this->form);
        if ($throwErrors) $this->throwErrorsIfInvalid();
    }

    /**
     * Gets error messages from form.
     *
     * @param FormInterface $form
     * @return array|string
     */
    private function loadErrors(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            // Take one error per field
            $errors[] = $error->getMessage();
        }

        /** @var Form $child */
        foreach ($form->getIterator() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->loadErrors($child)[0];
            }
        }

        return $errors;
    }

    /**
     * Throws exception if form is invalid.
     *
     * @throws ApiException
     */
    public function throwErrorsIfInvalid()
    {
        if (!$this->isValid()) {
            throw new ApiException(StatusCodes::FAILED_DATA_VALIDATION, ['errors' => $this->errors]);
        }
    }

    /**
     * Returns true if data are valid, false else.
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Gets form.
     *
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Returns form data.
     *
     * @return object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns error messages.
     *
     * @return array of errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
