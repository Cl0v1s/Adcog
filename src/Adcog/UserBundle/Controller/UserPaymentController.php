<?php

namespace Adcog\UserBundle\Controller;

use Adcog\DefaultBundle\Entity\PaymentPaypal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UserPaymentController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/reglement")
 */
class UserPaymentController extends Controller
{
    /**
     * Cancel payment and redirect
     *
     * @param PaymentPaypal $payment PaymentPaypal
     *
     * @return RedirectResponse
     * @throws NotFoundHttpException
     * @Route("/{payment}/cancel", requirements={"payment":"\d+"})
     * @ParamConverter("payment", class="Adcog\DefaultBundle\Entity\PaymentPaypal")
     * @Method("GET")
     */
    public function cancelAction(PaymentPaypal $payment)
    {
        if ($payment->getUser()->getId() !== $this->getUser()->getId()) {
            throw new NotFoundHttpException();
        }

        $this->get('adcog_default.payment.paypal')->cancel($payment);
        $this->get('session')->getFlashBag()->add('error', 'Votre demande d\'adhésion a été annulée.');

        return $this->redirect($this->generateUrl('user_subscribe_index'));
    }

    /**
     * Complete payment and redirect
     *
     * @param Request       $request
     * @param PaymentPaypal $payment
     *
     * @return RedirectResponse
     * @throws \Exception
     * @Route("/{payment}/complete", requirements={"payment":"\d+"})
     * @ParamConverter("payment", class="Adcog\DefaultBundle\Entity\PaymentPaypal")
     * @Method("GET")
     */
    public function completeAction(Request $request, PaymentPaypal $payment)
    {
        if ($payment->getUser()->getId() !== $this->getUser()->getId()) {
            throw new NotFoundHttpException();
        }
//        if ((null === $token = $request->query->get('token')) || $token !== $payment->getTokenId()) {
//            throw new NotFoundHttpException();
//        }
        if (null === $payerId = $request->query->get('PayerID')) {
            throw new NotFoundHttpException();
        }

        if (null === $this->get('adcog_default.payment.paypal')->complete($payment, $payerId)) {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur s\'est produite durant votre adhésion. Merci de nous contacter.');
        } else {
            $this->get('session')->getFlashBag()->add('success', 'Votre adhésion a bien été prise en compte.');
        }

        return $this->redirect($this->generateUrl('user_bill_index'));
    }
}
