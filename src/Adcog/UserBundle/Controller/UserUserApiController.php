<?php

namespace Adcog\UserBundle\Controller;

use Adcog\DefaultBundle\AdcogDefaultBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserUserApiController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/utilisateur/api")
 */
class UserUserApiController extends Controller
{
    /**
     * Zip
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/zip.{_format}", requirements={"_format":"json"}, defaults={"_format":"json"})
     * @Method("GET")
     */
    public function zipAction(Request $request)
    {
        $data = [];
        if (null !== $query = $request->query->get('query')) {
            if (AdcogDefaultBundle::WS_MIN_SIZE <= mb_strlen($query)) {
                $data = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:User')->autocomplete($query, 'zip');
            }
        }

        return new JsonResponse($data);
    }

    /**
     * City
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/city.{_format}", requirements={"_format":"json"}, defaults={"_format":"json"})
     * @Method("GET")
     */
    public function cityAction(Request $request)
    {
        $data = [];
        if (null !== $query = $request->query->get('query')) {
            if (AdcogDefaultBundle::WS_MIN_SIZE <= mb_strlen($query)) {
                $data = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:User')->autocomplete($query, 'city');
            }
        }

        return new JsonResponse($data);
    }

    /**
     * Country
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/country.{_format}", requirements={"_format":"json"}, defaults={"_format":"json"})
     * @Method("GET")
     */
    public function countryAction(Request $request)
    {
        $data = [];
        if (null !== $query = $request->query->get('query')) {
            if (AdcogDefaultBundle::WS_MIN_SIZE <= mb_strlen($query)) {
                $data = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:User')->autocomplete($query, 'country');
            }
        }

        return new JsonResponse($data);
    }

    /**
     * Tags WS
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/tags.{_format}", requirements={"_format":"json"}, defaults={"_format":"json"})
     * @Method("GET")
     */
    public function tagAction(Request $request)
    {
        $data = [];
        if (null !== $query = $request->query->get('query')) {
            if (AdcogDefaultBundle::WS_MIN_SIZE <= mb_strlen($query)) {
                $data = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Tag')->autocomplete($query);
            }
        }

        return new JsonResponse($data);
    }

    /**
     * Sectors
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/sectors.{_format}", requirements={"_format":"json"}, defaults={"_format":"json"})
     * @Method("GET")
     */
    public function sectorWsAction(Request $request)
    {

        $data = [];
        if (null !== $query = $request->query->get('query')) {
            if (AdcogDefaultBundle::WS_MIN_SIZE <= mb_strlen($query)) {
                $data = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Sector')->search($query);
            }
        }

        return new JsonResponse($data);
    }
}
