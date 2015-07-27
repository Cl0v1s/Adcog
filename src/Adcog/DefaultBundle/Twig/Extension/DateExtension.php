<?php

namespace Adcog\DefaultBundle\Twig\Extension;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class DateExtension
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class DateExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param string              $name       Extension name
     * @param TranslatorInterface $translator Translator
     */
    public function __construct($name, TranslatorInterface $translator)
    {
        $this->name = $name;
        $this->translator = $translator;
    }

    /**
     * Get dateago structure
     *
     * @param null|string|\DateTime $date   Date
     * @param null|string           $format Format
     *
     * @return string
     */
    public function getDateAgo($date = null, $format = null)
    {
        if (null === $date = $this->createDateTime($date, $format)) {
            return $this->translator->trans('eb.date.never');
        }

        return sprintf(
            '<abbr title="%1$s" data-admin-date="%2$u">%1$s</abbr>',
            sprintf(
                '%02u %s %u',
                $date->format('d'),
                $this->translator->trans(sprintf('eb.date.months.%u', $date->format('m'))),
                $date->format('Y')
            ),
            $date->getTimestamp()
        );
    }

    /**
     * Get datediff structure
     *
     * @param string|\DateTime $started Date
     * @param string|\DateTime $ended   Date
     * @param null|string      $format  Format
     *
     * @return string
     */
    public function getDateDiff($started, $ended, $format = null)
    {
        if (null === $started = $this->createDateTime($started, $ended)) {
            return $this->translator->trans('eb.date.none');
        }
        if (null === $ended = $this->createDateTime($ended, $format)) {
            return $this->translator->trans('eb.date.none');
        }
        $diff = $started->diff($ended, true);

        return sprintf(
            '<abbr title="%s">%s</abbr>',
            sprintf(
                '%02u %s %u',
                $ended->format('d'),
                $this->translator->trans(sprintf('eb.date.months.%u', $ended->format('m'))),
                $ended->format('Y')
            ),
            implode(' ', array_filter([
                $this->translator->trans('eb.date.during'),
                0 !== $diff->y ? sprintf('%u %s', $diff->y, $this->translator->transChoice('eb.date.year', $diff->y)) : null,
                (0 !== $diff->y && 0 !== $diff->m) ? $this->translator->trans('eb.date.and') : null,
                0 !== $diff->m ? sprintf('%u %s', $diff->m, $this->translator->transChoice('eb.date.month', $diff->m)) : null,
            ]))
        );
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
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('dateago', [$this, 'getDateAgo'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('datediff', [$this, 'getDateDiff'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('timeago', [$this, 'getTimeAgo'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('timediff', [$this, 'getTimeDiff'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('is_past', [$this, 'isPast']),
            new \Twig_SimpleFunction('is_future', [$this, 'isFuture']),
        ];
    }

    /**
     * Get timeago structure
     *
     * @param null|string|\DateTime $date   Date
     * @param null|string           $format Format
     *
     * @return string
     */
    public function getTimeAgo($date = null, $format = null)
    {
        if (null === $date = $this->createDateTime($date, $format)) {
            return $this->translator->trans('eb.date.never');
        }

        return sprintf(
            '<abbr title="%1$s" data-admin-date="%2$u">%1$s</abbr>',
            sprintf(
                '%02u %s %u à %02u:%02u:%02u',
                $date->format('d'),
                $this->translator->trans(sprintf('eb.date.months.%u', $date->format('m'))),
                $date->format('Y'),
                $date->format('H'),
                $date->format('i'),
                $date->format('s')
            ),
            $date->getTimestamp()
        );
    }

    /**
     * Get timediff structure
     *
     * @param string|\DateTime $started Date
     * @param string|\DateTime $ended   Date
     * @param null|string      $format  Format
     *
     * @return string
     */
    public function getTimeDiff($started, $ended, $format = null)
    {
        if (null === $started = $this->createDateTime($started, $ended)) {
            return $this->translator->trans('eb.date.none');
        }
        if (null === $ended = $this->createDateTime($ended, $format)) {
            return $this->translator->trans('eb.date.none');
        }
        $diff = $started->diff($ended, true);

        return sprintf(
            '<abbr title="%s">%s</abbr>',
            sprintf(
                '%02u %s %u',
                $ended->format('d'),
                $this->translator->trans(sprintf('eb.date.months.%u', $ended->format('m'))),
                $ended->format('Y')
            ),
            implode(' ', array_filter([
                $this->translator->trans('eb.date.during'),
                0 !== $diff->y ? sprintf('%u %s', $diff->y, $this->translator->transChoice('eb.date.year', $diff->y)) : null,
                (0 !== $diff->y && 0 !== $diff->m) ? $this->translator->trans('eb.date.and') : null,
                0 !== $diff->m ? sprintf('%u %s', $diff->m, $this->translator->transChoice('eb.date.month', $diff->m)) : null,
                0 !== $diff->h ? sprintf('%u %s', $diff->h, $this->translator->transChoice('eb.date.hour', $diff->h)) : null,
                0 !== $diff->m ? sprintf('%u %s', $diff->m, $this->translator->transChoice('eb.date.minute', $diff->m)) : null,
                0 !== $diff->s ? sprintf('%u %s', $diff->s, $this->translator->transChoice('eb.date.second', $diff->s)) : null,
            ]))
        );
    }

    /**
     * Future
     *
     * @param null|string|\DateTime $date   Date
     * @param null|string           $format Format
     *
     * @return bool
     */
    public function isFuture($date = null, $format = null)
    {
        return false === $this->isPast($date, $format);
    }

    /**
     * Past
     *
     * @param null|string|\DateTime $date   Date
     * @param null|string           $format Format
     *
     * @return bool
     */
    public function isPast($date = null, $format = null)
    {
        if (null === $date = $this->createDateTime($date, $format)) {
            return false;
        }

        return $date->getTimestamp() < time();
    }

    /**
     * Create DateTime
     *
     * @param null|\DateTime|string|int $date   Date
     * @param null|string               $format Format
     *
     * @return \DateTime|null
     */
    private function createDateTime($date, $format = null)
    {
        if (null === $date) {
            return null;
        }
        if ($date instanceof \DateTime) {
            return $date;
        }
        if (null === $format) {
            return new \DateTime($date);
        } elseif (false !== $date = \DateTime::createFromFormat($format, $date)) {
            return $date;
        }

        return null;
    }
}
