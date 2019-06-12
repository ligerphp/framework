<?php

namespace Core\JWT;

class Payloads {


    /**
     * Build the Payload.
     *
     *
     * @return void
     */
    public function __construct( $claims){

        $this->claims = $claims;
    
    }



    public function generateWithDefault(){

    }


    /**
     * Checks if a payload matches some expected values.
     *
     * @param  array  $values
     * @param  bool  $strict
     *
     * @return bool
     */
    public function matches(array $values, $strict = false)
    {
        if (empty($values)) {
            return false;
        }

        $claims = $this->getClaims();

        foreach ($values as $key => $value) {
            if (! $claims->has($key) || ! $claims->get($key)->matches($value, $strict)) {
                return false;
            }
        }

        return true;
    }


    /**
     * Get the array of claim instances.
     * 
     */
    public function getClaims()
    {
        return $this->claims;
    }

}