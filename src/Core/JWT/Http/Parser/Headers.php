<?php
namespace Core\JWT\Http\Parser;

use Core\Http\Request;
use Core\JWT\Contracts\Http\Parser as ParserContract;

class Headers implements ParserContract
{
    /**
     * The header name.
     *
     * @var string
     */
    protected $header = 'authorization';

    /**
     * The header prefix.
     *
     * @var string
     */
    protected $prefix = 'bearer';

    /**
     * Attempt to parse the token from some other possible headers.
     *
     * @param  \Core\Http\Request  $request
     *
     * @return null|string
     */
    protected function fromAltHeaders(Request $request)
    {
        return $request->server->get('HTTP_AUTHORIZATION') ?: $request->server->get('REDIRECT_HTTP_AUTHORIZATION');
    }

    /**
     * Try to parse the token from the request header.
     *
     * @param  \Core\Http\Request  $request
     *
     * @return null|string
     */
    public function parse(Request $request)
    {
        $header = $request->headers->get($this->header) ?: $this->fromAltHeaders($request);

        if ($header && preg_match('/'.$this->prefix.'\s*(\S+)\b/i', $header, $matches)) {
            return $matches[1];
        }
    }

    /**
     * Set the header name.
     *
     * @param  string  $headerName
     *
     * @return $this
     */
    public function setHeaderName($headerName)
    {
        $this->header = $headerName;

        return $this;
    }

    /**
     * Set the header prefix.
     *
     * @param  string  $headerPrefix
     *
     * @return $this
     */
    public function setHeaderPrefix($headerPrefix)
    {
        $this->prefix = $headerPrefix;

        return $this;
    }
}
