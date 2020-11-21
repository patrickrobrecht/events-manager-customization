<?php

namespace EventsManagerCustomization;

class Field
{
    private $key;
    private $label;
    private $inputType;
    private $errorMessageIfMissing;

    public function __construct(string $key, string $label, string $inputType, string $errorMessageIfMissing)
    {
        $this->key = $key;
        $this->label = $label;
        $this->inputType = $inputType;
        $this->errorMessageIfMissing = $errorMessageIfMissing;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getInputType(): string
    {
        return $this->inputType;
    }

    public function getErrorMessageIfMissing(): string
    {
        return $this->errorMessageIfMissing;
    }

    public function getValue(): string
    {
        return !empty($_REQUEST[$this->getKey()]) ? esc_attr($_REQUEST[$this->getKey()]) : '';
    }
}
