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
    
    "showAll"=>"<h1>Liste des modèles d'entretien</h1>
    <ul>
    <li><b>Nom</b> : le nom du modèle d'entretien</li>
    <li><b>Description</b> : un texte descriptif optionnel</li>
    </ul>
    ",
    
    "show"=>"<h1>Détails d'un modèle d'entretien</h1>
    <ul>
    <li><b>Nom</b> : le nom du modèle d'entretien</li>
    <li><b>Description</b> : un texte optionnel décrivant le modèle</li>
    <li class='help_separator'></li>
    <li><b>Visible par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant consulter ce modèle</li>
    <li><b>Modifiable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier ce modèle</li>
    ",

    "edit"=>"<h1>Modification des informations d'un modèle d'entretien</h1>
    <ul>
    <li><b>Nom</b> : le nom du modèle d'entretien</li>
    <li><b>Description</b> : un texte optionnel décrivant le modèle</li>
    <li class='help_separator'></li>
    <li><b>Visible par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant consulter ce modèle</li>
    <li><b>Modifiable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier ce modèle</li>
    </ul>
    ",

    "create"=>"<h1>Création d'un modèle d'entretien</h1>
    <ul>
    <li><b>Nom</b> : le nom du modèle d'entretien</li>
    <li><b>Description</b> : un texte optionnel décrivant le modèle</li>
    <li class='help_separator'></li>
    <li><b>Visible par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant consulter ce modèle</li>
    <li><b>Modifiable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier ce modèle</li>
    </ul>
    ",

    "make"=>"<h1>Création d'un modèle à partir d'une session d'entretien</h1>
    Les informations sont pré-remplies avec celles de la session
    <ul>
    <li><b>Nom</b> : le nom du modèle d'entretien</li>
    <li><b>Description</b> : un texte optionnel décrivant le modèle</li>
    <li class='help_separator'></li>
    <li><b>Visible par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant consulter ce modèle</li>
    <li><b>Modifiable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier ce modèle</li>
    </ul>
    ",
    
    "copy"=>"<h1>Création d'une nouvelle version du modèle</h1>
    Vous pouvez créer une nouvelle version du modèle d'entretien qui pourra être modifiée sans toucher au modèle original.
    <ul>
    <li><b>Nom</b> : le nom de la session</li>
    <li><b>Description</b> : un texte optionnel décrivant la session</li>
    <li class='help_separator'></li>
    <li><b>Visible par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant consulter ce modèle</li>
    <li><b>Modifiable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier ce modèle</li>
    </ul>
    ",

    "owner"=>"<h1>Modification du propriétaire d'un modèle (administration)</h1>
    Sélectionnez un utilisateur dans la liste pour en faire le nouveau propriétaire de ce modèle.
    ",
    
    "questions"=>"<h1>Liste des questions</h1>
    Cette page présente la liste des questions définies pour les entretiens. Chaque question est représentée sous la forme d'un cadre. Vous pouvez afficher ou masquer les détails d'une question, ou bien agir directement sur toutes les questions grâce aux boutons 
    &laquo;&nbsp;Cacher tous les détails&nbsp;&raquo; et &laquo;&nbsp;Montrer tous les détails&nbsp;&raquo;.
    <br/>Chaque question contient les informations suivantes :
    <ul>
    <li><b>Texte</b> : l'intitulé de la question</li>
    <li><b>Description</b> : un texte optionnel de description. Ce texte est affiché à côté de la question lors de la saisie du rapport d'entretien</li>
    </ul>
    <h1>Ajouter une question (gestionnaires)</h1>
    Si vous avez les droits pour modifier le modèle, vous pouvez ajouter une question (bouton en bas de l'écran) : un nouveau cadre apparaît, dans lequel vous pouvez 
    entrer les informations sur cette nouvelle question.

    <h1>Ajouter un séparateur (gestionnaires)</h1>
    Si vous avez les droits pour modifier le modèle, vous pouvez ajouter un séparateur (bouton en bas de l'écran). Un séparateur permet de définir plusieurs sections dans le questionnaire. 
    <br/>Il contient les informations suivantes :
    <ul>
    <li><b>Texte</b> : le texte qui sera affiché sur le séparateur</li>
    <li><b>Description</b> : un texte optionnel qui sera également affiché sur le questionnaire, juste après le séparateur</li>
    </ul>

    <h1>Modifier l'ordre des questions (gestionnaires)</h1>
    Si vous avez les droits pour modifier la session et si celle-ci n'est pas dans l'état &laquo;&nbsp;en cours&nbsp;&raquo;, vous pouvez modifier l'ordre dans lequel 
    apparaissent les questions : il vous suffit de cliquer et de faire glisser l'image".html::image(array("src"=>Kohana::config("toucan.images_directory")."/handle.png"))."
    ",

    "indicators"=>"<h1>Liste des indicateurs liés au modèle</h1>
    Cette page présente la liste des indicateurs définis dans le modèle. Chaque indicateur est représenté sous la forme d'un cadre.
    Seul un <b>indicateur manuel</b> peut être associé à un modèle d'entretien, c'est à dire un indicateur qui affiche la valeur telle qu'elle a été définie par les évaluateurs. En fonction de la manière dont il a été défini, sa couleur peut changer en fonction de cette valeur.
    <h1>Réorganiser les indicateurs (gestionnaires)</h1>
    Si vous avez les droits pour modifier le modèle, vous pouvez modifier l'ordre dans lequel 
    apparaissent les indicateurs : il vous suffit de cliquer et de faire glisser l'image".html::image(array("src"=>Kohana::config("toucan.images_directory")."/handle.png"))."
    ",

    "copyIndicators"=>"<h1>Création d'un modèle - récupération des indicateurs</h1>
    Vous pouvez choisir d'importer dans votre modèle des indicateurs définis à partir du questionnaire : sélectionnez ceux que vous souhaitez récupérer, ou sélectionnez-les tous grâce au bouton &laquo;&nbsp;Sélectionner tous les indicateurs&nbsp;&raquo;. Si vous ne souhaitez pas en importer, cliquez directement sur &laquo;&nbsp;Ignorer les indicateurs&nbsp;&raquo; en bas de l'écran, ou cliquez sur &laquo;&nbsp;Enregistrer&nbsp;&raquo; sans avoir sélectionné d'indicateur.
    ",
    
    "preview"=>"<h1>Aperçu du rapport d'entretien</h1>
    Cette page vous présente l'aperçu d'un rapport d'entretien.
    ",
    
);
?>