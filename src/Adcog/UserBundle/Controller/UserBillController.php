<?php

namespace Adcog\UserBundle\Controller;

use Adcog\DefaultBundle\Entity\Payment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class UserBillController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/factures")
 */
class UserBillController extends Controller
{
    /**
     * Index
     *
     * @return array
     * @Route()
     * @Method("GET|POST")
     * @Template()
     */
    public function indexAction()
    {
        return [
            'payments' => $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Payment')->findBy(['user' => $this->getUser()], ['created' => 'desc']),
        ];
    }
}
