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
}