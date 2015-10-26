<?php

namespace T4webDomain;

trait ErrorAwareTrait {

    /**
     * @var InvalidInputError
     */
    private $errors;

    /**
     * @param array $errors
     */
    public function setErrors(array $errors) {
        $this->errors = new InvalidInputError($errors);
    }

    /**
     * @return InvalidInputError
     */
    public function getErrors() {
        if (!$this->errors) {
            $this->setErrors([]);
        }

        return $this->errors;
    }

    /**
     * @return bool
     */
    public function hasErrors() {
        return $this->errors->hasErrors();
    }

}