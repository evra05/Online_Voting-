-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2024 at 05:41 AM
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
-- Database: `web_application_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `candidate_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `faculty` varchar(255) NOT NULL,
  `manifesto` text NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`candidate_id`, `name`, `faculty`, `manifesto`, `image`) VALUES
(2, ' John Carter', ' Engineering', '\"As a student of Engineering, I promise to bridge the gap between theoretical knowledge and practical application. My primary goal is to improve the infrastructure of our university, ensuring that our labs are equipped with the latest technology to help us stay at the forefront of innovation. I also want to foster more collaboration between different departments, allowing students to work together on groundbreaking projects that will shape the future. I will work to create a strong network of alumni who can help us in our academic and professional journey.\"', '673f45f6ca64a.png'),
(3, 'Marcus Williams', 'Business Administration', '\"I believe that a well-rounded education is the key to success. As your candidate, I aim to create more opportunities for practical business experience, from internships to case competitions. I will work towards making our curriculum more adaptable to the rapidly changing business world, ensuring that we are equipped with the skills necessary to thrive. Additionally, I want to implement mentorship programs where senior students guide freshmen and help them navigate the challenges of university life.\"', '673f4604097b0.png'),
(4, 'Sarah Davis', ' Medicine', '\"As a student of Medicine, I am passionate about improving the well-being of our community. My manifesto centers on mental health and student support. I plan to advocate for more accessible mental health resources and workshops to address stress, anxiety, and other challenges students face. Additionally, I want to improve communication between students and faculty to ensure that we have the best possible educational experience. My goal is to make sure that every student has the support they need to excel academically and personally.\"', '673f4613579a1.png'),
(5, 'Emma Johnson', ' Arts and Humanities', '\"The arts are the heart of creativity and innovation. As a student in the Arts and Humanities faculty, I want to create a stronger connection between the arts and the broader community. My manifesto focuses on increasing funding for creative projects, facilitating art exhibitions, and encouraging students to share their work with a wider audience. I also aim to improve the mental health resources available for students in the arts, who often face unique pressures. Together, we can foster an environment where creativity and well-being are prioritized.\"', '673f462912c49.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(20) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `faculty` varchar(255) NOT NULL,
  `semester` int(11) NOT NULL,
  `password` text NOT NULL,
  `username` varchar(255) NOT NULL,
  `has_voted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `faculty`, `semester`, `password`, `username`, `has_voted`) VALUES
('STU2024001', 'Evra', 'Salvateara', 'Faculty of Business and Management', 4, '$2y$10$JI56IRjphWshbetFeB8aR.BEbOri5nOxV5zeIUs3M7wK7NPgBbapG', 'EvraSal', 1);

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `vote_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `user_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`vote_id`, `candidate_id`, `user_id`) VALUES
(2, 2, 'STU2024001');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`candidate_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`vote_id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `candidate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
