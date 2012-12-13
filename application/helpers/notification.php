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

class notification_Core {
    const NO_NOTIFICATION = 0;
    const NOTIFY_OWNER = 1;
    const NOTIFY_MANAGERS = 2;
    const NOTIFY_OTHER = 3;

    public static function getText($type, $owner = "", $address = "") {
        switch ($type) {
            case self::NOTIFY_OWNER:
                return sprintf(Kohana::lang("notification.owner"), $owner);
                break;
            case self::NOTIFY_MANAGERS:
                return Kohana::lang("notification.managers");
                break;
            case self::NOTIFY_OTHER:
                return sprintf(Kohana::lang("notification.other_to"), $address);
                break;
            default:
                return Kohana::lang("notification.none");
                break;
        }
    }

    public static function requiresAddress($type) {
        return ($type == self::NOTIFY_OTHER);
    }

    public static function getSelection($owner = "") {
        $selection = array(self::NO_NOTIFICATION=>Kohana::lang('notification.none'), self::NOTIFY_OWNER=>sprintf(Kohana::lang('notification.owner'), $owner), self::NOTIFY_MANAGERS=>Kohana::lang('notification.managers'), self::NOTIFY_OTHER=>Kohana::lang('notification.other'));
        return $selection;
    }

}
?>