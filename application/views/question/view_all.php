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
    function addChoice(id) {
        elementName = "item_choices_content_"+id;
        countElementName = "item_choices_count_"+id;
        choiceCount = parseInt($F(countElementName));

        new Ajax.Updater(elementName, '<?php echo html::url($addChoiceUrl)?>/'+id+'/'+choiceCount, {
            method: 'get',
            evalScripts: true,
            insertion:'bottom'
        });
        $(countElementName).setValue(choiceCount+1);
    }

    function deleteChoice(id, elementName) {
        $(elementName).remove();
        countElementName = "item_choices_count_"+id;
        choiceCount = parseInt($F(countElementName));
        $(countElementName).setValue(choiceCount-1);
    }

    function editTriggers(id) {
        elementName = "item_"+id;
        new Ajax.Updater(elementName, '<?php echo html::url($triggersUrl)?>/'+id, { method: 'get',  evalScripts: true});
    }

    function saveTriggers(id) {
        elementName = 'item_'+id;
        formName = 'form_question_triggers_'+id;
        new Ajax.Updater(elementName, '<?php echo html::url($triggersUrl)?>/'+id , {
            method: 'post',
            parameters:$(formName).serialize(true),
            evalScripts: true
        });
    }

    function addSeparator() {
        addItem('<?php echo html::url($addSeparatorUrl)?>');
    }

    function deleteSeparator(id) {
        deleteItem(id, "<?php echo addslashes(Kohana::lang($confirmSeparatorDeletion));?>");
    }



</script>
<?php
    include APPPATH."/views/data/view_items.php";
?>