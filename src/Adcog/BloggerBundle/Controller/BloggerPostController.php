<?php

namespace Adcog\BloggerBundle\Controller;

use Adcog\DefaultBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BloggerPostController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/post")
 */
class BloggerPostController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_blogger_post_filter', [
            'not_validated' => true,
        ], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Post')->getPaginator($paginatorHelper, $filterData);

        return [
            'paginator' => $paginator,
            'filter' => $filter->createView(),
        ];
    }

    /**
     * Create
     *
     * @param Request $request Request
     *
     * @return array|RedirectResponse
     * @Route("/create")
     * @Method("GET|POST")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $post = new Post();
        $post->setAuthor($this->getUser());
        $form = $this->createForm('adcog_blogger_post', $post);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($post);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_post_read', ['post' => $post->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Post $post Post
     *
     * @return array
     * @Route("/{post}/read", requirements={"post":"\d+"})
     * @ParamConverter("post", class="AdcogDefaultBundle:Post")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Post $post)
    {
        return [
            'post' => $post,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param Post    $post    Post
     *
     * @return array|RedirectResponse
     * @Route("/{post}/update", requirements={"post":"\d+"})
     * @ParamConverter("post", class="AdcogDefaultBundle:Post")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, Post $post)
    {
        $form = $this->createForm('adcog_blogger_post', $post);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('blogger_post_read', ['post' => $post->getId()]));
        }

        return [
            'post' => $post,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param Post    $post    Post
     *
     * @return array|RedirectResponse
     * @Route("/{post}/delete", requirements={"post":"\d+"})
     * @ParamConverter("post", class="AdcogDefaultBundle:Post")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, Post $post)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($post);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_post_index'));
        }

        return [
            'post' => $post,
            'form' => $form->createView(),
        ];
    }
}
