<?php

namespace Adcog\UserBundle\Controller;

use Adcog\DefaultBundle\Entity\Profile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class UserProfileController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/profil")
 */
class UserProfileController extends Controller
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
        return [];
    }

    /**
     * @param Request $request
     *
     * @return array
     * @Route("/creer")
     * @Method("GET|POST")
     * @Template()
     */
    public function createAction(Request $request)
    {
        if (null !== $profile = $this->getUser()->getProfile()) {
            return $this->redirect($this->generateUrl('user_profile_update'));
        }

        $profile = new Profile();
        $profile
            ->setUser($this->getUser())
            ->setName((string)$this->getUser());

        $form = $this->createForm('adcog_profile', $profile);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($profile);
            $em->flush();
            $em->refresh($this->getUser());

            return $this->redirect($this->generateUrl('user_profile_index'));
        }

        return [
            'form' => $form->createView(),
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
        if (null === $profile = $this->getUser()->getProfile()) {
            return $this->redirect($this->generateUrl('user_profile_create'));
        }

        $form = $this->createForm('adcog_profile', $profile);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('user_profile_index'));
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
        if (null === $profile = $this->getUser()->getProfile()) {
            return $this->redirect($this->generateUrl('user_profile_index'));
        }

        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($profile);
            $em->flush();

            return $this->redirect($this->generateUrl('user_profile_index'));
        }

        return [
            'form' => $form->createView(),
        ];
    }
}