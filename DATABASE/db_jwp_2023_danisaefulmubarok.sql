-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 16 Jun 2023 pada 11.19
-- Versi Server: 10.4.24-MariaDB
-- PHP Version: 7.1.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_jwp_2023_danisaefulmubarok`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_user`
--

CREATE TABLE `tb_user` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` text NOT NULL,
  `is_active` int(1) NOT NULL,
  `img` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_user`
--

INSERT INTO `tb_user` (`id`, `name`, `email`, `password`, `is_active`, `img`) VALUES
(18, 'Dani Saeful Mubarok', 'danisaefulmubarok@gmail.com', '$2y$10$2Ukt4ZWRIpD28gu6.yd0GOVnWdUzjEZ6d5Bd9cEe7M7cL8iV8CQ6e', 1, 'file_1686905350.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_token`
--

CREATE TABLE `user_token` (
  `id` int(11) NOT NULL,
  `email` varchar(128) NOT NULL,
  `token` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user_token`
--

INSERT INTO `user_token` (`id`, `email`, `token`) VALUES
(17, 'dfoxfox60@gmail.com', '3bNYGW+yTiNRpn3zpVDjvyjKYeswN4XOAB+YyMAdY5g='),
(18, 'dfoxfox60@gmail.com', '/HZGowz+w2Yy2mCKTIUwRGSSOUBejG/0aMMg/D9tI0E='),
(19, 'dfoxfox60@gmail.com', 'Nr7VObbDZfVdNna655O2k2AqdW0TH1xVO1gG8bxFXUk='),
(20, 'dfoxfox60@gmail.com', 'cRx054hckznA/mCkgplPxl1UaWtltOmIRAZimv+Zv2A='),
(21, 'dfoxfox60@gmail.com', 'O1uk6RvAorsR5ki12Q3pHH/lCrpDxfpdR+8XKWIqc+I='),
(23, 'danisaefulmubarok@gmail.com', 'oPJtOdTsc8L/PIL71sWBPKuDAArTmXLQgtPVV1g+zyw=');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_token`
--
ALTER TABLE `user_token`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `user_token`
--
ALTER TABLE `user_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
