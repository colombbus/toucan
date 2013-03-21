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
    "show"=>"<h1>Affichage des détails d'un indicateur</h1>
    <ul>
    <li><b>Nom</b> : le nom de l'indicateur</li>
    <li><b>Description</b> : un texte optionnel décrivant l'indicateur</li>
    <li class='help_separator'></li>
    <li><b>Type</b> : le type d'indicateur. Celui-ci peut être :
    <ul>
    <li><b>Fixé par les évaluateurs</b> : ce sont les évaluateurs qui définissent sa valeur au cours de l'évaluation</li>
    <li><b>Valeur numérique calculée automatiquement</b> : sa valeur se met à jour en fonction des questionnaires saisis</li>
    <li><b>Graphique calculé automatiquement</b> : sa représentation se met à jour en fonction des questionnaires saisis</li>
    </ul>
    </li>
    <li><b>Valeurs</b> (indicateur fixé manuellement) : lien pour consulter les valeurs que peut prendre l'indicateur</li>
    <li><b>Questionnaire utilisé</b> (indicateur automatique) : le questionnaire pris en compte pour le calcul de l'indicateur</li>
    <li class='help_separator'></li>
    <li><b>Visible par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant consulter cet indicateur</li>
    <li><b>Gestionnaires</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier cet indicateur</li>
    <li class='help_separator'></li>
    <li><b>Evaluateurs</b> (indicateur fixé manuellement) : l'utilisateur ou l'ensemble des utilisateurs pouvant définir la valeur de l'indicateur</li>
    </ul>
    ",
    
    "owner"=>"<h1>Modification du propriétaire d'un indicateur (administration)</h1>
    Sélectionnez un utilisateur dans la liste pour en faire le nouveau propriétaire de l'indicateur.
    ",
    
    "edit"=>"<h1>Modification d'un indicateur</h1>
    <ul>
    <li><b>Nom</b> : le nom de l'indicateur</li>
    <li><b>Description</b> : un texte optionnel décrivant l'indicateur</li>
    <li class='help_separator'></li>
    <li><b>Type</b> : le type d'indicateur. Celui-ci peut être :
    <ul>
    <li><b>Fixé par les évaluateurs</b> : ce sont les évaluateurs qui définissent sa valeur au cours de l'évaluation</li>
    <li><b>Valeur numérique calculée automatiquement</b> : sa valeur se met à jour en fonction des questionnaires saisis</li>
    <li><b>Graphique calculé automatiquement</b> : sa représentation se met à jour en fonction des questionnaires saisis</li>
    </ul>
    </li>
    <li><b>Questionnaire utilisé</b> (indicateur automatique) : choisissez le questionnaire à prendre en compte pour le calcul de l'indicateur</li>
    <li class='help_separator'></li>
    <li><b>Appliquer le même contrôle d'accès que pour l'évaluation</b> : si la case est cochée, la session est consultable par tous les utilisateurs qui peuvent consulter l'évaluation et est modifiable par tous ceux qui peuvent modifier l'évaluation</li>
    <li><b>Visible par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant consulter cet indicateur</li>
    <li><b>Gestionnaires</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier cet indicateur</li>
    <li class='help_separator'></li>
    <li><b>Evaluateurs</b> (indicateur fixé manuellement) : l'utilisateur ou l'ensemble des utilisateurs pouvant définir la valeur de l'indicateur</li>
    </ul>
    ",

    "values"=>"<h1>Liste des valeurs pouvant être prises par l'indicateur</h1>
    Lorsqu'un indicateur est fixé manuellement, sa valeur est définie parmi plusieurs valeurs potentielles, listées sur cette page.
    Chaque valeur est représentée sous la forme d'un cadre. Vous pouvez afficher ou masquer les détails d'une valeur, ou bien agir directement sur toutes les valeurs grâce aux boutons 
    &laquo;&nbsp;Cacher tous les détails&nbsp;&raquo; et &laquo;&nbsp;Montrer tous les détails&nbsp;&raquo;.
    <br/>Chaque valeur contient les informations suivantes :
    <ul>
    <li><b>Valeur</b> : le texte correspondant à la valeur</li>
    <li><b>Description</b> : un texte optionnel de description</li>
    <li><b>Couleur</b> : éventuellement, une couleur peut être définie pour la valeur. Dans ce cas, le cadre apparaît dans la couleur sélectionnée</li>
    </ul>
    <h1>Ajouter une valeur (gestionnaires)</h1>
    Si vous avez les droits pour modifier l'indicateur, vous pouvez ajouter une valeur (bouton en bas de l'écran) : un nouveau cadre apparaît, dans lequel vous pouvez 
    entrer les informations sur cette nouvelle valeur.
    <h1>Modifier l'ordre des valeurs (gestionnaires)</h1>
    Si vous avez les droits pour modifier l'indicateur, vous pouvez modifier l'ordre dans lequel 
    apparaissent les valeurs : il vous suffit de cliquer et de faire glisser l'image".html::image(array("src"=>Kohana::config("toucan.images_directory")."/handle.png"))."
    ",

    "set"=>"<h1>Définition de la valeur de l'indicateur</h1>
    Vous pouvez définir la valeur de l'indicateur sur cette page : 
    <ul>
    <li><b>Valeur</b> : la valeur choisie parmi les valeurs possibles</li>
    <li><b>Explications</b> : un texte optionnel permettant de justifier le choix de la valeur</li>
    </ul>
    ",
    
    "graphic"=>"<h1>Affichage des détails du graphique</h1>
    <ul>
    <li><b>Type de graphique</b> : le type de graphique affiché. Celui-ci peut être :
    <ul>
    <li><b>courbe</b></li>
    <li><b>histogramme</b></li>
    <li><b>diagramme en camembert</b></li>
    </ul>
    </li>
    <li><b>Variable</b> : la variable du questionnaire représentée sur le graphique</li>
    </ul>
    <h1>Recalculer le graphique</h1>
    Le graphique se met normalement automatiquement à jour en fonction des exemplaires de questionnaire saisis.
    Cependant, si jamais le graphique ne s'affichait pas correctement ou ne reflétait pas toutes les valeurs prises par la variable du questionnaire, le bouton &laquo;&nbsp;Recalculer&nbsp;&raquo; permet de regénérer le graphique.
    ",

    "editGraphic"=>"<h1>Paramètres d'un indicateur graphique</h1>
    <ul>
    <li><b>Type de graphique</b> : le type de graphique affiché. Celui-ci peut être :
    <ul>
    <li><b>courbe</b></li>
    <li><b>histogramme</b></li>
    <li><b>diagramme en camembert</b></li>
    </ul>
    </li>
    <li><b>Variable</b> : la variable du questionnaire représentée sur le graphique. Vous pouvez choisir parmi la liste des variables du questionnaire sélectionné lors de la définition de l'indicateur.</li>
    <li><b>Inclure le nombre de &laquo;&nbsp;non répondus&nbsp;&raquo;</b> : cochez cette case si vous souhaitez inclure les exemplaires pour lesquels la variable n'a pas de valeur</li>
    <li><b>Afficher les textes au lieu des valuers</b> : cochez cette case si vous souhaitez que le graphique affiche le texte correspondant aux valeurs de la variable (tels que définis dans le questionnaire) plutôt que ces valeurs elles-mêmes.</li>
    <li><b>Titre du graphique</b> : un titre optionnel pour le graphique</li>
    <li><b>Légende en abscisse</b> : un texte optionnel à afficher sur l'axe des abscisses</li>
    <li><b>Légende en ordonnée</b> : un texte optionnel à afficher sur l'axe des ordonnées</li>
    </ul>
    ",

    "population"=>"<h1>Sous-population d'un indicateur</h1>
    Un indicateur automatique peut être calculé à partir d'un sous-ensemble des exemplaires d'un questionnaire. Ce sous-ensemble, appelé dans Toucan &laquo;&nbsp;sous-population&nbsp;&raquo;, est alors décrit par une liste de critères, reliés entre eux par un opérateur : ET ou OU.
    Chaque critère est représenté sous la forme d'un cadre et contient les informations suivantes :
    <ul>
    <li><b>Variable</b> : la variable du questionnaire sur laquelle le critère est défini</li>
    <li><b>Test</b> : le test qui est réalisé sur cette variable. Ce test peut être : <ul>
    <li><b>égale à</b> : la variable est égale à la valeur précisée</li>
    <li><b>différente de</b> : la variable est différente de la valeur précisée</li>
    <li><b>définie</b> : la variable est définie (la question correspondante a une réponse)</li>
    <li><b>non définie</b> : la variable est non définie (la question correspondante est non répondue)</li>
    <li><b>inférieure à</b> : la variable (numérique) est strictement inférieure à la valeur précisée</li>
    <li><b>supérieure à</b> : la variable (numérique) est strictement supérieure à la valeur précisée</li>
    <li><b>inférieure ou égale à</b> : la variable (numérique) est inférieure ou égale à la valeur spécifiée</li>
    <li><b>supérieure ou égale à</b> : la variable (numérique) est supérieure ou égale à la valeur spécifiée</li>
    </ul>
    </li>
    <li><b>Valeur</b> : une valeur qui précise le test (n'apparaît pas dans le cas d'un test &laquo;&nbsp;définie&nbsp;&raquo; ou &laquo;&nbsp;non définie&nbsp;&raquo;)</li>
    </ul>
    <h1>Ajouter un critère (gestionnaires)</h1>
    Si vous avez l'autorisation de modifier l'indicateur, vous pouvez ajouter un critère en cliquant sur le bouton correspondant, en bas de la page. Vous pourrez alors préciser les différents éléments de ce nouveau critères (cf. liste précédente).
    <h1>Changer l'opérateur (gestionnaires)</h1>
    Si vous avez l'autorisation de modifier l'indicateur, vous pouvez changer l'opérateur reliant les critères (ET / OU) en cliquant sur le bouton correspondant, en bas de la page.
    <h1>Population entière</h1>
    Si vous ne souhaitez pas limiter le calcul de l'indicateur à une sous-population et prendre en compte tous les exemplaires du questionnaire, il suffit de ne définir aucun critère.
    ",

    "calculation"=>"<h1>Calcul d'un indicateur</h1>
    <ul>
    <li><b>Calcul</b> : le type de calcul effectué. Celui-ci peut être :
    <ul>
    <li><b>nombre d'inidividus de la sous-populations</b> : le nombre d'exemplaires du questionnaire correspondant à la sous-population</li>
    <li><b>valeur minimale</b> : la valeur minimale d'une variable numérique</li>
    <li><b>valeur maximale</b> : la valeur maximale d'une variable numérique</li>
    <li><b>valeur moyenne</b> : la valeur moyenne d'une variable numérique</li>
    <li><b>variance</b> : la variance d'une variable numérique</li>
    <li><b>écart type</b> : l'écart type d'une variable numérique</li>
    </ul>
    </li>
    <li><b>Variable</b> : la variable du questionnaire utilisée pour le calcul (n'apparaît pas dans le cas du calcul du nombre d'individus)</li>
    </ul>
    <h1>Recalculer l'indicateur</h1>
    L'indicateur se met normalement automatiquement à jour en fonction des exemplaires de questionnaire saisis.
    Cependant, si jamais la valeur ne s'affichait pas correctement ou ne reflétait pas toutes les valeurs prises par la variable du questionnaire, le bouton &laquo;&nbsp;Recalculer&nbsp;&raquo; permet de regénérer l'indicateur.
    ",

    "editCalculation"=>"<h1>Paramètres du calcul d'un indicateur</h1>
    <ul>
    <li><b>Calcul</b> : le type de calcul effectué. Celui-ci peut être :
    <ul>
    <li><b>nombre d'inidividus de la sous-populations</b> : le nombre d'exemplaires du questionnaire correspondant à la sous-population</li>
    <li><b>valeur minimale</b> : la valeur minimale d'une variable numérique</li>
    <li><b>valeur maximale</b> : la valeur maximale d'une variable numérique</li>
    <li><b>valeur moyenne</b> : la valeur moyenne d'une variable numérique</li>
    <li><b>variance</b> : la variance d'une variable numérique</li>
    <li><b>écart type</b> : l'écart type d'une variable numérique</li>
    </ul>
    </li>
    <li><b>Variable</b> : la variable du questionnaire utilisée pour le calcul (n'apparaît pas dans le cas du calcul du nombre d'individus)</li>
    </ul>
    <h1>Variables numériques</h1>
    Seules les variables <b>numériques</b> sont présentées dans la liste des variables, c'est à dire celles correspondant aux types <i>Nombre entier</i>, <i>Valeur décimale</i> d'une part, et d'autre part celles de types <i>Un choix parmi plusieurs</i>
    et <i>Plusieurs choix parmi plusieurs</i>, si tous les choix proposés ou les valeurs de variable spécifiées sont des valeurs numériques.
    ",
    
    "limits"=>"<h1>Seuils de représentation</h1>
    Un indicateur numérique automatique peut prendre différentes couleurs selon sa valeur : il suffit pour cela de définir des seuils de représentation. 
    Chaque seuil est représenté sous la forme d'un cadre et contient les informations suivantes :
    <ul>
    <li><b>Limite minimale</b> : la valeur au dessus de laquelle le seuil devient valable. Si aucune valeur minimale n'est définie, le seuil est valable pour toute valeur inférieure à la valeur maximale</li>
    <li><b>Limite maximale</b> : la valeur en dessous de laquelle le seuil devient valable. Si aucune valeur maximale n'est définie, le seuil est valable pour toute valeur supérieure à la valeur minimale</li>
    <li><b>limites inclusives</b> : définit si la valeur des limites doit être comprise ou non dans le seuil</li>
    <li><b>Couleur</b> : la couleur correspondant à ce seuil</li>
    </ul>
    <h1>Ajouter un seuil (gestionnaires)</h1>
    Si vous avez l'autorisation de modifier l'indicateur, vous pouvez ajouter un seuil en cliquant sur le bouton correspondant, en bas de la page. Vous pourrez alors préciser les différents éléments de ce nouveau seuil (cf. liste précédente).
    ",

    "createStart"=>"<h1>Création d'un indicateur - choix du type d'indicateur</h1>
    Vous pouvez créer un indicateur :
    <ul>
    <li><b>fixé par les évaluateurs</b> : la liste de ses valeurs potentielles sera définie à l'avance, et sa valeur définitive sera choisie par les évaluateurs au cours de l'évaluation</li>
    <li><b>graphique</b> : représentant l'évolution d'une variable d'un questionnaire, cet indicateur se mettra à jour automatiquement</li>
    <li><b>numérique</b> : calculé à partir d'une variable d'un questionnaire, cet indicateur se mettra à jour automatiquement</li>
    </ul>
    ",

    "create"=>"<h1>Création d'un nouvel indicateur</h1>
    <ul>
    <li><b>Nom</b> : le nom de l'indicateur</li>
    <li><b>Description</b> : un texte optionnel décrivant l'indicateur</li>
    <li class='help_separator'></li>
    <li><b>Questionnaire utilisé</b> (indicateur automatique) : choisissez le questionnaire à prendre en compte pour le calcul de l'indicateur</li>
    <li class='help_separator'></li>
    <li><b>Appliquer le même contrôle d'accès que pour l'évaluation</b> : si la case est cochée, la session est consultable par tous les utilisateurs qui peuvent consulter l'évaluation et est modifiable par tous ceux qui peuvent modifier l'évaluation</li>
    <li><b>Visible par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant consulter cet indicateur</li>
    <li><b>Gestionnaires</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier cet indicateur</li>
    <li class='help_separator'></li>
    <li><b>Evaluateurs</b> (indicateur fixé manuellement) : l'utilisateur ou l'ensemble des utilisateurs pouvant définir la valeur de l'indicateur</li>
    </ul>
    ",
    
    "categories"=>"<h1>Définition des catégories auxquelles appartient l'indicateur</h1>
    Cochez les catégories auxquelles vous souhaitez faire appartenir l'indicateur.
    ",

    
);
?>