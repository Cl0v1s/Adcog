<?php

namespace Adcog\UserBundle\Controller;

use Adcog\DefaultBundle\Entity\Bookmark;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserBookmarkController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/bookmarks")
 */
class UserBookmarkController extends Controller
{
    /**
     * Index
     *
     * @param Request $request
     *
     * @return array
     * @Route("/{page}", requirements={"page":"\d+"}, defaults={"page":1 })
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        // Filter results
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_user_bookmark_filter', [], ['method' => 'GET', 'csrf_protection' => false], [])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];
        $filterData['valid'] = true;

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Bookmark')->getPaginator($paginatorHelper, $filterData);

        return [
            'paginator' => $paginator,
            'filter' => $filter->createView(),
        ];
    }

    /**
     * Create
     *
     * @param Request $request
     *
     * @return array
     * @Route("/create")
     * @Method("GET|POST")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $bookmark = new Bookmark();
        $bookmark->setAuthor($this->getUser());
        $form = $this->createForm('adcog_user_bookmark', $bookmark);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($bookmark);
            $em->flush();

            return $this->redirect($this->generateUrl('user_bookmark_index'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Upload
     *
     * @param Request $request
     *
     * @return array
     * @Route("/upload")
     * @Method("GET|POST")
     * @Template()
     */
    public function uploadAction(Request $request)
    {
        $bookmark = new Bookmark();
        $bookmark
            ->setAuthor($this->getUser())
            ->setTitle($request->query->get('title'))
            ->setHref($request->query->get('href'));
        $form = $this->createForm('adcog_user_bookmark', $bookmark);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($bookmark);
            $em->flush();

            return new Response('<script type="text/javascript">window.close();</script>');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
