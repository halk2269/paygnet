-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Май 26 2015 г., 14:04
-- Версия сервера: 5.1.69-community-log
-- Версия PHP: 5.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `A876010_paygnet`
--

-- --------------------------------------------------------

--
-- Структура таблицы `dt_award`
--

CREATE TABLE IF NOT EXISTS `dt_award` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `smallimg` int(10) unsigned NOT NULL DEFAULT '0',
  `bigimg` int(10) unsigned NOT NULL DEFAULT '0',
  `offer` int(10) unsigned NOT NULL DEFAULT '0',
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`),
  KEY `ref&enabled` (`enabled`,`ref`),
  FULLTEXT KEY `fulltext` (`title`,`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `dt_award`
--

INSERT INTO `dt_award` (`id`, `enabled`, `ref`, `title`, `description`, `smallimg`, `bigimg`, `offer`, `chtime`, `addtime`) VALUES
(1, 1, 48, 'Dr. George Lenchner Award', 'Perfect score<br />Bronze Medallion<br />Actual size: 2 3/16&quot; diameter', 4, 3, 78, '2014-09-09 14:23:54', '2014-08-30 00:04:27'),
(2, 1, 42, '20% discount', 'Create your own team and get discount.', 0, 0, 79, '2014-09-09 14:25:31', '2014-09-09 18:25:31'),
(3, 1, 42, 'Tuition', 'Win competition and get 50% discount to classes in Stanford.', 0, 0, 80, '2014-09-09 14:33:59', '2014-09-09 18:33:59');

-- --------------------------------------------------------

--
-- Структура таблицы `dt_classes`
--

CREATE TABLE IF NOT EXISTS `dt_classes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `source` varchar(255) NOT NULL DEFAULT '',
  `width` int(10) unsigned NOT NULL DEFAULT '0',
  `height` int(10) unsigned NOT NULL DEFAULT '0',
  `description` text,
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`),
  KEY `ref&enabled` (`enabled`,`ref`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `dt_classes`
--

INSERT INTO `dt_classes` (`id`, `enabled`, `ref`, `source`, `width`, `height`, `description`, `chtime`, `addtime`) VALUES
(1, 1, 54, 'summerinstitutes.stanford.edu/middle-school', 600, 400, 'Stanford school', '2014-09-11 11:58:57', '0000-00-00 00:00:00'),
(2, 1, 57, 'www.princeton.edu/community/learning/summer/', 600, 400, 'Princeton Summer school.', '2014-09-11 11:58:34', '2014-09-11 15:57:25'),
(3, 1, 58, 'www.summer.harvard.edu/courses', 600, 500, 'Take Harvard summer courses for degree credit, career advancement, or personal enrichment. Most of our 300 courses do not require application for college and adult students.', '2014-09-11 12:02:59', '2014-09-11 16:01:04');

-- --------------------------------------------------------

--
-- Структура таблицы `dt_contacts`
--

CREATE TABLE IF NOT EXISTS `dt_contacts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `email` varchar(40) NOT NULL DEFAULT '',
  `theme` varchar(30) NOT NULL DEFAULT '',
  `answer` varchar(255) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`),
  KEY `ref&enabled` (`ref`,`enabled`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `dt_contacts`
--

INSERT INTO `dt_contacts` (`id`, `name`, `email`, `theme`, `answer`, `text`, `addtime`, `chtime`, `ref`, `enabled`) VALUES
(1, 'Evgeny', 'busta@hovrino.net', 'Test', 'Test', '', '2008-03-30 22:26:44', '2008-03-30 18:26:44', 9, 0),
(2, 'Evgeny', 'busta@hovrino.net', 'Test', 'Test', '', '2008-03-30 22:28:12', '2008-03-30 18:28:12', 20, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `dt_event`
--

CREATE TABLE IF NOT EXISTS `dt_event` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `preview` text NOT NULL,
  `text` text NOT NULL,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `pubdate` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`),
  KEY `ref&enabled` (`ref`,`enabled`),
  FULLTEXT KEY `fulltext` (`title`,`text`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `dt_event`
--

INSERT INTO `dt_event` (`id`, `title`, `preview`, `text`, `addtime`, `chtime`, `ref`, `enabled`, `pubdate`) VALUES
(10, 'Regional competiton', 'Preview', 'Full text content', '2007-06-18 14:44:29', '2014-08-24 10:41:59', 11, 1, '2014-06-18');

-- --------------------------------------------------------

--
-- Структура таблицы `dt_form`
--

CREATE TABLE IF NOT EXISTS `dt_form` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `rrr` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`),
  KEY `ref&enabled` (`enabled`,`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `dt_html`
--

CREATE TABLE IF NOT EXISTS `dt_html` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `text` text NOT NULL,
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ref&enabled` (`ref`,`enabled`),
  KEY `ref` (`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='ТД HTML-код' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `dt_news`
--

CREATE TABLE IF NOT EXISTS `dt_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `pubdate` date DEFAULT NULL,
  `text` text NOT NULL,
  `onmain` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `preview` text NOT NULL,
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`),
  KEY `ref&enabled` (`enabled`,`ref`),
  FULLTEXT KEY `fulltext` (`text`,`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='news with preview' AUTO_INCREMENT=19 ;

--
-- Дамп данных таблицы `dt_news`
--

INSERT INTO `dt_news` (`id`, `enabled`, `ref`, `title`, `pubdate`, `text`, `onmain`, `preview`, `chtime`, `addtime`) VALUES
(16, 1, 10, 'Last news', '2014-08-22', 'Full text', 1, 'Preview', '2014-08-22 16:11:55', '2014-08-22 20:11:55'),
(17, 1, 10, 'Hot news', '2014-08-22', 'Full text', 1, 'Hot preview', '2014-08-22 18:00:28', '2014-08-22 22:00:28');

-- --------------------------------------------------------

--
-- Структура таблицы `dt_reward`
--

CREATE TABLE IF NOT EXISTS `dt_reward` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `personal` int(10) unsigned NOT NULL DEFAULT '0',
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`),
  KEY `ref&enabled` (`enabled`,`ref`),
  FULLTEXT KEY `fulltext` (`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `dt_school`
--

CREATE TABLE IF NOT EXISTS `dt_school` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `state` varchar(100) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`),
  KEY `ref&enabled` (`enabled`,`ref`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `dt_school`
--

INSERT INTO `dt_school` (`id`, `enabled`, `ref`, `title`, `description`, `state`, `city`, `chtime`, `addtime`) VALUES
(1, 1, 52, 'Manhattan middle school', 'Some description', 'NY', 'New York', '2014-09-08 16:28:39', '2014-09-08 20:28:39'),
(2, 1, 52, 'Queens school', 'Some descritpion', 'NY', 'New York', '2014-09-09 06:34:45', '2014-09-09 10:34:45');

-- --------------------------------------------------------

--
-- Структура таблицы `dt_team`
--

CREATE TABLE IF NOT EXISTS `dt_team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `state` varchar(100) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`),
  KEY `ref&enabled` (`enabled`,`ref`),
  FULLTEXT KEY `fulltext` (`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `dt_team`
--

INSERT INTO `dt_team` (`id`, `enabled`, `ref`, `title`, `description`, `state`, `city`, `chtime`, `addtime`) VALUES
(1, 1, 32, 'San Jose Sharks', 'Regional team', 'SJ', 'Los Altos', '2014-08-24 13:30:26', '2014-08-24 17:30:26');

-- --------------------------------------------------------

--
-- Структура таблицы `dt_test`
--

CREATE TABLE IF NOT EXISTS `dt_test` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `chtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `tstring1` varchar(200) NOT NULL DEFAULT '',
  `tstring2` varchar(100) NOT NULL DEFAULT '',
  `ttext1` text NOT NULL,
  `ttext2` text NOT NULL,
  `ttext3` text NOT NULL,
  `ttable` text NOT NULL,
  `tdate` date NOT NULL DEFAULT '0000-00-00',
  `tdatetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tint` int(10) unsigned NOT NULL DEFAULT '0',
  `tfloat` double NOT NULL DEFAULT '0',
  `tpassword` varchar(32) NOT NULL DEFAULT '',
  `tbool` tinyint(1) NOT NULL DEFAULT '0',
  `tfile` int(10) unsigned NOT NULL DEFAULT '0',
  `ttable_file` text,
  `tfile_table` int(10) unsigned NOT NULL DEFAULT '0',
  `timage` int(10) unsigned NOT NULL DEFAULT '0',
  `tselect` int(10) unsigned NOT NULL DEFAULT '0',
  `tradio` int(10) unsigned NOT NULL DEFAULT '0',
  `tmulti` int(10) unsigned NOT NULL DEFAULT '0',
  `tstrlist` int(10) unsigned NOT NULL DEFAULT '0',
  `tarray` int(10) unsigned NOT NULL DEFAULT '0',
  `tlink` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`),
  KEY `ref&enabled` (`ref`,`enabled`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `dt_test`
--

INSERT INTO `dt_test` (`id`, `ref`, `chtime`, `addtime`, `enabled`, `tstring1`, `tstring2`, `ttext1`, `ttext2`, `ttext3`, `ttable`, `tdate`, `tdatetime`, `tint`, `tfloat`, `tpassword`, `tbool`, `tfile`, `ttable_file`, `tfile_table`, `timage`, `tselect`, `tradio`, `tmulti`, `tstrlist`, `tarray`, `tlink`) VALUES
(10, 13, '2014-08-22 19:52:07', '2014-08-22 23:52:07', 1, 'Common title', '', 'Test 1', 'Test 2', 'Test 3', '<table>\n<tr><th>Test</th><th>Test</th><th>Test</th></tr>\n<tr><td>new1</td><td>new2</td><td>new3</td></tr>\n</table>\n', '1970-01-01', '1970-01-01 00:00:00', 125, 12.5, '202cb962ac59075b964b07152d234b70', 0, 0, NULL, 0, 0, 77, 0, 0, 43, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `dt_testa`
--

CREATE TABLE IF NOT EXISTS `dt_testa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `str` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `field_name` varchar(30) NOT NULL DEFAULT '',
  `dt_name` varchar(30) NOT NULL DEFAULT '',
  `chtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`),
  KEY `ref&enabled` (`enabled`,`ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `dt_test_ref`
--

CREATE TABLE IF NOT EXISTS `dt_test_ref` (
  `doc_id` int(10) unsigned NOT NULL DEFAULT '0',
  `cat_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`doc_id`,`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `dt_text`
--

CREATE TABLE IF NOT EXISTS `dt_text` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `text` text,
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ref&enabled` (`ref`,`enabled`),
  KEY `ref` (`ref`),
  FULLTEXT KEY `fulltext` (`text`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='ТД Текст' AUTO_INCREMENT=481 ;

--
-- Дамп данных таблицы `dt_text`
--

INSERT INTO `dt_text` (`id`, `ref`, `enabled`, `text`, `chtime`, `addtime`) VALUES
(429, 8, 1, 'Text under contact form', '2014-08-26 12:50:50', '2006-06-02 20:50:57'),
(431, 12, 1, 'Text is in search section', '2014-08-26 12:50:50', '2006-06-05 15:51:15'),
(441, 5, 1, '<h2><span>More About</span> Math Olympiads</h2>\r\n<p>Where are official representative of MOEMS.</p>', '2014-09-09 14:37:43', '0000-00-00 00:00:00'),
(444, 18, 1, '<p>Справочная информация.</p>', '2007-06-18 10:14:22', '2006-12-14 18:28:32'),
(445, 19, 1, '', '2007-06-18 10:16:11', '2007-03-29 17:07:00'),
(467, 22, 1, '<p>Information about company and project</p>', '2014-08-26 12:49:30', '2007-04-25 16:23:19'),
(468, 35, 1, '<span>Math</span>Olympiads<br /><small>put your slogan here</small>', '2014-08-29 18:46:12', '0000-00-00 00:00:00'),
(469, 36, 1, '<p><strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam ac magna a turpis ornare aliquam id hendrerit nisl.</strong></p>\r\n			      	<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam ac magna a turpis ornare aliquam id hendrerit nisl. Pellentesque adipiscing blandit mollis. Curabitur varius est et sem rhoncus et pretium massa molestie. Vestibulum mattis justo ultricies mauris fringilla rutrum. Vestibulum id mauris non lorem euismod posuere. <a href="#">Read more</a></p>', '2014-08-26 11:05:31', '0000-00-00 00:00:00'),
(470, 37, 1, '<h2><span>About</span></h2>\r\n	        		<img src="img/white.jpg" width="56" height="56" alt="pix" />\r\n	      			<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec libero. Suspendisse bibendum. Cras id urna. Morbi tincidunt, orci ac convallis aliquam, lectus turpis varius lorem, eu posuere nunc justo tempus leo.  llorem, eu posuere nunc justo tempus leo. Donec mattis, purus nec placerat bibendum.</p>', '2014-08-26 13:51:54', '0000-00-00 00:00:00'),
(477, 51, 1, NULL, '2014-09-08 16:03:39', '2014-09-08 20:03:39'),
(478, 53, 1, 'Get a great opportunity to pass tuition in top universities.', '2014-09-09 17:29:51', '2014-09-09 18:28:53'),
(474, 46, 1, NULL, '2014-09-03 19:24:41', '2014-09-03 23:24:41'),
(475, 47, 1, NULL, '2014-09-05 08:57:06', '2014-09-05 12:57:06'),
(476, 48, 1, NULL, '2014-09-05 09:16:04', '2014-09-05 13:16:04'),
(480, 55, 1, '<h2>MOEMS</h2>', '2014-09-11 11:40:38', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `dt_user`
--

CREATE TABLE IF NOT EXISTS `dt_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` int(10) unsigned NOT NULL DEFAULT '0',
  `login` varchar(50) NOT NULL DEFAULT '',
  `pass` varchar(32) NOT NULL DEFAULT '',
  `role_id` int(10) unsigned NOT NULL DEFAULT '1',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `chtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `email` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`) USING BTREE,
  KEY `login&enabled` (`login`,`enabled`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Дамп данных таблицы `dt_user`
--

INSERT INTO `dt_user` (`id`, `ref`, `enabled`, `login`, `pass`, `role_id`, `addtime`, `chtime`, `email`) VALUES
(2, 28, 1, 'admin', 'ebf0254b722240fbcc1267eaa15b3731', 2, '0000-00-00 00:00:00', '2014-09-03 19:31:08', 'eplayorgr@gmail.com'),
(3, 28, 1, 'global', 'ebf0254b722240fbcc1267eaa15b3731', 3, '0000-00-00 00:00:00', '2014-09-03 20:24:53', 'eplayorg@gmail.com'),
(15, 28, 1, 'test', 'd8578edf8458ce06fbc5bb76a58c5ca4', 5, '2014-09-09 10:31:47', '2014-09-09 06:31:47', 'eplayorg@yandex.ru'),
(16, 28, 1, 'globy', 'd8578edf8458ce06fbc5bb76a58c5ca4', 4, '2014-09-09 10:35:47', '2014-09-09 06:35:47', 'busta@begun.ru');

-- --------------------------------------------------------

--
-- Структура таблицы `dt_user_admin`
--

CREATE TABLE IF NOT EXISTS `dt_user_admin` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `chtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `extra` varchar(255) NOT NULL,
  KEY `FK_dt_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `dt_user_admin`
--

INSERT INTO `dt_user_admin` (`id`, `ref`, `enabled`, `addtime`, `chtime`, `extra`) VALUES
(2, 28, 1, '0000-00-00 00:00:00', '2014-09-03 19:31:09', 'test');

-- --------------------------------------------------------

--
-- Структура таблицы `dt_user_director`
--

CREATE TABLE IF NOT EXISTS `dt_user_director` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `surname` varchar(255) NOT NULL DEFAULT '',
  `school` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `chtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `FK_dt_user_director_1` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Дамп данных таблицы `dt_user_director`
--

INSERT INTO `dt_user_director` (`id`, `ref`, `enabled`, `name`, `surname`, `school`, `state`, `city`, `addtime`, `chtime`) VALUES
(11, 28, 1, 'Test', 'Testov', 'High School', 'Michigan', 'Detroit', '2014-09-05 11:50:27', '2014-09-05 07:55:49');

-- --------------------------------------------------------

--
-- Структура таблицы `dt_user_official`
--

CREATE TABLE IF NOT EXISTS `dt_user_official` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `surname` varchar(255) NOT NULL DEFAULT '',
  `position` varchar(255) NOT NULL,
  `school` int(10) unsigned NOT NULL DEFAULT '0',
  `state` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `chtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `FK_dt_user_official_1` (`id`),
  KEY `FK_dt_user_official_2` (`school`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Дамп данных таблицы `dt_user_official`
--

INSERT INTO `dt_user_official` (`id`, `ref`, `enabled`, `name`, `surname`, `position`, `school`, `state`, `city`, `addtime`, `chtime`) VALUES
(15, 28, 1, 'Test', 'Testov', 'Director', 0, 'NY', 'New York', '2014-09-09 10:31:47', '2014-09-09 06:31:47');

-- --------------------------------------------------------

--
-- Структура таблицы `dt_user_team_member`
--

CREATE TABLE IF NOT EXISTS `dt_user_team_member` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `surname` varchar(255) NOT NULL DEFAULT '',
  `school` int(10) unsigned NOT NULL DEFAULT '0',
  `state` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `link_team` int(10) unsigned NOT NULL DEFAULT '0',
  `smallimg` int(10) unsigned NOT NULL DEFAULT '0',
  `bigimg` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `chtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `FK_dt_user_team_member_1` (`id`),
  KEY `FK_dt_user_team_member_2` (`school`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Дамп данных таблицы `dt_user_team_member`
--

INSERT INTO `dt_user_team_member` (`id`, `ref`, `enabled`, `name`, `surname`, `school`, `state`, `city`, `link_team`, `smallimg`, `bigimg`, `addtime`, `chtime`) VALUES
(16, 28, 1, 'Glob', 'Globov', 2, 'NY', 'New York', 0, 0, 0, '2014-09-09 10:35:47', '2014-09-09 06:35:47');

-- --------------------------------------------------------

--
-- Структура таблицы `link_test_testa`
--

CREATE TABLE IF NOT EXISTS `link_test_testa` (
  `from_id` int(10) unsigned NOT NULL DEFAULT '0',
  `to_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`from_id`,`to_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `sys_createsec_refrights`
--

CREATE TABLE IF NOT EXISTS `sys_createsec_refrights` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `role` int(10) unsigned DEFAULT NULL,
  `rights` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_sys_createsec_refrights_1` (`ref`),
  KEY `FK_sys_roles2` (`role`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `sys_createsec_refrights`
--

INSERT INTO `sys_createsec_refrights` (`id`, `ref`, `role`, `rights`) VALUES
(3, 1, 2, '10010');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_createsec_refs`
--

CREATE TABLE IF NOT EXISTS `sys_createsec_refs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(50) NOT NULL DEFAULT '',
  `filename` varchar(50) NOT NULL DEFAULT '',
  `xslt` varchar(50) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  `create_dt` varchar(50) NOT NULL DEFAULT '',
  `loadinfo` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `priority` int(10) unsigned NOT NULL DEFAULT '0',
  `inherited` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_sys_createsec_refs_1` (`ref`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `sys_createsec_refs`
--

INSERT INTO `sys_createsec_refs` (`id`, `class`, `filename`, `xslt`, `params`, `create_dt`, `loadinfo`, `ref`, `priority`, `inherited`) VALUES
(1, 'AdvDocReadClass', 'advdoc.php', 'applytpl.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 'text', 0, 1, 0, 0),
(2, 'AdvDocReadClass', 'advdoc.php', '#news.xslt', '$inDTName = "news";\r\n$inPerPage = 15;\r\n$inOrder = "pubdate DESC";\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', '', 0, 2, 0, 0),
(3, 'AdvDocReadClass', 'advdoc.php', '#article.xslt', '$inDTName = "event";\r\n$inPerPage = 8;\r\n$inOrder = "pubdate DESC";\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', '', 0, 3, 0, 0),
(4, 'AdvDocReadClass', 'advdoc.php', '#classes.xslt', '$inDTName = "classes";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 'classes', 0, 4, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `sys_createsec_secrights`
--

CREATE TABLE IF NOT EXISTS `sys_createsec_secrights` (
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rights` char(6) NOT NULL DEFAULT '',
  PRIMARY KEY (`ref`,`role_id`),
  KEY `FK_sys_createsec_secrights_2` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `sys_createsec_types`
--

CREATE TABLE IF NOT EXISTS `sys_createsec_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `ancestor` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `sys_createsec_types`
--

INSERT INTO `sys_createsec_types` (`id`, `title`, `ancestor`) VALUES
(1, 'Create text', ''),
(2, 'Create news', ''),
(3, 'Create event', ''),
(4, 'Create classes', '');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_cronmodules`
--

CREATE TABLE IF NOT EXISTS `sys_cronmodules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `class` char(50) NOT NULL DEFAULT '',
  `filename` char(50) NOT NULL DEFAULT '',
  `lastdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `period` char(9) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Cron Modules' AUTO_INCREMENT=9 ;

--
-- Дамп данных таблицы `sys_cronmodules`
--

INSERT INTO `sys_cronmodules` (`id`, `enabled`, `class`, `filename`, `lastdate`, `period`) VALUES
(1, 1, 'CleanPassInfoCronClass', 'cleanpassinfo.php', '2008-09-07 16:09:19', '7 00:00'),
(2, 1, 'CleanCacheCronClass', 'cleancache.php', '2008-09-07 16:09:19', '14 00:00'),
(8, 1, 'SearchCleanCronClass', 'searchclean.php', '2008-09-07 16:09:19', '0 01:00');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_docwrite_rights`
--

CREATE TABLE IF NOT EXISTS `sys_docwrite_rights` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0 = any',
  `ref` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0 = any',
  `write_class` char(50) NOT NULL DEFAULT '' COMMENT '% = any',
  `rights` char(4) NOT NULL DEFAULT '' COMMENT 'C | CE | E | D',
  PRIMARY KEY (`id`),
  UNIQUE KEY `role&ref&class` (`role_id`,`ref`,`write_class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `sys_dt_files`
--

CREATE TABLE IF NOT EXISTS `sys_dt_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) NOT NULL DEFAULT '',
  `ext` char(10) NOT NULL DEFAULT '',
  `size` int(11) unsigned NOT NULL DEFAULT '0',
  `mimetype` char(100) NOT NULL DEFAULT '',
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `filename` char(43) NOT NULL DEFAULT '',
  `download_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `EXTENSION` (`ext`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `sys_dt_images`
--

CREATE TABLE IF NOT EXISTS `sys_dt_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) NOT NULL DEFAULT '',
  `ext` char(10) NOT NULL DEFAULT '',
  `size` int(11) unsigned NOT NULL DEFAULT '0',
  `mimetype` char(100) NOT NULL DEFAULT '',
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `filename` char(52) NOT NULL DEFAULT '',
  `width` int(10) unsigned NOT NULL DEFAULT '0',
  `height` int(10) unsigned NOT NULL DEFAULT '0',
  `download_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `EXTENSION` (`ext`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `sys_dt_images`
--

INSERT INTO `sys_dt_images` (`id`, `name`, `ext`, `size`, `mimetype`, `chtime`, `filename`, `width`, `height`, `download_count`) VALUES
(2, '52281.jpg', 'jpg', 54868, 'image/jpeg', '2008-03-29 15:13:25', 'bdf98105aeb4e542aeaa6fff1b1444d4.jpg', 670, 448, 0),
(3, '1982windsorcastleandthebullofclarencetowermintbronzemedallionrev400.jpg', 'jpg', 79761, 'image/jpeg', '2014-08-29 20:04:27', '161f6ebc8d60c0d4c49b4cedfaf63058.jpg', 400, 400, 0),
(4, 'preview5400dccb129091982windsorcastleandthebullofclarencetowermintbronzemedallionrev400.jpg', 'jpg', 15803, 'image/jpeg', '2015-03-16 15:23:07', 'd89e98952371be1f73c71c440db3d05e.jpg', 200, 200, 83);

-- --------------------------------------------------------

--
-- Структура таблицы `sys_dt_notifies`
--

CREATE TABLE IF NOT EXISTS `sys_dt_notifies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `action` varchar(13) NOT NULL DEFAULT '',
  `xslt` varchar(50) NOT NULL DEFAULT '',
  `mailto` varchar(255) NOT NULL DEFAULT '',
  `mailfrom` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `send_to_initiator` varchar(50) NOT NULL DEFAULT '' COMMENT 'initiator email field',
  `initiator_template` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ref&action` (`ref`,`action`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Уведомления на e-mail' AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `sys_dt_notifies`
--

INSERT INTO `sys_dt_notifies` (`id`, `ref`, `action`, `xslt`, `mailto`, `mailfrom`, `subject`, `send_to_initiator`, `initiator_template`) VALUES
(2, 9, 'Create', 'formsend.xslt', 'ContactsMailTo', 'NotifyMailFrom', 'Message from a site', '', ''),
(3, 30, 'Create', '', '', 'RegisterMailFrom', 'Восстановление пароля на сайте', 'email', 'send/passrestoremail.xslt'),
(4, 28, 'Create', 'formsend.xslt', 'UserRegTo', 'NotifyMailFrom', 'Team member registration', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_dt_select`
--

CREATE TABLE IF NOT EXISTS `sys_dt_select` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `list_id` int(10) unsigned NOT NULL DEFAULT '0',
  `list_title` char(100) NOT NULL DEFAULT '',
  `item_id` int(10) unsigned NOT NULL DEFAULT '0',
  `item_name` char(50) NOT NULL DEFAULT '',
  `item_title` char(100) NOT NULL DEFAULT '',
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `list_id` (`list_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=81 ;

--
-- Дамп данных таблицы `sys_dt_select`
--

INSERT INTO `sys_dt_select` (`id`, `list_id`, `list_title`, `item_id`, `item_name`, `item_title`, `chtime`) VALUES
(22, 1, 'Список', 13, 'id2', 'Второй', '2008-09-16 19:05:17'),
(23, 2, 'Тестовый radio', 16, 'id2', 'Пункт 2', '2008-09-16 19:05:17'),
(34, 1, 'Список', 13, 'id2', 'Второй', '2008-09-16 19:21:56'),
(35, 2, 'Тестовый radio', 16, 'id2', 'Пункт 2', '2008-09-16 19:21:56'),
(41, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2008-09-16 19:31:26'),
(45, 2, 'Тестовый radio', 16, 'id2', 'Пункт 2', '2008-09-16 19:42:48'),
(47, 2, 'Тестовый radio', 16, 'id2', 'Пункт 2', '2008-09-16 19:42:57'),
(49, 2, 'Тестовый radio', 16, 'id2', 'Пункт 2', '2008-09-16 19:44:02'),
(51, 2, 'Тестовый radio', 16, 'id2', 'Пункт 2', '2008-09-16 19:49:02'),
(53, 2, 'Тестовый radio', 16, 'id2', 'Пункт 2', '2008-09-16 19:55:29'),
(54, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2014-08-22 15:58:01'),
(55, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2014-08-22 16:02:16'),
(57, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2014-08-22 16:10:34'),
(58, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2014-08-22 16:17:51'),
(59, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2014-08-22 16:20:01'),
(60, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2014-08-22 16:21:11'),
(62, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2014-08-22 16:48:06'),
(64, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2014-08-22 18:04:07'),
(65, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2014-08-22 18:20:12'),
(66, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2014-08-22 18:39:14'),
(70, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2014-08-22 19:05:47'),
(71, 2, 'Тестовый radio', 16, 'id2', 'Пункт 2', '2014-08-22 19:07:57'),
(72, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2014-08-22 19:12:09'),
(73, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2014-08-22 19:13:20'),
(77, 2, 'Тестовый radio', 15, 'id1', 'Пункт 1', '2014-08-22 19:52:07'),
(78, 4, 'Personal offers for', 26, 'offer2', 'Team member', '2014-09-09 14:23:54'),
(79, 4, 'Personal offers for', 26, 'offer2', 'Team member', '2014-09-09 14:25:31'),
(80, 4, 'Personal offers for', 26, 'offer2', 'Team member', '2014-09-09 14:33:59');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_dt_select_items`
--

CREATE TABLE IF NOT EXISTS `sys_dt_select_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `list_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` char(50) NOT NULL DEFAULT '',
  `title` char(100) NOT NULL DEFAULT '',
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`),
  KEY `FK_list_id` (`list_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Дамп данных таблицы `sys_dt_select_items`
--

INSERT INTO `sys_dt_select_items` (`id`, `list_id`, `name`, `title`, `chtime`, `sort`) VALUES
(12, 1, 'id1', 'Первый', '2008-09-16 18:59:41', 0),
(13, 1, 'id2', 'Второй', '2008-09-16 18:59:41', 0),
(14, 1, 'id3', 'Третий', '2008-09-16 18:59:41', 0),
(15, 2, 'id1', 'Пункт 1', '2008-09-16 18:59:41', 0),
(16, 2, 'id2', 'Пункт 2', '2008-09-16 18:59:41', 0),
(17, 2, 'id3', 'Пункт 3', '2008-09-16 18:59:41', 0),
(18, 3, 'check1', 'Выбор 1', '2008-09-16 18:59:41', 0),
(19, 3, 'check2', 'Выбор 2', '2008-09-16 18:59:41', 0),
(20, 3, 'check3', 'Выбор 3', '2008-09-16 18:59:41', 0),
(21, 3, 'check4', 'Выбор 4', '2008-09-16 18:59:41', 0),
(25, 4, 'offer1', 'Official representative', '2014-09-09 14:18:50', 1),
(26, 4, 'offer2', 'Team member', '2014-09-09 14:18:50', 2),
(27, 4, 'offer3', 'For all users', '2014-09-09 14:18:50', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `sys_dt_select_lists`
--

CREATE TABLE IF NOT EXISTS `sys_dt_select_lists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `canedit` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `editboth` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `canedit` (`canedit`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `sys_dt_select_lists`
--

INSERT INTO `sys_dt_select_lists` (`id`, `title`, `chtime`, `canedit`, `editboth`) VALUES
(1, 'Список', '2008-09-16 18:52:05', 1, 1),
(2, 'Тестовый radio', '2008-09-16 18:52:05', 1, 1),
(3, 'Тестовый multi checkbox', '2008-09-16 18:52:05', 1, 1),
(4, 'Personal offers for', '2014-09-05 11:01:45', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `sys_dt_strlist`
--

CREATE TABLE IF NOT EXISTS `sys_dt_strlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

--
-- Дамп данных таблицы `sys_dt_strlist`
--

INSERT INTO `sys_dt_strlist` (`id`, `text`, `chtime`) VALUES
(10, 'Пункт 1\r\nПункт 2', '2008-09-14 20:53:26'),
(11, 'Тест 1\r\nТест 2', '2008-09-16 19:05:17'),
(13, 'Test 1\r\nTest 2', '2008-09-16 19:21:56'),
(20, 'Item1\r\nItem2\r\nItem3', '2014-08-22 15:58:01'),
(21, 'Item1\r\nItem2', '2014-08-22 16:02:16'),
(23, 'test1\r\ntest2', '2014-08-22 16:10:34'),
(24, 'test1\r\ntest2', '2014-08-22 16:17:51'),
(25, 'Test1\r\nTest2', '2014-08-22 16:20:01'),
(26, 'Test1\r\nTest2', '2014-08-22 16:21:11'),
(28, 'Test1\r\nTest2', '2014-08-22 16:48:06'),
(30, 'Item1\r\nItem2', '2014-08-22 18:04:07'),
(31, 'Item1\r\nItem2', '2014-08-22 18:20:12'),
(32, 'Item1\r\nItem2', '2014-08-22 18:39:14'),
(36, 'Item1\r\nItem2', '2014-08-22 19:05:47'),
(37, 'Item1\r\nItem2', '2014-08-22 19:07:57'),
(38, 'Item1\r\nItem2', '2014-08-22 19:12:09'),
(39, 'Item1\r\nItem2', '2014-08-22 19:13:20'),
(43, 'Item1\r\nItem2', '2014-08-22 19:52:07');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_jsmodules`
--

CREATE TABLE IF NOT EXISTS `sys_jsmodules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `class` char(50) NOT NULL DEFAULT '',
  `filename` char(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `class_enabled` (`class`,`enabled`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Модули записи' AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `sys_jsmodules`
--

INSERT INTO `sys_jsmodules` (`id`, `enabled`, `class`, `filename`) VALUES
(1, 1, 'TypoGraphJSClass', 'typograph.php');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_mimetypes`
--

CREATE TABLE IF NOT EXISTS `sys_mimetypes` (
  `ext` char(7) NOT NULL DEFAULT '0',
  `type` char(30) DEFAULT '0',
  PRIMARY KEY (`ext`),
  UNIQUE KEY `ext` (`ext`),
  KEY `ext_2` (`ext`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='MIME òèïû';

--
-- Дамп данных таблицы `sys_mimetypes`
--

INSERT INTO `sys_mimetypes` (`ext`, `type`) VALUES
('ai', 'application/postscript'),
('aif', 'audio/x-aiff'),
('aifc', 'audio/x-aiff'),
('aiff', 'audio/x-aiff'),
('asc', 'text/plain'),
('au', 'audio/basic'),
('avi', 'video/x-msvideo'),
('bcpio', 'application/x-bcpio'),
('bin', 'application/octet-stream'),
('c', 'text/plain'),
('cc', 'text/plain'),
('ccad', 'application/clariscad'),
('cdf', 'application/x-netcdf'),
('class', 'application/octet-stream'),
('cpio', 'application/x-cpio'),
('cpt', 'application/mac-compactpro'),
('csh', 'application/x-csh'),
('css', 'text/css'),
('dcr', 'application/x-director'),
('dir', 'application/x-director'),
('dms', 'application/octet-stream'),
('doc', 'application/msword'),
('drw', 'application/drafting'),
('dvi', 'application/x-dvi'),
('dwg', 'application/acad'),
('dxf', 'application/dxf'),
('dxr', 'application/x-director'),
('eps', 'application/postscript'),
('etx', 'text/x-setext'),
('exe', 'application/octet-stream'),
('ez', 'application/andrew-inset'),
('f', 'text/plain'),
('f90', 'text/plain'),
('fli', 'video/x-fli'),
('gif', 'image/gif'),
('gtar', 'application/x-gtar'),
('gz', 'application/x-gzip'),
('h', 'text/plain'),
('hdf', 'application/x-hdf'),
('hh', 'text/plain'),
('hqx', 'application/mac-binhex40'),
('htm', 'text/html'),
('html', 'text/html'),
('ice', 'x-conference/x-cooltalk'),
('ief', 'image/ief'),
('iges', 'model/iges'),
('igs', 'model/iges'),
('ips', 'application/x-ipscript'),
('ipx', 'application/x-ipix'),
('jpe', 'image/jpeg'),
('jpeg', 'image/jpeg'),
('jpg', 'image/jpeg'),
('js', 'application/x-javascript'),
('kar', 'audio/midi'),
('latex', 'application/x-latex'),
('lha', 'application/octet-stream'),
('lsp', 'application/x-lisp'),
('lzh', 'application/octet-stream'),
('m', 'text/plain'),
('man', 'application/x-troff-man'),
('me', 'application/x-troff-me'),
('mesh', 'model/mesh'),
('mid', 'audio/midi'),
('midi', 'audio/midi'),
('mif', 'application/vnd.mif'),
('mime', 'www/mime'),
('mov', 'video/quicktime'),
('movie', 'video/x-sgi-movie'),
('mp2', 'audio/mpeg'),
('mp3', 'audio/mpeg'),
('mpe', 'video/mpeg'),
('mpeg', 'video/mpeg'),
('mpg', 'video/mpeg'),
('mpga', 'audio/mpeg'),
('ms', 'application/x-troff-ms'),
('msh', 'model/mesh'),
('nc', 'application/x-netcdf'),
('oda', 'application/oda'),
('pbm', 'image/x-portable-bitmap'),
('pdb', 'chemical/x-pdb'),
('pdf', 'application/pdf'),
('pgm', 'image/x-portable-graymap'),
('pgn', 'application/x-chess-pgn'),
('png', 'image/png'),
('pnm', 'image/x-portable-anymap'),
('pot', 'application/mspowerpoint'),
('ppm', 'image/x-portable-pixmap'),
('pps', 'application/mspowerpoint'),
('ppt', 'application/mspowerpoint'),
('ppz', 'application/mspowerpoint'),
('pre', 'application/x-freelance'),
('prt', 'application/pro_eng'),
('ps', 'application/postscript'),
('qt', 'video/quicktime'),
('ra', 'audio/x-realaudio'),
('ram', 'audio/x-pn-realaudio'),
('ras', 'image/cmu-raster'),
('rgb', 'image/x-rgb'),
('rm', 'audio/x-pn-realaudio'),
('roff', 'application/x-troff'),
('rpm', 'audio/x-pn-realaudio-plugin'),
('rtf', 'text/rtf'),
('rtx', 'text/richtext'),
('scm', 'application/x-lotusscreencam'),
('set', 'application/set'),
('sgm', 'text/sgml'),
('sgml', 'text/sgml'),
('sh', 'application/x-sh'),
('shar', 'application/x-shar'),
('silo', 'model/mesh'),
('sit', 'application/x-stuffit'),
('skd', 'application/x-koan'),
('skm', 'application/x-koan'),
('skp', 'application/x-koan'),
('skt', 'application/x-koan'),
('smi', 'application/smil'),
('smil', 'application/smil'),
('snd', 'audio/basic'),
('sol', 'application/solids'),
('spl', 'application/x-futuresplash'),
('src', 'application/x-wais-source'),
('step', 'application/STEP'),
('stl', 'application/SLA'),
('stp', 'application/STEP'),
('sv4cpio', 'application/x-sv4cpio'),
('sv4crc', 'application/x-sv4crc'),
('swf', 'application/x-shockwave-flash'),
('t', 'application/x-troff'),
('tar', 'application/x-tar'),
('tcl', 'application/x-tcl'),
('tex', 'application/x-tex'),
('texi', 'application/x-texinfo'),
('texinfo', 'application/x-texinfo'),
('tif', 'image/tiff'),
('tiff', 'image/tiff'),
('tr', 'application/x-troff'),
('tsi', 'audio/TSP-audio'),
('tsp', 'application/dsptype'),
('tsv', 'text/tab-separated-values'),
('txt', 'text/plain'),
('unv', 'application/i-deas'),
('ustar', 'application/x-ustar'),
('vcd', 'application/x-cdlink'),
('vda', 'application/vda'),
('viv', 'video/vnd.vivo'),
('vivo', 'video/vnd.vivo'),
('vrml', 'model/vrml'),
('wav', 'audio/x-wav'),
('wrl', 'model/vrml'),
('xbm', 'image/x-xbitmap'),
('xlc', 'application/vnd.ms-excel'),
('xll', 'application/vnd.ms-excel'),
('xlm', 'application/vnd.ms-excel'),
('xls', 'application/vnd.ms-excel'),
('xlw', 'application/vnd.ms-excel'),
('xml', 'text/xml'),
('xpm', 'image/x-xpixmap'),
('xwd', 'image/x-xwindowdump'),
('xyz', 'chemical/x-pdb'),
('zip', 'application/zip');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_multibox_select`
--

CREATE TABLE IF NOT EXISTS `sys_multibox_select` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `item_id` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `item_id` (`item_id`),
  KEY `id` (`id`),
  KEY `id&item_id` (`id`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 12288 kB';

--
-- Дамп данных таблицы `sys_multibox_select`
--

INSERT INTO `sys_multibox_select` (`id`, `item_id`) VALUES
(1, 18),
(1, 20),
(1, 21);

-- --------------------------------------------------------

--
-- Структура таблицы `sys_passinfo`
--

CREATE TABLE IF NOT EXISTS `sys_passinfo` (
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` char(32) NOT NULL DEFAULT '',
  `type` char(4) NOT NULL DEFAULT '',
  `name` char(255) NOT NULL DEFAULT '',
  `descr` char(255) NOT NULL DEFAULT '',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `time` (`time`),
  KEY `ref` (`ref`),
  KEY `sid` (`sid`),
  KEY `type` (`type`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Информация для передачи из модул';

--
-- Дамп данных таблицы `sys_passinfo`
--

INSERT INTO `sys_passinfo` (`time`, `ref`, `sid`, `type`, `name`, `descr`, `sort`) VALUES
('2014-09-03 21:06:32', 44, 'je931pbt0d2r3vj3vm8hf03i46', 'info', 'DocWasSaved', '', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `sys_passvars`
--

CREATE TABLE IF NOT EXISTS `sys_passvars` (
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` varchar(32) NOT NULL DEFAULT '',
  `varname` varchar(255) NOT NULL DEFAULT '',
  `varvalue` text NOT NULL,
  `type` varchar(3) NOT NULL DEFAULT '',
  KEY `time` (`time`),
  KEY `ref` (`ref`),
  KEY `sid` (`sid`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `sys_pass_restore`
--

CREATE TABLE IF NOT EXISTS `sys_pass_restore` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `key` char(32) NOT NULL DEFAULT '',
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `user_id&key` (`user_id`,`key`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `sys_pass_restore`
--

INSERT INTO `sys_pass_restore` (`user_id`, `key`, `date`) VALUES
(2, '9dba1c7ffff0e3095b72e2785694953d', '2007-06-18 11:40:28'),
(2, 'c66bb320df88fe9547cd11f58e3529e2', '2007-06-18 11:41:01'),
(2, '710c40056ba8c34a56501c728825be4a', '2007-06-18 11:41:57'),
(2, '398c5d32db105c7c3288a7462303124e', '2007-06-18 11:43:15'),
(2, '82aedf3d613e0160047b20978ef4f787', '2007-06-18 11:45:11'),
(2, '95758424b8bfcc5eb7a724244c500a81', '2007-06-18 11:46:14'),
(2, 'de7fc3f1be4c7417346d405b39992de2', '2007-06-18 11:46:31'),
(2, '390ee03ffe881fbc81aeabecc74b6043', '2007-06-18 11:46:42'),
(2, '3988f69346cd301c52823208032dbdf2', '2007-06-18 11:46:55'),
(2, '434cd9b2bfc8d911c9feb5db01b5b4cb', '2007-06-18 11:47:07'),
(2, '5b949894cec6a248a02b664db248d8be', '2007-06-18 11:54:26'),
(2, 'fc0c1451192b8d10af807c41055e9c43', '2007-06-18 11:54:54'),
(2, 'fd84f0748f4cef9ad41fa9c70a623030', '2007-06-18 11:55:23'),
(2, '07fcf45d1ed8e89e2c56202c712045f7', '2007-06-18 11:57:12'),
(2, 'f6a93af53229f544b79dc6ac5acc46fe', '2007-06-18 11:57:22'),
(2, '27eb9d6fd86b37c075a997549c6bfc6b', '2007-06-18 12:00:45');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_references`
--

CREATE TABLE IF NOT EXISTS `sys_references` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ref` int(10) unsigned NOT NULL DEFAULT '0',
  `class` varchar(50) NOT NULL DEFAULT '',
  `filename` varchar(50) NOT NULL DEFAULT '',
  `xslt` varchar(50) NOT NULL DEFAULT '',
  `params` text,
  `priority` int(10) unsigned NOT NULL DEFAULT '0',
  `inherited` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `loadinfo` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `comment` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ref_enabled` (`enabled`,`ref`),
  KEY `ref` (`ref`),
  KEY `sort` (`priority`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Связи между секциями и модулями' AUTO_INCREMENT=59 ;

--
-- Дамп данных таблицы `sys_references`
--

INSERT INTO `sys_references` (`id`, `name`, `enabled`, `ref`, `class`, `filename`, `xslt`, `params`, `priority`, `inherited`, `loadinfo`, `comment`) VALUES
(1, '', 1, 2, 'DocEditReadClass', 'docedit.php', 'docedit.xslt', '$inDTName = "test";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;', 0, 0, 1, 'DocEdit'),
(2, NULL, 1, 3, 'BlankReadClass', 'blank.php', '#error404.xslt', '', 0, 0, 0, 'Страница 404 ошибки'),
(3, NULL, 1, 1, 'TreeReadClass', 'tree.php', 'tree.xslt', '$inShowInOwnXSL = false;\r\n$inAllowWriteDocClass = true;', 0, 0, 1, 'Вывод дерева секций'),
(4, NULL, 1, 0, 'ShowParamsReadClass', 'showparams.php', 'auth.xslt', '', 0, 0, 1, 'Авторизация'),
(5, 'mainContent', 1, 4, 'AdvDocReadClass', 'advdoc.php', 'applytpl.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'Главная'),
(6, NULL, 1, 5, 'BlankReadClass', 'blank.php', '#sitemap.xslt', '', 0, 0, 0, 'Карта сайта'),
(8, NULL, 1, 8, 'AdvDocReadClass', 'advdoc.php', 'applytpl.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 1, 0, 0, 'Контакты'),
(9, NULL, 1, 8, 'FormReadClass', 'form.php', 'form.xslt', '$inDTName = "contacts";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inFormToSendText = "Вы можете отправить нам письмо, используя форму обратной связи:";\r\n$inFormSuccessText = "Ваш вопрос будет в ближайщее время рассмотрен";\r\n$inFormButtonText = "отправить";\r\n$feedbackRef = 20;', 0, 0, 1, 'Форма в контактах'),
(10, NULL, 1, 9, 'AdvDocReadClass', 'advdoc.php', '#news.xslt', '$inDTName = "news";\r\n$inPerPage = 10;\r\n$inOrder = "pubdate DESC";\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'Новости'),
(11, NULL, 1, 10, 'AdvDocReadClass', 'advdoc.php', '#article.xslt', '$inDTName = "event";\r\n$inPerPage = 3;\r\n$inOrder = "pubdate DESC";\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";\r\n$inWriteRef = 10001;', 0, 0, 0, 'Events'),
(12, NULL, 1, 11, 'AdvDocReadClass', 'advdoc.php', 'applytpl.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'Поиск'),
(13, NULL, 1, 12, 'AdvDocReadClass', 'advdoc.php', 'test/testdt.xslt', '$inDTName = "test";\r\n$inShowInOwnXSL = false;\r\n$inSelectRef = "owndeep";\r\n$inDTFields = "*";', 0, 0, 1, 'Test section'),
(15, NULL, 1, 11, 'SearchReadClass', 'search.php', '#search.xslt', '$inPerPage = 25;', 0, 0, 1, 'Поиск'),
(16, 'newsBlock', 1, 4, 'AdvDocReadClass', 'advdoc.php', 'blank.xslt', '$inDTName = "news";\r\n$inWhere = "onmain = 1";\r\n$inLimit = 3;\r\n$inOrder = "pubdate DESC";\r\n$inSelectRef = 10;', 0, 1, 0, 'News on main'),
(17, NULL, 1, 13, 'GetFileReadClass', 'getfile.php', 'blank.xslt', '', 0, 0, 0, 'Скачивание файлов'),
(18, NULL, 1, 14, 'AdvDocReadClass', 'advdoc.php', '#adminfaq.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'Справка для администратора'),
(19, NULL, 1, 15, 'ExcelTablesReadClass', 'excel_tables.php', 'blank.xslt', '', 0, 0, 0, 'Загрузка Excel-таблиц'),
(20, NULL, 1, 16, 'AdvDocReadClass', 'advdoc.php', '#contacts.xslt', '$inDTName = "contacts";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";\r\n$inEnabledCheck = false;\r\n$inOrder = "addtime DESC";\r\n$inPerPage = 10;', 0, 0, 1, 'Сводная информация (контакты)'),
(22, NULL, 1, 17, 'AdvDocReadClass', 'advdoc.php', 'applytpl.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'Текстовый раздел'),
(23, 'sectionRightsEdit', 1, 1, 'SectionRightsReadClass', 'sectionrights.php', '', '', 0, 0, 1, 'Редактирование прав на секции'),
(24, 'moduleRightsEdit', 1, 0, 'ModuleRightsReadClass', 'modulerights.php', '', '', 0, 1, 1, 'Редактирование прав на модуль'),
(25, NULL, 1, 18, 'UserActionsReadClass', 'useractions.php', 'useractions.xslt', '', 0, 0, 0, 'Логирование действий пользователей'),
(26, NULL, 1, 19, 'ListsReadClass', 'lists.php', 'listedit.xslt', '', 0, 0, 1, 'Редактирование списков'),
(27, NULL, 1, 20, 'ProfileReadClass', 'profile.php', '#profile.xslt', '$inUsersRef = 28;\r\n$inAllowProfile = true;\r\n$inAllowLoginChange = false;\r\n$inAllowRegister = false;', 0, 0, 1, 'Профиль пользователя'),
(28, NULL, 1, 21, 'UserReadClass', 'user.php', 'users.xslt', '$inDTName = "user";\r\n$inSelectRef = "owndeep";', 0, 0, 1, 'Редактирование пользователей'),
(29, NULL, 1, 22, 'BlankReadClass', 'blank.php', 'login.xslt', '', 0, 0, 1, 'Авторизация'),
(30, NULL, 1, 23, 'BlankReadClass', 'blank.php', 'passrestore.xslt', '', 0, 0, 1, 'Восстановление пароля'),
(31, NULL, 1, 24, 'CaptchaReadClass', 'captcha.php', '', '', 0, 0, 0, 'Генератор изображений captcha'),
(32, NULL, 1, 25, 'AdvDocReadClass', 'advdoc.php', '#team.xslt', '$inDTName = "team";\r\n$inPerPage = 8;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'Teams'),
(35, 'sloganOnTop', 1, 4, 'AdvDocReadClass', 'advdoc.php', 'blank.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 1, 0, 'Slogan on top'),
(36, 'headerText', 1, 4, 'AdvDocReadClass', 'advdoc.php', 'blank.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 1, 0, 'Text on header block'),
(37, 'about', 1, 4, 'AdvDocReadClass', 'advdoc.php', 'blank.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 1, 0, 'About short text'),
(38, 'teamBlock', 1, 4, 'AdvDocReadClass', 'advdoc.php', 'blank.xslt', '$inDTName = "team";\r\n$inLimit = 1;\r\n$inOrder = "addtime DESC";\r\n$inSelectRef = 32;', 0, 1, 0, 'Team block'),
(39, 'eventBlock', 1, 4, 'AdvDocReadClass', 'advdoc.php', 'blank.xslt', '$inDTName = "event";\r\n$inLimit = 3;\r\n$inOrder = "pubdate DESC";\r\n$inSelectRef = 11;', 0, 1, 0, 'Event block'),
(41, 'editTexts', 1, 28, 'AdvDocReadClass', 'advdoc.php', '#content.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "define";\r\n$inWhere = "sys_references.ref = 4";', 0, 0, 0, 'Edit text content'),
(42, NULL, 1, 29, 'AdvDocReadClass', 'advdoc.php', '#award.xslt', '$inDTName = "award";\r\n$inPerPage = 8;\r\n$inOrder = "addtime DESC";\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'Our Awards'),
(44, NULL, 1, 31, 'ProfileReadClass', 'profile.php', '#profile.xslt', '$inUsersRef = 28;\r\n$inUserRole = "team_member";\r\n$inAllowProfile = true;\r\n$inAllowLoginChange = false;\r\n$inAllowRegister = true;', 0, 0, 1, 'Team member registration'),
(45, NULL, 1, 32, 'ProfileReadClass', 'profile.php', '#profile.xslt', '$inUsersRef = 28;\r\n$inUserRole = "official";\r\n$inAllowProfile = true;\r\n$inAllowLoginChange = false;\r\n$inAllowRegister = true;', 0, 0, 1, 'Official registration'),
(46, NULL, 1, 33, 'AdvDocReadClass', 'advdoc.php', 'applytpl.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'Support'),
(47, NULL, 1, 34, 'AdvDocReadClass', 'advdoc.php', 'applytpl.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'Olympiads+'),
(48, NULL, 1, 35, 'AdvDocReadClass', 'advdoc.php', '#award.xslt', '$inDTName = "award";\r\n$inPerPage = 8;\r\n$inOrder = "addtime DESC";\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'MOEMS'),
(51, NULL, 1, 36, 'AdvDocReadClass', 'advdoc.php', 'applytpl.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'Schools'),
(52, NULL, 1, 36, 'AdvDocReadClass', 'advdoc.php', '#school.xslt', '$inDTName = "school";\r\n$inPerPage = 8;\r\n$inOrder = "addtime DESC";\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'Schools list'),
(53, NULL, 1, 37, 'AdvDocReadClass', 'advdoc.php', 'applytpl.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'Stanford+'),
(54, NULL, 1, 38, 'AdvDocReadClass', 'advdoc.php', '#classes.xslt', '$inDTName = "classes";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'Stanford'),
(55, NULL, 1, 35, 'AdvDocReadClass', 'advdoc.php', 'applytpl.xslt', '$inDTName = "text";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 1, 0, 0, 'MOEMS description'),
(57, NULL, 1, 40, 'AdvDocReadClass', 'advdoc.php', '#classes.xslt', '$inDTName = "classes";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'Princeton'),
(58, NULL, 1, 41, 'AdvDocReadClass', 'advdoc.php', '#classes.xslt', '$inDTName = "classes";\r\n$inShowInOwnXSL = true;\r\n$inAllowWriteDocClass = true;\r\n$inSelectRef = "owndeep";', 0, 0, 0, 'harvard');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_ref_rights`
--

CREATE TABLE IF NOT EXISTS `sys_ref_rights` (
  `ref_id` int(10) unsigned NOT NULL DEFAULT '0',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rights` char(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`ref_id`,`role_id`),
  KEY `FK_roles` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Права на связи';

--
-- Дамп данных таблицы `sys_ref_rights`
--

INSERT INTO `sys_ref_rights` (`ref_id`, `role_id`, `rights`) VALUES
(5, 2, '10010'),
(12, 2, '10010'),
(18, 2, '10000'),
(20, 1, '11000'),
(20, 2, '10000'),
(22, 2, '10010'),
(32, 2, '11110'),
(32, 5, '11100'),
(44, 4, '11000'),
(45, 5, '11000'),
(46, 2, '10010'),
(47, 2, '10010'),
(48, 2, '10010'),
(51, 2, '10010'),
(52, 5, '11100'),
(53, 2, '10010'),
(54, 2, '10010');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_roles`
--

CREATE TABLE IF NOT EXISTS `sys_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL DEFAULT '',
  `title` char(50) NOT NULL DEFAULT '',
  `defrights` char(5) NOT NULL DEFAULT '00000',
  `dtsuperaccess` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `defsectionrights` char(6) NOT NULL DEFAULT '0000',
  `listedit` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `uploadallowed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `sys_roles`
--

INSERT INTO `sys_roles` (`id`, `name`, `title`, `defrights`, `dtsuperaccess`, `defsectionrights`, `listedit`, `uploadallowed`) VALUES
(1, 'user', 'Пользователь', '10000', 0, '100000', 0, 0),
(2, 'admin', 'Администратор сайта', '11111', 0, '111111', 1, 1),
(3, 'superadmin', 'Суперадминистратор', '11111', 1, '111111', 1, 1),
(4, 'team_member', 'Team member', '10000', 0, '100000', 0, 0),
(5, 'official', 'Official representative', '10000', 0, '100000', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `sys_search_cache`
--

CREATE TABLE IF NOT EXISTS `sys_search_cache` (
  `hash` varchar(32) NOT NULL DEFAULT '',
  `rank` float NOT NULL DEFAULT '0',
  `sec_title` varchar(255) NOT NULL DEFAULT '',
  `sec_id` int(10) unsigned NOT NULL DEFAULT '0',
  `doc_title` varchar(255) NOT NULL DEFAULT '',
  `doc_type` varchar(50) NOT NULL DEFAULT '',
  `doc_addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `url` varchar(255) NOT NULL DEFAULT '',
  `addtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sec_path` varchar(255) NOT NULL DEFAULT '',
  KEY `hash` (`hash`),
  KEY `rank` (`rank`),
  KEY `doc_addtime` (`doc_addtime`),
  KEY `addtime` (`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Кеш для поиска; InnoDB free: 11264 kB';

--
-- Дамп данных таблицы `sys_search_cache`
--

INSERT INTO `sys_search_cache` (`hash`, `rank`, `sec_title`, `sec_id`, `doc_title`, `doc_type`, `doc_addtime`, `url`, `addtime`, `sec_path`) VALUES
('d41d8cd98f00b204e9800998ecf8427e', 0, '', 0, '', '', '0000-00-00 00:00:00', 'XXX', '2014-08-26 15:08:01', ''),
('d41d8cd98f00b204e9800998ecf8427e', 0, '', 0, '', '', '0000-00-00 00:00:00', 'XXX', '2014-08-26 15:08:08', '');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_sections`
--

CREATE TABLE IF NOT EXISTS `sys_sections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `enabled` int(1) unsigned NOT NULL DEFAULT '1',
  `hidden` int(1) unsigned NOT NULL DEFAULT '0',
  `onmap` int(1) unsigned NOT NULL DEFAULT '1',
  `out` varchar(4) NOT NULL DEFAULT 'html',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `path` varchar(255) DEFAULT NULL,
  `xslt` varchar(50) DEFAULT NULL,
  `auth` varchar(15) NOT NULL DEFAULT 'no',
  `redirect_url` varchar(255) NOT NULL DEFAULT '',
  `go_to_child` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`name`),
  UNIQUE KEY `alias_enabled` (`name`,`enabled`),
  KEY `sort` (`sort`),
  KEY `parent_id` (`parent_id`),
  FULLTEXT KEY `fulltext` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Дерево секций; InnoDB free: 7168 kB' AUTO_INCREMENT=42 ;

--
-- Дамп данных таблицы `sys_sections`
--

INSERT INTO `sys_sections` (`id`, `parent_id`, `name`, `title`, `enabled`, `hidden`, `onmap`, `out`, `sort`, `path`, `xslt`, `auth`, `redirect_url`, `go_to_child`) VALUES
(1, 0, 'admin', 'Административная часть', 1, 0, 1, 'html', 2, '0', '_base_admin.xslt', 'AdminPanel', '', 0),
(2, 1, 'docedit', 'Редактирование документов', 1, 0, 1, 'html', 1, '0,1', '_base_docedit.xslt', 'AdminPanel', '', 0),
(3, 0, '404', 'Ошибка 404', 1, 0, 1, 'html', 4, '0', '_base_404.xslt', 'no', '', 0),
(4, 0, 'main', 'Главная', 1, 0, 1, 'html', 1, '0', '#base.xslt', 'no', '', 0),
(5, 4, 'map', 'Site map', 1, 0, 0, 'html', 11, '0,4', '#base.xslt', 'no', '', 0),
(6, 0, 'show', 'Просмотр изображений', 1, 0, 1, 'html', 3, '0', 'viewimage.xslt', 'no', '', 0),
(8, 4, 'contacts', 'Contacts', 1, 0, 1, 'html', 9, '0,4', '#base.xslt', 'no', '', 0),
(9, 4, 'news', 'News', 1, 0, 1, 'html', 7, '0,4', '#base.xslt', 'no', '', 0),
(10, 4, 'events', 'Events', 1, 0, 1, 'html', 4, '0,4', '#base.xslt', 'no', '', 0),
(11, 4, 'search', 'Search', 1, 0, 1, 'html', 12, '0,4', '#base.xslt', 'no', '', 0),
(12, 4, 'example', 'Example section', 1, 1, 0, 'html', 22, '0,4', '#base.xslt', 'AdminPanel', '', 0),
(13, 0, 'getf', 'Скачивание файлов', 1, 0, 0, 'html', 5, '0', '#base.xslt', 'no', '', 0),
(14, 0, 'adminfaq', 'Справка для администратора', 1, 0, 1, 'html', 6, '0', '#base.xslt', 'no', '', 0),
(15, 1, 'excel-tables', 'Загрузка Excel-таблиц', 1, 0, 1, 'html', 2, '0,1', 'blank.xslt', 'AdminPanel', '', 0),
(16, 4, 'allcontacts', 'All contacts', 1, 1, 0, 'html', 16, '0,4', '#base.xslt', 'no', '', 0),
(17, 4, 'about', 'About', 1, 0, 1, 'html', 8, '0,4', '#base.xslt', 'no', '', 0),
(18, 4, 'useractions', 'User activity', 1, 1, 0, 'html', 15, '0,4', '#base.xslt', 'no', '', 0),
(19, 1, 'listedit', 'Lists edit', 1, 1, 0, 'html', 3, '0,1', '_base_admin.xslt', 'AdminPanel', '', 0),
(20, 4, 'profile', 'User profile', 1, 1, 0, 'html', 17, '0,4', '#base.xslt', 'no', '', 0),
(21, 1, 'users', 'Manage users', 1, 1, 0, 'html', 6, '0,1', '#base.xslt', 'no', '', 0),
(22, 4, 'login', 'Authorization', 1, 1, 0, 'html', 18, '0,4', '#base.xslt', 'no', '', 0),
(23, 4, 'passrestore', 'Password restore', 1, 1, 0, 'html', 19, '0,4', '#base.xslt', 'no', '', 0),
(24, 4, 'captcha', 'Captcha generator', 1, 1, 0, 'html', 20, '0,4', '#base.xslt', 'no', '', 0),
(25, 4, 'our-teams', 'Our teams', 1, 0, 1, 'html', 5, '0,4', '#base.xslt', 'no', '', 0),
(28, 4, 'edit-texts', 'Edit texts', 1, 1, 0, 'html', 21, '0,4', '#base.xslt', 'no', '', 0),
(36, 4, 'school', 'Schools', 1, 0, 1, 'html', 6, '0,4', '#base.xslt', 'no', '', 0),
(29, 4, 'your-prizes', 'Your Prizes', 1, 0, 1, 'html', 3, '0,4', '#base.xslt', 'no', '', 0),
(31, 4, 'team-member-reg', 'Team member registration', 1, 1, 0, 'html', 13, '0,4', '#base.xslt', 'no', '', 0),
(32, 4, 'official-reg', 'Official registration', 1, 1, 0, 'html', 14, '0,4', '#base.xslt', 'no', '', 0),
(33, 4, 'support', 'Support', 1, 0, 1, 'html', 10, '0,4', '#base.xslt', 'no', '', 0),
(34, 4, 'olympiads', 'Olympiads+', 1, 0, 1, 'html', 1, '0,4', '#base.xslt', 'no', '', 1),
(35, 34, 'moems', 'MOEMS', 1, 0, 1, 'html', 1, '0,4,34', '#base.xslt', 'no', '', 0),
(41, 37, 'harvard', 'Harvard', 1, 0, 1, 'html', 3, '0,4,37', '#base.xslt', 'no', '', 0),
(37, 4, 'summer-classes', 'Stanford+', 1, 0, 1, 'html', 2, '0,4', '#base.xslt', 'no', '', 0),
(38, 37, 'stanford', 'Stanford', 1, 0, 1, 'html', 1, '0,4,37', '#base.xslt', 'no', '', 0),
(40, 37, 'princeton', 'Princeton', 1, 0, 1, 'html', 2, '0,4,37', '#base.xslt', 'no', '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `sys_section_meta`
--

CREATE TABLE IF NOT EXISTS `sys_section_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ref` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID секции',
  `chtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `addtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Name - мета-тега',
  `content` text NOT NULL COMMENT 'Content - мета-тега',
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Мета теги для секций' AUTO_INCREMENT=15 ;

--
-- Дамп данных таблицы `sys_section_meta`
--

INSERT INTO `sys_section_meta` (`id`, `ref`, `chtime`, `addtime`, `name`, `content`) VALUES
(1, 10, '2014-08-26 10:21:47', '0000-00-00 00:00:00', 'menu_footer', '1'),
(2, 25, '2014-08-26 10:22:20', '0000-00-00 00:00:00', 'menu_footer', '1'),
(3, 17, '2014-08-26 10:22:39', '0000-00-00 00:00:00', 'menu_footer', '1'),
(14, 8, '2014-09-11 06:23:00', '0000-00-00 00:00:00', 'menu_footer', '1'),
(5, 10, '2014-08-26 11:18:30', '0000-00-00 00:00:00', 'menu_header', '1'),
(6, 25, '2014-08-26 11:19:05', '0000-00-00 00:00:00', 'menu_header', '1'),
(9, 29, '2014-08-29 19:56:51', '0000-00-00 00:00:00', 'menu_header', '1'),
(13, 37, '2014-09-11 06:20:01', '0000-00-00 00:00:00', 'menu_header', '1'),
(12, 34, '2014-09-11 06:19:36', '0000-00-00 00:00:00', 'menu_header', '1');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_section_rights`
--

CREATE TABLE IF NOT EXISTS `sys_section_rights` (
  `section_id` int(10) unsigned NOT NULL DEFAULT '0',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rights` char(6) NOT NULL DEFAULT '',
  PRIMARY KEY (`section_id`,`role_id`),
  KEY `FK_sys_roles1` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Права на секции; InnoDB free: 4096 kB';

--
-- Дамп данных таблицы `sys_section_rights`
--

INSERT INTO `sys_section_rights` (`section_id`, `role_id`, `rights`) VALUES
(0, 2, '100000'),
(1, 1, '000000'),
(1, 2, '100000'),
(2, 1, '000000'),
(2, 2, '100000'),
(3, 2, '100000'),
(4, 2, '111000'),
(5, 2, '101001'),
(6, 2, '100000'),
(8, 2, '101011'),
(11, 2, '101001'),
(12, 1, '000000'),
(12, 2, '101001'),
(13, 2, '100000'),
(14, 2, '101011'),
(15, 2, '101001'),
(16, 1, '000000'),
(16, 2, '101011'),
(18, 1, '000000'),
(18, 2, '100000'),
(20, 1, '000000'),
(20, 2, '101001'),
(21, 1, '000000'),
(21, 2, '101001'),
(22, 2, '101001'),
(23, 2, '101001');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_var_ints`
--

CREATE TABLE IF NOT EXISTS `sys_var_ints` (
  `name` char(20) NOT NULL DEFAULT '',
  `value` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Глобальные числовые значения';

--
-- Дамп данных таблицы `sys_var_ints`
--

INSERT INTO `sys_var_ints` (`name`, `value`) VALUES
('AuthRef', 4),
('ChatStartMessages', 30),
('ContestPhotoMaxXSize', 800),
('ContestPhotoMaxYSize', 600),
('ContestPhotoMinXSize', 200),
('ContestPhotoMinYSize', 200),
('ContestPhotoTmbSize', 120),
('DefaultRoleID', 1),
('PathLevelBeginFrom', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `sys_var_strings`
--

CREATE TABLE IF NOT EXISTS `sys_var_strings` (
  `name` varchar(20) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Глобальные строковые константы';

--
-- Дамп данных таблицы `sys_var_strings`
--

INSERT INTO `sys_var_strings` (`name`, `value`) VALUES
('DefaultSectionName', 'main'),
('DefaultSectionTpl', '#base.xslt'),
('DocEditSectionName', 'docedit'),
('Error404URL', '404'),
('PassRestoreSection', 'passrestore'),
('RootSectionName', 'Дерево секций'),
('Template_ApplyTpl', 'applytpl.xslt'),
('Template_Auth', 'auth.xslt'),
('Template_AuthBase', '_base_auth.xslt'),
('Template_Blank', 'blank.xslt');

-- --------------------------------------------------------

--
-- Структура таблицы `sys_writemodules`
--

CREATE TABLE IF NOT EXISTS `sys_writemodules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `class` char(50) NOT NULL DEFAULT '',
  `filename` char(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `class_enabled` (`class`,`enabled`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Модули записи' AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `sys_writemodules`
--

INSERT INTO `sys_writemodules` (`id`, `enabled`, `class`, `filename`) VALUES
(1, 1, 'AuthorizeWriteClass', 'authorize.php'),
(2, 1, 'DocWritingWriteClass', 'docwriting.php'),
(3, 1, 'SectWriteClass', 'sect.php'),
(4, 1, 'ProfileWriteClass', 'profile_write.php'),
(5, 1, 'MoveWriteClass', 'move.php'),
(6, 1, 'ListsWriteClass', 'lists_write.php'),
(7, 1, 'UserWriteClass', 'user.php'),
(8, 1, 'SectionRightsWriteClass', 'sectionrights.php'),
(9, 1, 'ModuleRightsWriteClass', 'modulerights.php'),
(10, 1, 'PassRestoreWriteClass', 'passrestore.php');

-- --------------------------------------------------------

--
-- Структура таблицы `user_actions`
--

CREATE TABLE IF NOT EXISTS `user_actions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `action` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `doc_type` varchar(30) NOT NULL DEFAULT '',
  `doc_id` int(10) unsigned NOT NULL DEFAULT '0',
  `section_id` varchar(45) NOT NULL DEFAULT '',
  `section_title` varchar(200) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `user_login` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `time` (`time`) USING HASH
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=593 ;

--
-- Дамп данных таблицы `user_actions`
--

INSERT INTO `user_actions` (`id`, `user_id`, `time`, `action`, `doc_type`, `doc_id`, `section_id`, `section_title`, `type`, `user_login`) VALUES
(331, 3, '2014-08-22 20:17:51', 'Create', 'test', 24, '', '', 0, 'global'),
(332, 3, '2014-08-22 20:20:01', 'Create', 'test', 25, '', '', 0, 'global'),
(333, 3, '2014-08-22 20:21:11', 'Create', 'test', 26, '', '', 0, 'global'),
(334, 3, '2014-08-22 20:48:06', 'Create', 'test', 28, '', '', 0, 'global'),
(335, 3, '2014-08-22 21:10:32', 'Edit', 'test', 9, '', '', 0, 'global'),
(336, 3, '2014-08-22 21:12:36', 'Edit', 'news', 16, '', '', 0, 'global'),
(337, 3, '2014-08-22 21:15:11', 'Edit', 'news', 15, '', '', 0, 'global'),
(338, 3, '2014-08-22 21:15:41', 'Edit', 'news', 16, '', '', 0, 'global'),
(339, 3, '2014-08-22 21:16:50', 'Edit', 'news', 16, '', '', 0, 'global'),
(340, 3, '2014-08-22 21:16:58', 'Edit', 'news', 16, '', '', 0, 'global'),
(341, 3, '2014-08-22 21:21:33', 'Edit', 'news', 16, '', '', 0, 'global'),
(342, 3, '2014-08-22 21:21:48', 'Edit', 'news', 16, '', '', 0, 'global'),
(343, 3, '2014-08-22 21:43:33', 'Edit', 'test', 9, '', '', 0, 'global'),
(344, 3, '2014-08-22 21:43:52', 'Edit', 'test', 9, '', '', 0, 'global'),
(345, 3, '2014-08-22 21:47:55', 'Edit', 'test', 9, '', '', 0, 'global'),
(346, 3, '2014-08-22 21:59:20', 'Edit', 'test', 9, '', '', 0, 'global'),
(347, 3, '2014-08-22 21:59:35', 'Edit', 'test', 9, '', '', 0, 'global'),
(348, 3, '2014-08-22 22:00:28', 'Create', 'news', 17, '', '', 0, 'global'),
(349, 3, '2014-08-22 22:01:15', 'Create', 'news', 18, '', '', 0, 'global'),
(350, 3, '2014-08-22 22:04:07', 'Create', 'test', 30, '', '', 0, 'global'),
(351, 3, '2014-08-22 22:04:44', 'Create', 'testa', 4, '', '', 0, 'global'),
(352, 3, '2014-08-22 22:20:12', 'Create', 'test', 31, '', '', 0, 'global'),
(353, 3, '2014-08-22 22:22:45', 'Edit', 'test', 9, '', '', 0, 'global'),
(354, 3, '2014-08-22 22:39:14', 'Create', 'test', 32, '', '', 0, 'global'),
(355, 3, '2014-08-22 23:05:47', 'Create', 'test', 36, '', '', 0, 'global'),
(356, 3, '2014-08-22 23:07:05', 'Delete', 'test', 9, '', '', 0, 'global'),
(357, 3, '2014-08-22 23:07:57', 'Create', 'test', 37, '', '', 0, 'global'),
(358, 3, '2014-08-22 23:12:09', 'Create', 'test', 38, '', '', 0, 'global'),
(359, 3, '2014-08-22 23:13:20', 'Create', 'test', 39, '', '', 0, 'global'),
(360, 3, '2014-08-22 23:52:07', 'Create', 'test', 10, '', '', 0, 'global'),
(361, 3, '2014-08-22 23:53:55', 'rename', '', 0, '10', 'Статьи', 1, ''),
(362, 3, '2014-08-22 23:54:09', 'rename', '', 0, '9', 'Новости', 1, ''),
(363, 3, '2014-08-22 23:54:29', 'rename', '', 0, '17', 'Текстовый раздел', 1, ''),
(364, 3, '2014-08-22 23:54:46', 'rename', '', 0, '8', 'Контакты', 1, ''),
(365, 3, '2014-08-22 23:55:02', 'rename', '', 0, '5', 'Карта сайта', 1, ''),
(366, 3, '2014-08-22 23:55:25', 'rename', '', 0, '11', 'Поиск', 1, ''),
(367, 3, '2014-08-22 23:55:50', 'rename', '', 0, '18', 'Действия пользователей', 1, ''),
(368, 3, '2014-08-22 23:56:43', 'rename', '', 0, '16', 'Сводная информация (контакты)', 1, ''),
(369, 3, '2014-08-22 23:57:03', 'rename', '', 0, '20', 'Профиль пользователя', 1, ''),
(370, 3, '2014-08-22 23:57:19', 'rename', '', 0, '22', 'Авторизация', 1, ''),
(371, 3, '2014-08-22 23:57:29', 'rename', '', 0, '22', 'Authentication', 1, ''),
(372, 3, '2014-08-22 23:57:48', 'rename', '', 0, '23', 'Восстановление пароля', 1, ''),
(373, 3, '2014-08-22 23:58:11', 'rename', '', 0, '24', 'Генератор изображений captcha', 1, ''),
(374, 3, '2014-08-22 23:58:22', 'Delete', 'news', 15, '', '', 0, 'global'),
(375, 3, '2014-08-22 23:58:27', 'Delete', 'news', 18, '', '', 0, 'global'),
(376, 3, '2014-08-24 14:37:23', 'rename', '', 0, '10', 'Articles', 1, ''),
(377, 3, '2014-08-24 14:37:36', 'chname', '', 0, '10', 'Events', 1, ''),
(378, 3, '2014-08-24 14:38:09', 'rename', '', 0, '12', 'Test section', 1, ''),
(379, 3, '2014-08-24 14:38:17', 'chname', '', 0, '12', 'Example section', 1, ''),
(380, 3, '2014-08-24 14:39:30', 'rename', '', 0, '17', 'Text section', 1, ''),
(381, 3, '2014-08-24 14:39:38', 'chname', '', 0, '17', 'About Us', 1, ''),
(382, 3, '2014-08-24 14:41:59', 'Edit', 'article', 10, '', '', 0, 'global'),
(383, 3, '2014-08-24 17:17:22', 'create', '', 0, '4', 'Главная', 1, ''),
(384, 3, '2014-08-24 17:19:53', 'moveup', '', 0, '25', 'Teams', 1, ''),
(385, 3, '2014-08-24 17:19:57', 'movetotop', '', 0, '25', 'Teams', 1, ''),
(386, 3, '2014-08-24 17:20:02', 'movedown', '', 0, '25', 'Teams', 1, ''),
(387, 3, '2014-08-24 17:30:26', 'Create', 'team', 1, '', '', 0, 'global'),
(388, 3, '2014-08-24 17:49:35', 'create', '', 0, '4', 'Главная', 1, ''),
(389, 3, '2014-08-24 17:51:02', 'create', '', 0, '4', 'Главная', 1, ''),
(390, 3, '2014-08-24 18:00:18', 'create', '', 0, '4', 'Главная', 1, ''),
(391, 3, '2014-08-24 18:00:29', 'delete', '', 0, '26', 'Nesn', 1, ''),
(392, 3, '2014-08-24 18:00:42', 'create', '', 0, '4', 'Главная', 1, ''),
(393, 3, '2014-08-24 18:00:53', 'delete', '', 0, '27', 'News-main', 1, ''),
(394, 3, '2014-08-24 18:06:16', 'Edit', 'user', 3, '', '', 0, 'global'),
(395, 4, '2014-08-24 18:32:54', 'Edit', 'user', 4, '', '', 0, 'captan'),
(396, 4, '2014-08-24 18:32:54', 'Edit', 'user_team_member', 4, '', '', 0, 'captan'),
(397, 4, '2014-08-24 18:35:00', 'Edit', 'user', 4, '', '', 0, 'captan'),
(398, 4, '2014-08-24 18:35:00', 'Edit', 'user_team_member', 4, '', '', 0, 'captan'),
(399, 3, '2014-08-26 14:21:47', 'metaedit', '', 0, '10', 'Events', 1, ''),
(400, 3, '2014-08-26 14:22:19', 'metaedit', '', 0, '25', 'Teams', 1, ''),
(401, 3, '2014-08-26 14:22:39', 'metaedit', '', 0, '17', 'About Us', 1, ''),
(402, 3, '2014-08-26 14:22:50', 'metaedit', '', 0, '8', 'Contacts', 1, ''),
(403, 3, '2014-08-26 15:18:30', 'metaedit', '', 0, '10', 'Events', 1, ''),
(404, 3, '2014-08-26 15:18:44', 'metaedit', '', 0, '10', 'Events', 1, ''),
(405, 3, '2014-08-26 15:18:51', 'metaedit', '', 0, '10', 'Events', 1, ''),
(406, 3, '2014-08-26 15:19:05', 'metaedit', '', 0, '25', 'Teams', 1, ''),
(407, 3, '2014-08-26 15:19:33', 'metaedit', '', 0, '17', 'About Us', 1, ''),
(408, 3, '2014-08-26 15:19:46', 'metaedit', '', 0, '8', 'Contacts', 1, ''),
(409, 3, '2014-08-29 22:40:17', 'create', '', 0, '4', 'Главная', 1, ''),
(410, 3, '2014-08-29 22:40:44', 'chname', '', 0, '28', 'Редактирование текстов', 1, ''),
(411, 3, '2014-08-29 22:40:49', 'hideonmap', '', 0, '28', 'Редактирование текстов', 1, ''),
(412, 3, '2014-08-29 22:40:54', 'hide', '', 0, '28', 'Редактирование текстов', 1, ''),
(413, 3, '2014-08-29 22:46:12', 'Edit', 'text', 468, '', '', 0, 'global'),
(414, 3, '2014-08-29 22:47:24', 'Edit', 'text', 441, '', '', 0, 'global'),
(415, 3, '2014-08-29 23:44:16', 'create', '', 0, '4', 'Главная', 1, ''),
(416, 3, '2014-08-29 23:44:26', 'movetotop', '', 0, '29', 'Awards', 1, ''),
(417, 3, '2014-08-29 23:45:01', 'chname', '', 0, '29', 'Awards', 1, ''),
(418, 3, '2014-08-29 23:56:51', 'metaedit', '', 0, '29', 'Awards', 1, ''),
(419, 3, '2014-08-30 00:04:27', 'Create', 'award', 1, '', '', 0, 'global'),
(420, 3, '2014-08-30 00:06:04', 'Edit', 'award', 1, '', '', 0, 'global'),
(421, 3, '2014-08-31 15:35:03', 'Edit', 'text', 441, '', '', 0, 'global'),
(422, 3, '2014-08-31 15:38:02', 'rename', '', 0, '28', 'Редактирование текстов', 1, ''),
(423, 3, '2014-08-31 15:38:26', 'create', '', 0, '4', 'Главная', 1, ''),
(424, 3, '2014-08-31 15:38:35', 'moveup', '', 0, '30', 'How to start', 1, ''),
(425, 3, '2014-08-31 15:38:39', 'movetotop', '', 0, '30', 'How to start', 1, ''),
(426, 3, '2014-08-31 15:39:20', 'metaedit', '', 0, '30', 'How to start', 1, ''),
(427, 3, '2014-08-31 15:40:01', 'chname', '', 0, '30', 'How to start', 1, ''),
(428, 3, '2014-08-31 15:42:19', 'Edit', 'text', 471, '', '', 0, 'global'),
(429, 3, '2014-09-03 12:59:48', 'rename', '', 0, '17', 'About Us', 1, ''),
(430, 3, '2014-09-03 13:00:16', 'rename', '', 0, '29', 'Awards', 1, ''),
(431, 3, '2014-09-03 13:00:27', 'chname', '', 0, '29', 'Our Awards', 1, ''),
(432, 3, '2014-09-03 21:08:06', 'create', '', 0, '4', 'Главная', 1, ''),
(433, 3, '2014-09-03 21:08:19', 'hideonmap', '', 0, '31', 'Team member registration', 1, ''),
(434, 3, '2014-09-03 21:08:25', 'hide', '', 0, '31', 'Team member registration', 1, ''),
(435, 3, '2014-09-03 21:08:30', 'moveup', '', 0, '31', 'Team member registration', 1, ''),
(436, 3, '2014-09-03 21:08:33', 'moveup', '', 0, '31', 'Team member registration', 1, ''),
(437, 3, '2014-09-03 21:08:35', 'moveup', '', 0, '31', 'Team member registration', 1, ''),
(438, 3, '2014-09-03 21:08:37', 'moveup', '', 0, '31', 'Team member registration', 1, ''),
(439, 3, '2014-09-03 21:08:39', 'moveup', '', 0, '31', 'Team member registration', 1, ''),
(440, 3, '2014-09-03 21:08:42', 'moveup', '', 0, '31', 'Team member registration', 1, ''),
(441, 3, '2014-09-03 21:08:45', 'moveup', '', 0, '31', 'Team member registration', 1, ''),
(442, 3, '2014-09-03 21:08:48', 'moveup', '', 0, '31', 'Team member registration', 1, ''),
(443, 3, '2014-09-03 21:08:55', 'movetobottom', '', 0, '12', 'Example section', 1, ''),
(444, 3, '2014-09-03 21:18:07', 'chname', '', 0, '31', 'Team member registration', 1, ''),
(445, 3, '2014-09-03 22:24:37', 'create', '', 0, '4', 'Главная', 1, ''),
(446, 3, '2014-09-03 22:24:46', 'moveup', '', 0, '32', 'Director registration', 1, ''),
(447, 3, '2014-09-03 22:24:49', 'moveup', '', 0, '32', 'Director registration', 1, ''),
(448, 3, '2014-09-03 22:24:52', 'moveup', '', 0, '32', 'Director registration', 1, ''),
(449, 3, '2014-09-03 22:24:54', 'moveup', '', 0, '32', 'Director registration', 1, ''),
(450, 3, '2014-09-03 22:24:59', 'moveup', '', 0, '32', 'Director registration', 1, ''),
(451, 3, '2014-09-03 22:25:05', 'moveup', '', 0, '32', 'Director registration', 1, ''),
(452, 3, '2014-09-03 22:25:12', 'moveup', '', 0, '32', 'Director registration', 1, ''),
(453, 3, '2014-09-03 22:25:15', 'moveup', '', 0, '32', 'Director registration', 1, ''),
(454, 3, '2014-09-03 22:25:20', 'hideonmap', '', 0, '32', 'Director registration', 1, ''),
(455, 3, '2014-09-03 22:25:24', 'hide', '', 0, '32', 'Director registration', 1, ''),
(456, 3, '2014-09-03 22:25:48', 'chname', '', 0, '32', 'Director registration', 1, ''),
(457, 3, '2014-09-03 23:23:32', 'rename', '', 0, '30', 'How to start', 1, ''),
(458, 3, '2014-09-03 23:23:45', 'chname', '', 0, '30', 'Reward', 1, ''),
(459, 3, '2014-09-03 23:24:20', 'chname', '', 0, '30', 'Reward', 1, ''),
(460, 3, '2014-09-03 23:24:28', 'rename', '', 0, '30', 'Reward', 1, ''),
(461, 3, '2014-09-03 23:24:41', 'create', '', 0, '4', 'Главная', 1, ''),
(462, 3, '2014-09-03 23:25:06', 'chname', '', 0, '33', 'Support', 1, ''),
(463, 3, '2014-09-03 23:25:11', 'moveup', '', 0, '33', 'Support', 1, ''),
(464, 3, '2014-09-03 23:25:14', 'moveup', '', 0, '33', 'Support', 1, ''),
(465, 3, '2014-09-03 23:25:17', 'moveup', '', 0, '33', 'Support', 1, ''),
(466, 3, '2014-09-03 23:25:21', 'moveup', '', 0, '33', 'Support', 1, ''),
(467, 3, '2014-09-03 23:25:24', 'moveup', '', 0, '33', 'Support', 1, ''),
(468, 3, '2014-09-03 23:25:29', 'moveup', '', 0, '33', 'Support', 1, ''),
(469, 3, '2014-09-03 23:25:32', 'moveup', '', 0, '33', 'Support', 1, ''),
(470, 3, '2014-09-03 23:25:35', 'moveup', '', 0, '33', 'Support', 1, ''),
(471, 3, '2014-09-03 23:25:42', 'moveup', '', 0, '33', 'Support', 1, ''),
(472, 3, '2014-09-03 23:25:45', 'moveup', '', 0, '33', 'Support', 1, ''),
(473, 3, '2014-09-03 23:25:48', 'moveup', '', 0, '33', 'Support', 1, ''),
(474, 3, '2014-09-03 23:25:51', 'moveup', '', 0, '33', 'Support', 1, ''),
(475, 3, '2014-09-03 23:31:09', 'Edit', 'user', 2, '', '', 0, 'global'),
(476, 3, '2014-09-03 23:31:09', 'Edit', 'user_admin', 2, '', '', 0, 'global'),
(477, 3, '2014-09-03 23:37:24', 'move', '', 0, '21', 'Главная', 1, ''),
(478, 3, '2014-09-03 23:37:30', 'movetobottom', '', 0, '21', 'Manage users', 1, ''),
(479, 3, '2014-09-03 23:38:06', 'hideonmap', '', 0, '21', 'Lists edit', 1, ''),
(480, 3, '2014-09-03 23:41:06', 'Delete', 'user_team_member', 4, '', '', 0, 'global'),
(481, 3, '2014-09-03 23:47:51', 'Delete', 'user', 4, '', '', 0, 'global'),
(482, 3, '2014-09-04 00:07:38', 'move', '', 0, '21', 'Административная часть', 1, ''),
(483, 0, '2014-09-04 00:17:00', 'Create', 'user', 5, '', '', 0, ''),
(484, 0, '2014-09-04 00:22:54', 'Create', 'user', 6, '', '', 0, ''),
(485, 3, '2014-09-04 00:24:53', 'Edit', 'user', 3, '', '', 0, 'global'),
(486, 0, '2014-09-04 00:30:12', 'Create', 'user', 7, '', '', 0, ''),
(487, 0, '2014-09-04 00:37:26', 'Create', 'user', 8, '', '', 0, ''),
(488, 0, '2014-09-04 00:38:49', 'Create', 'user', 9, '', '', 0, ''),
(489, 0, '2014-09-04 00:47:44', 'Create', 'user', 4, '', '', 0, ''),
(490, 0, '2014-09-04 01:01:09', 'Create', 'user', 5, '', '', 0, ''),
(491, 0, '2014-09-04 01:06:32', 'Create', 'user', 6, '', '', 0, ''),
(492, 0, '2014-09-04 01:09:00', 'Create', 'user', 7, '', '', 0, ''),
(493, 0, '2014-09-04 01:12:05', 'Create', 'user', 8, '', '', 0, ''),
(494, 0, '2014-09-04 23:34:21', 'Create', 'user', 9, '', '', 0, ''),
(495, 0, '2014-09-04 23:54:43', 'Create', 'user', 10, '', '', 0, ''),
(496, 0, '2014-09-05 11:50:27', 'Create', 'user', 11, '', '', 0, ''),
(497, 11, '2014-09-05 11:55:49', 'Edit', 'user', 11, '', '', 0, 'test'),
(498, 11, '2014-09-05 11:55:49', 'Edit', 'user_director', 11, '', '', 0, 'test'),
(499, 0, '2014-09-05 11:59:00', 'Create', 'user', 12, '', '', 0, ''),
(500, 3, '2014-09-05 12:57:06', 'create', '', 0, '4', 'Главная', 1, ''),
(501, 3, '2014-09-05 12:57:18', 'movetotop', '', 0, '34', 'Awards', 1, ''),
(502, 3, '2014-09-05 12:57:23', 'movedown', '', 0, '34', 'Awards', 1, ''),
(503, 3, '2014-09-05 12:57:38', 'move', '', 0, '29', 'Awards', 1, ''),
(504, 3, '2014-09-05 12:57:53', 'chname', '', 0, '34', 'Awards', 1, ''),
(505, 3, '2014-09-05 13:15:48', 'gotochild', '', 0, '34', 'Awards', 1, ''),
(506, 3, '2014-09-05 13:16:04', 'create', '', 0, '34', 'Awards', 1, ''),
(507, 3, '2014-09-05 13:16:22', 'chname', '', 0, '35', 'MOEMS Awards', 1, ''),
(508, 3, '2014-09-05 19:49:05', 'chname', '', 0, '29', 'Our Awards', 1, ''),
(509, 3, '2014-09-05 19:49:24', 'rename', '', 0, '29', 'Our Awards', 1, ''),
(510, 3, '2014-09-05 19:49:32', 'moveup', '', 0, '35', 'MOEMS Awards', 1, ''),
(511, 3, '2014-09-05 19:49:45', 'metaedit', '', 0, '35', 'MOEMS Awards', 1, ''),
(512, 3, '2014-09-05 19:49:59', 'metadelete', '', 0, '17', 'About', 1, ''),
(513, 3, '2014-09-05 19:51:04', 'delete', '', 0, '30', 'Rewards', 1, ''),
(514, 3, '2014-09-05 19:51:51', 'chname', '', 0, '25', 'Teams', 1, ''),
(515, 3, '2014-09-05 19:52:01', 'rename', '', 0, '25', 'Teams', 1, ''),
(516, 3, '2014-09-08 20:03:39', 'create', '', 0, '4', 'Главная', 1, ''),
(517, 3, '2014-09-08 20:03:55', 'chname', '', 0, '36', 'Schools', 1, ''),
(518, 3, '2014-09-08 20:04:00', 'moveup', '', 0, '36', 'Schools', 1, ''),
(519, 3, '2014-09-08 20:04:03', 'moveup', '', 0, '36', 'Schools', 1, ''),
(520, 3, '2014-09-08 20:04:06', 'moveup', '', 0, '36', 'Schools', 1, ''),
(521, 3, '2014-09-08 20:04:10', 'moveup', '', 0, '36', 'Schools', 1, ''),
(522, 3, '2014-09-08 20:04:13', 'moveup', '', 0, '36', 'Schools', 1, ''),
(523, 3, '2014-09-08 20:04:17', 'moveup', '', 0, '36', 'Schools', 1, ''),
(524, 3, '2014-09-08 20:04:20', 'moveup', '', 0, '36', 'Schools', 1, ''),
(525, 3, '2014-09-08 20:04:24', 'moveup', '', 0, '36', 'Schools', 1, ''),
(526, 3, '2014-09-08 20:04:28', 'moveup', '', 0, '36', 'Schools', 1, ''),
(527, 3, '2014-09-08 20:04:31', 'moveup', '', 0, '36', 'Schools', 1, ''),
(528, 3, '2014-09-08 20:04:34', 'moveup', '', 0, '36', 'Schools', 1, ''),
(529, 3, '2014-09-08 20:04:38', 'moveup', '', 0, '36', 'Schools', 1, ''),
(530, 3, '2014-09-08 20:04:41', 'moveup', '', 0, '36', 'Schools', 1, ''),
(531, 3, '2014-09-08 20:04:45', 'moveup', '', 0, '36', 'Schools', 1, ''),
(532, 3, '2014-09-08 20:04:48', 'moveup', '', 0, '36', 'Schools', 1, ''),
(533, 3, '2014-09-08 20:04:52', 'moveup', '', 0, '36', 'Schools', 1, ''),
(534, 3, '2014-09-08 20:05:12', 'chname', '', 0, '32', 'Director registration', 1, ''),
(535, 3, '2014-09-08 20:05:27', 'rename', '', 0, '32', 'Director registration', 1, ''),
(536, 3, '2014-09-08 20:28:39', 'Create', 'school', 1, '', '', 0, 'global'),
(537, 3, '2014-09-09 10:21:48', 'hideonmap', '', 0, '36', 'Schools', 1, ''),
(538, 3, '2014-09-09 10:21:52', 'showonmap', '', 0, '36', 'Schools', 1, ''),
(539, 0, '2014-09-09 10:28:18', 'Create', 'user', 13, '', '', 0, ''),
(540, 0, '2014-09-09 10:29:53', 'Create', 'user', 14, '', '', 0, ''),
(541, 0, '2014-09-09 10:31:47', 'Create', 'user', 15, '', '', 0, ''),
(542, 15, '2014-09-09 10:34:45', 'Create', 'school', 2, '', '', 0, 'test'),
(543, 0, '2014-09-09 10:35:47', 'Create', 'user', 16, '', '', 0, ''),
(544, 3, '2014-09-09 18:23:27', 'Edit', 'award', 1, '', '', 0, 'global'),
(545, 3, '2014-09-09 18:23:36', 'Edit', 'award', 1, '', '', 0, 'global'),
(546, 3, '2014-09-09 18:23:54', 'Edit', 'award', 1, '', '', 0, 'global'),
(547, 3, '2014-09-09 18:25:31', 'Create', 'award', 2, '', '', 0, 'global'),
(548, 3, '2014-09-09 18:28:53', 'create', '', 0, '4', 'Главная', 1, ''),
(549, 3, '2014-09-09 18:28:58', 'movetotop', '', 0, '4', 'Главная', 1, ''),
(550, 3, '2014-09-09 18:29:11', 'movetotop', '', 0, '37', 'Classes', 1, ''),
(551, 3, '2014-09-09 18:29:16', 'movedown', '', 0, '37', 'Classes', 1, ''),
(552, 3, '2014-09-09 18:29:46', 'chname', '', 0, '37', 'Classes', 1, ''),
(553, 3, '2014-09-09 18:29:59', 'create', '', 0, '37', 'Classes', 1, ''),
(554, 3, '2014-09-09 18:30:11', 'chname', '', 0, '38', 'Stanford', 1, ''),
(555, 3, '2014-09-09 18:33:59', 'Create', 'award', 3, '', '', 0, 'global'),
(556, 3, '2014-09-09 18:37:43', 'Edit', 'text', 441, '', '', 0, 'global'),
(557, 3, '2014-09-09 19:51:13', 'Edit', 'text', 479, '', '', 0, 'global'),
(558, 3, '2014-09-09 19:51:33', 'Edit', 'text', 479, '', '', 0, 'global'),
(559, 3, '2014-09-09 19:51:47', 'Edit', 'text', 479, '', '', 0, 'global'),
(560, 3, '2014-09-09 19:51:59', 'Edit', 'text', 479, '', '', 0, 'global'),
(561, 3, '2014-09-09 19:53:55', 'Edit', 'text', 479, '', '', 0, 'global'),
(562, 3, '2014-09-09 21:29:51', 'Edit', 'text', 478, '', '', 0, 'global'),
(563, 3, '2014-09-11 09:40:30', 'rename', '', 0, '34', 'Awards', 1, ''),
(564, 3, '2014-09-11 09:40:51', 'chname', '', 0, '34', 'Olympiads+', 1, ''),
(565, 3, '2014-09-11 10:17:44', 'move', '', 0, '29', 'Главная', 1, ''),
(566, 3, '2014-09-11 10:17:55', 'moveup', '', 0, '29', 'Your Prizes', 1, ''),
(567, 3, '2014-09-11 10:17:59', 'moveup', '', 0, '29', 'Your Prizes', 1, ''),
(568, 3, '2014-09-11 10:18:03', 'moveup', '', 0, '29', 'Your Prizes', 1, ''),
(569, 3, '2014-09-11 10:18:06', 'moveup', '', 0, '29', 'Your Prizes', 1, ''),
(570, 3, '2014-09-11 10:18:37', 'rename', '', 0, '37', 'Classes', 1, ''),
(571, 3, '2014-09-11 10:19:00', 'chname', '', 0, '37', 'Stanford+', 1, ''),
(572, 3, '2014-09-11 10:19:23', 'metaedit', '', 0, '35', 'MOEMS Awards', 1, ''),
(573, 3, '2014-09-11 10:19:26', 'metadelete', '', 0, '35', 'MOEMS Awards', 1, ''),
(574, 3, '2014-09-11 10:19:36', 'metaedit', '', 0, '34', 'Olympiads+', 1, ''),
(575, 3, '2014-09-11 10:20:01', 'metaedit', '', 0, '37', 'Stanford+', 1, ''),
(576, 3, '2014-09-11 10:22:26', 'metadelete', '', 0, '8', 'Contacts', 1, ''),
(577, 3, '2014-09-11 10:22:32', 'metadelete', '', 0, '8', 'Contacts', 1, ''),
(578, 3, '2014-09-11 10:22:52', 'metaedit', '', 0, '17', 'About', 1, ''),
(579, 3, '2014-09-11 10:23:00', 'metaedit', '', 0, '8', 'Contacts', 1, ''),
(580, 3, '2014-09-11 10:26:36', 'rename', '', 0, '35', 'MOEMS Awards', 1, ''),
(581, 3, '2014-09-11 10:26:46', 'chname', '', 0, '35', 'MOEMS', 1, ''),
(582, 3, '2014-09-11 15:56:31', 'create', '', 0, '37', 'Stanford+', 1, ''),
(583, 3, '2014-09-11 15:56:47', 'delete', '', 0, '39', 'Princeton', 1, ''),
(584, 3, '2014-09-11 15:57:25', 'create', '', 0, '37', 'Stanford+', 1, ''),
(585, 3, '2014-09-11 15:57:57', 'chname', '', 0, '40', 'Princeton', 1, ''),
(586, 3, '2014-09-11 15:58:34', 'Edit', 'classes', 2, '', '', 0, 'global'),
(587, 3, '2014-09-11 15:58:57', 'Edit', 'classes', 1, '', '', 0, 'global'),
(588, 3, '2014-09-11 16:01:04', 'create', '', 0, '37', 'Stanford+', 1, ''),
(589, 3, '2014-09-11 16:01:16', 'chname', '', 0, '41', 'harvard', 1, ''),
(590, 3, '2014-09-11 16:01:25', 'rename', '', 0, '41', 'harvard', 1, ''),
(591, 3, '2014-09-11 16:02:08', 'Edit', 'classes', 3, '', '', 0, 'global'),
(592, 3, '2014-09-11 16:02:59', 'Edit', 'classes', 3, '', '', 0, 'global');

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `dt_user_admin`
--
ALTER TABLE `dt_user_admin`
  ADD CONSTRAINT `FK_dt_user` FOREIGN KEY (`id`) REFERENCES `dt_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `dt_user_director`
--
ALTER TABLE `dt_user_director`
  ADD CONSTRAINT `FK_dt_user_director_1` FOREIGN KEY (`id`) REFERENCES `dt_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `dt_user_official`
--
ALTER TABLE `dt_user_official`
  ADD CONSTRAINT `FK_dt_user_official_1` FOREIGN KEY (`id`) REFERENCES `dt_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `dt_user_team_member`
--
ALTER TABLE `dt_user_team_member`
  ADD CONSTRAINT `FK_dt_user_team_member_1` FOREIGN KEY (`id`) REFERENCES `dt_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `sys_createsec_refrights`
--
ALTER TABLE `sys_createsec_refrights`
  ADD CONSTRAINT `FK_sys_createsec_refrights_1` FOREIGN KEY (`ref`) REFERENCES `sys_createsec_refs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_sys_roles2` FOREIGN KEY (`role`) REFERENCES `sys_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `sys_createsec_refs`
--
ALTER TABLE `sys_createsec_refs`
  ADD CONSTRAINT `FK_sys_createsec_refs_1` FOREIGN KEY (`ref`) REFERENCES `sys_createsec_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `sys_createsec_secrights`
--
ALTER TABLE `sys_createsec_secrights`
  ADD CONSTRAINT `FK_sys_roles` FOREIGN KEY (`role_id`) REFERENCES `sys_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_types` FOREIGN KEY (`ref`) REFERENCES `sys_createsec_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `sys_dt_select`
--
ALTER TABLE `sys_dt_select`
  ADD CONSTRAINT `item_id` FOREIGN KEY (`item_id`) REFERENCES `sys_dt_select_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `list_id` FOREIGN KEY (`list_id`) REFERENCES `sys_dt_select_lists` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `sys_dt_select_items`
--
ALTER TABLE `sys_dt_select_items`
  ADD CONSTRAINT `FK_list_id` FOREIGN KEY (`list_id`) REFERENCES `sys_dt_select_lists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `sys_multibox_select`
--
ALTER TABLE `sys_multibox_select`
  ADD CONSTRAINT `multibox_item_id` FOREIGN KEY (`item_id`) REFERENCES `sys_dt_select_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `sys_pass_restore`
--
ALTER TABLE `sys_pass_restore`
  ADD CONSTRAINT `FK_dt_user_id` FOREIGN KEY (`user_id`) REFERENCES `dt_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `sys_ref_rights`
--
ALTER TABLE `sys_ref_rights`
  ADD CONSTRAINT `FK_references` FOREIGN KEY (`ref_id`) REFERENCES `sys_references` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_roles` FOREIGN KEY (`role_id`) REFERENCES `sys_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `sys_section_rights`
--
ALTER TABLE `sys_section_rights`
  ADD CONSTRAINT `FK_sys_roles1` FOREIGN KEY (`role_id`) REFERENCES `sys_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
