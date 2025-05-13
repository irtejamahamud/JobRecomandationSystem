-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2025 at 11:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nextworkx_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'admin', 'admin@gmail.com', '0192023a7bbd73250516f069df18b500', '2025-04-27 05:09:13');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `job_seeker_id` int(11) NOT NULL,
  `status` enum('Pending','Shortlisted','Rejected') DEFAULT 'Pending',
  `applied_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `job_id`, `job_seeker_id`, `status`, `applied_at`) VALUES
(1, 6, 1, 'Shortlisted', '2025-04-26 17:40:53'),
(2, 4, 1, 'Rejected', '2025-04-26 17:45:55'),
(3, 7, 8, 'Rejected', '2025-04-27 09:39:12'),
(4, 1, 8, 'Pending', '2025-04-27 09:39:43'),
(5, 1, 1, 'Pending', '2025-04-27 09:52:43'),
(6, 8, 1, 'Pending', '2025-05-03 15:18:50');

-- --------------------------------------------------------

--
-- Table structure for table `applied_jobs`
--

CREATE TABLE `applied_jobs` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `job_seeker_id` int(11) NOT NULL,
  `applied_at` datetime DEFAULT current_timestamp(),
  `status` enum('Pending','Shortlisted','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `applied_jobs`
--

INSERT INTO `applied_jobs` (`id`, `job_id`, `job_seeker_id`, `applied_at`, `status`) VALUES
(1, 6, 1, '2025-04-26 17:40:53', 'Pending'),
(2, 4, 1, '2025-04-26 17:45:55', 'Pending'),
(3, 7, 8, '2025-04-27 09:39:12', 'Pending'),
(4, 1, 8, '2025-04-27 09:39:43', 'Pending'),
(5, 1, 1, '2025-04-27 09:52:43', 'Pending'),
(6, 8, 1, '2025-05-03 15:18:50', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `certifications`
--

CREATE TABLE `certifications` (
  `id` int(11) NOT NULL,
  `job_seeker_id` int(11) NOT NULL,
  `certification_name` varchar(255) DEFAULT NULL,
  `issuing_organization` varchar(255) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `certificate_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_profiles`
--

CREATE TABLE `company_profiles` (
  `id` int(11) NOT NULL,
  `recruiter_id` int(11) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `about_us` text DEFAULT NULL,
  `org_type` varchar(100) DEFAULT NULL,
  `industry_type` varchar(100) DEFAULT NULL,
  `team_size` varchar(50) DEFAULT NULL,
  `est_year` year(4) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `company_vision` text DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `map_location` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `completion_percentage` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `progress` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_profiles`
--

INSERT INTO `company_profiles` (`id`, `recruiter_id`, `logo`, `banner`, `company_name`, `about_us`, `org_type`, `industry_type`, `team_size`, `est_year`, `website`, `company_vision`, `facebook`, `twitter`, `instagram`, `youtube`, `map_location`, `phone`, `email`, `completion_percentage`, `created_at`, `updated_at`, `progress`) VALUES
(1, 2, '283772310_750481309455094_5604539716106464380_n.jpg', '282333935_310071968001155_343628481189003933_n.jpg', 'WorkXLTD', 'gfdgsgsd', 'Private', 'Technology', '1–10', '2024', 'https://www.facebook.com/', 'test1', 'https://www.facebook.com/', 'https://www.facebook.com/', 'https://www.facebook.com/', 'https://www.facebook.com/', '', '', '', 25, '2025-04-24 20:41:07', '2025-05-02 11:54:34', 82),
(2, 3, '359820352_790564532895968_8813457284352352279_n.jpg', 'bs-cybersecurity-scaled.jpeg', 'Worktest', 'wdasd', 'Private', 'Technology', '1–10', '2025', 'https://www.facebook.com/', 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 24, '2025-04-25 04:59:27', '2025-04-25 05:00:07', 59),
(3, 5, '_96053764-5dc1-4208-9ab0-129ca3fbc0bf.jpg', '86635666-3ebf-4bd8-8990-18f1791e6f7f.webp', 'Test3', 'Very Good for work', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 24, '2025-04-25 22:35:54', '2025-04-25 22:37:32', 24),
(4, 9, 'GUB.png', 'bs-cybersecurity-scaled.jpeg', 'test', 'fsd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 24, '2025-04-26 08:45:43', '2025-04-26 08:45:43', 24);

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(11) NOT NULL,
  `job_seeker_id` int(11) NOT NULL,
  `level_id` int(11) DEFAULT NULL,
  `institution_name` varchar(255) DEFAULT NULL,
  `degree_title` varchar(255) DEFAULT NULL,
  `start_year` year(4) DEFAULT NULL,
  `end_year` year(4) DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `job_seeker_id`, `level_id`, `institution_name`, `degree_title`, `start_year`, `end_year`, `grade`) VALUES
(1, 1, 3, 'Green University of Bangladesh', 'CSE', '2020', '2024', '3.9');

-- --------------------------------------------------------

--
-- Table structure for table `education_levels`
--

CREATE TABLE `education_levels` (
  `id` int(11) NOT NULL,
  `level_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `education_levels`
--

INSERT INTO `education_levels` (`id`, `level_name`) VALUES
(1, 'HSC'),
(2, 'Diploma'),
(3, 'BSc'),
(4, 'BTech'),
(5, 'MSc'),
(6, 'MTech'),
(7, 'MBA'),
(8, 'PhD');

-- --------------------------------------------------------

--
-- Table structure for table `experience`
--

CREATE TABLE `experience` (
  `id` int(11) NOT NULL,
  `job_seeker_id` int(11) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `responsibilities` text DEFAULT NULL,
  `experience_year` int(11) GENERATED ALWAYS AS (greatest(0,timestampdiff(YEAR,`start_date`,`end_date`))) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `experience`
--

INSERT INTO `experience` (`id`, `job_seeker_id`, `company_name`, `job_title`, `start_date`, `end_date`, `responsibilities`) VALUES
(4, 1, 'Green University', 'Programmer', '2021-04-03', '2025-04-01', '');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `recruiter_id` int(11) NOT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `job_role` varchar(100) DEFAULT NULL,
  `min_salary` int(11) DEFAULT NULL,
  `max_salary` int(11) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'BDT',
  `vacancies` int(11) DEFAULT 1,
  `job_level` enum('Intern','Entry','Mid','Senior','Lead') DEFAULT 'Entry',
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `job_description` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `posted_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `start_date` date DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `job_type` enum('Full Time','Part Time','Remote','Freelance','Seasonal','Fixed-Price') DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `recruiter_id`, `job_title`, `tags`, `job_role`, `min_salary`, `max_salary`, `currency`, `vacancies`, `job_level`, `country`, `city`, `job_description`, `skills`, `posted_on`, `start_date`, `expire_date`, `job_type`, `category`) VALUES
(1, 2, 'Software Engineer', 'PHP', 'Developer', 20000, 30000, 'BDT', 3, 'Entry', 'Bangladesh', 'Dhaka', 'First Job Post', 'HTML,CSS,PHP,MYSQL', '2025-04-24 20:12:24', '2025-04-25', '2025-04-30', 'Full Time', NULL),
(2, 3, 'Manager', 'Team Lead', 'Manager', 30000, 40000, 'BDT', 3, 'Mid', 'Bangladesh', 'Dhaka', 'test', 'HTML,CSS,PHP,MYSQL', '2025-04-25 21:56:15', '2025-04-10', '2025-04-30', 'Full Time', NULL),
(3, 5, 'UI Designer', 'Figma, Css', 'Designer', 60000, 700000, 'BDT', 6, 'Senior', 'Bangladesh', 'Dhaka', 'test3', 'CSS,Figma,Canva', '2025-04-25 22:33:18', '2025-04-27', '2025-04-30', 'Full Time', NULL),
(4, 9, 'Manager Test', 'Team Lead', 'Manager', 100000, 120000, 'BDT', 1, 'Senior', 'Bangladesh', 'Dhaka', 'test', 'CSS,Figma,Canva', '2025-04-26 08:46:45', '2025-04-26', '2025-05-07', 'Full Time', NULL),
(5, 2, 'Software Engineer Test', 'PHP', 'Developer', 100000, 120000, 'BDT', 1, 'Mid', 'Bangladesh', 'Dhaka', 'Nunc sed a nisl purus. Nibh dis faucibus proin lacus tristique. Sit congue non vitae odio sit erat in. Felis eu ultrices a sed massa. Commodo fringilla sed tempor risus laoreet ultricies ipsum. Habitasse morbi faucibus in iaculis lectus. Nisi enim feugiat enim volutpat. Sem quis viverra viverra odio mauris nunc. \r\nEt nunc ut tempus duis nisl sed massa. Ornare varius faucibus nisi vitae vitae cras ornare. Cras facilisis dignissim augue lorem amet adipiscing cursus fames mauris. Tortor amet porta proin in. Orci imperdiet nisi dignissim pellentesque morbi vitae. Quisque tincidunt metus lectus porta eget blandit euismod sem nunc. Tortor gravida amet amet sapien mauris massa.Tortor varius nam maecenas duis blandit elit sit sit. Ante mauris morbi diam habitant donec.', 'HTML,CSS,PHP,MYSQL', '2025-04-26 09:58:56', '2025-04-23', '2025-04-28', 'Full Time', NULL),
(6, 2, 'Software Engineer Test-2', 'PHP,C', 'Developer', 30000, 50000, 'BDT', 4, 'Senior', 'Bangladesh', 'Dhaka', 'A job description is a written document that details the requirements, duties, and responsibilities of a specific job position. It helps potential candidates understand the job\'s scope and whether they are qualified. Employers use job descriptions to attract suitable candidates and ensure they are hiring for the right skills and experience', 'HTML,CSS,PHP,MYSQL', '2025-04-26 10:03:11', '2025-04-26', '2025-04-25', 'Full Time', NULL),
(7, 2, '	PHP Developer', '	PHP, Laravel, Backend', 'Developer', 20000, 50000, 'BDT', 1, 'Mid', 'Bangladesh', 'Dhaka', '	Build REST APIs, optimize MySQL databases, server-side development.', 'MySQL, RESTful API, OOP PHP', '2025-04-26 19:44:46', '2025-04-27', '2025-04-30', 'Full Time', NULL),
(8, 2, 'Test-5', 'Figma, Css', 'Marketing Executive', 20000, 30000, 'BDT', 2, 'Mid', 'Bangladesh', 'Dhaka', 'Dummy text', 'CSS,Figma,Canva', '2025-05-02 16:18:43', '2025-05-02', '2025-05-15', 'Full Time', 'Digital Marketing');

-- --------------------------------------------------------

--
-- Table structure for table `job_categories`
--

CREATE TABLE `job_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_categories`
--

INSERT INTO `job_categories` (`id`, `name`) VALUES
(1, 'Commerce'),
(20, 'Consulting'),
(18, 'Content Writing'),
(13, 'Customer Support'),
(10, 'Digital Marketing'),
(4, 'Education'),
(11, 'Engineering'),
(14, 'Finance & Accounting'),
(5, 'Financial Services'),
(9, 'Graphic Design'),
(12, 'Healthcare'),
(3, 'Hotels & Tourism'),
(15, 'Human Resources'),
(6, 'Information Technology'),
(16, 'Legal Services'),
(19, 'Project Management'),
(17, 'Sales'),
(7, 'Software Development'),
(2, 'Telecommunications'),
(8, 'Web Development');

-- --------------------------------------------------------

--
-- Table structure for table `job_details`
--

CREATE TABLE `job_details` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `experience_required` varchar(100) DEFAULT NULL,
  `degree_required` varchar(100) DEFAULT NULL,
  `responsibilities` text DEFAULT NULL,
  `professional_skills` text DEFAULT NULL,
  `map_embed_url` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_details`
--

INSERT INTO `job_details` (`id`, `job_id`, `experience_required`, `degree_required`, `responsibilities`, `professional_skills`, `map_embed_url`, `created_at`) VALUES
(1, 6, '3', 'BSc', 'Et nunc ut tempus duis nisl sed massa. Ornare varius faucibus nisi vitae vitae cras ornare. Cras facilisis dignissim augu\r\nCras facilisis dignissim augue lorem amet adipiscing cursus fames mauris. Tortor amet porta proin in\r\nOrnare varius faucibus nisi vitae vitae cras ornare. Cras facilisis dignissim augue lorem amet adipiscing cursus fames\r\nTortor amet porta proin in. Orci imperdiet nisi dignissim pellentesque morbi vitae. Quisque tincidunt metus lectus porta \r\nTortor amet porta proin in. Orci imperdiet nisi dignissim pellentesque morbi vitae. Quisque tincidunt metus lectus porta \r\nTortor amet porta proin in. Orci imperdiet nisi dignissim pellentesque morbi vitae. Quisque tincidunt metus lectus porta ', 'Et nunc ut tempus duis nisl sed massa. Ornare varius faucibus nisi vitae vitae cras ornare.\r\nOrnare varius faucibus nisi vitae vitae cras ornare\r\nTortor amet porta proin in. Orci imperdiet nisi dignissim pellentesque morbi vitae\r\nTortor amet porta proin in. Orci imperdiet nisi dignissim pellentesque morbi vitae\r\nTortor amet porta proin in. Orci imperdiet nisi dignissim pellentesque morbi vitae', '', '2025-04-26 10:03:11'),
(2, 7, '2', 'BSc', '', '', '', '2025-04-26 19:44:46'),
(3, 8, '2', 'BSc', 'dummy', 'dummy', '', '2025-05-02 16:18:43');

-- --------------------------------------------------------

--
-- Table structure for table `job_seekers`
--

CREATE TABLE `job_seekers` (
  `id` int(11) NOT NULL,
  `job_seeker_id` int(11) DEFAULT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `resume_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_seekers`
--

INSERT INTO `job_seekers` (`id`, `job_seeker_id`, `fullname`, `email`, `password`, `mobile`, `date_of_birth`, `gender`, `address`, `profile_image`, `resume_file`, `created_at`) VALUES
(1, 1, 'Irteja Mahmud', 'irtejamahmud9@gmail.com', '$2y$10$/muS.5BA/mXRabZXfjU7zeGlFO2TM/aBjNWL22ExoRLc5Fdpg4w9a', '01632645891', '2001-12-17', 'Male', 'Khilkhet,Dhaka', '1706601911714.jpg', 'CV1.pdf', '2025-04-25 11:52:55'),
(3, 4, 'Nazmul Hasan', 'nazmul123@gmail.com', '$2y$10$vO55fwrem9AvMz/puIXk4.ChsciiVyfS6aPQoLxGck2LTeF1.EDjy', '01632645894', NULL, NULL, NULL, NULL, NULL, '2025-04-25 11:54:59'),
(4, 8, 'Tanjim Mahtab', 'tanjim123@gmail.com', '', '01632645894', '0000-00-00', '', '', '1746012092_image.jpg', NULL, '2025-04-26 07:38:36');

-- --------------------------------------------------------

--
-- Table structure for table `job_seeker_profiles`
--

CREATE TABLE `job_seeker_profiles` (
  `id` int(11) NOT NULL,
  `job_seeker_id` int(11) NOT NULL,
  `biography` text DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `facebook_link` varchar(255) DEFAULT NULL,
  `twitter_link` varchar(255) DEFAULT NULL,
  `linkedin_link` varchar(255) DEFAULT NULL,
  `reddit_link` varchar(255) DEFAULT NULL,
  `instagram_link` varchar(255) DEFAULT NULL,
  `youtube_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `marital_status` varchar(50) DEFAULT 'Single',
  `secondary_phone` varchar(20) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_seeker_profiles`
--

INSERT INTO `job_seeker_profiles` (`id`, `job_seeker_id`, `biography`, `cover_letter`, `facebook_link`, `twitter_link`, `linkedin_link`, `reddit_link`, `instagram_link`, `youtube_link`, `created_at`, `marital_status`, `secondary_phone`, `website`) VALUES
(1, 1, 'A biography, or simply bio, is a detailed description of a person\'s life. It involves more than just basic facts like education, work, relationships, and death; it portrays a person\'s experience of these life events. Unlike a profile or curriculum vitae (résumé), a biography presents a subject\'s life story, highlighting various aspects of their life, including intimate details of experience, and may include an analysis of the subject\'s personality.', 'I am currently a senior at the University of Central Florida involved in Performing Arts. I believe that my\r\nproficient communication skills and ability to multi-task in a fast-paced environment will fit well with the\r\nMiami Shakespeare Theatre’s marketing internship. With a major in Communication, my educational\r\nbackground has provided me with an understanding of media components and the ability to clearly\r\narticulate my thoughts and ideas. Additionally, I have gained practical skills in my work as a Media Intern\r\nwith XYZ, Inc. Through this experience, I have learned to effectively facilitate meetings, develop\r\npromotional materials, and engage in program planning featuring local artists and performers. I have also\r\nhad two years of event planning and marketing experience through my previous work with the UCF\r\nMarketing Department. In that role, I updated website content and developed concepts for events. These\r\nexperiences have taught me how to effectively manage my time while working under tight deadlines.', 'https://www.facebook.com/irteja.mahamud/', 'https://www.facebook.com/irteja.mahamud/', 'https://www.facebook.com/irteja.mahamud/', 'https://www.facebook.com/irteja.mahamud/', 'https://www.facebook.com/irteja.mahamud/', '', '2025-04-27 20:21:26', 'Single', '', ''),
(2, 8, '', '', NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-30 11:21:32', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `job_seeker_skills`
--

CREATE TABLE `job_seeker_skills` (
  `id` int(11) NOT NULL,
  `job_seeker_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL,
  `proficiency` enum('Beginner','Intermediate','Advanced') DEFAULT 'Intermediate'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_seeker_skills`
--

INSERT INTO `job_seeker_skills` (`id`, `job_seeker_id`, `skill_id`, `proficiency`) VALUES
(8, 1, 4, 'Intermediate'),
(9, 1, 12, 'Beginner'),
(10, 1, 5, 'Advanced'),
(11, 1, 10, 'Intermediate'),
(12, 1, 3, 'Intermediate'),
(13, 1, 18, 'Intermediate'),
(14, 1, 22, 'Advanced');

-- --------------------------------------------------------

--
-- Table structure for table `job_views`
--

CREATE TABLE `job_views` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `viewed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `job_seeker_id` int(11) NOT NULL,
  `language_name` varchar(100) DEFAULT NULL,
  `proficiency` enum('Basic','Conversational','Fluent','Native') DEFAULT 'Conversational'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `job_seeker_id`, `language_name`, `proficiency`) VALUES
(5, 1, 'English', 'Conversational'),
(6, 1, 'Bangla', 'Basic');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `job_seeker_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `project_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `job_seeker_id`, `title`, `description`, `project_link`) VALUES
(2, 1, 'Ecommerce Website Using PHP', 'Using PHP ,HTML,MYSQl i have greated a grate project', 'https://github.com/irtejamahamud/E-Commerce-Website');

-- --------------------------------------------------------

--
-- Table structure for table `recruiters`
--

CREATE TABLE `recruiters` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recruiters`
--

INSERT INTO `recruiters` (`id`, `user_id`, `fullname`, `email`, `mobile`, `created_at`) VALUES
(1, 2, 'Pankaj Mahanta', 'pankaj123@gmail.com', '01632645892', '2025-04-25 22:17:31'),
(2, 3, 'Irteja Mahmud', 'irteja123@gmail.com', '01632645893', '2025-04-25 22:17:31'),
(3, 5, 'Shakil', 'shakil123@gmail.com', '01632645895', '2025-04-25 22:31:10'),
(4, 9, 'Test -5', 'test@gmail.com', '01632645891', '2025-04-26 08:44:20');

-- --------------------------------------------------------

--
-- Table structure for table `resumes`
--

CREATE TABLE `resumes` (
  `id` int(11) NOT NULL,
  `job_seeker_id` int(11) NOT NULL,
  `resume_title` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `resumes`
--

INSERT INTO `resumes` (`id`, `job_seeker_id`, `resume_title`, `file_name`, `uploaded_at`) VALUES
(1, 1, 'Irteja Mahmud CV', '1745790583_CV2.pdf', '2025-04-27 21:49:43'),
(2, 1, 'Irteja Mahmud CV2', '1745837979_CV6.pdf', '2025-04-28 10:59:39');

-- --------------------------------------------------------

--
-- Table structure for table `saved_jobs`
--

CREATE TABLE `saved_jobs` (
  `id` int(11) NOT NULL,
  `job_seeker_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `saved_jobs`
--

INSERT INTO `saved_jobs` (`id`, `job_seeker_id`, `job_id`, `saved_at`) VALUES
(3, 1, 4, '2025-04-26 18:33:04'),
(4, 1, 2, '2025-04-26 18:33:06'),
(5, 1, 5, '2025-04-26 18:33:48');

-- --------------------------------------------------------

--
-- Table structure for table `skill_master`
--

CREATE TABLE `skill_master` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skill_master`
--

INSERT INTO `skill_master` (`id`, `name`) VALUES
(1, 'HTML'),
(2, 'CSS'),
(3, 'JavaScript'),
(4, 'PHP'),
(5, 'Python'),
(6, 'Java'),
(7, 'C++'),
(8, 'SQL'),
(9, 'React'),
(10, 'Node.js'),
(11, 'Laravel'),
(12, 'Django'),
(13, 'Machine Learning'),
(14, 'UI/UX Design'),
(15, 'Communication'),
(16, 'Bootstrap'),
(17, 'TailwindCSS'),
(18, 'RESTful API'),
(19, 'GraphQL'),
(20, 'MongoDB'),
(21, 'PostgreSQL'),
(22, 'MySql'),
(23, 'Azure Cloud'),
(24, 'Google Cloud'),
(25, 'Docker'),
(26, 'Kubernetes'),
(27, 'Figma'),
(28, 'Adobe XD'),
(29, 'Redux'),
(30, 'TypeScript'),
(31, 'Express.js'),
(32, 'Flask'),
(33, 'TensorFlow'),
(34, 'Pandas'),
(35, 'Data Visualization');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `role` enum('job_seeker','recruiter') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `mobile`, `role`, `created_at`, `last_login`) VALUES
(1, 'Irteja Mahmud', 'irtejamahamud9@gmail.com', '$2y$10$/muS.5BA/mXRabZXfjU7zeGIFO2TM/aBjINWL22ExoRuWElAktG6a', '01632645891', 'job_seeker', '2025-04-23 13:15:30', NULL),
(2, 'Pankaj Mahanta', 'pankaj123@gmail.com', '$2y$10$tim2k5/Pa/G0EFq0nkt4E.aY4q1UUVk9IXWsPdx4k3hgoE0tSykHy', '01632645892', 'recruiter', '2025-04-24 15:10:00', NULL),
(3, 'Irteja Mahmud', 'irteja123@gmail.com', '$2y$10$cn/8EQqxAjedTgmdb5vCouUxqR1rcKIBqdZXzg/RPHNhggoEffb9e', '01632645893', 'recruiter', '2025-04-25 04:55:03', NULL),
(4, 'Nazmul Hasan', 'nazmul123@gmail.com', '$2y$10$vO55fwrem9AvMz/puIXk4.ChsciiVyfS6aPQoLxGck2LTeF1.EDjy', '01632645894', 'job_seeker', '2025-04-25 11:54:59', NULL),
(5, 'Shakil', 'shakil123@gmail.com', '$2y$10$K4ux1SeAoqwmedZsUEVUqeg1hBEFaa.nwuqKGizT6CLLmWlE2HQau', '01632645895', 'recruiter', '2025-04-25 22:31:10', NULL),
(8, 'Tanjim Mahtab', 'tanjim123@gmail.com', '$2y$10$7Pzae1QFM3Kt99ZmlIh2Zug48Xn/5oTk6JSoaBXIDfiAdfVHAZA5i', '01632645899', 'job_seeker', '2025-04-26 07:38:36', NULL),
(9, 'Test -5', 'test@gmail.com', '$2y$10$.dJHsp7FRJel3ZtkZW.whu1VcmSg87BctFCn/K8m/jPCubzjrmG.C', '01632645856', 'recruiter', '2025-04-26 08:44:20', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_id` (`job_id`,`job_seeker_id`),
  ADD KEY `job_seeker_id` (`job_seeker_id`);

--
-- Indexes for table `applied_jobs`
--
ALTER TABLE `applied_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `job_seeker_id` (`job_seeker_id`);

--
-- Indexes for table `certifications`
--
ALTER TABLE `certifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_certifications_job_seeker_id` (`job_seeker_id`);

--
-- Indexes for table `company_profiles`
--
ALTER TABLE `company_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `recruiter_id` (`recruiter_id`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `level_id` (`level_id`),
  ADD KEY `fk_education_job_seeker_id` (`job_seeker_id`);

--
-- Indexes for table `education_levels`
--
ALTER TABLE `education_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `experience`
--
ALTER TABLE `experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_experience_job_seeker_id` (`job_seeker_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `fk_recruiter_job` (`recruiter_id`);

--
-- Indexes for table `job_categories`
--
ALTER TABLE `job_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `job_details`
--
ALTER TABLE `job_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `job_seekers`
--
ALTER TABLE `job_seekers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_seeker_id` (`job_seeker_id`);

--
-- Indexes for table `job_seeker_profiles`
--
ALTER TABLE `job_seeker_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_seeker_id` (`job_seeker_id`);

--
-- Indexes for table `job_seeker_skills`
--
ALTER TABLE `job_seeker_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `skill_id` (`skill_id`),
  ADD KEY `fk_skills_job_seeker_id` (`job_seeker_id`);

--
-- Indexes for table `job_views`
--
ALTER TABLE `job_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_languages_job_seeker_id` (`job_seeker_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_projects_job_seeker_id` (`job_seeker_id`);

--
-- Indexes for table `recruiters`
--
ALTER TABLE `recruiters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `resumes`
--
ALTER TABLE `resumes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_seeker_id` (`job_seeker_id`);

--
-- Indexes for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_seeker_id` (`job_seeker_id`,`job_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `skill_master`
--
ALTER TABLE `skill_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `email_3` (`email`),
  ADD UNIQUE KEY `mobile` (`mobile`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `applied_jobs`
--
ALTER TABLE `applied_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `certifications`
--
ALTER TABLE `certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_profiles`
--
ALTER TABLE `company_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `education_levels`
--
ALTER TABLE `education_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `experience`
--
ALTER TABLE `experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `job_categories`
--
ALTER TABLE `job_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `job_details`
--
ALTER TABLE `job_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `job_seekers`
--
ALTER TABLE `job_seekers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `job_seeker_profiles`
--
ALTER TABLE `job_seeker_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_seeker_skills`
--
ALTER TABLE `job_seeker_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `job_views`
--
ALTER TABLE `job_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `recruiters`
--
ALTER TABLE `recruiters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `resumes`
--
ALTER TABLE `resumes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `skill_master`
--
ALTER TABLE `skill_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`job_seeker_id`) REFERENCES `job_seekers` (`job_seeker_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `applied_jobs`
--
ALTER TABLE `applied_jobs`
  ADD CONSTRAINT `applied_jobs_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applied_jobs_ibfk_2` FOREIGN KEY (`job_seeker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certifications`
--
ALTER TABLE `certifications`
  ADD CONSTRAINT `fk_certifications_job_seeker_id` FOREIGN KEY (`job_seeker_id`) REFERENCES `job_seekers` (`job_seeker_id`) ON DELETE CASCADE;

--
-- Constraints for table `company_profiles`
--
ALTER TABLE `company_profiles`
  ADD CONSTRAINT `company_profiles_ibfk_1` FOREIGN KEY (`recruiter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_company_recruiter` FOREIGN KEY (`recruiter_id`) REFERENCES `recruiters` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `education`
--
ALTER TABLE `education`
  ADD CONSTRAINT `education_ibfk_2` FOREIGN KEY (`level_id`) REFERENCES `education_levels` (`id`),
  ADD CONSTRAINT `fk_education_job_seeker_id` FOREIGN KEY (`job_seeker_id`) REFERENCES `job_seekers` (`job_seeker_id`) ON DELETE CASCADE;

--
-- Constraints for table `experience`
--
ALTER TABLE `experience`
  ADD CONSTRAINT `fk_experience_job_seeker_id` FOREIGN KEY (`job_seeker_id`) REFERENCES `job_seekers` (`job_seeker_id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `fk_recruiter_job` FOREIGN KEY (`recruiter_id`) REFERENCES `recruiters` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `job_details`
--
ALTER TABLE `job_details`
  ADD CONSTRAINT `job_details_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE;

--
-- Constraints for table `job_seekers`
--
ALTER TABLE `job_seekers`
  ADD CONSTRAINT `fk_jobseeker_user` FOREIGN KEY (`job_seeker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `job_seeker_profiles`
--
ALTER TABLE `job_seeker_profiles`
  ADD CONSTRAINT `job_seeker_profiles_ibfk_1` FOREIGN KEY (`job_seeker_id`) REFERENCES `job_seekers` (`job_seeker_id`);

--
-- Constraints for table `job_seeker_skills`
--
ALTER TABLE `job_seeker_skills`
  ADD CONSTRAINT `fk_skills_job_seeker_id` FOREIGN KEY (`job_seeker_id`) REFERENCES `job_seekers` (`job_seeker_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_seeker_skills_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skill_master` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_views`
--
ALTER TABLE `job_views`
  ADD CONSTRAINT `job_views_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_views_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `job_seekers` (`job_seeker_id`) ON DELETE SET NULL;

--
-- Constraints for table `languages`
--
ALTER TABLE `languages`
  ADD CONSTRAINT `fk_languages_job_seeker_id` FOREIGN KEY (`job_seeker_id`) REFERENCES `job_seekers` (`job_seeker_id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_job_seeker_id` FOREIGN KEY (`job_seeker_id`) REFERENCES `job_seekers` (`job_seeker_id`) ON DELETE CASCADE;

--
-- Constraints for table `recruiters`
--
ALTER TABLE `recruiters`
  ADD CONSTRAINT `recruiters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resumes`
--
ALTER TABLE `resumes`
  ADD CONSTRAINT `resumes_ibfk_1` FOREIGN KEY (`job_seeker_id`) REFERENCES `job_seekers` (`job_seeker_id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD CONSTRAINT `saved_jobs_ibfk_1` FOREIGN KEY (`job_seeker_id`) REFERENCES `job_seekers` (`job_seeker_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_jobs_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
