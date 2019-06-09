<?php
namespace Core\JWT;

use Core\JWT\Payloads;

class Factory {
       

   public  $claims = [
        'iss', //issuer
        'exp', //expires
        'aud', //audience
        'nbf', // not before
        'iat', //issues at
    ];

    /**
     * Jwt headers
     * 
     */
    private $header;


    /**
     * 
     * jwt token secret
     * 
     */
    private $secret;


    private $usingCustomClaims = false;


    private $payload;


    public function makeHeaders(){

        $this->header = json_encode(['alg' => $_ENV['JWT_ALG'],'typ' => 'JWT']);
    }

    /**
     * 
     * Make a new JWT Token
     * 
     * @return string
     */

    public function make(){
        
        $this->makeHeaders();

        $this->setDefaultClaims();

        
        $signature = $this->signToken($_ENV['JWT_ALG'],$this->encodeToken($this->header,$this->payload,$this->secret));
       
       return $this->encode($this->header) . '.' . $this->encode($this->payload) . '.' . $signature;

    }


    public function encode($data){
        $encoded =  base64_encode($data);

        return $encoded;

    }

    public function encodeToken($header,$payload,$secret){
        
        $_header =  base64_encode($header);
        $_payload =  base64_encode($payload);
        $_secret =  base64_encode($secret);
        
        return $_header.$_payload.$_secret;
    }

    /**
     * Sign a Token by hashing 
     * 
     * @return string
     * 
     * @param $algorithm, "sha256,md5"
     * 
     */
    public function signToken($algorithm,$data){

        return hash($algorithm,$data);
    }

    /**
     * Make Token with custom claims
     * 
     * @return Core\JWT\Payload
     * 
     * @param $claims Array required
     */
    public function makeWithCustomClaims($claims){

        $this->usingCustomClaims = true;
        
        $this->setClaim($claims);

        return $this->make();

    }


    

    /**
     * 
     * Get default Claims from env eg 'iss'
     * 
     */
    public function getInitClaims(){

      return $this->payload;
           
    }


    public function setDefaultClaims(){
        if($this->usingCustomClaims) return;
        $exp =  time() + $_ENV['JWT_EXPIRES'];
        $issuer = $_ENV['JWT_ISS'];
        $audience = $_ENV['JWT_AUD'];
        $this->payload = json_encode(['iss' => $issuer,'exp' => $exp,'aud' => $audience],JSON_FORCE_OBJECT);
    
        
    }

    /**
     * 
     * Set claims for token 
     */
    
    public function setClaim(array $claim){
      
        // if using custom claims add them to the initial payload 
        $exp =  time() + $_ENV['JWT_EXPIRES'];
        $issuer = $_ENV['JWT_ISS'];
        $audience = $_ENV['JWT_AUD'];
      
      
        if($this->usingCustomClaims){
        $payload = json_encode(['iss' => $issuer,'exp' => $exp,'aud' => $audience],JSON_FORCE_OBJECT);

        $decodedClaims = json_decode($payload,true);

        $newClaim = array_merge($decodedClaims,$claim);

        $this->payload = json_encode($newClaim);
       }




    }

    /**
     * Decode a tokens payload
     */
    public function decode($token){

        $this->verifySignature($token);
    }

    public function verifySignature($token){
       
        $flattendArray =  explode('.',$token);

    }
}