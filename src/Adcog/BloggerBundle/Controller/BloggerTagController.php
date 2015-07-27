<?php

namespace Adcog\BloggerBundle\Controller;

use Adcog\DefaultBundle\Entity\Tag;
use Adcog\DefaultBundle\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class BloggerTagController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/tag")
 */
class BloggerTagController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_blogger_tag_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Tag')->getPaginator($paginatorHelper, $filterData);

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
        $form = $this->createForm('adcog_blogger_tag', $tag = new Tag());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($tag);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_tag_read', ['tag' => $tag->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Tag $tag Tag
     *
     * @return array
     * @Route("/{tag}/read", requirements={"tag":"\d+"})
     * @ParamConverter("tag", class="AdcogDefaultBundle:Tag")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Tag $tag)
    {
        return [
            'tag' => $tag,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param Tag     $tag     Tag
     *
     * @return array|RedirectResponse
     * @Route("/{tag}/update", requirements={"tag":"\d+"})
     * @ParamConverter("tag", class="AdcogDefaultBundle:Tag")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, Tag $tag)
    {
        $form = $this->createForm('adcog_blogger_tag', $tag);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('blogger_tag_read', ['tag' => $tag->getId()]));
        }

        return [
            'tag' => $tag,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param Tag     $tag     Tag
     *
     * @return array|RedirectResponse
     * @Route("/{tag}/delete", requirements={"tag":"\d+"})
     * @ParamConverter("tag", class="AdcogDefaultBundle:Tag")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, Tag $tag)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($tag);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_tag_index'));
        }

        return [
            'tag' => $tag,
            'form' => $form->createView(),
        ];
    }

    /**
     * Replace
     *
     * @param Request $request Request
     * @param Tag     $tag     Tag
     *
     * @return array|RedirectResponse
     * @Route("/{tag}/replace", requirements={"tag":"\d+"})
     * @ParamConverter("tag", class="AdcogDefaultBundle:Tag")
     * @Method("GET|POST")
     * @Template()
     */
    public function replaceAction(Request $request, Tag $tag)
    {
        $query = function (TagRepository $er) use ($tag) {
            $qb = $er->createQueryBuilder('a');

            return $qb
                ->andWhere($qb->expr()->neq('a', ':tag'))
                ->setParameter('tag', $tag);
        };

        $form = $this
            ->createFormBuilder()
            ->add('tag', 'entity', [
                'class' => 'Adcog\DefaultBundle\Entity\Tag',
                'query_builder' => $query,
                'label' => 'Nouveau tag',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->getForm();
        if ($form->handleRequest($request)->isValid()) {
            $new = $form->get('tag')->getData();
            $posts = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Post')->findAllPostTaguedWith($tag);
            foreach ($posts as $post) {
                $post->removeTag($tag);
                $post->addTag($new);
            }
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->flush();
            $em->remove($tag);
            $em->flush();

            return $this->redirect($this->generateUrl('blogger_tag_index'));
        }

        return [
            'tag' => $tag,
            'form' => $form->createView(),
        ];
    }
}
