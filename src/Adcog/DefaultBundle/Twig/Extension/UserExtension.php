<?php

namespace Adcog\DefaultBundle\Twig\Extension;

use Adcog\DefaultBundle\Entity\Experience;
use Adcog\DefaultBundle\Entity\Payment;
use Adcog\DefaultBundle\Entity\School;
use Adcog\DefaultBundle\Entity\User;

/**
 * Class UserExtension
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class UserExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $months = [
        'janvier',
        'février',
        'mars',
        'avril',
        'mai',
        'juin',
        'juillet',
        'aôut',
        'septembre',
        'octobre',
        'novembre',
        'décembre',
    ];

    /**
     * @param string $name Extension name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return [
            'USER_ROLE_USER' => User::ROLE_USER,
            'USER_ROLE_USER_VALIDATED' => User::ROLE_USER_VALIDATED,
            'USER_ROLE_ADMIN' => User::ROLE_ADMIN,
            'USER_ROLE_VALIDATOR' => User::ROLE_VALIDATOR,
            'USER_ROLE_BLOGGER' => User::ROLE_BLOGGER,
            'USER_ROLE_MEMBER' => User::ROLE_MEMBER,
            'PAYMENT_TYPE_CASH' => Payment::TYPE_CASH,
            'PAYMENT_TYPE_CHECK' => Payment::TYPE_CHECK,
            'PAYMENT_TYPE_TRANSFER' => Payment::TYPE_TRANSFER,
            'PAYMENT_TYPE_PAYPAL' => Payment::TYPE_PAYPAL,
            'EXPERIENCE_TYPE_STUDY' => Experience::TYPE_STUDY,
            'EXPERIENCE_TYPE_WORK' => Experience::TYPE_WORK,
            'EXPERIENCE_TYPE_INTERNSHIP' => Experience::TYPE_INTERNSHIP,
            'EXPERIENCE_TYPE_THESIS' => Experience::TYPE_THESIS,
            'table_class' => 'table table-striped table-bordered table-hover table-condensed',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('user_profile', [$this, 'getUserProfile']),
            new \Twig_SimpleFilter('school_profile', [$this, 'getSchoolProfile']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('strftime', 'strftime'),
            new \Twig_SimpleFunction('month', [$this, 'getFrMonth']),
        ];
    }

    /**
     * Get user profile
     *
     * @param null|User $user
     *
     * @return string
     */
    public function getUserProfile(User $user = null)
    {
        $default = '/bundles/adcogdefault/img/default-user.jpg';
        if (null === $user) {
            return $default;
        }
        if (null === $uri = $user->getUri()) {
            return $default;
        }
        if (false === file_exists($user->getPath())) {
            return $default;
        }

        return $uri;
    }

    /**
     * Get school profile
     *
     * @param School $school
     *
     * @return string
     */
    public function getSchoolProfile(School $school)
    {
        $default = '/bundles/adcogdefault/img/default-school.jpg';
        if (null === $uri = $school->getUri()) {
            return $default;
        }
        if (false === file_exists($school->getPath())) {
            return $default;
        }

        return $uri;
    }

    /**
     * Get FR month
     *
     * @param \DateTime $date
     *
     * @return string
     */
    public function getFrMonth(\DateTime $date)
    {
        return $this->months[intval($date->format('m')) - 1];
    }
}
