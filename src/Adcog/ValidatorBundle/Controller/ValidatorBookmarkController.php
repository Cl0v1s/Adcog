<?php

namespace Adcog\ValidatorBundle\Controller;

use Adcog\DefaultBundle\Entity\Bookmark;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ValidatorBookmarkController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/bookmark")
 */
class ValidatorBookmarkController extends Controller
{
    /**
     * Index
     *
     * @param Request $request Request
     *
     * @return array
     * @Route("/{page}", requirements={"page":"\d+"}, defaults={"page":1})
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        // Filter results
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_validator_bookmark_filter', ['not_validated' => true], ['method' => 'GET', 'csrf_protection' => false], [])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Bookmark')->getPaginator($paginatorHelper, $filterData);

        return [
            'paginator' => $paginator,
            'filter' => $filter->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Request  $request  Request
     * @param Bookmark $bookmark Bookmark
     *
     * @return array
     * @Route("/{bookmark}/read", requirements={"bookmark":"\d+"})
     * @ParamConverter("bookmark", class="AdcogDefaultBundle:Bookmark")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Request $request, Bookmark $bookmark)
    {
        $form = $this->createForm('adcog_validator_bookmark', $bookmark);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('validator_bookmark_index'));
        }

        return [
            'bookmark' => $bookmark,
            'form' => $form->createView(),
        ];
    }
}
