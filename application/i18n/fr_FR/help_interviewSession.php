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
    
    "show"=>"<h1>Affichage du détail d'une session d'entretien</h1>
    <ul>
    <li><b>Nom</b> : le nom de la session d'entretien</li>
    <li><b>Description</b> : un texte optionnel décrivant la session</li>
    <li><b>Etat</b> : la session peut être <i>en élaboration</i>, <i>en cours</i>, <i>terminée</i> ou <i>annulée</i></li>
    <li class='help_separator'></li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant consulter cette session</li>
    <li><b>Gestionnaires</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier les informations de cette session</li>
    <li class='help_separator'></li>
    <li><b>Evaluateurs</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant saisir des rapports d'entretien</li>
    <li class='help_separator'></li>
    <li><b>Notification</b> : La méthode de notification sélectionnée pour signaler l'enregistrement d'un exemplaire
    <ul>
    <li><b>aucune</b> : aucun mail n'est envoyé</li>
    <li><b>envoi d'un mail au propriétaire</b> : le créateur de la session reçoit un mail</li>
    <li><b>envoi d'un mail aux gestionnaires</b> : les gestionnaires de la session reçoivent un mail</li>
    <li><b>envoi d'un mail à une autre adresse</b> : un mail est envoyé à l'adresse affichée</li>
    </ul>
    </li>
    </ul>
    
    <h1>Actions rapides (gestionnaires)</h1>
    Si vous avez l'autorisation de modifier la session (propriétaire, gestionnaire, administrateur), des boutons en bas à droite vous permettent de changer directement son état :
    <ul class='no_bullet'>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/under_construction.png"))." en élaboration</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/going_on.png"))." en cours</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/over.png"))." terminée</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/cancelled.png"))." annulée</li>
    </ul>
    ",

    "edit"=>"<h1>Modification des informations d'une session d'entretien</h1>
    <ul>
    <li><b>Nom</b> : le nom de la session</li>
    <li><b>Description</b> : un texte optionnel décrivant la session</li>
    <li><b>Etat</b> : une session peut être <i>en élaboration</i>, <i>en cours</i>, <i>terminée</i> ou <i>annulée</i></li>
    <li class='help_separator'></li>
    <li><b>Appliquer le même contrôle d'accès que pour l'évaluation</b> : si la case est cochée, la session est consultable par tous les utilisateurs qui peuvent consulter l'évaluation et est modifiable par tous ceux qui peuvent modifier l'évaluation</li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant lire les informations de cette session</li>
    <li><b>Gestionnaires</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier les informations de cette session</li>
    <li class='help_separator'></li>
    <li><b>Notification</b> : Toucan peut envoyer un mail à chaque fois qu'un rapport d'entretien est saisi, suivant la méthode sélectionnée :
    <ul>
    <li><b>aucune</b> : aucun mail n'est envoyé</li>
    <li><b>envoi d'un mail au propriétaire</b> : le créateur de la session recevra un mail</li>
    <li><b>envoi d'un mail aux gestionnaires</b> : les gestionnaires de la session recevront un mail</li>
    <li><b>envoi d'un mail à une autre adresse</b> : vous pourrez saisir une adresse différente à laquelle Toucan enverra un mail</li>
    </ul>
    </li>
    </ul>
    ",

    "owner"=>"<h1>Modification du propriétaire d'une session (administration)</h1>
    Sélectionnez un utilisateur dans la liste pour en faire le nouveau propriétaire de la session.
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
    Si vous avez les droits pour modifier la session et si celle-ci n'est pas dans l'état &laquo;&nbsp;en cours&nbsp;&raquo;, vous pouvez ajouter une question (bouton en bas de l'écran) : un nouveau cadre apparaît, dans lequel vous pouvez 
    entrer les informations sur cette nouvelle question.

    <h1>Ajouter un séparateur (gestionnaires)</h1>
    Si vous avez les droits pour modifier la session et si celle-ci n'est pas dans l'état &laquo;&nbsp;en cours&nbsp;&raquo;, vous pouvez ajouter un séparateur (bouton en bas de l'écran). Un séparateur permet de définir plusieurs sections dans le questionnaire. 
    <br/>Il contient les informations suivantes :
    <ul>
    <li><b>Texte</b> : le texte qui sera affiché sur le séparateur</li>
    <li><b>Description</b> : un texte optionnel qui sera également affiché sur le questionnaire, juste après le séparateur</li>
    </ul>

    <h1>Modifier l'ordre des questions (gestionnaires)</h1>
    Si vous avez les droits pour modifier la session et si celle-ci n'est pas dans l'état &laquo;&nbsp;en cours&nbsp;&raquo;, vous pouvez modifier l'ordre dans lequel 
    apparaissent les questions : il vous suffit de cliquer et de faire glisser l'image".html::image(array("src"=>Kohana::config("toucan.images_directory")."/handle.png"))."
    ",

    "createStart"=>"<h1>Création d'une session - choix du type d'entretien</h1>
    Vous pouvez  :
    <ul>
    <li><b>Créer une session à partir d'un modèle d'entretien</b> : si vous choisissez cette option, Toucan vous demandera ensuite de choisir le modèle</li>
    <li><b>Créer une session en créant un nouvel entretien</b></li>
    </ul>
    ",

    "selectTemplate"=>"<h1>Création d'une session - choix du modèle d'entretien</h1>
    Cet écran donne la liste des modèles d'entretien auxquels vous avez accès : choisissez celui sur lequel vous voulez baser la session.
    ",

    "copyIndicators"=>"<h1>Création d'une session - récupération des indicateurs du modèle</h1>
    Vous pouvez choisir d'importer dans votre évaluation des indicateurs définis dans le modèle d'entretien : sélectionnez ceux que vous souhaitez récupérer, ou sélectionnez-les tous grâce au bouton &laquo;&nbsp;Sélectionner tous les indicateurs&nbsp;&raquo;. Si vous ne souhaitez pas en importer, cliquez directement sur &laquo;&nbsp;Ignorer les indicateurs&nbsp;&raquo; en bas de l'écran, ou cliquez sur &laquo;&nbsp;Enregistrer&nbsp;&raquo; sans avoir sélectionné d'indicateur.
    ",
    
    "create"=>"<h1>Création d'une session</h1>
    Vous pouvez précisez les informations suivantes :
    <ul>
    <li><b>Nom</b> : le nom de la session</li>
    <li><b>Description</b> : un texte optionnel décrivant la session</li>
    <li><b>Etat</b> : une session, peut être <i>en élaboration</i>, <i>en cours</i>, <i>terminée</i> ou <i>annulée</i></li>
    <li class='help_separator'></li>
    <li><b>Appliquer le même contrôle d'accès que pour l'évaluation</b> : si la case est cochée, la session est consultable par tous les utilisateurs qui peuvent consulter l'évaluation et est modifiable par tous ceux qui peuvent modifier l'évaluation</li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant lire les informations de cette session</li>
    <li><b>Gestionnaires</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier les informations de cette session</li>
    <li class='help_separator'></li>
    <li><b>Evaluateurs</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant saisir des rapports d'entretien</li>
    <li class='help_separator'></li>
    <li><b>Notification</b> : Toucan peut envoyer un mail à chaque fois qu'un questionnaire est saisi, suivant la méthode sélectionnée :
    <ul>
    <li><b>aucune</b> : aucun mail n'est envoyé</li>
    <li><b>envoi d'un mail au propriétaire</b> : le créateur de la session recevra un mail</li>
    <li><b>envoi d'un mail aux gestionnaires</b> : les gestionnaires de la session recevront un mail</li>
    <li><b>envoi d'un mail à une autre adresse</b> : vous pourrez saisir une adresse différente à laquelle Toucan enverra un mail</li>
    </ul>
    </li>
    </ul>
    ",
    
    "preview"=>"<h1>Aperçu du questionnaire</h1>
    Cette page vous présente l'aperçu du questionnaire. 
    ",

    "copies"=>"<h1>Liste des rapports d'entretien remplis</h1>
    Cette page donne la liste des rapports d'entretien remplis. 
    <ul>
    <li><b>Rempli par</b> : le nom de l'évaluateur.</li>
    <li><b>Date et heure</b> : la date et l'heure de l'enregistrement du rapport</li>
    <li><b>Etat</b> : le rapport peut être <i>sauvegardé automatiquement</i>, <i>provisoire</i>, <i>enregistré</i>, <i>signalé</i> ou <i>traité</i></li>
    </ul>
    ",
    
    "export"=>"<h1>Export des données de la session</h1>
    Cette page vous permet d'exporter les données saisies dans les rapports d'entretien sous la forme d'un tableau (une ligne par questionnaire, une colonne par réponse ou information).
    <br/>Vous pouvez définir les paramètres suivants :
    <ul>
    <li><b>Inclure le nom de l'auteur</b> : si coché, une colonne est ajoutée pour indiquer le nom de l'enquêteur qui a saisi le questionnaire</li>
    <li><b>Inclure la date et l'heure</b> : si coché, une colonne est ajoutée pour indiquer la date et l'heure d'enregistrement du questionnaire</li>
    <li><b>Inclure les titres des colonnes</b> : si coché, la première ligne du tableau contient l'intitulé de chaque colonne</li>
    <li><b>Inclure les exemplaires non publiés</b> : si coché, les questionnaires non publiés seront inclus dans le tableau</li>
    <li><b>Séparateur de champs</b> : les caractères utilisés pour séparer les colonnes d'une même ligne (';' par défaut)</li>
    <li><b>Séparateur d'enregistrements</b> : les caractères utilisés pour séparer les lignes ('\\n' par défaut)</li>
    <li><b>Encadrer les valeurs par</b> : les caractères utilisés pour encadrer les valeurs de chaque colonne ('\"' par défaut)</li>
    <li><b>Pour les questions à choix multiples, séparer les réponses par</b> : les caractères utilisés pour séparer les choix d'une question à choix multiples</li>
    <li><b>Format d'export</b> : 
    <ul>
    <li><b>Affichage direct à l'écran</b> : le tableau s'affiche dans la page</li>
    <li><b>Fichier CSV</b> : format lisible par MS Excel et OpenOffice</li>
    <li><b>Fichier texte</b></li>
    </ul>
    </li>
    <li><b>Encodage des fichiers</b> (export fichier) : l'encodage du fichier dépend du système sur lequel vous souhaitez le transférer : choisissez Windows ou Linux</li>
    <li><b>N'exporter que les exemplaires d'une période</b> : permet de ne sélectionner que les questionnaires enregistrés au cours de la période spécifiée</li>
    </ul>
    <h1>Export en CSV</h1>
    Le format d'export CSV en gardant les valeurs fournies par défaut pour les séparateurs de champs, d'enregistrements et l'encadrement des valeurs, permet d'obtenir un fichier totalement exploitable sous MS Excel et OpenOffice.
    ",
);
?>