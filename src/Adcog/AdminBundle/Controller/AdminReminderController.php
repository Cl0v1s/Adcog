<?php
/**
 * Created by PhpStorm.
 * User: Lolve
 * Date: 15/01/2017
 * Time: 23:43
 */

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\Reminder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminReminderController
 *
 * @author "Nicolas Drufin" <nicolas.drufin@ensc.fr>
 * @Route("/reminder")
 */
class AdminReminderController extends Controller {
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
        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Reminder')->getPaginator($paginatorHelper, []);

        return [
            'paginator' => $paginator,
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
        $form = $this->createForm('adcog_admin_reminder', $reminder = new Reminder());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($reminder);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_reminder_read', ['price' => $reminder->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param Reminder $reminder Reminder
     *
     * @return array
     * @Route("/{reminder}/read", requirements={"reminder":"\d+"})
     * @ParamConverter("reminder", class="AdcogDefaultBundle:Reminder")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Reminder $reminder)
    {
        return [
            'reminder' => $reminder,
        ];
    }

} 