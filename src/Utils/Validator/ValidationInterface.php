<?php

namespace App\Utils\Validator;

use App\Exception\ApiException;

/**
 * Interface for the validation models.
 *
 * @author Fondative <devteam@fondative.com>
 */
Interface ValidationInterface
{
    /**
     * Checks if valid
     *
     * @return bool
     */
    public function isValid();

    /**
     * Gets data
     *
     * @return object|array
     */
    public function getData();

    /**
     * Gets errors
     *
     * @return array|string
     */
    public function getErrors();

    /**
     * Throws errors if invalid object
     *
     * @throws ApiException
     */
    public function throwErrorsIfInvalid();
}
