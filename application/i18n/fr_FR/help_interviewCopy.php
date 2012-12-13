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

    "create"=>"<h1>Saisie d'un nouveau rapport d'entretien</h1>
    Vous pouvez saisir un nouveau rapport d'entretien. Un rapport d'entretien se compose de deux parties : 
    <ul>
    <li>Un <b>rapport général</b> sur l'entretien</li>
    <li>Un <b>rapport par question</b>, qui permet de saisir des informations relatives à chacune des questions</li>
    </ul>
    <h1>Actions</h1>
    Les actions disponibles sont :
    <ul>
    <li><b>Annuler</b> : annuler la saisie et revenir à l'écran précédent</li>
    <li><b>Sauvegarder sans publier</b> : le rapport ne sera visible que de vous et ne sera pas pris en compte dans l'évaluation. Vous pourrez y revenir ultérieurement pour le modifier ou le compléter.</li>
    <li><b>Sauvegarder</b> : enregistrer le rapport qui sera pris en compte dans l'évaluation.</li>
    </ul>
    <h1>Sauvegarde automatique</h1>
    Les rapports en cours de rédaction sont sauvegardés automatiquement toutes les 5 minutes environ. 
    Un message d'information apparaît alors en bas de la page, avec l'heure de la sauvegarde. Les rapports sauvegardés ne sont pas publiés et peuvent être retrouvés dans la liste des rapports.
    ",
    
    "show"=>"<h1>Affichage d'un rapport d'entretien</h1>
    <ul>
    <li><b>Créé par</b> : le nom de la personne l'ayant rempli</li>
    <li><b>Date de création</b> : la date et l'heure d'enregistrement du rapport</li>
    <li><b>Etat</b> : le rapport peut être <i>sauvegardé automatiquement</i>, <i>provisoire</i>, <i>enregistré</i>, <i>signalé</i> ou <i>traité</i></li>
    </ul>
    <h1>Liste des fichiers joints</h1>
    Si des fichiers ont été joints à ce rapport, ils apparaissent après le rapport, sous la forme de liste. Si vous avez les droits pour modifier la session, des liens apparaissent à côté de chaque fichier pour en modifier le titre ou le supprimer.
    <h1>Joindre un fichier (évaluateurs)</h1>
    Vous avez la possibilité de joindre un nouveau fichier au rapport d'entretien en cliquant sur le bouton &laquo;&nbsp;Joindre un fichier&nbsp;&raquo; en bas de la page. Celui-ci fait apparaître un formulaire vous permettant de choisir le fichier sur votre ordinateur 
    et de lui attribuer un titre (optionnel).
    
    <h1>Actions rapides (gestionnaires)</h1>
    Si vous avez l'autorisation de modifier la session (propriétaire, gestionnaire, administrateur) et que ce rapport n'est pas provisoire, des boutons en bas à droite vous permettent de changer son état :
    <ul class='no_bullet'>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/page_white.png"))." enregistré</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/page_white_star.png"))." signalé</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/page_white_tick.png"))." traité</li>
    </ul>

    ",

    "edit"=>"<h1>Modification d'un rapport d'entretien</h1>
    Cet écran vous permet de modifier un rapport d'entretien, et éventuellement d'en modifier l'état.
    <h1>Changement d'état du rapport d'entretien</h1>
    S'il s'agit d'un rapport <i>non publié</i> ou <i>sauvegardé automatiquement</i>, vous pouvez choisir entre les boutons &laquo;&nbsp;Sauvegarder sans publier&nbsp;&raquo; et &laquo;&nbsp;Publier&nbsp;&raquo; :
    <ul>
    <li><b>Sauvegarder sans publier</b> : le rapport ne sera visible que de vous et ne sera pas pris en compte dans l'évaluation. Vous pourrez y revenir ultérieurement pour le modifier ou le compléter.</li>
    <li><b>Sauvegarder</b> : enregistrer le rapport qui sera pris en compte dans l'évaluation.</li>
    </ul>
    Un rapport <i>publié</i> ne peut pas être dépublié.
    ",

);
?>