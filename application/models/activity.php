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

class Activity_Model extends ToucanTree_Model {

    protected $ORM_Tree_children = "activities";
    protected $belongs_to = array('owner' => 'user');
    protected $has_one = array('view' => 'group', 'edit' => 'group', 'logo'=>'file');
    protected $has_many = array('evaluations');
    protected $ignored_columns = array('logo', 'delete_logo');
    protected $accessHeirs = array('evaluations');


    protected function buildValidation(array & $array) {
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('name', 'required', array($this, 'uniqueName'), 'length[1,127]')
            ->add_rules('description', 'length[0,500]')
            ->add_rules('view_id', 'valid::numeric')
            ->add_rules('parent_id', 'valid::numeric')
            ->add_rules('edit_id', 'valid::numeric')
            ->add_rules('delete_logo','in_array[0,1]');
    }

    protected function buildLogoValidation(array & $array) {
        $this->validation = Validation::factory($array)
            ->add_rules('logo', 'upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[1M]');
    }

    public function validateEdition(array & $array, & $user, $save = FALSE) {
        $this->keepOldValues($array);
        $this->buildValidation($array);
        if ($result = parent::validate($this->validation, false)) {
            // Validate optional uploaded files
            $this->buildLogoValidation($_FILES);
            if ($result = parent::validate($this->validation, false)) {
                if (isset($array['delete_logo'])&& $array['delete_logo'] == 1 && $this->logo_id>0) {
                    $this->logo->delete();
                    $this->logo_id = 0;
                }
                if (upload::required($_FILES['logo']))
                    $this->setLogoFromUploadedPhoto(false);
                if ($save) {
                    $this->save();
                }
            }
        }
        return $result;
    }

    public function validateCreation(array & $array, & $user, $save = FALSE) {
        if ($result = $this->validateEdition($array, $user, false)) {
            $this->setOwner($user, false);
            $this->created = time();
            if ($save)
                $this->save();
        }
        return $result;
    }

    public function getDisplayableData($access, & $user = null) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $displayableData = array();
        // NAME & DESCRIPTION
        $displayableData[] = array ('type' => 'text', 'label' => 'activity.name', 'value'=> $this->name);
        $displayableData[] = array ('type' => 'long_text', 'label' => 'activity.description', 'value'=> $this->description);
        if ($this->parent->loaded) {
            if (DataAccess::testAccess($this->parent, $user, access::MAY_VIEW))
                $displayableData[] = array ('type' => 'link', 'label' => 'activity.parent', 'value'=> $this->parent->name, 'link'=>'/activity/showAll/'.$this->parent_id);
            else
                $displayableData[] = array ('type' => 'text', 'label' => 'activity.parent', 'value'=> $this->parent->name);
        }

        // PHOTO
        if ((isset($this->logo))) {
            $displayableData[] = array ('type' => 'images', 'label' => 'activity.logo', 'path' => Kohana::config('toucan.logo_directory').'/'.$this->logo);
        }
        if ($owner|$admin) {
            $this->addDisplayableGroups($displayableData, 'activity');
            $displayableData[] = array ('type' => 'separator');
            // OWNER
            $displayableData[] = array ('type' => 'link', 'label' => 'activity.owner', 'value'=> $this->owner->fullName, 'link'=> '/user/profile/'.$this->owner->id);
            // Creation date
            $displayableData[] = array ('type' => 'text', 'label' => 'activity.creation_date', 'value'=> Utils::translateTimestamp($this->created));
        }
        return $displayableData;
    }

    protected function recFillActivityList(& $list, & $user, $activity, $currentId, $level = 0){
        if (($activity->id != $currentId)&&($activity->isViewableBy($user))){//&&($activity->isEditableBy($user))){
            $space = "";
            for ($i=0;$i<$level;$i++) {
                $space.="\xc2\xa0\xc2\xa0\xc2\xa0\xc2\xa0";
            }
            $list[$activity->id] = $space.$activity->name;
            $children = $activity->children->as_array();
            if (sizeof($children)>0) {
                foreach($children as $child) {
                    $this->recFillActivityList($list, $user, $child, $currentId, $level+1);
                }
            }
        }
    }

    public function getCreationData($access, & $user, & $parameters = null) {
        if (isset ($parameters) && isset($parameters['parentId'])) {
            if ($this->parent_id==0)
                $this->parent_id = $parameters['parentId'];
        }
        return $this->getEditableData(access::OWNER, $user);
    }

    public function getEditableData($access, & $user) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $editableData = array();
        $filter = null;
        $editableData[] = array ('type' => 'text','name' => 'name','label' => 'activity.name','required'=>'1', 'value' => $this->name);
        $editableData[] = array ('type' => 'long_text','name' => 'description','label' => 'activity.description','value' => $this->description);
        if (isset($this->logo_id) && $this->logo_id>0) {
            $editableData[] = array ('type' => 'file','name' => 'logo', 'label' => 'activity.modify_logo');
            $editableData[] = array ('type' => 'check','name' => 'delete_logo', 'label' => 'activity.delete_logo');
        } else {
            $editableData[] = array ('type' => 'file','name' => 'logo', 'label' => 'activity.add_logo');
        }
        if ($owner|$admin) {
            $editableData[] = array ('type' => 'separator');
            $rawActivities = $this->getItems($filter, 0);//, $user);
            $activities = array();
            $activities[0] = Kohana::lang('activity.none');
            foreach ($rawActivities as $activity) {
                $this->recFillActivityList($activities, $user, $activity, $this->id);
            }
            $editableData[] = array ('type' => 'select','name' => 'parent_id', 'label' => 'activity.parent', 'values' => $activities, 'value'=>$this->parent_id);
            $editableData[] = array ('type' => 'separator');
            $this->addEditableGroups($editableData,'activity');
        }
        return $editableData;
    }

    public function getItems(& $filter = null,$parentId = 0, & $user = null, $offset = 0, $number = null, $constraintId = null) {
        $activities = ORM::factory('activity');
        if (isset($user)&&!$user->isAdmin())
            $activities->where('owner_id',$user->id);
        if (isset($filter))
            $filter->add($activities);
        else
            $activities->orderby('name','ASC');
        $activities->where('parent_id',$parentId);
        if (isset($number))
            $activities->limit($number, $offset);
        return $activities->find_all();
    }

    public function hasActiveEvaluations() {
        // First: if any of the children activities has active evaluations, this one either
        $children = $this->children;
        foreach ($children as $child) {
            if ($child->hasActiveEvaluations())
                return true;
        }

        // Second: if this activity contains an evaluation which is open or over return true
        $evaluations = $this->evaluations;
        foreach ($evaluations as $evaluation) {
            if ($evaluation->isOpen()||$evaluation->isOver())
                return false;
        }

        // Third: return false otherwise
        return false;
    }

    public function delete() {
        if ($this->loaded) {
            // First: delete children activities
            $children = $this->children;
            foreach ($children as $child) {
                $child->delete();
            }

            // Second: delete corresponding evaluations if any
            $evaluations = $this->evaluations;
            foreach ($evaluations as $evaluation) {
                $evaluation->delete();
            }

            // Third: delete the activity
            parent::delete();
        }
    }

    public function keepOldValues(& $array) {
        if (!isset($array['view_id']))
            $array['view_id'] = $this->view_id;
        if (!isset($array['edit_id']))
            $array['edit_id'] = $this->edit_id;
        if (!isset($array['parent_id']))
            $array['parent_id'] = $this->parent_id;
    }

    public function setLogoFromUploadedPhoto($save = false) {
        switch ($_FILES['logo']['type']) {
            case "image/jpeg":
                $extension=".jpg";
                break;
            case "image/gif":
                $extension=".gif";
                break;
            case "image/png":
                $extension=".png";
                break;
        }
        $file = File_Model::newFile('logo',$extension);

        $filename = upload::save('logo');

        $image = Image::factory($filename);
        if ($image->width>150 || $image->height>70)
            $image->resize(150, 70, Image::AUTO);
        $image->save($file->getFile(),Kohana::config('toucan.public_file_mode'));

        unlink($filename);

        $this->logo_id = $file->id;

        if ($save)
            $this->save();
    }

    public function count(& $filter , & $user, $constraintId = null) {
        return 0;
    }
}
?>