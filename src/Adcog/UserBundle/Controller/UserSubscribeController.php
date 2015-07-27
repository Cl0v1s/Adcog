<?php

namespace Adcog\UserBundle\Controller;

use Adcog\DefaultBundle\Entity\Payment;
use Adcog\DefaultBundle\Entity\PaymentPaypal;
use Adcog\DefaultBundle\Entity\Price;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class UserSubscribeController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/adhesion")
 */
class UserSubscribeController extends Controller
{
    /**
     * Index
     *
     * @return array
     * @Route()
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        return [
            'prices' => $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Price')->findBy([], ['amount' => 'asc']),
        ];
    }

    /**
     * Price
     *
     * @param Price $price
     *
     * @return array
     * @Route("/{price}-{slug}", requirements={"price":"\d+"})
     * @Method("GET")
     * @Template()
     */
    public function modeAction(Price $price)
    {
        if (0 == $price->getAmount()) {
            return $this->redirect($this->generateUrl('user_subscribe_confirmation', [
                'price' => $price->getId(),
                'slug' => $price->getSlug(),
                'type' => Payment::TYPE_CASH,
            ]));
        }

        return [
            'price' => $price,
        ];
    }

    /**
     * Confirmation
     *
     * @param Request $request Request
     * @param Price   $price   Price
     * @param string  $type    What kind
     *
     * @return array
     * @throws NotFoundHttpException
     * @Route("/{price}-{slug}/{type}", requirements={"price":"\d+"})
     * @Method("GET|POST")
     * @Template()
     */
    public function confirmationAction(Request $request, Price $price, $type)
    {
        if (null === $payment = Payment::createObject($type, $price)) {
            throw new NotFoundHttpException();
        }
        $payment->setUser($this->getUser());

        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $errors = $this->get('validator')->validate($payment);
            if (0 === $errors->count()) {
                $em = $this->get('doctrine.orm.default_entity_manager');
                $em->persist($payment);
                $em->flush();

                if($payment instanceof PaymentPaypal && $payment->getAmount() > 0) {
                    if(null !== $url = $this->get('adcog_default.payment.paypal')->execute($payment)) {
                        return new RedirectResponse($url);
                    }
                }

                return $this->redirect($this->generateUrl('user_bill_index'));
            }
        }

        return [
            'price' => $price,
            'type' => $type,
            'payment' => $payment,
            'form' => $form->createView(),
        ];
    }
}
