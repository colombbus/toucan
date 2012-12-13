<?php defined('SYSPATH') OR die('No direct access allowed.');
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

class DataAccess_Core  {

    public static function checkAccess(& $user, & $group, $ownerId=null) {
        if (isset($user)&&($user->isAdmin() || (isset($ownerId)&& ($user->id == $ownerId))))
            return true;
        switch ($group->id) {
            case 0: // restricted to the owner
                return (isset($user)&&isset($ownerId)&&($user->id == $ownerId));
                break;
            case 1: // public
                return true;
                break;
            case 2: // restricted to registered users
                return ($user !== null);
                break;
            default: // restricted to a group
                if ((!isset($group))||(!$group->loaded)) {
                    // group does not exist
                    return false;
                }
                return (isset($user)&&($user->memberOf($group)));
                break;
        }
        // never reached
        return false;
    }

    public static function computeAccess(& $data, & $user) {
        if (isset($user)&&$user->isAdmin()) {
            return access::ADMIN;
        }
        if (isset($data)) {
            if ($data->isOwner($user)) {
                return access::OWNER;
            }
            if ($data->isEditableBy($user)) {
                return access::MAY_EDIT;
            }
            if ($data->mayBeContributedBy($user)) {
                return access::MAY_CONTRIBUTE;
            }
            if ($data->isViewableBy($user)) {
                return access::MAY_VIEW;
            }
        }
        return access::NO_ACCESS;
    }

    public static function testAccess(& $data, & $user, $role) {
        return (self::computeAccess($data, $user)>=$role);
    }

}

?>