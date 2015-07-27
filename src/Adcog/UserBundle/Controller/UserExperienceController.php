<?php

namespace Adcog\UserBundle\Controller;

use Adcog\DefaultBundle\Entity\Experience;
use Adcog\DefaultBundle\Entity\ExperienceInternship;
use Adcog\DefaultBundle\Entity\ExperienceStudy;
use Adcog\DefaultBundle\Entity\ExperienceThesis;
use Adcog\DefaultBundle\Entity\ExperienceWork;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class UserExperienceController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/cursus")
 */
class UserExperienceController extends Controller
{
    /**
     * @Route()
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Experience')->getPaginator($paginatorHelper, ['user' => $this->getUser()]);

        return [
            'paginator' => $paginator,
        ];
    }

    /**
     * Create work
     *
     * @param Request $request
     *
     * @return array
     * @Route("/ajouter-experience-professionnelle")
     * @Method("GET|POST")
     * @Template()
     */
    public function createWorkAction(Request $request)
    {
        $experience = new ExperienceWork();
        $experience->setUser($this->getUser());
        $form = $this->createForm('adcog_experience_work', $experience);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($experience);
            $em->flush();

            return $this->redirect($this->generateUrl('user_experience_index'));
        }

        return [
            'experience' => $experience,
            'form' => $form->createView(),
        ];
    }

    /**
     * Create strudy
     *
     * @param Request $request
     *
     * @return array
     * @Route("/ajouter-diplome")
     * @Method("GET|POST")
     * @Template()
     */
    public function createStudyAction(Request $request)
    {
        $experience = new ExperienceStudy();
        $experience->setUser($this->getUser());
        $form = $this->createForm('adcog_experience_study', $experience);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($experience);
            $em->flush();

            return $this->redirect($this->generateUrl('user_experience_index'));
        }

        return [
            'experience' => $experience,
            'form' => $form->createView(),
        ];
    }

    /**
     * Create thesis
     *
     * @param Request $request
     *
     * @return array
     * @Route("/ajouter-these")
     * @Method("GET|POST")
     * @Template()
     */
    public function createThesisAction(Request $request)
    {
        $experience = new ExperienceThesis();
        $experience->setUser($this->getUser());
        $form = $this->createForm('adcog_experience_thesis', $experience);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($experience);
            $em->flush();

            return $this->redirect($this->generateUrl('user_experience_index'));
        }

        return [
            'experience' => $experience,
            'form' => $form->createView(),
        ];
    }

    /**
     * Create
     *
     * @param Request $request
     *
     * @return array
     * @Route("/ajouter-stage")
     * @Method("GET|POST")
     * @Template()
     */
    public function createInternshipAction(Request $request)
    {
        $experience = new ExperienceInternship();
        $experience->setUser($this->getUser());
        $form = $this->createForm('adcog_experience_internship', $experience);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($experience);
            $em->flush();

            return $this->redirect($this->generateUrl('user_experience_index'));
        }

        return [
            'experience' => $experience,
            'form' => $form->createView(),
        ];
    }

    /**
     * Update
     *
     * @param Request    $request    Request
     * @param Experience $experience Experience
     *
     * @return array
     * @throws NotFoundHttpException
     * @Route("/{experience}/modifier", requirements={"experience":"\d+"})
     * @ParamConverter("experience", class="AdcogDefaultBundle:Experience")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, Experience $experience)
    {
        if ($experience->getUser()->getId() !== $this->getUser()->getId()) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(sprintf('adcog_experience_%s', $experience->getType()), $experience);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($experience);
            $em->flush();

            return $this->redirect($this->generateUrl('user_experience_index'));
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
     * @return array
     * @throws NotFoundHttpException
     * @Route("/{experience}/supprimer", requirements={"experience":"\d+"})
     * @ParamConverter("experience", class="AdcogDefaultBundle:Experience")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, Experience $experience)
    {
        if ($experience->getUser()->getId() !== $this->getUser()->getId()) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($experience);
            $em->flush();

            return $this->redirect($this->generateUrl('user_experience_index'));
        }

        return [
            'experience' => $experience,
            'form' => $form->createView(),
        ];
    }
}
