-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2019 at 01:48 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `examdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `exam_answer`
--

CREATE TABLE `exam_answer` (
  `answer_id` int(11) NOT NULL DEFAULT '0',
  `question_id` int(11) DEFAULT NULL,
  `answer` text COLLATE utf8_swedish_ci,
  `answer_sn` tinyint(4) NOT NULL,
  `is_correct` enum('N','Y') COLLATE utf8_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `exam_answer`
--

INSERT INTO `exam_answer` (`answer_id`, `question_id`, `answer`, `answer_sn`, `is_correct`) VALUES
(1, 1, 'Test 0', 1, 'N'),
(2, 1, 'Test 1', 2, 'N'),
(3, 1, 'Test 2', 3, 'N'),
(4, 1, 'Test 3', 4, 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `exam_center`
--

CREATE TABLE `exam_center` (
  `center_id` int(11) NOT NULL,
  `center_name` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `center_address` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `center_phone` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `center_fax` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `center_email` varchar(100) COLLATE utf8_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `exam_center`
--

INSERT INTO `exam_center` (`center_id`, `center_name`, `center_address`, `center_phone`, `center_fax`, `center_email`) VALUES
(1, 'itest', 'itest', '0215589', '054489', 'itest@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `exam_exam`
--

CREATE TABLE `exam_exam` (
  `exam_id` int(11) NOT NULL,
  `exam_code` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `exam_type_id` int(11) NOT NULL,
  `exam_create_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `max_time_adjustment` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `exam_exam`
--

INSERT INTO `exam_exam` (`exam_id`, `exam_code`, `exam_type_id`, `exam_create_date`, `max_time_adjustment`) VALUES
(1, '02', 1, '2019-04-23 07:24:49', 0),
(2, '05', 2, '2019-04-30 11:01:01', 0),
(3, '60-1', 2, '2019-05-06 10:28:57', 0);

-- --------------------------------------------------------

--
-- Table structure for table `exam_exam_center`
--

CREATE TABLE `exam_exam_center` (
  `exam_id` int(11) NOT NULL,
  `center_id` int(11) NOT NULL,
  `exam_code` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `schedule_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `exam_exam_center`
--

INSERT INTO `exam_exam_center` (`exam_id`, `center_id`, `exam_code`, `schedule_date`) VALUES
(1, 1, NULL, NULL),
(2, 1, NULL, NULL),
(3, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exam_exam_message`
--

CREATE TABLE `exam_exam_message` (
  `exam_id` int(11) NOT NULL,
  `start_message` text COLLATE utf8_swedish_ci NOT NULL,
  `end_message` text COLLATE utf8_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_exam_question`
--

CREATE TABLE `exam_exam_question` (
  `exam_id` int(11) NOT NULL,
  `chapter_no` int(11) NOT NULL,
  `question_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `exam_exam_question`
--

INSERT INTO `exam_exam_question` (`exam_id`, `chapter_no`, `question_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `exam_exam_status`
--

CREATE TABLE `exam_exam_status` (
  `exam_id` int(11) NOT NULL,
  `center_id` int(11) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `full_close` enum('N','Y') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'N',
  `grace_time` int(3) NOT NULL,
  `grace_reason` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `send_for_approve` enum('N','Y') COLLATE utf8_swedish_ci DEFAULT NULL,
  `final_submit_time` datetime DEFAULT NULL,
  `is_approved` enum('N','Y') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `exam_exam_status`
--

INSERT INTO `exam_exam_status` (`exam_id`, `center_id`, `start_time`, `end_time`, `full_close`, `grace_time`, `grace_reason`, `send_for_approve`, `final_submit_time`, `is_approved`) VALUES
(1, 1, NULL, NULL, 'N', 0, NULL, NULL, NULL, 'N'),
(2, 1, NULL, NULL, 'N', 0, NULL, NULL, NULL, 'N'),
(3, 1, NULL, NULL, 'N', 0, NULL, NULL, NULL, 'N');

-- --------------------------------------------------------

--
-- Table structure for table `exam_exam_student`
--

CREATE TABLE `exam_exam_student` (
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `center_id` int(11) NOT NULL,
  `password` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `txtPassword` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `active_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `exam_exam_student`
--

INSERT INTO `exam_exam_student` (`student_id`, `exam_id`, `center_id`, `password`, `txtPassword`, `active_date`) VALUES
(1, 2, 1, 'cW56Rw==', '$1$4bfe4209$AdScjbn1Wc8G8eiAE6yFk0', '2019-05-08 17:33:06'),
(2, 2, 1, 'UGxlbQ==', '$1$a77bfe98$sc6.LmJC2oDnE51hho3Zr0', '2019-05-08 17:33:06');

-- --------------------------------------------------------

--
-- Table structure for table `exam_exam_type`
--

CREATE TABLE `exam_exam_type` (
  `exam_type_id` int(11) NOT NULL,
  `exam_type` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `total_question` int(11) NOT NULL,
  `full_mark` int(11) NOT NULL,
  `pass_mark` int(11) NOT NULL,
  `total_time` int(11) NOT NULL,
  `mcq_mark` int(11) NOT NULL,
  `practical_mark` int(11) NOT NULL,
  `start_message` text COLLATE utf8_swedish_ci NOT NULL,
  `end_message` text COLLATE utf8_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `exam_exam_type`
--

INSERT INTO `exam_exam_type` (`exam_type_id`, `exam_type`, `total_question`, `full_mark`, `pass_mark`, `total_time`, `mcq_mark`, `practical_mark`, `start_message`, `end_message`) VALUES
(1, '40 Hrs', 30, 100, 40, 30, 60, 40, '<p>exam will be start 10:30 AM</p>', '<p>exam will be strop 11:00 AM</p>'),
(2, '60 Hrs', 20, 100, 40, 30, 60, 40, '<p>Exam start 10:30 AM</p>', '<p>Exam stop 11:00 AM</p>');

-- --------------------------------------------------------

--
-- Table structure for table `exam_question`
--

CREATE TABLE `exam_question` (
  `question_id` int(11) NOT NULL DEFAULT '0',
  `question` text COLLATE utf8_swedish_ci,
  `exam_type_id` int(11) NOT NULL,
  `chapter_no` int(11) NOT NULL,
  `answer_type` enum('S','M') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'S' COMMENT 'S=Single, M=Multiple',
  `rand_answer` enum('Y','N') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'Y',
  `is_active` enum('Y','N') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `exam_question`
--

INSERT INTO `exam_question` (`question_id`, `question`, `exam_type_id`, `chapter_no`, `answer_type`, `rand_answer`, `is_active`) VALUES
(1, '1', 1, 1, 'M', 'Y', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `exam_question_remarks`
--

CREATE TABLE `exam_question_remarks` (
  `question_id` int(11) NOT NULL,
  `question_remarks` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `exam_question_remarks`
--

INSERT INTO `exam_question_remarks` (`question_id`, `question_remarks`) VALUES
(1, 'time test');

-- --------------------------------------------------------

--
-- Table structure for table `exam_session`
--

CREATE TABLE `exam_session` (
  `session_id` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `remote_ip` varchar(20) COLLATE utf8_swedish_ci NOT NULL,
  `browser` varchar(500) COLLATE utf8_swedish_ci NOT NULL,
  `last_activity` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `exam_session`
--

INSERT INTO `exam_session` (`session_id`, `username`, `remote_ip`, `browser`, `last_activity`) VALUES
('dhee0n4resqajc0osqm7vbsou7', 'abc', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36', 1557314219),
('9f45ssmqoivmf7tpfv7vmakdbs', 'abc', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36', 1557315863),
('dsbe9v74btq0g5da2avmn6t0nd', 'abc', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36', 1557316086);

-- --------------------------------------------------------

--
-- Table structure for table `exam_student`
--

CREATE TABLE `exam_student` (
  `student_id` int(11) NOT NULL DEFAULT '0',
  `it_serial` varchar(30) COLLATE utf8_swedish_ci NOT NULL,
  `reg_no` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `exam_student`
--

INSERT INTO `exam_student` (`student_id`, `it_serial`, `reg_no`, `name`) VALUES
(1, '60-21', 'FN00125', 'Prakash'),
(2, '60-22', 'FN00125', 'gfghfh');

-- --------------------------------------------------------

--
-- Table structure for table `exam_student_answer`
--

CREATE TABLE `exam_student_answer` (
  `student_answer_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `center_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `choosen_answer` int(11) DEFAULT NULL,
  `correct_answer` int(11) DEFAULT NULL,
  `is_mark` enum('0','1') COLLATE utf8_swedish_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_student_exam`
--

CREATE TABLE `exam_student_exam` (
  `student_id` int(11) NOT NULL DEFAULT '0',
  `exam_id` int(11) NOT NULL DEFAULT '0',
  `center_id` int(11) NOT NULL DEFAULT '0',
  `max_question` int(11) NOT NULL DEFAULT '0',
  `mcq_mark` int(11) NOT NULL DEFAULT '0',
  `practical_mark` int(11) NOT NULL DEFAULT '0',
  `is_closed` enum('N','Y') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_student_question`
--

CREATE TABLE `exam_student_question` (
  `exam_student_question_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL DEFAULT '0',
  `chapter_no` int(11) NOT NULL,
  `answer_order` varchar(20) COLLATE utf8_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_type_chapter_question`
--

CREATE TABLE `exam_type_chapter_question` (
  `exam_type_id` int(11) NOT NULL,
  `chapter_no` int(11) NOT NULL,
  `no_of_question` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `exam_type_chapter_question`
--

INSERT INTO `exam_type_chapter_question` (`exam_type_id`, `chapter_no`, `no_of_question`) VALUES
(1, 1, 1),
(1, 2, 1),
(1, 3, 1),
(1, 4, 1),
(1, 5, 1),
(1, 6, 1),
(1, 7, 1),
(1, 8, 1),
(1, 9, 1),
(1, 10, 1),
(1, 11, 1),
(1, 12, 1),
(1, 13, 1),
(1, 14, 1),
(1, 15, 1),
(1, 16, 1),
(1, 17, 1),
(1, 18, 1),
(1, 19, 1),
(1, 20, 1),
(1, 21, 1),
(1, 22, 1),
(1, 23, 1),
(1, 24, 1),
(1, 25, 1),
(1, 26, 1),
(1, 27, 1),
(1, 28, 1),
(1, 29, 1),
(1, 30, 1);

-- --------------------------------------------------------

--
-- Table structure for table `exam_user`
--

CREATE TABLE `exam_user` (
  `user_id` int(11) NOT NULL,
  `UserName` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `FullName` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `user_type` enum('C','A') COLLATE utf8_swedish_ci DEFAULT NULL COMMENT 'c=center, a=admin',
  `active` enum('N','Y') COLLATE utf8_swedish_ci DEFAULT NULL,
  `LastLogin` bigint(20) NOT NULL,
  `center_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `exam_user`
--

INSERT INTO `exam_user` (`user_id`, `UserName`, `Password`, `FullName`, `user_type`, `active`, `LastLogin`, `center_id`) VALUES
(0, 'test', '$1$703932bf$XSTR.pAvtqm2j4Fa1F0EO0', 'Test', 'A', 'Y', 0, 0),
(1, 'admin', '$1$c2c230c6$MAEksP8/cHRay/.M05Khq0', 'Admin User', 'A', 'Y', 1444122087, 0),
(2, 'ram', '$1$c2c230c6$MAEksP8/cHRay/.M05Khq0', 'Ram Kumar Nepal', 'A', 'Y', 1048575, 0),
(3, 'ananda', '$1$c2c230c6$MAEksP8/cHRay/.M05Khq0', 'Ananda Chaurasiya', 'C', 'Y', 1501831835, 1),
(4, 'gyan', '$1$49050cbd$.7hHEDpovYSp4hHdwqJTC0', 'Gyan Ratna Maharjan', 'C', 'Y', 1557138470, 1),
(7, 'testcpn', '$1$c2c230c6$MAEksP8/cHRay/.M05Khq0', 'Test', 'C', 'Y', 1486637563, 1),
(8, 'manoj', '$1$c2c230c6$MAEksP8/cHRay/.M05Khq0', 'Manoj Baral', 'C', 'N', 1490334896, 3),
(9, 'testssi', '$1$c2c230c6$MAEksP8/cHRay/.M05Khq0', 'Test', 'C', 'Y', 1490075246, 3),
(10, 'sagar', '$1$c2c230c6$MAEksP8/cHRay/.M05Khq0', 'sagar', 'C', 'Y', 1534928701, 4),
(11, 'testbiit', '$1$c2c230c6$MAEksP8/cHRay/.M05Khq0', 'biit', 'C', 'Y', 1493353250, 4),
(12, 'testaite', '$1$c2c230c6$MAEksP8/cHRay/.M05Khq0', 'testaite', 'C', 'Y', 1500160375, 2),
(13, 'Sadish', '$1$c2c230c6$MAEksP8/cHRay/.M05Khq0', 'Sadish Tiwari', 'C', 'Y', 1522829777, 3),
(14, 'ican', '$1$e9e9a8be$.3IJpMwuchqfuhzjNRdys.', 'ram', 'C', 'Y', 1551860513, 5),
(15, 'isaudit', '$1$c2c230c6$MAEksP8/cHRay/.M05Khq0', 'IS Audit', 'A', 'Y', 1538725000, 0),
(16, 'abc', '$1$831e1575$Ghe87auVg/Hm5V0ebIbTY0', 'ABC', 'A', 'Y', 1556870666, 0),
(17, 'vvvvv', '$1$0f0e6eec$vEDqvvIoaDJl.nSBCwsQp.', '123654', 'A', 'Y', 1557138055, 0);

-- --------------------------------------------------------

--
-- Table structure for table `exam_user_permission`
--

CREATE TABLE `exam_user_permission` (
  `user_id` int(11) NOT NULL,
  `permission` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `exam_user_permission`
--

INSERT INTO `exam_user_permission` (`user_id`, `permission`) VALUES
(1, 1073741823),
(2, 1048575),
(15, 0),
(16, 8388607),
(17, 8388607);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `exam_answer`
--
ALTER TABLE `exam_answer`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `exam_center`
--
ALTER TABLE `exam_center`
  ADD PRIMARY KEY (`center_id`);

--
-- Indexes for table `exam_exam`
--
ALTER TABLE `exam_exam`
  ADD PRIMARY KEY (`exam_id`),
  ADD KEY `exam_type_id` (`exam_type_id`);

--
-- Indexes for table `exam_exam_center`
--
ALTER TABLE `exam_exam_center`
  ADD PRIMARY KEY (`exam_id`,`center_id`),
  ADD KEY `center_id` (`center_id`);

--
-- Indexes for table `exam_exam_message`
--
ALTER TABLE `exam_exam_message`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `exam_exam_question`
--
ALTER TABLE `exam_exam_question`
  ADD PRIMARY KEY (`exam_id`,`question_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `exam_exam_status`
--
ALTER TABLE `exam_exam_status`
  ADD PRIMARY KEY (`exam_id`,`center_id`),
  ADD KEY `center_id` (`center_id`);

--
-- Indexes for table `exam_exam_student`
--
ALTER TABLE `exam_exam_student`
  ADD PRIMARY KEY (`student_id`,`exam_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `center_id` (`center_id`);

--
-- Indexes for table `exam_exam_type`
--
ALTER TABLE `exam_exam_type`
  ADD PRIMARY KEY (`exam_type_id`),
  ADD UNIQUE KEY `exam_type` (`exam_type`);

--
-- Indexes for table `exam_question`
--
ALTER TABLE `exam_question`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `exam_type_id` (`exam_type_id`);

--
-- Indexes for table `exam_question_remarks`
--
ALTER TABLE `exam_question_remarks`
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `exam_session`
--
ALTER TABLE `exam_session`
  ADD KEY `session_id` (`session_id`,`username`,`remote_ip`);

--
-- Indexes for table `exam_student`
--
ALTER TABLE `exam_student`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `it_serial` (`it_serial`);

--
-- Indexes for table `exam_student_answer`
--
ALTER TABLE `exam_student_answer`
  ADD PRIMARY KEY (`student_answer_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `center_id` (`center_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `exam_student_exam`
--
ALTER TABLE `exam_student_exam`
  ADD PRIMARY KEY (`student_id`,`exam_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`exam_id`),
  ADD KEY `center_id` (`center_id`);

--
-- Indexes for table `exam_student_question`
--
ALTER TABLE `exam_student_question`
  ADD PRIMARY KEY (`exam_student_question_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `exam_type_chapter_question`
--
ALTER TABLE `exam_type_chapter_question`
  ADD PRIMARY KEY (`exam_type_id`,`chapter_no`);

--
-- Indexes for table `exam_user`
--
ALTER TABLE `exam_user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `UserName` (`UserName`);

--
-- Indexes for table `exam_user_permission`
--
ALTER TABLE `exam_user_permission`
  ADD PRIMARY KEY (`user_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exam_answer`
--
ALTER TABLE `exam_answer`
  ADD CONSTRAINT `exam_answer_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `exam_question` (`question_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exam_exam`
--
ALTER TABLE `exam_exam`
  ADD CONSTRAINT `exam_exam_ibfk_1` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_exam_type` (`exam_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exam_exam_center`
--
ALTER TABLE `exam_exam_center`
  ADD CONSTRAINT `exam_exam_center_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam_exam` (`exam_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_exam_center_ibfk_2` FOREIGN KEY (`center_id`) REFERENCES `exam_center` (`center_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exam_exam_message`
--
ALTER TABLE `exam_exam_message`
  ADD CONSTRAINT `exam_exam_message_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam_exam` (`exam_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exam_exam_question`
--
ALTER TABLE `exam_exam_question`
  ADD CONSTRAINT `exam_exam_question_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam_exam` (`exam_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_exam_question_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `exam_question` (`question_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exam_exam_status`
--
ALTER TABLE `exam_exam_status`
  ADD CONSTRAINT `exam_exam_status_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam_exam` (`exam_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_exam_status_ibfk_2` FOREIGN KEY (`center_id`) REFERENCES `exam_center` (`center_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exam_exam_student`
--
ALTER TABLE `exam_exam_student`
  ADD CONSTRAINT `exam_exam_student_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam_exam` (`exam_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_exam_student_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `exam_student` (`student_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_exam_student_ibfk_3` FOREIGN KEY (`center_id`) REFERENCES `exam_center` (`center_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exam_question`
--
ALTER TABLE `exam_question`
  ADD CONSTRAINT `exam_question_ibfk_1` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_exam_type` (`exam_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exam_question_remarks`
--
ALTER TABLE `exam_question_remarks`
  ADD CONSTRAINT `exam_question_remarks_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `exam_question` (`question_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exam_student_answer`
--
ALTER TABLE `exam_student_answer`
  ADD CONSTRAINT `exam_student_answer_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `exam_question` (`question_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_student_answer_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exam_exam` (`exam_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_student_answer_ibfk_3` FOREIGN KEY (`center_id`) REFERENCES `exam_center` (`center_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_student_answer_ibfk_4` FOREIGN KEY (`student_id`) REFERENCES `exam_student` (`student_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exam_student_exam`
--
ALTER TABLE `exam_student_exam`
  ADD CONSTRAINT `exam_student_exam_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `exam_student` (`student_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_student_exam_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exam_exam` (`exam_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_student_exam_ibfk_3` FOREIGN KEY (`center_id`) REFERENCES `exam_center` (`center_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exam_student_question`
--
ALTER TABLE `exam_student_question`
  ADD CONSTRAINT `exam_student_question_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam_exam` (`exam_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_student_question_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `exam_student` (`student_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_student_question_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `exam_question` (`question_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exam_type_chapter_question`
--
ALTER TABLE `exam_type_chapter_question`
  ADD CONSTRAINT `exam_type_chapter_question_ibfk_1` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_exam_type` (`exam_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exam_user_permission`
--
ALTER TABLE `exam_user_permission`
  ADD CONSTRAINT `exam_user_permission_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `exam_user` (`user_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
