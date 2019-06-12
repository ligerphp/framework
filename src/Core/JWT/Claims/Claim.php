<?php


namespace Core\JWT\Claims;

use JsonSerializable;
use Core\JWT\Contracts\Claim as ClaimContract;

abstract class Claim implements  ClaimContract, JsonSerializable
{
    /**
     * The claim name.
     *
     * @var string
     */
    protected $name;

    /**
     * The claim value.
     *
     * @var mixed
     */
    private $value;

    /**
     * @param  mixed  $value
     *
     * @return void
     */
    public function __construct($value)
    {
        $this->setValue($value);
    }

    /**
     * Set the claim value, and call a validate method.
     *
     * @param  mixed  $value
     *
     * @throws \Core\JWTExceptions\InvalidClaimException
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $this->validateCreate($value);

        return $this;
    }

    /**
     * Get the claim value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the claim name.
     *
     * @param  string  $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the claim name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Validate the claim in a standalone Claim context.
     *
     * @param  mixed  $value
     *
     * @return bool
     */
    public function validateCreate($value)
    {
        return $value;
    }

    /**
     * Validate the Claim within a Payload context.
     *
     * @return bool
     */
    public function validatePayload()
    {
        return $this->getValue();
    }

    /**
     * Validate the Claim within a refresh context.
     *
     * @param  int  $refreshTTL
     *
     * @return bool
     */
    public function validateRefresh($refreshTTL)
    {
        return $this->getValue();
    }

    /**
     * Checks if the value matches the claim.
     *
     * @param  mixed  $value
     * @param  bool  $strict
     *
     * @return bool
     */
    public function matches($value, $strict = true)
    {
        return $strict ? $this->value === $value : $this->value == $value;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Build a key value array comprising of the claim name and value.
     *
     * @return array
     */
    public function toArray()
    {
        return [$this->getName() => $this->getValue()];
    }

    /**
     * Get the claim as JSON.
     *
     * @param  int  $options
     *
     * @return string
     */
    public function toJson($options = JSON_UNESCAPED_SLASHES)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Get the payload as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
