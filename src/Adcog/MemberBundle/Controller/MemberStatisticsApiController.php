<?php

namespace Adcog\MemberBundle\Controller;

use Adcog\DefaultBundle\Entity\Experience;
use Adcog\DefaultBundle\HightChart\Chart;
use Adcog\DefaultBundle\HightChart\SeriePie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class MemberStatisticsApiController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/statistiques/api")
 */
class MemberStatisticsApiController extends Controller
{
    /**
     * Schools
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/promotions.{_format}", defaults={"_format":"json"}, requirements={"_format":"json"})
     * @Method("GET")
     */
    public function schoolsAction(Request $request)
    {
        $fetchEmpty = (bool)$request->query->get('empty', 0);
        $details = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:User')->getSchoolStatistics($fetchEmpty);
        $chart = new Chart('Présence sur le site par promotion');
        $chart->add($serie = new SeriePie('Promotions'));
        foreach ($details as $detail) {
            $name = (null === $detail['year'] || null === $detail['name']) ? 'Non renseigné' : sprintf('%s (%u)', $detail['name'], $detail['year']);
            $serie->add([$name, (int)$detail['user_count']]);
        }

        return new JsonResponse($chart->toArray());
    }

    /**
     * City Member
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/villes/membres.{_format}", defaults={"_format":"json"}, requirements={"_format":"json"})
     * @Method("GET")
     */
    public function cityMemberAction(Request $request)
    {
        $fetchEmpty = (bool)$request->query->get('empty', 0);
        $details = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:User')->getCityStatistics($fetchEmpty);
        $chart = new Chart('Ville de résidence des membres');
        $chart->add($serie = new SeriePie('Villes'));
        foreach ($details as $detail) {
            $name = null === $detail['city'] ? 'Non renseigné' : $detail['city'];
            $serie->add([$name, (int)$detail['city_count']]);
        }

        return new JsonResponse($chart->toArray());
    }

    /**
     * City Employer
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/villes/etablissements.{_format}", defaults={"_format":"json"}, requirements={"_format":"json"})
     * @Method("GET")
     */
    public function cityEmployerAction(Request $request)
    {
        $fetchEmpty = (bool)$request->query->get('empty', 0);
        $details = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Employer')->getCityStatistics($fetchEmpty);
        $chart = new Chart('Villes des établissements');
        $chart->add($serie = new SeriePie('Villes'));
        foreach ($details as $detail) {
            $name = null === $detail['city'] ? 'Non renseigné' : $detail['city'];
            $serie->add([$name, (int)$detail['city_count']]);
        }

        return new JsonResponse($chart->toArray());
    }

    /**
     * Country Member
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/pays/membres.{_format}", defaults={"_format":"json"}, requirements={"_format":"json"})
     * @Method("GET")
     */
    public function countryMemberAction(Request $request)
    {
        $fetchEmpty = (bool)$request->query->get('empty', 0);
        $details = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:User')->getCountryStatistics($fetchEmpty);
        $chart = new Chart('Pays de résidence des membres');
        $chart->add($serie = new SeriePie('Pays'));
        foreach ($details as $detail) {
            $name = null === $detail['country'] ? 'Non renseigné' : $detail['country'];
            $serie->add([$name, (int)$detail['country_count']]);
        }

        return new JsonResponse($chart->toArray());
    }

    /**
     * Country Employer
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/pays/etablissements.{_format}", defaults={"_format":"json"}, requirements={"_format":"json"})
     * @Method("GET")
     */
    public function countryEmployerAction(Request $request)
    {
        $fetchEmpty = (bool)$request->query->get('empty', 0);
        $details = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Employer')->getCountryStatistics($fetchEmpty);
        $chart = new Chart('Pays des établissements');
        $chart->add($serie = new SeriePie('Pays'));
        foreach ($details as $detail) {
            $name = null === $detail['country'] ? 'Non renseigné' : $detail['country'];
            $serie->add([$name, (int)$detail['country_count']]);
        }

        return new JsonResponse($chart->toArray());
    }

    /**
     * Salary First
     *
     * @return JsonResponse
     * @Route("/salaires/premier.{_format}", defaults={"_format":"json"}, requirements={"_format":"json"})
     * @Method("GET")
     */
    public function salaryFirstAction()
    {
        $experiences = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Experience')->getFirstExperiences();

        $chart = new Chart('Premier salaire');
        $chart->add($serie = new SeriePie('Salaires'));

        $details = [];
        foreach ($experiences as $experience) {
            if (false === array_key_exists($experience->getSalary(), $details)) {
                $details[$experience->getSalary()] = 0;
            }
            $details[$experience->getSalary()] += 1;
        }
        foreach ($details as $name => $detail) {
            $serie->add([Experience::getSalaryNameList()[$name], $detail]);
        }

        return new JsonResponse($chart->toArray());
    }

    /**
     * Salary Last
     *
     * @return JsonResponse
     * @Route("/salaires/dernier.{_format}", defaults={"_format":"json"}, requirements={"_format":"json"})
     * @Method("GET")
     */
    public function salaryLastAction()
    {
        $experiences = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Experience')->getLastExperiences();

        $chart = new Chart('Dernier salaire');
        $chart->add($serie = new SeriePie('Salaires'));

        $details = [];
        foreach ($experiences as $experience) {
            if (false === array_key_exists($experience->getSalary(), $details)) {
                $details[$experience->getSalary()] = 0;
            }
            $details[$experience->getSalary()] += 1;
        }
        foreach ($details as $name => $detail) {
            $serie->add([Experience::getSalaryNameList()[$name], $detail]);
        }

        return new JsonResponse($chart->toArray());
    }
}
