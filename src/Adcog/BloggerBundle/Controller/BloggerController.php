<?php

namespace Adcog\BloggerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class BloggerController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class BloggerController extends Controller
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
        return $this->redirect($this->generateUrl('blogger_slider_index'));
    }
}
