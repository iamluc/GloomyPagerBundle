<?php

namespace Gloomy\PagerBundle\RESTConnector;

use Symfony\Component\HttpFoundation\Response;

class RESTBase
{
    protected $_request = null;

    public function __construct($request)
    {
        $this->_request = $request;
    }

    protected function response($response)
    {
        if ($callback = $this->_request->get('callback', false)) { // Assume the request wants JSONP. FIXME : better detection needed
            return new Response($callback.'('.json_encode($response).')');
        }

        return new Response(json_encode($response));
    }
}