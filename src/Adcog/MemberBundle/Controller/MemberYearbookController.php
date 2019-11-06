<?php

namespace Adcog\MemberBundle\Controller;

use Adcog\DefaultBundle\Entity\Employer;
use Adcog\DefaultBundle\Entity\Experience;
use Adcog\DefaultBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MemberYearbookController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/annuaire")
 */
class MemberYearbookController extends Controller
{
    /**
     * Index
     *
     * @return RedirectResponse
     * @Route()
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('member_yearbook_members'));
    }

    /**
     * Employer
     *
     * @param Employer $employer Employer
     * @param string   $slug     Slug
     *
     * @return array
     * @Route("/etablissements/{employer}-{slug}", requirements={"employer":"\d+"})
     * @Method("GET")
     * @Template()
     */
    public function employerAction(Employer $employer, $slug)
    {
        if ($slug !== $employer->getSlug()) {
            return $this->redirect($this->generateUrl('member_yearbook_employer', [
                'employer' => $employer->getId(),
                'slug' => $employer->getSlug(),
            ]));
        }

        return [
            'employer' => $employer,
        ];
    }

    /**
     * Employers
     *
     * @param Request $request Request
     *
     * @return array
     * @Route("/etablissement/{page}/{order}", requirements={"page":"\d+"}, defaults={"page":1, "order":"ASC"})
     * @Method("GET|POST")
     * @Template()
     */
    public function employersAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed(null, 'adcog_member_employer_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filter = $form->isValid() ? $form->getData() : [];

        $order = strtoupper($request->get('order')) == 'ASC' ? 'ASC' : 'DESC';

        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Employer')->getPaginator($paginatorHelper, $filter, ['name' => $order, 'created' => 'DESC']);
        
        return [
            'form' => $form->createView(),
            'paginator' => $paginator,
            'order' => $order
        ];
    }

    /**
     * Experience
     *
     * @param Experience $experience Experience
     * @param string     $slug       Slug
     *
     * @return array
     * @Route("/experiences/{experience}-{slug}", requirements={"experience":"\d+"})
     * @Method("GET")
     * @Template()
     */
    public function experienceAction(Experience $experience, $slug)
    {
        if ($slug !== $experience->getSlug()) {
            return $this->redirect($this->generateUrl('member_yearbook_experience', [
                'experience' => $experience->getId(),
                'slug' => $experience->getSlug(),
            ]));
        }
        if (false === $experience->getUser()->isValid()) {
            return $this->createNotFoundException();
        }

        return [
            'experience' => $experience,
        ];
    }

    /**
     * Experiences
     *
     * @param Request $request Request
     *
     * @return array
     * @Route("/experiences/{page}", requirements={"page":"\d+"}, defaults={"page":1})
     * @Method("GET|POST")
     * @Template()
     */
    public function experiencesAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed(null, 'adcog_member_experience_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filter = $form->isValid() ? $form->getData() : [];
        $filter['valid'] = true;

        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Experience')->getPaginator($paginatorHelper, $filter);

        return [
            'form' => $form->createView(),
            'paginator' => $paginator,
        ];
    }

    /**
     * Member
     *
     * @param User   $user User
     * @param string $slug Slug
     *
     * @return array
     * @Route("/membres/{user}-{slug}", requirements={"user":"\d+"})
     * @Method("GET")
     * @Template()
     */
    public function memberAction(User $user, $slug)
    {		
        if ($slug !== $user->getSlug()) {			
            return $this->redirect($this->generateUrl('member_yearbook_member', [
                'user' => $user->getId(),
                'slug' => $user->getSlug(),
            ]));
        }
        if (false === $user->isValid()) {
            return $this->createNotFoundException();
        }
		
		# get experiences paginator
		$paginatorHelper = $this->get('eb_paginator_helper');
		$paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Experience')->getPaginator($paginatorHelper, ['user' => $user]);

        return [
            'user' => $user,
			'paginator' => $paginator,
        ];
    }

    /**
     * Members
     *
     * @param Request $request Request
     *
     * @return array
     * @Route("/membres/{page}/{order}", requirements={"page":"\d+"}, defaults={"page":1, "order":"ASC"})
     * @Method("GET|POST")
     * @Template()
     */
    public function membersAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed(null, 'adcog_member_member_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filter = $form->isValid() ? $form->getData() : [];
        $filter['valid'] = true;

        $order = strtoupper($request->get('order')) == 'ASC' ? 'ASC' : 'DESC';

        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:User')->getPaginator($paginatorHelper, $filter, ['lastname' => $order, 'firstname' => 'ASC', 'created' => 'DESC']);
        
        return [
            'form' => $form->createView(),
            'paginator' => $paginator,
            'order' => $order,
        ];
    }
}
