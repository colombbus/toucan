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

class calculation_Core {

    // TODO : gérer les choix multiples

    public static function number(& $values) {
        return (sizeof($values));
    }

    public static function minimum(& $values) {
        if (sizeof($values)==0)
            return null;
        reset($values);
        $result = current($values);
        foreach ($values as $value) {
            if ($value<$result)
                $result = $value;
        }
        // or return min($values)
        return $result;
    }

    public static function maximum(& $values) {
        if (sizeof($values)==0)
            return null;
        reset($values);
        $result = current($values);
        foreach ($values as $value) {
            if ($value>$result)
                $result = $value;
        }
        // or return max($values)
        return $result;
    }

    public static function average(& $values) {
        if (sizeof($values)==0)
            return null;
        $sum = 0;
        foreach ($values as $value) {
            $sum+=$value;
        }
        return $sum/sizeof($values);
    }

    public static function variance(& $values) {
        if (sizeof($values)==0)
            return null;
        $average = self::average($values);
        $sum = 0;
        foreach ($values as $value) {
            $sum+=pow($value-$average,2);
        }
        return $sum/sizeof($values);
    }

    public static function standard_deviation(& $values) {
        if (sizeof($values)==0)
            return null;
        return sqrt(self::variance($values));
    }

    public static function median(& $values) {
        if (sizeof($values)==0)
            return null;
        if (!sort($values))
            return null;
        $num=count($values);
        if ($num % 2) {
            return $values[floor($num/2)];
        } else {
            return ($values[$num/2] + $values[$num/2 - 1]) / 2;
        }
    }
}
?>