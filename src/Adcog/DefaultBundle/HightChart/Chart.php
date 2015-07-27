<?php

namespace Adcog\DefaultBundle\HightChart;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Chart
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class Chart extends ArrayCollection
{
    /**
     * @var string
     */
    private $title;

    /**
     * @param string $title
     */
    public function __construct($title)
    {
        $this->title = $title;
    }

    /**
     * Set Title
     *
     * @param string $title Title
     *
     * @return Chart
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * To array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'chart' => [
                'plotBackgroundColor' => null,
                'plotBorderWidth' => null,
                'plotShadow' => false,
            ],
            'title' => [
                'text' => $this->getTitle(),
            ],
            'tooltip' => [
                'pointFormat' => '<b>{point.percentage:.1f}%</b>',
            ],
            'plotOptions' => [
                'pie' => [
                    'allowPointSelect' => true,
                    'cursor' => 'pointer',
                    'dataLabels' => [
                        'enabled' => true,
                        'color' => '#000000',
                        'connectorColor' => '#000000',
                        'format' => '<b>{point.name}</b>: {point.percentage:.1f} %',
                    ],
                ],
            ],
            'series' => $this->getSeries(),
            'credits' => [
                'enabled' => false,
            ],
        ];
    }

    /**
     * Get series
     *
     * @return array
     */
    public function getSeries()
    {
        $data = [];
        foreach ($this as $key => $value) {
            if ($value instanceof SerieInterface) {
                $data[$key] = $value->toArray();
            }
        }

        return $data;
    }
}
