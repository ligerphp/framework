<?php
namespace Core\JWT\Claims;

use Core\JWT\Exceptions\InvalidClaimException;
use Core\JWT\Exceptions\TokenExpiredException;
use Core\JWT\Exceptions\TokenInvalidException;

class IssuedAt extends Claim
{
    use DatetimeTrait {
        validateCreate as commonValidateCreate;
    }

    /**
     * {@inheritdoc}
     */
    protected $name = 'iat';

    /**
     * {@inheritdoc}
     */
    public function validateCreate($value)
    {
        $this->commonValidateCreate($value);

        if ($this->isFuture($value)) {
            throw new InvalidClaimException($this);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function validatePayload()
    {
        if ($this->isFuture($this->getValue())) {
            throw new TokenInvalidException('Issued At (iat) timestamp cannot be in the future');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateRefresh($refreshTTL)
    {
        if ($this->isPast($this->getValue() + $refreshTTL * 60)) {
            throw new TokenExpiredException('Token has expired and can no longer be refreshed');
        }
    }
}
