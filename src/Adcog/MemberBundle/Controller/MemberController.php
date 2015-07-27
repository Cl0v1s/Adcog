<?php

namespace Adcog\MemberBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class MemberController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class MemberController extends Controller
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
        return $this->redirect($this->generateUrl('member_school_index'));
    }
}
