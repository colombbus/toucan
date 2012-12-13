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
<?php
    if (isset($automaticSaveUrl))
        $INCLUDE_BEFORE_CLOSING = "<div id =\"automatic_save_message\" style=\"display:none\"></div>";
    include APPPATH."/views/data/edit.php";
?>
<script language = "javascript">
    function saveCopy() {
        $('state_id').value = <?php echo $goingOnState ?>;
        $('<?php echo $formId; ?>').submit();
    }
<?php
    if (isset($automaticSaveUrl)) {
?>
    function automaticSave() {
        parameters = $('<?php echo $formId; ?>').serialize();
        parameters += "&currentTime="+Math.floor((new Date()).getTime() / 1000);
        new Ajax.Updater('automatic_save_message','<?php echo html::url($automaticSaveUrl)?>', { method: 'post',  evalScripts:true, parameters: parameters});
    }

    new PeriodicalExecuter(function(pe) {  automaticSave(); }, 240);


<?php
    }
    if (isset($questionConditionals)) {
?>
   form = $('<?php echo $formId;?>');
   radios = form.getInputs('radio');
   checkboxes = form.getInputs('checkbox');
   radios.each(function(e) {e.observe('click', checkTriggers);});
   radios.each(function(e) {e.observe('keypressed', checkTriggers);});
   checkboxes.each(function(e) {e.observe('click', checkTriggers);});
   checkboxes.each(function(e) {e.observe('keypressed', checkTriggers);});

   function checkTriggers(event) {
	    element = event.element();
	    questionId = element.readAttribute('question_id');
	    index = triggers.indexOf(questionId);
	    if (index>-1) {
	    	triggeredQuestions = triggered[index];
	    	for (i=0;i< triggeredQuestions.length;i++) {
	    	    triggeringChoices = choices[index][i];
	    	    display = false;
	    	    for (j=0;j< triggeringChoices.length;j++) {
	    	    	choice = $(triggeringChoices[j]);
                    if (choice.checked) {
                        display = true;
                        break;
                    }
	    	    }
	    	    if (display) {
		    	    $("form_"+triggeredQuestions[i]).show();
	    	    } else {
	    	    	$("form_"+triggeredQuestions[i]).hide();
	    	    }
		    }
	    }
   }
   triggers = new Array();
   triggered = new Array();
   choices = new Array();
<?php
        $i=0;
        foreach ($questionConditionals as $key=>$question) {
?>
   triggers[<?php echo $i; ?>] = "question_<?php echo $key;?>";
   triggered[<?php echo $i; ?>] = Array();
   choices[<?php echo $i; ?>] = Array();
<?php
            $j = 0;
            foreach ($question as $triggered=>$choices) {
?>
   triggered[<?php echo $i; ?>][<?php echo $j; ?>] = "question_<?php echo $triggered;?>";
   choices[<?php echo $i; ?>][<?php echo $j; ?>] = Array();
<?php
                $k = 0;
                foreach ($choices as $choice) {
?>
   choices[<?php echo $i; ?>][<?php echo $j; ?>][<?php echo $k; ?>] = "question_<?php echo $key;?>_<?php echo $choice;?>";
<?php
                    $k++;
                }
                $j++;
            }
            $i++;
        }
    }
?>

</script>