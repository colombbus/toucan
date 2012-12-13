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
    "show"=>"<h1>Affichage du détail d'une enquête</h1>
    <ul>
    <li><b>Nom</b> : le nom de l'enquête</li>
    <li><b>Description</b> : un texte optionnel décrivant l'enquête</li>
    <li><b>Etat</b> : une enquête peut être <i>en élaboration</i>, <i>en cours</i>, <i>terminée</i> ou <i>annulée</i></li>
    <li class='help_separator'></li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant consulter cette enquête</li>
    <li><b>Gestionnaires</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier les informations de cette enquête</li>
    <li class='help_separator'></li>
    <li><b>Enquêteurs</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant saisir des questionnaires</li>
    <li class='help_separator'></li>
    <li><b>Adresse d'accès par le public (accès public)</b> : lien menant au questionnaire public, à envoyer aux personnes interrogées</li>
    <li><b>Style (accès public)</b> : modèle de style utilisé pour le questionnaire public</li>
    <li><b>Langue du questionnaire (accès public)</b> : la langue utilisée par Toucan pour afficher les informations aux utilisateurs</li>
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
    Si vous avez l'autorisation de modifier l'enquête (propriétaire, gestionnaire, administrateur), des boutons en bas à droite vous permettent de changer directement son état :
    <ul class='no_bullet'>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/under_construction.png"))." en élaboration</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/going_on.png"))." en cours</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/over.png"))." terminée</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/cancelled.png"))." annulée</li>
    </ul>
    ",
    
    "edit"=>"<h1>Modification des informations d'une enquête</h1>
    <ul>
    <li><b>Nom</b> : le nom de l'enquête</li>
    <li><b>Description</b> : un texte optionnel décrivant l'enquête</li>
    <li><b>Etat</b> : une enquête peut être <i>en élaboration</i>, <i>en cours</i>, <i>terminée</i> ou <i>annulée</i></li>
    <li class='help_separator'></li>
    <li><b>Appliquer le même contrôle d'accès que pour l'activité</b> : si la case est cochée, l'enquête est consultable par tous les utilisateurs qui peuvent consulter l'activité et est modifiable par tous ceux qui peuvent modifier l'activité</li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant lire les informations de cette enquête</li>
    <li><b>Gestionnaires</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier les informations de cette enquête</li>
    <li class='help_separator'></li>
    <li><b>Questionnaire en accès public</b> : cochez la case si vous souhaitez faire remplir le questionaire en ligne</li>
    <li><b>Accès protégé par un mot de passe (accès public)</b> : cochez la case si vous souhaitez que seules les personnes connaissant le mot de passe puissent remplir le questionnaie</li>
    <li><b>Mot de passe (accès public)</b> : le mot de passe qui protège l'accès au questionnaire</li>
    <li><b>Style (accès public)</b> : le modèle de style que vous souhaitez appliquer au questionnaire</li>
    <li><b>Langue du questionnaire (accès public)</b> : la langue que Toucan doit utiliser pour afficher les informations aux utilisateurs</li>
    <li><b>Enquêteurs (accès privé)</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant saisir des questionnaires</li>
    <li class='help_separator'></li>
    <li><b>Notification</b> : Toucan peut envoyer un mail à chaque fois qu'un questionnaire est saisi, suivant la méthode sélectionnée :
    <ul>
    <li><b>aucune</b> : aucun mail n'est envoyé</li>
    <li><b>envoi d'un mail au propriétaire</b> : le créateur de l'enquête recevra un mail</li>
    <li><b>envoi d'un mail aux gestionnaires</b> : les gestionnaires de l'enquête recevront un mail</li>
    <li><b>envoi d'un mail à une autre adresse</b> : vous pourrez saisir une adresse différente à laquelle Toucan enverra un mail</li>
    </ul>
    </li>
    </ul>
    ",

    "owner"=>"<h1>Modification du propriétaire d'une enquête (administration)</h1>
    Sélectionnez un utilisateur dans la liste pour en faire le nouveau propriétaire de l'enquête.
    ",
    
    "questions"=>"<h1>Liste des questions</h1>
    Cette page présente la liste des questions définies pour l'enquête. Chaque question est représentée sous la forme d'un cadre. Vous pouvez afficher ou masquer les détails d'une question, ou bien agir directement sur toutes les questions grâce aux boutons 
    &laquo;&nbsp;Cacher tous les détails&nbsp;&raquo; et &laquo;&nbsp;Montrer tous les détails&nbsp;&raquo;.
    <br/>Chaque question contient les informations suivantes :
    <ul>
    <li><b>Texte</b> : l'intitulé de la question</li>
    <li><b>Description</b> : un texte optionnel de description. Ce texte est affiché à côté de la question lors de la saisie du questionnaire</li>
    <li><b>Obligatoire</b> : indique si une réponse doit obligatoirement être fournie pour cette question</li>
    <li><b>Nom de la variable</b> : la variable contenant la réponse, qui permet ensuite d'élaborer des indicateurs</li>
    <li><b>Type</b> : 
    <ul>
    <li><b>Texte court</b> (127 caractères maximum)</li>
    <li><b>Texte long</b> (500 caractères maximum)</li>
    <li><b>Nombre entier</b></li>
    <li><b>Valeur décimale</b></li>
    <li><b>Un choix parmi plusieurs</b></li>
    <li><b>Plusieurs choix parmi plusieurs</b></li>
    </ul>
    </li>
    <li><b>Réponses possibles</b> : si la question est de type &laquo;&nbsp;Un choix parmi plusieurs&nbsp;&raquo; ou &laquo;&nbsp;Plusieurs choix parmi plusieurs&nbsp;&raquo; les choix possibles sont affichés
    <ul>
    <li><b>Texte</b> : le texte du choix</li>
    <li><b>Valeur</b> : la valeur prise par la variable lorsque cette réponse est sélectionnée. Si aucune valeur n'est précisée, la variable prendra le texte du choix comme valeur</li>
    </ul>
    </li>
    </ul>
    <h1>Ajouter une question (gestionnaires)</h1>
    Si vous avez les droits pour modifier l'enquête et si celle-ci n'est pas dans l'état &laquo;&nbsp;en cours&nbsp;&raquo;, vous pouvez ajouter une question (bouton en bas de l'écran) : un nouveau cadre apparaît, dans lequel vous pouvez 
    entrer les informations sur cette nouvelle question.

    <h1>Ajouter un séparateur (gestionnaires)</h1>
    Si vous avez les droits pour modifier l'enquête et si celle-ci n'est pas dans l'état &laquo;&nbsp;en cours&nbsp;&raquo;, vous pouvez ajouter un séparateur (bouton en bas de l'écran). Un séparateur permet de définir plusieurs sections dans le questionnaire. 
    <br/>Il contient les informations suivantes :
    <ul>
    <li><b>Texte</b> : le texte qui sera affiché sur le séparateur</li>
    <li><b>Description</b> : un texte optionnel qui sera également affiché sur le questionnaire, juste après le séparateur</li>
    </ul>

    <h1>Conditions d'apparition</h1>
    Une question et un séparateur peuvent être <b>conditionnés</b>, c'est à dire que lors de la saisie d'un questionnaire, ils n'apparaîtront que si une réponse particulière a été sélectionnée. Ceci peut être utile par exemple pour demander d'expliquer une réponse, ou pour alléger l'affichage.
    Pour conditionner une question ou un séparateur, il suffit de cliquer sur l'action &laquo;&nbsp;Conditions d'apparition&nbsp;&raquo;. Toucan affiche alors la liste des questions à choix multiples présente dans le questionnaire : il suffit ensuite de sélectionner la question ainsi que le ou les choix devant déclencher l'apparition de la question ou du séparateur.

    <h1>Modifier l'ordre des questions et des choix (gestionnaires)</h1>
    Si vous avez les droits pour modifier l'enquête et si celle-ci n'est pas dans l'état &laquo;&nbsp;en cours&nbsp;&raquo;, vous pouvez modifier l'ordre dans lequel 
    apparaissent les questions et les choix pour une question à choix multiple : il vous suffit de cliquer et de faire glisser l'image".html::image(array("src"=>Kohana::config("toucan.images_directory")."/handle.png"))."
    ",

    "indicators"=>"<h1>Liste des indicateurs de l'enquête</h1>
    Cette page présente la liste des indicateurs définis pour l'enquête. Chaque indicateur est représenté sous la forme d'un cadre.
    <ul>
    <li>Un  <b>indicateur graphique</b> s'affiche directement sous la forme d'une représentation graphique</li>
    <li>Un <b>indicateur numérique</b> affiche sa valeur sous la forme &laquo;&nbsp;Valeur : XXX&nbsp;&raquo;. Si des seuils de représentation ont été défini, sa couleur peut varier en fonction de cette valeur.</li>
    <li>Un <b>indicateur manuel</b> affiche la valeur telle qu'elle a été définie par les évaluateurs. En fonction de la manière dont il a été défini, sa couleur peut changer en fonction de cette valeur.</li>
    </ul>
    <h1>Indicateurs non renseignés</h1>
    <ul>
    <li>Si un indicateur ne trouve aucune information pour calculer sa valeur ou sa représentation graphique, il affiche le texte &laquo;&nbsp;aucune valeur&nbsp;&raquo;</li>
    <li>Si un indicateur manuel n'a pas encore été renseigné par les évaluateurs, il affiche le texte &laquo;&nbsp;indicateur non défini&nbsp;&raquo;</li>
    </ul>
    <h1>Exporter les indicateurs</h1>
    Un bouton situé en bas de la page vous permet d'exporter les indicateurs sous la forme d'un document au format rtf (lisible par MS Word, OpenOffice et de nombreux éditeurs de texte).
    <h1>Réorganiser les indicateurs (gestionnaires)</h1>
    Si vous avez les droits pour modifier l'enquête (propriétaire, gestionnaires, administrateur), vous pouvez modifier l'ordre dans lequel 
    apparaissent les indicateurs : il vous suffit de cliquer et de faire glisser l'image".html::image(array("src"=>Kohana::config("toucan.images_directory")."/handle.png"))."
    ",

    "createStart"=>"<h1>Création d'une enquête - choix du questionnaire</h1>
    Vous pouvez  :
    <ul>
    <li><b>Créer une enquête à partir d'un modèle de questionnaire</b> : si vous choisissez cette option, Toucan vous demandera ensuite de choisir le modèle</li>
    <li><b>Créer une enquête en créant un nouveau questionnaire</b></li>
    </ul>
    ",

    "selectActivity"=>"<h1>Création d'une enquête - choix de l'activité</h1>
    Une enquête est nécessairement rattachée à une activité. Cet écran vous présente la liste des activités enregistrées dans Toucan : sélectionnez celle à laquelle la nouvelle enquête sera rattachée.
    ",

    "selectTemplate"=>"<h1>Création d'une enquête - choix du modèle de questionnaire</h1>
    Cet écran donne la liste des modèles de questionnaires auxquels vous avez accès : choisissez celui sur lequel vous voulez baser l'enquête.
    ",

    "copyIndicators"=>"<h1>Création d'une enquête - récupération des indicateurs du modèle</h1>
    Vous pouvez choisir d'importer dans votre enquête des indicateurs définis dans le modèle de questionnaire : sélectionnez ceux que vous souhaitez récupérer, ou sélectionnez-les tous grâce au bouton &laquo;&nbsp;Sélectionner tous les indicateurs&nbsp;&raquo;. Si vous ne souhaitez pas en importer, cliquez directement sur &laquo;&nbsp;Ignorer les indicateurs&nbsp;&raquo; en bas de l'écran, ou cliquez sur &laquo;&nbsp;Enregistrer&nbsp;&raquo; sans avoir sélectionné d'indicateur.
    ",
    
    "create"=>"<h1>Création d'une enquête</h1>
    Vous pouvez précisez les informations suivantes :
    <ul>
    <li><b>Nom</b> : le nom de l'enquête</li>
    <li><b>Description</b> : un texte optionnel décrivant l'enquête</li>
    <li><b>Etat</b> : une enquête peut être <i>en élaboration</i>, <i>en cours</i>, <i>terminée</i> ou <i>annulée</i></li>
    <li class='help_separator'></li>
    <li><b>Appliquer le même contrôle d'accès que pour l'activité</b> : si la case est cochée, l'enquête est consultable par tous les utilisateurs qui peuvent consulter l'activité et est modifiable par tous ceux qui peuvent modifier l'activité</li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant lire les informations de cette activité</li>
    <li><b>Gestionnaires</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier les informations de cette évaluation</li>
    <li class='help_separator'></li>
    <li><b>Questionnaire en accès public</b> : cochez la case si vous souhaitez faire remplir le questionaire en ligne</li>
    <li><b>Accès protégé par un mot de passe (accès public)</b> : cochez la case si vous souhaitez que seules les personnes connaissant le mot de passe puissent remplir le questionnaie</li>
    <li><b>Mot de passe (accès public)</b> : le mot de passe qui protège l'accès au questionnaire</li>
    <li><b>Style (accès public)</b> : le modèle de style que vous souhaitez appliquer au questionnaire</li>
    <li><b>Langue du questionnaire (accès public)</b> : la langue que Toucan doit utiliser pour afficher les informations aux utilisateurs</li>
    <li><b>Enquêteurs (accès privé)</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant saisir des questionnaires</li>
    <li class='help_separator'></li>
    <li><b>Notification</b> : Toucan peut envoyer un mail à chaque fois qu'un questionnaire est saisi, suivant la méthode sélectionnée :
    <ul>
    <li><b>aucune</b> : aucun mail n'est envoyé</li>
    <li><b>envoi d'un mail au propriétaire</b> : le créateur de l'enquête recevra un mail</li>
    <li><b>envoi d'un mail aux gestionnaires</b> : les gestionnaires de l'enquête recevront un mail</li>
    <li><b>envoi d'un mail à une autre adresse</b> : vous pourrez saisir une adresse différente à laquelle Toucan enverra un mail</li>
    </ul>
    </li>
    </ul>
    ",
    
    "showAll"=>"<h1>Liste des enquêtes</h1>
    Cette liste présente l'ensemble des enquêtes enregistrées dans Toucan.
    <ul>
    <li><b>Nom</b> : le nom de l'enquête</li>
    <li><b>Activité</b> : l'activité à laquelle est rattachée l'enquête</li>
    <li><b>Etat</b> : une enquête peut être <i>en élaboration</i>, <i>en cours</i>, <i>terminée</i> ou <i>annulée</i></li>
    </ul>
    <h1>Filtres</h1>
    Les boutons en bas à droite vous permettent de n'afficher que les enquêtes qui sont dans un certain état :
    <ul class='no_bullet'>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/all.png"))." toutes les enquêtes</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/going_on.png"))." enquêtes en cours</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/over.png"))." enquêtes terminées</li>
    </ul>
    ",

    "preview"=>"<h1>Aperçu du questionnaire</h1>
    Cette page vous présente l'aperçu du questionnaire. Vous pouvez tester l'affichage des questions, ainsi que leur apparition si elles sont conditionnées. 
    Si le questionnaire est public, le bouton &laquo;&nbsp;Prévisualiser le questionnaire public&nbsp;&raquo; ouvre une nouvelle fenêtre dans laquelle s'affiche le questionnaire avec le style et la langue choisis.
    ",

    "copies"=>"<h1>Liste des questionnaires remplis</h1>
    Cette page donne la liste les questionnaires remplis. 
    <ul>
    <li><b>Rempli par</b> : le nom de l'enquêteur. Dans le cas d'une enquête publique, cette information est vide.</li>
    <li><b>Date et heure</b> : la date et l'heure de l'enregistrement du questionnaire</li>
    <li><b>Etat</b> : le questionnaire peut être <i>sauvegardé automatiquement</i>, <i>provisoire</i>, <i>enregistré</i>, <i>signalé</i> ou <i>traité</i></li>
    </ul>
    ",
  
    "export"=>"<h1>Export des questionnaires</h1>
    Cette page vous permet d'exporter les données saisies dans les questionnaires sous la forme d'un tableau (une ligne par questionnaire, une colonne par réponse ou information).
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
    <li><b>Encodage des fichiers</b>(export fichier) : l'encodage du fichier dépend du système sur lequel vous souhaitez le transférer : choisissez Windows ou Linux</li>
    <li><b>N'exporter que les exemplaires d'une période</b> : permet de ne sélectionner que les questionnaires enregistrés au cours de la période spécifiée</li>
    </ul>
    <h1>Export en CSV</h1>
    Le format d'export CSV en gardant les valeurs fournies par défaut pour les séparateurs de champs, d'enregistrements et l'encadrement des valeurs, permet d'obtenir un fichier totalement exploitable sous MS Excel et OpenOffice.
    ",
    
    "downloadTemplate"=>"<h1>Téléchargement du modèle : inclure ou non les questions privées</h1>
    Ce questionnaire comprend des questions privées (c'est à dire dont l'accès est réservé aux gestionnaires). Choisissez si vous souhaitez inclure ces questions dans le modèle téléchargé.
    ",

);
?>