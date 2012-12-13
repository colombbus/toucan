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

class Home_Controller extends Page_Controller {

    public function __construct() {
        parent::__construct();
    }

	public function index () {
        if (defined('INSTALL_IN_PROGRESS'))
            return $this->install();
        return $this->activities();
    }

    public function openEvaluations() {
	    $this->template->content = new View('home');
		$filter = null;

        // LOOK FOR OPEN EVALUATIONS
        // -------------------------
		$evaluations = ORM::factory('evaluation');
        $evaluations = $evaluations->getItems($filter, $this->user, 0,null, array('state_id'=>EvaluationState_Model::GOING_ON));
        $displayableEvaluations = array();
        if (count($evaluations)>0) {
            foreach($evaluations as $evaluation) {
                $infos = array();
                $infos['evaluation'] = array('link'=>'evaluation/show/'.$evaluation->id, 'text'=>$evaluation->name);
                $infos['activity'] = array('link'=>'activity/show/'.$evaluation->activity->id, 'text'=>$evaluation->activity->name);
                if (strlen(trim($evaluation->description))>0) {
                    $infos['description'] = $evaluation->description;
                }
                // LOOK FOR OPEN FORM SESSIONS TO WHICH USER MAY CONTRIBUTE
                // --------------------------------------------------------
                $formSessions = $evaluation->where('state_id',SessionState_Model::GOING_ON)->formSessions;
                $displayableFormSessions = array();
                foreach($formSessions as $session) {
                    if ($session->mayBeContributedBy($this->user)) {
                        $displayableFormSessions[] = array('link'=>'formSession/copies/'.$session->id, 'text'=>$session->name);
                    }
                }
                $sessionsNumber = sizeof($displayableFormSessions);
                if ($sessionsNumber>0) {
                    $infos['formSessions'] = $displayableFormSessions;
                    if ($sessionsNumber==1)
                        $infos['formSessions_title'] = Kohana::lang('home.formSession_one');
                    else
                        $infos['formSessions_title'] = sprintf(Kohana::lang('home.formSession_many'), $sessionsNumber);
                }

                // LOOK FOR OPEN INTERVIEW SESSIONS TO WHICH USER MAY CONTRIBUTE
                // -------------------------------------------------------------
                $interviewSessions = $evaluation->where('state_id',SessionState_Model::GOING_ON)->interviewSessions;
                $displayableInterviewSessions = array();
                foreach($interviewSessions as $session) {
                    if ($session->mayBeContributedBy($this->user)) {
                        $displayableInterviewSessions[] = array('link'=>'interviewSession/copies/'.$session->id, 'text'=>$session->name);
                    }
                }
                $sessionsNumber = sizeof($displayableInterviewSessions);
                if ($sessionsNumber>0) {
                    $infos['interviewSessions'] = $displayableInterviewSessions;
                    if ($sessionsNumber==1)
                        $infos['interviewSessions_title'] = Kohana::lang('home.interviewSession_one');
                    else
                        $infos['interviewSessions_title'] = sprintf(Kohana::lang('home.interviewSession_many'), $sessionsNumber);
                }
                $displayableEvaluations[] = $infos;
            }
            $this->template->content->evaluations = $displayableEvaluations;
            if ($evaluations->count()==1)
                $this->template->content->description = Kohana::lang('home.evaluation_one');
            else
                $this->template->content->description = sprintf(Kohana::lang('home.evaluation_many'), $evaluations->count());
        } else {
            $this->template->content->evaluations = array();
            $this->template->content->description = Kohana::lang('home.no_open_evaluation');
        }
        
        // PAGE INFOS
        $this->setPageInfo('OPEN_EVALUATIONS');
    }


    public function closedEvaluations() {
        $this->template->content = new View('home');
		$filter = null;

        // LOOK FOR CLOSED EVALUATIONS
        // ---------------------------
        $evaluations = ORM::factory('evaluation');
        $evaluations = $evaluations->getItems($filter, $this->user, 0,null, array('state_id'=>EvaluationState_Model::OVER));
        $displayableEvaluationsOver = array();
        if (count($evaluations)>0) {
            foreach($evaluations as $evaluation) {
                $infos = array();
                $infos['link'] = 'evaluation/show/'.$evaluation->id;
                $infos['text'] = $evaluation->name;
                $infos['activity'] = array('link'=>'activity/show/'.$evaluation->activity->id, 'text'=>$evaluation->activity->name);
                if (strlen(trim($evaluation->description))>0) {
                    $infos['description'] = $evaluation->description;
                }
                $displayableEvaluationsOver[] = $infos;
            }
            $this->template->content->evaluationsOver = $displayableEvaluationsOver;
            if ($evaluations->count()==1)
                $this->template->content->description = Kohana::lang('home.evaluation_over_one');
            else
                $this->template->content->description = sprintf(Kohana::lang('home.evaluation_over_many'), $evaluations->count());
        } else {
            $this->template->content->evaluationsOver = array();
            $this->template->content->description = Kohana::lang('home.no_closed_evaluation');
        }

        // PAGE INFOS
        $this->setPageInfo('CLOSED_EVALUATIONS');
	}

    public function activities() {
        $choices = array();
        $this->template->content = new View('home');
        if ($this->testAccess()) {
            $choices[] = array('text'=>Kohana::lang('activity.show_all_title'), 'link'=>'activity/showAll', 'image'=>Kohana::config('toucan.images_directory')."/activity.png"); 
            $choices[] = array('text'=>Kohana::lang('evaluation.show_all_title'), 'link'=>'evaluation/showAll', 'image'=>Kohana::config('toucan.images_directory')."/evaluation.png"); 
            $choices[] = array('text'=>Kohana::lang('survey.show_all_title'), 'link'=>'survey/showAll', 'image'=>Kohana::config('toucan.images_directory')."/survey.png"); 
            $choices[] = array('text'=>Kohana::lang('template.main_title'), 'link'=>'template/toc', 'image'=>Kohana::config('toucan.images_directory')."/template.png"); 
            $choices[] = array('text'=>Kohana::lang('user.show_all_title'), 'link'=>'user/showAll', 'image'=>Kohana::config('toucan.images_directory')."/user.png"); 
        } else {
            $choices[] = array('text'=>Kohana::lang('activity.show_all_title'), 'link'=>'activity/showAll', 'image'=>Kohana::config('toucan.images_directory')."/activity.png"); 
            $choices[] = array('text'=>Kohana::lang('evaluation.show_all_title'), 'link'=>'evaluation/showAll', 'image'=>Kohana::config('toucan.images_directory')."/evaluation.png"); 
            $choices[] = array('text'=>Kohana::lang('survey.show_all_title'), 'link'=>'survey/showAll', 'image'=>Kohana::config('toucan.images_directory')."/survey.png"); 
        }
        $this->template->content->choices  = $choices;
        $this->template->content->selectType = "select_home";

        
        
        /*$activityControler = new Activity_Controller();
        $activityControler->showAll();
        $activityControler->auto_render = false;
        $this->template->content = new View('home');
        $this->template->content->activities = $activityControler->template->content->items;
        $this->template->content->description = Kohana::lang('activity.show_all_description');*/
        // PAGE INFOS
        $this->setPageInfo('ACTIVITIES');
    }

    public function setPageInfo($action) {
        if ($this->hasMessage())
            $this->template->content->message = $this->getMessage();
        $this->template->content->title = Kohana::lang('home.welcome');
        $this->template->content->pathType = "path_home";
        if (!$this->testAccess()) {
            $this->template->content->public_access = Kohana::lang('home.public_access');
        }
        $this->template->content->description = Kohana::lang('home.start');
        $this->setActions($action);
    }

    protected function setActions($action) {
        $actions = array();
        $actions_back = array();
        $tabs = array();
        /*$tabs[] = array('text'=>'home.activities', 'link' => 'home/activities', 'image' => Kohana::config('toucan.images_directory')."/activity.png");
        $tabs[] = array('text'=>'home.open_evaluations', 'link' => 'home/openEvaluations', 'image' => Kohana::config('toucan.images_directory')."/open.png");
        $tabs[] = array('text'=>'home.closed_evaluations', 'link' => 'home/closedEvaluations', 'image' => Kohana::config('toucan.images_directory')."/published.png");
        switch($action) {
            case 'ACTIVITIES' :
                $tabs[0]['current'] = 1;
                break;
            case 'OPEN_EVALUATIONS' :
                $tabs[1]['current'] = 1;
                break;
            case 'CLOSED_EVALUATIONS' :
                $tabs[2]['current'] = 1;
                break;
        }*/
        $this->template->content->actions = $actions;
        $this->template->content->actions_back = $actions_back;
        $this->template->content->tabs = $tabs;
    }

    protected function install() {
        $this->template = new View('install');
        $failed = false;
        $tests = array();
        // 1st test "site domain" config
        $domainTest = array();
        $domainTest['title'] = "Site domain";
        $siteDomain = Kohana::config("config.site_domain");
        if ($siteDomain == NULL) {
            $domainTest['result'] = "not defined: please define constant \$config[\'site_domain\'] in file <code>application/config/config".EXT."</code>";
            $domainTest['pass'] = false;
            $failed = true;
        } else {
            // site domain should be part of the uri
            $uri = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            if (strpos($uri, $siteDomain) !== FALSE) {
                // domainTest has been found in the uri
                $domainTest['result'] = $siteDomain;
                $domainTest['pass'] = true;
            } else {
                // domainTest has not been found in the uri
                $domainTest['result'] = $siteDomain."<br/>This constant does not match the current site address<br/>please define constant \$config['site_domain'] in file <code>application/config/config".EXT."</code>";
                $domainTest['pass'] = false;
                $failed = true;
            }
        }
        $tests[] = $domainTest;
        
        // 2nd test write permissions to directories
        $writeTest = array();
        $writeTest['title'] = "Write permissions";
        $writeTest['pass'] = true;
        $publicDirectory = DOCROOT."public";
        if (is_writeable($publicDirectory)) {
            $ok = $publicDirectory;
            $public = opendir($publicDirectory);
            while (($file = readdir($public)) !== false) {
                if ((strcmp($file,".")!=0)&&(strcmp($file,"..")!=0)) {
                    $fileName = $publicDirectory.DIRECTORY_SEPARATOR.$file;
                    if (is_dir($fileName)) {
                        if (is_writeable($fileName)) {
                            $ok.="<br/>".$fileName;
                        } else {
                            $writeTest['result'] = "directory <code>$fileName</code> is not writeable";
                            $writeTest['pass'] = false;
                            $failed = true;
                            break;
                        }
                    }
                }
            }
            closedir($public);
            if ($writeTest['pass']) {
                $writeTest['result'] = $ok;
            }
        } else {
            $writeTest['result'] = "directory <code>public</code> is not writeable";
            $writeTest['pass'] = false;
            $failed = true;
        }
        $tests[] = $writeTest;

        // 3rnd test database connection
        $dbTest = array();
        $dbTest['title'] = "Database connection";
        // disable potential errors resulting from connection attempts
        $oldReport = error_reporting(0);
        $db = null;
        try {
            $db = new Database();
            $db->connect();
            $dbTest['result'] = "ok";
            $dbTest['pass'] = true;
        } catch (Kohana_Database_Exception $e) {
            $dbTest['result'] = "cannot connect to database<br/>please define connection parameters in file <code>application/config/database".EXT."</code>";
            $dbTest['pass'] = false;
            $failed = true;
        }
        // Set back error reporting
        error_reporting($oldReport);
        $tests[] = $dbTest;

        if ($dbTest['pass'] && $db !== null) {
            // 4th test presence of required tables
            $tables = array("activities", "answers", "calculations", "choices", "colors", "copies", "copies_files", "copy_states", "evaluations", "evaluation_states", "files", "graphics", "groups", "groups_users", "indicators", "indicator_values", "individuals",  "limits", "long_values", "questions", "question_triggers", "question_types", "roles", "roles_users", "selections", "sessions", "session_states", "short_values", "styles", "templates", "text_values", "users", "user_tokens", "variables");
            $dbTablesTest = array();
            $dbTablesTest['title'] = "Database tables";
            $dbTablesTest['result'] = sizeof($tables)." tables detected";
            $dbTablesTest['pass'] = true;
            foreach ($tables as $table) {
                if (!$db->table_exists($table)) {
                    $dbTablesTest['result'] = "could not find table $table<br/>please initialize database using file <code>toucan.sql</code>";
                    $dbTablesTest['pass'] = false;
                    $failed = true;
                }
            }
            $tests[] = $dbTablesTest;

            if ($dbTablesTest['pass']) {
            // 5th test admin connection
                $adminTest = array();
                $adminTest['title'] = "Admin connection test";
                $admin = ORM::factory('user', 1);
                if ($admin->loaded) {
                    $adminTest['result'] = "ok";
                    $adminTest['pass'] = true;
                } else {
                    $adminTest['result'] = "could not find admin account<br/>please initialize database using file <code>toucan.sql</code>";
                    $adminTest['pass'] = false;
                    $failed = true;
                }
                $tests[] = $adminTest;

                if ($adminTest['pass']) {
                    // 6th test default admin account
                    $adminAccount = array();
                    $adminAccount['title'] = "Default admin account";
                    if ($this->authentication->login("admin","admin")) {
                        $adminAccount['result'] = "username:admin - password:admin<br/>Remember to change this default account, or everybody will have access to administration!";
                        $adminAccount['pass'] = true;
                    } else {
                        $adminAccount['result'] = "admin account is not correctly initialized<br/>please initialize database using file <code>toucan.sql</code>";
                        $adminAccount['pass'] = false;
                        $failed = true;
                    }
                    $tests[] = $adminAccount;
                }
            }
        }



        // last: transmit results to the view
        $this->template->failed = $failed;
        $this->template->tests = $tests;
    }


}
?>