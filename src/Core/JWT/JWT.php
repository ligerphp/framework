<?php
namespace Core\JWT;

use Core\JWT\Factory;
use Core\JWT\Http\Parser\Parser;
use Core\JWT\Exceptions\JWTException;
use Core\JWT\JWTManager;

class JWT extends Factory{

    private $parsers = [
      'headers' => Core\JWT\Http\Parser\Headers::class
    ];


    /**
     * @param \Core\JWT\Http\Parser\Parser
     * 
     * @return void
     */
    public function __construct(Parser $parser)
    {
      $this->parser = $parser;
      $this->manager =   new JWTManager();

    }
    /**
     * JWT Factory
     * 
     * @return \Core\JWT\Factory
     */
    public $factory;


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
     * Parse the token from the request.
     *
     * @throws \Core\JWT\Exceptions\JWTException
     *
     * @return $this
     */
    public function parseToken()
    {

        if (! $token = $this->parser->parseToken()) {
            throw new JWTException('The token could not be parsed from the request');
        }

        return $this->setToken($token);
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
     * Verify Token
     * 
     */
    public function verifyToken($token){

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