

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `userName` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `date_of_joining` date DEFAULT NULL,
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin` (`id`, `email`, `first_name`, `last_name`, `userName`, `password`, `phone`, `role`, `date_of_joining`, `last_login`, `department`) VALUES
(1, 'admin1@example.com', 'Admin1', 'One', 'adminone', 'adminpass1', '1234567890', 'superadmin', '2024-01-10', '2025-07-29 09:48:32', 'IT'),
(2, 'admin2@example.com', 'Admin2', 'Two', 'admintwo', 'adminpass2', '0987654321', 'editor', '2024-01-11', '2024-01-21 05:20:25', 'HR'),
(3, 'admin3@example.com', 'Admin3', 'Three', 'adminthree', 'adminpass3', '1122334455', 'moderator', '2024-01-12', '2024-01-22 03:30:45', 'Finance'),
(4, 'admin4@example.com', 'Admin4', 'Four', 'adminfour', 'adminpass4', '6677889900', 'superadmin', '2024-01-13', '2024-01-23 06:45:55', 'Operations'),
(5, 'admin5@example.com', 'Admin5', 'Five', 'adminfive', 'adminpass5', '4433221100', 'editor', '2024-01-14', '2024-01-24 08:50:35', 'Marketing');



CREATE TABLE `bus` (
  `bus_code` char(4) NOT NULL,
  `source_location` varchar(255) NOT NULL,
  `end_location` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `coach_type` varchar(255) DEFAULT NULL,
  `bus_type` varchar(255) DEFAULT NULL,
  `via_road` varchar(255) DEFAULT NULL,
  `max_price` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `bus` (`bus_code`, `source_location`, `end_location`, `capacity`, `coach_type`, `bus_type`, `via_road`, `max_price`) VALUES
('0001', 'Dhaka', 'Benapole', 24, 'AC', 'Business Class', 'Via Faridpur/Magura', 1100),
('0002', 'Dhaka', 'Benapole', 32, 'Non AC', 'Economy Class', 'Via Kalna', 900),
('0003', 'Rajshahi', 'Khulna', 24, 'AC', 'Business Class', 'Via Kalna', 1100),
('0004', 'Barisal', 'Jessore', 32, 'Non AC', 'Economy Class', 'Via Faridpur/Magura', 900),
('0005', 'Comilla', 'Dhaka', 24, 'AC', 'Business Class', 'Via Kalna', 1100),
('0006', 'Mymensingh', 'Tangail', 32, 'Non AC', 'Economy Class', 'Via Faridpur/Magura', 900),
('0007', 'Cox\'s Bazar', 'Chittagong', 24, 'AC', 'Business Class', 'Via Kalna', 1100),
('0008', 'Rangpur', 'Bogra', 32, 'Non AC', 'Economy Class', 'Via Faridpur/Magura', 900),
('0009', 'Khulna', 'Barisal', 24, 'AC', 'Business Class', 'Via Kalna', 1100),
('0010', 'Gazipur', 'Dhaka', 32, 'Non AC', 'Economy Class', 'Via Faridpur/Magura', 900),
('1111', 'Dhaka', 'Benapole', 24, 'AC', 'Business-Class', 'Padma-kalna', 1100);


CREATE TABLE `bus_stop` (
  `id` int(11) NOT NULL,
  `bus_code` char(4) NOT NULL,
  `location` varchar(255) NOT NULL,
  `time_1` time NOT NULL,
  `time_2` time NOT NULL,
  `location_level` int(11) NOT NULL,
  `point_location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `bus_stop` (`id`, `bus_code`, `location`, `time_1`, `time_2`, `location_level`, `point_location`) VALUES
(18, '0007', 'Dhaka', '10:00:00', '15:00:00', 1, 'MALIBAGH BAZAR'),
(19, '0007', 'Narayanganj', '11:00:00', '14:00:00', 2, 'Narayanganj bazar'),
(20, '0007', 'Comilla', '12:00:00', '13:00:00', 3, 'Comilla bazar'),
(21, '0002', 'Dhaka', '09:00:00', '20:00:00', 1, 'MALIBAGH'),
(22, '0002', 'Jessore', '13:00:00', '16:00:00', 2, 'NEW MARKET'),
(23, '0003', 'Sylhet', '10:00:00', '10:30:00', 1, 'SYLHET BAZAR'),
(29, '0002', 'Benapole', '14:00:00', '15:00:00', 3, 'BENAPOLE BAZAR'),
(30, '0001', 'Dhaka', '08:00:00', '17:45:00', 1, 'MALIBAGH'),
(31, '0001', 'Jessore', '11:45:00', '14:30:00', 2, 'NEW MARKET'),
(32, '0001', 'Benapole', '12:45:00', '13:30:00', 3, 'BENAPOLE BAZAR');


CREATE TABLE `date_tracking` (
  `id` int(11) NOT NULL,
  `min_date` varchar(255) NOT NULL,
  `max_date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_signup` date NOT NULL DEFAULT curdate(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `user` (`id`, `email`, `first_name`, `last_name`, `user_name`, `password`, `phone`, `date_of_signup`, `last_login`) VALUES
(1, 'john.doe@example.com', 'John', 'Doe', 'johndoe', 'password123', '1234567890', '2024-01-15', '2024-12-03 08:29:41'),
(2, 'jane.smith@example.com', 'Jane', 'Smith', 'janesmith', 'securepass', '0987654321', '2024-01-16', '2024-01-21 05:20:25'),
(3, 'mark.jones@example.com', 'Mark', 'Jones', 'markjones', 'mypassword', '1122334455', '2024-01-17', '2024-01-22 03:30:45'),
(4, 'emily.davis@example.com', 'Emily', 'Davis', 'emilydavis', 'emilypassword', '6677889900', '2024-01-18', '2024-01-23 06:45:55'),
(6, 'rakib@gmail.com', 'rakib', 'islam', 'rakib', '1234', '01521777777', '2024-11-21', '2025-07-29 10:21:38'),
(7, 'saif@gmail.com', 'saif', 'khan', 'saifKhan', '1234', '018457465', '2024-11-21', '2024-11-21 19:02:06'),
(8, 'test5@testmail.com', 'test', '5', 'test5', '1234', '01275456477', '2024-12-01', '2024-12-01 11:17:07');


ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);


ALTER TABLE `bus`
  ADD PRIMARY KEY (`bus_code`);


ALTER TABLE `bus_stop`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bus_code` (`bus_code`);


ALTER TABLE `date_tracking`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);




ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


ALTER TABLE `bus_stop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;


ALTER TABLE `date_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
