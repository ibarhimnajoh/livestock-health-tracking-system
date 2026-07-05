-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2026 at 10:31 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `livestock_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `animals`
--

CREATE TABLE `animals` (
  `id` int(11) NOT NULL,
  `animal_id` varchar(50) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `species` varchar(50) NOT NULL,
  `breed` varchar(50) DEFAULT NULL,
  `age_months` int(11) DEFAULT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `status` enum('Healthy','Quarantined','Treatment','Exported') DEFAULT 'Healthy',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `animals`
--

INSERT INTO `animals` (`id`, `animal_id`, `owner_id`, `species`, `breed`, `age_months`, `gender`, `status`, `created_at`) VALUES
(4, 'RFID-451-22', 2, 'Ovine', 'Somali Blackhead', 24, 'Male', 'Quarantined', '2026-07-04 21:16:57'),
(5, 'RFID-889-12', 2, 'Ovine', 'Somali Blackhead', 12, 'Female', 'Healthy', '2026-07-04 21:16:57'),
(6, 'RFID-102-44', 3, 'Caprine', 'Galla', 18, 'Male', 'Treatment', '2026-07-04 21:16:57'),
(7, 'RFID-309-88', 4, 'Cameline', 'Barkie', 60, 'Male', 'Healthy', '2026-07-04 21:16:57'),
(8, 'RFID-552-61', 5, 'Bovine', 'Boran', 30, 'Female', 'Healthy', '2026-07-04 21:16:57'),
(9, 'AUS-102-44', 6, 'Bovine', 'Boran', 28, 'Male', 'Exported', '2026-07-04 21:21:38'),
(10, 'AUS-556-12', 6, 'Bovine', 'Zebu', 34, 'Female', 'Healthy', '2026-07-04 21:21:38'),
(11, 'RFID-991-01', 7, 'Cameline', 'Heenyo', 72, 'Female', 'Healthy', '2026-07-04 21:21:38'),
(12, 'RFID-991-02', 7, 'Cameline', 'Hoog', 84, 'Male', 'Healthy', '2026-07-04 21:21:38'),
(13, 'RFID-203-88', 8, 'Ovine', 'Somali Blackhead', 14, 'Male', 'Healthy', '2026-07-04 21:21:38'),
(14, 'RFID-203-89', 8, 'Ovine', 'Somali Blackhead', 16, 'Female', 'Healthy', '2026-07-04 21:21:38'),
(15, 'RFID-203-90', 8, 'Ovine', 'Somali Blackhead', 12, 'Male', 'Quarantined', '2026-07-04 21:21:38'),
(16, 'RFID-774-11', 9, 'Caprine', 'Galla', 20, 'Female', 'Healthy', '2026-07-04 21:21:38'),
(17, 'RFID-774-12', 9, 'Caprine', 'Abgalio', 22, 'Male', 'Treatment', '2026-07-04 21:21:38'),
(19, 'AUS-882-91', 2, 'Bovine', 'Zebu', 42, 'Female', 'Exported', '2026-07-04 21:21:38'),
(20, 'RFID-441-12', 3, 'Ovine', 'Somali Blackhead', 18, 'Male', 'Exported', '2026-07-04 21:21:38'),
(21, 'RFID-559-90', 4, 'Cameline', 'Barkie', 65, 'Male', 'Exported', '2026-07-04 21:21:38'),
(22, 'RFID-112-88', 5, 'Caprine', 'Galla', 24, 'Female', 'Healthy', '2026-07-04 21:21:38'),
(23, 'RFID-667-31', 6, 'Ovine', 'Somali Blackhead', 15, 'Female', 'Healthy', '2026-07-04 21:21:38');

-- --------------------------------------------------------

--
-- Table structure for table `export_permits`
--

CREATE TABLE `export_permits` (
  `id` int(11) NOT NULL,
  `permit_number` varchar(50) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `officer_id` int(11) NOT NULL,
  `destination_country` varchar(100) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `issue_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `export_permits`
--

INSERT INTO `export_permits` (`id`, `permit_number`, `animal_id`, `officer_id`, `destination_country`, `status`, `issue_date`, `created_at`) VALUES
(4, 'PERMIT-2026-003', 4, 3, 'Oman', 'Approved', '2026-07-05', '2026-07-04 21:16:57'),
(5, 'PERMIT-2026-004', 6, 3, 'Saudi Arabia', 'Pending', NULL, '2026-07-04 21:16:57'),
(6, 'PERMIT-2026-005', 8, 3, 'Saudi Arabia', 'Approved', '2026-07-05', '2026-07-04 21:21:38'),
(7, 'PERMIT-2026-006', 9, 3, 'Qatar', 'Pending', NULL, '2026-07-04 21:21:38'),
(8, 'PERMIT-2026-007', 11, 3, 'Egypt', 'Approved', '2026-07-05', '2026-07-04 21:21:38'),
(9, 'PERMIT-2026-008', 12, 3, 'UAE', 'Approved', '2026-07-04', '2026-07-04 21:21:38'),
(10, 'PERMIT-2026-009', 10, 3, 'Saudi Arabia', 'Rejected', NULL, '2026-07-04 21:21:38'),
(11, 'PERMIT-2026-010', 13, 3, 'Oman', 'Pending', NULL, '2026-07-04 21:21:38'),
(12, 'EXP-20260705-30', 21, 1, 'Yemen', 'Approved', '2026-07-05', '2026-07-04 22:21:12'),
(13, 'EXP-20260705-69', 9, 1, 'UAE', 'Approved', '2026-07-05', '2026-07-04 23:23:36');

-- --------------------------------------------------------

--
-- Table structure for table `health_records`
--

CREATE TABLE `health_records` (
  `id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `vet_id` int(11) NOT NULL,
  `diagnosis` text NOT NULL,
  `status` enum('Healthy','Quarantined','Suspended') DEFAULT 'Healthy',
  `inspection_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `health_records`
--

INSERT INTO `health_records` (`id`, `animal_id`, `vet_id`, `diagnosis`, `status`, `inspection_date`, `created_at`) VALUES
(4, 5, 2, 'Foot injury under active medical treatment.', 'Suspended', '2026-07-04', '2026-07-04 21:16:57'),
(5, 8, 2, 'Routine check: Passed export readiness protocol.', 'Healthy', '2026-07-05', '2026-07-04 21:21:38'),
(6, 10, 2, 'Suspected Contagious Caprine Pleuropneumonia (CCPP).', 'Quarantined', '2026-07-05', '2026-07-04 21:21:38'),
(7, 12, 2, 'Brucellosis test negative. Animal fit for international trade.', 'Healthy', '2026-07-04', '2026-07-04 21:21:38'),
(8, 14, 2, 'Mild respiratory infection. Placed under standard 5-day treatment.', 'Quarantined', '2026-07-03', '2026-07-04 21:21:38'),
(9, 9, 2, 'Severe ticks infestation. Quarantine and treatment required.', 'Quarantined', '2026-07-02', '2026-07-04 21:21:38'),
(10, 23, 1, 'waa cafimad qaba wax xanuuna lagu arag', 'Healthy', '2026-07-05', '2026-07-04 22:15:09'),
(11, 23, 1, 'waxa laga helay xanun', '', '2026-07-05', '2026-07-04 23:20:17'),
(12, 23, 1, 'waa cafimad qaba', 'Healthy', '2026-07-05', '2026-07-04 23:22:41');

-- --------------------------------------------------------

--
-- Table structure for table `owners`
--

CREATE TABLE `owners` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `region` varchar(50) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `owners`
--

INSERT INTO `owners` (`id`, `name`, `phone`, `email`, `region`, `address`, `created_at`) VALUES
(2, 'Jaamac Maxamed Cilmi', '0634441111', NULL, '', 'Hargeisa', '2026-07-04 21:12:24'),
(3, 'Khadr Cabdi Cali', '0634442222', NULL, '', 'Burao', '2026-07-04 21:12:24'),
(4, 'Deqa Omar Hassan', '0634443333', NULL, '', 'Berbera', '2026-07-04 21:12:24'),
(5, 'Mustafe Axmed Yoonis', '0634444444', NULL, '', 'Borama', '2026-07-04 21:12:24'),
(6, 'Hodman Siciid Faarax', '0634445555', NULL, '', 'Erigavo', '2026-07-04 21:12:24'),
(7, 'Jaamac Maxamed Cilmi', '0634441111', 'jaamac@email.com', 'Maroodi Jeex', 'Hargeisa, Somaliland', '2026-07-04 21:16:57'),
(8, 'Khadr Cabdi Cali', '0634442222', 'khadar@email.com', 'Togdheer', 'Burao, Somaliland', '2026-07-04 21:16:57'),
(9, 'Deqa Omar Hassan', '0634443333', 'deqa@email.com', 'Saaxil', 'Berbera, Somaliland', '2026-07-04 21:16:57'),
(10, 'Mustafe Axmed Yoonis', '0634444444', 'mustafe@email.com', 'Awdal', 'Borama, Somaliland', '2026-07-04 21:16:57'),
(11, 'Hodman Siciid Faarax', '0634445555', 'hodan@email.com', 'Sanaag', 'Erigavo, Somaliland', '2026-07-04 21:16:57'),
(12, 'Garaad Cabdiweli Cilmi', '0635551111', 'garaad@email.com', 'Sool', 'Las Anod, Somaliland', '2026-07-04 21:21:37'),
(13, 'Fadumo Yaasiin Cumar', '0635552222', 'fadumo@email.com', 'Bari', 'Bosaso, Somalia', '2026-07-04 21:21:37'),
(14, 'Maxamed Cali Shirdon', '0635553333', 'shirdon@email.com', 'Mudug', 'Galkacyo, Somalia', '2026-07-04 21:21:37'),
(15, 'Caaisha Cilmi Nuur', '0635554444', 'caaisha@email.com', 'Nugaal', 'Garowe, Somalia', '2026-07-04 21:21:37'),
(16, 'Hamse Ahmad Cabdilaahi', '4567239', 'hamse@gmail.com', 'Maroodi Jeex', 'Saaxil', '2026-07-04 22:33:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Veterinary Officer','Export Officer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Ibrahim Hassan', 'admin@authority.gov', '1111', 'Admin', '2026-07-04 19:16:03'),
(2, 'Dr. Hootho Abdikiin', 'vet@authority.gov', '2222', 'Veterinary Officer', '2026-07-04 19:16:03'),
(3, 'Safa Ahmad', 'export@authority.gov', '3333', 'Export Officer', '2026-07-04 19:16:03');

-- --------------------------------------------------------

--
-- Table structure for table `vaccinations`
--

CREATE TABLE `vaccinations` (
  `id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `vaccine_name` varchar(100) NOT NULL,
  `batch_number` varchar(50) DEFAULT NULL,
  `vaccinated_at` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccinations`
--

INSERT INTO `vaccinations` (`id`, `animal_id`, `vaccine_name`, `batch_number`, `vaccinated_at`, `expiry_date`, `created_at`) VALUES
(4, 8, 'Foot and Mouth Disease', 'FMD-2026B', '2026-04-10', '2027-04-10', '2026-07-04 21:21:38'),
(5, 9, 'Camel Pox Vaccine', 'CPX-881', '2026-05-12', '2027-05-12', '2026-07-04 21:21:38'),
(6, 11, 'Anthrax Vaccine', 'ANT-009', '2026-03-01', '2027-03-01', '2026-07-04 21:21:38'),
(7, 12, 'PPR Vaccine', 'PPR-8821', '2026-02-14', '2027-02-14', '2026-07-04 21:21:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `animals`
--
ALTER TABLE `animals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `animal_id` (`animal_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `export_permits`
--
ALTER TABLE `export_permits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permit_number` (`permit_number`),
  ADD KEY `animal_id` (`animal_id`),
  ADD KEY `officer_id` (`officer_id`);

--
-- Indexes for table `health_records`
--
ALTER TABLE `health_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `animal_id` (`animal_id`),
  ADD KEY `vet_id` (`vet_id`);

--
-- Indexes for table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vaccinations`
--
ALTER TABLE `vaccinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `animal_id` (`animal_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `animals`
--
ALTER TABLE `animals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `export_permits`
--
ALTER TABLE `export_permits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `health_records`
--
ALTER TABLE `health_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vaccinations`
--
ALTER TABLE `vaccinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `animals`
--
ALTER TABLE `animals`
  ADD CONSTRAINT `animals_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `export_permits`
--
ALTER TABLE `export_permits`
  ADD CONSTRAINT `export_permits_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `export_permits_ibfk_2` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `health_records`
--
ALTER TABLE `health_records`
  ADD CONSTRAINT `health_records_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `health_records_ibfk_2` FOREIGN KEY (`vet_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `vaccinations`
--
ALTER TABLE `vaccinations`
  ADD CONSTRAINT `vaccinations_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
