<?php

namespace Adcog\BloggerBundle\Controller;

use Adcog\DefaultBundle\Entity\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class BloggerFileController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/file")
 */
class BloggerFileController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_blogger_file_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:File')->getPaginator($paginatorHelper, $filterData);

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
        $form = $this->createForm('adcog_blogger_file', $file = new File());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($file);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_file_read', ['file' => $file->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param File $file File
     *
     * @return array
     * @Route("/{file}/read", requirements={"file":"\d+"})
     * @ParamConverter("file", class="AdcogDefaultBundle:File")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(File $file)
    {
        $paginatorHelper = $this->get('eb_paginator_helper')->createEmptyInstance();
        $posts = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Post')->getPaginator($paginatorHelper, ['text' => $file->getUri()]);
        $staticContents = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:StaticContent')->getPaginator($paginatorHelper, ['content' => $file->getUri()]);
        $events = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Event')->getPaginator($paginatorHelper, ['description' => $file->getUri()]);

        return [
            'file' => $file,
            'posts' => $posts,
            'staticContents' => $staticContents,
            'events' => $events,
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param File    $file    File
     *
     * @return array|RedirectResponse
     * @Route("/{file}/delete", requirements={"file":"\d+"})
     * @ParamConverter("file", class="AdcogDefaultBundle:File")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, File $file)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($file);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_file_index'));
        }

        return [
            'file' => $file,
            'form' => $form->createView(),
        ];
    }

    /**
     * Browse
     *
     * @return RedirectResponse
     * @Route("/browse/{page}", requirements={"page":"\d+"}, defaults={"page":1})
     * @Method("GET")
     * @Template()
     */
    public function browseAction()
    {
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:File')->getPaginator($paginatorHelper);

        return [
            'paginator' => $paginator,
        ];
    }

    /**
     * Upload
     *
     * @param Request $request
     *
     * @return RedirectResponse
     * @throws NotFoundHttpException
     * @Route("/upload")
     * @Method("POST")
     * @Template()
     */
    public function uploadAction(Request $request)
    {
        if (null === $file = $request->files->get('upload')) {
            throw new NotFoundHttpException();
        }

        $entity = new File();
        $entity->setFile($file);
        $em = $this->get('doctrine.orm.default_entity_manager');
        $em->persist($entity);
        $em->flush();

        return [
            'file' => $entity,
        ];
    }
}
