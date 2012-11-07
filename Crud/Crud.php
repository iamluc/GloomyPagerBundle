<?php

namespace Gloomy\PagerBundle\Crud;

use Symfony\Component\HttpFoundation\Response;

use Gloomy\PagerBundle\DataGrid\DataGrid;
use Gloomy\PagerBundle\DataGrid\Action;
use Gloomy\PagerBundle\DataGrid\Notification;

class Crud
{
    protected $_request;

    protected $_router;

    protected $_doctrine;

    protected $_templating;

    protected $_translator;

    protected $_session;

    protected $_datagridService;

    protected $_form;

    protected $_entity;

    protected $_entityType;

    protected $_config;

    protected $_datagrid;

    protected $_formDatas;

    protected $_route;

    protected $_title;

    public function __construct(
            $request, $router, $doctrine, $templating, $translator, $session,
            $datagridService, $form, $entity, $entityType = null, $config = array())
    {
        /**
         * Initialize pager
         */
        static $crudNum;

        $this->_request         = $request;
        $this->_router          = $router;
        $this->_doctrine        = $doctrine;
        $this->_templating      = $templating;
        $this->_translator      = $translator;
        $this->_session         = $session;
        $this->_datagridService = $datagridService;
        $this->_form            = $form;
        $this->_entity          = $entity;
        $this->_route           = $this->_request->get('_route');

        $crudVar       = isset($config['crudVar']) ? $config['crudVar'] : '_gc'.$crudNum++;
        $defaultConfig = array(
                'idVar'     => $crudVar.'[i]',
                'viewVar'   => $crudVar.'[v]',
//                 'pagerVar'  => $crudVar.'[p]',
                'canDelete' => true,
                'canAdd'    => true,
                'canEdit'   => true,
                'addToURL'  => array()
        );
        $this->_config     = array_merge($defaultConfig, $config);

        if (is_null($entityType)) {
            $entityType    = $entity.'Type';
        }
        $this->_entityType = $entityType;

        // Entity
        list($bundle, $entity) = $this->parseShortcutNotation($entity);
        $this->_entityClass = $this->_doctrine->getEntityNamespace($bundle).'\\'.$entity;

        // EntityType
        list($bundle, $entityType) = $this->parseShortcutNotation($entityType);
        $entityTypeClass   = $this->_doctrine->getEntityNamespace($bundle);
        $parts             = explode('\\', $entityTypeClass);
        array_pop($parts);
        $this->_entityTypeClass = implode('\\', $parts).'\\Form\\'.$entityType;

        switch ($this->getView()) {

            case 'add':
            case 'edit':
            case 'delete':
                break;

            default:
                return $this->prepareListView();
                break;
        }
    }

    public function getView()
    {
        $view = $this->getValue('viewVar');

        if ('add' === $view && $this->getConfig('canAdd')) {
            return $view;
        }
        elseif ('edit' === $view && $this->getConfig('canEdit')) {
            return $view;
        }
        elseif ('delete' === $view && $this->getConfig('canDelete')) {
            return $view;
        }
        else {
            return 'list';
        }
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setTitle($title)
    {
        $this->_title = $title;

        if (!is_null($this->getDatagrid())) {
            $this->getDatagrid()->setTitle($title);
        }
        return $this;
    }

    public function setOrderBy($orderBy)
    {
        if (!is_null($this->getDatagrid())) {
            $this->getDatagrid()->getPager()->getWrapper()->setOrderBy($orderBy);
        }
        return $this;
    }

    public function setItemsPerPage($num)
    {
        if (!is_null($this->getDatagrid())) {
            $this->getDatagrid()->setItemsPerPage($num);
        }
        return $this;
    }

    public function setRoute($route)
    {
        $this->_route = $route;
        return $this;
    }

    public function getRoute()
    {
        return $this->_route;
    }

    public function handle()
    {
        switch ($this->getView()) {

            case 'add':
                return $this->addAction();
                break;

            case 'edit':
                return $this->editAction($this->getValue('idVar'));
                break;

            case 'delete':
                return $this->deleteAction($this->getValue('idVar'));
                break;

            default:
                return $this->listAction();
                break;
        }
    }

    public function getDatagrid()
    {
        return $this->_datagrid;
    }

    public function getFormDatas()
    {
        return $this->_formDatas;
    }

    protected function trans($str)
    {
        return $this->_translator->trans($str, array(), 'crud');
    }

    protected function prepareListView()
    {
        $this->_datagrid = $this->_datagridService->factory($this->_entity, array(
                'rowIdVar' => $this->getConfig('idVar'),
                'addToURL' => $this->getConfig('addToURL'),
        ));

        if ($this->getConfig('canAdd')) {
            $this->_datagrid->addAction(new Action($this->trans('Add'), $this->_request->get('_route'), array_merge($this->getConfig('addToURL'), array($this->getConfig('viewVar') => 'add')), null, '#icon-plus'), 'add', 'toolbar');
        }
        if ($this->getConfig('canEdit')) {
            $this->_datagrid->addAction(new Action($this->trans('Edit'), $this->_request->get('_route'), array_merge($this->getConfig('addToURL'), array($this->getConfig('viewVar') => 'edit')), null, '#icon-pencil'), 'edit');
        }
        if ($this->getConfig('canDelete')) {
            $this->_datagrid->addAction(new Action($this->trans('Delete'), $this->_request->get('_route'), array_merge($this->getConfig('addToURL'), array($this->getConfig('viewVar') => 'delete')), $this->trans('Confirm delete ?'), '#icon-remove'), 'delete');
        }

        if ($this->_session->hasFlash('crud_success')) {
            $this->_datagrid->addNotification(new Notification($this->_session->getFlash('crud_success'), 'success'));
        }
        if ($this->_session->hasFlash('crud_error')) {
            $this->_datagrid->addNotification(new Notification($this->_session->getFlash('crud_error'), 'error'));
        }
    }

    protected function listAction()
    {
        return array('crud' => $this);
    }

    protected function addAction()
    {
        $options          = array('url' => $this->_router->generate($this->_request->get('_route'), $this->getConfig('addToURL')));
        $this->_formDatas = $this->_form->create(new $this->_entityTypeClass, new $this->_entityClass, array(), 'redirect', $options);

        if ($this->_formDatas instanceof Response) {
            $this->_session->setFlash('crud_success', $this->trans('Creation successful'));
            return $this->_formDatas;
        }
        return array('crud' => $this);
    }

    protected function editAction($id)
    {
        $options          = array('url' => $this->_router->generate($this->_request->get('_route'), $this->getConfig('addToURL')));
        $this->_formDatas = $this->_form->edit(new $this->_entityTypeClass, array($this->_entity, $id), array(), 'redirect', $options);

        if ($this->_formDatas instanceof Response) {
            $this->_session->setFlash('crud_success', $this->trans('Edition successful'));
            return $this->_formDatas;
        }
        return array('crud' => $this);
    }

    protected function deleteAction($id)
    {
        $options          = array('url' => $this->_router->generate($this->_request->get('_route'), $this->getConfig('addToURL')));
        $this->_formDatas = $this->_form->delete(array($this->_entity, $id), 'redirect', $options);

        if ($this->_formDatas instanceof Response) {
            $this->_session->setFlash('crud_success', $this->trans('Delete successful'));
        }
        return $this->_formDatas;
    }

    /**
     * From Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand
     */
    protected function parseShortcutNotation($shortcut)
    {
        $entity = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($entity, ':')) {
            throw new \InvalidArgumentException(sprintf('The entity name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)', $entity));
        }

        return array(substr($entity, 0, $pos), substr($entity, $pos + 1));
    }

    public function getConfig($option)
    {
        return $this->_config[$option];
    }

    public function getValue($option, $default = null)
    {
        return $this->_request->get($this->getConfig($option), $default, true);
    }
}