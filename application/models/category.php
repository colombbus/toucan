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
            }
        }
        return $this->getEditableData(access::OWNER, $user);
    }
    
    public function getEditableData($access, & $user) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $editableData = array();
        $editableData[] = array ('type' => 'text','name' => 'name','label' => 'category.name','required'=>'1', 'value' => $this->name);
        $editableData[] = array ('type' => 'long_text','name' => 'description','label' => 'category.description','value' => $this->description);
        $editableData[] = array ('type' => 'check','name' => 'active','label' => 'category.active','checked' => $this->isActive());
        if ($owner|$admin) {
            $editableData[] = array ('type' => 'separator');
            $this->addEditableGroups($editableData, "category");
            /*if ($this->mayBeEditedByPublic()) {
                $editGroups = end($editableData);
                $currentIndex = sizeOf($editableData)-1;
                $editableData[$currentIndex] = array ('type' => 'check','name' => 'public_access','label' => $this->sessionName.'.public_access','required'=>'1', 'checked'=>($this->contribute_id==1));
                $editGroups['hidden'] = ($this->contribute_id==1);
                $editableData[] = $editGroups;
                $editableData[] = array ('type' => 'check','name' => 'password_flag','label' => $this->sessionName.'.password_flag','checked'=>$this->password_flag,'hidden'=>($this->contribute_id!=1));
                $editableData[] = array ('type' => 'text','name' => 'password','label' => $this->sessionName.'.password','value'=>$this->password, 'disabled'=>!$this->password_flag, 'hidden'=>($this->contribute_id!=1));
                $styles = ORM::factory("style");
                $filter = null;
                if ($styles->count($filter, $user)>0) {
                    $styles = $styles->getVisibleItems($filter, $user);
                    $templates = array();
                    $templates[0] = Kohana::lang('session.defaultStyle');
                    foreach ($styles as $item) {
                        $templates[$item->id] = $item->name;
                    }
                    $editableData[] = array ('type' => 'select','name' => 'style_id','label' => 'session.style','values' => $templates, 'value'=>$this->style_id, 'hidden'=>($this->contribute_id!=1));
                } else {
                    $editableData[] = array ('type' => 'hidden','name' => 'style_id','value'=>$this->style_id);
                }
                $editableData[] = array ('type' => 'select','name' => 'language','label' => $this->sessionName.'.language','values'=>language::getAvailableLanguages(), 'value'=>$this->language, 'hidden'=>($this->contribute_id!=1));
            } */
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

        // GROUPS
        if ($owner|$admin) {
            $displayableData[] = array ('type' => 'separator');
            $this->addDisplayableGroups($displayableData, "category");
        }

        // PUBLIC URL
        /*if ($this->mayBeEditedByPublic()&&($this->contribute_id == 1)) {
            $link = $this->getPublicUrl();
            $displayableData[] = array ('type' => 'separator');
            $displayableData[] = array ('type' => 'link', 'label' => $this->sessionName.'.url', 'value'=> html::url($link), 'link'=>$link);
            if ($this->password_flag) {
                $displayableData[] = array ('type' => 'text', 'label' => $this->sessionName.'.password', 'value'=> $this->password);
            }
            if ($this->style_id>0) {
                if (DataAccess::testAccess($this->style, $user, access::MAY_VIEW))
                    $displayableData[] = array ('type' => 'link', 'label' => 'session.style', 'value'=> $this->style->name, 'link'=>'style/show/'.$this->style_id);
                else
                    $displayableData[] = array ('type' => 'text', 'label' => 'session.style', 'value'=> $this->style->name);
            } else {
                $displayableData[] = array ('type' => 'text', 'label' => 'session.style', 'value'=> Kohana::lang('session.defaultStyle'));
            }
            $languages = language::getAvailableLanguages();
            if (isset($this->language) && isset($languages[$this->language]))
                $displayableData[] = array ('type' => 'text', 'label' => $this->sessionName.'.language', 'value'=> $languages[$this->language]);
        }*/
        
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
            $this->created = time();
            if ($save)
                $this->save();
            return true;
        }
        return false;
    }
    
    public function count(& $filter , & $user, $constraints = null) {
        return $this->countVisibleItems($filter , $user, $constraints);
    }
    
    public function getItems(& $filter,& $user,$offset = 0, $number = null, $constraints = null) {
        return $this->getVisibleItems($filter , $user, $offset, $number, $constraints);
       }

    public function isActive() {
        return $this->active;
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
            ->add_rules('inherit', 'in_array[0,1]');
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
        if ($user->isAdmin()||$this->isOwner($user)) {
            if (!isset($array['inherit']))
                $array['inherit']=0;
        }
    }
}
?>