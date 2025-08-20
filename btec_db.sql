-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th8 20, 2025 lúc 01:16 AM
-- Phiên bản máy phục vụ: 8.0.30
-- Phiên bản PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `btec_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `registrations`
--

CREATE TABLE `registrations` (
  `id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `campus` varchar(100) NOT NULL,
  `program_of_interest` varchar(100) NOT NULL,
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `registrations`
--

INSERT INTO `registrations` (`id`, `full_name`, `phone_number`, `email`, `date_of_birth`, `campus`, `program_of_interest`, `notes`) VALUES
(3, 'Nguyen Quang Thanh', '0839783612', 'quangthanh130302@gmail.com', '2005-03-13', 'hanoi', 'cntt', ''),
(4, 'nguyễn văn A', '0987555444', 'zeus@gmail.com', '2001-03-13', 'hcm', 'design', 'nope');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `staff`
--

CREATE TABLE `staff` (
  `ID` int NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'staff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `staff`
--

INSERT INTO `staff` (`ID`, `FullName`, `Email`, `PhoneNumber`, `Password`, `role`) VALUES
(13, 'Nguyễn Gia Bách', 'bachstaff@gmail.com', '0987666555', '$2y$10$ZBQ1yf/IwUMKuWk5wxzPnekMMSYZ6sg6qyRu7BvjjjFx0eczRLeWm', 'staff'),
(14, 'Hoàng Mạnh Quốc', 'quocmanager@gmail.com', '0987555444', '$2y$10$uHvEevmKGb9BZLEXQlBJ8u/LMK3Q9iKJ.L1nhBUMWSyNaRz0eZXCS', 'manager'),
(15, 'Hoàng Quốc Việt', 'viethq@gmail.com', '0987444333', '$2y$10$f0ao3MYR566doR3k4RDCTeE9nyNe.IhQGfv0GYqvHu6FD0mwS5d4C', 'staff'),
(16, 'Đỗ Mạnh Quân', 'quandm@gmail.com', '0987333222', '$2y$10$n83I7.XtSm6GeMtKPT2kVuh1zOx4tBT35ELkPcY0E/0SLT7znSUB2', 'manager');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL COMMENT 'Mã định danh duy nhất',
  `full_name` varchar(255) NOT NULL COMMENT 'Họ và tên của ứng viên',
  `phone_number` varchar(20) NOT NULL COMMENT 'Số điện thoại của ứng viên',
  `year_of_birth` int NOT NULL COMMENT 'Năm sinh của ứng viên',
  `facility` varchar(50) NOT NULL COMMENT 'Cơ sở quan tâm',
  `email` varchar(255) NOT NULL COMMENT 'Địa chỉ email cá nhân',
  `status` enum('unknown','not_contacted','contacted','interested','no_need') DEFAULT 'unknown'
) ;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `full_name`, `phone_number`, `year_of_birth`, `facility`, `email`, `status`) VALUES
(1, 'Nguyễn Quang Thành', '0839783612', 2004, 'Hà Nội', 'quangthanh130304@gmail.com', 'interested'),
(23, 'Nguyễn Trường G', '0987654350', 2005, 'Đà Nẵng', 'truongg2005@gmail.com', 'unknown'),
(26, 'Nguyễn Thị Cẩm Ly', '0987999115', 2002, 'Hà Nội', 'camly2002@gmail.com', 'unknown'),
(27, 'Đỗ Minh Quân', '0987000222', 2004, 'TP.Hồ Chí Minh', 'quandominh@gmail.com', 'unknown');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `staff`
--
ALTER TABLE `staff`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'Mã định danh duy nhất';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
