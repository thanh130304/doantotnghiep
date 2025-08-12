-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th8 10, 2025 lúc 04:24 AM
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
  `Avatar` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`ID`, `FullName`, `Email`, `PhoneNumber`, `Password`, `Avatar`) VALUES
(1, 'Nguyễn Quang Thành', 'thanhadmin2002@gmail.com', '0839783613', '$2y$10$ErC.t//vPOiuTTAZRO5g0.3amIX.e/omjYumIu.7G.UIV22p7ssMa', 'uploads/admin/avatar_1753884907.jpg'),
(2, 'Quốc Anh Admin', 'quocanhadmin@gmail.com', '0987123456', '$2y$10$RU6JPSiKiJR91ompIyQgTOK0DzSgxs0B.3u/DFUK4pER9Y90jRTai', 'https://haycafe.vn/wp-content/uploads/2022/02/Avatar-trang-den.png'),
(3, 'Dũng Admin', 'dungadmin@gmail.com', '0987456123', '$2y$10$.tWJFBVNgsVUz86HuoDereM9rgsUOYvQ3X/cGO6POQBkYQTA8dUQe', 'https://haycafe.vn/wp-content/uploads/2022/02/Avatar-trang-den.png'),
(4, 'Gia Bách', 'bachadmin@gmail.com', '0987123789', '$2y$10$4aO6i2s6vJ9rOTl27y5Wh.uSD3QL8FGUS6NaT0ZcGoNEoj8xDV3mu', 'https://haycafe.vn/wp-content/uploads/2022/02/Avatar-trang-den.png'),
(5, 'Đào Minh Hoàng', 'hoangadmin@gmail.com', '0987456789', '$2y$10$Bc3Z898wvlgfW3JOXMX5euM.pGXiUulg.yvfLgbYV.XnQ8jSGfM.y', 'https://haycafe.vn/wp-content/uploads/2022/02/Avatar-trang-den.png'),
(6, 'Nguyễn Quang Thành C', 'yukinagato234@gmail.com', '0839783612', '$2y$10$fnbyFgv/m.kzbRlVw119O.lcC.yf/OtE1zhmKi7otQFmRSjNlNUyS', 'https://haycafe.vn/wp-content/uploads/2022/02/Avatar-trang-den.png');

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
(1, 'localhostngu', '0987654321', 'occholocalhost@gmail.com', '2000-02-23', 'hanoi', 'cntt', ''),
(2, 'localhostngu', '0987654321', 'occholocalhost@gmail.com', '2000-02-23', 'hanoi', 'cntt', '');

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
(4, 'Đào Minh Hoàng', '0987567890', 2004, 'Hà Nội', 'minhhoang90@gmail.com', 'unknown'),
(5, 'Phạm Gia Bách', '0988776655', 2005, 'Hà Nội', 'bachpg2k5@gmail.com', 'unknown'),
(6, 'Đỗ Quốc Bình', '0987987987', 2002, 'Hà Nội', 'binhdo2002@gmail.com', 'unknown'),
(7, 'Nguyễn Thanh Triều', '0912412087', 2005, 'Hà Nội', 'thanhtrieu2k5@gmail.com', 'unknown'),
(9, 'Phạm Ngọc Sơn', '0921001100', 2004, 'Hà Nội', 'sonpn2004@gmail.com', 'unknown'),
(11, 'Nguyễn Văn An', '0987654312', 2003, 'Hà Nội', 'annv2003@gmail.com', 'unknown'),
(12, 'Đỗ Minh Quân', '0987999111', 2002, 'Hà Nội', 'quando2002@gmail.com', 'unknown'),
(13, 'Nguyễn Thị Cẩm Ly', '0987000111', 2001, 'Hà Nội', 'camly2001fpt@gmail.com', 'unknown'),
(14, 'Lò Đức Minh', '0987612345', 2004, 'Hà Nội', 'ducminh2004@gmail.com', 'unknown'),
(15, 'Nguyễn Trường Nam', '0987654345', 2006, 'Hà Nội', 'truongnam2k6@gmail.com', 'unknown'),
(16, 'Nguyễn Văn Bảo', '0987656789', 2000, 'Hà Nội', 'vanbao2000@gmail.com', 'unknown');

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
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'Mã định danh duy nhất';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
