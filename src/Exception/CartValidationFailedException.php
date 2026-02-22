<?php

namespace Pantono\Cart\Exception;

class CartValidationFailedException extends AbstractCartException
{
    /**
     * @var array<int, array{'field': string, 'error': string}>
     */
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Cart validation failed');
    }

    /**
     * @return array<int, array{'field': string, 'error': string}>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
