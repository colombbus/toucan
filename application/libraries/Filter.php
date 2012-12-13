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

class Filter_Core {

    protected $session;
    protected $sortingName = null;
    protected $sortingOrder = null;
    protected $search = null;

    public function __construct() {
        $this->session = Session::instance();
    }

    public static function factory() {
        return new Filter();
    }

    public static function instance() {
        static $instance;
        empty($instance) and $instance = new Filter();
        return $instance;
    }

    public function setSorting($name, $order=1) {
        $this->sortingName = $name;
        $this->sortingOrder = $order;
    }

    public function setDefaultSorting($name, $order=1) {
        if (!isset($sortingName)) {
            $this->sortingName = $name;
            $this->sortingOrder = $order;
        }
    }

    public function setSearch($fields) {
        $this->search = $fields;
    }

    public function getSortingName() {
        // if filter is not set, returns 'id'
        if (isset($this->sortingName))
            return $this->sortingName;
        else
            return "id";
    }

    public function getSortingOrderInt() {
        if (isset($this->sortingOrder))
            return ($this->sortingOrder);
        else
            return 1;
    }

    public function getSortingOrder() {
        // ASC by default
        $value = $this->getSortingOrderInt();
        if ($value == 0) {
            return "DESC";
        } else {
            return "ASC";
        }
    }

    public function getSearch() {
        if (isset($this->search))
            return $this->search;
        else
            return false;
    }

    public function clearSearch() {
        $this->search = null;
    }

    public function clearSorting() {
        $this->sortingName=null;
        $this->sortingOrder=null;
    }

    public function fillSearchFields(& $fields) {
        $searchFields = $this->getSearch();
        if ($searchFields !== false) {
            $change = false;
            foreach ($fields as & $field) {
                if (isset($field['name'])&&isset($searchFields[$field['name']])) {
                    $field['value'] = $searchFields[$field['name']];
                    $change = true;
                }
            }
            return $change;
        }
        return false;
    }

    public function getSQLOrder($tablePrefix=false) {
        if ($tablePrefix) {
            return " order by `".$tablePrefix."`.`".$this->getSortingName()."` ".$this->getSortingOrder();
        } else {
            return " order by `".$this->getSortingName()."` ".$this->getSortingOrder();
        }
    }

    public function getSQLWhere($tablePrefix=false) {
        if ($tablePrefix) {
            $prefix = "`$tablePrefix`.";
        } else {
            $prefix = "";
        }
        $sql = "";
        if ($search = $this->getSearch()) {
            $first = true;
            foreach ($search as $key=>$value) {
                if (!$first) {
                    $sql .= " and";
                }
                $sql .= " $prefix`$key` like '%$value%'";
                $first = false;
            }
            return $sql;
        } else {
            return false;
        }
    }

    public function add(& $data) {
        $data->orderby(array($this->getSortingName() => $this->getSortingOrder()));
        if ($search = $this->getSearch()) {
            $data->like($search);
        }
        return $data;
    }

    public function clear() {
        $this->clearSearch();
        $this->clearSorting();
    }

}
?>