<?php

namespace Core\JWT\Contracts\Http;

use Core\Http\Request;

interface Parser
{
    /**
     * Parse the request.
     *
     * @param  \Core\Http\Request  $request
     *
     * @return null|string
     */
    public function parse(Request $request);
}
