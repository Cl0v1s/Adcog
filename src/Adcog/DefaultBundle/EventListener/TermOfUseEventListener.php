<?php

namespace Adcog\DefaultBundle\EventListener;

use Adcog\DefaultBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class TermOfUseEventListener
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class TermOfUseEventListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var string[]
     */
    private $userValidatedRoutePrefixes = [
        'user_bill_',
        'user_bookmark_',
        'user_profile_',
        'user_subscribe_',
    ];

    /**
     * @var string[]
     */
    private $userInvalidRoutePrefixes = [
        'user_',
        'member_',
        'validator_',
        'blogger_',
        'admin_',
    ];

    /**
     * @param TokenStorageInterface $tokenStorage Token storage
     * @param FormFactoryInterface  $formFactory  Form factory
     * @param RouterInterface       $router       Router
     * @param EntityManager         $em           Entity manager
     * @param EngineInterface       $templating   Templating
     */
    public function __construct(TokenStorageInterface $tokenStorage, FormFactoryInterface $formFactory, RouterInterface $router, EntityManager $em, EngineInterface $templating)
    {
        $this->tokenStorage = $tokenStorage;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->em = $em;
        $this->templating = $templating;
    }

    /**
     * On kernel request
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            if (null !== $token = $this->tokenStorage->getToken()) {
                if (null !== $user = $token->getUser()) {
                    if ($user instanceof User) {
                        // Ensure that terms are accepted
                        if (null === $user->getTermsAccepted()) {
                            $form = $this->formFactory->create('adcog_user_terms', $user, [
                                'action' => $this->router->generate('user_account_update'),
                            ]);
                            if ($form->handleRequest($event->getRequest())->isValid()) {
                                $this->em->flush();

                                $event->setResponse(new RedirectResponse($this->router->generate('user_account_index')));
                            } else {
                                $event->setResponse(new Response($this->templating->render('AdcogDefaultBundle:Default:terms.html.twig', [
                                    'form' => $form->createView(),
                                ])));
                            }
                        }

                        // Get current route
                        $route = $event->getRequest()->attributes->get('_route');

                        // Ensure that user is validated
                        if (false === $user->isValid()) {
                            foreach ($this->userValidatedRoutePrefixes as $routeSuffix) {
                                if (0 === mb_strpos($route, $routeSuffix)) {
                                    $event->setResponse(new Response($this->templating->render('AdcogUserBundle:User:notValidated.html.twig')));
                                }
                            }
                        }

                        // If user is invalid
                        if ($user->hasBeenValidated() && false === $user->isValid()) {
                            foreach ($this->userInvalidRoutePrefixes as $routeSuffix) {
                                if (0 === mb_strpos($route, $routeSuffix)) {
                                    $event->setResponse(new Response($this->templating->render('AdcogUserBundle:User:notValid.html.twig')));
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
