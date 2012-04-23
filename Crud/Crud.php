<?php

namespace Gloomy\PagerBundle\Crud;

use Symfony\Component\HttpFoundation\Response;

use Gloomy\PagerBundle\DataGrid\DataGrid;

class Crud
{
    protected $_request;

    protected $_router;

    protected $_doctrine;

    protected $_templating;

    protected $_datagridService;

    protected $_form;

    protected $_entity;

    protected $_entityType;

    protected $_config;

    protected $_datagrid;

    protected $_viewVar = 'crud_action';

    protected $_idVar = 'id';

    public function __construct($request, $router, $doctrine, $templating, $datagridService, $form, $entity, $entityType = null)
    {
        $this->_request    = $request;
        $this->_router     = $router;
        $this->_doctrine   = $doctrine;
        $this->_templating = $templating;
        $this->_datagridService = $datagridService;
        $this->_form       = $form;
        $this->_entity     = $entity;

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
        $view = $this->_request->get($this->_viewVar);
        if (in_array($view, array('add', 'edit', 'delete'))) {
            return $view;
        }
        return 'list';
    }

    public function run()
    {
        switch ($this->getView()) {

            case 'add':
                return $this->addAction();
                break;

            case 'edit':
                return $this->editAction($this->_request->get($this->_idVar));
                break;

            case 'delete':
                return $this->deleteAction($this->_request->get($this->_idVar));
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

    protected function prepareListView()
    {
        $this->_datagrid = $this->_datagridService
            ->factory($this->_entity)
            ->addAction('Add', $this->_request->get('_route'), 'toolbar', null, array($this->_viewVar => 'add'))
            ->addAction('Edit', $this->_request->get('_route'), 'row', null, array($this->_viewVar => 'edit'))
            ->addAction('Delete', $this->_request->get('_route'), 'row', 'Confirm delete ?', array($this->_viewVar => 'delete'));
    }

    protected function listAction()
    {
        return array('crud' => $this->_templating->render('GloomyPagerBundle:Crud:list.html.twig', array('datagrid' => $this->_datagrid)));
    }

    protected function addAction()
    {
        $options = array('url' => $this->_router->generate($this->_request->get('_route')));
        $ret = $this->_form->create(new $this->_entityTypeClass, new $this->_entityClass, array(), 'redirect', $options);

        if ($ret instanceof Response) {
            return $ret;
        }
        return array('crud' => $this->_templating->render('GloomyPagerBundle:Crud:add.html.twig', $ret));
    }

    protected function editAction($id)
    {
        $options = array('url' => $this->_router->generate($this->_request->get('_route')));
        $ret = $this->_form->edit(new $this->_entityTypeClass, array($this->_entity, $id), array(), 'redirect', $options);

        if ($ret instanceof Response) {
            return $ret;
        }
        return array('crud' => $this->_templating->render('GloomyPagerBundle:Crud:edit.html.twig', $ret));
    }

    protected function deleteAction($id)
    {
        $options = array('url' => $this->_router->generate($this->_request->get('_route')));
        return $this->_form->delete(array($this->_entity, $id), 'redirect', $options);
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

}