<?php

namespace Gloomy\PagerBundle\RESTConnector;

use Symfony\Component\HttpFoundation\Response;

class RESTBase
{
    protected $_request = null;
    protected $_pager   = null;
    protected $_config  = array();

    public function __construct($request, $pager, array $config)
    {
        $defaultConfig  = array(
                'json_headers' => array(
                        'Access-Control-Allow-Origin' => '*',
                        'Content-type'                => 'application/json'
                        ),
                'jsonp_headers' => array(
                        'Content-type'                => 'application/javascript'
                )
        );

        $this->_request = $request;
        $this->_pager   = $pager;
        $this->_config  = array_merge($defaultConfig, $config);
    }

    protected function jsonResponse($response)
    {
        if ($callback = $this->_request->get('callback', false)) { // Assume the request wants JSONP.
            return new Response($callback.'('.json_encode($response).')', 200, $this->_config['jsonp_headers']);
        }

        return new Response(json_encode($response), 200, $this->_config['json_headers']);
    }
}