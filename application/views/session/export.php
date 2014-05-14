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
?>
<script type="text/javascript">
    function progress() {
        if (fetchRequired) {
            fetchingRequest = new Ajax.Updater("progress", '<?php echo html::url($fetchUrl)?>', { method: 'get',  evalScripts: true});
        } else {
            completed();
        }
    }


    function completed() {
        window.location.href = '<?php echo html::url($resultUrl)?>';
    }    

    var fetchRequired = true;
    
    document.observe("dom:loaded", function() {
        progress();
    });
</script>
<?php
    include APPPATH."/views/data/inc_description.php";
?>
<div id='progress'>
    <div class='progress_bar'>
    </div>
</div>
<?php
    include APPPATH."/views/data/inc_information.php";
    include APPPATH."/views/data/inc_actions.php";
?>