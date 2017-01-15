<?php
/**
 * Created by PhpStorm.
 * User: Lolve
 * Date: 15/01/2017
 * Time: 23:43
 */

namespace Adcog\AdminBundle\Controller;

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

} 