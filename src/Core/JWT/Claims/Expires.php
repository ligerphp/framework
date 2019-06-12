<?php


namespace Core\JWT\Claims;

use Core\JWT\Exceptions\TokenExpiredException;

class Expires extends Claim
{
    use DatetimeTrait;

    /**
     * {@inheritdoc}
     */
    protected $name = 'exp';

    /**
     * {@inheritdoc}
     */
    public function validatePayload()
    {
        if ($this->isPast($this->getValue())) {
            throw new TokenExpiredException('Token has expired');
        }
    }
}
