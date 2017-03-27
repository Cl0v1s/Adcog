<?php

namespace Adcog\UserBundle\Service;

use Adcog\DefaultBundle\Entity\Country;
use Adcog\DefaultBundle\Entity\City;
use Adcog\DefaultBundle\Entity\Employer;
use Adcog\DefaultBundle\Entity\Experience;
use Adcog\DefaultBundle\Entity\Sector;
use Adcog\DefaultBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Adcog\DefaultBundle\Functions\StringFunc;

class CheckDoubles
{
    protected $em; 
    protected $strfunc;

	public function checkEmployerDoubles(Experience $experience)
    {
        $empRep = $this->em->getRepository('AdcogDefaultBundle:Employer');
        //at this place, we do not make a case insensitive or accent insensitive search. However, the utf8_unicode_ci interclassment makes so that it is ignored.
        $employers = $empRep->findByName($experience->getEmployer()->getName());
        foreach($employers as $oneEmployer)
        {
            if (strtolower($this->strfunc->remove_accents($oneEmployer->getName())) == strtolower($this->strfunc->remove_accents($experience->getEmployer()->getName())))
            {
                $experience->setEmployer($oneEmployer);
                return true;
            }
        }
        return false;
    }

    public function checkSectorsDoubles(Experience $experience)
    {
        $nbOfSectorsReplaced = 0;
        $sectRep = $this->em->getRepository('AdcogDefaultBundle:Sector');
        $expSectors = $experience->getSectors();
        foreach($expSectors as $sectNumber => $oneExpSector)
        {
            $sectors = $sectRep->findByName($oneExpSector->getName());
            foreach($sectors as $oneSector)
            {
                if (strtolower($this->strfunc->remove_accents($oneSector->getName())) == strtolower($this->strfunc->remove_accents($oneExpSector->getName())))
                {
                    $expSectors[$sectNumber] = $oneSector;
                    $nbOfSectorsReplaced++;
                    break;
                }
            }
        }
        return $nbOfSectorsReplaced;
    }

    public function checkCountryDoubles(Experience $experience)
    {
        $empRep = $this->em->getRepository('AdcogDefaultBundle:Employer');
        //at this place, we do not make a case insensitive or accent insensitive search. However, the utf8_unicode_ci interclassment makes so that it is ignored.
        $employers = $empRep->findByCountry($experience->getEmployer()->getCountry());
        foreach($employers as $oneEmployer)
        {
            if (strtolower($this->strfunc->remove_accents($oneEmployer->getCountry())) == strtolower($this->strfunc->remove_accents($experience->getEmployer()->getCountry())))
            {
                $experience->getEmployer()->setCountry($oneEmployer->getCountry());                
                return true;
            }
        }
        return false;
    }
    public function checkCityDoubles(Experience $experience)
    {
        $empRep = $this->em->getRepository('AdcogDefaultBundle:Employer');
        //at this place, we do not make a case insensitive or accent insensitive search. However, the utf8_unicode_ci interclassment makes so that it is ignored.
        $employers = $empRep->findByCity($experience->getEmployer()->getCity());
        foreach($employers as $oneEmployer)
        {
            if (strtolower($this->strfunc->remove_accents($oneEmployer->getCity())) == strtolower($this->strfunc->remove_accents($experience->getEmployer()->getCity())))
            {
                $experience->getEmployer()->setCity($oneEmployer->getCity());
                return true;
            }
        }
        return false;
    }
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->strfunc = new StringFunc();
    }
}