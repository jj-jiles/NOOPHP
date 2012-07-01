--
-- Database: `noophp`
--
CREATE DATABASE `noophp` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `noophp`;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `password` char(64) NOT NULL,
  `full_name` varchar(75) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `app_config`
--

CREATE TABLE IF NOT EXISTS `app_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(20) NOT NULL,
  `app_title` varchar(255) NOT NULL,
  `app_domain` varchar(35) NOT NULL,
  `app_meta_title` varchar(200) NOT NULL,
  `app_meta_description` text NOT NULL,
  `app_meta_keywords` text NOT NULL,
  `app_structure` varchar(150) NOT NULL,
  `app_allowed_login_failures` int(1) NOT NULL,
  `app_account_lock_out_interval` int(2) NOT NULL,
  `url_base` varchar(75) NOT NULL,
  `url_admin` varchar(40) NOT NULL DEFAULT '/admin',
  `url_error` varchar(100) NOT NULL,
  `url_libraries` varchar(150) NOT NULL,
  `path_base` varchar(150) NOT NULL,
  `path_admin` varchar(40) NOT NULL DEFAULT '/admin',
  `path_libraries` varchar(200) NOT NULL,
  `cookie_app` varchar(25) NOT NULL DEFAULT 'main_cookie',
  `cookie_session` varchar(25) NOT NULL DEFAULT 'main_session',
  `cookie_general` varchar(25) NOT NULL DEFAULT 'main_general',
  `cookie_lifespan` int(11) NOT NULL,
  `key_google_maps` varchar(200) NOT NULL,
  `analytics_code` text NOT NULL,
  `status_is_active` enum('Y','N') NOT NULL DEFAULT 'N',
  `status_is_live` enum('Y','N') NOT NULL DEFAULT 'N',
  `status_display_errors` enum('Y','N') NOT NULL DEFAULT 'N',
  `salts_password` varchar(64) NOT NULL,
  `aes_password` varchar(64) NOT NULL,
  `contact_name` varchar(75) NOT NULL,
  `contact_email` varchar(75) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

--
-- Dumping data for table `app_config`
--

INSERT INTO `app_config` (`id`, `app_name`, `app_title`, `app_domain`, `app_meta_title`, `app_meta_description`, `app_meta_keywords`, `app_structure`, `app_allowed_login_failures`, `app_account_lock_out_interval`, `url_base`, `url_admin`, `url_error`, `url_libraries`, `path_base`, `path_admin`, `path_libraries`, `cookie_app`, `cookie_session`, `cookie_general`, `cookie_lifespan`, `key_google_maps`, `analytics_code`, `status_is_active`, `status_is_live`, `status_display_errors`, `salts_password`, `aes_password`, `contact_name`, `contact_email`) VALUES
(7, 'app_name', 'NOOPHP', '', '', '', '', 'component,content,subcontent,extendedcontent', 5, 60, 'http://noophp.com', '/admin', '/error', '', '/public_html/noophp', '/admin', '', 'main_cookie', 'main_session', 'main_general', 20, '', '', 'Y', 'N', 'Y', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_title` varchar(225) NOT NULL,
  `page_description` tinytext NOT NULL,
  `page_url` varchar(225) NOT NULL,
  `session_required` enum('N','Y') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `page_title`, `page_description`, `page_url`, `session_required`) VALUES
(1, 'Welcome To NOOPHP', '', 'home', 'N'),
(2, 'NOOPHP Account', '', 'account', 'Y');
