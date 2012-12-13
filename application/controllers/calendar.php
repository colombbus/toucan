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

class Calendar_Controller extends Controller {

    public function popup($index,$month=null,$year=null)
    {
        if ($month===null)
            $month = date('n');
        if ($year===null)
            $year = date('Y');
        $calendar = new Calendar($month,$year);
        $view =  new View('calendar/popup', array
        (
            'month'  => $month,
            'year'   => $year,
            'weeks'  => $calendar->weeks(),
        ));
        $view->index = $index;
        $view->base_url = html::url("calendar/popup/".$index);
        $view->render(TRUE);
    }
}
?>