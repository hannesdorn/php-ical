<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 24.12.16
 * Time: 12:51
 */

namespace janxb\PHPical;


use DateTime;
use ICal\EventObject;

class Event
{
    /** @var  string */
    private $color;
    /** @var EventObject */
    private $event;

    private $dateStart;
    private $dateEnd;

    public function __construct($color, EventObject $event)
    {
        $this->color = $color;
        $this->event = $event;

        /** @noinspection PhpUndefinedFieldInspection */
        $this->dateStart = new DateTime($this->event->dtstart_tz);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->dateEnd = new DateTime($this->event->dtend_tz);
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    public function getTitle()
    {
        return $this->event->summary;
    }

    public function getRawTimestamp()
    {
        return $this->dateStart->format('YmdHi');
    }

    public function isFullDayEvent()
    {
        return ($this->getStartTime() == '00:00' &&
            $this->getStartTime() == $this->getEndTime() &&
            $this->getFullDayCount() == 1
        );
    }

    public function getFullDayCount()
    {
        return $this->dateEnd->diff($this->dateStart)->d;
    }

    public function isMultiDayEvent()
    {
        return ($this->getFullDayCount() >= 1 && !$this->isFullDayEvent());
    }

    public function isFullDayEventFromYesterday($year, $month, $day)
    {
        $dateString = $year . str_pad($month, 2, '0', STR_PAD_LEFT) . str_pad($day, 2, '0', STR_PAD_LEFT);
        return ($this->isFullDayEvent() && $this->dateEnd->format('Ymd') == $dateString);
    }

    public function getDuration($year, $month, $day)
    {
        if ($this->isFullDayEvent())
            return '';

        $dateString = $year . str_pad($month, 2, '0', STR_PAD_LEFT) . str_pad($day, 2, '0', STR_PAD_LEFT);
        $result = '';
        if ($this->dateStart->format('Ymd') == $dateString)
            $result .= $this->getStartTime();
        $result .= '-';
        if ($this->dateEnd->format('Ymd') == $dateString)
            $result .= $this->getEndTime();

        return $result;
    }

    public function getStartTime()
    {
        return $this->dateStart->format('H:i');
    }

    public function getEndTime()
    {
        return $this->dateEnd->format('H:i');
    }

    public function getLocation()
    {
        $this->event->location = str_replace('\\n', ', ', $this->event->location);
        $this->event->location = str_replace('\\r', '', $this->event->location);
        return str_replace('\\', '', $this->event->location);
    }
}