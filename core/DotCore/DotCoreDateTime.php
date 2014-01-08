<?php

class InvalidDateTimeException extends DotCoreException {}

/**
 * DotCoreDateTime
 *
 * @author perrin
 */
class DotCoreDateTime extends DateTime {

    /**
     * Converts a date in the format of YYYY-MM-DD HH:MM:SS into a timestamp
     * @param string $date_string
     * @return int timestamp
     *
     * @throws InvalidDateTimeException if the date in $date_string is invalid
     */
    public static function DateTimeStringToTimestamp($date_string) {
        // Break into compotents - input format for MySQL = YYYY-MM-DD HH:MM:SS
        $dateTimeComponents = explode(" ", $date_string);
        $dateComponents = explode("-", $dateTimeComponents[0]);
        $timeComponents = explode(":", $dateTimeComponents[1]);

        $month = intval($dateComponents[1]);
        $day = intval($dateComponents[2]);
        $year = intval($dateComponents[0]);

        $hour = isset($timeComponents[0]) ? intval($timeComponents[0]) : 0;
        $minutes = isset($timeComponents[1]) ? intval($timeComponents[1]) : 0;
        $seconds = isset($timeComponents[2]) ? intval($timeComponents[2]) : 0;

        if(!checkdate($month, $day, $year) ||
            $hour > 23 || $hour < 0 ||
            $minutes > 59 || $minutes < 0 ||
            $seconds > 59 || $seconds < 0)
        {
            throw new InvalidDateTimeException();
        }
        else
        {
            return mktime($hour, $minutes, $seconds, $month, $day, $year);
        }
    }

}
?>
