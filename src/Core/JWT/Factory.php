<?php
namespace Core\JWT;

use Core\JWT\Payloads;
use Core\JWT\Claims;


class Factory {

   public  $defaultClaims = [
        'iss' => Claims\Issuer::class, //issuer
        'exp' => Claims\Expires::class, //expires
        'aud' => Claims\Audience::class, //audience
        'nbf' => Claims\NotBefore::class, 
        'iat' => Claims\IssuedAt::class, //issues at
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
    private $secret ;


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

        $this->secret = $_ENV['JWT_SECRET'];
        
        $signature = $this->signToken($_ENV['JWT_ALG'],$this->encodeToken($this->header,$this->payload,$this->secret));
       
       return $this->encode($this->header) . '.' . $this->encode($this->payload) . '.' . $signature;

    }


    public function makePayloadToArray($token){
        
        
        $flatToken = explode('.',$token);
        $payload = $flatToken[1];
        $claims = \base64_decode($payload);

        //check if token has expired
        if($this->hasValidClaims($token)){
            return array('claims' => json_decode($claims,true));
        }


    }

    /**
     * Check if a token is valid 
     * Loops through all default claims
     */
    public function hasValidClaims($token){
     
        $flatToken = explode('.',$token);
        $payload = $flatToken[1];
        $decodedPayload = base64_decode($payload);

        $claimsArray = $this->filterClaimsFromDecoded($decodedPayload);
        // dd($claimsArray);

        //validate claims
        foreach ($claimsArray as $key => $value) {
            $claimClassFromObj = $this->defaultClaims[$key];
            $claimClass =  (new $claimClassFromObj($value));
            if(method_exists($claimClass,'validatePayload')){
                $claimClass->validatePayload();
            }
        }
        return true;

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



    
    public function verifySignature($token){
       
        $flatToken = explode('.',$token);
        $payload = $flatToken[1];
        $headers = $flatToken[0];
        
        $old_signature = $flatToken[2];
        
        //try to make a clone of token to verify signature
        
        $signature = $this->signToken($_ENV['JWT_ALG'],$headers.$payload.$this->secret);

      return $signature === $old_signature;
        // return true;
    }

    /**
     * return an array of required claims eg iss
     */
    public function filterClaimsFromDecoded($payload){
              $dc_pl =    json_decode($payload,TRUE);
                $res = array();

        foreach ($dc_pl as $key => $val ) {
            if(array_key_exists($key,$this->defaultClaims)){
               $res[$key] = $val; 
            }
        }
        
        return $res;

    }
}