<?php

namespace Core\JWT;


use Core\JWT\Exceptions\JWTException;
use Core\JWT\Exceptions\TokenBlacklistedException;
use Core\JWT\Token;
use Core\JWT\Factory;


class JWTManager {


    public function __construct()
    {
        $this->factory = new Factory();
    }

    /**
     * decode a token and return claims
     * @param \Core\JWT\Token
     * 
     */
public function decode(Token $token){

    // verify signature
    if($this->factory->verifySignature($token->get())){
        // in future we check for black listed tokens
        $payload = $this->factory->makePayloadToArray($token->get());

    }else {
        throw new JWTException('Invalid JWT signature');
    }

    return $payload;

}

}