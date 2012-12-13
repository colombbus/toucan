<?php defined('SYSPATH') or die('No direct script access.');
 /**
 * Toucan is a web application to perform evaluation and follow-up of
 * activities.
 * Copyright (C) 2010 Colombbus (http://www.colombbus.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Utils_Core {

    public static function date_str2db($date, $format) {
        $newDate = date_parse_from_format($format , $date );
        if ($newDate['error_count']>0)
            return FALSE;
        if (!checkdate($newDate['month'], $newDate['day'], $newDate['year']))
            return FALSE;
        return $newDate['year']."-".$newDate['month']."-".$newDate['day'];
    }

    public static function date_db2str($date, $format) {
        try {
            $newDate = new DateTime($date);
            return $newDate->format($format);
        }
        catch (Exception $e){
            return FALSE;
        }
    }

    public static function timestamp_str2db($date, $format) {
        $decodedDate = Utils::date_str2db($date,$format);
        if ($decodedDate===FALSE)
            return FALSE;
        else {
            try {
                $dateObject = new DateTime($decodedDate);
                return $dateObject->getTimestamp();
            }
            catch (Exception $e)
            {}
        }
        return FALSE;
    }

    public static function translateDate($date) {
        if (strlen($date)>0)
            return Utils::date_db2str($date,Kohana::lang("calendar.format"));
        else
            return "";
    }

    public static function translateTimestamp($timestamp) {
        if (strlen($timestamp)>0)
            return date(Kohana::lang("calendar.timestamp_format"),$timestamp);
        else
            return "";
    }

    public static function translateTimestampForFilename($timestamp) {
        if (strlen($timestamp)>0)
            return date(Kohana::lang("calendar.timestamp_filename_format"),$timestamp);
        else
            return "";
    }

    // tests if $date1 < $date2
    public static function compareDates($date1, $date2) {
        try {
            $time1 = new DateTime($date1);
            $time2 = new DateTime($date2);
            return ($time1<$time2);
        } catch (Exception $e) {
            return FALSE;
        }
    }

    public static function getRemoteIp() {
        // function from here: http://www.kavoir.com/2010/03/php-how-to-detect-get-the-real-client-ip-address-of-website-visitors.html
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return NULL;
    }
}
 ?>