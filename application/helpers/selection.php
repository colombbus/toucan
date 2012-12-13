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

class selection_Core {
    public static function equal($value1, $value2) {
        if (is_array($value1)) {
            return in_array($value2, $value1);
        } else {
            return ($value1 == $value2);
        }
    }

    public static function different($value1, $value2) {
        if (is_array($value1)) {
            return !in_array($value2, $value1);
        } else {
            return ($value1 != $value2);
        }
    }

    public static function set($value) {
        return (isset($value));
    }

    public static function un_set($value) {
        return (!isset($value));
    }

    public static function inferior($value1, $value2) {
        return ($value1 < $value2);
    }

    public static function superior($value1, $value2) {
        return ($value1 > $value2);

    }

    public static function inferior_or_equal($value1, $value2) {
        return ($value1 <= $value2);

    }

    public static function superior_or_equal($value1, $value2) {
        return ($value1 >= $value2);
    }

}
?>