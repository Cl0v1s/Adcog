<?php

namespace Adcog\ValidatorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class ValidatorController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ValidatorController extends Controller
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
        return $this->redirect($this->generateUrl('validator_comment_index'));
    }
}
