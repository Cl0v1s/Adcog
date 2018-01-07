<?php

namespace Adcog\DefaultBundle\Entity;

use Adcog\DefaultBundle\Entity\Common\ValidatedTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use EB\DoctrineBundle\Entity\LoggableInterface;
use EB\DoctrineBundle\Entity\CreatedInterface;
use EB\DoctrineBundle\Entity\Doctrine\CreatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\FileReadableTrait;
use EB\DoctrineBundle\Entity\Doctrine\FileTrait;
use EB\DoctrineBundle\Entity\Doctrine\FileVersionableTrait;
use EB\DoctrineBundle\Entity\Doctrine\SlugTrait;
use EB\DoctrineBundle\Entity\Doctrine\UpdatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\UserAdvancedTrait;
use EB\DoctrineBundle\Entity\Doctrine\UserLoginTrait;
use EB\DoctrineBundle\Entity\Doctrine\UserPasswordDateTrait;
use EB\DoctrineBundle\Entity\Doctrine\UserTrait;
use EB\DoctrineBundle\Entity\FileReadableInterface;
use EB\DoctrineBundle\Entity\FileVersionableInterface;
use EB\DoctrineBundle\Entity\SlugInterface;
use EB\DoctrineBundle\Entity\UpdatedInterface;
use EB\DoctrineBundle\Entity\UserInterface;
use EB\DoctrineBundle\Entity\UserLoginInterface;
use EB\DoctrineBundle\Entity\UserPasswordDateInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\UserRepository")
 * @ORM\Table(name="adcog_user")
 * @DoctrineAssert\UniqueEntity(fields={"username"})
 */
class User implements AdvancedUserInterface, UserInterface, UserLoginInterface, UserPasswordDateInterface, CreatedInterface, UpdatedInterface, \Serializable, LoggableInterface, FileReadableInterface, FileVersionableInterface, SlugInterface
{

    

    use UserTrait;
    use UserLoginTrait;
    use UserPasswordDateTrait;
    use UserAdvancedTrait;
    use CreatedTrait;
    use UpdatedTrait;
    use FileReadableTrait;
    use FileTrait;
    use FileVersionableTrait;
    use SlugTrait;
    use ValidatedTrait;

    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_BLOGGER = 'ROLE_BLOGGER';
    const ROLE_MEMBER = 'ROLE_MEMBER';
    const ROLE_USER = 'ROLE_USER';
    const ROLE_USER_SCHOOL = 'ROLE_USER_SCHOOL_%u';
    const ROLE_USER_VALIDATED = 'ROLE_USER_VALIDATED';
    const ROLE_VALIDATOR = 'ROLE_VALIDATOR';

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max="255", min="2")
     */
    private $firstname;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max="255", min="2")
     */
    private $lastname;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $termsAccepted;

    /**
     * @var null|string
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Choice(callback="getRoleList")
     */
    private $role;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(type="string")
     */
    private $address;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $zip;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    private $website;

    /**
     * @var Experience[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Experience", mappedBy="user")
     */
    private $experiences;

    /**
     * @var null|School
     * @ORM\ManyToOne(targetEntity="School", inversedBy="users")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $school;

    /**
     * @var Payment[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="user")
     */
    private $payments;

    /**
     * @var Comment[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="author")
     */
    private $comments;

    /**
     * @var null|Profile
     * @ORM\OneToOne(targetEntity="Profile", mappedBy="user", cascade={"persist","remove"})
     */
    private $profile;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nationality;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gender;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $birthDate;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $acceptedContact = false;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->experiences = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->setEnabled(true);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('%s %s', $this->getFirstname(), mb_strtoupper($this->getLastname(), 'UTF-8'));
    }

    /**
     * @return string[]
     */
    public static function getRoleList()
    {
        return [
            self::ROLE_VALIDATOR,
            self::ROLE_BLOGGER,
            self::ROLE_ADMIN,
        ];
    }

    /**
     * @return string[]
     */
    public static function getRoleNameList()
    {
        return [
            self::ROLE_VALIDATOR => 'Validateur',
            self::ROLE_BLOGGER => 'Blogger',
            self::ROLE_ADMIN => 'Administrateur',
        ];
    }

    /**
     * Add comments
     *
     * @param Comment $comment
     *
     * @return User
     */
    public function addComment(Comment $comment)
    {
        if (false === $this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    /**
     * Add experience
     *
     * @param Experience $experience
     *
     * @return User
     */
    public function addExperience(Experience $experience)
    {
        if (false === $this->experiences->contains($experience)) {
            $this->experiences[] = $experience;
            $experience->setUser($this);
        }

        return $this;
    }

    /**
     * Add payment
     *
     * @param Payment $payment
     *
     * @return User
     */
    public function addPayment(Payment $payment)
    {
        if (false === $this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setUser($this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->rawPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return array_filter([
            // User
            self::ROLE_USER,
            // Validated user
            null === $this->getValidated() ? null : self::ROLE_USER_VALIDATED,
            // Valid member
            $this->isMember() ? self::ROLE_MEMBER : null,
            // School
            null === $this->getSchool() ? null : sprintf(self::ROLE_USER_SCHOOL, $this->getSchool()->getYear()),
            // Admin, Blogger, Validator
            $this->getRole(),
        ]);
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get comments
     *
     * @return Comment[]|ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    

    /**
     * Get experiences
     *
     * @return Experience[]|ArrayCollection
     */
    public function getExperiences()
    {
        return $this->experiences;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }
    
    /**
     * Get Last payment expiration date
     *
     * @return null|\DateTime
     */
    public function getLastPaymentEnded()
    {
        $array_date = array();
        
        // Verify all payment
        foreach ($this->getPayments() as $payment) {
            if (true === $payment->isActive()) {
                return $payment->getEnded();
            } else {
                $array_date[] = $payment->getEnded();
            }
        }

        // get max payment
        if (!empty($array_date)) {
            return max($array_date);
        }
        return null;
    }

    /**
     * Member
     *
     * @return null|\DateTime
     */
    public function getMembered()
    {
        if (null !== $school = $this->getSchool()) {
            $limit = \DateTime::createFromFormat('Ymd', sprintf('%s0901', $school->getYear()));
            if ($limit->getTimestamp() > time()) {
                return $limit;
            }
        }

        foreach ($this->getPayments() as $payment) {
            if (true === $payment->isActive()) {
                return $payment->getEnded();
            }
        }

        return null;
    }

    /**
     * Get payments
     *
     * @return Payment[]|ArrayCollection
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get Profile
     *
     * @return null|Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set Profile
     *
     * @param null|Profile $profile Profile
     *
     * @return User
     */
    public function setProfile(Profile $profile = null)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get Role
     *
     * @return null|string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set Role
     *
     * @param null|string $role Role
     *
     * @return User
     */
    public function setRole($role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get school
     *
     * @return null|School
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * Set school
     *
     * @param null|School $school
     *
     * @return User
     */
    public function setSchool(School $school = null)
    {
        if (null === $school) {
            $this->school->removeUser($this);
        } else {
            $school->addUser($this);
        }
        $this->school = $school;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStringToSlug()
    {
        return (string)$this;
    }

    /**
     * Get TermsAccepted
     *
     * @return \DateTime
     */
    public function getTermsAccepted()
    {
        return $this->termsAccepted;
    }

    /**
     * Set TermsAccepted
     *
     * @param \DateTime $termsAccepted TermsAccepted
     *
     * @return User
     */
    public function setTermsAccepted(\DateTime $termsAccepted = null)
    {
        $this->termsAccepted = $termsAccepted;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set website
     *
     * @param string $website
     *
     * @return User
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return User
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return
            self::ROLE_ADMIN === $this->getRole();
    }

    /**
     * Blogger
     *
     * @return bool
     */
    public function isBlogger()
    {
        return
            self::ROLE_BLOGGER === $this->getRole() ||
            $this->isAdmin();
    }

    /**
     * Member
     *
     * @return bool
     */
    public function isMember()
    {
        return null !== $this->getMembered();
    }

    /**
     * Validator
     *
     * @return bool
     */
    public function isValidator()
    {
        return
            self::ROLE_VALIDATOR === $this->getRole() ||
            $this->isBlogger();
    }

    /**
     * Remove comments
     *
     * @param Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        if (true === $this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            $comment->setAuthor(null);
        }
    }

    /**
     * Remove experience
     *
     * @param Experience $experience
     *
     * @return User
     */
    public function removeExperience(Experience $experience)
    {
        if (true === $this->experiences->contains($experience)) {
            $this->experiences->removeElement($experience);
            $experience->setUser(null);
        }

        return $this;
    }

    /**
     * Remove payment
     *
     * @param Payment $payment
     *
     * @return User
     */
    public function removePayment(Payment $payment)
    {
        if (true === $this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            $payment->setUser(null);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([$this->id, $this->username, $this->password, $this->salt]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list ($this->id, $this->username, $this->password, $this->salt) = unserialize($serialized);
    }


    public function toArray() 
    {
        $result = get_object_vars($this);
        foreach ((array)$this as $key => $value) 
        {
            $key = trim($key);
        }

        return $result;
    }
    /*Changement apporté Emile et Thomas*/ 

    /**
     * Get nationality
     *
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set nationality
     *
     * @param string $nationality
     *
     * @return User
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }
    
    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get BirthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set 
     *
     * @param \DateTime $birthDate BirthDate
     *
     * @return User
     */
    public function setBirthDate(\DateTime $birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get AcceptedContact
     *
     * @return bool
     */
    public function getAcceptedContact()
    {
        return $this->acceptedContact;
    }

    /**
     * Set AcceptedContact
     *
     * @param bool $acceptedContact AcceptedContact
     *
     * @return User
     */
    public function setAcceptedContact($acceptedContact)
    {
        $this->acceptedContact = $acceptedContact;

        return $this;
    }
}
