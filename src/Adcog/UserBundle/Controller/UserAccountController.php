<?php

namespace Adcog\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adcog\DefaultBundle\Entity\Experience;


/**
 * Class UserAccountController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/informations")
 */
class UserAccountController extends Controller
{
    /**
     * User
     *
     * @return array
     * @Route()
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $countStudies = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Experience')->getStudiesNumber($this->getUser()->getId());

        $countExperiences = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Experience')->getExperiencesNumber($this->getUser()->getId());

        return [
            'nbrStudies' => $countStudies,
            'nbrExperiences' => $countExperiences,
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     * @Route("/modifier")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request)
    {
        $form = $this->createForm('adcog_user_account', $this->getUser());
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('user_account_index'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     * @Route("/photo")
     * @Method("GET|POST")
     * @Template()
     */
    public function profileAction(Request $request)
    {
        $form = $this->createForm('adcog_user_profile', $this->getUser());
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('user_account_index'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     * @Route("/supprimer")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request)
    {
        $form = $this->createFormBuilder()->getForm();
        if ($form->handleRequest($request)->isValid()) {
            $this->getUser()->setEnabled(false);
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('default_logout'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     * @Route("/mot-de-passe")
     * @Method("GET|POST")
     * @Template()
     */
    public function passwordAction(Request $request)
    {
        $form = $this->createForm('adcog_user_password', $this->getUser());
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('user_account_index'));
        }

        return [
            'form' => $form->createView(),
        ];
    }
}