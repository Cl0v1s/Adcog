<?php

namespace Adcog\UserBundle\Controller;

use Adcog\DefaultBundle\AdcogDefaultBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class UserEmployerApiController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/etablissement/api")
 */
class UserEmployerApiController extends Controller
{
    /**
     * Name - Full employer
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/name.{_format}", requirements={"_format":"json"}, defaults={"_format":"json"})
     * @Method("GET")
     */
    public function nameAction(Request $request)
    {
        $data = [];
        if (null !== $query = $request->query->get('query')) {
            if (AdcogDefaultBundle::WS_MIN_SIZE_0 <= mb_strlen($query)) {
                $data = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Employer')->autocompleteEmployer($query);
            }
        }

        return new JsonResponse($data);
    }

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
                $data = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Employer')->autocomplete($query, 'zip');
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
                $data = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Employer')->autocomplete($query, 'city');
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
                $data = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Employer')->autocomplete($query, 'country');
            }
        }

        return new JsonResponse($data);
    }
}
