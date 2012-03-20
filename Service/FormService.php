<?php

namespace Gloomy\PagerBundle\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Gloomy\PagerBundle\Pager\Pager;

class FormService {

    private $_formFactory;

    private $_request;

    private $_doctrine;

    private $_translator;

    protected $defaultResponseOptions;

    public function __construct($formFactory, $request, $doctrine, $translator)
    {
        $this->_formFactory = $formFactory;
        $this->_request     = $request;
        $this->_doctrine    = $doctrine;
        $this->_translator  = $translator;

        $this->defaultResponseOptions = array(
                'success'           => $this->_translator->trans('Operation successful', array(), 'form'),
                'error'             => $this->_translator->trans('An error occured', array(), 'form'),
                'notFound'          => $this->_translator->trans('Object not found', array(), 'form'),
                'url'               => '/',
                'status'            => 200,
                'redirectStatus'    => 302
        );
    }

    public function create($type, $data, array $options = array(), $responseType = 'html', array $responseOptions = array())
    {
        $responseOptions    = array_merge($this->defaultResponseOptions, $responseOptions);
        $message            = null;

        $em                 = $this->_doctrine->getEntityManager();
        $form               = $this->_formFactory->create($type, $data, $options);
        $request            = $this->_request;

        if ($request->getMethod() === 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->persist($data);
                $em->flush();

                switch ($responseType) {
                    case 'json':
                        $message    = array(
                            'success'    => true,
                            'message'    => $responseOptions['success']
                        );
                        return new Response(json_encode($message), $responseOptions['status'], array('Content-type' => 'application/json'));
                        break;

                    case 'redirect':
                        return new RedirectResponse($responseOptions['url'], $responseOptions['redirectStatus']);
                        break;

                    default:
                    case 'html':
                        $message    = $responseOptions['success'];
                        break;
                }
            }
            else {
                $message    = $responseOptions['error'];
            }
        }
        return array('form' => $form->createView(), 'item' => $data, 'action' => 'create', 'message' => $message);
    }

    public function edit($type, $data, array $options = array(), $responseType = 'html', array $responseOptions = array())
    {
        $responseOptions    = array_merge($this->defaultResponseOptions, $responseOptions);
        $message            = null;

        $em                 = $this->_doctrine->getEntityManager();
        if (is_array($data)) {
            $data           = $em->getRepository($data[0])->find($data[1]);
        }

        if (!$data) {
            switch ($responseType) {
                case 'json':
                    $message    = array(
                        'success'    => false,
                        'message'    => $responseOptions['notFound']
                    );
                    return new Response(json_encode($message), $responseOptions['status'], array('Content-type' => 'application/json'));
                    break;

                default:
                case 'redirect':
                case 'html':
                    $message    = $responseOptions['notFound'];
                    break;
            }
        }

        $form          = $this->_formFactory->create($type, $data, $options);
        $request       = $this->_request;

        if ($request->getMethod() === 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->flush();

                switch ($responseType) {
                    case 'json':
                        $message    = array(
                            'success'    => true,
                            'message'    => $responseOptions['success']
                        );
                        return new Response(json_encode($message), 200, array('Content-type' => 'application/json'));
                        break;

                    case 'redirect':
                        return new RedirectResponse($responseOptions['url'], $responseOptions['redirectStatus']);
                        break;

                    default:
                    case 'html':
                        $message    = $responseOptions['success'];
                        break;
                }
            }
            else {
                $message    = $responseOptions['error'];
            }
        }

        return array('form' => $form->createView(), 'item' => $data, 'action' => 'edit', 'message' => $message);
    }

    public function delete($data, $responseType = 'json', array $responseOptions = array())
    {
        $responseOptions    = array_merge($this->defaultResponseOptions, $responseOptions);
        $message            = null;

        $em                 = $this->_doctrine->getEntityManager();
        if (is_array($data)) {
            $data           = $em->getRepository($data[0])->find($data[1]);
        }

        if (!$data) {
            switch ($responseType) {
                case 'json':
                    $message    = array(
                        'success'    => false,
                        'message'    => $responseOptions['notFound']
                    );
                    return new Response(json_encode($message), $responseOptions['status'], array('Content-type' => 'application/json'));
                    break;

                default:
                case 'redirect':
                case 'html':
                    $message    = $responseOptions['notFound'];
                    break;
            }
        }
        else {
            $em->remove($data);
            $em->flush();
        }

        switch ($responseType) {
            case 'json':
                $message    = array(
                    'success'    => true,
                    'message'    => $responseOptions['success']
                );
                return new Response(json_encode($message), 200, array('Content-type' => 'application/json'));
                break;

            case 'redirect':
                return new RedirectResponse($responseOptions['url'], $responseOptions['redirectStatus']);
                break;

            default:
            case 'html':
                $message    = $responseOptions['success'];
                break;
        }

        return array('action' => 'delete', 'message' => $message);
    }
}
