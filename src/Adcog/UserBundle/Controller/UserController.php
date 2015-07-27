<?php

namespace Adcog\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class UserController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route()
 */
class UserController extends Controller
{
    /**
     * User
     *
     * @return RedirectResponse
     * @Route()
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('user_account_index'));
    }

    /**
     * User is not yet validated
     *
     * @return array
     */
    public function notValidatedAction()
    {
        return [];
    }
}