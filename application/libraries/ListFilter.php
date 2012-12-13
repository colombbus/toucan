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

class ListFilter_Core {

    protected $session;
    protected $kept = false;

    public function __construct() {
        $this->session = Session::instance();
        $this->kept = false;
    }

    public static function factory() {
        return new ListFilter();
    }

    public static function instance() {
        static $instance;
        empty($instance) and $instance = new ListFilter();
        return $instance;
    }

    public function setSorting($name, $order=1) {
        $this->session->set('FILTER_SORTING_NAME',$name);
        $this->session->set('FILTER_SORTING_ORDER',$order);
    }

    public function setDefaultSorting($name, $order=1) {
        $sorting =  $this->session->get('FILTER_SORTING_NAME',false);
        if ($sorting === false) {
            $this->session->set('FILTER_SORTING_NAME',$name);
            $this->session->set('FILTER_SORTING_ORDER',$order);
        }
    }

    public function setSearch($fields) {
        $this->session->set('FILTER_SEARCH',$fields);
    }

    public function getSortingName() {
        // if filter is not set, returns 'id'
        return $this->session->get('FILTER_SORTING_NAME','id');
    }

    public function getSortingOrderInt() {
        return ($this->session->get('FILTER_SORTING_ORDER',1));
    }

    public function getSortingOrder() {
        // ASC by default
        $value = $this->session->get('FILTER_SORTING_ORDER',1);
        if ($value == 0) {
            return "DESC";
        } else {
            return "ASC";
        }
    }

    public function getSearch() {
        return $this->session->get('FILTER_SEARCH',false);
    }

    public function clearSearch() {
        $this->session->delete('FILTER_SEARCH');
    }

    public function clearSorting() {
        $this->session->delete('FILTER_SORTING_NAME');
        $this->session->delete('FILTER_SORTING_ORDER');
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
        $this->keep();
        if ($tablePrefix) {
            return " order by `".$tablePrefix."`.`".$this->getSortingName()."` ".$this->getSortingOrder();
        } else {
            return " order by `".$this->getSortingName()."` ".$this->getSortingOrder();
        }
    }

    public function getSQLWhere($tablePrefix=false) {
        $this->keep();
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
        $this->keep();
        $data->orderby(array($this->getSortingName() => $this->getSortingOrder()));
        if ($search = $this->getSearch()) {
            $data->like($search);
        }
        return $data;
    }

    public function keep() {
        if (!$this->kept) {
            $this->session->keep_flash('FILTER_SORTIN_NAME');
            $this->session->keep_flash('FILTER_SORTING_ORDER');
            $this->session->keep_flash('FILTER_SEARCH');
            $this->kept = true;
        }
    }

    public function clear() {
        $this->clearSearch();
        $this->clearSorting();
    }

}
?>