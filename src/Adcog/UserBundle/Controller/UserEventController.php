<?php

namespace Adcog\UserBundle\Controller;

use Adcog\DefaultBundle\Entity\Event;
use Adcog\DefaultBundle\Entity\EventParticipation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class UserEventController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/evenements")
 */
class UserEventController extends Controller
{
    /**
     * Event
     *
     * @return array
     * @Route()
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        return [
            'events' => $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Event')->findBy([], ['date' => 'desc']),
        ];
    }

    /**
     * Read
     *
     * @param Event  $event Event
     * @param string $slug  Slug
     *
     * @return array
     * @Route("/{event}-{slug}", requirements={"event":"\d+"})
     * @ParamConverter("event", class="AdcogDefaultBundle:Event")
     * @Method("GET")
     * @Template()
     */
    public function readAction(Event $event, $slug)
    {
        if ($slug !== $event->getSlug()) {
            return $this->redirect($this->generateUrl('user_event_read', [
                'event' => $event->getId(),
                'slug' => $event->getSlug(),
            ]));
        }

        return [
            'event' => $event,
        ];
    }

    /**
     * @param Request $request Request
     * @param Event   $event   Event
     *
     * @return array
     * @Route("/participer-a-{event}")
     * @ParamConverter("event", class="AdcogDefaultBundle:Event")
     * @Method("GET|POST")
     * @Template()
     */
    public function participateAction(Request $request, Event $event)
    {
        if ($event->getDate()->getTimestamp() < time() || null !== $event->getUserParticipation($this->getUser())) {
            return $this->redirect($this->generateUrl('user_event_read', [
                'event' => $event->getId(),
                'slug' => $event->getSlug(),
            ]));
        }

        $participation = new EventParticipation();
        $participation->setUser($this->getUser());
        $participation->setEvent($event);
        $form = $this->createForm('adcog_event_participate', $participation);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($participation);
            $em->flush();

            return $this->redirect($this->generateUrl('user_event_index'));
        }

        return [
            'event' => $event,
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Request $request Request
     * @param Event   $event   Event
     *
     * @return array|RedirectResponse
     * @throws NotFoundHttpException
     * @Route("/annuler-la-participation-a-{event}")
     * @ParamConverter("event", class="AdcogDefaultBundle:Event")
     * @Method("GET|POST")
     * @Template()
     */
    public function cancelAction(Request $request, Event $event)
    {
        if ($event->getDate()->getTimestamp() < time()) {
            return $this->redirect($this->generateUrl('user_event_read', [
                'event' => $event->getId(),
                'slug' => $event->getSlug(),
            ]));
        }
        if (null === $participation = $event->getUserParticipation($this->getUser())) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($participation);
            $em->flush();

            $session = $request->getSession();
            if ($session instanceof Session) {
                $session->getFlashBag()->add('success', 'Votre participation a été annulée ...');
            }

            return $this->redirect($this->generateUrl('user_event_index'));
        }

        return [
            'form' => $form->createView(),
            'event' => $event,
        ];
    }
}
