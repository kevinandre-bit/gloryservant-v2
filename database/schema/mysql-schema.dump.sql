-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 07, 2025 at 08:22 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u276774975_serveflow_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bible_verses`
--

CREATE TABLE `bible_verses` (
  `id` int(11) NOT NULL,
  `verse_id` int(11) NOT NULL,
  `book_name` varchar(50) NOT NULL,
  `book_number` int(11) NOT NULL,
  `chapter` int(11) NOT NULL,
  `verse` int(11) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_notifications`
--

CREATE TABLE `custom_notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `notification_code` char(36) NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `receiver_id` int(10) UNSIGNED NOT NULL,
  `target_type` varchar(50) NOT NULL,
  `target_name` varchar(100) DEFAULT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `status` enum('unread','read') DEFAULT 'unread',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daily_tokens`
--

CREATE TABLE `daily_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `focus_projects`
--

CREATE TABLE `focus_projects` (
  `id` char(36) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `workspace_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `deadline` date DEFAULT NULL,
  `priority` char(1) NOT NULL DEFAULT 'B',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `focus_tasks`
--

CREATE TABLE `focus_tasks` (
  `id` char(36) NOT NULL,
  `project_id` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `deadline` date DEFAULT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT 0,
  `priority` char(1) NOT NULL DEFAULT 'B',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `focus_task_sessions`
--

CREATE TABLE `focus_task_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` char(36) NOT NULL,
  `focus_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `focus_workspaces`
--

CREATE TABLE `focus_workspaces` (
  `id` char(36) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `focus_workspace_user`
--

CREATE TABLE `focus_workspace_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` char(36) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ht_arrondissements`
--

CREATE TABLE `ht_arrondissements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `departement_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ht_communes`
--

CREATE TABLE `ht_communes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `arrondissement_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ht_departements`
--

CREATE TABLE `ht_departements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `radius` int(11) DEFAULT 500
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `meeting_code` varchar(30) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_attendance`
--

CREATE TABLE `meeting_attendance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reference` int(11) DEFAULT NULL,
  `email` varchar(190) DEFAULT NULL,
  `email_norm` varchar(190) DEFAULT NULL,
  `idno` varchar(50) DEFAULT NULL,
  `employee` varchar(190) DEFAULT NULL,
  `campus` varchar(100) DEFAULT NULL,
  `ministry` varchar(100) DEFAULT NULL,
  `dept` varchar(100) DEFAULT NULL,
  `token` varchar(64) DEFAULT NULL,
  `source_mode` varchar(16) DEFAULT NULL,
  `meeting` varchar(190) DEFAULT NULL,
  `meeting_id` varchar(255) NOT NULL,
  `meeting_date` date NOT NULL,
  `occurrence_no` tinyint(3) UNSIGNED DEFAULT NULL,
  `checked_in_at` datetime DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_events`
--

CREATE TABLE `meeting_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_link_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `meeting_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `frequency` enum('once','weekly','biweekly','monthly','quarterly','custom') NOT NULL DEFAULT 'once',
  `frequency_meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`frequency_meta`)),
  `expires_at` datetime DEFAULT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `meeting_type` enum('meeting','training') NOT NULL DEFAULT 'meeting',
  `campus_group` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`campus_group`)),
  `ministry_group` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ministry_group`)),
  `dept_group` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dept_group`)),
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_link`
--

CREATE TABLE `meeting_link` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` varchar(50) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `qr_path` varchar(255) DEFAULT NULL,
  `mode` enum('auto','form','auto-or-form') NOT NULL DEFAULT 'auto-or-form',
  `require_auth` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `campus_group` longtext DEFAULT json_array(),
  `ministry_group` longtext DEFAULT json_array(),
  `dept_group` longtext DEFAULT json_array()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `monthly_digital_gifts`
--

CREATE TABLE `monthly_digital_gifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `month` date NOT NULL,
  `theme_heading` varchar(255) NOT NULL,
  `welcome_subtext` text DEFAULT NULL,
  `sermon_title` varchar(255) NOT NULL,
  `sermon_date` date DEFAULT NULL,
  `sermon_description` text DEFAULT NULL,
  `sermon_audio_url` varchar(255) NOT NULL,
  `worship_title` varchar(255) NOT NULL,
  `worship_leader` varchar(255) DEFAULT NULL,
  `worship_theme_note` text DEFAULT NULL,
  `worship_audio_url` varchar(255) NOT NULL,
  `testimony_type` varchar(255) DEFAULT NULL,
  `testimony_body` text NOT NULL,
  `testimony_image_path` varchar(255) DEFAULT NULL,
  `verse_reference` varchar(255) NOT NULL,
  `verse_text` text NOT NULL,
  `verse_reflection` text DEFAULT NULL,
  `meditation_paragraphs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meditation_paragraphs`)),
  `artwork_image_path` varchar(255) DEFAULT NULL,
  `artwork_caption` varchar(255) DEFAULT NULL,
  `wallpaper_phone_url` varchar(255) DEFAULT NULL,
  `wallpaper_desktop_url` varchar(255) DEFAULT NULL,
  `closing_blessing` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mr_agenda_items`
--

CREATE TABLE `mr_agenda_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_id` bigint(20) UNSIGNED NOT NULL,
  `position` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `planned_minutes` int(10) UNSIGNED DEFAULT NULL,
  `display_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_covered` tinyint(1) NOT NULL DEFAULT 0,
  `started_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  `actual_seconds` int(10) UNSIGNED DEFAULT NULL,
  `note_excerpt` varchar(512) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mr_asana_mappings`
--

CREATE TABLE `mr_asana_mappings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `show_id` bigint(20) UNSIGNED DEFAULT NULL,
  `project_gid` varchar(64) NOT NULL,
  `project_name` varchar(191) NOT NULL,
  `section_gid` varchar(64) DEFAULT NULL,
  `section_name` varchar(191) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mr_attendees`
--

CREATE TABLE `mr_attendees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_id` bigint(20) UNSIGNED NOT NULL,
  `attendee_name` varchar(191) NOT NULL,
  `attendee_email` varchar(191) DEFAULT NULL,
  `status` enum('present','late','excused','absent') NOT NULL DEFAULT 'absent',
  `checked_in_at` datetime DEFAULT NULL,
  `source` enum('manual','link','qr','form') NOT NULL DEFAULT 'manual'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mr_drive_settings`
--

CREATE TABLE `mr_drive_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `root_folder_id` varchar(128) NOT NULL,
  `root_folder_url` varchar(512) NOT NULL,
  `meeting_type_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mr_magic_links`
--

CREATE TABLE `mr_magic_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_id` bigint(20) UNSIGNED NOT NULL,
  `token_hash` char(64) NOT NULL,
  `role` enum('editor','viewer') NOT NULL DEFAULT 'editor',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `last_used_at` datetime DEFAULT NULL,
  `created_ip` varchar(45) DEFAULT NULL,
  `last_used_ip` varchar(45) DEFAULT NULL,
  `is_revoked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mr_meetings`
--

CREATE TABLE `mr_meetings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_type_id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `show_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `timezone` varchar(64) NOT NULL DEFAULT 'America/Santo_Domingo',
  `location` varchar(191) DEFAULT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `status` enum('scheduled','in_progress','reported','published') NOT NULL DEFAULT 'scheduled',
  `notetaker_name` varchar(191) DEFAULT NULL,
  `notetaker_email` varchar(191) DEFAULT NULL,
  `drive_file_id` varchar(128) DEFAULT NULL,
  `drive_file_url` varchar(512) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `team_name` varchar(120) DEFAULT NULL,
  `show_name` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mr_meeting_attendees`
--

CREATE TABLE `mr_meeting_attendees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_id` bigint(20) UNSIGNED NOT NULL,
  `attendee_name` varchar(191) NOT NULL,
  `attendee_email` varchar(191) NOT NULL,
  `status` enum('present','absent','late','excused') DEFAULT 'present',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mr_meeting_notes`
--

CREATE TABLE `mr_meeting_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_id` bigint(20) UNSIGNED NOT NULL,
  `content` longtext DEFAULT NULL,
  `draft_json` longtext DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `last_saved_at` datetime NOT NULL DEFAULT current_timestamp(),
  `published_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mr_meeting_types`
--

CREATE TABLE `mr_meeting_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `default_duration_minutes` int(10) UNSIGNED DEFAULT 60,
  `default_drive_template_id` varchar(128) DEFAULT NULL,
  `default_drive_template_url` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mr_metrics`
--

CREATE TABLE `mr_metrics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_id` bigint(20) UNSIGNED NOT NULL,
  `metric_key` varchar(64) NOT NULL,
  `metric_label` varchar(128) NOT NULL,
  `numeric_value` decimal(18,2) DEFAULT NULL,
  `text_value` varchar(191) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mr_shows`
--

CREATE TABLE `mr_shows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mr_tasks`
--

CREATE TABLE `mr_tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_id` bigint(20) UNSIGNED NOT NULL,
  `asana_task_gid` varchar(64) NOT NULL,
  `asana_permalink_url` varchar(512) NOT NULL,
  `asana_project_gid` varchar(64) NOT NULL,
  `asana_assignee_gid` varchar(64) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `due_on` date DEFAULT NULL,
  `last_seen_status` varchar(64) DEFAULT NULL,
  `last_seen_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mr_teams`
--

CREATE TABLE `mr_teams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `new_tbl_people_schedules`
--

CREATE TABLE `new_tbl_people_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference` varchar(100) NOT NULL,
  `employee` varchar(250) DEFAULT NULL,
  `label` varchar(100) DEFAULT NULL,
  `intime` varchar(20) DEFAULT NULL,
  `outime` varchar(20) DEFAULT NULL,
  `shift_label` varchar(100) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `schedule_date` date DEFAULT NULL,
  `campus` varchar(250) DEFAULT NULL,
  `department` varchar(250) DEFAULT NULL,
  `ministry` varchar(250) DEFAULT NULL,
  `min_start_time` time DEFAULT NULL,
  `max_start_time` time DEFAULT NULL,
  `min_end_time` time DEFAULT NULL,
  `max_end_time` time DEFAULT NULL,
  `break_time` time DEFAULT '00:00:00',
  `accept_extra_hours` tinyint(1) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `archive` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `datefrom` date DEFAULT NULL,
  `dateto` date DEFAULT NULL,
  `hours` varchar(20) DEFAULT NULL,
  `restday` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(160) NOT NULL,
  `body` text DEFAULT NULL,
  `actor_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject_table` varchar(64) NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `icon` varchar(32) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_targets`
--

CREATE TABLE `notification_targets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notification_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlists`
--

CREATE TABLE `playlists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `youtube_peak` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `status` enum('draft','approved','exported') NOT NULL DEFAULT 'draft',
  `start_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlist_exports`
--

CREATE TABLE `playlist_exports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `playlist_id` bigint(20) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlist_items`
--

CREATE TABLE `playlist_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `playlist_id` bigint(20) UNSIGNED NOT NULL,
  `track_id` bigint(20) UNSIGNED NOT NULL,
  `position` int(10) UNSIGNED NOT NULL,
  `start_time` time DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radio_assignments`
--

CREATE TABLE `radio_assignments` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `site` varchar(255) NOT NULL,
  `dept` varchar(100) DEFAULT NULL,
  `window` varchar(100) DEFAULT NULL,
  `priority` varchar(20) DEFAULT NULL CHECK (`priority` in ('Low','Normal','High')),
  `status` varchar(50) DEFAULT NULL CHECK (`status` in ('Assigned','Scheduled','Pending','Completed')),
  `tech` varchar(100) DEFAULT NULL,
  `desc` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radio_checkins`
--

CREATE TABLE `radio_checkins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `station_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `responsibility` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `radio_checkins`
--
DELIMITER $$
CREATE TRIGGER `trg_radio_checkins_after_insert` AFTER INSERT ON `radio_checkins` FOR EACH ROW BEGIN
  UPDATE radio_stations
  SET on_air = IF(NEW.status = 'ok', 1, 0),
      updated_at = NOW()
  WHERE id = NEW.station_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `radio_maintenance`
--

CREATE TABLE `radio_maintenance` (
  `id` bigint(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `site` varchar(255) NOT NULL,
  `station_id` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL CHECK (`type` in ('Preventive','Corrective')),
  `priority` varchar(20) DEFAULT NULL CHECK (`priority` in ('Low','Medium','High')),
  `due` date NOT NULL,
  `status` varchar(50) DEFAULT NULL CHECK (`status` in ('Open','Scheduled','Overdue','Closed')),
  `assignee` varchar(100) DEFAULT NULL,
  `assignee_id` int(10) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radio_pocs`
--

CREATE TABLE `radio_pocs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `station_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radio_sites`
--

CREATE TABLE `radio_sites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `arrondissement_id` bigint(20) UNSIGNED NOT NULL,
  `commune_id` bigint(20) UNSIGNED NOT NULL,
  `owner` varchar(190) NOT NULL,
  `rep_name` varchar(190) DEFAULT NULL,
  `rep_phone` varchar(60) DEFAULT NULL,
  `rep_email` varchar(190) DEFAULT NULL,
  `contract_start` date DEFAULT NULL,
  `contract_end` date DEFAULT NULL,
  `contract_link` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `nickname` varchar(64) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radio_stations`
--

CREATE TABLE `radio_stations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `arrondissement_id` bigint(20) UNSIGNED NOT NULL,
  `commune_id` bigint(20) UNSIGNED NOT NULL,
  `frequency` varchar(30) DEFAULT NULL,
  `frequency_status` enum('Acquired','Not acquired') NOT NULL DEFAULT 'Not acquired',
  `on_air` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radio_station_status_log`
--

CREATE TABLE `radio_station_status_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `station_id` bigint(20) UNSIGNED NOT NULL,
  `was_on_air` tinyint(1) NOT NULL,
  `now_on_air` tinyint(1) NOT NULL,
  `changed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `radio_technician`
--

CREATE TABLE `radio_technician` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `station_id` bigint(20) UNSIGNED NOT NULL,
  `technician_id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reading_plan_chapters`
--

CREATE TABLE `reading_plan_chapters` (
  `id` int(11) NOT NULL,
  `plan_date` date NOT NULL,
  `book_name` varchar(50) NOT NULL,
  `chapter` int(11) NOT NULL,
  `verse_start` int(11) NOT NULL,
  `verse_end` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference` int(20) UNSIGNED DEFAULT NULL,
  `Employee` varchar(255) DEFAULT NULL,
  `idno` varchar(50) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','resolved') NOT NULL DEFAULT 'pending',
  `admin_response` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `country` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `clock_comment` varchar(255) DEFAULT NULL,
  `rfid` varchar(255) DEFAULT NULL,
  `time_format` int(11) DEFAULT NULL,
  `iprestriction` varchar(500) DEFAULT NULL,
  `opt` varchar(800) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `small_groups`
--

CREATE TABLE `small_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `leader_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `small_group_members`
--

CREATE TABLE `small_group_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL,
  `volunteer_id` int(10) UNSIGNED NOT NULL,
  `is_leader` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_campus_data`
--

CREATE TABLE `tbl_campus_data` (
  `id` int(11) UNSIGNED NOT NULL,
  `reference` int(11) NOT NULL,
  `campus` varchar(255) DEFAULT NULL,
  `campus_id` int(11) DEFAULT NULL,
  `ministry` varchar(255) DEFAULT '0',
  `department` varchar(50) NOT NULL,
  `jobposition` varchar(255) DEFAULT '',
  `corporate_email` varchar(255) DEFAULT NULL,
  `idno` varchar(50) DEFAULT '',
  `startdate` varchar(255) DEFAULT '',
  `dateregularized` varchar(255) DEFAULT '',
  `reason` varchar(455) DEFAULT '',
  `leaveprivilege` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_campus_info`
--

CREATE TABLE `tbl_campus_info` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `campus_email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `primary_admin` varchar(255) DEFAULT NULL,
  `campus_name` varchar(255) DEFAULT NULL,
  `campus_address` text DEFAULT NULL,
  `time_of_service` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_computers`
--

CREATE TABLE `tbl_computers` (
  `id` int(11) NOT NULL,
  `hostname` varchar(255) NOT NULL,
  `asset_tag` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `bios_version` varchar(100) DEFAULT NULL,
  `cpu_model` varchar(100) DEFAULT NULL,
  `cpu_cores` int(11) DEFAULT NULL,
  `ram_gb` int(11) DEFAULT NULL,
  `storage_type` varchar(50) DEFAULT NULL,
  `storage_capacity_gb` int(11) DEFAULT NULL,
  `gpu_model` varchar(100) DEFAULT NULL,
  `network_interfaces` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`network_interfaces`)),
  `operating_system` varchar(100) DEFAULT NULL,
  `os_version` varchar(100) DEFAULT NULL,
  `installed_patches` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`installed_patches`)),
  `installed_applications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`installed_applications`)),
  `antivirus_status` varchar(100) DEFAULT NULL,
  `firewall_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`firewall_config`)),
  `primary_ip` varchar(45) DEFAULT NULL,
  `mac_addresses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`mac_addresses`)),
  `vlan` varchar(50) DEFAULT NULL,
  `last_network_login` datetime DEFAULT NULL,
  `assigned_user_id` int(11) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `purpose` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `vendor` varchar(100) DEFAULT NULL,
  `purchase_price` decimal(12,2) DEFAULT NULL,
  `warranty_expiration` date DEFAULT NULL,
  `support_contract` text DEFAULT NULL,
  `metrics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metrics`)),
  `uptime_seconds` bigint(20) DEFAULT NULL,
  `last_reboot` datetime DEFAULT NULL,
  `backup_status` varchar(100) DEFAULT NULL,
  `last_backup` datetime DEFAULT NULL,
  `security_events` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`security_events`)),
  `configuration_changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuration_changes`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_creative_attachments`
--

CREATE TABLE `tbl_creative_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_id` bigint(20) UNSIGNED DEFAULT NULL,
  `task_id` bigint(20) UNSIGNED DEFAULT NULL,
  `uploaded_by_people_id` int(10) UNSIGNED NOT NULL,
  `disk` varchar(32) NOT NULL DEFAULT 'public',
  `path` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `mime` varchar(100) DEFAULT NULL,
  `size_bytes` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_creative_badges`
--

CREATE TABLE `tbl_creative_badges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `criteria` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`criteria`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_creative_contribution_snapshots`
--

CREATE TABLE `tbl_creative_contribution_snapshots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `people_id` int(10) UNSIGNED NOT NULL,
  `period` enum('day','week','month','quarter','year') NOT NULL DEFAULT 'week',
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `tasks_completed` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `minutes_logged` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `points_earned` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_creative_points_ledger`
--

CREATE TABLE `tbl_creative_points_ledger` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `people_id` int(10) UNSIGNED NOT NULL,
  `points` int(11) NOT NULL,
  `reason` enum('task_completed','priority_bonus','time_logged','manual_adjustment') NOT NULL DEFAULT 'task_completed',
  `ref_table` varchar(64) DEFAULT NULL,
  `ref_id` bigint(20) UNSIGNED DEFAULT NULL,
  `idempotency_key` varchar(255) DEFAULT NULL,
  `occurred_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_creative_requests`
--

CREATE TABLE `tbl_creative_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `form_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`form_data`)),
  `request_type` enum('video','graphic','audio','other') NOT NULL DEFAULT 'other',
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `status` enum('pending','in_progress','review','completed','on_hold','cancelled') NOT NULL DEFAULT 'pending',
  `admin_approved` tinyint(1) NOT NULL DEFAULT 0,
  `requester_people_id` int(10) UNSIGNED NOT NULL,
  `requester_name` varchar(255) DEFAULT NULL,
  `requester_ministry` varchar(255) DEFAULT NULL,
  `requester_email` varchar(255) DEFAULT NULL,
  `desired_due_at` date DEFAULT NULL,
  `campus_id` int(10) UNSIGNED DEFAULT NULL,
  `ministry_id` int(10) UNSIGNED DEFAULT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_creative_tasks`
--

CREATE TABLE `tbl_creative_tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','in_progress','review','completed','on_hold','cancelled') NOT NULL DEFAULT 'pending',
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `estimated_minutes` int(10) UNSIGNED DEFAULT NULL,
  `due_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_creative_task_assignments`
--

CREATE TABLE `tbl_creative_task_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `people_id` int(10) UNSIGNED NOT NULL,
  `role` enum('owner','reviewer') NOT NULL DEFAULT 'owner',
  `allocation_percent` tinyint(3) UNSIGNED DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_creative_task_comments`
--

CREATE TABLE `tbl_creative_task_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `people_id` bigint(20) UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_creative_task_events`
--

CREATE TABLE `tbl_creative_task_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `people_id` int(10) UNSIGNED DEFAULT NULL,
  `event` enum('created','started','moved_status','completed','commented','attached_asset') NOT NULL DEFAULT 'created',
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `occurred_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_creative_user_badges`
--

CREATE TABLE `tbl_creative_user_badges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `people_id` int(10) UNSIGNED NOT NULL,
  `badge_id` bigint(20) UNSIGNED NOT NULL,
  `awarded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `earned_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_equipment`
--

CREATE TABLE `tbl_equipment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL COMMENT 'Path to uploaded image',
  `category` varchar(100) NOT NULL COMMENT 'Equipment category',
  `location` varchar(100) NOT NULL COMMENT 'Storage location',
  `status` enum('Active','In Repair','Retired') NOT NULL DEFAULT 'Active',
  `acquired_at` date DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `cost` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Purchase cost per unit',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_campus`
--

CREATE TABLE `tbl_form_campus` (
  `id` int(11) UNSIGNED NOT NULL,
  `campus` varchar(250) DEFAULT NULL,
  `subdomain` varchar(64) DEFAULT NULL,
  `status` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `tbl_form_campus`
--
DELIMITER $$
CREATE TRIGGER `trg_tbl_form_campus_after_delete` AFTER DELETE ON `tbl_form_campus` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'campus_deleted',
    CONCAT('Campus supprimé — ', COALESCE(OLD.campus,'Sans nom'), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Suppression de campus',
      CONCAT('Campus : ', COALESCE(OLD.campus,'N/A')),
      CONCAT('Sous-domaine : ', COALESCE(OLD.subdomain,'N/A')),
      CONCAT('ID : ', OLD.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_form_campus', OLD.id, CONCAT('/campuses'), 'home', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_tbl_form_campus_after_insert` AFTER INSERT ON `tbl_form_campus` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'campus_created',
    CONCAT('Campus créé — ', COALESCE(NEW.campus,'Sans nom'), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Création de campus',
      CONCAT('Campus : ', COALESCE(NEW.campus,'N/A')),
      CONCAT('Sous-domaine : ', COALESCE(NEW.subdomain,'N/A')),
      CONCAT('ID : ', NEW.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_form_campus', NEW.id, CONCAT('/campuses/', NEW.id), 'home', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_tbl_form_campus_after_update` AFTER UPDATE ON `tbl_form_campus` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'campus_updated',
    CONCAT('Campus mis à jour — ', COALESCE(NEW.campus, OLD.campus,'Sans nom'), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Mise à jour de campus',
      CONCAT('Campus : ', COALESCE(NEW.campus, OLD.campus,'N/A')),
      CONCAT('Sous-domaine : ', COALESCE(NEW.subdomain, OLD.subdomain,'N/A')),
      CONCAT('ID : ', NEW.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_form_campus', NEW.id, CONCAT('/campuses/', NEW.id), 'home', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_department`
--

CREATE TABLE `tbl_form_department` (
  `id` int(10) UNSIGNED NOT NULL,
  `department` varchar(250) DEFAULT '',
  `status` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `tbl_form_department`
--
DELIMITER $$
CREATE TRIGGER `trg_tbl_form_department_after_delete` AFTER DELETE ON `tbl_form_department` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'department_deleted',
    CONCAT('Département supprimé — ', COALESCE(OLD.department,'Sans nom'), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Suppression de département',
      CONCAT('Département : ', COALESCE(OLD.department,'N/A')),
      CONCAT('ID : ', OLD.id), 
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_form_department', OLD.id, CONCAT('/departments'), 'building', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_tbl_form_department_after_insert` AFTER INSERT ON `tbl_form_department` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'department_created',
    CONCAT('Département créé — ', COALESCE(NEW.department,'Sans nom'), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Création de département',
      CONCAT('Département : ', COALESCE(NEW.department,'N/A')),
      CONCAT('ID : ', NEW.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_form_department', NEW.id, CONCAT('/departments/', NEW.id), 'building', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_tbl_form_department_after_update` AFTER UPDATE ON `tbl_form_department` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'department_updated',
    CONCAT('Département mis à jour — ', COALESCE(NEW.department, OLD.department,'Sans nom'), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Mise à jour de département',
      CONCAT('Département : ', COALESCE(NEW.department, OLD.department,'N/A')),
      CONCAT('ID : ', NEW.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_form_department', NEW.id, CONCAT('/departments/', NEW.id), 'building', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_jobtitle`
--

CREATE TABLE `tbl_form_jobtitle` (
  `id` int(10) UNSIGNED NOT NULL,
  `jobtitle` varchar(250) DEFAULT '',
  `dept_code` int(11) DEFAULT NULL,
  `Job description` varchar(5000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_leavegroup`
--

CREATE TABLE `tbl_form_leavegroup` (
  `id` int(10) UNSIGNED NOT NULL,
  `leavegroup` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `leaveprivileges` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_leavetype`
--

CREATE TABLE `tbl_form_leavetype` (
  `id` int(10) UNSIGNED NOT NULL,
  `leavetype` varchar(255) DEFAULT NULL,
  `limit` varchar(255) DEFAULT NULL,
  `percalendar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_form_ministry`
--

CREATE TABLE `tbl_form_ministry` (
  `id` int(11) UNSIGNED NOT NULL,
  `ministry` varchar(250) DEFAULT NULL,
  `department` varchar(20) DEFAULT NULL,
  `status` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `tbl_form_ministry`
--
DELIMITER $$
CREATE TRIGGER `trg_tbl_form_ministry_after_delete` AFTER DELETE ON `tbl_form_ministry` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'ministry_deleted',
    CONCAT('Ministère supprimé — ', COALESCE(OLD.ministry,'Sans nom'), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Suppression de ministère',
      CONCAT('Ministère : ', COALESCE(OLD.ministry,'N/A')),
      CONCAT('ID : ', OLD.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_form_ministry', OLD.id, CONCAT('/ministries'), 'heart', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_tbl_form_ministry_after_insert` AFTER INSERT ON `tbl_form_ministry` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'ministry_created',
    CONCAT('Ministère créé — ', COALESCE(NEW.ministry,'Sans nom'), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Création de ministère',
      CONCAT('Ministère : ', COALESCE(NEW.ministry,'N/A')),
      CONCAT('ID : ', NEW.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_form_ministry', NEW.id, CONCAT('/ministries/', NEW.id), 'heart', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_tbl_form_ministry_after_update` AFTER UPDATE ON `tbl_form_ministry` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'ministry_updated',
    CONCAT('Ministère mis à jour — ', COALESCE(NEW.ministry, OLD.ministry,'Sans nom'), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Mise à jour de ministère',
      CONCAT('Ministère : ', COALESCE(NEW.ministry, OLD.ministry,'N/A')),
      CONCAT('ID : ', NEW.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_form_ministry', NEW.id, CONCAT('/ministries/', NEW.id), 'heart', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_meeting_category`
--

CREATE TABLE `tbl_meeting_category` (
  `id` int(20) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(20) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_people`
--

CREATE TABLE `tbl_people` (
  `id` int(6) UNSIGNED NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `mi` varchar(255) DEFAULT '',
  `lastname` varchar(255) DEFAULT NULL,
  `age` int(3) DEFAULT NULL,
  `gender` varchar(255) DEFAULT '',
  `emailaddress` varchar(255) DEFAULT '',
  `civilstatus` varchar(255) DEFAULT '',
  `temperament` varchar(255) DEFAULT NULL,
  `love_language` varchar(255) DEFAULT NULL,
  `mobileno` varchar(255) DEFAULT '',
  `birthday` varchar(255) DEFAULT '',
  `nationalid` varchar(255) DEFAULT NULL,
  `birthplace` varchar(255) DEFAULT '',
  `homeaddress` varchar(255) DEFAULT '',
  `employmentstatus` varchar(11) DEFAULT '',
  `employmenttype` varchar(11) DEFAULT '',
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `tbl_people`
--
DELIMITER $$
CREATE TRIGGER `trg_tbl_people_after_delete` AFTER DELETE ON `tbl_people` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'person_deleted',
    CONCAT('Personne supprimée — ', TRIM(CONCAT(COALESCE(OLD.firstname,''),' ',COALESCE(OLD.lastname,''))), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Suppression de personne',
      CONCAT('Nom : ', TRIM(CONCAT(COALESCE(OLD.firstname,''),' ',COALESCE(OLD.lastname,'')))),
      CONCAT('Email : ', COALESCE(OLD.emailaddress,'N/A')),
      CONCAT('Téléphone : ', COALESCE(OLD.mobileno,'N/A')),
      CONCAT('ID : ', OLD.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_people', OLD.id, CONCAT('/people'), 'person_add', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_tbl_people_after_insert` AFTER INSERT ON `tbl_people` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;
  DECLARE v_recipient BIGINT DEFAULT NULL;  -- utilisateur lié
  DECLARE v_recipient_name VARCHAR(191) DEFAULT NULL;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  SELECT u.id, u.name INTO v_recipient, v_recipient_name FROM users u WHERE u.email = NEW.emailaddress LIMIT 1;
  IF v_recipient IS NULL THEN
    SELECT u.id, u.name INTO v_recipient, v_recipient_name FROM users u WHERE u.reference = NEW.id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'person_created',
    CONCAT('Nouvelle personne — ', TRIM(CONCAT(COALESCE(NEW.firstname,''),' ',COALESCE(NEW.lastname,''))), ' (ajoutée par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Ajout de personne',
      CONCAT('Nom : ', TRIM(CONCAT(COALESCE(NEW.firstname,''),' ',COALESCE(NEW.lastname,'')))),
      CONCAT('Email : ', COALESCE(NEW.emailaddress,'N/A')),
      CONCAT('Téléphone : ', COALESCE(NEW.mobileno,'N/A')),
      CONCAT('ID : ', NEW.id),
      CONCAT('Concerné (compte) : ', COALESCE(v_recipient_name,'—'), IF(v_recipient IS NOT NULL, CONCAT(' (#', v_recipient, ')'), '')),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_people', NEW.id, CONCAT('/people/', NEW.id), 'person_add', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF v_recipient IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, v_recipient);
  END IF;
  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, @actor_user_id WHERE NOT EXISTS(
      SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id AND t.user_id = @actor_user_id
    );
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_tbl_people_after_update` AFTER UPDATE ON `tbl_people` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;
  DECLARE v_recipient BIGINT DEFAULT NULL;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  SELECT u.id INTO v_recipient FROM users u WHERE u.email = NEW.emailaddress LIMIT 1;
  IF v_recipient IS NULL THEN
    SELECT u.id INTO v_recipient FROM users u WHERE u.reference = NEW.id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'person_updated',
    CONCAT('Personne mise à jour — ', TRIM(CONCAT(COALESCE(NEW.firstname,''),' ',COALESCE(NEW.lastname,''))), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Mise à jour de personne',
      CONCAT('Nom : ', TRIM(CONCAT(COALESCE(NEW.firstname,''),' ',COALESCE(NEW.lastname,'')))),
      CONCAT('Email : ', COALESCE(NEW.emailaddress, OLD.emailaddress, 'N/A')),
      CONCAT('Téléphone : ', COALESCE(NEW.mobileno, OLD.mobileno, 'N/A')),
      CONCAT('ID : ', NEW.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_people', NEW.id, CONCAT('/people/', NEW.id), 'person_add', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF v_recipient IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, v_recipient);
  END IF;
  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, @actor_user_id WHERE NOT EXISTS(
      SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id AND t.user_id = @actor_user_id
    );
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_people_attendance`
--

CREATE TABLE `tbl_people_attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `reference` int(11) DEFAULT NULL,
  `idno` varchar(11) DEFAULT '',
  `date` date DEFAULT NULL,
  `employee` varchar(255) DEFAULT '',
  `timein` varchar(255) DEFAULT NULL,
  `timeout` varchar(255) DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `totalhours` varchar(255) DEFAULT '',
  `status_timein` varchar(255) DEFAULT '',
  `status_timeout` varchar(255) DEFAULT '',
  `reason` varchar(255) DEFAULT '',
  `comment` varchar(255) DEFAULT '',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `flagged_for_review` tinyint(1) DEFAULT 0,
  `admin_note` text DEFAULT NULL,
  `needs_review` tinyint(1) NOT NULL DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT NULL,
  `computer_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `tbl_people_attendance`
--
DELIMITER $$
CREATE TRIGGER `trg_attendance_insert_notify` AFTER INSERT ON `tbl_people_attendance` FOR EACH ROW BEGIN
  DECLARE v_recipient BIGINT;

  SELECT u.id INTO v_recipient
  FROM users u
  WHERE u.idno = NEW.idno
  LIMIT 1;

  CALL notify_event(
    'clock_in',
    CONCAT('Clock-in ', DATE_FORMAT(NEW.timein, '%h:%i %p')),
    CONCAT('Employee ID ', NEW.idno, ' on ', DATE_FORMAT(NEW.`date`, '%Y-%m-%d')),
    @actor_user_id,
    'tbl_people_attendance',
    NEW.id,
    CONCAT('/attendance/', NEW.id),
    'alarm_on',
    v_recipient,
    1
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_attendance_update_notify` AFTER UPDATE ON `tbl_people_attendance` FOR EACH ROW BEGIN
  DECLARE v_recipient BIGINT;

  SELECT u.id INTO v_recipient
  FROM users u
  WHERE u.idno = NEW.idno
  LIMIT 1;

  IF (OLD.timeout IS NULL AND NEW.timeout IS NOT NULL) THEN
    CALL notify_event(
      'clock_out',
      CONCAT('Clock-out ', DATE_FORMAT(NEW.timeout, '%h:%i %p')),
      CONCAT('Total: ', IFNULL(NEW.totalhours,'-'), ' h'),
      @actor_user_id,
      'tbl_people_attendance',
      NEW.id,
      CONCAT('/attendance/', NEW.id),
      'alarm_off',
      v_recipient,
      1
    );
  ELSEIF (OLD.timein <> NEW.timein OR OLD.timeout <> NEW.timeout OR OLD.totalhours <> NEW.totalhours) THEN
    CALL notify_event(
      'attendance_updated',
      'Attendance updated',
      CONCAT('Hours: ', IFNULL(OLD.totalhours,'-'), ' → ', IFNULL(NEW.totalhours,'-')),
      @actor_user_id,
      'tbl_people_attendance',
      NEW.id,
      CONCAT('/attendance/', NEW.id),
      'edit',
      v_recipient,
      1
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_tbl_people_attendance_after_delete` AFTER DELETE ON `tbl_people_attendance` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'attendance_deleted',
    CONCAT('Pointage supprimé — ID#', COALESCE(OLD.idno,''), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Suppression de présence',
      CONCAT('Employé : ', COALESCE(OLD.employee,'N/A')),
      CONCAT('Date : ', COALESCE(DATE_FORMAT(OLD.date, '%Y-%m-%d'),'N/A')),
      CONCAT('Entrée : ', COALESCE(OLD.timein,'N/A')),
      CONCAT('Sortie : ', COALESCE(OLD.timeout,'N/A')),
      CONCAT('Total : ', COALESCE(OLD.totalhours,'N/A')),
      CONCAT('ID : ', OLD.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_people_attendance', OLD.id, CONCAT('/attendance'), 'alarm_on', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_tbl_people_attendance_after_insert` AFTER INSERT ON `tbl_people_attendance` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;
  DECLARE v_user BIGINT DEFAULT NULL;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  -- destinataire : utilisateur avec le même idno
  SELECT id INTO v_user FROM users WHERE idno = NEW.idno LIMIT 1;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'attendance_created',
    CONCAT('Pointage créé — ID#', COALESCE(NEW.idno,''), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Enregistrement de présence',
      CONCAT('Employé : ', COALESCE(NEW.employee,'N/A')),
      CONCAT('Date : ', COALESCE(DATE_FORMAT(NEW.date, '%Y-%m-%d'),'N/A')),
      CONCAT('Entrée : ', COALESCE(NEW.timein,'N/A')),
      CONCAT('Sortie : ', COALESCE(NEW.timeout,'N/A')),
      CONCAT('Total : ', COALESCE(NEW.totalhours,'N/A')),
      CONCAT('Statut Entrée : ', COALESCE(NEW.status_timein,'')),
      CONCAT('Statut Sortie : ', COALESCE(NEW.status_timeout,'')),
      CONCAT('Raison : ', COALESCE(NEW.reason,'')),
      CONCAT('Commentaire : ', COALESCE(NEW.comment,'')),
      CONCAT('ID : ', NEW.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_people_attendance', NEW.id, CONCAT('/attendance/', NEW.id), 'alarm_on', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF v_user IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, v_user);
  END IF;
  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_tbl_people_attendance_after_update` AFTER UPDATE ON `tbl_people_attendance` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;
  DECLARE v_user BIGINT DEFAULT NULL;
  DECLARE v_change VARCHAR(255) DEFAULT 'Mise à jour de présence';

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  SELECT id INTO v_user FROM users WHERE idno = NEW.idno LIMIT 1;

  -- petit résumé de changement (entrée/sortie)
  IF (OLD.timeout IS NULL AND NEW.timeout IS NOT NULL) THEN
    SET v_change = CONCAT('Sortie enregistrée à ', NEW.timeout);
  ELSEIF (OLD.timein <> NEW.timein) THEN
    SET v_change = CONCAT('Entrée modifiée : ', COALESCE(OLD.timein,'N/A'), ' → ', COALESCE(NEW.timein,'N/A'));
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'attendance_updated',
    CONCAT('Pointage mis à jour — ID#', COALESCE(NEW.idno,''), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Mise à jour de présence',
      CONCAT('Résumé : ', v_change),
      CONCAT('Employé : ', COALESCE(NEW.employee, OLD.employee,'N/A')),
      CONCAT('Date : ', COALESCE(DATE_FORMAT(NEW.date, '%Y-%m-%d'), DATE_FORMAT(OLD.date, '%Y-%m-%d'), 'N/A')),
      CONCAT('Entrée : ', COALESCE(NEW.timein, OLD.timein,'N/A')),
      CONCAT('Sortie : ', COALESCE(NEW.timeout, OLD.timeout,'N/A')),
      CONCAT('Total : ', COALESCE(NEW.totalhours, OLD.totalhours,'N/A')),
      CONCAT('ID : ', NEW.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'tbl_people_attendance', NEW.id, CONCAT('/attendance/', NEW.id), 'alarm_on', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF v_user IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, v_user);
  END IF;
  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_people_devotion`
--

CREATE TABLE `tbl_people_devotion` (
  `id` int(11) UNSIGNED NOT NULL,
  `reference` int(11) DEFAULT NULL,
  `idno` varchar(11) DEFAULT NULL,
  `employee` varchar(255) DEFAULT '',
  `devotion_date` date DEFAULT NULL,
  `devotion_text` text DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `archived` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `tbl_people_devotion`
--
DELIMITER $$
CREATE TRIGGER `trg_devotion_insert_notify` AFTER INSERT ON `tbl_people_devotion` FOR EACH ROW BEGIN
  DECLARE v_recipient BIGINT;

  SELECT u.id INTO v_recipient
  FROM users u
  WHERE u.idno = NEW.idno
  LIMIT 1;

  CALL notify_event(
    'devotion_posted',
    'New devotion posted',
    LEFT(NEW.devotion_text, 160),
    @actor_user_id,
    'tbl_people_devotion',
    NEW.id,
    CONCAT('/devotions/', NEW.id),
    'menu_book',
    v_recipient,
    1
  );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_people_leaves`
--

CREATE TABLE `tbl_people_leaves` (
  `id` int(10) UNSIGNED NOT NULL,
  `reference` int(11) DEFAULT NULL,
  `idno` varchar(11) DEFAULT NULL,
  `employee` varchar(255) DEFAULT '',
  `typeid` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT '',
  `leavefrom` date DEFAULT NULL,
  `leaveto` date DEFAULT NULL,
  `returndate` date DEFAULT NULL,
  `reason` varchar(255) DEFAULT '',
  `status` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `archived` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `tbl_people_leaves`
--
DELIMITER $$
CREATE TRIGGER `trg_leave_insert_notify` AFTER INSERT ON `tbl_people_leaves` FOR EACH ROW BEGIN
  DECLARE v_recipient BIGINT;

  SELECT u.id INTO v_recipient
  FROM users u
  WHERE u.idno = NEW.idno
  LIMIT 1;

  CALL notify_event(
    'leave_requested',
    'Leave requested',
    CONCAT(
      'Type: ', IFNULL(NEW.`type`, '-'),
      ' | From ', DATE_FORMAT(NEW.leavefrom, '%Y-%m-%d'),
      ' to ', DATE_FORMAT(NEW.leaveto, '%Y-%m-%d')
    ),
    @actor_user_id,
    'tbl_people_leaves',
    NEW.id,
    CONCAT('/leaves/', NEW.id),
    'event',
    v_recipient,
    1
  );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_people_schedules`
--

CREATE TABLE `tbl_people_schedules` (
  `id` int(10) UNSIGNED NOT NULL,
  `reference` int(11) DEFAULT NULL,
  `idno` varchar(11) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `employee` varchar(255) DEFAULT NULL,
  `intime` text DEFAULT NULL,
  `outime` text DEFAULT NULL,
  `datefrom` date DEFAULT NULL,
  `dateto` date DEFAULT NULL,
  `hours` int(11) DEFAULT NULL,
  `restday` varchar(255) DEFAULT NULL,
  `archive` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_assignments`
--

CREATE TABLE `tbl_report_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `person_id` bigint(20) UNSIGNED NOT NULL,
  `metric_id` bigint(20) UNSIGNED NOT NULL,
  `starts_on` date DEFAULT NULL,
  `ends_on` date DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `overrides` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`overrides`)),
  `created_by` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_categories`
--

CREATE TABLE `tbl_report_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_metrics`
--

CREATE TABLE `tbl_report_metrics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `status_set_id` int(11) DEFAULT NULL,
  `name` varchar(160) NOT NULL,
  `value_type` enum('status','number','percent','status_or_percent','boolean','text') DEFAULT 'status',
  `value_mode` varchar(16) NOT NULL DEFAULT 'scale',
  `allow_percent` tinyint(1) DEFAULT 0,
  `allow_number` tinyint(1) DEFAULT 0,
  `scoring_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`scoring_rules`)),
  `weight` decimal(6,2) NOT NULL DEFAULT 1.00,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `scoring_mode` enum('preset','status_map','numeric_scale','percent','boolean') NOT NULL DEFAULT 'preset'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_metric_events`
--

CREATE TABLE `tbl_report_metric_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `person_id` bigint(20) UNSIGNED NOT NULL,
  `metric_id` bigint(20) UNSIGNED NOT NULL,
  `occurred_on` date NOT NULL,
  `status` varchar(120) DEFAULT NULL,
  `numeric_value` int(10) DEFAULT NULL,
  `source` varchar(80) NOT NULL DEFAULT 'manual',
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `created_by` varchar(64) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_metric_person_overrides`
--

CREATE TABLE `tbl_report_metric_person_overrides` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `person_id` bigint(20) UNSIGNED NOT NULL,
  `metric_id` bigint(20) UNSIGNED NOT NULL,
  `scoring_mode` enum('preset','status_map','numeric_scale','percent','boolean') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_metric_scale`
--

CREATE TABLE `tbl_report_metric_scale` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `metric_id` bigint(20) UNSIGNED NOT NULL,
  `kind` enum('threshold','range') NOT NULL,
  `min_value` decimal(12,4) DEFAULT NULL,
  `max_value` decimal(12,4) DEFAULT NULL,
  `score` decimal(6,4) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_metric_status_items`
--

CREATE TABLE `tbl_report_metric_status_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `metric_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(80) NOT NULL,
  `score` decimal(6,4) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_people`
--

CREATE TABLE `tbl_report_people` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(120) NOT NULL,
  `last_name` varchar(120) NOT NULL,
  `idno` varchar(50) NOT NULL,
  `reference` int(11) DEFAULT NULL,
  `team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_report_rollups`
--

CREATE TABLE `tbl_report_report_rollups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `person_id` bigint(20) UNSIGNED NOT NULL,
  `metric_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `score` decimal(6,4) NOT NULL,
  `components` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`components`)),
  `created_by` varchar(64) NOT NULL DEFAULT 'SYS',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_status_options`
--

CREATE TABLE `tbl_report_status_options` (
  `id` int(11) NOT NULL,
  `set_id` int(11) NOT NULL,
  `code` varchar(60) NOT NULL,
  `label` varchar(80) NOT NULL,
  `weight` decimal(6,2) DEFAULT NULL,
  `color` varchar(16) DEFAULT NULL,
  `sort` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_status_sets`
--

CREATE TABLE `tbl_report_status_sets` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `description` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_status_set_items`
--

CREATE TABLE `tbl_report_status_set_items` (
  `id` int(11) NOT NULL,
  `status_set_id` int(11) NOT NULL,
  `code` varchar(60) NOT NULL,
  `label` varchar(120) NOT NULL,
  `score` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_teams`
--

CREATE TABLE `tbl_report_teams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `description` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_views`
--

CREATE TABLE `tbl_report_views` (
  `id` int(10) UNSIGNED NOT NULL,
  `report_id` int(11) DEFAULT NULL,
  `last_viewed` varchar(255) DEFAULT NULL,
  `title` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `technicians`
--

CREATE TABLE `technicians` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tracks`
--

CREATE TABLE `tracks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `theme` varchar(255) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `relative_path` varchar(255) DEFAULT NULL,
  `duration_seconds` varchar(10) NOT NULL DEFAULT '0',
  `performer` varchar(255) DEFAULT NULL,
  `ext` varchar(255) DEFAULT NULL,
  `duration_raw` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `reference` int(11) DEFAULT NULL,
  `idno` varchar(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `role_id` int(11) DEFAULT NULL,
  `work_type` varchar(255) DEFAULT NULL,
  `acc_type` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `scope_level` enum('inherit','all','campus','ministry','department') NOT NULL DEFAULT 'inherit',
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `fcm_token` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `trg_user_insert_notify` AFTER INSERT ON `users` FOR EACH ROW BEGIN
  -- Notify the new user and the creator (if any)
  CALL notify_event(
    'user_created',
    'Welcome! Account created',
    CONCAT('User: ', NEW.name, ' (', IFNULL(NEW.email,''), ')'),
    @actor_user_id,
    'users', NEW.id,
    CONCAT('/users/', NEW.id),
    'person_add',
    NEW.id,        -- recipient = the new user
    1              -- cc actor if exists
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_users_after_delete` AFTER DELETE ON `users` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'user_deleted',
    CONCAT('Utilisateur supprimé — ', COALESCE(OLD.name, CONCAT('User #', OLD.id)), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Suppression de compte utilisateur',
      CONCAT('Nom : ', COALESCE(OLD.name, CONCAT('User #', OLD.id))),
      CONCAT('Email : ', COALESCE(OLD.email, 'N/A')),
      CONCAT('Rôle ID : ', COALESCE(OLD.role_id, 0)),
      CONCAT('ID : ', OLD.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'users', OLD.id, CONCAT('/users'), 'person_add', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  -- CC acteur (fallback Admins si personne)
  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_users_after_insert` AFTER INSERT ON `users` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'user_created',
    CONCAT('Utilisateur créé — ', COALESCE(NEW.name, CONCAT('User #', NEW.id)), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Création de compte utilisateur',
      CONCAT('Nom : ', COALESCE(NEW.name, CONCAT('User #', NEW.id))),
      CONCAT('Email : ', COALESCE(NEW.email, 'N/A')),
      CONCAT('Rôle ID : ', COALESCE(NEW.role_id, 0)),
      CONCAT('ID : ', NEW.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'users', NEW.id, CONCAT('/users/', NEW.id), 'person_add', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  -- destinataire principal : le nouvel utilisateur
  IF NEW.id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, NEW.id);
  END IF;
  -- CC acteur
  IF @actor_user_id IS NOT NULL AND @actor_user_id <> NEW.id THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_users_after_update` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name
    FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'user_updated',
    CONCAT('Profil mis à jour — ', COALESCE(NEW.name, OLD.name, CONCAT('User #', NEW.id)), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Mise à jour de compte utilisateur',
      CONCAT('Nom : ', COALESCE(NEW.name, OLD.name, CONCAT('User #', NEW.id))),
      CONCAT('Email : ', COALESCE(NEW.email, OLD.email, 'N/A')),
      CONCAT('Rôle ID : ', COALESCE(NEW.role_id, OLD.role_id, 0)),
      CONCAT('ID : ', NEW.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'users', NEW.id, CONCAT('/users/', NEW.id), 'person_add', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  -- destinataire : l’utilisateur concerné
  IF NEW.id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, NEW.id);
  END IF;
  -- CC acteur
  IF @actor_user_id IS NOT NULL AND @actor_user_id <> NEW.id THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users_permissions`
--

CREATE TABLE `users_permissions` (
  `id` int(11) UNSIGNED NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `perm_id` int(11) DEFAULT NULL,
  `allow` tinyint(1) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `users_permissions`
--
DELIMITER $$
CREATE TRIGGER `trg_users_permissions_after_delete` AFTER DELETE ON `users_permissions` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;
  DECLARE v_role_name VARCHAR(191);

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;
  SELECT role_name INTO v_role_name FROM users_roles WHERE id = OLD.role_id LIMIT 1;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'permissions_revoked',
    CONCAT('Permissions mises à jour — Rôle ', COALESCE(v_role_name, CONCAT('#', OLD.role_id)), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Retrait de permission',
      CONCAT('Rôle : ', COALESCE(v_role_name, CONCAT('#', OLD.role_id))),
      CONCAT('Permission ID : #', OLD.perm_id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'users_permissions', OLD.role_id, CONCAT('/roles/', OLD.role_id, '/permissions'), 'key', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  INSERT INTO notification_targets(notification_id,user_id)
  SELECT v_notif_id, u.id FROM users u WHERE u.role_id = OLD.role_id AND u.id IS NOT NULL AND (@actor_user_id IS NULL OR u.id <> @actor_user_id);
  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, @actor_user_id WHERE NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id AND t.user_id = @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id) SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_users_permissions_after_insert` AFTER INSERT ON `users_permissions` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;
  DECLARE v_role_name VARCHAR(191);

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;
  SELECT role_name INTO v_role_name FROM users_roles WHERE id = NEW.role_id LIMIT 1;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'permissions_granted',
    CONCAT('Permissions mises à jour — Rôle ', COALESCE(v_role_name, CONCAT('#', NEW.role_id)), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Attribution de permission',
      CONCAT('Rôle : ', COALESCE(v_role_name, CONCAT('#', NEW.role_id))),
      CONCAT('Permission ID : #', NEW.perm_id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'users_permissions', NEW.role_id, CONCAT('/roles/', NEW.role_id, '/permissions'), 'key', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  INSERT INTO notification_targets(notification_id,user_id)
  SELECT v_notif_id, u.id FROM users u WHERE u.role_id = NEW.role_id AND u.id IS NOT NULL AND (@actor_user_id IS NULL OR u.id <> @actor_user_id);
  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, @actor_user_id WHERE NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id AND t.user_id = @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id) SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_users_permissions_after_update` AFTER UPDATE ON `users_permissions` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;
  DECLARE v_role_name_old VARCHAR(191);
  DECLARE v_role_name_new VARCHAR(191);

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;
  SELECT role_name INTO v_role_name_old FROM users_roles WHERE id = OLD.role_id LIMIT 1;
  SELECT role_name INTO v_role_name_new FROM users_roles WHERE id = NEW.role_id LIMIT 1;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'permissions_changed',
    CONCAT('Permissions modifiées — Rôle ', COALESCE(v_role_name_new, COALESCE(v_role_name_old, CONCAT('#', NEW.role_id))), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Modification de lien rôle ↔ permission',
      CONCAT('Rôle (old → new) : ', COALESCE(v_role_name_old, CONCAT('#', OLD.role_id)), ' → ', COALESCE(v_role_name_new, CONCAT('#', NEW.role_id))),
      CONCAT('Permission (old → new) : #', OLD.perm_id, ' → #', NEW.perm_id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'users_permissions', NEW.role_id, CONCAT('/roles/', NEW.role_id, '/permissions'), 'key', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  INSERT INTO notification_targets(notification_id,user_id)
  SELECT v_notif_id, u.id FROM users u WHERE u.role_id = NEW.role_id AND u.id IS NOT NULL AND (@actor_user_id IS NULL OR u.id <> @actor_user_id);
  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id)
    SELECT v_notif_id, @actor_user_id WHERE NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id AND t.user_id = @actor_user_id);
  END IF;
  IF NOT EXISTS(SELECT 1 FROM notification_targets t WHERE t.notification_id = v_notif_id) THEN
    INSERT INTO notification_targets(notification_id,user_id) SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users_roles`
--

CREATE TABLE `users_roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_name` varchar(255) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `scope_level` enum('all','campus','ministry','department') NOT NULL DEFAULT 'all'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `users_roles`
--
DELIMITER $$
CREATE TRIGGER `trg_users_roles_after_delete` AFTER DELETE ON `users_roles` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'role_deleted',
    CONCAT('Rôle supprimé — ', COALESCE(OLD.role_name, CONCAT('#', OLD.id)), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Suppression de rôle',
      CONCAT('Nom du rôle : ', COALESCE(OLD.role_name, CONCAT('#', OLD.id))),
      CONCAT('État : ', COALESCE(OLD.state, 'N/A')),
      CONCAT('ID : ', OLD.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'users_roles', OLD.id, CONCAT('/roles'), 'badge', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  INSERT INTO notification_targets(notification_id,user_id)
  SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_users_roles_after_insert` AFTER INSERT ON `users_roles` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'role_created',
    CONCAT('Rôle créé — ', COALESCE(NEW.role_name, CONCAT('#', NEW.id)), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Création de rôle',
      CONCAT('Nom du rôle : ', COALESCE(NEW.role_name, CONCAT('#', NEW.id))),
      CONCAT('État : ', COALESCE(NEW.state, 'N/A')),
      CONCAT('ID : ', NEW.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'users_roles', NEW.id, CONCAT('/roles/', NEW.id), 'badge', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  -- Destinataires : Admins + acteur
  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  INSERT INTO notification_targets(notification_id,user_id)
  SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_users_roles_after_update` AFTER UPDATE ON `users_roles` FOR EACH ROW BEGIN
  DECLARE v_notif_id BIGINT;
  DECLARE v_actor_name VARCHAR(191) DEFAULT 'Système';
  DECLARE v_admin_role_id INT DEFAULT 1;

  IF @actor_user_id IS NOT NULL THEN
    SELECT COALESCE(name, CONCAT('Utilisateur #', id)) INTO v_actor_name FROM users WHERE id = @actor_user_id LIMIT 1;
  END IF;

  INSERT INTO notifications(type,title,body,actor_user_id,subject_table,subject_id,url,icon,created_at)
  VALUES(
    'role_updated',
    CONCAT('Rôle mis à jour — ', COALESCE(NEW.role_name, OLD.role_name, CONCAT('#', NEW.id)), ' (par ', v_actor_name, ')'),
    CONCAT_WS(CHAR(10),
      'Action : Mise à jour de rôle',
      CONCAT('Nom (old → new) : ', COALESCE(OLD.role_name, CONCAT('#', OLD.id)), ' → ', COALESCE(NEW.role_name, CONCAT('#', NEW.id))),
      CONCAT('État : ', COALESCE(NEW.state, OLD.state, 'N/A')),
      CONCAT('ID : ', NEW.id),
      CONCAT('Effectué par : ', v_actor_name, IF(@actor_user_id IS NOT NULL, CONCAT(' (#', @actor_user_id, ')'), '')),
      CONCAT('Horodatage : ', DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))
    ),
    @actor_user_id,
    'users_roles', NEW.id, CONCAT('/roles/', NEW.id), 'badge', NOW()
  );
  SET v_notif_id = LAST_INSERT_ID();

  IF @actor_user_id IS NOT NULL THEN
    INSERT INTO notification_targets(notification_id,user_id) VALUES(v_notif_id, @actor_user_id);
  END IF;
  INSERT INTO notification_targets(notification_id,user_id)
  SELECT v_notif_id, u.id FROM users u WHERE u.role_id = v_admin_role_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_logs`
--

CREATE TABLE `user_activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `referrer` text DEFAULT NULL,
  `utm_source` varchar(255) DEFAULT NULL,
  `utm_campaign` varchar(255) DEFAULT NULL,
  `route_name` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `session_path` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`session_path`)),
  `device_type` varchar(255) NOT NULL,
  `platform` varchar(100) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `browser_version` varchar(50) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `locale` varchar(255) DEFAULT NULL,
  `page_load_time` int(11) DEFAULT NULL,
  `error_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`error_data`)),
  `event_metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_metadata`)),
  `conversion_flag` tinyint(1) NOT NULL DEFAULT 0,
  `experiment_variant` varchar(255) DEFAULT NULL,
  `ip_risk_score` float DEFAULT NULL,
  `is_https` tinyint(1) NOT NULL DEFAULT 1,
  `page` varchar(255) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `action_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `session_start` datetime DEFAULT NULL,
  `session_end` datetime DEFAULT NULL,
  `session_duration` int(11) DEFAULT NULL,
  `time_spent` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `volunteers`
--

CREATE TABLE `volunteers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_followups`
--

CREATE TABLE `volunteer_followups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `leader_id` int(10) UNSIGNED NOT NULL,
  `volunteer_id` int(10) UNSIGNED NOT NULL,
  `group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `meeting_date` date NOT NULL,
  `categories_json` longtext DEFAULT NULL,
  `severity` varchar(20) NOT NULL DEFAULT 'medium',
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `followup_due_on` date DEFAULT NULL,
  `assigned_to_id` bigint(20) UNSIGNED DEFAULT NULL,
  `checkin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `attachment_name` varchar(255) DEFAULT NULL,
  `attachment_size` int(11) DEFAULT NULL,
  `escalate_to_ml` tinyint(1) NOT NULL DEFAULT 0,
  `reasons_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`reasons_json`)),
  `reason_other` varchar(200) DEFAULT NULL,
  `conversation_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`conversation_json`)),
  `conversation_other` varchar(200) DEFAULT NULL,
  `response_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response_json`)),
  `response_other` varchar(200) DEFAULT NULL,
  `next_steps_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`next_steps_json`)),
  `next_other` varchar(200) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `resolution_summary` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_progress`
--

CREATE TABLE `volunteer_progress` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `volunteer_id` bigint(20) UNSIGNED NOT NULL,
  `week_stage` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_tasks`
--

CREATE TABLE `volunteer_tasks` (
  `id` int(11) NOT NULL,
  `reference` int(11) DEFAULT NULL,
  `idno` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `total_task` int(11) DEFAULT NULL,
  `month` date DEFAULT NULL,
  `task_count` int(11) DEFAULT NULL,
  `completion_status` varchar(50) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bible_verses`
--
ALTER TABLE `bible_verses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_notifications`
--
ALTER TABLE `custom_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daily_tokens`
--
ALTER TABLE `daily_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `focus_projects`
--
ALTER TABLE `focus_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projects_workspace_id_deadline_index` (`workspace_id`,`deadline`),
  ADD KEY `idx_projects_user_id` (`user_id`);

--
-- Indexes for table `focus_tasks`
--
ALTER TABLE `focus_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_project_id_completed_deadline_index` (`project_id`,`completed`,`deadline`);

--
-- Indexes for table `focus_task_sessions`
--
ALTER TABLE `focus_task_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_task_date` (`task_id`,`focus_date`),
  ADD KEY `idx_task_id` (`task_id`),
  ADD KEY `idx_focus_date` (`focus_date`);

--
-- Indexes for table `focus_workspaces`
--
ALTER TABLE `focus_workspaces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_workspaces_user_id` (`user_id`);

--
-- Indexes for table `focus_workspace_user`
--
ALTER TABLE `focus_workspace_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_workspace_user` (`workspace_id`,`user_id`),
  ADD KEY `idx_workspace_id` (`workspace_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `ht_arrondissements`
--
ALTER TABLE `ht_arrondissements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ht_communes`
--
ALTER TABLE `ht_communes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ht_departements`
--
ALTER TABLE `ht_departements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meeting_attendance`
--
ALTER TABLE `meeting_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meeting_attendance_reference_index` (`reference`);

--
-- Indexes for table `meeting_events`
--
ALTER TABLE `meeting_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meeting_link_id` (`meeting_link_id`),
  ADD KEY `meeting_type` (`meeting_type`,`meeting_date`);

--
-- Indexes for table `meeting_link`
--
ALTER TABLE `meeting_link`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monthly_digital_gifts`
--
ALTER TABLE `monthly_digital_gifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `month` (`month`);

--
-- Indexes for table `mr_agenda_items`
--
ALTER TABLE `mr_agenda_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mr_asana_mappings`
--
ALTER TABLE `mr_asana_mappings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mr_attendees`
--
ALTER TABLE `mr_attendees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mr_drive_settings`
--
ALTER TABLE `mr_drive_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mr_magic_links`
--
ALTER TABLE `mr_magic_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mr_meetings`
--
ALTER TABLE `mr_meetings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mr_meeting_attendees`
--
ALTER TABLE `mr_meeting_attendees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mr_meeting_notes`
--
ALTER TABLE `mr_meeting_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mr_meeting_types`
--
ALTER TABLE `mr_meeting_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mr_metrics`
--
ALTER TABLE `mr_metrics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mr_shows`
--
ALTER TABLE `mr_shows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mr_tasks`
--
ALTER TABLE `mr_tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mr_teams`
--
ALTER TABLE `mr_teams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `new_tbl_people_schedules`
--
ALTER TABLE `new_tbl_people_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `playlist_exports`
--
ALTER TABLE `playlist_exports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `playlist_items`
--
ALTER TABLE `playlist_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `radio_assignments`
--
ALTER TABLE `radio_assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `radio_checkins`
--
ALTER TABLE `radio_checkins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `radio_maintenance`
--
ALTER TABLE `radio_maintenance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `radio_pocs`
--
ALTER TABLE `radio_pocs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `radio_sites`
--
ALTER TABLE `radio_sites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `radio_stations`
--
ALTER TABLE `radio_stations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `radio_station_status_log`
--
ALTER TABLE `radio_station_status_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `radio_technician`
--
ALTER TABLE `radio_technician`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reading_plan_chapters`
--
ALTER TABLE `reading_plan_chapters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `small_groups`
--
ALTER TABLE `small_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `small_group_members`
--
ALTER TABLE `small_group_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_campus_data`
--
ALTER TABLE `tbl_campus_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_campus_info`
--
ALTER TABLE `tbl_campus_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_computers`
--
ALTER TABLE `tbl_computers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_creative_attachments`
--
ALTER TABLE `tbl_creative_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_creative_attachments_request_id_foreign` (`request_id`),
  ADD KEY `tbl_creative_attachments_task_id_foreign` (`task_id`),
  ADD KEY `tbl_creative_attachments_uploaded_by_people_id_foreign` (`uploaded_by_people_id`);

--
-- Indexes for table `tbl_creative_badges`
--
ALTER TABLE `tbl_creative_badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tbl_creative_badges_code_unique` (`code`);

--
-- Indexes for table `tbl_creative_contribution_snapshots`
--
ALTER TABLE `tbl_creative_contribution_snapshots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `creative_contrib_unique` (`people_id`,`period`,`period_start`);

--
-- Indexes for table `tbl_creative_points_ledger`
--
ALTER TABLE `tbl_creative_points_ledger`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tbl_creative_points_ledger_idempotency_key_unique` (`idempotency_key`),
  ADD KEY `tbl_creative_points_ledger_people_id_occurred_at_index` (`people_id`,`occurred_at`);

--
-- Indexes for table `tbl_creative_requests`
--
ALTER TABLE `tbl_creative_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_creative_requests_status_request_type_priority_index` (`status`,`request_type`,`priority`),
  ADD KEY `tbl_creative_requests_desired_due_at_index` (`desired_due_at`),
  ADD KEY `tbl_creative_requests_campus_id_ministry_id_department_id_index` (`campus_id`,`ministry_id`,`department_id`),
  ADD KEY `tbl_creative_requests_requester_people_id_foreign` (`requester_people_id`);

--
-- Indexes for table `tbl_creative_tasks`
--
ALTER TABLE `tbl_creative_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_creative_tasks_request_id_status_index` (`request_id`,`status`),
  ADD KEY `tbl_creative_tasks_priority_due_at_index` (`priority`,`due_at`);

--
-- Indexes for table `tbl_creative_task_assignments`
--
ALTER TABLE `tbl_creative_task_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tbl_creative_task_assignments_task_id_people_id_unique` (`task_id`,`people_id`),
  ADD KEY `tbl_creative_task_assignments_people_id_foreign` (`people_id`);

--
-- Indexes for table `tbl_creative_task_comments`
--
ALTER TABLE `tbl_creative_task_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_task_id` (`task_id`),
  ADD KEY `idx_people_id` (`people_id`);

--
-- Indexes for table `tbl_creative_task_events`
--
ALTER TABLE `tbl_creative_task_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_creative_task_events_task_id_occurred_at_index` (`task_id`,`occurred_at`),
  ADD KEY `tbl_creative_task_events_people_id_foreign` (`people_id`);

--
-- Indexes for table `tbl_creative_user_badges`
--
ALTER TABLE `tbl_creative_user_badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tbl_creative_user_badges_people_id_badge_id_unique` (`people_id`,`badge_id`),
  ADD KEY `tbl_creative_user_badges_badge_id_foreign` (`badge_id`);

--
-- Indexes for table `tbl_equipment`
--
ALTER TABLE `tbl_equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_form_campus`
--
ALTER TABLE `tbl_form_campus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_form_department`
--
ALTER TABLE `tbl_form_department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_form_jobtitle`
--
ALTER TABLE `tbl_form_jobtitle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_form_leavegroup`
--
ALTER TABLE `tbl_form_leavegroup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_form_leavetype`
--
ALTER TABLE `tbl_form_leavetype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_form_ministry`
--
ALTER TABLE `tbl_form_ministry`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_meeting_category`
--
ALTER TABLE `tbl_meeting_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_people`
--
ALTER TABLE `tbl_people`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_people_attendance`
--
ALTER TABLE `tbl_people_attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_people_devotion`
--
ALTER TABLE `tbl_people_devotion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_people_leaves`
--
ALTER TABLE `tbl_people_leaves`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_people_schedules`
--
ALTER TABLE `tbl_people_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report_assignments`
--
ALTER TABLE `tbl_report_assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report_categories`
--
ALTER TABLE `tbl_report_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report_metrics`
--
ALTER TABLE `tbl_report_metrics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report_metric_events`
--
ALTER TABLE `tbl_report_metric_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report_metric_person_overrides`
--
ALTER TABLE `tbl_report_metric_person_overrides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report_metric_scale`
--
ALTER TABLE `tbl_report_metric_scale`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report_metric_status_items`
--
ALTER TABLE `tbl_report_metric_status_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report_people`
--
ALTER TABLE `tbl_report_people`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tbl_report_people_reference_unique` (`reference`);

--
-- Indexes for table `tbl_report_report_rollups`
--
ALTER TABLE `tbl_report_report_rollups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report_status_options`
--
ALTER TABLE `tbl_report_status_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report_status_sets`
--
ALTER TABLE `tbl_report_status_sets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report_status_set_items`
--
ALTER TABLE `tbl_report_status_set_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report_teams`
--
ALTER TABLE `tbl_report_teams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report_views`
--
ALTER TABLE `tbl_report_views`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `technicians`
--
ALTER TABLE `technicians`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tracks`
--
ALTER TABLE `tracks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_permissions`
--
ALTER TABLE `users_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_roles`
--
ALTER TABLE `users_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `volunteers`
--
ALTER TABLE `volunteers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `volunteer_followups`
--
ALTER TABLE `volunteer_followups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `volunteer_progress`
--
ALTER TABLE `volunteer_progress`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `volunteer_tasks`
--
ALTER TABLE `volunteer_tasks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bible_verses`
--
ALTER TABLE `bible_verses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_notifications`
--
ALTER TABLE `custom_notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daily_tokens`
--
ALTER TABLE `daily_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `focus_task_sessions`
--
ALTER TABLE `focus_task_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `focus_workspace_user`
--
ALTER TABLE `focus_workspace_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ht_arrondissements`
--
ALTER TABLE `ht_arrondissements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ht_communes`
--
ALTER TABLE `ht_communes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ht_departements`
--
ALTER TABLE `ht_departements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meeting_attendance`
--
ALTER TABLE `meeting_attendance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meeting_events`
--
ALTER TABLE `meeting_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meeting_link`
--
ALTER TABLE `meeting_link`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `monthly_digital_gifts`
--
ALTER TABLE `monthly_digital_gifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mr_agenda_items`
--
ALTER TABLE `mr_agenda_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mr_asana_mappings`
--
ALTER TABLE `mr_asana_mappings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mr_attendees`
--
ALTER TABLE `mr_attendees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mr_drive_settings`
--
ALTER TABLE `mr_drive_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mr_magic_links`
--
ALTER TABLE `mr_magic_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mr_meetings`
--
ALTER TABLE `mr_meetings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mr_meeting_attendees`
--
ALTER TABLE `mr_meeting_attendees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mr_meeting_notes`
--
ALTER TABLE `mr_meeting_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mr_meeting_types`
--
ALTER TABLE `mr_meeting_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mr_metrics`
--
ALTER TABLE `mr_metrics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mr_shows`
--
ALTER TABLE `mr_shows`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mr_tasks`
--
ALTER TABLE `mr_tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mr_teams`
--
ALTER TABLE `mr_teams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `new_tbl_people_schedules`
--
ALTER TABLE `new_tbl_people_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playlists`
--
ALTER TABLE `playlists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playlist_exports`
--
ALTER TABLE `playlist_exports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playlist_items`
--
ALTER TABLE `playlist_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `radio_assignments`
--
ALTER TABLE `radio_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `radio_checkins`
--
ALTER TABLE `radio_checkins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `radio_maintenance`
--
ALTER TABLE `radio_maintenance`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `radio_pocs`
--
ALTER TABLE `radio_pocs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `radio_sites`
--
ALTER TABLE `radio_sites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `radio_stations`
--
ALTER TABLE `radio_stations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `radio_station_status_log`
--
ALTER TABLE `radio_station_status_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `radio_technician`
--
ALTER TABLE `radio_technician`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `small_groups`
--
ALTER TABLE `small_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `small_group_members`
--
ALTER TABLE `small_group_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_campus_data`
--
ALTER TABLE `tbl_campus_data`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_campus_info`
--
ALTER TABLE `tbl_campus_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_computers`
--
ALTER TABLE `tbl_computers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_creative_attachments`
--
ALTER TABLE `tbl_creative_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_creative_badges`
--
ALTER TABLE `tbl_creative_badges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_creative_contribution_snapshots`
--
ALTER TABLE `tbl_creative_contribution_snapshots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_creative_points_ledger`
--
ALTER TABLE `tbl_creative_points_ledger`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_creative_requests`
--
ALTER TABLE `tbl_creative_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_creative_tasks`
--
ALTER TABLE `tbl_creative_tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_creative_task_assignments`
--
ALTER TABLE `tbl_creative_task_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_creative_task_comments`
--
ALTER TABLE `tbl_creative_task_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_creative_task_events`
--
ALTER TABLE `tbl_creative_task_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_creative_user_badges`
--
ALTER TABLE `tbl_creative_user_badges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_equipment`
--
ALTER TABLE `tbl_equipment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_form_campus`
--
ALTER TABLE `tbl_form_campus`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_form_department`
--
ALTER TABLE `tbl_form_department`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_form_jobtitle`
--
ALTER TABLE `tbl_form_jobtitle`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_form_leavegroup`
--
ALTER TABLE `tbl_form_leavegroup`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_form_leavetype`
--
ALTER TABLE `tbl_form_leavetype`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_form_ministry`
--
ALTER TABLE `tbl_form_ministry`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_meeting_category`
--
ALTER TABLE `tbl_meeting_category`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_people`
--
ALTER TABLE `tbl_people`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_people_attendance`
--
ALTER TABLE `tbl_people_attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_people_leaves`
--
ALTER TABLE `tbl_people_leaves`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_people_schedules`
--
ALTER TABLE `tbl_people_schedules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_assignments`
--
ALTER TABLE `tbl_report_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_categories`
--
ALTER TABLE `tbl_report_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_metrics`
--
ALTER TABLE `tbl_report_metrics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_metric_events`
--
ALTER TABLE `tbl_report_metric_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_metric_person_overrides`
--
ALTER TABLE `tbl_report_metric_person_overrides`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_metric_scale`
--
ALTER TABLE `tbl_report_metric_scale`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_metric_status_items`
--
ALTER TABLE `tbl_report_metric_status_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_people`
--
ALTER TABLE `tbl_report_people`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_report_rollups`
--
ALTER TABLE `tbl_report_report_rollups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_status_options`
--
ALTER TABLE `tbl_report_status_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_status_sets`
--
ALTER TABLE `tbl_report_status_sets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_status_set_items`
--
ALTER TABLE `tbl_report_status_set_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_teams`
--
ALTER TABLE `tbl_report_teams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_views`
--
ALTER TABLE `tbl_report_views`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `technicians`
--
ALTER TABLE `technicians`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tracks`
--
ALTER TABLE `tracks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_permissions`
--
ALTER TABLE `users_permissions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_roles`
--
ALTER TABLE `users_roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `volunteers`
--
ALTER TABLE `volunteers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `volunteer_followups`
--
ALTER TABLE `volunteer_followups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `volunteer_progress`
--
ALTER TABLE `volunteer_progress`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `volunteer_tasks`
--
ALTER TABLE `volunteer_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `focus_projects`
--
ALTER TABLE `focus_projects`
  ADD CONSTRAINT `projects_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `focus_workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `focus_tasks`
--
ALTER TABLE `focus_tasks`
  ADD CONSTRAINT `tasks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `focus_projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meeting_events`
--
ALTER TABLE `meeting_events`
  ADD CONSTRAINT `fk_meeting_events_meeting_link` FOREIGN KEY (`meeting_link_id`) REFERENCES `meeting_link` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_creative_attachments`
--
ALTER TABLE `tbl_creative_attachments`
  ADD CONSTRAINT `tbl_creative_attachments_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `tbl_creative_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_creative_attachments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tbl_creative_tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_creative_attachments_uploaded_by_people_id_foreign` FOREIGN KEY (`uploaded_by_people_id`) REFERENCES `tbl_people` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_creative_contribution_snapshots`
--
ALTER TABLE `tbl_creative_contribution_snapshots`
  ADD CONSTRAINT `tbl_creative_contribution_snapshots_people_id_foreign` FOREIGN KEY (`people_id`) REFERENCES `tbl_people` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_creative_points_ledger`
--
ALTER TABLE `tbl_creative_points_ledger`
  ADD CONSTRAINT `tbl_creative_points_ledger_people_id_foreign` FOREIGN KEY (`people_id`) REFERENCES `tbl_people` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_creative_requests`
--
ALTER TABLE `tbl_creative_requests`
  ADD CONSTRAINT `tbl_creative_requests_requester_people_id_foreign` FOREIGN KEY (`requester_people_id`) REFERENCES `tbl_people` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_creative_tasks`
--
ALTER TABLE `tbl_creative_tasks`
  ADD CONSTRAINT `tbl_creative_tasks_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `tbl_creative_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_creative_task_assignments`
--
ALTER TABLE `tbl_creative_task_assignments`
  ADD CONSTRAINT `tbl_creative_task_assignments_people_id_foreign` FOREIGN KEY (`people_id`) REFERENCES `tbl_people` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_creative_task_assignments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tbl_creative_tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_creative_task_events`
--
ALTER TABLE `tbl_creative_task_events`
  ADD CONSTRAINT `tbl_creative_task_events_people_id_foreign` FOREIGN KEY (`people_id`) REFERENCES `tbl_people` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tbl_creative_task_events_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tbl_creative_tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_creative_user_badges`
--
ALTER TABLE `tbl_creative_user_badges`
  ADD CONSTRAINT `tbl_creative_user_badges_badge_id_foreign` FOREIGN KEY (`badge_id`) REFERENCES `tbl_creative_badges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_creative_user_badges_people_id_foreign` FOREIGN KEY (`people_id`) REFERENCES `tbl_people` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
