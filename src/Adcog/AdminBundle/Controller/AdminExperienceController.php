<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\Experience;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminExperienceController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/experience")
 */
class AdminExperienceController extends Controller
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
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_experience_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Experience')->getPaginator($paginatorHelper, $filterData);

        return [
            'paginator' => $paginator,
            'filter' => $filter->createView(),
        ];
    }

    /**
     * Create
     *
     * @param Request $request Request
     * @param string  $type    Type
     *
     * @return array|RedirectResponse
     * @Route("/create/{type}")
     * @Method("GET|POST")
     * @Template()
     */
    public function createAction(Request $request, $type = null)
    {
        if (null === $type || false === in_array($type, Experience::getTypeList())) {
            $whichBuilder = $this->get('form.factory')->createNamedBuilder(null, 'form', [], ['method' => 'GET', 'csrf_protection' => false]);
            $whichBuilder->add('type', 'choice', ['label' => 'Type', 'choices' => Experience::getTypeNameList()]);
            $form = $whichBuilder->getForm();
            if ($form->handleRequest($request)->isValid()) {
                return $this->redirect($this->generateUrl('admin_experience_create', ['type' => $form->get('type')->getData()]));
            }
        } else {
            $experience = Experience::createObject($type);
            $experience->setUser($this->getUser());
            $form = $this->createForm(sprintf('adcog_experience_%s', $type), $experience);
            if ($form->handleRequest($request)->isValid()) {
                $em = $this->get('doctrine.orm.default_entity_manager');
                $em->persist($experience);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_experience_read', ['experience' => $experience->getId()]));
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Experience $experience Experience
     *
     * @return array
     * @Route("/{experience}/read", requirements={"experience":"\d+"})
     * @ParamConverter("experience", class="AdcogDefaultBundle:Experience")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Experience $experience)
    {
        return [
            'experience' => $experience,
        ];
    }

    /**
     * Update
     *
     * @param Request    $request    Request
     * @param Experience $experience Experience
     *
     * @return array|RedirectResponse
     * @Route("/{experience}/update", requirements={"experience":"\d+"})
     * @ParamConverter("experience", class="AdcogDefaultBundle:Experience")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, Experience $experience)
    {
        $form = $this->createForm(sprintf('adcog_experience_%s', $experience->getType()), $experience);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('admin_experience_read', ['experience' => $experience->getId()]));
        }

        return [
            'experience' => $experience,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request    $request    Request
     * @param Experience $experience Experience
     *
     * @return array|RedirectResponse
     * @Route("/{experience}/delete", requirements={"experience":"\d+"})
     * @ParamConverter("experience", class="AdcogDefaultBundle:Experience")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, Experience $experience)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($experience);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_experience_index'));
        }

        return [
            'experience' => $experience,
            'form' => $form->createView(),
        ];
    }
}
