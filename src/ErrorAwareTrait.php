<?php

namespace T4webDomain;

trait ErrorAwareTrait {

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param array $errors
     */
    public function setErrors(array $errors) {
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function hasErrors() {
        return !empty($this->errors);
    }

}
