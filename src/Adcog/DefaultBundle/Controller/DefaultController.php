<?php

namespace Adcog\DefaultBundle\Controller;

use Adcog\DefaultBundle\Entity\Contact;
use Adcog\DefaultBundle\Entity\FaqCategory;
use Adcog\DefaultBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;

/**
 * Class DefaultController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class DefaultController extends Controller
{
    /**
     * Index
     *
     * @return array
     * @Route()
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $staticManager = $this->get('adcog_default.static_content.static_manager');

        return [
            'sliders' => $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Slider')->findBy([], ['created' => 'DESC']),
            'welcome' => $staticManager->getDefaultIndexWelcome(),
            'organization' => $staticManager->getDefaultIndexOrganization(),
            'join' => $staticManager->getDefaultIndexJoin(),
            'speek' => $staticManager->getDefaultIndexSpeek(),
            'share' => $staticManager->getDefaultIndexShare(),
        ];
    }

    /**
     * Adhesion
     *
     * @return RedirectResponse
     * @Route("/adhesion")
     * @Method("GET")
     */
    public function adhesionAction()
    {
        return $this->redirect($this->generateUrl('default_register'));
    }

    /**
     * Connexion
     *
     * @param Request $request
     *
     * @return array
     * @Route("/connexion")
     * @Method("GET|POST")
     * @Template()
     */
    public function connexionAction(Request $request)
    {
        $session = $request->getSession();
        if (null !== $username = $session->get(Security::LAST_USERNAME)) {
            $session->remove(Security::LAST_USERNAME);
        }
        if (null !== $error = $session->get(Security::AUTHENTICATION_ERROR)) {
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        return [
            'username' => $username,
            'error' => $error,
        ];
    }

    /**
     * Contact
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     * @Route("/contact")
     * @Method("GET|POST")
     * @Template()
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm('adcog_contact', $contact = new Contact());
        if ($form->handleRequest($request)->isValid()) {
            $this->get('eb_email.mailer.mailer')->send('contact', 'contact@adcog.fr', [
                'mail' => $form->getData(),
            ]);

            $session = $request->getSession();
            if ($session instanceof Session) {
                $session->getFlashBag()->add('success', 'Email envoyé avec succès !');
            }

            return $this->redirect($this->generateUrl('default_contact'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Présentation de l'école
     *
     * @return array
     * @Route("/ensc/{page}", requirements={"page":"\d+"}, defaults={"page":1})
     * @Method("GET")
     * @Template()
     */
    public function enscAction()
    {
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginatorHelper->setLimit(6);
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:School')->getPaginator($paginatorHelper);

        return [
            'paginator' => $paginator,
            'year' => (int)date('Y'),
            'ensc' => $this->get('adcog_default.static_content.static_manager')->getDefaultEnsc(),
        ];
    }

    /**
     * Présentation de l'association
     *
     * @param int $year Année de la promotion
     *
     * @return array
     * @throws NotFoundHttpException
     * @Route("/ensc/promotion-{year}/{page}", requirements={"year":"\d+","page":"\d+"}, defaults={"page":1})
     * @Method("GET")
     * @Template()
     */
    public function enscSchoolAction($year)
    {
        if (null === $school = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:School')->findOneBy(['year' => $year])) {
            throw new NotFoundHttpException(sprintf('La promotion %u n\'existe pas !', $year));
        }

        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:User')->getPaginator($paginatorHelper, ['school' => $school, 'allow' => true]);

        return [
            'school' => $school,
            'paginator' => $paginator,
        ];
    }

    /**
     * Event
     *
     * @return array
     * @Route("/evenements")
     * @Method("GET")
     * @Template()
     */
    public function eventAction()
    {
        $staticManager = $this->get('adcog_default.static_content.static_manager');

        return [
            'events' => $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Event')->findIncomingEvents(),
            'picognitique' => $staticManager->getDefaultEventPicognitique(),
            'cognitoconf' => $staticManager->getDefaultEventCognitoconf(),
            'cogout' => $staticManager->getDefaultEventCogout(),
        ];
    }

    /**
     * Faq
     *
     * @return array
     * @Route("/foire-aux-questions")
     * @Method("GET")
     * @Template()
     */
    public function faqAction()
    {
        if (null !== $firstFaqCategory = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:FaqCategory')->findOneBy([], ['name' => 'ASC'])) {
            return $this->redirect($this->generateUrl('default_faq_category', ['slug' => $firstFaqCategory->getSlug()]));
        }

        return [];
    }

    /**
     * Faq page
     *
     * @param string $slug
     *
     * @return array
     * @throws HttpException
     * @Route("/foire-aux-questions/{slug}", requirements={"slug":"[a-z0-9-]+"})
     * @Method("GET")
     * @Template()
     */
    public function faqCategoryAction($slug)
    {
        if (null === $category = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:FaqCategory')->findOneBy(['slug' => $slug])) {
            throw new HttpException(404);
        }

        return [
            'slug' => $slug,
            'category' => $category,
            'faqs' => $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Faq')->findBy(['category' => $category], ['title' => 'ASC']),
            'faqsByCategory' => $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:FaqCategory')->findFaqsByCategory(),
        ];
    }

    /**
     * Présentation de l'INP de Bordeaux
     *
     * @return array
     * @Route("/inp-bordeaux")
     * @Method("GET")
     * @Template()
     */
    public function inpbordeauxAction()
    {
        return [
            'inpbordeaux' => $this->get('adcog_default.static_content.static_manager')->getDefaultInpbordeaux(),
        ];
    }

    /**
     * Mentions
     *
     * @return array
     * @Route("/mentions-legales")
     * @Method("GET")
     * @Template()
     */
    public function mentionsAction()
    {
        return [];
    }

    /**
     * Mot de passe
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     * @Route("/mot-de-passe")
     * @Method("GET|POST")
     * @Template()
     */
    public function passeAction(Request $request)
    {
        $form = $this->createForm('adcog_user_forgotten');
        if ($form->handleRequest($request)->isValid()) {
            $username = $form->get('username')->getData();
            $user = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:User')->findOneBy(['username' => $username]);
            if (null == $user) {
                $session = $request->getSession();
                if ($session instanceof Session) {
                    $session->getFlashBag()->add('error', 'Désolé, cet utilisateur n\'existe pas !');
                }
            } else {
                $this->get('eb_email')->send('user_password_lost', $user, ['user' => $user]);

                $session = $request->getSession();
                if ($session instanceof Session) {
                    $session->getFlashBag()->add('success', 'Email envoyé avec succès !');
                }
            }

            return $this->redirect($this->generateUrl('default_passe'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Nouveau mot de passe
     *
     * @param Request $request Request
     * @param User    $user    User
     * @param string  $salt    Salt
     *
     * @return array|RedirectResponse
     * @throws NotFoundHttpException
     * @Route("/mot-de-passe/{user}-{salt}", requirements={"user":"\d+","salt":".*"})
     * @ParamConverter("user", class="AdcogDefaultBundle:User")
     * @Method("GET|POST")
     * @Template()
     */
    public function passeHelpAction(Request $request, User $user, $salt = null)
    {
        if ($salt !== $user->getSalt()) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm('adcog_user_password', $user);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            $token = new UsernamePasswordToken($user, $form->get('rawPassword')->getData(), 'secured_area');
            $this->get('security.token_storage')->setToken($token);

            return $this->redirect($this->generateUrl('user_index'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Présentation de l'association
     *
     * @return array
     * @Route("/presentation")
     * @Method("GET")
     * @Template()
     */
    public function presentationAction()
    {
        $staticManager = $this->get('adcog_default.static_content.static_manager');

        return [
            'offices' => $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Office')->getCurrent(),
            'organization' => $staticManager->getDefaultPresentationOrganization(),
            'list' => $staticManager->getDefaultPresentationList(),
            'office' => $staticManager->getDefaultPresentationOffice(),
        ];
    }

    /**
     * Profil
     *
     * @param string $slug
     *
     * @return array
     * @throws NotFoundHttpException
     * @Route("/presentation/{slug}")
     * @Method("GET")
     * @Template()
     */
    public function profileAction($slug)
    {
        if (null === $profile = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Profile')->findOneBy(['slug' => $slug])) {
            throw new NotFoundHttpException();
        }

        // Validated user
        if (false === $profile->getUser()->isValid()) {
            throw new NotFoundHttpException();
        }

        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginatorHelper->setLimit(null);
        $paginatorHelper->setOffset(null);
        $experiences = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Experience')->getPaginator($paginatorHelper, [
            'user' => $profile->getUser(),
            'isPublic' => true,
        ]);

        return [
            'profile' => $profile,
            'experiences' => $experiences,
        ];
    }

    /**
     * Inscription
     *
     * @param Request $request
     *
     * @return array
     * @Route("/inscription")
     * @Method("GET|POST")
     * @Template()
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm('adcog_user_register', $user = new User());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($user);
            $em->flush();

            $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);

            $request->getSession()
                ->getFlashBag()
                ->add('reussite', 'Inscription confirmée');
            return $this->redirect($this->generateUrl('user_index'));
        }

        $staticManager = $this->get('adcog_default.static_content.static_manager');

        return [
            'form' => $form->createView(),
            'go' => $staticManager->getDefaultRegisterGo(),
            'why' => $staticManager->getDefaultRegisterWhy(),
            'more' => $staticManager->getDefaultRegisterMore(),
        ];
    }
}
