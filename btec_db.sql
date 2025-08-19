-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th8 19, 2025 lúc 09:03 AM
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
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `ID` int NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Avatar` varchar(150) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`ID`, `FullName`, `Email`, `PhoneNumber`, `Password`, `Avatar`, `role`) VALUES
(2, 'Quốc Anh Admin', 'quocanhadmin@gmail.com', '0987123456', '$2y$10$HUYYrMLoT5TAoGZUTsH3a.1HHYafIylXEeUdF5S8.CEkT9QYWOqJq', 'https://haycafe.vn/wp-content/uploads/2022/02/Avatar-trang-den.png', 'admin'),
(3, 'Dũng Admin', 'dungadmin@gmail.com', '0987456123', '$2y$10$.tWJFBVNgsVUz86HuoDereM9rgsUOYvQ3X/cGO6POQBkYQTA8dUQe', 'https://haycafe.vn/wp-content/uploads/2022/02/Avatar-trang-den.png', 'admin'),
(4, 'Gia Bách', 'bachadmin@gmail.com', '0987123789', '$2y$10$4aO6i2s6vJ9rOTl27y5Wh.uSD3QL8FGUS6NaT0ZcGoNEoj8xDV3mu', 'https://haycafe.vn/wp-content/uploads/2022/02/Avatar-trang-den.png', 'admin'),
(5, 'Đào Minh Hoàng', 'hoangadmin@gmail.com', '0987456789', '$2y$10$Bc3Z898wvlgfW3JOXMX5euM.pGXiUulg.yvfLgbYV.XnQ8jSGfM.y', 'https://haycafe.vn/wp-content/uploads/2022/02/Avatar-trang-den.png', 'admin'),
(6, 'Nguyễn Quang Thành C', 'yukinagato234@gmail.com', '0839783612', '$2y$10$fnbyFgv/m.kzbRlVw119O.lcC.yf/OtE1zhmKi7otQFmRSjNlNUyS', 'https://haycafe.vn/wp-content/uploads/2022/02/Avatar-trang-den.png', 'admin'),
(7, 'Thanh Admin', 'quangthanh130302@gmail.com', '0839783612', '$2y$10$UXAhRyiIpHcK8BXdOM4N/u/6gkSeaWDMAcO1RE57LpQew3SoQNA7m', 'https://haycafe.vn/wp-content/uploads/2022/02/Avatar-trang-den.png', 'admin'),
(8, 'Nguyễn Thành', 'quangthanh130305@gmail.com', '0987654000', '$2y$10$1L6qXwVVT/ObSjPL0Q6GouWd.HQjvgJABVtJ3.Tpw79UifvIiu116', 'Uploads/admin/avatar_8_1755529000.jpg', 'admin'),
(9, 'Nguyễn Văn Thành', 'manager@gmail.com', '0839783612', '$2y$10$uX/uNesnr22S396GnID3q.67ZhCB3tOPYlmv7s81GaUI0xQGezoR.', NULL, 'manager'),
(10, 'Quốc Anh', 'manager1@gmail.com', '0912000111', '$2y$10$TyDOCbKMCfrb2Y9rMJwKceQsgE.70Hmb8T1taHB4Pux6PB7Lv82ia', NULL, 'manager');

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
(1, 'Nguyễn Quang Thành', '0839783612', 2005, 'Hà Nội', 'quangthanh130304@gmail.com', 'interested'),
(2, 'Dương Quốc Anh', '0987564567', 2004, 'Hà Nội', 'quocanh2004@gmail.com', 'no_need'),
(3, 'Hoàng Mạnh Dũng', '0985674565', 2000, 'Hà Nội', 'dung2khn@gmail.com', 'contacted'),
(4, 'Đào Minh Hoàng', '0987567890', 2004, 'Hà Nội', 'minhhoang90@gmail.com', 'contacted'),
(5, 'Phạm Gia Bách', '0988776655', 2005, 'Hà Nội', 'bachpg2k5@gmail.com', 'not_contacted'),
(6, 'Đỗ Quốc Bình', '0987987987', 2002, 'Hà Nội', 'binhdo2002@gmail.com', 'unknown'),
(7, 'Nguyễn Thanh Triều', '0912412087', 2005, 'Hà Nội', 'thanhtrieu2k5@gmail.com', 'contacted'),
(9, 'Phạm Ngọc Sơn', '0921001100', 2004, 'Hà Nội', 'sonpn2004@gmail.com', 'contacted'),
(11, 'Nguyễn Văn An', '0987654312', 2003, 'Hà Nội', 'annv2003@gmail.com', 'contacted'),
(12, 'Đỗ Minh Quân', '0987999111', 2002, 'Hà Nội', 'quando2002@gmail.com', 'contacted'),
(13, 'Nguyễn Thị Cẩm Ly', '0987000111', 2001, 'Hà Nội', 'camly2001fpt@gmail.com', 'no_need'),
(14, 'Lò Đức Minh', '0987612345', 2004, 'Hà Nội', 'ducminh2004@gmail.com', 'no_need'),
(15, 'Nguyễn Trường Nam', '0987654345', 2006, 'Hà Nội', 'truongnam2k6@gmail.com', 'interested'),
(16, 'Nguyễn Văn Bảo', '0987656789', 2000, 'Hà Nội', 'vanbao2000@gmail.com', 'not_contacted'),
(17, 'Trần Đức Nam', '0987678000', 2007, 'Hà Nội', 'toilanam2007@gmail.com', 'interested'),
(18, 'Nguyễn Trường Nam', '0987654345', 2002, 'Hà Nội', 'truongnam2k6@gmail.com', 'unknown'),
(19, 'Nguyễn Văn A', '0999888777', 2005, 'Đà Nẵng', 'vannguyena@gmail.com', 'unknown'),
(20, 'Nguyễn Văn C', '0999888666', 2007, 'TP.Hồ Chí Minh', 'vannguyenc@gmail.com', 'unknown'),
(21, 'Nguyễn Trường D', '0987654349', 2004, 'Cần Thơ', 'truongnam2k4@gmail.com', 'unknown'),
(22, 'Nguyễn Văn E', '0999888555', 2008, 'Hà Nội', 'vannguyene@gmail.com', 'unknown'),
(23, 'Nguyễn Trường G', '0987654350', 2005, 'Đà Nẵng', 'truongg2005@gmail.com', 'unknown'),
(24, 'Nguyễn Văn Y', '0999888000', 1999, 'TP.Hồ Chí Minh', 'vannguyeny@gmail.com', 'unknown');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Chỉ mục cho bảng `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin`
--
ALTER TABLE `admin`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'Mã định danh duy nhất';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
