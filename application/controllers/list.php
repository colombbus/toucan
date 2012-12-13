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

class List_Controller extends Toucan_Controller {

    protected $filter = NULL;

    public function __construct() {
        parent::__construct();
        $this->filter = ListFilter::instance();
    }

    public static function initList(& $user, $access, $data, $action, $fields, $constraints = null, $members = false, $icons = null) {
        $prefix = self::getSessionPrefix($data);
        $session = Session::instance();
        $session->set_flash($prefix.'ACCESS',$access);
        $session->set_flash($prefix.'ACTION',$action);
        $session->set_flash($prefix.'FIELDS',$fields);
        $filter = ListFilter::instance();
        if (isset($constraints)) {
            $session->set_flash($prefix.'CONSTRAINT',$constraints);
        }
        if (isset($icons)) {
            $session->set_flash($prefix.'ICONS',$icons);
        }
        
        $model = ORM::Factory($data);
        $per_page = Kohana::config("toucan.".$data."_per_page");
        
        // Check if we are in the same list as the previous one. If so, try to keep the values for page number and filter.
        // Otherwise reset filter and set page number to 1
        $lastList = $session->get('LIST_URL', "");
        if (strcmp($lastList, url::current())==0) {
            // We are in the same list
            $pageNumber = $session->get($prefix.'PAGE',1);
            if (!$members)
                $count = $model->count($filter,$user,$constraints);
            else
                $count = $model->count($filter,$user);
            if (($pageNumber-1) * $per_page >= $count) {
                // there are no more items to display with last page number: reset it
                $pageNumber = 1;
                $filter->clear();
            }
        } else {
            $pageNumber = 1;
            $filter->clear();
        }
        $session->set('LIST_URL',url::current());

        
        if (!$members)
            $count = $model->count($filter,$user,$constraints);
        else
            $count = $model->count($filter,$user);
        $session->set_flash($prefix.'COUNT',$count);
        
        $session->set_flash($prefix.'PER_PAGE',$per_page);
        if ($members) {
            $session->set_flash($prefix.'MEMBERS',1);
        }
        return "list/items/$data/".$pageNumber;
    }

    public static function getSessionPrefix($data) {
        return 'LIST_'.$data.'_';
    }

    protected function computeCount($data) {
        $prefix = self::getSessionPrefix($data);
        $model = ORM::Factory($data);
        $members = $this->session->get($prefix.'MEMBERS',false);
        if ($members) {
            $count = $model->count($this->filter,$this->user);
        } else {
            $constraints = $this->session->get($prefix.'CONSTRAINT',null);
            $count = $model->count($this->filter,$this->user,$constraints);
        }
        $this->session->set_flash($prefix.'COUNT',$count);
    }

    public function items($data, $page) {
        // RETRIEVE DATA FROM SESSION
        $prefix = self::getSessionPrefix($data);
        $access = $this->session->get($prefix.'ACCESS',access::ANYBODY);
        $action = $this->session->get($prefix.'ACTION', false);
        $fields = $this->session->get($prefix.'FIELDS', false);
        $count = $this->session->get($prefix.'COUNT', 0);
        $per_page = $this->session->get($prefix.'PER_PAGE', 0);
        $constraints = $this->session->get($prefix.'CONSTRAINT', null);
        $icons = $this->session->get($prefix.'ICONS', null);
        $members = $this->session->get($prefix.'MEMBERS', false);

        if ($members) {
            $template = 'data/members_content';
        } else {
            $template = 'data/list_content';
        }

        // RECORD PAGE NUMBER
        $this->session->set($prefix.'PAGE',$page);

        // KEEP SESSION DATA
        $this->session->keep_flash();

        // CHECK ACCESS
        $this->ensureAccess($access);

		// DATA
        $model = ORM::Factory($data);
		
        if ($count > 0) {
            // GET ITEMS
            $offset   = ($page - 1) * $per_page;

            if ($members) {
                $group = ORM::factory('group',$constraints);
                $items = $model->getItems($this->filter, $this->user, $offset, $per_page);
            } else {
                $items = $model->getItems($this->filter, $this->user, $offset, $per_page, $constraints);
            }

            $displayableItems = array();
            foreach ($items as $item) {
                $newItem = array();
                foreach ($fields as $key=>$value) {
                    if (strpos($value, '->') === FALSE) {
                        $value = $item->$value;
                    } else {
                        $valueParts = explode("->",$value);
                        $value = $item->$valueParts[0];
                        for ($i=1;$i<sizeof($valueParts);$i++) {
                            $value = $value->$valueParts[$i];
                        }
                    }
                    $newItem['info'][$key] = $value;
                }
                $newItem['link'] = $action.$item->id;
                $newItem['id'] = $item->id;
                if ($members) {
                    $newItem['member'] = $item->memberOf($group);
                }
                $displayableItems[] = $newItem;
            }

            $view=new View($template);
            $view->items = $displayableItems;
            $view->pagination = new Pagination(array('base_url'=> "list/items/$data",'uri_segment' => $data,
               'items_per_page' => $per_page,'style' => 'extended','total_items' => $count));
            $view->data = $data;
            if (isset($icons))
                $view->icons = $icons;
            $view->render(TRUE);
        } else {
            $view=new View('data/list_content');
            $view->noItems = "$data.no_item";
            $view->render(TRUE);
        }
    }

    protected function displayError($message = false) {
        if ($message)
            die($message);
        else
            die('error');
    }

    public function register($groupId, $value, $userId) {
        $this->dataName = "group";
        $this->loadData($groupId);
        $this->ensureAccess(access::OWNER);
        $group = $this->data;
        $user = ORM::factory('user',$userId);
        if (!$user->loaded) {
            $this->displayError('user unknown');
        }
        if (!$user->registerGroup($group, $value, true))
            $this->displayError("error");
        $this->session->keep_flash();
    }

    public function setSorting($name, $order=1) {
        $this->filter->setSorting($name, $order);
        $this->session->keep_flash();
    }

    public function setSearch() {
        if (($post = $this->input->post())&&isset($post['form_list_search'])) {
            $data = $post['form_list_search'];
            unset($post['form_list_search']);
            $this->filter->setSearch($post);
            $this->computeCount($data);
        }
        $this->session->keep_flash();
    }

    public function clearSearch() {
        $this->filter->clearSearch();
        if (($post = $this->input->post())&&isset($post['form_list_search'])) {
            $data = $post['form_list_search'];
            $this->computeCount($data);
        }
        $this->session->keep_flash();
    }

}
?>