<?php
namespace Core\JWT;

use Core\JWT\Factory;

class JWT {


    /**
     * JWT Factory
     * 
     * @return \Core\JWT\Factory
     */
    public $factory;


    public function __construct()
    {
        $this->factory =  new Factory();
        
    }

    /**
     * Create new Token
     */
    public function make(){

      return $this->factory->make();
      
    }

    public function withClaims(array $claims){
      
      if(empty($claims)) return false;
      
      return $this->factory->makeWithCustomClaims($claims);
      
    }

    /**
     * Verify Token
     * 
     */
    public function verify($token){

      $this->isValid($token);
    }

    /**
     * Check if token is valid
     */
    public function isValid($token){
      
      if(empty($token)){
        throw New EmptyTokenSuppliedException('No token was supplied');
      }

    }

        /**
     * Convenience method to get a claim value.
     *
     * @param  string  $claim
     *
     * @return mixed
     */
    public function getClaim($claim)
    {
        return $this->payload()->get($claim);
    }

    /**
     * Get the raw Payload instance.
     *
     * @return \Tymon\JWTAuth\Payload
     */
    public function getPayload()
    {
        $this->requireToken();

        return $this->manager->decode($this->token);
    }

    /**
     * Alias for getPayload().
     *
     * @return \Core\JWT\Payload
     */
    public function payload()
    {
        return $this->getPayload();
    }

    
    /**
     * Set the token.
     *
     * @param  \Core\JWT\Token|string  $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token instanceof Token ? $token : new Token($token);

        return $this;
    }

    /**
     * Unset the current token.
     *
     * @return $this
     */
    public function unsetToken()
    {
        $this->token = null;

        return $this;
    }

    /**
     * Ensure that a token is available.
     *
     * @throws \Core\JWT\Exceptions\JWTException
     *
     * @return void
     */
    protected function requireToken()
    {
        if (! $this->token) {
            throw new JWTException('A token is required');
        }
    }

}