<?php
namespace Core\JWT\Exceptions;

use Exception;

class AuthCredentialsNotSetException extends Exception{

/**
     * {@inheritdoc}
     */
    protected $message = 'An error occurred';
}