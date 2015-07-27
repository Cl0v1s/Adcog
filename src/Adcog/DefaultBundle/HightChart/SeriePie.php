<?php

namespace Adcog\DefaultBundle\HightChart;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SeriePie
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class SeriePie extends ArrayCollection implements SerieInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name Name
     * @param array  $data Data
     */
    function __construct($name, array $data = [])
    {
        $this->name = $name;
        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'type' => 'pie',
            'name' => $this->getName(),
            'data' => $this->getData(),
        ];
    }

    /**
     * getData
     *
     * @return array
     */
    public function getData()
    {
        $data = [];
        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Set Name
     *
     * @param string $name Name
     *
     * @return SeriePie
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
