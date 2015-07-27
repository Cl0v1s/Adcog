<?php

namespace Adcog\DefaultBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Contact
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class Contact
{
    /**
     * Contact subjects
     *
     * @var array
     */
    static private $subjects = [
        'Renseignement sur l\'association',
        'Renseignement sur les anciens',
        'Renseignement sur l\'école',
        'Offre de stage',
        'Offre d\'emploi',
        'Autre'
    ];

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max=255, min=2)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(max=255)
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     */
    private $subject;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="15")
     */
    private $message;

    /**
     * @param null $key
     *
     * @return array
     * @throws \Exception
     */
    static public function getContactSubjects($key = null)
    {
        if ($key) {
            if (empty(self::$subjects[$key])) {
                throw new \Exception('This subject does not exist.');
            }

            return self::$subjects[$key];
        }

        return self::$subjects;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
}
