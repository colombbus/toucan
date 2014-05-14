-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Mer 20 Mars 2013 à 19:13
-- Version du serveur: 5.5.16
-- Version de PHP: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `toucan`
--

-- --------------------------------------------------------

--
-- Structure de la table `activities`
--

CREATE TABLE IF NOT EXISTS `activities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(127) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `created` int(10) NOT NULL,
  `view_id` int(11) NOT NULL DEFAULT '0',
  `edit_id` int(11) NOT NULL DEFAULT '0',
  `logo_id` int(127) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`name`),
  KEY `id_creator` (`owner_id`),
  KEY `id_father` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `answers`
--

CREATE TABLE IF NOT EXISTS `answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `copy_id` int(11) NOT NULL,
  `choice_id` int(11) NOT NULL,
  `value_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `cached_groups`
--

CREATE TABLE IF NOT EXISTS `cached_groups` (
  `user_id` int(11) unsigned NOT NULL,
  `groups` text,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `cached_groups`
--

INSERT INTO `cached_groups` (`user_id`, `groups`) VALUES
(1, '');

-- --------------------------------------------------------

--
-- Structure de la table `calculations`
--

CREATE TABLE IF NOT EXISTS `calculations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `requires_variable` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `calculations`
--

INSERT INTO `calculations` (`id`, `name`, `requires_variable`) VALUES
(1, 'number', 0),
(2, 'minimum', 1),
(3, 'maximum', 1),
(4, 'average', 1),
(5, 'variance', 1),
(6, 'median', 1),
(7, 'standard_deviation', 1);

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  `description` varchar(10000) NOT NULL,
  `evaluation_id` int(11) NOT NULL,
  `view_id` int(11) NOT NULL,
  `edit_id` int(11) NOT NULL,
  `created` int(10) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `password` varchar(50) NOT NULL,
  `password_flag` tinyint(4) NOT NULL,
  `inherit` tinyint(1) NOT NULL,
  `access_owner_id` int(11) NOT NULL,
  `style_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `language` varchar(2) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `recapitulative` tinyint(1) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `categories_indicators`
--

CREATE TABLE IF NOT EXISTS `categories_indicators` (
  `category_id` int(11) unsigned NOT NULL,
  `indicator_id` int(11) unsigned NOT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`,`indicator_id`),
  KEY `id_category` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `choices`
--

CREATE TABLE IF NOT EXISTS `choices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(127) NOT NULL,
  `question_id` int(11) NOT NULL,
  `order` smallint(11) NOT NULL,
  `value` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `colors`
--

CREATE TABLE IF NOT EXISTS `colors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `colors`
--

INSERT INTO `colors` (`id`, `name`, `code`) VALUES
(1, 'blue', 'DDE8FF'),
(2, 'red', 'FFE8DD'),
(3, 'green', '7FFF9F'),
(4, 'light_green', 'DDFFE8'),
(5, 'orange', 'FFF0D8'),
(6, 'yellow', 'FEFFCC');

-- --------------------------------------------------------

--
-- Structure de la table `copies`
--

CREATE TABLE IF NOT EXISTS `copies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `created` int(10) NOT NULL,
  `summary_id` int(11) DEFAULT NULL,
  `ip_address` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `copies_files`
--

CREATE TABLE IF NOT EXISTS `copies_files` (
  `copy_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  UNIQUE KEY `copy_id` (`copy_id`,`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `copy_states`
--

CREATE TABLE IF NOT EXISTS `copy_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Contenu de la table `copy_states`
--

INSERT INTO `copy_states` (`id`, `name`) VALUES
(1, 'under_construction'),
(2, 'published'),
(3, 'removed'),
(4, 'processing'),
(5, 'managed'),
(6, 'marked'),
(10, 'auto_saved');

-- --------------------------------------------------------

--
-- Structure de la table `evaluations`
--

CREATE TABLE IF NOT EXISTS `evaluations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `name` varchar(127) NOT NULL,
  `description` varchar(500) NOT NULL,
  `view_id` int(11) NOT NULL,
  `edit_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `created` int(10) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `inherit` tinyint(1) NOT NULL,
  `access_owner_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `evaluation_states`
--

CREATE TABLE IF NOT EXISTS `evaluation_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `evaluation_states`
--

INSERT INTO `evaluation_states` (`id`, `name`) VALUES
(1, 'under_construction'),
(2, 'going_on'),
(3, 'under_analyse'),
(4, 'over'),
(5, 'cancelled');

-- --------------------------------------------------------

--
-- Structure de la table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `directory` varchar(100) NOT NULL,
  `title` varchar(127) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `files_styles`
--

CREATE TABLE IF NOT EXISTS `files_styles` (
  `file_id` int(11) NOT NULL,
  `style_id` int(11) NOT NULL,
  PRIMARY KEY (`file_id`,`style_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `graphics`
--

CREATE TABLE IF NOT EXISTS `graphics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `graphics`
--

INSERT INTO `graphics` (`id`, `name`) VALUES
(1, 'diagram'),
(2, 'histogram'),
(3, 'pie_chart');

-- --------------------------------------------------------

--
-- Structure de la table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `owner_id` int(11) unsigned NOT NULL,
  `created` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `id_creator` (`owner_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`, `active`, `owner_id`, `created`) VALUES
(1, 'all', 'all users', 1, 1, 0),
(2, 'registered', 'registered users', 1, 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `groups_users`
--

CREATE TABLE IF NOT EXISTS `groups_users` (
  `user_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`),
  KEY `id_group` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `indicators`
--

CREATE TABLE IF NOT EXISTS `indicators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  `description` varchar(500) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `session_id` int(11) NOT NULL,
  `contribute_id` int(11) NOT NULL,
  `view_id` int(11) NOT NULL,
  `edit_id` int(11) NOT NULL,
  `evaluation_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `value_id` int(11) DEFAULT NULL,
  `population_operator` tinyint(4) NOT NULL DEFAULT '1',
  `calculation_id` int(11) NOT NULL DEFAULT '1',
  `variable_id` int(11) NOT NULL,
  `graphic_id` int(11) NOT NULL DEFAULT '1',
  `evaluator_id` int(11) NOT NULL,
  `set_date` int(10) NOT NULL,
  `explanations` varchar(500) NOT NULL,
  `cached_value` float DEFAULT NULL,
  `cached_graphic_id` int(11) DEFAULT NULL,
  `graphic_x_axis` varchar(127) NOT NULL,
  `graphic_y_axis` varchar(127) NOT NULL,
  `graphic_title` varchar(127) NOT NULL,
  `not_answered` tinyint(1) NOT NULL,
  `inherit` tinyint(1) NOT NULL,
  `created` int(10) NOT NULL,
  `access_owner_id` int(11) NOT NULL,
  `text_values` tinyint(1) NOT NULL,
  `template_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `indicator_values`
--

CREATE TABLE IF NOT EXISTS `indicator_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  `description` varchar(500) NOT NULL,
  `indicator_id` int(11) NOT NULL,
  `color_id` int(11) DEFAULT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Structure de la table `individuals`
--

CREATE TABLE IF NOT EXISTS `individuals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `indicator_id` int(11) NOT NULL,
  `variable_id` int(11) NOT NULL,
  `selection_id` int(11) NOT NULL,
  `value` varchar(127) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `limits`
--

CREATE TABLE IF NOT EXISTS `limits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `indicator_id` int(11) NOT NULL,
  `value_min` double DEFAULT NULL,
  `value_max` float DEFAULT NULL,
  `inclusive` tinyint(1) NOT NULL,
  `color_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `long_values`
--

CREATE TABLE IF NOT EXISTS `long_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(250) NOT NULL,
  `type_id` int(6) NOT NULL,
  `template_id` int(11) NOT NULL,
  `order` smallint(11) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(500) DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `variable_id` int(11) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `question_triggers`
--

CREATE TABLE IF NOT EXISTS `question_triggers` (
  `question_id` int(11) NOT NULL,
  `choice_id` int(11) NOT NULL,
  PRIMARY KEY (`question_id`,`choice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `question_types`
--

CREATE TABLE IF NOT EXISTS `question_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `choices` tinyint(4) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `question_types`
--

INSERT INTO `question_types` (`id`, `name`, `choices`, `order`) VALUES
(1, 'one_choice', 1, 5),
(2, 'several_choices', 1, 6),
(3, 'integer_value', 0, 3),
(4, 'real_value', 0, 4),
(5, 'short_text', 0, 1),
(6, 'long_text', 0, 2);

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'login', 'Login privileges, granted after account confirmation'),
(2, 'admin', 'Administrative user, has access to everything.'),
(3, 'pending', 'Waiting for email confirmation');

-- --------------------------------------------------------

--
-- Structure de la table `roles_users`
--

CREATE TABLE IF NOT EXISTS `roles_users` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `roles_users`
--

INSERT INTO `roles_users` (`user_id`, `role_id`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Structure de la table `selections`
--

CREATE TABLE IF NOT EXISTS `selections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `numerical` tinyint(1) NOT NULL,
  `requires_value` tinyint(1) NOT NULL,
  `multiple` tinyint(1) NOT NULL DEFAULT '0',
  `simple` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Contenu de la table `selections`
--

INSERT INTO `selections` (`id`, `name`, `numerical`, `requires_value`, `multiple`, `simple`) VALUES
(1, 'equal', 0, 1, 0, 1),
(2, 'different', 0, 1, 0, 1),
(3, 'set', 0, 0, 1, 1),
(4, 'un_set', 0, 0, 1, 1),
(5, 'inferior', 1, 1, 0, 1),
(6, 'superior', 1, 1, 0, 1),
(7, 'inferior_or_equal', 1, 1, 0, 1),
(8, 'superior_or_equal', 1, 1, 0, 1),
(9, 'contains', 0, 1, 1, 0),
(10, 'not_contains', 0, 1, 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  `description` varchar(10000) NOT NULL,
  `evaluation_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `contribute_id` int(11) NOT NULL,
  `view_id` int(11) NOT NULL,
  `edit_id` int(11) NOT NULL,
  `created` int(10) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `template_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `password` varchar(50) NOT NULL,
  `password_flag` tinyint(4) NOT NULL,
  `inherit` tinyint(1) NOT NULL,
  `access_owner_id` int(11) NOT NULL,
  `style_id` int(11) NOT NULL,
  `notification` tinyint(4) NOT NULL,
  `email` varchar(127) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `language` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `session_states`
--

CREATE TABLE IF NOT EXISTS `session_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `session_states`
--

INSERT INTO `session_states` (`id`, `name`) VALUES
(1, 'under_construction'),
(2, 'going_on'),
(4, 'over'),
(5, 'cancelled');

-- --------------------------------------------------------

--
-- Structure de la table `short_values`
--

CREATE TABLE IF NOT EXISTS `short_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(127) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `styles`
--

CREATE TABLE IF NOT EXISTS `styles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `edit_id` int(11) NOT NULL,
  `view_id` int(11) NOT NULL,
  `name` varchar(127) CHARACTER SET utf8 NOT NULL,
  `description` varchar(500) CHARACTER SET utf8 NOT NULL,
  `directory` varchar(127) CHARACTER SET utf8 NOT NULL,
  `created` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Structure de la table `templates`
--

CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  `description` varchar(10000) DEFAULT NULL,
  `owner_id` int(11) NOT NULL,
  `view_id` int(11) NOT NULL DEFAULT '0',
  `edit_id` int(11) NOT NULL DEFAULT '0',
  `created` varchar(10) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `shared` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `text_values`
--

CREATE TABLE IF NOT EXISTS `text_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(127) NOT NULL,
  `username` varchar(32) NOT NULL DEFAULT '''''',
  `password` varchar(50) NOT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `active_to` datetime DEFAULT NULL,
  `ip_address` varchar(15) DEFAULT NULL,
  `last_ip_address` varchar(15) DEFAULT NULL,
  `created` int(10) DEFAULT NULL,
  `sex` tinyint(1) DEFAULT NULL,
  `name` varchar(127) NOT NULL,
  `firstname` varchar(127) NOT NULL,
  `birthday` date DEFAULT NULL,
  `extra` varchar(500) DEFAULT NULL,
  `location` varchar(127) DEFAULT NULL,
  `last_login` int(10) unsigned DEFAULT NULL,
  `logo_id` int(11) NOT NULL,
  `options` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=150 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `logins`, `active_to`, `ip_address`, `last_ip_address`, `created`, `sex`, `name`, `firstname`, `birthday`, `extra`, `location`, `last_login`, `logo_id`, `options`) VALUES
(1, 'admin@admin.com', 'admin', 'd2a9e84b62ddec55efc0e00ae9d6d012ae6814da2ad56839c4', 1, NULL, '127.0.0.1', '127.0.0.1', 1252682957, 0, 'Admin', 'Admin', '2013-03-20', '', 'Paris', 1363803086, 0, 'a:2:{s:13:"tabs_expanded";b:1;s:5:"email";b:0;}');

-- --------------------------------------------------------

--
-- Structure de la table `user_tokens`
--

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(32) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `variables`
--

CREATE TABLE IF NOT EXISTS `variables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `calculation` varchar(200) NOT NULL,
  `name` varchar(50) NOT NULL,
  `numerical` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
