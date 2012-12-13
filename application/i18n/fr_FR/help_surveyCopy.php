<?php defined('SYSPATH') or die('No direct access allowed.');
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

$lang=array(

    "create"=>"<h1>Saisie d'un nouveau questionnaire</h1>
    Vous pouvez saisir un nouvel exemplaire de questionnaire. Les actions disponibles sont :
    <ul>
    <li><b>Annuler</b> : annuler la saisie et revenir à l'écran précédent</li>
    <li><b>Sauvegarder sans publier</b> : l'exemplaire ne sera visible que de vous et ne sera pas pris en compte dans l'évaluation. Vous pourrez y revenir ultérieurement pour le modifier ou le compléter. La saisie des questions obligatoires n'est pas vérifiée.</li>
    <li><b>Sauvegarder</b> : enregistrer l'exemplaire qui sera pris en compte dans l'évaluation. Toutes les questions obligatoires devront être remplies.</li>
    </ul>
    <h1>Sauvegarde automatique</h1>
    Les exemplaires en cours de rédaction sont sauvegardés automatiquement toutes les 5 minutes environ. 
    Un message d'information apparaît alors en bas de la page, avec l'heure de la sauvegarde. Les exemplaires sauvegardés ne sont pas publiés et peuvent être retrouvés dans la liste des exemplaires.
    ",
    
    "show"=>"<h1>Affichage d'un exemplaire de questionnaire</h1>
    <ul>
    <li><b>Créé par</b> : le nom de la personne l'ayant rempli - en cas de questionnaire public, &lt;public&gt; apparaît</li>
    <li><b>Date de création</b> : la date et l'heure d'enregistrement de l'exemplaire</li>
    <li><b>Etat</b> : l'exemplaire peut être <i>sauvegardé automatiquement</i>, <i>provisoire</i>, <i>enregistré</i>, <i>signalé</i> ou <i>traité</i></li>
    </ul>
    
    <h1>Actions rapides (gestionnaires)</h1>
    Si vous avez l'autorisation de modifier l'enquête (propriétaire, gestionnaire, administrateur) et que cet exemplaire n'est pas provisoire, des boutons en bas à droite vous permettent de changer son état :
    <ul class='no_bullet'>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/page_white.png"))." enregistré</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/page_white_star.png"))." signalé</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/page_white_tick.png"))." traité</li>
    </ul>
    ",

    "edit"=>"<h1>Modification d'un questionnaire</h1>
    Cet écran vous permet de modifier les réponses d'un questionnaire, et éventuellement d'en modifier l'état.
    <h1>Changement d'état du questionnaire</h1>
    S'il s'agit d'un questionnaire <i>non publié</i> ou <i>sauvegardé automatiquement</i>, vous pouvez choisir entre les boutons &laquo;&nbsp;Sauvegarder sans publier&nbsp;&raquo; et &laquo;&nbsp;Publier&nbsp;&raquo; :
    <ul>
    <li><b>Sauvegarder sans publier</b> : l'exemplaire ne sera visible que de vous et ne sera pas pris en compte dans l'évaluation. Vous pourrez y revenir ultérieurement pour le modifier ou le compléter. La saisie des questions obligatoires n'est pas vérifiée.</li>
    <li><b>Sauvegarder</b> : enregistrer l'exemplaire qui sera pris en compte dans l'évaluation. Toutes les questions obligatoires devront être remplies.</li>
    </ul>
    Un exemplaire <i>publié</i> ne peut pas être dépublié.
    ",

);
?>