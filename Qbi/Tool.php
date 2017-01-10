<?php

namespace Qbi;

class Tool
{
    public static function niceDiffFromTimestamp(int $start, int $end = 0) : string
    {
        if ($end === 0) {
            $end = time();
        }

        $start = (new \DateTime())->setTimestamp($start);
        $end   = (new \DateTime())->setTimestamp($end);

        return static::niceDiffFromDateTime($start, $end);
    }

    public static function niceDiffFromDateTime(\DateTime $start, $end = null) : string
    {
        if (!$end) {
            $end = new \DateTime();
        }

        $diff = $start->diff($end);
        $diffString = [];
        foreach ($diff as $key => $value) {
            $order = '';
            switch ($key) {
                case 'y': $order = 'year'; break;
                case 'm': $order = 'month'; break;
                case 'd': $order = 'day'; break;
                case 'h': $order = 'hour'; break;
                case 'i': $order = 'minute'; break;
                case 's': $order = 'second'; break;
            }
            if ($value > 0) {
                if ($value > 1) {
                    $order .= 's';
                }
                $diffString[] = $value.' '.$order;
            }
        }
        return implode(', ', $diffString);
    }

    public static function secondsFromMinutes(int $minutes) :int
    {
        return $minutes * 60;
    }

    public static function secondsFromHours(int $hours) :int
    {
        return self::secondsFromMinutes($hours * 60);
    }

    public static function secondsFromDays(int $days) :int
    {
        return self::secondsFromHours($days * 24);
    }

    public static function secondsFromWeeks(int $weeks) :int
    {
        return self::secondsFromDays($weeks * 7);
    }

    /**
     * We settle for 30.5, since it's close enough.
     */
    public static function secondsFromMonth(int $months) :int
    {
        return self::secondsFromDays($months * 30.5);
    }
}