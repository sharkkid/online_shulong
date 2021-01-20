-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2021-01-01 17:01:30
-- 伺服器版本： 10.1.38-MariaDB
-- PHP 版本： 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `onlineoem_sql`
--

-- --------------------------------------------------------

--
-- 資料表結構 `online_planting_performance`
--

CREATE TABLE `online_planting_performance` (
  `onpp_sn` int(11) NOT NULL,
  `onpp_date` varchar(20) NOT NULL,
  `onpp_year` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '日期年分',
  `onpp_month` int(11) NOT NULL COMMENT '日期月份',
  `onpp_plant_staff` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '種植人員',
  `onpp_target_number` varchar(1024) CHARACTER SET utf8 NOT NULL COMMENT '目標數量',
  `onpp_actual_number` varchar(1024) CHARACTER SET utf8 NOT NULL COMMENT '實際數量',
  `onpp_up_to_standard` int(11) NOT NULL COMMENT '是否達標',
  `onpp_status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='種植績效';

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `online_planting_performance`
--
ALTER TABLE `online_planting_performance`
  ADD PRIMARY KEY (`onpp_sn`);

--
-- 在傾印的資料表使用自動增長(AUTO_INCREMENT)
--

--
-- 使用資料表自動增長(AUTO_INCREMENT) `online_planting_performance`
--
ALTER TABLE `online_planting_performance`
  MODIFY `onpp_sn` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
