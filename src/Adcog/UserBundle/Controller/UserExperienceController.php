<?php

namespace Adcog\UserBundle\Controller;

use Adcog\DefaultBundle\Entity\Experience;
use Adcog\DefaultBundle\Entity\ExperienceInternship;
use Adcog\DefaultBundle\Entity\ExperienceStudy;
use Adcog\DefaultBundle\Entity\ExperienceThesis;
use Adcog\DefaultBundle\Entity\ExperienceWork;
use Adcog\DefaultBundle\Entity\Employer;
use Adcog\DefaultBundle\Functions\StringFunc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adcog\UserBundle\LinkedIn\LinkedIn;
use DateTime;

/**
 * Class UserExperienceController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/cursus")
 */
class UserExperienceController extends Controller
{
    /**
     * @Route()
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Experience')->getPaginator($paginatorHelper, ['user' => $this->getUser()]);

        return [
            'paginator' => $paginator,
        ];
    }

    /**
     * Create work
     *
     * @param Request $request
     *
     * @return array
     * @Route("/ajouter-experience-professionnelle")
     * @Method("GET|POST")
     * @Template()
     */
    public function createWorkAction(Request $request)
    {
        $experience = new ExperienceWork();
        $experience->setUser($this->getUser());
        $form = $this->createForm('adcog_experience_work', $experience);
        if ($form->handleRequest($request)->isValid()) {
            $this->checkEmployerDoubles($experience);
            $this->checkSectorsDoubles($experience);
            $this->checkCountryDoubles($experience);
            $this->checkCityDoubles($experience);
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($experience);
            $em->flush();

            return $this->redirect($this->generateUrl('user_experience_index'));
        }

        return [
            'experience' => $experience,
            'form' => $form->createView(),
        ];
    }

    /**
     * Create strudy
     *
     * @param Request $request
     *
     * @return array
     * @Route("/ajouter-diplome")
     * @Method("GET|POST")
     * @Template()
     */
    public function createStudyAction(Request $request)
    {
        $experience = new ExperienceStudy();
        $experience->setUser($this->getUser());
        $form = $this->createForm('adcog_experience_study', $experience);
        if ($form->handleRequest($request)->isValid()) {
            $this->checkEmployerDoubles($experience);
            $this->checkSectorsDoubles($experience);
            $this->checkCountryDoubles($experience);
            $this->checkCityDoubles($experience);
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($experience);
            $em->flush();

            return $this->redirect($this->generateUrl('user_experience_index'));
        }

        return [
            'experience' => $experience,
            'form' => $form->createView(),
        ];
    }

    /**
     * Create thesis
     *
     * @param Request $request
     *
     * @return array
     * @Route("/ajouter-these")
     * @Method("GET|POST")
     * @Template()
     */
    public function createThesisAction(Request $request)
    {
        $experience = new ExperienceThesis();
        $experience->setUser($this->getUser());
        $form = $this->createForm('adcog_experience_thesis', $experience);
        if ($form->handleRequest($request)->isValid()) {
            $this->checkEmployerDoubles($experience);
            $this->checkSectorsDoubles($experience);
            $this->checkCountryDoubles($experience);
            $this->checkCityDoubles($experience);
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($experience);
            $em->flush();

            return $this->redirect($this->generateUrl('user_experience_index'));
        }

        return [
            'experience' => $experience,
            'form' => $form->createView(),
        ];
    }

    /**
     * Create
     *
     * @param Request $request
     *
     * @return array
     * @Route("/ajouter-stage")
     * @Method("GET|POST")
     * @Template()
     */
    public function createInternshipAction(Request $request)
    {
        $experience = new ExperienceInternship();
        $experience->setUser($this->getUser());
        $form = $this->createForm('adcog_experience_internship', $experience);
        if ($form->handleRequest($request)->isValid()) {
            $this->checkEmployerDoubles($experience);
            $this->checkSectorsDoubles($experience);
            $this->checkCountryDoubles($experience);
            $this->checkCityDoubles($experience);
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($experience);
            $em->flush();

            return $this->redirect($this->generateUrl('user_experience_index'));
        }

        return [
            'experience' => $experience,
            'form' => $form->createView(),
        ];
    }

    /**
     * Update
     *
     * @param Request    $request    Request
     * @param Experience $experience Experience
     *
     * @return array
     * @throws NotFoundHttpException
     * @Route("/{experience}/modifier", requirements={"experience":"\d+"})
     * @ParamConverter("experience", class="AdcogDefaultBundle:Experience")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, Experience $experience)
    {
        if ($experience->getUser()->getId() !== $this->getUser()->getId()) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(sprintf('adcog_experience_%s', $experience->getType()), $experience);
        if ($form->handleRequest($request)->isValid()) {
            $this->checkEmployerDoubles($experience);
            $this->checkSectorsDoubles($experience);
            $this->checkCountryDoubles($experience);
            $this->checkCityDoubles($experience);
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($experience);
            $em->flush();

            return $this->redirect($this->generateUrl('user_experience_index'));
        }

        return [
            'experience' => $experience,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request    $request    Request
     * @param Experience $experience Experience
     *
     * @return array
     * @throws NotFoundHttpException
     * @Route("/{experience}/supprimer", requirements={"experience":"\d+"})
     * @ParamConverter("experience", class="AdcogDefaultBundle:Experience")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, Experience $experience)
    {
        if ($experience->getUser()->getId() !== $this->getUser()->getId()) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($experience);
            $em->flush();

            return $this->redirect($this->generateUrl('user_experience_index'));
        }

        return [
            'experience' => $experience,
            'form' => $form->createView(),
        ];
    }

    
    /**
     * @param Request    $request    Request
     * @return array

     * @Route("/import-linkedin")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateFromLinkedinAction(Request $request)
    {
        $callback = $this->generateUrl("user_experience_update_from_linkedin", array(), true);

        if ($container->has_paramaeter('linkedin_id') && $container->has_paramaeter('linkedin_secret'))
        {
            $client_id = $container->getParameter('linkedin_id');
            $client_secret = $container->getParameter('linkedin_secret');
        }
        else throw new Exception("parameters linkedin_id and linkedin_secret must be defined");

        

        $li = new linkedIn(array(
                'api_key' => $client_id,
                'api_secret' => $client_secret,
                'callback_url' => $callback,
                )
            );

        if (!isset($_SESSION['access_token'])) //ajouter si le token est expiré
        { 


            $code = $request->query->get('code');
            $state = $request->query->get('state');

            $accessToken = $li->getAccessToken($code);
            $accessTokenExpire = $li->getAccessTokenExpiration();

            $_SESSION['access_token'] = $accessToken;
            $_SESSION['access_token_expire'] = $accessTokenExpire;


        }
        $li->setAccessToken($_SESSION['access_token']);

        
        //beaucoup des champs liés à la companie ne sont accessible que si la personne est administratrice de la page de la companie.
        $datas = $li->get('/people/~:(positions:(title,summary,start-date,end-date,location,company:(name)),industry,location)');
        //        $datas = $li->get('/people/~:(positions:(title,summary,start-date,end-date,location,company:(name,website-url,specialties,email-domains,locations:(address:(city,state,postal-code,street1,street2),contact-info:(phone1)))),industry,location)');

        if (isset($datas['positions']['values']))
        { 
            $lp = $datas['positions']['values'][0];

            //on créé un formulaire pré-rempli avec les données extraites de LinkedIn

            $experience = new ExperienceWork();
            $experience->setWorkPosition($lp['title']);
            $experience->setStarted(new DateTime($lp['startDate']['year']."-".$lp['startDate']['month']));
            
            $experience->setDescription($lp['summary']);
            $experience->setUser($this->getUser());

            $employer = new Employer();
            $emp = $lp['company'];
            $employer->setName($emp['name']);

            /*if (isset($datas['industry']))
            {
                $experience->addSector($datas['industry']);
            }*/

            if (isset($datas['location']['name']) && $location = explode(',', $datas['location']['name']))
            {
                if (isset($location[1])) 
                    $employer->setCountry(trim($location[1]));
                $employer->setCity(trim(substr($location[0], 0, -5)));
            }
            
            $experience->setEmployer($employer);
            

            $form = $this->createForm('adcog_experience_work', $experience);
            if ($form->handleRequest($request)->isValid()) 
            {
                $this->checkEmployerDoubles($experience);
                $this->checkSectorsDoubles($experience);
                $this->checkCountryDoubles($experience);
                $this->checkCityDoubles($experience);
                $em = $this->get('doctrine.orm.default_entity_manager');
                $em->persist($experience);
                $em->flush();

                return $this->redirect($this->generateUrl('user_experience_index'));
            }

            $this->get('session')->getFlashBag()->add('success', 'Données récupérées depuis votre compte LinkedIn');

            return [
                'experience' => $experience,
                'form' => $form->createView(),
                'toBeDumped' => $datas['positions']['values'],
            ];  
        }
        else 
        {
            $this->get('session')->getFlashBag()->add('error', 'Nous n\'avons pas pu trouver votre emploi actuel sur LinkedIn');
            return $this->redirect(generateUrl("user_experience_create_work"));   
        }
    }

    /**
     * @param Request    $request    Request
     * @return array

     * @Route("/request-code")
     * @Method("GET|POST")
     * @Template()
     */

    public function requestAuthorizationCodeAction(Request $request)
    {
        $client_id = '775rlmtewci542';
        $client_secret = '57laN2m5Go1jw1Mx';
        $callBack = $this->generateUrl("user_experience_update_from_linkedin", array(), true);

        $li = new linkedIn(array(
            'api_key' => $client_id,
            'api_secret' => $client_secret,
            'callback_url' => $callBack,
            'curl_option' => array(
                CURLOPT_SSL_VERIFYPEER => false,
                ),
            )
        );

        $url = $li->getLoginUrl(
          array(
            LinkedIn::SCOPE_BASIC_PROFILE,
          )
        );

        return $this->redirect($url);
    }

    private function checkEmployerDoubles(Experience $experience)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $strfunc = $this->get('adcog.functions.string');
        $empRep = $em->getRepository('AdcogDefaultBundle:Employer');
        //at this place, we do not make a case insensitive or accent insensitive search. However, the utf8_unicode_ci interclassment makes so that it is ignored.
        $employers = $empRep->findByName($experience->getEmployer()->getName());
        foreach($employers as $oneEmployer)
        {
            if (strtolower($strfunc->remove_accents($oneEmployer->getName())) == strtolower($strfunc->remove_accents($experience->getEmployer()->getName())))
            {
                $experience->setEmployer($oneEmployer);
                return true;
            }
        }
        return false;
    }

    private function checkSectorsDoubles(Experience $experience)
    {
        $nbOfSectorsReplaced = 0;
        $em = $this->get('doctrine.orm.default_entity_manager');
        $strfunc = $this->get('adcog.functions.string');
        $sectRep = $em->getRepository('AdcogDefaultBundle:Sector');
        $expSectors = $experience->getSectors();
        foreach($expSectors as $sectNumber => $oneExpSector)
        {
            $sectors = $sectRep->findByName($oneExpSector->getName());
            foreach($sectors as $oneSector)
            {
                if (strtolower($strfunc->remove_accents($oneSector->getName())) == strtolower($strfunc->remove_accents($oneExpSector->getName())))
                {
                    $expSectors[$sectNumber] = $oneSector;
                    $nbOfSectorsReplaced++;
                    break;
                }
            }
        }
        return $nbOfSectorsReplaced;
    }

    private function checkCountryDoubles(Experience $experience)
    {
        $strfunc = $this->get('adcog.functions.string');
        $empRep = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Employer');
        //at this place, we do not make a case insensitive or accent insensitive search. However, the utf8_unicode_ci interclassment makes so that it is ignored.
        $employers = $empRep->findByCountry($experience->getEmployer()->getCountry());
        foreach($employers as $oneEmployer)
        {
            if (strtolower($strfunc->remove_accents($oneEmployer->getCountry())) == strtolower($strfunc->remove_accents($experience->getEmployer()->getCountry())))
            {
                $experience->getEmployer()->setCountry($oneEmployer->getCountry());                
                return true;
            }
        }
        return false;
    }
    private function checkCityDoubles(Experience $experience)
    {
        $strfunc = $this->get('adcog.functions.string');
        $empRep = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Employer');
        //at this place, we do not make a case insensitive or accent insensitive search. However, the utf8_unicode_ci interclassment makes so that it is ignored.
        $employers = $empRep->findByCity($experience->getEmployer()->getCity());
        foreach($employers as $oneEmployer)
        {
            if (strtolower($strfunc->remove_accents($oneEmployer->getCity())) == strtolower($strfunc->remove_accents($experience->getEmployer()->getCity())))
            {
                $experience->getEmployer()->setCity($oneEmployer->getCity());
                return true;
            }
        }
        return false;
    }
}
