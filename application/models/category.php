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

class Category_Model extends Toucan_Model {

    protected $table_name = "categories";
    protected $belongs_to = array('owner'=>'user', 'evaluation','style', 'session');
    protected $has_one = array('view' => 'group', 'edit' => 'group');
    protected $has_and_belongs_to_many = array('indicators');
    protected $accessParent = "generic_parent";
    protected $parentId = "evaluation_id";
    protected $controllerName = "category";
    const COLOR_RECAPITULATIVE = "FFEEFF";
    const COLOR_DEFAULT = "FFFFFF";

    
    public function getCreationData($access, & $user, & $parameters = null) {
        $parentId = $this->parentId;
        if (isset ($parameters[$parentId])) {
            // Initialize inherited access control
            $this->$parentId = $parameters[$this->parentId];
            if (!$this->valuesSet) {
                $this->inherit = 1;
                $this->active = 1;
                $accessParent = $this->accessParent;
                $parent = $this->$accessParent;
                $this->view_id = $parent->getDisplayGroupId();
                $this->edit_id = $parent->getEditGroupId();
                $this->language = language::getCurrentLanguage();
            }
        }
        return $this->getEditableData(access::OWNER, $user);
    }
    
    public function getEditableData($access, & $user) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $editableData = array();
        $editableData[] = array ('type' => 'check','name' => 'active','label' => 'category.active','checked' => $this->isActive());
        $editableData[] = array ('type' => 'separator');
        $editableData[] = array ('type' => 'text','name' => 'name','label' => 'category.name','required'=>'1', 'value' => $this->name);
        $editableData[] = array ('type' => 'long_text','name' => 'description','label' => 'category.description','value' => $this->description);
        $editableData[] = array ('type' => 'check','name' => 'recapitulative','label' => 'category.recapitulative','checked' => $this->isRecapitulative());
        if ($owner|$admin) {
            $editableData[] = array ('type' => 'separator');
            $this->addEditableGroups($editableData, "category");
            $editableData[] = array ('type' => 'separator');
            $editableData[] = array ('type' => 'check','name' => 'published','label' => 'category.published', 'checked'=>($this->published==1));
            $editableData[] = array ('type' => 'check','name' => 'password_flag','label' => 'category.password_flag','checked'=>$this->password_flag,'hidden'=>($this->published!=1));
            $editableData[] = array ('type' => 'text','name' => 'password','label' => 'category.password','value'=>$this->password, 'disabled'=>($this->password_flag!=1), 'hidden'=>($this->published!=1));
            $styles = ORM::factory("style");
            $filter = null;
            if ($styles->count($filter, $user)>0) {
                $styles = $styles->getVisibleItems($filter, $user);
                $templates = array();
                $templates[0] = Kohana::lang('session.defaultStyle');
                foreach ($styles as $item) {
                    $templates[$item->id] = $item->name;
                }
                $editableData[] = array ('type' => 'select','name' => 'style_id','label' => 'category.style','values' => $templates, 'value'=>$this->style_id, 'hidden'=>($this->published!=1));
            } else {
                $editableData[] = array ('type' => 'hidden','name' => 'style_id','value'=>$this->style_id);
            }
            $editableData[] = array ('type' => 'select','name' => 'language','label' => 'category.language','values'=>language::getAvailableLanguages(), 'value'=>$this->language, 'hidden'=>($this->published!=1));
        }
        if ($this->loaded) {
            $parentId = $this->parentId;
            $editableData[] = array ('type' => 'hidden','name' => $this->parentId,'value'=>$this->$parentId);
        }
        return $editableData;
    }
    
    public function getDisplayableData($access,& $user =null) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $mayEdit = ($access == access::MAY_EDIT);
        $displayableData = array();
        // ACTIVE
        if ($this->isActive()) {
            $value = Kohana::lang('category.yes');
        } else {
            $value = Kohana::lang('category.no');
        }
        $displayableData[] = array ('type' => 'text', 'label' => 'category.active', 'value'=> $value);
        $displayableData[] = array ('type' => 'separator');
        // NAME & DESCRIPTION
        $displayableData[] = array ('type' => 'text', 'label' => 'category.name', 'value'=> $this->name);
        $displayableData[] = array ('type' => 'long_text', 'label' => 'category.description', 'value'=> $this->description);
        // RECAPITULATUVE
        if ($this->isRecapitulative()) {
            $value = Kohana::lang('category.yes');
        } else {
            $value = Kohana::lang('category.no');
        }
        $displayableData[] = array ('type' => 'text', 'label' => 'category.recapitulative', 'value'=> $value);

        // GROUPS
        if ($owner|$admin) {
            $displayableData[] = array ('type' => 'separator');
            $this->addDisplayableGroups($displayableData, "category");
        }

        // PUBLIC URL
        if ($this->isPublished()) {
            $link = $this->getPublicUrl();
            $displayableData[] = array ('type' => 'separator');
            $displayableData[] = array ('type' => 'link', 'label' => 'category.url', 'value'=> html::url($link), 'link'=>$link);
            if ($this->password_flag) {
                $displayableData[] = array ('type' => 'text', 'label' => 'category.password', 'value'=> $this->password);
            }
            if ($this->style_id>0) {
                if (DataAccess::testAccess($this->style, $user, access::MAY_VIEW))
                    $displayableData[] = array ('type' => 'link', 'label' => 'category.style', 'value'=> $this->style->name, 'link'=>'style/show/'.$this->style_id);
                else
                    $displayableData[] = array ('type' => 'text', 'label' => 'category.style', 'value'=> $this->style->name);
            } else {
                $displayableData[] = array ('type' => 'text', 'label' => 'category.style', 'value'=> Kohana::lang('category.defaultStyle'));
            }
            $languages = language::getAvailableLanguages();
            if (isset($this->language) && isset($languages[$this->language]))
                $displayableData[] = array ('type' => 'text', 'label' => 'category.language', 'value'=> $languages[$this->language]);
        }
        
        // ADMIN INFO
        if ($admin|$owner) {
            $displayableData[] = array ('type' => 'separator');
            // OWNER
            $displayableData[] = array ('type' => 'link', 'label' => 'session.owner', 'value'=> $this->owner->fullName, 'link'=> '/user/profile/'.$this->owner->id);
            // Creation date
            $displayableData[] = array ('type' => 'text', 'label' => 'session.creation_date', 'value'=> Utils::translateTimestamp($this->created));
        }
        return $displayableData;
   }
   
    public function validateEdition(array & $array,& $user, $save = FALSE) {
        $this->checkBooleans($array, $user);
        $this->buildValidation($array);
        $result = parent::validate($this->validation, $save);
        if ($result&&$save) {
            $this->save();
        }
        return $result;
    }
    
    public function validateCreation(array & $array,& $user, $save = FALSE) {
        $this->checkBooleans($array, $user);
        $this->buildValidation($array);
        if (parent::validate($this->validation, false)) {
            $this->setOwner($user, false);
            $this->order = $this->getNextOrder();
            $this->created = time();
            if ($save)
                $this->save();
            return true;
        }
        return false;
    }
    
    public function count(& $filter , & $user, $constraints = null) {
        $parentId = $this->parentId;
        if (isset($constraints) && isset($constraints[$parentId])) {
            // Initialize inherited access control
            $this->$parentId = $constraints[$parentId];
        }
        return $this->countVisibleItems($filter , $user, $constraints);
    }
    
    public function getItems(& $filter,& $user,$offset = 0, $number = null, $constraints = null) {
        $parentId = $this->parentId;
        if (!isset ($filter)) {
            $filter = Filter::instance();
            $filter->setSorting("order");
        }
        if (isset($constraints) && isset($constraints[$parentId])) {
            // Initialize inherited access control
            $this->$parentId = $constraints[$parentId];
        }
        return $this->getVisibleItems($filter , $user, $offset, $number, $constraints);
    }

    public function isActive() {
        return $this->active;
    }

    public function isRecapitulative() {
        return $this->recapitulative;
    }

    public function setActive($value, $save=false) {
        if (!($this->loaded)||($value != $this->isActive())) {
            if ($value) {
                $this->active = 1;
            } else {
                $this->active = 0;
            }
            if ($save)
                $this->save();
        }
    }
    
    protected function buildValidation(array & $array) {
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('name', 'required', 'length[1,127]')
            ->add_callbacks('name', array($this, 'uniqueNameByParent'))
            ->add_rules('description', 'length[0,10000]')
            ->add_rules('view_id', 'valid::numeric')
            ->add_rules('edit_id', 'valid::numeric')
            ->add_rules($this->parentId, 'required', 'valid::numeric')
            ->add_rules('active', 'in_array[0,1]')
            ->add_rules('recapitulative', 'in_array[0,1]')
            ->add_rules('inherit', 'in_array[0,1]')
            ->add_rules('published', 'in_array[0,1]')
            ->add_rules('password_flag', 'in_array[0,1]')
            ->add_rules('style_id', 'valid::numeric')
            ->add_rules('language', 'valid::alpha_numeric', 'length[0,2]');
        
        if (isset($array['password_flag'])) {
            if ($array['password_flag'])
                $this->validation->add_rules('password', 'required','length[5,50]');
            else
                $this->password = "";
        }

    }

    public function uniqueNameByParent(Validation $valid) {
        if (array_key_exists('name', $valid->errors()))
            return;
        $parentId = $this->parentId;
        if (isset ($valid->$parentId)) {
            $escapedName = addslashes($valid->name);
            $value = $valid->$parentId;
            if ($this->loaded) {
                $other = $this->db->query("SELECT id from ".$this->table_name." WHERE id != $this->id AND $this->parentId = '$value' AND name = '$escapedName'");
            } else {
                $other = $this->db->query("SELECT id from ".$this->table_name." WHERE $this->parentId = '$value' AND name = '$escapedName'");
            }
            if ($other->count() > 0) {
                $valid->add_error( 'name', 'uniqueName');
            }
        }
    }

    protected function checkBooleans(& $array, &$user) {
        if (!isset($array['active']))
            $array['active']=0;
        if (!isset($array['recapitulative']))
            $array['recapitulative']=0;
        if ($user->isAdmin()||$this->isOwner($user)) {
            if (!isset($array['inherit']))
                $array['inherit']=0;
            if (!isset($array['password_flag']))
                $array['password_flag']=0;
            if (!isset($array['published']))
                $array['published']=0;
        }
    }
    
    public function __get($column) {
        if ($column == 'generic_parent') {
            if ($this->evaluation_id >0) {
                // category linked to an evaluation
                return $this->evaluation;
            } else if ($this->session_id >0) {
                // category linked to a survey
                return ORM::factory("survey", $this->session_id);
            }
        }
        return parent::__get($column);
    }
    
    public function getIndicatorOrder($indicatorId) {
        if (!$this->loaded)
            return null;
        $result = $this->db->query("SELECT `order` from categories_indicators WHERE category_id=$this->id AND indicator_id=$indicatorId");
        $result->result(false,MYSQL_NUM);
        if ($result[0][0] === null)
            return null;
        else
            return $result[0][0];
    }

    public function getNextIndicatorOrder() {
        if (!$this->loaded)
            return null;
        $result = $this->db->query("SELECT max(`order`)+1 from categories_indicators WHERE category_id=$this->id");
        $result->result(false,MYSQL_NUM);
        if ($result[0][0] === null)
            return 1;
        else
            return $result[0][0];
    }

    public function getNextOrder() {
        $parentId = $this->parentId;
        $result = $this->db->query("SELECT max(`order`)+1 from categories WHERE $parentId = '".$this->$parentId."'");
        $result->result(false,MYSQL_NUM);
        if ($result[0][0] === null)
            return 1;
        else
            return $result[0][0];
    }

    
    public function updateIndicatorOrders($constraints = array()) {
        if (!$this->loaded)
            return null;
        $next = $this->getNextIndicatorOrder();
        $result = $this->db->query("SELECT `indicator_id`, `order` from `categories_indicators` WHERE `category_id`=$this->id ORDER by `order` ASC");
        $indicatorIds = array();
        $current = 1;
        foreach($result as $row) {
            if (!isset($constraints[$row->indicator_id])) {
                // order is not constrainted: we set it according to other elements
                if (!isset($row->order)||$row->order == 0) {
                    $order = $next++;
                } else {
                    $order = $current++;
                }
                $indicatorIds[$order] = $row->indicator_id;
            }
        }
        // Handle constraints
        foreach($constraints as $id=>$order) {
            for ($newOrder = $next; $newOrder>$order; $newOrder--) {
                $indicatorIds[$newOrder] = $indicatorIds[$newOrder-1];
            }
            $indicatorIds[$order] = $id;
            $next++;
        }
        
        ksort($indicatorIds);
        $current = 1;
        foreach($indicatorIds as $indicatorId) {
            $result = $this->db->query("UPDATE `categories_indicators` SET `order`=$current WHERE `category_id`=$this->id AND `indicator_id`=$indicatorId");
            $current++;
        }
    }
    
    public function setIndicators(& $ids) {
        $this->indicators = $ids;
        $this->save();
        $current = 1;
        foreach($ids as $id) {
            $result = $this->db->query("UPDATE `categories_indicators` SET `order`=$current WHERE `category_id`=$this->id AND `indicator_id`=$id");
            $current++;
        }
    }
    
    public function getColor() {
        if ($this->loaded&&$this->isRecapitulative())
            return self::COLOR_RECAPITULATIVE;
        else
            return self::COLOR_DEFAULT;
    }
    
    public function getItemActions(& $user) {
        $itemActions = array();
        $itemActions[] = array('link'=>$this->controllerName."/show/", 'text'=>"category.details");
        if ($this->isEditableBy($user)) {
            if (!$this->isRecapitulative()) {
                $itemActions[] = array('link'=>$this->controllerName."/members/", 'text'=>"category.members");
            }
        }
        return $itemActions;
    }
    
    public function getParent() {
        return $this->generic_parent;
    }
    
    public function isPublished() {
        return $this->published;
    }
    
    public function getPublicUrl() {
        if ($this->isPublished()) {
            return "public/indicators/".$this->id;
        }
    }
    
    public function export() {
        if (strlen($this->description)>0) {
            $text = $this->description;
        } else {
            $text ="";
        }
        rtf::addSeparator($this->name, $text);
    }
}
?>