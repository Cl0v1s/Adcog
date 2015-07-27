<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\Comment;
use Adcog\DefaultBundle\Entity\Contact;
use Adcog\DefaultBundle\Entity\PaymentCash;
use Adcog\DefaultBundle\Entity\Post;
use Adcog\DefaultBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AdminController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class AdminController extends Controller
{
    /**
     * Admin
     *
     * @return RedirectResponse
     * @Route()
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('admin_user_index'));
    }

    /**
     * Email
     *
     * @param Request $request Request
     *
     * @return array
     * @Route("/email")
     * @Method("GET|POST")
     * @Template()
     */
    public function emailSendAction(Request $request)
    {
        $form = $this->createForm('adcog_admin_email');
        if ($form->handleRequest($request)->isValid()) {
            $users = $form->get('users')->getData()->toArray();
            $users = array_filter(array_merge($users, (array)$form->get('emails')->getData()));
            $this->get('eb_email')->send('email', $users, $form->getData());

            $session = $request->getSession();
            if ($session instanceof Session) {
                $session->getFlashBag()->add('success', 'Email envoyé avec succès.');
            }

            return $this->redirect($this->generateUrl('admin_email_send'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Email
     *
     * @param string $templateName Email template name
     *
     * @return Response
     * @throws NotFoundHttpException
     * @Route("/email-{templateName}", requirements={"templateName":"[a-z_-]+"})
     * @Method("GET")
     * @Template()
     */
    public function emailAction($templateName)
    {
        return [
            'templateName' => $templateName,
            'src' => $this->generateUrl('admin_email_render', ['templateName' => $templateName]),
        ];
    }

    /**
     * Email render
     *
     * @param string $templateName Email template name
     *
     * @return Response
     * @throws NotFoundHttpException
     * @Route("/email/render-{templateName}", requirements={"templateName":"[a-z_-]+"})
     * @Method("GET")
     */
    public function emailRenderAction($templateName)
    {
        $message = <<<EOF
This is a message with :
    - Comment    : <!-- COMMENT -->
    - A          : <a href="#">A</a>
    - ABBR       : <abbr title="ABBR">ABBR</abbr>
    - B          : <b>B</b>
    - BLOCKQUOTE : <blockquote>BLOCKQUOTE</blockquote>
    - CENTER     : <center>CENTER</center>
    - EM         : <em>EM</em>
    - H1         : <h1>H1</h1>
    - H2         : <h2>H2</h2>
    - H3         : <h3>H3</h3>
    - H4         : <h4>H4</h4>
    - H5         : <h5>H5</h5>
    - H6         : <h6>H6</h6>
    - HR         : <hr>
    - I          : <i>I</i>
    - SPAN       : <span>SPAN</span>
    - STRONG     : <strong>STRONG</strong>
    - U          : <u>U</u>

Do you see any Teletubbies in here? Do you see a slender plastic tag clipped to my shirt with my name printed on it? Do you see a little Asian child with a blank expression on his face sitting outside on a mechanical helicopter that shakes when you put quarters in it? No? Well, that's what you see at a toy store. And you must think you're in a toy store, because you're here shopping for an infant named Jeb.

Now that there is the Tec-9, a crappy spray gun from South Miami. This gun is advertised as the most popular gun in American crime. Do you believe that shit? It actually says that in the little book that comes with it: the most popular gun in American crime. Like they're actually proud of that shit.

The path of the righteous man is beset on all sides by the iniquities of the selfish and the tyranny of evil men. Blessed is he who, in the name of charity and good will, shepherds the weak through the valley of darkness, for he is truly his brother's keeper and the finder of lost children. And I will strike down upon thee with great vengeance and furious anger those who would attempt to poison and destroy My brothers. And you will know My name is the Lord when I lay My vengeance upon thee.

Normally, both your asses would be dead as fucking fried chicken, but you happen to pull this shit while I'm in a transitional period so I don't wanna kill you, I wanna help you. But I can't give you this case, it don't belong to me. Besides, I've already been through too much shit this morning over this case to hand it over to your dumb ass.

The path of the righteous man is beset on all sides by the iniquities of the selfish and the tyranny of evil men. Blessed is he who, in the name of charity and good will, shepherds the weak through the valley of darkness, for he is truly his brother's keeper and the finder of lost children. And I will strike down upon thee with great vengeance and furious anger those who would attempt to poison and destroy My brothers. And you will know My name is the Lord when I lay My vengeance upon thee.
EOF;

        $data = [];
        switch ($templateName) {
            case 'contact':
                $mail = new Contact();
                $mail->setEmail('no-reply@adcog.fr');
                $mail->setName('John DO');
                $mail->setSubject('This is a test message');
                $mail->setMessage($message);
                $data['mail'] = $mail;
                break;
            case 'email':
                $data['message'] = $message;
                break;
            case 'payment_persist':
                $payment = new PaymentCash();
                $payment->setUser($this->getUser());
                $payment->setId(999999);
                $payment->setDuration(99);
                $payment->setAmount(500);
                $payment->setTitle('This is a test payment');
                $payment->setDescription($message);

                $data['user'] = $this->getUser();
                $data['payment'] = $payment;
                break;
            case 'payment_persist_alert':
                $payment = new PaymentCash();
                $payment->setUser($this->getUser());
                $payment->setId(999999);
                $payment->setDuration(99);
                $payment->setAmount(500);
                $payment->setTitle('This is a test payment');
                $payment->setDescription($message);

                $data['user'] = $this->getUser();
                $data['payment'] = $payment;
                break;
            case 'payment_validate':
                $payment = new PaymentCash();
                $payment->setUser($this->getUser());
                $payment->setId(999999);
                $payment->setDuration(99);
                $payment->setAmount(500);
                $payment->setTitle('This is a test payment');
                $payment->setDescription($message);

                $data['user'] = $this->getUser();
                $data['payment'] = $payment;
                break;
            case 'payment_invalidate':
                $payment = new PaymentCash();
                $payment->setUser($this->getUser());
                $payment->setId(999999);
                $payment->setDuration(99);
                $payment->setAmount(500);
                $payment->setTitle('This is a test payment');
                $payment->setDescription($message);

                $data['user'] = $this->getUser();
                $data['payment'] = $payment;
                break;
            case 'user_persist':
                $data['user'] = $this->getUser();
                break;
            case 'user_persist_alert':
                $data['user'] = $this->getUser();
                break;
            case 'user_password_lost':
                $data['user'] = $this->getUser();
                break;
            case 'user_update_email':
                $data['user'] = $this->getUser();
                break;
            case 'user_update_password':
                $data['user'] = $this->getUser();
                break;
            case 'comment_persist_alert':
                $comment = new Comment();
                $comment
                    ->setId(999999)
                    ->setText($message)
                    ->setAuthor($user = new User())
                    ->setPost($post = new Post());

                $user
                    ->setFirstname('John')
                    ->setLastname('DOE');

                $post
                    ->setId(999999)
                    ->setTitle('Bogus post')
                    ->setSlug('bogus-post');

                $data['comment'] = $comment;
                break;
            default:
                throw new NotFoundHttpException();
        }

        return new Response($this->get('eb_email')->render($templateName, $data));
    }
}
