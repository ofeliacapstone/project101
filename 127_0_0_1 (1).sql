-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 09, 2025 at 04:47 AM
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
-- Database: `capstone`
--
CREATE DATABASE IF NOT EXISTS `capstone` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `capstone`;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `activity_log_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `date` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`activity_log_id`, `username`, `date`, `action`) VALUES
(1, 'jkev', '2013-11-18 15:25:33', 'Add Subject RIZAL'),
(2, 'jkev', '2013-11-18 15:27:08', 'Edit Subject RIZAL'),
(3, '', '2013-11-18 15:30:46', 'Edit Subject IS 221'),
(4, '', '2013-11-18 15:31:12', 'Edit Subject IS 222'),
(5, '', '2013-11-18 15:31:24', 'Edit Subject IS 223'),
(6, '', '2013-11-18 15:31:34', 'Edit Subject IS 224'),
(7, '', '2013-11-18 15:31:54', 'Edit Subject IS 227'),
(8, '', '2013-11-18 15:32:37', 'Add Subject IS 411B'),
(9, '', '2013-11-18 15:34:54', 'Edit User jkev'),
(10, 'jkev', '2014-01-17 13:26:18', 'Add User admin'),
(11, 'admin', '2020-12-21 08:37:51', 'Add Subject 1234');

-- --------------------------------------------------------

--
-- Table structure for table `answer`
--

CREATE TABLE `answer` (
  `answer_id` int(11) NOT NULL,
  `quiz_question_id` int(11) NOT NULL,
  `answer_text` varchar(100) NOT NULL,
  `choices` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `answer`
--

INSERT INTO `answer` (`answer_id`, `quiz_question_id`, `answer_text`, `choices`) VALUES
(81, 32, 'john', 'A'),
(82, 32, 'smith', 'B'),
(83, 32, 'kevin', 'C'),
(84, 32, 'lorayna', 'D'),
(85, 34, 'Peso', 'A'),
(86, 34, 'PHP Hypertext', 'B'),
(87, 34, 'PHP Hypertext Preprosesor', 'C'),
(88, 34, 'Philippines', 'D'),
(89, 36, 'Right', 'A'),
(90, 36, 'Wrong', 'B'),
(91, 36, 'Wrong', 'C'),
(92, 36, 'Wrong', 'D');

-- --------------------------------------------------------

--
-- Table structure for table `assignment`
--

CREATE TABLE `assignment` (
  `assignment_id` int(11) NOT NULL,
  `floc` varchar(300) NOT NULL,
  `fdatein` varchar(100) NOT NULL,
  `fdesc` varchar(100) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `assignment`
--

INSERT INTO `assignment` (`assignment_id`, `floc`, `fdatein`, `fdesc`, `teacher_id`, `class_id`, `fname`) VALUES
(2, 'uploads/6843_File_Doc3.docx', '2013-10-11 01:24:32', 'fasfasf', 13, 36, 'Assignment number 1'),
(3, 'uploads/3617_File_login.mdb', '2013-10-28 19:35:28', 'q', 9, 80, 'q'),
(4, 'admin/uploads/7146_File_normalization.ppt', '2013-10-30 18:48:15', 'fsaf', 9, 95, 'fsaf'),
(5, 'admin/uploads/7784_File_ABSTRACT.docx', '2013-10-30 18:48:33', 'fsaf', 9, 95, 'dsaf'),
(6, 'admin/uploads/4536_File_ABSTRACT.docx', '2013-10-30 18:53:32', 'file', 9, 95, 'abstract'),
(10, 'admin/uploads/2209_File_598378_543547629007198_436971088_n.jpg', '2013-11-01 13:13:18', 'fsafasf', 9, 95, 'Assignment#2'),
(11, 'admin/uploads/1511_File_bootstrap.css', '2013-11-01 13:18:25', 'sdsa', 9, 95, 'css'),
(12, 'admin/uploads/4309_File_new  2.txt', '2013-11-17 23:21:46', 'test', 12, 145, 'test'),
(13, 'admin/uploads/5901_File_IS 112-Personal Productivity Using IS.doc', '2013-11-18 16:59:35', 'q', 12, 145, 'q'),
(15, 'admin/uploads/7077_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-11-25 10:38:45', 'afs', 18, 159, 'dasf'),
(16, 'admin/uploads/8470_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-11-25 10:39:19', 'test', 18, 160, 'assign1'),
(17, 'admin/uploads/2840_File_IMG_0698.jpg', '2013-11-25 15:53:20', 'q', 12, 161, 'q'),
(19, '', '2013-12-07 20:11:39', 'kevin test', 12, 162, ''),
(20, '', '2013-12-07 20:26:43', 'dasddsd', 12, 145, ''),
(21, '', '2013-12-07 20:26:43', 'dasddsd', 12, 162, ''),
(22, '', '2013-12-07 20:27:18', 'dasffsafsaf', 12, 162, ''),
(23, '', '2013-12-07 20:33:11', 'test', 12, 162, ''),
(24, 'admin/uploads/7053_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-12-07 20:39:05', 'kevin', 12, 0, 'kevin'),
(25, 'admin/uploads/2417_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-12-07 20:41:10', 'kevin', 12, 0, 'kevin'),
(26, 'admin/uploads/8095_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-12-07 20:43:25', 'kevin', 12, 0, 'kevin'),
(27, 'admin/uploads/4089_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-12-07 20:47:48', 'fasfafaf', 12, 0, 'fasf'),
(28, 'admin/uploads/2948_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-12-07 20:48:59', 'dasdasd', 12, 0, 'dasd'),
(29, 'admin/uploads/5971_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-12-07 20:50:47', 'dasdasd', 12, 0, 'dsad'),
(30, 'admin/uploads/6926_File_Resume.docx', '2014-02-13 11:27:59', 'q', 12, 167, 'q'),
(31, 'admin/uploads/8289_File_sample.pdf', '2020-12-21 09:56:48', 'asdasd', 9, 186, 'asdasd');

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`class_id`, `class_name`) VALUES
(7, 'BSIS-4A'),
(8, 'BSIS-4B'),
(12, 'BSIS-3A'),
(13, 'BSIS-3B'),
(14, 'BSIS-3C'),
(15, 'BSIS-2A'),
(16, 'BSIS-2B'),
(17, 'BSIS-2C'),
(18, 'BSIS-1A'),
(19, 'BSIS-1B'),
(20, 'BSIS-1C'),
(21, 'BSED-1A'),
(22, 'AB-1C'),
(23, 'BSIT-2B'),
(24, 'BSIT-1A');

-- --------------------------------------------------------

--
-- Table structure for table `class_quiz`
--

CREATE TABLE `class_quiz` (
  `class_quiz_id` int(11) NOT NULL,
  `teacher_class_id` int(11) NOT NULL,
  `quiz_time` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `class_quiz`
--

INSERT INTO `class_quiz` (`class_quiz_id`, `teacher_class_id`, `quiz_time`, `quiz_id`) VALUES
(13, 167, 3600, 3),
(14, 167, 3600, 3),
(15, 167, 1800, 3),
(16, 185, 900, 0),
(17, 186, 1800, 6);

-- --------------------------------------------------------

--
-- Table structure for table `class_subject_overview`
--

CREATE TABLE `class_subject_overview` (
  `class_subject_overview_id` int(11) NOT NULL,
  `teacher_class_id` int(11) NOT NULL,
  `content` varchar(10000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `class_subject_overview`
--

INSERT INTO `class_subject_overview` (`class_subject_overview_id`, `teacher_class_id`, `content`) VALUES
(1, 167, '<p>Chapter&nbsp; 1</p>\r\n\r\n<p>Cha</p>\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `content_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`content_id`, `title`, `content`) VALUES
(1, 'Mission', '<pre>\r\n<span style=\"font-size:16px\"><strong>Mission</strong></span></pre>\r\n\r\n<p style=\"text-align:left\"><span style=\"font-family:arial,helvetica,sans-serif; font-size:medium\"><span style=\"font-size:large\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span></span>&nbsp; &nbsp;<span style=\"font-size:18px\"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; A leading institution in higher and continuing education commited to engage in quality instruction, development-oriented research sustinable lucrative economic enterprise, and responsive extension and training services through relevant academic programs to empower a human resource that responds effectively to challenges in life and acts as catalyst in the holistoic development of a humane society.&nbsp;</span></p>\r\n\r\n<p style=\"text-align:left\">&nbsp;</p>\r\n'),
(2, 'Vision', '<pre><span style=\"font-size: large;\"><strong>Vision</strong></span></pre>\r\n<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style=\"font-size: large;\">&nbsp; Driven by its passion for continous improvement, the State College has to vigorously pursue distinction and proficieny in delivering its statutory functions to the Filipino people in the fields of education, business, agro-fishery, industrial, science and technology, through committed and competent human resource, guided by the beacon of innovation and productivity towards the heights of elevated status. </span><br /><br /></p>'),
(3, 'History', '<pre><span style=\"font-size: large;\">HISTORY &nbsp;</span> </pre>\r\n<p style=\"text-align: justify;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Carlos Hilado Memorial State College, formerly Paglaum State College, is a public educational institution that aims to provide higher technological, professional and vocational instruction and training in science, agriculture and industrial fields as well as short term or vocational courses. It was Batas Pambansa Bilang 477 which integrated these three institutions of learning: the Negros Occidental College of Arts and Trades (NOCAT) in the Municipality of Talisay, Bacolod City National Trade School (BCNTS) in Alijis, Bacolod City, and the Negros Occidental Provincial Community College (NOPCC) in Bacolod City, into a tertiary state educational institution to be called Paglaum State College. Approved in 1983, the College Charter was implemented effective January 1, 1984, with Mr. Sulpicio P. Cartera as its President. The administrative seat of the first state college in Negros Occidental is located at the Talisay Campus which was originally established as Negros Occidental School of Arts and Trades (NOSAT) under R.A. 848, authored and sponsored by Hon. Carlos Hilado. It occupies a five-hectare land donated by the provincial government under Provincial Board Resolution No. 1163. The renaming of the college to Carlos Hilado Memorial State College was effected by virtue of House Bill No. 7707 authored by then Congressman Jose Carlos V. Lacson of the 3rd Congressional District, Province of Negros Occidental, and which finally became a law on May 5, 1994</p>\r\n<p style=\"text-align: justify;\">&nbsp;</p>\r\n<p style=\"text-align: justify;\">&nbsp;&nbsp;&nbsp; Talisay Campus. July 1, 1954 marked the formal opening of NOSAT with Mr. Francisco Apilado as its first Superintendent and Mr. Gil H. Tenefrancia as Principal. There were five (5) full time teachers, with an initial enrolment of eighty-nine (89) secondary and trade technical students. The shop courses were General Metal Works, Practical Electricity and Woodworking. The first classes were held temporarily at Talisay Elementary School while the shop buildings and classrooms were under construction. NOSAT was a recipient of FOA-PHILCUA aid in terms of technical books, equipment, tools and machinery. Alijis Campus. The Alijis Campus of the Carlos Hilado Memorial State College is situated in a 5-hectare lot located at Barangay Alijis, Bacolod City. The lot was a donation of the late Dr. Antonio Lizares. The school was formerly established as the Bacolod City National Trade School. The establishment of this trade technical institution is pursuant to R.A. 3886 in 1968, authored by the late Congressman Inocencio V. Ferrer of the second congressional district of the Province of Negros Occidental. Fortune Towne. The Fortune Towne Campus of the Carlos Hilado Memorial State College was originally situated in Negros Occidental High School (NOHS), Bacolod City on a lot owned by the Provincial Government under Provincial Board Resolution No. 91 series of 1970. The school was formerly established as the Negros Occidental Provincial Community College and formally opened on July 13, 1970 with the following course offerings: Bachelor of Arts, Technical Education and Bachelor of Commerce. The initial operation of the school started in July 13, 1970, with an initial enrolment of 209 students. Classes were first housed at the Negros Occidental High School while the first building was constructed. Then Governor Alfredo L. Montelibano spearheaded the first operation of the NOPCC along with the members of the Board of Trustees. In June 1995, the campus transferred to its new site in Fortune Towne, Bacolod City. Binalbagan Campus. On Nov. 24, 2000, the Negros Occidental School of Fisheries (NOSOF) in Binalbagan, Negros Occidental was integrated to the Carlos Hilado Memorial State College system as an external campus by virtue of Resolution No. 46 series of 2000.</p>'),
(4, 'Footer', '<p style=\"text-align:center\">CHMSC Online Learning Managenment System</p>\r\n\r\n<p style=\"text-align:center\">All Rights Reserved &reg;2013</p>\r\n'),
(5, 'Upcoming Events', '<pre>\r\nUP COMING EVENTS</pre>\r\n\r\n<p><strong>&gt;</strong> EXAM</p>\r\n\r\n<p><strong>&gt;</strong> INTERCAMPUS MEET</p>\r\n\r\n<p><strong>&gt;</strong> DEFENSE</p>\r\n\r\n<p><strong>&gt;</strong> ENROLLMENT</p>\r\n\r\n<p>&nbsp;</p>\r\n'),
(6, 'Title', '<p><span style=\"font-family:trebuchet ms,geneva\">CHMSC Online Learning Management System</span></p>\r\n'),
(7, 'News', '<pre>\r\n<span style=\"font-size:medium\"><em><strong>Recent News\r\n</strong></em></span></pre>\r\n\r\n<h2><span style=\"font-size:small\">Extension and Community Services</span></h2>\r\n\r\n<p style=\"text-align:justify\">This technology package was promoted by the College of Industrial Technology Unit is an index to offer Practical Skills and Livelihood Training Program particularly to the Ina ngTahanan of Tayabas, Barangay Zone 15, Talisay City, Negros Occidental</p>\r\n\r\n<p style=\"text-align:justify\">The respondent of this technology package were mostly &ldquo;ina&rdquo; or mothers in PurokTayabas. There were twenty mothers who responded to the call of training and enhancing their sewing skills. The beginners projects include an apron, elastics waist skirts, pillow-cover and t-shirt style top. Short sleeve blouses with buttonholes or contoured seaming are also some of the many projects introduced to the mothers. Based on the interview conducted after the culmination activity, the projects done contributed as a means of earning to the respondents.</p>\r\n\r\n<p style=\"text-align:justify\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; In support to the thrust of the government to improve the health status of neighboring barangays, the Faculty and Staff of CHMSC ECS Fortune Towne, Bacolod City, launched its Medical Mission in Patag, Silay City. It was conducted last March 2010, to address the health needs of the people. A medical consultation is given to the residents of SitioPatag to attend to their health related problems that may need medical treatment. Medical tablets for headache, flu, fever, antibiotics and others were availed by the residents.</p>\r\n\r\n<p style=\"text-align:justify\">&nbsp;</p>\r\n\r\n<p style=\"text-align:justify\">&nbsp;The BAYAN-ANIHAN is a Food Production Program with a battle cry of &ldquo;GOODBYE GUTOM&rdquo;, advocating its legacy &ldquo;Food on the Table for every Filipino Family&rdquo; through backyard gardening. NGO&rsquo;s, governmental organizations, private and public sectors, business sectors are the cooperating agencies that support and facilitate this project and Carlos Hilado Memorial State College (CHMSC) is one of the identified partner school. Being a member institution in advocating its thrust, the school through its Extension and Community Services had conducted capability training workshop along this program identifying two deputy coordinators and trainers last November 26,27 and 28, 2009, with the end in view of implementing the project all throughout the neighboring towns, provinces and regions to help address poverty in the country. Program beneficiaries were the selected families of GawadKalinga (GK) in Hope Village, Brgy. Cabatangan, Talisay City, with 120 families beneficiaries; GK FIAT Village in Silay City with 30 beneficiaries; Bonbon Dream Village brgy. E. Lopez, Silay City with 60 beneficiaries; and respectively Had. Teresita and Had. Carmen in Talisay City, Negros Occidental both with 60 member beneficiaries. This program was introduced to 30 household members with the end in view of alleviating the quality standards of their living.</p>\r\n\r\n<p style=\"text-align:justify\">&nbsp;</p>\r\n\r\n<p style=\"text-align:justify\">The extension &amp; Community Services of the College conducted a series of consultations and meetings with the different local government units to assess technology needs to determines potential products to be developed considering the abundance of raw materials in their respective areas and their product marketability. The project was released in November 2009 in six cities in the province of Negros Occidental, namely San Carlos, Sagay, Silay, Bago, Himamaylan and Sipalay and the Municipality of E. B Magalona</p>\r\n\r\n<p style=\"text-align:justify\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; The City of San Carlos focused on peanut and fish processing. Sagay and bago focused on meat processing, while Silay City on fish processing. The City of Himamaylan is on sardines, and in Sipalay focused on fish processing specially on their famous BARONGAY product. The municipality of E.B Magalona focused on bangus deboning.</p>\r\n\r\n<p style=\"text-align:justify\">&nbsp;</p>\r\n\r\n<p style=\"text-align:justify\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; The food technology instructors are tasked to provide the needed skills along with the TLDC Livelihood project that each City is embarking on while the local government units provide the training venue for the project and the training equipment and machine were provided by the Department of Science and Technology.</p>\r\n\r\n<p style=\"text-align:justify\">&nbsp;</p>\r\n'),
(8, 'Announcements', '<pre>\r\n<span style=\"font-size:medium\"><em><strong>Announcements</strong></em></span></pre>\r\n\r\n<p>Examination Period: October 9-11, 2013</p>\r\n\r\n<p>Semestrial Break: October 12- November 3, 2013</p>\r\n\r\n<p>FASKFJASKFAFASFMFAS</p>\r\n\r\n<p>GASGA</p>\r\n'),
(10, 'Calendar', '<pre style=\"text-align:center\">\r\n<span style=\"font-size:medium\"><strong>&nbsp;CALENDAR OF EVENT</strong></span></pre>\r\n\r\n<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" style=\"line-height:1.6em; margin-left:auto; margin-right:auto\">\r\n	<tbody>\r\n		<tr>\r\n			<td>\r\n			<p>First Semester &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p>June 10, 2013 to October 11, 2013&nbsp;</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Semestral Break</p>\r\n			</td>\r\n			<td>\r\n			<p>Oct. 12, 2013 to November 3, 2013</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Second Semester</p>\r\n			</td>\r\n			<td>\r\n			<p>Nov. 5, 2013 to March 27, 2014</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Summer Break</p>\r\n			</td>\r\n			<td>\r\n			<p>March 27, 2014 to April 8, 2014</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Summer</p>\r\n			</td>\r\n			<td>\r\n			<p>April 8 , 2014 to May 24, 2014</p>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n\r\n<p style=\"text-align:center\">&nbsp;</p>\r\n\r\n<table cellpadding=\"0\" cellspacing=\"0\" style=\"line-height:1.6em; margin-left:auto; margin-right:auto\">\r\n	<tbody>\r\n		<tr>\r\n			<td colspan=\"4\">\r\n			<p><strong>June 5, 2013 to October 11, 2013 &ndash; First Semester AY 2013-2014</strong></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>June 4, 2013 &nbsp; &nbsp; &nbsp; &nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p>Orientation with the Parents of the College&nbsp;Freshmen</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>June 5</p>\r\n			</td>\r\n			<td>\r\n			<p>First Day of Service</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>June 5</p>\r\n			</td>\r\n			<td>\r\n			<p>College Personnel General Assembly</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>June 6,7</p>\r\n			</td>\r\n			<td>\r\n			<p>In-Service Training (Departmental)</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>June 10</p>\r\n			</td>\r\n			<td>\r\n			<p>First Day of Classes</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>June 14</p>\r\n			</td>\r\n			<td>\r\n			<p>Orientation with Students by College/Campus/Department</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>June 19,20,21</p>\r\n			</td>\r\n			<td>\r\n			<p>Branch/Campus Visit for Administrative / Academic/Accreditation/ Concerns</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td rowspan=\"2\">\r\n			<p>June</p>\r\n			</td>\r\n			<td>\r\n			<p>Club Organizations (By Discipline/Programs)</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Student Affiliation/Induction Programs</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>July</p>\r\n			</td>\r\n			<td>\r\n			<p>Nutrition Month (Sponsor: Laboratory School)</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>July 11, 12</p>\r\n			</td>\r\n			<td>\r\n			<p>Long Tests</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>August&nbsp; 8, 9</p>\r\n			</td>\r\n			<td>\r\n			<p>Midterm Examinations</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>August 19</p>\r\n			</td>\r\n			<td>\r\n			<p>ArawngLahi</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>August 23</p>\r\n			</td>\r\n			<td>\r\n			<p>Submission of Grade Sheets for Midterm</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>August</p>\r\n			</td>\r\n			<td>\r\n			<p>Recognition Program (Dean&rsquo;s List)</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>August 26</p>\r\n			</td>\r\n			<td>\r\n			<p>National Heroes Day (Regular Holiday)</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>August 28, 29, 30</p>\r\n			</td>\r\n			<td>\r\n			<p>Sports and Cultural Meet</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>September 19,20</p>\r\n			</td>\r\n			<td>\r\n			<p>Long Tests</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>October 5</p>\r\n			</td>\r\n			<td>\r\n			<p>Teachers&rsquo; Day / World Teachers&rsquo; Day</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>October 10, 11</p>\r\n			</td>\r\n			<td>\r\n			<p>Final Examination</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>October 12</p>\r\n			</td>\r\n			<td>\r\n			<p>Semestral Break</p>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n\r\n<p style=\"text-align:center\">&nbsp;</p>\r\n\r\n<table cellpadding=\"0\" cellspacing=\"0\" style=\"margin-left:auto; margin-right:auto\">\r\n	<tbody>\r\n		<tr>\r\n			<td colspan=\"4\">\r\n			<p><strong>Nov. 4, 2013 to March 27, 2014 &ndash; Second Semester AY 2013-2014</strong></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>November 4 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p>Start of Classes</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>November 19, 20, 21, 22</p>\r\n			</td>\r\n			<td>\r\n			<p>Intercampus Sports and Cultural Fest/College Week</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>December 5, 6</p>\r\n			</td>\r\n			<td>\r\n			<p>Long Tests</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>December 19,20</p>\r\n			</td>\r\n			<td>\r\n			<p>Thanksgiving Celebrations</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>December 21</p>\r\n			</td>\r\n			<td>\r\n			<p>Start of Christmas Vacation</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>December 25</p>\r\n			</td>\r\n			<td>\r\n			<p>Christmas Day (Regular Holiday)</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>December 30</p>\r\n			</td>\r\n			<td>\r\n			<p>Rizal Day (Regular Holiday)</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>January 6, 2014</p>\r\n			</td>\r\n			<td>\r\n			<p>Classes Resume</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>January 9, 10</p>\r\n			</td>\r\n			<td>\r\n			<p>Midterm Examinations</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>January 29</p>\r\n			</td>\r\n			<td>\r\n			<p>Submission of Grades Sheets for Midterm</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>February 13, 14</p>\r\n			</td>\r\n			<td>\r\n			<p>Long Tests</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>March 6, 7</p>\r\n			</td>\r\n			<td>\r\n			<p>Final Examinations (Graduating)</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>March 13, 14</p>\r\n			</td>\r\n			<td>\r\n			<p>Final Examinations (Non-Graduating)</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>March 17, 18, 19, 20, 21</p>\r\n			</td>\r\n			<td>\r\n			<p>Recognition / Graduation Rites</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>March 27</p>\r\n			</td>\r\n			<td>\r\n			<p>Last Day of Service for Faculty</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>June 5, 2014</p>\r\n			</td>\r\n			<td>\r\n			<p>First Day of Service for SY 2014-2015</p>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n\r\n<p style=\"text-align:center\">&nbsp;</p>\r\n\r\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-left:auto; margin-right:auto\">\r\n	<tbody>\r\n		<tr>\r\n			<td colspan=\"2\">\r\n			<p><strong>FLAG RAISING CEREMONY-TALISAY CAMPUS</strong></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>MONTHS &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p>UNIT-IN-CHARGE</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>June, Sept. and Dec. 2013, March 2014</p>\r\n			</td>\r\n			<td>\r\n			<p>COE</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>July and October 2013, Jan. 2014</p>\r\n			</td>\r\n			<td>\r\n			<p>SAS</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>August and November 2013, Feb. 2014</p>\r\n\r\n			<p>April and May 2014</p>\r\n			</td>\r\n			<td>\r\n			<p>CIT</p>\r\n\r\n			<p>GASS</p>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n'),
(11, 'Directories', '<div class=\"jsn-article-content\" style=\"text-align: left;\">\r\n<pre>\r\n<span style=\"font-size:medium\"><em><strong>DIRECTORIES</strong></em></span></pre>\r\n\r\n<ul>\r\n	<li>Lab School - 712-0848</li>\r\n	<li>Accounting - 495-5560</li>\r\n	<li>Presidents Office - 495-4064(telefax)</li>\r\n	<li>VPA/PME - 495-1635</li>\r\n	<li>Registrar Office - 495-4657(telefax)</li>\r\n	<li>Cashier - 712-7272</li>\r\n	<li>CIT - 712-0670</li>\r\n	<li>SAS/COE - 495-6017</li>\r\n	<li>BAC - 712-8404(telefax)</li>\r\n	<li>Records - 495-3470</li>\r\n	<li>Supply - 495-3767</li>\r\n	<li>Internet Lab - 712-6144/712-6459</li>\r\n	<li>COA - 495-5748</li>\r\n	<li>Guard House - 476-1600</li>\r\n	<li>HRM - 495-4996</li>\r\n	<li>Extension - 457-2819</li>\r\n	<li>Canteen - 495-5396</li>\r\n	<li>Research - 712-8464</li>\r\n	<li>Library - 495-5143</li>\r\n	<li>OSA - 495-1152</li>\r\n</ul>\r\n</div>\r\n'),
(12, 'president', '<p>It is my great pleasure and privilege to welcome you to CHMSC&rsquo;s official website. Accept my deep appreciation for continuously taking interest in CHMSC and its programs and activities.<br /> Recently, the challenges of the knowledge era of the 21st Century led me to think very deeply how educational institutions of higher learning must vigorously pursue relevant e<img style=\"float: left;\" src=\"images/president.jpg\" alt=\"\" />ducation to compete with and respond to the challenges of globalization. As an international fellow, I realized that in the face of this globalization and technological advancement, educational institutions are compelled to work extraordinary in educating the youths and enhancing their potentials for gainful employment and realization of their dreams to become effective citizens.<br /><br /> Honored and humbled to be given the opportunity for stewardship of this good College, I am fully aware that the goal is to make CHMSC as the center of excellence or development in various fields. The vision, CHMSC ExCELS: Excellence, Competence and Educational Leadership in Science and Technology is a profound battle cry for each member of CHMSC Community. A CHMSCian must be technologically and academically competent, socially mature, safety conscious with care for the environment, a good citizen and possesses high moral values. The way the College is being managed, the internal and the external culture of all stockholders, and the efforts for quality and excellence will result to the establishment of the good corporate image of the College. The hallmark is reflected as the image of the good institution.<br /><br /> The tasks at hand call for our full cooperation, support and active participation. Therefore, I urge everyone to help me in the crusade to <br /><br /></p>\r\n<p style=\"text-align: justify;\"><span style=\"line-height: 1.3em;\">Provide wider access to CHMSC programs;</span></p>\r\n<p style=\"text-align: justify;\"><span style=\"line-height: 1.3em;\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;* Harness the potentials of students thru effective teaching and learning methodologies and techniques;</span></p>\r\n<p style=\"text-align: justify;\"><span style=\"line-height: 1.3em;\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;* Enable CHMSC Environment for All through secure green campus;</span></p>\r\n<p style=\"text-align: justify;\"><span style=\"line-height: 1.3em;\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;* Advocate green movement, protect intellectual property and stimulate innovation;</span></p>\r\n<p style=\"text-align: justify;\"><span style=\"line-height: 1.3em;\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;* Promote lifelong learning;</span></p>\r\n<p style=\"text-align: justify;\"><span style=\"line-height: 1.3em;\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;* Conduct Research and Development for community and poverty alleviation;</span></p>\r\n<p style=\"text-align: justify;\"><span style=\"line-height: 1.3em;\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;* Share and disseminate knowledge through publication and extension outreach to communities; and</span></p>\r\n<p style=\"text-align: justify;\"><span style=\"line-height: 1.3em;\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;*Strengthen Institute-industry linkages and public-private partnership for mutual interest.</span></p>\r\n<p style=\"text-align: justify;\"><br /><span style=\"line-height: 1.3em; text-align: justify;\">Together, WE can make CHMSC</span></p>\r\n<p style=\"text-align: justify;\"><br /><span style=\"line-height: 1.3em;\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;*A model green institution for Human Resources Development, a builder of human resources in the knowledge era of the 21st Century;</span></p>\r\n<p style=\"text-align: justify;\"><span style=\"line-height: 1.3em;\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; *A center for curricular innovations and research especially in education, technology, engineering, ICT and entrepreneurship; and</span></p>\r\n<p style=\"text-align: justify;\"><span style=\"line-height: 1.3em;\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; *A Provider of quality graduates in professional and technological programs for industry and community.</span></p>\r\n<p style=\"text-align: justify;\"><br /><br /> Dear readers and guests, these are the challenges for every CHMSCian to hurdle and the dreams to realize. This website will be one of the connections with you as we ardently take each step. Feel free to visit often and be kept posted as we continue to work for discoveries and advancement that will bring benefits to the lives of the students, the community, and the world, as a whole.<br /><br /> Warmest welcome and I wish you well!</p>\r\n<p style=\"text-align: justify;\"><br /><br /></p>\r\n<p style=\"text-align: justify;\">RENATO M. SOROLLA, Ph.D.<br />SUC President II</p>'),
(13, 'motto', '<p><strong><span style=\"color:#FFF0F5\"><span style=\"font-family:arial,helvetica,sans-serif\">CHMSC EXCELS:</span></span></strong></p>\r\n\r\n<p><strong><span style=\"color:#FFF0F5\"><span style=\"font-family:arial,helvetica,sans-serif\">Excellence, Competence and Educational</span></span></strong></p>\r\n\r\n<p><strong><span style=\"color:#FFF0F5\"><span style=\"font-family:arial,helvetica,sans-serif\">Leadership in Science and Technology</span></span></strong></p>\r\n'),
(14, 'Campuses', '<pre>\r\n<span style=\"font-size:16px\"><strong>Campuses</strong></span></pre>\r\n\r\n<ul>\r\n	<li>Alijis Campus</li>\r\n	<li>Binalbagan Campus</li>\r\n	<li>Fortunetown Campus</li>\r\n	<li>Talisay Campus<br />\r\n	&nbsp;</li>\r\n</ul>\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `dean` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`department_id`, `department_name`, `dean`) VALUES
(4, 'College of Industrial Technology', 'Dr. Antonio Deraja'),
(5, 'School of Arts and Science', 'DR.'),
(9, 'College of Education', 'null'),
(10, 'Sample Department', 'DR. John Smith');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `event_id` int(11) NOT NULL,
  `event_title` varchar(100) NOT NULL,
  `teacher_class_id` int(11) NOT NULL,
  `date_start` varchar(100) NOT NULL,
  `date_end` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`event_id`, `event_title`, `teacher_class_id`, `date_start`, `date_end`) VALUES
(12, ' 	  Orientation with the Parents of the College Freshmen', 0, '06/04/2013', '06/04/2013'),
(13, 'Start of Classes', 0, '11/04/2013', '11/04/2013'),
(14, 'Intercampus Sports and Cultural Fest/College Week', 0, '11/19/2013', '11/22/2013'),
(15, 'Long Test', 113, '12/05/2013', '12/06/2013'),
(16, 'Long Test', 0, '12/05/2013', '12/06/2013'),
(17, 'sdasf', 147, '11/16/2013', '11/16/2013'),
(18, 'Sample', 186, '12/22/2020', '12/24/2020');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `file_id` int(11) NOT NULL,
  `floc` varchar(500) NOT NULL,
  `fdatein` varchar(200) NOT NULL,
  `fdesc` varchar(100) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `uploaded_by` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`file_id`, `floc`, `fdatein`, `fdesc`, `teacher_id`, `class_id`, `fname`, `uploaded_by`) VALUES
(133, 'admin/uploads/7939_File_449E26DB.jpg', '2014-02-20 10:31:38', 'sas', 14, 177, 'sss', ''),
(132, 'admin/uploads/7939_File_449E26DB.jpg', '2014-02-20 10:29:53', 'sas', 14, 178, 'sss', ''),
(131, 'admin/uploads/7939_File_449E26DB.jpg', '2014-02-20 10:28:09', 'sas', 14, 12, 'sss', ''),
(129, 'admin/uploads/7939_File_449E26DB.jpg', '2014-02-20 10:12:38', 'sas', 0, 12, 'sss', ''),
(130, 'admin/uploads/7939_File_449E26DB.jpg', '2014-02-20 10:26:11', 'sas', 0, 12, 'sss', ''),
(128, 'admin/uploads/7614_File_1476273_644977475552481_2029187901_n.jpg', '2014-02-13 13:31:18', 'qwwqw', 12, 185, 'kevi', 'Ruby Mae Morante'),
(127, 'admin/uploads/1085_File_Resume.docx', '2014-02-13 12:53:09', 'q', 12, 183, 'q', 'Ruby Mae Morante'),
(126, 'admin/uploads/7895_File_PERU REPORT.pptx', '2014-02-13 12:35:42', 'chapter 1', 12, 182, 'chapter 1', 'Ruby Mae Morante'),
(125, 'admin/uploads/2658_File_kevin.docx', '2014-02-13 11:10:56', 'test', 12, 181, 'test', 'Ruby Mae Morante'),
(123, 'admin/uploads/4801_File_painting-02.jpg', '2013-12-11 12:02:46', 'jdkasjfd', 12, 163, 'Test', 'Ruby Mae Morante'),
(122, 'admin/uploads/3985_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-12-07 20:00:22', 'dasdasd', 12, 145, 'dasd', 'Ruby Mae Morante'),
(121, 'admin/uploads/7439_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-12-07 19:59:46', 'asdad', 12, 162, 'kevin', 'Ruby Mae Morante'),
(120, 'admin/uploads/7439_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-12-07 19:59:46', 'asdad', 12, 145, 'kevin', 'Ruby Mae Morante'),
(119, 'admin/uploads/3166_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-12-07 19:58:44', 'kevin', 12, 145, 'kevin', 'Ruby Mae Morante'),
(118, 'admin/uploads/4849_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-11-26 23:59:20', 'q', 0, 162, 'qq', 'StephanieVillanueva'),
(117, 'admin/uploads/9467_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-11-26 10:42:37', 'test', 0, 162, 'report group 1', 'MarrianneTumala'),
(116, 'admin/uploads/5990_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-11-26 02:51:24', 'w', 12, 162, 'w', 'Ruby Mae Morante'),
(115, 'admin/uploads/5990_File_win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', '2013-11-26 02:51:24', 'w', 12, 145, 'w', 'Ruby Mae Morante'),
(138, 'admin/uploads/3952_File_sample.pdf', '2020-12-21 09:24:50', 'Sample', 9, 186, 'Sample', 'JomarPabuaya'),
(139, 'admin/uploads/3579_File_sample.pdf', '2020-12-21 09:38:22', 'adasd', 9, 186, '234234', 'JomarPabuaya'),
(140, 'admin/uploads/6898_File_sample.pdf', '2020-12-21 09:39:32', 'adasd', 9, 186, '234234', 'JomarPabuaya'),
(141, 'admin/uploads/9782_File_sample.pdf', '2020-12-21 09:40:28', 'adasd', 9, 186, '234234', 'JomarPabuaya');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL,
  `reciever_id` int(11) NOT NULL,
  `content` varchar(200) NOT NULL,
  `date_sended` varchar(100) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `reciever_name` varchar(50) NOT NULL,
  `sender_name` varchar(200) NOT NULL,
  `message_status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`message_id`, `reciever_id`, `content`, `date_sended`, `sender_id`, `reciever_name`, `sender_name`, `message_status`) VALUES
(2, 11, 'fasf', '2013-11-13 13:15:47', 42, 'Aladin Cabrera', 'john kevin lorayna', ''),
(4, 71, 'bcjhbcjksdbckldj', '2013-11-25 15:59:13', 71, 'Noli Mendoza', 'Noli Mendoza', 'read'),
(17, 12, 'tst', '2013-12-01 23:38:40', 93, 'Ruby Mae  Morante', 'John Kevin  Lorayna', ''),
(19, 12, 'fasfaf', '2013-12-01 23:56:17', 93, 'Ruby Mae  Morante', 'John Kevin  Lorayna', ''),
(27, 93, 'fa', '2013-12-02 00:01:54', 12, 'John Kevin  Lorayna', 'Ruby Mae  Morante', ''),
(28, 136, 'Submit your classcard', '2014-02-13 13:35:21', 12, 'Jorgielyn Serfino', 'Ruby Mae  Morante', ''),
(29, 18, 'Test message', '2020-12-21 08:51:10', 9, 'Allan Dela Torre', 'Jomar Pabuaya', '');

-- --------------------------------------------------------

--
-- Table structure for table `message_sent`
--

CREATE TABLE `message_sent` (
  `message_sent_id` int(11) NOT NULL,
  `reciever_id` int(11) NOT NULL,
  `content` varchar(200) NOT NULL,
  `date_sended` varchar(100) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `reciever_name` varchar(100) NOT NULL,
  `sender_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `message_sent`
--

INSERT INTO `message_sent` (`message_sent_id`, `reciever_id`, `content`, `date_sended`, `sender_id`, `reciever_name`, `sender_name`) VALUES
(1, 42, 'sad', '2013-11-12 22:50:05', 42, 'john kevin lorayna', 'john kevin lorayna'),
(2, 11, 'fasf', '2013-11-13 13:15:47', 42, 'Aladin Cabrera', 'john kevin lorayna'),
(3, 12, 'bjhkcbkjsdnckldvls', '2013-11-25 15:58:55', 71, 'Ruby Mae  Morante', 'Noli Mendoza'),
(4, 71, 'bcjhbcjksdbckldj', '2013-11-25 15:59:13', 71, 'Noli Mendoza', 'Noli Mendoza'),
(5, 12, 'test', '2013-11-30 20:54:05', 93, 'Ruby Mae  Morante', 'John Kevin  Lorayna'),
(11, 12, 'tst', '2013-12-01 23:38:40', 93, 'Ruby Mae  Morante', 'John Kevin  Lorayna'),
(12, 12, 'fasfasf', '2013-12-01 23:49:13', 93, 'Ruby Mae  Morante', 'John Kevin  Lorayna'),
(13, 136, 'Submit your classcard', '2014-02-13 13:35:21', 12, 'Jorgielyn Serfino', 'Ruby Mae  Morante'),
(14, 18, 'Test message', '2020-12-21 08:51:10', 9, 'Allan Dela Torre', 'Jomar Pabuaya');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `teacher_class_id` int(11) NOT NULL,
  `notification` varchar(100) NOT NULL,
  `date_of_notification` varchar(50) NOT NULL,
  `link` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`notification_id`, `teacher_class_id`, `notification`, `date_of_notification`, `link`) VALUES
(2, 0, 'Add Downloadable Materials file name <b>sss</b>', '2014-01-17 14:35:32', 'downloadable_student.php'),
(3, 167, 'Add Annoucements', '2014-01-17 14:36:32', 'announcements_student.php'),
(4, 0, 'Add Downloadable Materials file name <b>test</b>', '2014-02-13 11:10:56', 'downloadable_student.php'),
(5, 167, 'Add Assignment file name <b>q</b>', '2014-02-13 11:27:59', 'assignment_student.php'),
(6, 0, 'Add Downloadable Materials file name <b>chapter 1</b>', '2014-02-13 12:35:42', 'downloadable_student.php'),
(7, 0, 'Add Downloadable Materials file name <b>q</b>', '2014-02-13 12:53:09', 'downloadable_student.php'),
(8, 0, 'Add Downloadable Materials file name <b>kevi</b>', '2014-02-13 13:31:18', 'downloadable_student.php'),
(9, 185, 'Add Practice Quiz file', '2014-02-13 13:33:27', 'student_quiz_list.php'),
(10, 167, 'Add Annoucements', '2014-02-13 13:45:59', 'announcements_student.php'),
(11, 0, 'Add Downloadable Materials file name <b>q</b>', '2014-02-21 16:43:38', 'downloadable_student.php'),
(12, 0, 'Add Downloadable Materials file name <b>q</b>', '2014-02-21 16:46:18', 'downloadable_student.php'),
(13, 0, 'Add Downloadable Materials file name <b>q</b>', '2014-02-21 16:46:49', 'downloadable_student.php'),
(14, 0, 'Add Downloadable Materials file name <b>q</b>', '2014-02-21 16:52:30', 'downloadable_student.php'),
(15, 186, 'Add Downloadable Materials file name <b>Sample</b>', '2020-12-21 09:24:50', 'downloadable_student.php'),
(16, 0, 'Add Downloadable Materials file name <b>123</b>', '2020-12-21 09:31:40', 'downloadable_student.php'),
(17, 0, 'Add Downloadable Materials file name <b>234234</b>', '2020-12-21 09:36:27', 'downloadable_student.php'),
(18, 0, 'Add Downloadable Materials file name <b>234234</b>', '2020-12-21 09:38:22', 'downloadable_student.php'),
(19, 186, 'Add Downloadable Materials file name <b>234234</b>', '2020-12-21 09:39:32', 'downloadable_student.php'),
(20, 186, 'Add Downloadable Materials file name <b>234234</b>', '2020-12-21 09:40:28', 'downloadable_student.php'),
(21, 186, 'Add Assignment file name <b>asdasd</b>', '2020-12-21 09:56:48', 'assignment_student.php'),
(22, 186, 'Add Annoucements', '2020-12-21 09:59:00', 'announcements_student.php'),
(23, 186, 'Add Practice Quiz file', '2020-12-21 10:10:11', 'student_quiz_list.php');

-- --------------------------------------------------------

--
-- Table structure for table `notification_read`
--

CREATE TABLE `notification_read` (
  `notification_read_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `student_read` varchar(50) NOT NULL,
  `notification_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notification_read`
--

INSERT INTO `notification_read` (`notification_read_id`, `student_id`, `student_read`, `notification_id`) VALUES
(1, 219, 'yes', 22),
(2, 219, 'yes', 21),
(3, 219, 'yes', 20),
(4, 219, 'yes', 19),
(5, 219, 'yes', 15);

-- --------------------------------------------------------

--
-- Table structure for table `notification_read_teacher`
--

CREATE TABLE `notification_read_teacher` (
  `notification_read_teacher_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `student_read` varchar(100) NOT NULL,
  `notification_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notification_read_teacher`
--

INSERT INTO `notification_read_teacher` (`notification_read_teacher_id`, `teacher_id`, `student_read`, `notification_id`) VALUES
(1, 12, 'yes', 14),
(2, 12, 'yes', 13),
(3, 12, 'yes', 12),
(4, 12, 'yes', 11),
(5, 12, 'yes', 10),
(6, 12, 'yes', 9),
(7, 12, 'yes', 8),
(8, 12, 'yes', 7);

-- --------------------------------------------------------

--
-- Table structure for table `question_type`
--

CREATE TABLE `question_type` (
  `question_type_id` int(11) NOT NULL,
  `question_type` varchar(150) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `question_type`
--

INSERT INTO `question_type` (`question_type_id`, `question_type`) VALUES
(1, 'Multiple Choice'),
(2, 'True or False');

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `quiz_id` int(11) NOT NULL,
  `quiz_title` varchar(50) NOT NULL,
  `quiz_description` varchar(100) NOT NULL,
  `date_added` varchar(100) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`quiz_id`, `quiz_title`, `quiz_description`, `date_added`, `teacher_id`) VALUES
(3, 'Sample Test', 'Test', '2013-12-03 23:01:56', 12),
(4, 'Chapter 1', 'topics', '2013-12-13 01:51:02', 14),
(5, 'test3', '123', '2014-01-16 04:12:07', 12),
(6, 'Sample Quiz', 'Sample 101', '2020-12-21 10:04:11', 9);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_question`
--

CREATE TABLE `quiz_question` (
  `quiz_question_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` varchar(100) NOT NULL,
  `question_type_id` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `date_added` varchar(100) NOT NULL,
  `answer` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `quiz_question`
--

INSERT INTO `quiz_question` (`quiz_question_id`, `quiz_id`, `question_text`, `question_type_id`, `points`, `date_added`, `answer`) VALUES
(33, 5, '<p>q</p>\r\n', 2, 0, '2014-01-17 04:15:03', 'False'),
(34, 3, '<p>Php Stands for ?</p>\r\n', 1, 0, '2014-01-17 12:25:17', 'C'),
(35, 3, '<p>Echo is a Php code that display the output.</p>\r\n', 2, 0, '2014-01-17 12:26:18', 'True'),
(36, 6, '<p>sample</p>\r\n', 1, 0, '2020-12-21 10:05:09', 'A'),
(37, 6, '<p>asdasd</p>\r\n', 2, 0, '2020-12-21 10:05:25', 'True'),
(38, 6, '<p>sdsd</p>\r\n', 2, 0, '2020-12-21 10:05:35', 'False');

-- --------------------------------------------------------

--
-- Table structure for table `school_year`
--

CREATE TABLE `school_year` (
  `school_year_id` int(11) NOT NULL,
  `school_year` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `school_year`
--

INSERT INTO `school_year` (`school_year_id`, `school_year`) VALUES
(2, '2012-2013'),
(3, '2013-2014');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `class_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `firstname`, `lastname`, `class_id`, `username`, `password`, `location`, `status`) VALUES
(113, 'Clifford', 'Ledesma', 13, '21100324', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(112, 'Raymond', 'Serion', 13, '2700372', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(111, 'Mark Dominic', 'Sayon', 13, '21100867', 'heni', 'uploads/mark.jpg', 'Unregistered'),
(108, 'Kaye Angela', 'Cueva', 13, '21101151', '', 'uploads/dp.jpg', 'Unregistered'),
(105, 'Neljie', 'Guirnela', 13, '21101131', '', 'uploads/Koala.jpg', 'Unregistered'),
(106, 'Razel', 'Palermo', 13, '29000676', '', 'uploads/razel.jpg', 'Unregistered'),
(103, 'Jade', 'Gordoncillo', 13, '21100617', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(104, 'Felix Kirby', 'Ubas', 13, '21100277', 'lms10117', 'uploads/kirb.jpg', 'Unregistered'),
(100, 'Jamilah', 'Lonot', 13, '21100303', '', 'uploads/jamila.jpg', 'Unregistered'),
(101, 'Xenia Jane', 'Billones', 13, '21100318', 'sen', 'uploads/xenia.jpg', 'Unregistered'),
(102, 'Carell', 'Catuburan', 13, '21101124', '', 'uploads/carel.jpg', 'Unregistered'),
(97, 'Mary Joy', 'Lambosan', 13, '20101289', '', 'uploads/Desert.jpg', 'Unregistered'),
(98, 'Christine Joy', 'Macaya', 13, '21100579', '', 'uploads/tin.jpg', 'Unregistered'),
(95, 'Ergin Joy', 'Satoc', 13, '21101142', '', 'uploads/ergin.jpg', 'Unregistered'),
(93, 'John Kevin ', 'Lorayna', 7, '111', 'teph', 'uploads/3094_384893504898082_1563225657_n.jpg', 'Registered'),
(94, 'Leah Mae', 'Padilla', 13, '21100471', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(76, 'Jamaica Mae', 'Alipe', 13, '21100555', '123', 'uploads/maica.jpg', 'Registered'),
(107, 'Jose Harry', 'Polondaya', 13, '29001002', 'florypis', 'uploads/harry.jpg', 'Registered'),
(110, 'Zyryn', 'Corugda', 13, '21100881', '', 'uploads/baby.jpg', 'Unregistered'),
(109, 'Rena', 'Lamberto', 13, '29001081', '', 'uploads/ca.jpg', 'Unregistered'),
(99, 'Ryan Teofilo', 'Malbata-an', 13, '21100315', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(96, 'Glecy Marie', 'Navarosa', 13, '20101436', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(209, 'dhalia', 'hofilena', 20, '21300311', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(75, 'Miralyn', 'Pabalate', 13, '21100855', 'em', 'uploads/em2.jpg', 'Unregistered'),
(74, 'Ma. Nonie', 'Mendoza', 13, '21100913', '', 'uploads/nonie.jpg', 'Unregistered'),
(73, 'Stephanie', 'Villanueva', 13, '21101042', 'tephai', 'uploads/3094_384893504898082_1563225657_n.jpg', 'Registered'),
(72, 'Jayvon', 'Pig-ao', 13, '21100547', 'test', 'uploads/von.jpg', 'Unregistered'),
(71, 'Noli', 'Mendoza', 13, '21100556', 'noledel', 'uploads/noli.jpg', 'Registered'),
(134, 'Victor Anthony', 'Jacobo', 12, '21101050', 'akositon', 'uploads/win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', 'Registered'),
(135, 'Albert Kezzel', 'Naynay', 14, '20101361', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(136, 'Jorgielyn', 'Serfino', 7, '20100331', 'jorgie', 'uploads/Koala.jpg', 'Registered'),
(137, 'Wina Mae', 'Espenorio', 8, '20100447', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(138, 'Brian Paul', 'Sablan', 7, '29000557', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(139, 'Rodzil', 'Camato', 7, '20100RC', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(140, 'Dean Martin', 'Tingson', 14, '21100665', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(141, 'Jared Reu', 'Windam', 15, '21100695', 'iloveyoujam', 'uploads/1463666_678111108874417_1795412912_n.jpg', 'Registered'),
(142, 'Lee Ann', 'Vertucio', 12, '21100351', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(143, 'Danica', 'Lamis', 12, '21100396', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(144, 'Neovi', 'Devierte', 12, '21100557', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(145, 'Eril Pio', 'Mercado', 12, '21100291', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(146, 'Johnedel', 'Bauno', 12, '21100411', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(147, 'Jerwin', 'Delos Reyes', 12, '21100369', 'jerwin27 cute', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Registered'),
(148, 'Jendrix', 'Victosa', 12, '21100431', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(149, 'Jebson', 'Tordillos', 12, '21100406', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(150, 'Jethro', 'Pansales', 12, '21101273', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(151, 'Karyl June', 'Bacobo', 12, '21100895', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(152, 'Kristelle Shaine', 'Rubi', 12, '21101063', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(153, 'Richelle', 'Villarmia', 12, '20101392', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(154, 'Mae Ann', 'Panugaling', 12, '21100904', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(155, 'Ma. Roxette', 'Infante', 12, '21100421', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(156, 'Savrena Joy', 'Rael', 12, '2100287', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(157, 'Ace John', 'Casuyon', 12, '21100393', 'DianaraSayon', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Registered'),
(158, 'Rose Mae', 'Pido', 12, '21101195', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(159, 'Mary Ann', 'Panaguiton', 12, '21100701', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(162, 'kimberly kaye', 'salvatierra', 14, '21101182', 'kimzteng', 'uploads/29001002.jpg', 'Registered'),
(210, 'cherylda', 'ohiman', 20, '21300036', 'sawsa', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Registered'),
(164, 'Alit', 'Arvin', 14, '20101605', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(165, 'Ana Mae', 'Alquizar', 14, '21100785', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(166, 'Thessalonica', 'Arroz', 14, '21100651', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(167, 'Leslie', 'Campo', 14, '21100265', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(168, 'Ace', 'Casolino', 14, '27000921', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(169, 'Michael Jed', 'Flores', 14, '21100820', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(172, 'Hennie Rose', 'Laz', 14, '21100805', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(171, 'Joy', 'Macahilig', 14, '21100464', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(173, 'Ma. Nieva', 'Manuel ', 14, '21100711', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(174, 'Devina', 'Navarro', 14, '21100711', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(175, 'Aimee', 'Orlido', 14, '21100654', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(176, 'Mary Grace', 'Quizan', 14, '21100772', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(177, 'John Christopher', 'Reguindin', 14, '21100418', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(178, 'Mary Ann', 'Somosa', 14, '21101150', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(179, 'Marrianne', 'Tumala', 14, '21100710', 'test', 'uploads/win_boot_screen_16_9_by_medi_dadu-d4s7dc1.gif', 'Registered'),
(180, 'Deo Christopher', 'Tribaco', 14, '21101227', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(181, 'Jerson', 'Vargas', 14, '21100819', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(182, 'Valencia', 'Jeralice', 14, '29000405', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(183, 'Cristine', 'Yanson', 14, '21101148', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(184, 'Ariane', 'Alix', 17, '21201166', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(185, 'Mark Arvin', 'Arandilla', 17, '21201453', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(186, 'Ryan Carl', 'Biaquis', 17, '21201244', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(187, 'Ria', 'Bitar', 17, '21201282', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(188, 'Jeremae', 'Bustamante', 17, '21200798', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(189, 'Rhen Mark', 'Callado', 17, '21201012', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(190, 'Ma. Geraldine', 'Carisma', 17, '21201219', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(191, 'Jenny', 'Casapao', 17, '21200855', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(192, 'Welson', 'Castro', 17, '120733', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(193, 'Kimberly Hope', 'Centina', 17, '21201338', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(194, 'Sandra', 'Gomez', 17, '21201335', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(195, 'Dona Jean', 'Guardialao', 17, '21201113', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(196, 'Jeara Mae', 'Guttierrez', 17, '21200782', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(197, 'Mary Joy', 'Jimenez', 17, '21201437', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(198, 'Cyril', 'Lambayong', 17, '21201163', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(199, 'Angelie', 'Lape', 17, '21201356', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(200, 'Jamine', 'Navarosa', 17, '21201115', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(201, 'Allen Joshua', 'Nicor', 17, '21201430', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(202, 'Charis', 'Onate', 17, '21200984', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(203, 'Ikea', 'Padonio', 17, '20100527', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(204, 'Marissa', 'Pasco', 17, '21200935', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(205, 'Kenneth', 'Sayon', 17, '21201268', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(206, 'Mary Grace', 'Morales', 14, '21100293', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(207, 'Danica', 'Delarmente', 14, '21100613', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(208, 'Irish Dawn', 'Belo', 19, '21300413', 'olebirish', 'uploads/Desert.jpg', 'Registered'),
(211, 'val', 'roushen', 7, '201011231', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(212, 'chrystelle Marie', 'Belecina', 15, '21200363', 'chrys', 'uploads/380903_288008981235527_682004916_n.jpg', 'Registered'),
(213, 'kearl joy', 'bartolome', 18, '21300410', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(214, 'marie', 'rojo', 18, '21300375', 'maayeeh', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Registered'),
(215, 'cristine', 'trespuer', 18, '21300258', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(216, 'arian', 'baldostamon', 18, '21300176', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(217, 'Alyssa', 'David', 17, '21200507', '', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Unregistered'),
(218, 'josie', 'banday', 7, '20100452', 'heaven', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Registered'),
(219, 'Claire ', 'Blake', 18, '2011120', 'cblake123', 'uploads/NO-IMAGE-AVAILABLE.jpg', 'Registered');

-- --------------------------------------------------------

--
-- Table structure for table `student_assignment`
--

CREATE TABLE `student_assignment` (
  `student_assignment_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `floc` varchar(100) NOT NULL,
  `assignment_fdatein` varchar(50) NOT NULL,
  `fdesc` varchar(100) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `student_id` int(11) NOT NULL,
  `grade` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `student_assignment`
--

INSERT INTO `student_assignment` (`student_assignment_id`, `assignment_id`, `floc`, `assignment_fdatein`, `fdesc`, `fname`, `student_id`, `grade`) VALUES
(1, 31, 'admin/uploads/7820_File_sample.pdf', '2020-12-21 10:12:04', 'aaa', 'asdasd', 219, '');

-- --------------------------------------------------------

--
-- Table structure for table `student_backpack`
--

CREATE TABLE `student_backpack` (
  `file_id` int(11) NOT NULL,
  `floc` varchar(100) NOT NULL,
  `fdatein` varchar(100) NOT NULL,
  `fdesc` varchar(100) NOT NULL,
  `student_id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `student_backpack`
--

INSERT INTO `student_backpack` (`file_id`, `floc`, `fdatein`, `fdesc`, `student_id`, `fname`) VALUES
(1, 'admin/uploads/2658_File_kevin.docx', '2014-02-13 11:11:50', 'test', 210, 'test'),
(2, 'admin/uploads/9782_File_sample.pdf', '2020-12-21 10:12:54', 'adasd', 219, '234234'),
(3, 'admin/uploads/6898_File_sample.pdf', '2020-12-21 10:12:54', 'adasd', 219, '234234'),
(4, 'admin/uploads/3579_File_sample.pdf', '2020-12-21 10:12:54', 'adasd', 219, '234234');

-- --------------------------------------------------------

--
-- Table structure for table `student_class_quiz`
--

CREATE TABLE `student_class_quiz` (
  `student_class_quiz_id` int(11) NOT NULL,
  `class_quiz_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `student_quiz_time` varchar(100) NOT NULL,
  `grade` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `student_class_quiz`
--

INSERT INTO `student_class_quiz` (`student_class_quiz_id`, `class_quiz_id`, `student_id`, `student_quiz_time`, `grade`) VALUES
(1, 15, 107, '3600', '0 out of 2'),
(2, 16, 136, '3600', '0 out of 0'),
(3, 17, 219, '3600', '1 out of 3');

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(100) NOT NULL,
  `subject_title` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` longtext NOT NULL,
  `unit` int(11) NOT NULL,
  `Pre_req` varchar(100) NOT NULL,
  `semester` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`subject_id`, `subject_code`, `subject_title`, `category`, `description`, `unit`, `Pre_req`, `semester`) VALUES
(14, 'IS 411A', 'Senior Systems Project 1', '', '<p><span style=\"font-size: medium;\"><em>About the Subject</em></span></p>\r\n<p>This subject comprisea topics about systems development, SDLC methodologies, Conceptual Framework, diagrams such as DFD, ERD and Flowchart and writing a thesis proposal.</p>\r\n<p>&nbsp;</p>\r\n<p>The project requirement for this subject are:</p>\r\n<p>Chapters (1-5) Thesis Proposal</p>\r\n<p>100% Running System at the end of semester</p>\r\n<p>&nbsp;</p>', 3, '', ''),
(15, 'IS 412', 'Effective Human Communication for IT Professional', '', '<p><span style=\"font-size: medium;\"><em>About the Subject</em></span></p>\r\n<p>This subject is intended for IT students to develop or enhance communication skills that will be beneficial especially when used in the business industry. The lesson includes Verbal Communication (Written and Oral), Non-verbal Communication, etc.</p>', 3, '', ''),
(16, 'IS 311', 'Programming Languages', '', '<pre class=\"coursera-course-heading\" data-msg=\"coursera-course-about\"><span style=\"font-size: medium;\"><em>About the Subject</em></span></pre>\r\n<div class=\"coursera-course-detail\" data-user-generated=\"data-user-generated\">Learn many of the concepts that underlie all programming languages. Develop a programming style known as functional programming and contrast it with object-oriented programming. Through experience writing programs and studying three different languages, learn the key issues in designing and using programming languages, such as modularity and the complementary benefits of static and dynamic typing. This course is neither particularly theoretical nor just about programming specifics &ndash; it will give you a framework for understanding how to use language constructs effectively and how to design correct and elegant programs. By using different languages, you learn to think more deeply than in terms of the particular syntax of one language. The emphasis on functional programming is essential for learning how to write robust, reusable, composable, and elegant programs &ndash; in any language.</div>\r\n<h2 class=\"coursera-course-detail\" data-user-generated=\"data-user-generated\">&nbsp;</h2>\r\n<pre class=\"coursera-course-detail\" data-user-generated=\"data-user-generated\"><span style=\"font-size: medium;\"><em>&nbsp;Course Syllabus</em></span></pre>\r\n<div class=\"coursera-course-detail\" data-user-generated=\"data-user-generated\">\r\n<ul>\r\n<li>Syntax vs. semantics vs. idioms vs. libraries vs. tools</li>\r\n<li>ML basics (bindings, conditionals, records, functions)</li>\r\n<li>Recursive functions and recursive types</li>\r\n<li>Benefits of no mutation</li>\r\n<li>Algebraic datatypes, pattern matching</li>\r\n<li>Tail recursion</li>\r\n<li>First-class functions and function closures</li>\r\n<li>Lexical scope</li>\r\n<li>Equivalence and effects</li>\r\n<li>Parametric polymorphism and container types</li>\r\n<li>Type inference</li>\r\n<li>Abstract types and modules</li>\r\n<li>Racket basics</li>\r\n<li>Dynamic vs. static typing</li>\r\n<li>Implementing languages, especially higher-order functions</li>\r\n<li>Macro</li>\r\n<li>Ruby basics</li>\r\n<li>Object-oriented programming</li>\r\n<li>Pure object-orientation</li>\r\n<li>Implementing dynamic dispatch</li>\r\n<li>Multiple inheritance, interfaces, and mixins</li>\r\n<li>OOP vs. functional decomposition and extensibility</li>\r\n<li>Subtyping for records, functions, and objects</li>\r\n<li>Subtyping</li>\r\n<li>Class-based subtyping</li>\r\n<li>Subtyping vs. parametric polymorphism; bounded polymorphism</li>\r\n</ul>\r\n</div>', 3, '', ''),
(17, 'IS 413', 'Introduction to the IM Professional and Ethics', '', '<p>This subject discusses about Ethics, E-Commerce, Cybercrime Law, Computer Security, etc.</p>', 0, '', ''),
(22, 'IS 221', 'Application Development', '', '', 3, '', '2nd'),
(23, 'IS 222', 'Network and Internet Technology', '', '', 3, '', '2nd'),
(24, 'IS 223', 'Business Process', '', '', 3, '', '2nd'),
(25, 'IS 224', 'Discrete Structures', '', '', 3, '', '2nd'),
(26, 'IS 227', 'IS Programming 2', '', '', 3, '', '2nd'),
(27, 'SS POL GOV', 'Politics and Governance with Philippine Constitution', '', '', 3, '', '2nd'),
(28, 'LIT 1', 'Philippine  Literature', '', '', 3, '', '2nd'),
(29, 'ACCTG 2', 'Fundamentals of Accounting 2', '', '', 3, '', '2nd'),
(30, 'PE 4', 'Team Sports', '', '', 3, '', '2nd'),
(31, 'IS 302', 'Survey of Programming Languages', '', '', 3, '', '2nd'),
(32, 'IS 303', 'Structured Query Language', '', '', 3, '', '2nd'),
(33, 'IS 321', 'Information System Planning', '', '', 3, '', '2nd'),
(34, 'IS 322', 'Management of Technology', '', '', 3, '', '2nd'),
(35, 'IS 323', 'E-commerce Strategy Architectural', '', '', 3, '', '2nd'),
(36, 'IS 324', 'System Analysis and Design', '', '', 3, '', '2nd'),
(37, 'Law 1', 'Law on Obligation and Contracts', '', '', 3, '', '2nd'),
(38, 'Philo 1', 'Social Philosophy & Logic', '', '', 3, '', '2nd'),
(39, 'MQTB', 'Quantitative Techniques in Business', '', '', 3, '', '2nd'),
(40, 'RIZAL', 'Rizal: Life and Works', '', '<p>COURSE OUTLINE<br />\r\n1. Course Code : RIZAL</p>\r\n\r\n<p>2. Course Title &nbsp;: RIZAL (Rizal Life and Works)<br />\r\n3. Pre-requisite : none<br />\r\n5. Credit/ Class Schedule : 3 units; 3 hrs/week<br />\r\n6. Course Description&nbsp;<br />\r\n1. A critical analysis of Jose Rizal&rsquo;s life and ideas as reflected in his biography, his novels Noli Me Tangere and El Filibusterismo and in his other writings composed of essays and poems to provide the students a value based reference for reacting to certain ideas and behavior.<br />\r\n<br />\r\n<strong>PROGRAM OBJECTIVES</strong><br />\r\n1. To instill in the students human values and cultural refinement through the humanities and social sciences.<br />\r\n2. To inculcate high ethical standards in the students through its integration in the learning activities.<br />\r\n3. To have critical studies and discussions why Rizal is made the national hero of the Philippines.<br />\r\n<br />\r\nTOPICS:&nbsp;<br />\r\n1. A Hero is Born&nbsp;<br />\r\n2. Childhood Days in Calamba<br />\r\n3. School Days in Binan<br />\r\n4. Triumphs in the Ateneo<br />\r\n5. At the UST<br />\r\n6. In Spain<br />\r\n7. Paris to Berlin<br />\r\n8. Noli Me Tangere<br />\r\n9. Elias and Salome<br />\r\n10. Rizal&rsquo;s Tour of Europe with with Viola<br />\r\n11. Back to Calamba<br />\r\n12. HK, Macao and Japan<br />\r\n13. Rizal in Japan<br />\r\n14. Rizal in America<br />\r\n15. Life and Works in London<br />\r\n16. In Gay Paris<br />\r\n17. Rizal in Brussles<br />\r\n18. In Madrid<br />\r\n19. El Filibusterismo<br />\r\n20. In Hong Kong<br />\r\n21. Exile in Dapitan<br />\r\n22. The Trial of Rizal<br />\r\n23. Martyrdom at Bagumbayan<br />\r\n<br />\r\nTextbook and References:<br />\r\n1. Rizal&rsquo;s Life, Works and Writings (The Centennial Edition) by: Gregorio F. Zaide<br />\r\nand Sonia M. Zaide Quezon City, 1988. All Nations Publishing Co.<br />\r\n2. Coates, Austin. Rizal: First Filipino Nationalist and Martyr, Quezon City, UP Press 1999.<br />\r\n3. Constantino, Renato. Veneration Without Understanding. Quezon City, UP Press Inc., 2001.<br />\r\n4. Dela Cruz, W. &amp; Zulueta, M. Rizal: Buhay at Kaisipan. Manila, NBS Publications 2002.<br />\r\n5. Ocampo, Ambeth. Rizal Without the Overcoat (New Edition). Pasig City, anvil Publishing House 2002.<br />\r\n6. Odullo-de Guzman, Maria. Noli Me Tangere and El Filibusterismo. Manila, NBS Publications 1998.<br />\r\n7. Palma, Rafael. Rizal: The Pride of the Malay Race. Manila, Saint Anthony Company 2000.<br />\r\n8.Romero, M.C. &amp; Sta Roman, J. Rizal &amp; the Development of Filipino Consciousness (Third Edition). Manila, JMC Press Inc., 2001.<br />\r\n<br />\r\nCourse Evaluation:<br />\r\n<br />\r\n1. Quizzes : 30 %<br />\r\n2. Exams : 40 %<br />\r\n3. Class Standing : 20 %<br />\r\n- recitation<br />\r\n- attendance<br />\r\n- behavior<br />\r\n4. Final Grade<br />\r\n- 40 % previous grade<br />\r\n- 60 % current grade</p>\r\n', 3, '', '2nd'),
(41, 'IS 411B', 'Senior Systems Project 2', '', '', 3, '', '2nd'),
(42, '1234', 'Sample Subject', '', '<p>Sample Only</p>\r\n', 3, '', '1st');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `department_id` int(11) NOT NULL,
  `location` varchar(200) NOT NULL,
  `about` varchar(500) NOT NULL,
  `teacher_status` varchar(20) NOT NULL,
  `teacher_stat` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `username`, `password`, `firstname`, `lastname`, `department_id`, `location`, `about`, `teacher_status`, `teacher_stat`) VALUES
(9, '1001', 'test', 'Jomar', 'Pabuaya', 4, 'uploads/NO-IMAGE-AVAILABLE.jpg', '', 'Registered', 'Deactivated'),
(5, '1002', 'red', 'Cristine', 'Redoblo', 4, 'uploads/NO-IMAGE-AVAILABLE.jpg', '', '', 'Activated'),
(11, '1003', 'aladin', 'Aladin', 'Cabrera', 4, 'uploads/NO-IMAGE-AVAILABLE.jpg', '', '', 'Activated'),
(13, 'test', 'test', 'Rammel', 'Cadagat', 4, 'uploads/NO-IMAGE-AVAILABLE.jpg', '', '', 'Activated'),
(12, '1000', 'morante', 'Ruby Mae ', 'Morante', 4, 'uploads/NO-IMAGE-AVAILABLE.jpg', '<p style=\"text-align: justify;\">Dan Grossman has taught programming languages at the University of Washington since 2003. During his 10 years as a faculty member, his department&rsquo;s undergraduate students have elected him &ldquo;teacher of the year&rdquo; twice and awarded him second place once. His research, resulting in over 50 peer-reviewed publications, has covered the theory, design, and implementation of programming languages, as well as connections to computer architecture and softwar', '', 'Activated'),
(14, 'honey', 'lee', 'Honeylee', 'Magbanua', 4, 'uploads/NO-IMAGE-AVAILABLE.jpg', '', '', 'Activated'),
(15, 'chaw', 'chaw', 'Charito ', 'Puray', 4, 'uploads/NO-IMAGE-AVAILABLE.jpg', '', '', 'Activated'),
(17, '', '', 'Lovelyn ', 'Layson', 5, 'uploads/NO-IMAGE-AVAILABLE.jpg', '', '', 'Activated'),
(18, 'test123', 'test123', 'Allan', 'Dela Torre', 4, 'uploads/NO-IMAGE-AVAILABLE.jpg', '', 'Registered', 'Activated'),
(19, 'delam', 'denise', 'Denesa', 'Lamique', 4, 'uploads/NO-IMAGE-AVAILABLE.jpg', '', 'Registered', 'Activated');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_backpack`
--

CREATE TABLE `teacher_backpack` (
  `file_id` int(11) NOT NULL,
  `floc` varchar(100) NOT NULL,
  `fdatein` varchar(100) NOT NULL,
  `fdesc` varchar(100) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_class`
--

CREATE TABLE `teacher_class` (
  `teacher_class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `thumbnails` varchar(100) NOT NULL,
  `school_year` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `teacher_class`
--

INSERT INTO `teacher_class` (`teacher_class_id`, `teacher_id`, `class_id`, `subject_id`, `thumbnails`, `school_year`) VALUES
(97, 9, 7, 15, 'admin/uploads/thumbnails.jpg', '2012-2013'),
(135, 0, 22, 29, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(151, 5, 7, 14, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(152, 5, 8, 14, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(153, 5, 13, 36, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(157, 18, 15, 23, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(158, 18, 16, 23, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(159, 18, 12, 23, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(160, 18, 7, 29, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(165, 134, 15, 23, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(167, 12, 13, 35, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(168, 12, 14, 35, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(170, 12, 16, 24, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(172, 18, 13, 39, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(173, 18, 14, 39, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(174, 13, 12, 16, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(175, 13, 13, 16, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(176, 13, 14, 16, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(177, 14, 12, 32, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(178, 14, 13, 32, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(179, 14, 14, 32, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(180, 19, 13, 22, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(181, 12, 20, 24, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(183, 12, 18, 24, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(184, 12, 17, 25, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(185, 12, 7, 22, 'admin/uploads/thumbnails.jpg', '2013-2014'),
(186, 9, 18, 42, 'admin/uploads/thumbnails.jpg', '2013-2014');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_class_announcements`
--

CREATE TABLE `teacher_class_announcements` (
  `teacher_class_announcements_id` int(11) NOT NULL,
  `content` varchar(500) NOT NULL,
  `teacher_id` varchar(100) NOT NULL,
  `teacher_class_id` int(11) NOT NULL,
  `date` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `teacher_class_announcements`
--

INSERT INTO `teacher_class_announcements` (`teacher_class_announcements_id`, `content`, `teacher_id`, `teacher_class_id`, `date`) VALUES
(2, '<p><strong>Project Deadline</strong></p>\r\n\r\n<p>In December 1st week&nbsp; system must fully functioning.</p>\r\n\r\n<p><br />\r\n&nbsp;</p>\r\n', '9', 87, '2013-10-30 13:21:13'),
(21, '<p>fsaf</p>\r\n', '9', 87, '2013-10-30 14:33:21'),
(31, '<p>Hi im kevin i edit this</p>\r\n', '9', 87, '2013-10-30 15:41:56'),
(33, '<p>hello teph</p>\r\n', '9', 95, '2013-10-30 17:44:28'),
(34, '<p>hello guys</p>\r\n', '9', 95, '2013-11-02 10:51:39'),
(35, '<p>dsdasd</p>\r\n', '12', 147, '2013-11-16 13:59:33'),
(36, '<p>BSIS 1A: Submit assignment on November 20, 2013 before 5pm.</p>\r\n', '12', 154, '2013-11-18 15:29:34'),
(37, '<p>aaaaa<br />\r\n&nbsp;</p>\r\n', '12', 167, '2014-01-17 14:36:32'),
(38, '<p>wala klase<img alt=\"sad\" src=\"http://localhost/lms/admin/vendors/ckeditor/plugins/smiley/images/sad_smile.gif\" style=\"height:20px; width:20px\" title=\"sad\" /></p>\r\n', '12', 167, '2014-02-13 13:45:59'),
(39, '<p>Test</p>\r\n', '9', 186, '2020-12-21 09:59:00');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_class_student`
--

CREATE TABLE `teacher_class_student` (
  `teacher_class_student_id` int(11) NOT NULL,
  `teacher_class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `teacher_class_student`
--

INSERT INTO `teacher_class_student` (`teacher_class_student_id`, `teacher_class_id`, `student_id`, `teacher_id`) VALUES
(31, 165, 141, 134),
(32, 165, 134, 134),
(54, 167, 113, 12),
(55, 167, 112, 12),
(57, 167, 108, 12),
(58, 167, 105, 12),
(59, 167, 106, 12),
(60, 167, 103, 12),
(61, 167, 104, 12),
(62, 167, 100, 12),
(63, 167, 101, 12),
(64, 167, 102, 12),
(65, 167, 97, 12),
(66, 167, 98, 12),
(67, 167, 95, 12),
(68, 167, 94, 12),
(69, 167, 76, 12),
(70, 167, 107, 12),
(71, 167, 110, 12),
(72, 167, 109, 12),
(73, 167, 99, 12),
(74, 167, 96, 12),
(75, 167, 75, 12),
(76, 167, 74, 12),
(77, 167, 73, 12),
(78, 167, 72, 12),
(79, 167, 71, 12),
(80, 168, 135, 12),
(81, 168, 140, 12),
(82, 168, 162, 12),
(83, 168, 164, 12),
(84, 168, 165, 12),
(85, 168, 166, 12),
(86, 168, 167, 12),
(87, 168, 168, 12),
(88, 168, 169, 12),
(89, 168, 172, 12),
(90, 168, 171, 12),
(91, 168, 173, 12),
(92, 168, 174, 12),
(93, 168, 175, 12),
(94, 168, 176, 12),
(95, 168, 177, 12),
(96, 168, 178, 12),
(97, 168, 179, 12),
(98, 168, 180, 12),
(99, 168, 181, 12),
(100, 168, 182, 12),
(101, 168, 183, 12),
(102, 168, 206, 12),
(103, 168, 207, 12),
(127, 172, 113, 18),
(128, 172, 112, 18),
(129, 172, 111, 18),
(130, 172, 108, 18),
(131, 172, 105, 18),
(132, 172, 106, 18),
(133, 172, 103, 18),
(134, 172, 104, 18),
(135, 172, 100, 18),
(136, 172, 101, 18),
(137, 172, 102, 18),
(138, 172, 97, 18),
(139, 172, 98, 18),
(140, 172, 95, 18),
(141, 172, 94, 18),
(142, 172, 76, 18),
(143, 172, 107, 18),
(144, 172, 110, 18),
(145, 172, 109, 18),
(146, 172, 99, 18),
(147, 172, 96, 18),
(148, 172, 75, 18),
(149, 172, 74, 18),
(150, 172, 73, 18),
(151, 172, 72, 18),
(152, 172, 71, 18),
(153, 173, 135, 18),
(154, 173, 140, 18),
(155, 173, 162, 18),
(156, 173, 164, 18),
(157, 173, 165, 18),
(158, 173, 166, 18),
(159, 173, 167, 18),
(160, 173, 168, 18),
(161, 173, 169, 18),
(162, 173, 172, 18),
(163, 173, 171, 18),
(164, 173, 173, 18),
(165, 173, 174, 18),
(166, 173, 175, 18),
(167, 173, 176, 18),
(168, 173, 177, 18),
(169, 173, 178, 18),
(170, 173, 179, 18),
(171, 173, 180, 18),
(172, 173, 181, 18),
(173, 173, 182, 18),
(174, 173, 183, 18),
(175, 173, 206, 18),
(176, 173, 207, 18),
(177, 174, 134, 13),
(178, 174, 142, 13),
(179, 174, 143, 13),
(180, 174, 144, 13),
(181, 174, 145, 13),
(182, 174, 146, 13),
(183, 174, 147, 13),
(184, 174, 148, 13),
(185, 174, 149, 13),
(186, 174, 150, 13),
(187, 174, 151, 13),
(188, 174, 152, 13),
(189, 174, 153, 13),
(190, 174, 154, 13),
(191, 174, 155, 13),
(192, 174, 156, 13),
(193, 174, 157, 13),
(194, 174, 158, 13),
(195, 174, 159, 13),
(196, 175, 113, 13),
(197, 175, 112, 13),
(198, 175, 111, 13),
(199, 175, 108, 13),
(200, 175, 105, 13),
(201, 175, 106, 13),
(202, 175, 103, 13),
(203, 175, 104, 13),
(204, 175, 100, 13),
(205, 175, 101, 13),
(206, 175, 102, 13),
(207, 175, 97, 13),
(208, 175, 98, 13),
(209, 175, 95, 13),
(210, 175, 94, 13),
(211, 175, 76, 13),
(212, 175, 107, 13),
(213, 175, 110, 13),
(214, 175, 109, 13),
(215, 175, 99, 13),
(216, 175, 96, 13),
(217, 175, 75, 13),
(218, 175, 74, 13),
(219, 175, 73, 13),
(220, 175, 72, 13),
(221, 175, 71, 13),
(222, 176, 135, 13),
(223, 176, 140, 13),
(224, 176, 162, 13),
(225, 176, 164, 13),
(226, 176, 165, 13),
(227, 176, 166, 13),
(228, 176, 167, 13),
(229, 176, 168, 13),
(230, 176, 169, 13),
(231, 176, 172, 13),
(232, 176, 171, 13),
(233, 176, 173, 13),
(234, 176, 174, 13),
(235, 176, 175, 13),
(236, 176, 176, 13),
(237, 176, 177, 13),
(238, 176, 178, 13),
(239, 176, 179, 13),
(240, 176, 180, 13),
(241, 176, 181, 13),
(242, 176, 182, 13),
(243, 176, 183, 13),
(244, 176, 206, 13),
(245, 176, 207, 13),
(246, 177, 134, 14),
(247, 177, 142, 14),
(248, 177, 143, 14),
(249, 177, 144, 14),
(250, 177, 145, 14),
(251, 177, 146, 14),
(252, 177, 147, 14),
(253, 177, 148, 14),
(254, 177, 149, 14),
(255, 177, 150, 14),
(256, 177, 151, 14),
(257, 177, 152, 14),
(258, 177, 153, 14),
(259, 177, 154, 14),
(260, 177, 155, 14),
(261, 177, 156, 14),
(262, 177, 157, 14),
(263, 177, 158, 14),
(264, 177, 159, 14),
(265, 178, 113, 14),
(266, 178, 112, 14),
(267, 178, 111, 14),
(268, 178, 108, 14),
(269, 178, 105, 14),
(270, 178, 106, 14),
(271, 178, 103, 14),
(272, 178, 104, 14),
(273, 178, 100, 14),
(274, 178, 101, 14),
(275, 178, 102, 14),
(276, 178, 97, 14),
(277, 178, 98, 14),
(278, 178, 95, 14),
(279, 178, 94, 14),
(280, 178, 76, 14),
(281, 178, 107, 14),
(282, 178, 110, 14),
(283, 178, 109, 14),
(284, 178, 99, 14),
(285, 178, 96, 14),
(286, 178, 75, 14),
(287, 178, 74, 14),
(288, 178, 73, 14),
(289, 178, 72, 14),
(290, 178, 71, 14),
(291, 179, 135, 14),
(292, 179, 140, 14),
(293, 179, 162, 14),
(294, 179, 164, 14),
(295, 179, 165, 14),
(296, 179, 166, 14),
(297, 179, 167, 14),
(298, 179, 168, 14),
(299, 179, 169, 14),
(300, 179, 172, 14),
(301, 179, 171, 14),
(302, 179, 173, 14),
(303, 179, 174, 14),
(304, 179, 175, 14),
(305, 179, 176, 14),
(306, 179, 177, 14),
(307, 179, 178, 14),
(308, 179, 179, 14),
(309, 179, 180, 14),
(310, 179, 181, 14),
(311, 179, 182, 14),
(312, 179, 183, 14),
(313, 179, 206, 14),
(314, 179, 207, 14),
(315, 180, 113, 19),
(316, 180, 112, 19),
(317, 180, 111, 19),
(318, 180, 108, 19),
(319, 180, 105, 19),
(320, 180, 106, 19),
(321, 180, 103, 19),
(322, 180, 104, 19),
(323, 180, 100, 19),
(324, 180, 101, 19),
(325, 180, 102, 19),
(326, 180, 97, 19),
(327, 180, 98, 19),
(328, 180, 95, 19),
(329, 180, 94, 19),
(330, 180, 76, 19),
(331, 180, 107, 19),
(332, 180, 110, 19),
(333, 180, 109, 19),
(334, 180, 99, 19),
(335, 180, 96, 19),
(336, 180, 75, 19),
(337, 180, 74, 19),
(338, 180, 73, 19),
(339, 180, 72, 19),
(340, 180, 71, 19),
(341, 181, 209, 12),
(342, 181, 210, 12),
(345, 183, 213, 12),
(346, 183, 214, 12),
(347, 183, 215, 12),
(348, 183, 216, 12),
(349, 184, 184, 12),
(350, 184, 185, 12),
(351, 184, 186, 12),
(352, 184, 187, 12),
(353, 184, 188, 12),
(354, 184, 189, 12),
(355, 184, 190, 12),
(356, 184, 191, 12),
(358, 184, 193, 12),
(359, 184, 194, 12),
(360, 184, 195, 12),
(361, 184, 196, 12),
(362, 184, 197, 12),
(363, 184, 198, 12),
(364, 184, 199, 12),
(365, 184, 200, 12),
(366, 184, 201, 12),
(367, 184, 202, 12),
(368, 184, 203, 12),
(369, 184, 204, 12),
(370, 184, 205, 12),
(371, 184, 217, 12),
(372, 184, 192, 12),
(373, 185, 93, 12),
(374, 185, 136, 12),
(375, 185, 138, 12),
(376, 185, 139, 12),
(377, 185, 211, 12),
(378, 186, 213, 9),
(379, 186, 214, 9),
(380, 186, 215, 9),
(381, 186, 216, 9),
(382, 186, 219, 9);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_notification`
--

CREATE TABLE `teacher_notification` (
  `teacher_notification_id` int(11) NOT NULL,
  `teacher_class_id` int(11) NOT NULL,
  `notification` varchar(100) NOT NULL,
  `date_of_notification` varchar(100) NOT NULL,
  `link` varchar(100) NOT NULL,
  `student_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `teacher_notification`
--

INSERT INTO `teacher_notification` (`teacher_notification_id`, `teacher_class_id`, `notification`, `date_of_notification`, `link`, `student_id`, `assignment_id`) VALUES
(15, 160, 'Submit Assignment file name <b>my_assginment</b>', '2013-11-25 10:39:52', 'view_submit_assignment.php', 93, 16),
(17, 161, 'Submit Assignment file name <b>q</b>', '2013-11-25 15:54:19', 'view_submit_assignment.php', 71, 17),
(18, 186, 'Submit Assignment file name <b>asdasd</b>', '2020-12-21 10:12:04', 'view_submit_assignment.php', 219, 31);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_shared`
--

CREATE TABLE `teacher_shared` (
  `teacher_shared_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `shared_teacher_id` int(11) NOT NULL,
  `floc` varchar(100) NOT NULL,
  `fdatein` varchar(100) NOT NULL,
  `fdesc` varchar(100) NOT NULL,
  `fname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `teacher_shared`
--

INSERT INTO `teacher_shared` (`teacher_shared_id`, `teacher_id`, `shared_teacher_id`, `floc`, `fdatein`, `fdesc`, `fname`) VALUES
(1, 12, 14, 'admin/uploads/7939_File_449E26DB.jpg', '2014-02-20 09:55:32', 'sas', 'sss');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_log`
--

CREATE TABLE `user_log` (
  `user_log_id` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `login_date` varchar(30) NOT NULL,
  `logout_date` varchar(30) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_log`
--

INSERT INTO `user_log` (`user_log_id`, `username`, `login_date`, `logout_date`, `user_id`) VALUES
(1, 'admin', '2013-11-01 11:57:33', '2013-11-18 10:33:54', 10),
(2, 'admin', '2013-11-05 09:52:09', '2013-11-18 10:33:54', 10),
(3, 'admin', '2013-11-08 10:41:09', '2013-11-18 10:33:54', 10),
(4, 'admin', '2013-11-12 22:53:05', '2013-11-18 10:33:54', 10),
(5, 'admin', '2013-11-13 07:07:04', '2013-11-18 10:33:54', 10),
(6, 'admin', '2013-11-13 13:07:58', '2013-11-18 10:33:54', 10),
(7, 'admin', '2013-11-13 13:30:45', '2013-11-18 10:33:54', 10),
(8, 'admin', '2013-11-13 15:25:20', '2013-11-18 10:33:54', 10),
(9, 'admin', '2013-11-13 15:46:28', '2013-11-18 10:33:54', 10),
(10, 'admin', '2013-11-13 16:04:10', '2013-11-18 10:33:54', 10),
(11, 'admin', '2013-11-13 17:31:37', '2013-11-18 10:33:54', 10),
(12, 'admin', '2013-11-13 22:47:45', '2013-11-18 10:33:54', 10),
(13, 'admin', '2013-11-14 10:27:06', '2013-11-18 10:33:54', 10),
(14, 'admin', '2013-11-14 10:27:55', '2013-11-18 10:33:54', 10),
(15, 'admin', '2013-11-14 10:38:08', '2013-11-18 10:33:54', 10),
(16, 'admin', '2013-11-14 10:38:09', '2013-11-18 10:33:54', 10),
(17, 'admin', '2013-11-14 10:41:06', '2013-11-18 10:33:54', 10),
(18, 'admin', '2013-11-14 11:44:08', '2013-11-18 10:33:54', 10),
(19, 'admin', '2013-11-14 21:53:56', '2013-11-18 10:33:54', 10),
(20, 'admin', '2013-11-14 22:03:53', '2013-11-18 10:33:54', 10),
(21, 'admin', '2013-11-16 13:40:56', '2013-11-18 10:33:54', 10),
(22, 'admin', '2013-11-18 10:22:07', '2013-11-18 10:33:54', 10),
(23, 'jkev', '2013-11-18 10:33:59', '2014-02-13 11:19:36', 14),
(24, 'jkev', '2013-11-18 15:20:45', '2014-02-13 11:19:36', 14),
(25, 'jkev', '2013-11-18 15:42:04', '2014-02-13 11:19:36', 14),
(26, 'jkev', '2013-11-18 16:30:14', '2014-02-13 11:19:36', 14),
(27, 'jkev', '2013-11-18 16:36:44', '2014-02-13 11:19:36', 14),
(28, 'jkev', '2013-11-18 17:39:55', '2014-02-13 11:19:36', 14),
(29, 'jkev', '2013-11-18 20:06:49', '2014-02-13 11:19:36', 14),
(30, 'jkev', '2013-11-23 08:04:27', '2014-02-13 11:19:36', 14),
(31, 'teph', '2013-11-23 12:02:27', '2013-11-30 21:33:02', 13),
(32, 'teph', '2013-11-24 08:55:55', '2013-11-30 21:33:02', 13),
(33, 'jkev', '2013-11-25 10:32:16', '2014-02-13 11:19:36', 14),
(34, 'jkev', '2013-11-25 14:33:05', '2014-02-13 11:19:36', 14),
(35, 'jkev', '2013-11-25 15:02:47', '2014-02-13 11:19:36', 14),
(36, 'jkev', '2013-11-25 21:08:19', '2014-02-13 11:19:36', 14),
(37, 'jkev', '2013-11-25 23:49:58', '2014-02-13 11:19:36', 14),
(38, 'jkev', '2013-11-26 00:32:22', '2014-02-13 11:19:36', 14),
(39, 'jkev', '2013-11-26 10:39:52', '2014-02-13 11:19:36', 14),
(40, 'jkev', '2013-11-26 21:48:05', '2014-02-13 11:19:36', 14),
(41, 'jkev', '2013-11-28 23:00:00', '2014-02-13 11:19:36', 14),
(42, 'jkev', '2013-11-28 23:00:06', '2014-02-13 11:19:36', 14),
(43, 'jkev', '2013-11-30 21:28:54', '2014-02-13 11:19:36', 14),
(44, 'teph', '2013-11-30 21:32:54', '2013-11-30 21:33:02', 13),
(45, 'jkev', '2013-12-04 12:45:09', '2014-02-13 11:19:36', 14),
(46, 'teph', '2013-12-04 14:02:19', '', 13),
(47, 'jkev', '2013-12-11 11:56:15', '2014-02-13 11:19:36', 14),
(48, 'jkev', '2013-12-11 12:04:44', '2014-02-13 11:19:36', 14),
(49, 'jkev', '2013-12-12 09:44:34', '2014-02-13 11:19:36', 14),
(50, 'jkev', '2013-12-13 01:48:23', '2014-02-13 11:19:36', 14),
(51, 'jkev', '2013-12-27 09:13:20', '2014-02-13 11:19:36', 14),
(52, 'jkev', '2013-12-27 10:18:38', '2014-02-13 11:19:36', 14),
(53, 'jkev', '2013-12-27 10:35:43', '2014-02-13 11:19:36', 14),
(54, 'jkev', '2013-12-27 11:08:54', '2014-02-13 11:19:36', 14),
(55, 'jkev', '2013-12-27 11:20:25', '2014-02-13 11:19:36', 14),
(56, 'jkev', '2013-12-27 11:41:58', '2014-02-13 11:19:36', 14),
(57, 'jkev', '2013-12-27 11:43:10', '2014-02-13 11:19:36', 14),
(58, 'jkev', '2013-12-27 14:54:57', '2014-02-13 11:19:36', 14),
(59, 'jkev', '2014-01-12 20:08:26', '2014-02-13 11:19:36', 14),
(60, 'jkev', '2014-01-13 15:24:07', '2014-02-13 11:19:36', 14),
(61, 'jkev', '2014-01-13 18:46:08', '2014-02-13 11:19:36', 14),
(62, 'jkev', '2014-01-15 20:40:15', '2014-02-13 11:19:36', 14),
(63, 'jkev', '2014-01-16 14:42:02', '2014-02-13 11:19:36', 14),
(64, 'jkev', '2014-01-17 09:16:17', '2014-02-13 11:19:36', 14),
(65, 'jkev', '2014-01-17 13:25:51', '2014-02-13 11:19:36', 14),
(66, 'admin', '2014-01-17 14:41:30', '2020-12-21 08:48:16', 15),
(67, 'admin', '2014-01-17 15:56:32', '2020-12-21 08:48:16', 15),
(68, 'admin', '2014-01-26 17:45:31', '2020-12-21 08:48:16', 15),
(69, 'admin', '2014-02-13 10:45:17', '2020-12-21 08:48:16', 15),
(70, 'admin', '2014-02-13 11:05:27', '2020-12-21 08:48:16', 15),
(71, 'jkev', '2014-02-13 11:16:48', '2014-02-13 11:19:36', 14),
(72, 'admin', '2014-02-13 11:55:36', '2020-12-21 08:48:16', 15),
(73, 'admin', '2014-02-13 12:32:38', '2020-12-21 08:48:16', 15),
(74, 'admin', '2014-02-13 12:52:05', '2020-12-21 08:48:16', 15),
(75, 'admin', '2014-02-13 13:04:35', '2020-12-21 08:48:16', 15),
(76, 'jkev', '2014-02-13 14:35:27', '', 14),
(77, 'admin', '2014-02-20 09:40:39', '2020-12-21 08:48:16', 15),
(78, 'admin', '2014-02-20 09:42:21', '2020-12-21 08:48:16', 15),
(79, 'admin', '2014-02-27 22:40:15', '2020-12-21 08:48:16', 15),
(80, 'admin', '2014-02-28 13:12:52', '2020-12-21 08:48:16', 15),
(81, 'admin', '2014-04-02 17:27:47', '2020-12-21 08:48:16', 15),
(82, 'admin', '2014-04-03 15:29:38', '2020-12-21 08:48:16', 15),
(83, 'admin', '2014-06-15 12:31:51', '2020-12-21 08:48:16', 15),
(84, 'Admin', '2020-12-21 08:32:51', '2020-12-21 08:48:16', 15),
(85, 'admin', '2020-12-21 08:48:23', '', 15),
(86, 'admin', '2025-04-03 19:35:23', '', 15),
(87, 'admin', '2025-04-03 19:35:25', '', 15),
(88, 'admin', '2025-04-03 19:37:20', '', 15),
(89, 'admin', '2025-04-03 19:39:04', '', 15),
(90, 'admin', '2025-04-03 19:39:06', '', 15),
(91, 'admin', '2025-04-03 19:39:26', '', 15),
(92, 'admin', '2025-04-03 19:39:28', '', 15),
(93, 'admin', '2025-04-03 19:40:03', '', 15),
(94, 'admin', '2025-04-03 19:41:03', '', 15),
(95, 'admin', '2025-04-03 19:42:15', '', 15),
(96, 'admin', '2025-04-03 19:50:32', '', 15);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`activity_log_id`);

--
-- Indexes for table `answer`
--
ALTER TABLE `answer`
  ADD PRIMARY KEY (`answer_id`);

--
-- Indexes for table `assignment`
--
ALTER TABLE `assignment`
  ADD PRIMARY KEY (`assignment_id`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `class_quiz`
--
ALTER TABLE `class_quiz`
  ADD PRIMARY KEY (`class_quiz_id`);

--
-- Indexes for table `class_subject_overview`
--
ALTER TABLE `class_subject_overview`
  ADD PRIMARY KEY (`class_subject_overview_id`);

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`content_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`file_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `message_sent`
--
ALTER TABLE `message_sent`
  ADD PRIMARY KEY (`message_sent_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `notification_read`
--
ALTER TABLE `notification_read`
  ADD PRIMARY KEY (`notification_read_id`);

--
-- Indexes for table `notification_read_teacher`
--
ALTER TABLE `notification_read_teacher`
  ADD PRIMARY KEY (`notification_read_teacher_id`);

--
-- Indexes for table `question_type`
--
ALTER TABLE `question_type`
  ADD PRIMARY KEY (`question_type_id`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`quiz_id`);

--
-- Indexes for table `quiz_question`
--
ALTER TABLE `quiz_question`
  ADD PRIMARY KEY (`quiz_question_id`);

--
-- Indexes for table `school_year`
--
ALTER TABLE `school_year`
  ADD PRIMARY KEY (`school_year_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `student_assignment`
--
ALTER TABLE `student_assignment`
  ADD PRIMARY KEY (`student_assignment_id`);

--
-- Indexes for table `student_backpack`
--
ALTER TABLE `student_backpack`
  ADD PRIMARY KEY (`file_id`);

--
-- Indexes for table `student_class_quiz`
--
ALTER TABLE `student_class_quiz`
  ADD PRIMARY KEY (`student_class_quiz_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `teacher_backpack`
--
ALTER TABLE `teacher_backpack`
  ADD PRIMARY KEY (`file_id`);

--
-- Indexes for table `teacher_class`
--
ALTER TABLE `teacher_class`
  ADD PRIMARY KEY (`teacher_class_id`);

--
-- Indexes for table `teacher_class_announcements`
--
ALTER TABLE `teacher_class_announcements`
  ADD PRIMARY KEY (`teacher_class_announcements_id`);

--
-- Indexes for table `teacher_class_student`
--
ALTER TABLE `teacher_class_student`
  ADD PRIMARY KEY (`teacher_class_student_id`);

--
-- Indexes for table `teacher_notification`
--
ALTER TABLE `teacher_notification`
  ADD PRIMARY KEY (`teacher_notification_id`);

--
-- Indexes for table `teacher_shared`
--
ALTER TABLE `teacher_shared`
  ADD PRIMARY KEY (`teacher_shared_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_log`
--
ALTER TABLE `user_log`
  ADD PRIMARY KEY (`user_log_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `activity_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `answer`
--
ALTER TABLE `answer`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `assignment`
--
ALTER TABLE `assignment`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `class_quiz`
--
ALTER TABLE `class_quiz`
  MODIFY `class_quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `class_subject_overview`
--
ALTER TABLE `class_subject_overview`
  MODIFY `class_subject_overview_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `message_sent`
--
ALTER TABLE `message_sent`
  MODIFY `message_sent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `notification_read`
--
ALTER TABLE `notification_read`
  MODIFY `notification_read_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notification_read_teacher`
--
ALTER TABLE `notification_read_teacher`
  MODIFY `notification_read_teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `quiz_question`
--
ALTER TABLE `quiz_question`
  MODIFY `quiz_question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `school_year`
--
ALTER TABLE `school_year`
  MODIFY `school_year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT for table `student_assignment`
--
ALTER TABLE `student_assignment`
  MODIFY `student_assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_backpack`
--
ALTER TABLE `student_backpack`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student_class_quiz`
--
ALTER TABLE `student_class_quiz`
  MODIFY `student_class_quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `teacher_backpack`
--
ALTER TABLE `teacher_backpack`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teacher_class`
--
ALTER TABLE `teacher_class`
  MODIFY `teacher_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT for table `teacher_class_announcements`
--
ALTER TABLE `teacher_class_announcements`
  MODIFY `teacher_class_announcements_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `teacher_class_student`
--
ALTER TABLE `teacher_class_student`
  MODIFY `teacher_class_student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=383;

--
-- AUTO_INCREMENT for table `teacher_notification`
--
ALTER TABLE `teacher_notification`
  MODIFY `teacher_notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `teacher_shared`
--
ALTER TABLE `teacher_shared`
  MODIFY `teacher_shared_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_log`
--
ALTER TABLE `user_log`
  MODIFY `user_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;
--
-- Database: `dms`
--
CREATE DATABASE IF NOT EXISTS `dms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `dms`;

-- --------------------------------------------------------

--
-- Table structure for table `facilities_problem`
--

CREATE TABLE `facilities_problem` (
  `Room_Number` int(5) NOT NULL,
  `Vacant_Seat` int(3) NOT NULL,
  `Damaged_Fan` int(3) NOT NULL,
  `Damaged_Light` int(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `facilities_problem`
--

INSERT INTO `facilities_problem` (`Room_Number`, `Vacant_Seat`, `Damaged_Fan`, `Damaged_Light`) VALUES
(101, 1, 2, 1),
(102, 2, 3, 1),
(101, 1, 2, 1),
(102, 2, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `facility_problem`
--

CREATE TABLE `facility_problem` (
  `Room_Number` int(11) NOT NULL,
  `Damaged_Fan_Un` int(11) NOT NULL,
  `Damaged_Fan_Pro` int(11) NOT NULL,
  `Damaged_Fan_Sol` int(11) NOT NULL,
  `Damaged_Light_Un` int(11) NOT NULL,
  `Damaged_Light_Pro` int(11) NOT NULL,
  `Damaged_Light_Sol` int(11) NOT NULL,
  `Modified_Date` date NOT NULL,
  `total_uv` int(20) DEFAULT NULL,
  `total_pro` int(20) DEFAULT NULL,
  `total_sol` int(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `facility_problem`
--

INSERT INTO `facility_problem` (`Room_Number`, `Damaged_Fan_Un`, `Damaged_Fan_Pro`, `Damaged_Fan_Sol`, `Damaged_Light_Un`, `Damaged_Light_Pro`, `Damaged_Light_Sol`, `Modified_Date`, `total_uv`, `total_pro`, `total_sol`) VALUES
(101, -3, 5, 4, 5, 34, 4, '2022-01-04', NULL, NULL, NULL),
(234, 1, 2, 3, 4, 5, 6, '2022-01-26', NULL, NULL, NULL),
(321, 3, 2, 5, 1, 3, 2, '2022-01-26', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `floor`
--

CREATE TABLE `floor` (
  `Floor_Number` varchar(10) NOT NULL,
  `Block` varchar(10) NOT NULL,
  `Num_of_Kitchen` int(5) NOT NULL,
  `Num_of_Room` int(5) NOT NULL,
  `Num_of_Washroom` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `floor`
--

INSERT INTO `floor` (`Floor_Number`, `Block`, `Num_of_Kitchen`, `Num_of_Room`, `Num_of_Washroom`) VALUES
('1/A', 'A', 4, 100, 20),
('2/B', 'B', 6, 150, 30),
('1/B', 'B', 43, 321, 1),
('4', 'A', 2, 40, 3),
('3', 'B', 3, 20, 5),
('1', 'A', 3, 22, 2),
('6', 'B', 7, 43, 2);

-- --------------------------------------------------------

--
-- Table structure for table `hall`
--

CREATE TABLE `hall` (
  `H_ID` int(11) NOT NULL,
  `H_Name` varchar(250) NOT NULL,
  `T_Seat` int(11) NOT NULL,
  `A_Seat` int(11) NOT NULL,
  `N_Student` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hall`
--

INSERT INTO `hall` (`H_ID`, `H_Name`, `T_Seat`, `A_Seat`, `N_Student`) VALUES
(1, 'Mir Mosharraf Hossain Hall', 800, 50, 750),
(2, 'Shaheed Salam-Barkat Hall', 400, 0, 400),
(3, 'Bangabandhu Sheikh Mujibur Rahman Hall', 500, 10, 490),
(4, 'Al Beruni Hall', 400, 50, 350),
(5, 'Shaheed Rafiq-Jabbar Hall', 500, 0, 500),
(6, 'A F M Kamaluddin Hall', 400, 100, 300),
(7, 'Mowlana Bhashani Hall', 600, 200, 400),
(8, 'Bishwakabi Rabindranath Tagore Hall', 400, 0, 400),
(9, 'Jahanara Imam Hall', 200, 50, 150),
(10, 'Nawab Faizunnesa Hall', 100, 20, 80),
(11, 'Pritilata Hall', 300, 60, 240),
(12, 'Fazilatunnesa Hall', 150, 50, 100),
(13, 'Begum Khaleda Zia Hall', 400, 100, 300),
(14, 'Sheikh Hasina Hall', 500, 100, 400),
(15, 'Bangamata Begum Fazilatunnessa Mujib Hall', 600, 100, 500),
(16, 'Begum Sufia Kamal Hall', 400, 20, 380);

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `message_table`
--

CREATE TABLE `message_table` (
  `Stu_ID` int(20) DEFAULT NULL,
  `Name` varchar(20) NOT NULL,
  `Room_Num` varchar(20) DEFAULT NULL,
  `Messages` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `message_table`
--

INSERT INTO `message_table` (`Stu_ID`, `Name`, `Room_Num`, `Messages`) VALUES
(2013, 'Shakil Ahmed', '125/A', 'problem a fan and light'),
(2090, 'Amit Azim', '125/A', 'Door problem'),
(2090, 'Amit Azim', '125/A', 'Door problem');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `id` int(11) NOT NULL,
  `type` varchar(250) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `name`, `id`, `type`, `quantity`) VALUES
(1, 'Md. Shakil Hossain ', 2023, 'breakfast', 1),
(5, 'Zamshed Ikbal', 1997, 'dinner', 2),
(6, 'Mahbubur Rahman', 2024, 'launch', 1);

-- --------------------------------------------------------

--
-- Table structure for table `provost`
--

CREATE TABLE `provost` (
  `P_ID` int(10) NOT NULL,
  `Phone` varchar(15) NOT NULL,
  `Name` varchar(30) NOT NULL,
  `Address` varchar(50) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `Designation` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `provost`
--

INSERT INTO `provost` (`P_ID`, `Phone`, `Name`, `Address`, `Email`, `Designation`) VALUES
(500001, '0169696969', 'Muzibur Rahman', 'Islampur Thana More, Islampur, Jamalpur', 'muzibur@juniv.edu', 'Professor');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `Room_Number` int(20) NOT NULL,
  `Num_of_Table` int(15) NOT NULL,
  `Num_of_Bed` int(5) NOT NULL,
  `Floor_Number` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`Room_Number`, `Num_of_Table`, `Num_of_Bed`, `Floor_Number`) VALUES
(101, 4, 2, '4/A'),
(102, 3, 2, '4/B'),
(103, 2, 4, '1/B'),
(104, 4, 3, '2/A'),
(105, 4, 4, '5/B'),
(504, 3, 2, '5/B'),
(234, 4, 4, '2/A'),
(320, 1, 4, '3/A'),
(410, 3, 4, '4/B');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `S_ID` int(10) NOT NULL,
  `Name` varchar(40) NOT NULL,
  `Address` varchar(50) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `Designation` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`S_ID`, `Name`, `Address`, `Email`, `Designation`) VALUES
(102, 'Shohid', 'Ju', 'example@gmail.com', 'Staff'),
(291, 'Shohel Rana', 'JU, Savar, Dhaka', 'sohel@gmail.com', 'Officer');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `Stu_id` int(10) NOT NULL,
  `Name` varchar(40) NOT NULL,
  `Department` varchar(30) NOT NULL,
  `Session` varchar(20) NOT NULL,
  `hall` varchar(250) NOT NULL,
  `Room_Number` int(10) NOT NULL,
  `Floor_Number` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`Stu_id`, `Name`, `Department`, `Session`, `hall`, `Room_Number`, `Floor_Number`) VALUES
(2023, 'Md. Shakil Hossain', 'IIT', '2018-2019', 'RTH', 406, '4/B'),
(1234, 'Md. Solaiman Ali', 'IR', '2019-2020', 'RTH', 501, '5/B'),
(2024, 'Mahbubur Rahman', 'IIT', '2018-2019', 'RTH', 104, '1/B'),
(2022, 'Ashfaqur Rahman Tokee', 'IIT', '2018-2019', 'SRJ', 234, '2/A'),
(2026, 'Mahfuz', 'CSE', '2018-2019', 'AFH', 309, '3/A'),
(2028, 'Nahidul Islam', 'IIT', '2018-2019', 'RTH', 104, '1/B');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `facility_problem`
--
ALTER TABLE `facility_problem`
  ADD PRIMARY KEY (`Room_Number`);

--
-- Indexes for table `floor`
--
ALTER TABLE `floor`
  ADD PRIMARY KEY (`Floor_Number`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `provost`
--
ALTER TABLE `provost`
  ADD PRIMARY KEY (`P_ID`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`Room_Number`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`S_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2025-09-09 02:47:51', '{\"Console\\/Mode\":\"collapse\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `student_services_db`
--
CREATE DATABASE IF NOT EXISTS `student_services_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `student_services_db`;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `date_posted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `description`, `image`, `date_posted`) VALUES
(34, 'No Title', '', '1.png', '2025-04-04 06:24:44'),
(35, 'No Title', '', '2.jpg', '2025-04-04 06:24:52'),
(36, 'No Title', '', '3.png', '2025-04-04 06:25:00'),
(37, 'No Title', '', 'Screenshot 2025-02-03 174320.png', '2025-04-07 19:17:00');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('pending','approved','completed') DEFAULT 'pending',
  `reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `student_id`, `user_id`, `appointment_date`, `status`, `reason`, `created_at`, `updated_at`, `admin_message`) VALUES
(53, 'GAB2022-1111', 'Guidance01', '2025-09-10 08:39:00', 'completed', 'ASAP', '2025-09-06 08:43:33', '2025-09-06 10:10:34', '');

-- --------------------------------------------------------

--
-- Table structure for table `appointment_logs`
--

CREATE TABLE `appointment_logs` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `actor_id` varchar(64) NOT NULL,
  `action` varchar(64) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_logs`
--

INSERT INTO `appointment_logs` (`id`, `appointment_id`, `actor_id`, `action`, `details`, `created_at`) VALUES
(1, 53, 'Guidance01', 'status_update', '{\"status\":\"completed\",\"message\":\"\"}', '2025-09-06 18:10:34'),
(2, 53, 'Guidance01', 'status_update', '{\"status\":\"completed\",\"message\":\"\"}', '2025-09-06 18:10:39');

-- --------------------------------------------------------

--
-- Table structure for table `calendar_blocks`
--

CREATE TABLE `calendar_blocks` (
  `id` int(11) NOT NULL,
  `counselor_id` varchar(64) NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `type` enum('available','blocked') NOT NULL DEFAULT 'available',
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `counseling_reports`
--

CREATE TABLE `counseling_reports` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `report_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dormitory_payments`
--

CREATE TABLE `dormitory_payments` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `period` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `receipt_number` varchar(100) NOT NULL,
  `date_paid` date NOT NULL,
  `receipt_file` varchar(255) NOT NULL,
  `status` enum('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending',
  `verified_by` varchar(50) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dorm_agreements`
--

CREATE TABLE `dorm_agreements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `effective_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dorm_agreements`
--

INSERT INTO `dorm_agreements` (`id`, `title`, `content`, `effective_date`, `created_at`, `is_active`) VALUES
(1, 'jskfjsdkfj', 'sdfsdajkfhasjkd fh\r\nsfsdfsjdfs\r\nfsdfjsfs\r\nfsdfsdjfsdf\r\nsdfsdjfkhsdf\r\nsdfsdjfhk', '2001-12-25', '2025-08-15 15:16:38', 1);

-- --------------------------------------------------------

--
-- Table structure for table `dorm_agreement_acceptance`
--

CREATE TABLE `dorm_agreement_acceptance` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `agreement_id` int(11) NOT NULL,
  `accepted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dorm_agreement_acceptance`
--

INSERT INTO `dorm_agreement_acceptance` (`id`, `user_id`, `agreement_id`, `accepted_at`) VALUES
(3, 'GAB2022-1111', 1, '2025-08-16 09:05:45');

-- --------------------------------------------------------

--
-- Table structure for table `grievances`
--

CREATE TABLE `grievances` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('pending','resolved','rejected') DEFAULT 'pending',
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolution_date` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `sub_category_id` int(11) DEFAULT NULL,
  `severity` enum('low','medium','high','critical') DEFAULT 'low',
  `is_anonymous` tinyint(1) DEFAULT 0,
  `assigned_to_user_id` varchar(50) DEFAULT NULL,
  `acknowledged_at` datetime DEFAULT NULL,
  `first_response_at` datetime DEFAULT NULL,
  `in_progress_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `resolution_summary` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `reopen_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grievances`
--

INSERT INTO `grievances` (`id`, `user_id`, `category`, `title`, `description`, `attachment`, `status`, `submission_date`, `resolution_date`, `updated_at`, `category_id`, `sub_category_id`, `severity`, `is_anonymous`, `assigned_to_user_id`, `acknowledged_at`, `first_response_at`, `in_progress_at`, `closed_at`, `resolution_summary`, `rejection_reason`, `reopen_count`) VALUES
(1, 'GAB2022-00258', NULL, 'sdf', 'sdfsfsdf', NULL, 'resolved', '2025-04-02 22:35:08', '2025-04-02 23:26:13', '2025-08-16 17:52:49', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(2, 'admin01', NULL, 'sdf', 'sdfsfsdf', NULL, 'resolved', '2025-04-02 22:59:25', '2025-04-02 23:22:45', '2025-08-16 17:52:49', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(3, 'admin01', NULL, 'sdf', 'sdfsfsdf', NULL, 'resolved', '2025-04-02 23:05:25', '2025-04-02 23:22:42', '2025-08-16 17:52:49', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(4, 'GAB2022-00258', NULL, 'sdf', 'sdfsfsdf', NULL, 'resolved', '2025-04-02 23:10:44', '2025-04-02 23:22:28', '2025-08-16 17:52:49', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(5, 'GAB2022-00258', NULL, 'sdf', 'sdfsfsdf', NULL, 'resolved', '2025-04-02 23:18:29', '2025-04-02 23:22:43', '2025-08-16 17:52:49', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(6, 'GAB2022-00258', NULL, 'sdf', 'sdfsfsdf', NULL, 'resolved', '2025-04-02 23:22:30', '2025-04-02 23:22:44', '2025-08-16 17:52:49', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(7, 'GAB2022-00258', NULL, 'sdf', 'sdfsfsdf', NULL, 'pending', '2025-04-02 23:22:48', NULL, '2025-08-16 17:52:49', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(8, 'GAB2022-00258', NULL, 'sdf', 'sdfsfsdf', NULL, 'pending', '2025-04-02 23:28:09', NULL, '2025-08-16 17:52:49', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(9, 'GAB2022-00258', NULL, 'sdf', 'sdfsfsdf', NULL, 'pending', '2025-04-02 23:28:48', NULL, '2025-08-16 17:52:49', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(10, 'Gab2022-00259', NULL, 'Binagsak ni Sir', 'Di daw nakapag comply pero may valid reason!', NULL, 'pending', '2025-04-03 00:00:04', NULL, '2025-08-16 17:52:49', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(11, 'GAB2022-00000', NULL, 'Security', 'kdsfkljdsflkslfjsjflds lkdsjfklj dsflkdsjflk ', NULL, 'pending', '2025-04-07 19:16:08', NULL, '2025-08-16 17:52:49', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(12, 'Guidance01', NULL, 'sdfsdfsdf', 'sdfdsfsdfsdfskdjfh sajkdfhsdajkfhsadjk hfjksadhfjksda', NULL, 'pending', '2025-08-16 17:42:41', NULL, '2025-08-16 17:52:49', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(13, 'Guidance01', NULL, 'sdfsdfsdf', 'sdfdsfsdfsdfskdjfh sajkdfhsdajkfhsadjk hfjksadhfjksda', NULL, 'pending', '2025-08-16 18:04:19', NULL, '2025-08-16 18:04:19', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(14, 'Guidance01', NULL, 'sdfsdfsdf', 'sdfdsfsdfsdfskdjfh sajkdfhsdajkfhsadjk hfjksadhfjksda', NULL, 'pending', '2025-08-16 18:04:21', NULL, '2025-08-16 18:04:21', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(15, 'Guidance01', NULL, 'sdfsdfsdf', 'sdfdsfsdfsdfskdjfh sajkdfhsdajkfhsadjk hfjksadhfjksda', NULL, 'pending', '2025-08-16 18:04:35', NULL, '2025-08-16 18:04:35', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(16, 'Guidance01', NULL, 'sdfsdfsdf', 'sdfdsfsdfsdfskdjfh sajkdfhsdajkfhsadjk hfjksadhfjksda', NULL, 'pending', '2025-08-16 18:04:37', NULL, '2025-08-16 18:04:37', NULL, NULL, 'low', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(17, 'GAB2022-1111', NULL, 'sfsda', 'sdfsafsdf sdfsadf safsdaf asdf sadf asdf saf sdfsasdfdsfsd fsad', NULL, 'pending', '2025-09-06 04:21:21', NULL, '2025-09-06 04:21:21', 1, NULL, 'medium', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(18, 'GAB2022-1111', NULL, 'sinapak ng teacher', 'gusto ko makabawa maam', NULL, 'pending', '2025-09-06 08:54:28', NULL, '2025-09-06 08:54:28', NULL, NULL, 'critical', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `grievance_attachments`
--

CREATE TABLE `grievance_attachments` (
  `id` int(11) NOT NULL,
  `grievance_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_by` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grievance_categories`
--

CREATE TABLE `grievance_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sla_first_response_hours` int(11) DEFAULT 24,
  `sla_resolve_hours` int(11) DEFAULT 168,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grievance_categories`
--

INSERT INTO `grievance_categories` (`id`, `name`, `parent_id`, `sla_first_response_hours`, `sla_resolve_hours`, `created_at`, `updated_at`) VALUES
(1, 'Academics', NULL, 24, 168, '2025-09-06 04:17:17', '2025-09-06 04:17:17'),
(2, 'Facilities', NULL, 24, 168, '2025-09-06 04:17:23', '2025-09-06 04:17:23'),
(3, 'Finance', NULL, 24, 168, '2025-09-06 04:17:33', '2025-09-06 04:17:33');

-- --------------------------------------------------------

--
-- Table structure for table `grievance_comments`
--

CREATE TABLE `grievance_comments` (
  `id` int(11) NOT NULL,
  `grievance_id` int(11) NOT NULL,
  `author_user_id` varchar(50) NOT NULL,
  `is_internal` tinyint(1) DEFAULT 0,
  `body` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grievance_status_history`
--

CREATE TABLE `grievance_status_history` (
  `id` int(11) NOT NULL,
  `grievance_id` int(11) NOT NULL,
  `from_status` varchar(50) DEFAULT NULL,
  `to_status` varchar(50) NOT NULL,
  `actor_user_id` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guidance_blackouts`
--

CREATE TABLE `guidance_blackouts` (
  `id` int(11) NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guidance_blackouts`
--

INSERT INTO `guidance_blackouts` (`id`, `date_start`, `date_end`, `note`, `created_at`) VALUES
(1, '2025-09-07', '2025-09-08', 'Holiday Maintenance', '2025-09-06 18:41:44');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `title` varchar(120) NOT NULL,
  `body` varchar(255) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `room_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `receipt_path` varchar(255) NOT NULL,
  `status` enum('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified_by` varchar(50) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `file_hash` varchar(64) DEFAULT NULL,
  `receipt_number` varchar(100) DEFAULT NULL,
  `date_paid` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `student_id`, `room_id`, `amount`, `receipt_path`, `status`, `submitted_at`, `verified_by`, `verified_at`, `remarks`, `file_hash`, `receipt_number`, `date_paid`) VALUES
(1, 'GAB2022-1111', 15, 550.00, 'GAB2022-1111_20250816_043013.jpg', 'Verified', '2025-08-15 18:30:13', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'GAB2022-1111', 15, 550.00, 'GAB2022-1111_20250816_044525.png', 'Rejected', '2025-08-15 18:45:25', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'GAB2022-1111', 15, 23123.00, 'GAB2022-1111_20250816_050502.png', 'Verified', '2025-08-15 19:05:02', 'Dormitory01', '2025-08-17 06:44:30', '', NULL, NULL, NULL),
(4, 'GAB2022-1111', 15, 345435.00, 'GAB2022-1111_20250817_005714.png', 'Rejected', '2025-08-16 14:57:14', NULL, NULL, 'SDF', NULL, NULL, NULL),
(5, 'GAB2022-1111', 15, 55.00, 'GAB2022-1111_20250817_025119.png', 'Verified', '2025-08-16 16:51:19', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'GAB2022-1111', 15, 550.00, 'GAB2022-1111_20250817_025424.png', 'Rejected', '2025-08-16 16:54:24', NULL, NULL, 'wrong receipt', NULL, NULL, NULL),
(7, 'GAB2022-1111', 15, 26256.00, 'GAB2022-1111_20250818_051242.png', 'Rejected', '2025-08-17 19:12:42', NULL, NULL, 'iba yan', NULL, NULL, NULL),
(8, 'GAB2022-1111', 15, 100.00, 'GAB2022-1111_20250818_124441.png', 'Verified', '2025-08-18 02:44:41', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_audit_logs`
--

CREATE TABLE `payment_audit_logs` (
  `id` int(11) NOT NULL,
  `payment_type` varchar(20) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `action` varchar(20) NOT NULL,
  `old_status` varchar(20) DEFAULT NULL,
  `new_status` varchar(20) DEFAULT NULL,
  `admin_id` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_audit_logs`
--

INSERT INTO `payment_audit_logs` (`id`, `payment_type`, `payment_id`, `action`, `old_status`, `new_status`, `admin_id`, `remarks`, `created_at`) VALUES
(1, 'Dorm', 4, 'reject', 'Pending', 'Rejected', 'Dormitory01', 'SDF', '2025-08-17 08:46:29'),
(2, 'Dorm', 5, 'verify', 'Pending', 'Verified', 'Dormitory01', NULL, '2025-08-17 08:52:14'),
(3, 'Dorm', 6, 'reject', 'Pending', 'Rejected', 'Dormitory01', 'wrong receipt', '2025-08-17 08:54:58'),
(4, 'Dorm', 7, 'reject', 'Pending', 'Rejected', 'Dormitory01', 'iba yan', '2025-08-18 11:13:23'),
(5, 'Dorm', 8, 'verify', 'Pending', 'Verified', 'Dormitory01', NULL, '2025-08-27 14:57:22');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `request_type` varchar(100) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reschedule_requests`
--

CREATE TABLE `reschedule_requests` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `student_id` varchar(64) NOT NULL,
  `requested_datetime` datetime NOT NULL,
  `note` text DEFAULT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'open',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_beds` int(11) NOT NULL,
  `occupied_beds` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `amenities` text NOT NULL,
  `price_per_month` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `total_beds`, `occupied_beds`, `image`, `amenities`, `price_per_month`) VALUES
(15, 'Room 1', 7, 4, 'uploads/images.jpg', 'Closet, Private Bathroom, Shared Bathroom, Laundry Facilities', 500),
(16, 'jsafhkshf', 5, 0, 'uploads/Screenshot 2024-11-01 112113.png', 'Wi-Fi, Closet, Private Bathroom', 2500),
(17, 'dsfds', 5, 0, 'uploads/istockphoto-528888367-612x612.jpg', 'Wi-Fi, Air Conditioning', 5556);

-- --------------------------------------------------------

--
-- Table structure for table `scholarships`
--

CREATE TABLE `scholarships` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(100) DEFAULT 'Academic',
  `description` text NOT NULL,
  `eligibility` text NOT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `requirements` text DEFAULT NULL,
  `documents_required` text DEFAULT NULL,
  `max_applicants` int(11) DEFAULT 0,
  `current_applicants` int(11) DEFAULT 0,
  `created_by` varchar(50) DEFAULT NULL,
  `deadline` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarships`
--

INSERT INTO `scholarships` (`id`, `name`, `type`, `description`, `eligibility`, `amount`, `requirements`, `documents_required`, `max_applicants`, `current_applicants`, `created_by`, `deadline`, `created_at`, `updated_at`, `status`) VALUES
(24, 'Sample', 'Academic', '2k per head', 'dapat pogi', 2000.00, 'birth certificate and report card', '[\"Birth Certificate\",\"Report Card\"]', 50, 1, 'Scholarship123', '0000-00-00', '2025-09-06 01:25:41', '2025-09-06 02:03:53', 'active'),
(27, 'BIGAY NI NINONG', 'Need-based', 'fsadjkf', 'klsjdfklsdjf', 1000.00, 'fsdjkk', '[\"Report Card\"]', 130, 1, 'Scholarship123', '0000-00-00', '2025-09-06 02:09:30', '2025-09-06 06:41:29', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_applications`
--

CREATE TABLE `scholarship_applications` (
  `id` int(11) NOT NULL,
  `scholarship_id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending',
  `approval_date` datetime DEFAULT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `year_level` int(11) DEFAULT NULL,
  `documents_submitted` longtext DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `reviewed_by` varchar(50) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_applications`
--

INSERT INTO `scholarship_applications` (`id`, `scholarship_id`, `user_id`, `application_date`, `status`, `approval_date`, `gpa`, `course`, `year_level`, `documents_submitted`, `review_notes`, `rejection_reason`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(14, 24, 'GAB2022-1111', '2025-09-06 01:26:16', 'rejected', NULL, NULL, 'HM', 2, '[{\"type\":\"Birth Certificate\",\"filename\":\"photoofb.jpg\",\"path\":\"uploads\\/scholarship_documents\\/14_0_5fe315a026cdc602.jpg\"},{\"type\":\"Report Card\",\"filename\":\"photoofb.jpg\",\"path\":\"uploads\\/scholarship_documents\\/14_1_ac525c5a112f3f5e.jpg\"}]', NULL, 'Eligibility criteria not met', 'Scholarship123', '2025-09-06 10:02:57', '2025-09-06 01:26:16', '2025-09-06 02:02:57'),
(15, 27, 'GAB2022-1111', '2025-09-06 06:41:29', 'approved', '2025-09-09 08:05:38', NULL, 'HM', 2, '[{\"type\":\"Report Card\",\"filename\":\"photoofb.jpg\",\"path\":\"uploads\\/scholarship_documents\\/15_0_34801592fe46bc74.jpg\"}]', NULL, NULL, 'Scholarship123', '2025-09-09 08:05:38', '2025-09-06 06:41:29', '2025-09-09 00:05:38');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_audit_log`
--

CREATE TABLE `scholarship_audit_log` (
  `id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `user_id` varchar(50) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_audit_log`
--

INSERT INTO `scholarship_audit_log` (`id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `user_id`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 'CREATE', 'scholarship_applications', 13, NULL, '{\"scholarship_id\":16,\"user_id\":\"GAB2022-1111\",\"gpa\":2,\"documents_count\":3}', 'GAB2022-1111', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-25 15:49:19'),
(2, 'CREATE', 'scholarship_applications', 14, NULL, '{\"scholarship_id\":24,\"user_id\":\"GAB2022-1111\",\"documents_count\":2}', 'GAB2022-1111', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-06 01:26:16'),
(3, 'CREATE', 'scholarship_applications', 15, NULL, '{\"scholarship_id\":27,\"user_id\":\"GAB2022-1111\",\"documents_count\":1}', 'GAB2022-1111', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-06 06:41:29');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_documents`
--

CREATE TABLE `scholarship_documents` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_documents`
--

INSERT INTO `scholarship_documents` (`id`, `application_id`, `document_type`, `file_name`, `file_path`, `file_size`, `mime_type`, `uploaded_at`) VALUES
(4, 14, 'Birth Certificate', 'photoofb.jpg', 'uploads/scholarship_documents/14_0_5fe315a026cdc602.jpg', 75331, 'image/jpeg', '2025-09-06 01:26:16'),
(5, 14, 'Report Card', 'photoofb.jpg', 'uploads/scholarship_documents/14_1_ac525c5a112f3f5e.jpg', 75331, 'image/jpeg', '2025-09-06 01:26:16'),
(6, 15, 'Report Card', 'photoofb.jpg', 'uploads/scholarship_documents/15_0_34801592fe46bc74.jpg', 75331, 'image/jpeg', '2025-09-06 06:41:29');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_notifications`
--

CREATE TABLE `scholarship_notifications` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_notifications`
--

INSERT INTO `scholarship_notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 'GAB2022-1111', 'Application Submitted Successfully', 'Your application for abot kamay na pangarap has been submitted successfully. Application ID: 13', 'success', 0, '2025-08-25 15:49:19'),
(2, 'GAB2022-1111', 'Application Approved', 'Your application for abot kamay na pangarap has been approved.', 'success', 0, '2025-08-25 17:34:41'),
(3, 'GAB2022-1111', 'Application Approved', 'Your application for abot kamay na pangarap has been approved.', 'success', 0, '2025-08-25 17:34:45'),
(4, 'GAB2022-1111', 'Application Approved', 'Your application for abot kamay na pangarap has been approved.', 'success', 0, '2025-08-25 19:16:08'),
(5, 'GAB2022-00000', 'Application Rejected', 'Your application for Abot kamay na pangarap was rejected. Reason: Documents not clear or illegible', 'error', 0, '2025-08-25 19:43:43'),
(6, 'GAB2022-1111', 'Application Submitted Successfully', 'Your application for Sample has been submitted successfully. Application ID: 14', 'success', 0, '2025-09-06 01:26:16'),
(7, 'GAB2022-1111', 'Application Approved', 'Your application for Sample has been approved.', 'success', 0, '2025-09-06 01:28:14'),
(8, 'GAB2022-1111', 'Application Rejected', 'Your application for Sample was rejected. Reason: Eligibility criteria not met', 'error', 0, '2025-09-06 02:02:57'),
(9, 'GAB2022-1111', 'Application Submitted Successfully', 'Your application for BIGAY NI NINONG has been submitted successfully. Application ID: 15', 'success', 0, '2025-09-06 06:41:29'),
(10, 'GAB2022-1111', 'Application Approved', 'Your application for BIGAY NI NINONG has been approved.', 'success', 0, '2025-09-09 00:05:38');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_reports`
--

CREATE TABLE `scholarship_reports` (
  `id` int(11) NOT NULL,
  `report_type` varchar(100) NOT NULL,
  `report_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`report_data`)),
  `generated_by` varchar(50) NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_room_applications`
--

CREATE TABLE `student_room_applications` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `room_id` int(11) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `applied_at` datetime DEFAULT current_timestamp(),
  `price_per_month` float DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_room_applications`
--

INSERT INTO `student_room_applications` (`id`, `user_id`, `room_id`, `status`, `applied_at`, `price_per_month`, `approved_at`) VALUES
(51, 'Dormitory01', 15, 'Rejected', '2025-04-06 13:48:34', 500, NULL),
(52, 'GAB2022-00258', 15, 'Approved', '2025-04-06 13:49:15', 500, NULL),
(53, 'GAB2022-00000', 15, 'Approved', '2025-04-08 11:14:12', 500, NULL),
(54, 'GAB2022-1111', 16, 'Rejected', '2025-08-16 05:20:33', 2500, NULL),
(55, 'GAB2022-1111', 15, 'Rejected', '2025-08-16 05:20:53', 500, NULL),
(56, 'GAB2022-1111', 16, 'Rejected', '2025-08-16 07:33:50', 2500, NULL),
(57, 'GAB2022-1111', 15, 'Rejected', '2025-08-16 07:55:37', 500, NULL),
(58, 'GAB2022-1111', 15, 'Rejected', '2025-08-16 08:21:17', 500, NULL),
(59, 'GAB2022-1111', 16, 'Rejected', '2025-08-16 08:34:33', 2500, NULL),
(60, 'GAB2022-1111', 16, 'Rejected', '2025-08-16 08:38:04', 2500, NULL),
(61, 'GAB2022-1111', 15, 'Rejected', '2025-08-16 08:41:21', 500, NULL),
(62, 'GAB2022-1111', 15, 'Approved', '2025-08-16 09:05:46', 500, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_room_assignments`
--

CREATE TABLE `student_room_assignments` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `room_id` int(11) NOT NULL,
  `status` enum('Active','Inactive','MovedOut') NOT NULL DEFAULT 'Active',
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp(),
  `ended_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `birth_date` date NOT NULL,
  `nationality` varchar(50) NOT NULL,
  `religion` varchar(50) NOT NULL,
  `biological_sex` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `current_address` varchar(255) NOT NULL,
  `permanent_address` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `mother_name` varchar(50) NOT NULL,
  `mother_work` varchar(50) NOT NULL,
  `mother_contact` varchar(20) NOT NULL,
  `father_name` varchar(50) NOT NULL,
  `father_work` varchar(50) NOT NULL,
  `father_contact` varchar(20) NOT NULL,
  `siblings_count` int(11) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `date_registered` datetime DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Inactive',
  `year` int(11) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `family_income` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `middle_name`, `last_name`, `birth_date`, `nationality`, `religion`, `biological_sex`, `email`, `phone`, `current_address`, `permanent_address`, `role`, `password_hash`, `mother_name`, `mother_work`, `mother_contact`, `father_name`, `father_work`, `father_contact`, `siblings_count`, `unit`, `last_login`, `date_registered`, `profile_picture`, `status`, `year`, `section`, `course`, `department`, `gpa`, `family_income`) VALUES
('admin01', 'John', NULL, 'Doe', '1990-01-01', 'Filipino', 'Christian', 'Male', 'admin@example.com', '1234567890', '123 Admin St', '456 Permanent Address St', 'Power Admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Doe', 'Teacher', '0987654321', 'John Doe', 'Engineer', '1230987654', 3, NULL, '2025-09-07 07:40:20', '2025-03-27 22:23:04', NULL, 'Active', NULL, NULL, NULL, NULL, NULL, NULL),
('ADM_67e683402a324', 'sdfnsdknf', 'nsmd,fn', 'lnfsdkfnskdn', '2025-03-04', '', '', '', 'dkdsm2@gmail.com', '0978456314', 'dfskdjfskj', 'jbsdjfbskfnk', 'Student', '$2y$10$3Vea4r2KxXa2AARJHuRRCuY8xzyHyKSosbzFlhJiAhzOf/gED17i2', 'sdfnksn', '', '', 'nskjdfns', '', '', 0, 'Dormitory', NULL, '2025-03-28 19:08:48', NULL, 'Inactive', NULL, NULL, NULL, NULL, NULL, NULL),
('ADM_67e68827e81d7', 'sdjfnk', 'njsdnkfj', 'nkjsdnfksdnfk', '2025-03-11', '', '', '', 'sdjfnjksdfnk2@gmail.com', '0798546321', 'sdfkjsdfkjsd', 'kljnjksdfieuhk', 'Student', '$2y$10$tfS2jakzj5y0HvRTHO0CH.dnt9YumArtP81ZUknSS8EA0naFQIS66', 'sdfnskdnfk', '', '', 'jnksdjfnksdn', '', '', 0, 'Dormitory', NULL, '2025-03-28 19:29:43', NULL, 'Inactive', NULL, NULL, NULL, NULL, NULL, NULL),
('Dorm123', 'sdfnsdkfjn', 'nsdkfjsn', 'kjnfkdsjnf', '2025-03-18', '', '', '', 'dskfnskdnf2@gmail.com', '09756412358', 'sdjfklj', 'kjsdkjfnkjd', 'Student', '$2y$10$CH.jZQU.BNAvthuwPEVxQ.EXoasIiFFCzWl/kUwYFV8Jw62gUG9kq', 'sdfn', '', '', 'klsldkfn', '', '', 0, 'Dormitory', NULL, '2025-03-28 19:33:31', NULL, 'Inactive', NULL, NULL, NULL, NULL, NULL, NULL),
('Dormitory01', 'Juan', 'Last', 'Song', '2025-03-19', 'filipino', 'wesleyan', 'Male', 'juan02@gmail.com', '09756315487', 'Homeless', 'Homeless', 'Dormitory Admin', '$2y$10$ZnsVQ29PizgLji8nqjICM.grJZlqXRdYAQMmzBfjXxnPnO5GPQ14u', 'Laika', '', '', 'Ralp', '', '', 0, 'Guidance', '2025-09-07 13:28:36', '2025-03-31 19:29:28', NULL, 'Active', NULL, NULL, NULL, NULL, NULL, NULL),
('GAB2022-00000', 'jahn', 'sjdfjkkjshdf', 'jhdsfjk', '2002-06-05', 'filipino', 'Catholic', 'Male', 'sjfkjssjdfhk@gmail.com', '09785456325', 'sdjfsk', 'jkhsddkfj', 'Student', '$2y$10$MNVOOMGzx9pDObQUL3.LJeUpFtzf4yu/3O27893.yryghehtDQmha', 'sdfkjh', 'jksdhfjk', 'jkdhsdkfjh', 'jkhsdkjfhajk', 'hkjdshfjk', 'khsdkfhsk', 8, NULL, NULL, '2025-04-08 11:13:14', NULL, 'Inactive', 3, 'A', 'BSIT', NULL, NULL, NULL),
('GAB2022-00258', 'Jessie ', 'De Guzman', 'Javier', '2001-02-05', 'Filipino', 'Catholic', 'Male', 'javierjessie02@gmail.com', '09701114903', 'South Poblacion Gabaldon Nueva Ecija', 'Bacong, Umiray, Dingalan, Aurora', 'Student', '$2y$10$mnCIIltXnw3vsZ5OW7NPo.hhLve6VkjsGOIjiWu6wFVhpWo4mjPde', 'Malou Javier', 'Housewife', '09784563241', 'Arnie Javier', 'Driver', '09854763254', 4, NULL, NULL, '2025-03-29 17:37:23', NULL, 'Inactive', NULL, NULL, NULL, NULL, NULL, NULL),
('Gab2022-00259', 'Lavizares ', 'Domingo', 'Frayres III', '2004-05-17', 'Filipino', 'Mistica', 'Male', 'lavizaresf@gmail.com', '09128325757', 'Cuyapa, Gabaldon. Nueva ecija', 'Cuyapa, Gabaldon Nueva Ecija', 'Student', '$2y$10$Vz8Ppzpv6xMk4Oq8bX7k2eH7YL11rNmSiSdV9gRRm.sHReqLUTGzC', 'Mercedes', 'OFW', '09123456789', 'Lavizares', 'N/A', '09234567890', 2, NULL, NULL, '2025-04-03 15:50:55', NULL, 'Inactive', 3, 'B', 'BIST', NULL, NULL, NULL),
('GAB2022-00271', 'Brando', 'Mendoza', 'Verganos', '2003-08-14', 'Filipino', 'Wesleyan', 'Male', 'verganosbrando555@gmail.com', '09303562427', 'Ligaya', 'Ligaya', 'Student', '$2y$10$UVfICg7VsmBCL/m0VXs00e81pqtTpz6Pr/Kd7vBvzm5shRcPMuUYq', 'Dolly Mendoza', 'Housewife', '09756482456', 'Benjamin Verganos', 'Contractor', '09785463254', 3, NULL, NULL, '2025-04-02 10:23:14', NULL, 'Inactive', 3, 'B', 'BIST', NULL, NULL, NULL),
('GAB2022-00291', 'SHANE', 'G', 'HGD', '2004-06-10', 'filipino', 'INC', 'Female', 'flororitashane@gmail.com', '09066832584', 'ibona', 'ibona', 'Student', '$2y$10$wCTquK/f4V.623lZhbyBc.ZC1kAHl31jFkKGq/0qnhApcroWCGE0q', 'sofia', 'Housewife', '09658746215', 'romeo', 'farmers', '09765432578', 5, NULL, NULL, '2025-04-03 15:35:15', NULL, 'Inactive', 3, 'B', 'BIST', NULL, NULL, NULL),
('GAB2022-00685', '', 'Noma', '', '0000-00-00', '', '', 'Female', 'arnykayeadobe@gmail.com', '09687969271', '', '', 'Student', '$2y$10$NCmNOSWTeCp.XLtazF7su.ZbDV9QVTZMHgK4io4gvzAHMP8yQL9n2', 'Cindy N. Adobe', 'Housewife', '09384528909', 'efren M. Adobe', 'Driver', 'NA', 3, NULL, NULL, '2025-03-27 15:31:52', NULL, 'Inactive', NULL, NULL, NULL, NULL, NULL, NULL),
('GAB2022-1111', 'Anderson', 'Sabandal', 'Ruiz', '1999-02-08', 'Filipino', 'Catholic', 'Male', 'jessiejavie89@gmail.com', '09784562351', 'Bulacan City', 'Bulacan City', 'Student', '$2y$10$/nF21Os95YdrTIqUdvc6m.JLe4Gfr8KCgAcZQccV3W4NFVCW31lzW', 'Linda Sabandal', 'Secretary of the President', '0913545698', 'Robert Ruiz', 'Tambay', '09786452134', 3, NULL, '2025-09-07 16:01:31', '2025-08-16 05:19:48', 'uploads/profile_pics/user_GAB2022-1111_1757385531.jpg', 'Active', 3, 'B', 'HM', '', 1.00, 50000.00),
('GAB2022-12345', 'Jeremy', 'Noma', 'javier', '1999-01-03', 'filipino', 'Catholic', 'Male', 'jeremy@gmail.com', '09458463254', 'bacong', 'bacong', 'Student', '$2y$10$1gMxcu.i2kncVd.fIxNRi.aMdGhVA37rvZoPV1ZOq9Eq4solTctQi', 'lala', 'na', '09784563214', 'gats', '09456325447', 'Driver', 3, NULL, NULL, '2025-07-21 19:33:54', NULL, 'Inactive', 3, 'B', 'Educ', NULL, NULL, NULL),
('Guidance01', 'Mila', 'Laso', 'Lambo', '2025-05-08', '', '', '', 'Mila023@gmail.com', '09756482384', 'Umiray', 'Umiray', 'Guidance Admin', '$2y$10$dX.X47cJ9oz.UyzR62eBwOf7PFhoTzkWD3WnMqRocm20HFeWKL1M6', 'Kimmy', '', '', 'Lando', '', '', 0, 'Guidance Admin', '2025-09-07 07:38:03', '2025-04-01 11:37:50', NULL, 'Active', NULL, NULL, NULL, NULL, NULL, NULL),
('Registrar01', 'Rarnar', 'Larn', 'Gard', '2025-04-17', '', '', '', 'ragnar033@gmail.com', '09756482364', 'Cuyapa', 'Cuyapa, Gabaldon Nueva Ecija', 'Registrar Admin', '$2y$10$y7Dqj2mq234NMYSOV.UvPe/bbRAbCbun8w3U/iVzDGbOwYBWiMibu', 'Linda', '', '', 'Sandy', '', '', 0, 'Registrar Admin', NULL, '2025-04-01 11:35:26', NULL, 'Inactive', NULL, NULL, NULL, NULL, NULL, NULL),
('Scholarship01', 'Scholarship', '', 'Administrator', '1990-01-01', 'Filipino', 'Christian', 'Male', 'scholarship@neust.edu.ph', '09123456789', 'NEUST Gabaldon Campus', 'NEUST Gabaldon Campus', 'Scholarship Admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Mother', 'N/A', 'N/A', 'Admin Father', 'N/A', 'N/A', 0, 'Scholarship Office', NULL, '2025-08-18 06:50:58', NULL, 'Active', NULL, NULL, 'Scholarship Management', 'Student Services', NULL, NULL),
('Scholarship123', 'LUNA', 'HEMENEZ', 'GARLANDO', '1998-02-03', '', '', '', 'lunagarlando04@gmail.com', '09756431524', 'Quezon City', 'Quezon City', 'Scholarship Admin', '$2y$10$vQQnFzqKMTeOw62ysm4uR.0h3HxjhXrcN8uGh71lLIjDLaKn1D/Im', 'Lany', '', '', 'Arman', '', '', 0, 'Scholarship Admin', '2025-09-07 13:28:17', '2025-08-18 10:29:09', NULL, 'Active', NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_appt_counselor_time` (`user_id`,`appointment_date`),
  ADD KEY `idx_appt_student_time` (`student_id`,`appointment_date`);

--
-- Indexes for table `appointment_logs`
--
ALTER TABLE `appointment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `actor_id` (`actor_id`),
  ADD KEY `action` (`action`);

--
-- Indexes for table `calendar_blocks`
--
ALTER TABLE `calendar_blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_block_time` (`start_datetime`,`end_datetime`),
  ADD KEY `idx_block_counselor` (`counselor_id`);

--
-- Indexes for table `counseling_reports`
--
ALTER TABLE `counseling_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `dormitory_payments`
--
ALTER TABLE `dormitory_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `receipt_number` (`receipt_number`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `dormitory_payments_verified_fk` (`verified_by`);

--
-- Indexes for table `dorm_agreements`
--
ALTER TABLE `dorm_agreements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dorm_agreement_acceptance`
--
ALTER TABLE `dorm_agreement_acceptance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_agreement` (`user_id`,`agreement_id`),
  ADD KEY `agreement_id` (`agreement_id`);

--
-- Indexes for table `grievances`
--
ALTER TABLE `grievances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_assigned` (`assigned_to_user_id`);

--
-- Indexes for table `grievance_attachments`
--
ALTER TABLE `grievance_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_grievance` (`grievance_id`);

--
-- Indexes for table `grievance_categories`
--
ALTER TABLE `grievance_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_parent` (`parent_id`);

--
-- Indexes for table `grievance_comments`
--
ALTER TABLE `grievance_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_grievance` (`grievance_id`);

--
-- Indexes for table `grievance_status_history`
--
ALTER TABLE `grievance_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_grievance` (`grievance_id`);

--
-- Indexes for table `guidance_blackouts`
--
ALTER TABLE `guidance_blackouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date_start` (`date_start`),
  ADD KEY `date_end` (`date_end`);

--
-- Indexes for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_user` (`user_id`,`is_read`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `status` (`status`),
  ADD KEY `submitted_at` (`submitted_at`),
  ADD KEY `idx_verified_by` (`verified_by`),
  ADD KEY `idx_payments_status_date` (`status`,`submitted_at`),
  ADD KEY `idx_payments_file_hash` (`file_hash`),
  ADD KEY `idx_payments_receipt_number` (`receipt_number`);

--
-- Indexes for table `payment_audit_logs`
--
ALTER TABLE `payment_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payment_id` (`payment_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reschedule_requests`
--
ALTER TABLE `reschedule_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scholarships`
--
ALTER TABLE `scholarships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_scholarship_status` (`status`),
  ADD KEY `idx_scholarship_deadline` (`deadline`),
  ADD KEY `idx_scholarship_type` (`type`),
  ADD KEY `fk_scholarships_created_by_users` (`created_by`);

--
-- Indexes for table `scholarship_applications`
--
ALTER TABLE `scholarship_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_application_status` (`status`),
  ADD KEY `idx_application_date` (`application_date`),
  ADD KEY `idx_apps_scholarship` (`scholarship_id`);

--
-- Indexes for table `scholarship_audit_log`
--
ALTER TABLE `scholarship_audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_table_name` (`table_name`),
  ADD KEY `idx_record_id` (`record_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `scholarship_documents`
--
ALTER TABLE `scholarship_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_application_id` (`application_id`),
  ADD KEY `idx_document_type` (`document_type`);

--
-- Indexes for table `scholarship_notifications`
--
ALTER TABLE `scholarship_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`);

--
-- Indexes for table `scholarship_reports`
--
ALTER TABLE `scholarship_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_report_type` (`report_type`),
  ADD KEY `idx_generated_by` (`generated_by`);

--
-- Indexes for table `student_room_applications`
--
ALTER TABLE `student_room_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `student_room_assignments`
--
ALTER TABLE `student_room_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `appointment_logs`
--
ALTER TABLE `appointment_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `calendar_blocks`
--
ALTER TABLE `calendar_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `counseling_reports`
--
ALTER TABLE `counseling_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dormitory_payments`
--
ALTER TABLE `dormitory_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dorm_agreements`
--
ALTER TABLE `dorm_agreements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dorm_agreement_acceptance`
--
ALTER TABLE `dorm_agreement_acceptance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `grievances`
--
ALTER TABLE `grievances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `grievance_attachments`
--
ALTER TABLE `grievance_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grievance_categories`
--
ALTER TABLE `grievance_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `grievance_comments`
--
ALTER TABLE `grievance_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grievance_status_history`
--
ALTER TABLE `grievance_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guidance_blackouts`
--
ALTER TABLE `guidance_blackouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payment_audit_logs`
--
ALTER TABLE `payment_audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reschedule_requests`
--
ALTER TABLE `reschedule_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `scholarships`
--
ALTER TABLE `scholarships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `scholarship_applications`
--
ALTER TABLE `scholarship_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `scholarship_audit_log`
--
ALTER TABLE `scholarship_audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `scholarship_documents`
--
ALTER TABLE `scholarship_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `scholarship_notifications`
--
ALTER TABLE `scholarship_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `scholarship_reports`
--
ALTER TABLE `scholarship_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_room_applications`
--
ALTER TABLE `student_room_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `student_room_assignments`
--
ALTER TABLE `student_room_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `counseling_reports`
--
ALTER TABLE `counseling_reports`
  ADD CONSTRAINT `counseling_reports_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dormitory_payments`
--
ALTER TABLE `dormitory_payments`
  ADD CONSTRAINT `dormitory_payments_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dormitory_payments_verified_fk` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dorm_agreement_acceptance`
--
ALTER TABLE `dorm_agreement_acceptance`
  ADD CONSTRAINT `dorm_agreement_acceptance_agreement_fk` FOREIGN KEY (`agreement_id`) REFERENCES `dorm_agreements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dorm_agreement_acceptance_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `grievances`
--
ALTER TABLE `grievances`
  ADD CONSTRAINT `grievances_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `grievance_attachments`
--
ALTER TABLE `grievance_attachments`
  ADD CONSTRAINT `fk_attachments_grievance` FOREIGN KEY (`grievance_id`) REFERENCES `grievances` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grievance_comments`
--
ALTER TABLE `grievance_comments`
  ADD CONSTRAINT `fk_comments_grievance` FOREIGN KEY (`grievance_id`) REFERENCES `grievances` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grievance_status_history`
--
ALTER TABLE `grievance_status_history`
  ADD CONSTRAINT `fk_history_grievance` FOREIGN KEY (`grievance_id`) REFERENCES `grievances` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_room_fk` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_student_fk` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_verified_by_fk` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `scholarships`
--
ALTER TABLE `scholarships`
  ADD CONSTRAINT `fk_scholarships_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `scholarship_applications`
--
ALTER TABLE `scholarship_applications`
  ADD CONSTRAINT `scholarship_applications_ibfk_1` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`),
  ADD CONSTRAINT `scholarship_applications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `scholarship_documents`
--
ALTER TABLE `scholarship_documents`
  ADD CONSTRAINT `scholarship_documents_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `scholarship_applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_room_applications`
--
ALTER TABLE `student_room_applications`
  ADD CONSTRAINT `student_room_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_room_applications_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_room_assignments`
--
ALTER TABLE `student_room_assignments`
  ADD CONSTRAINT `student_room_assignments_room_fk` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_room_assignments_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
