<?php

namespace Gloomy\PagerBundle\Service;

use Gloomy\PagerBundle\Crud\Crud;

class CrudService {

    private $_request;

    private $_router;

    private $_doctrine;

    private $_templating;

    private $_translator;

    private $_session;

    private $_datagrid;

    private $_form;

    public function __construct($request, $router, $doctrine, $templating, $translator, $session, $datagrid, $form)
    {
        $this->_request    = $request;
        $this->_router     = $router;
        $this->_doctrine   = $doctrine;
        $this->_templating = $templating;
        $this->_translator = $translator;
        $this->_session    = $session;
        $this->_datagrid   = $datagrid;
        $this->_form       = $form;
    }

    public function factory($entity, $entityType = null, $config = array())
    {
        return new Crud(
                $this->_request, $this->_router, $this->_doctrine, $this->_templating, $this->_translator, $this->_session,
                $this->_datagrid, $this->_form, $entity, $entityType, $config
        );
    }
}
