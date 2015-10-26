<?php

namespace T4webDomain;

use T4webDomainInterface\InvalidInputErrorInterface;

class InvalidInputError implements InvalidInputErrorInterface {

    /**
     * @var array
     */
    private $errors;

    public function __construct(array $errors) {
        $this->errors = $errors;
    }

    public function addErrors(array $errors)
    {
        $this->errors += $errors;
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function hasErrors($fieldName = '') {

        if (empty($fieldName)) {
            return !empty($this->errors);
        }

        if (!array_key_exists($fieldName, $this->errors)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $fieldName
     * @return array
     */
    public function getErrors($fieldName = null) {

        if (empty($fieldName)) {
            return $this->errors;
        }

        if (!array_key_exists($fieldName, $this->errors)) {
            return array();
        }

        return $this->errors[$fieldName];
    }

    /**
     * @return array
     */
    public function toArray() {
        return $this->getErrors();
    }

}