-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2021-01-06 18:14:57
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
-- 資料表結構 `online_sales_performance`
--

CREATE TABLE `online_sales_performance` (
  `onsp_sn` int(10) NOT NULL,
  `onsp_date` varchar(10) CHARACTER SET utf8 NOT NULL,
  `onsp_year` int(11) NOT NULL,
  `onsp_month` int(11) NOT NULL,
  `onsp_sales_staff` int(11) NOT NULL,
  `onsp_target_number` int(11) NOT NULL COMMENT '目標銷售金額',
  `onsp_target_order` int(11) NOT NULL COMMENT '目標訂單數量',
  `onsp_up_to_standard` int(11) DEFAULT '0',
  `onsp_status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='銷售績效';

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `online_sales_performance`
--
ALTER TABLE `online_sales_performance`
  ADD PRIMARY KEY (`onsp_sn`);

--
-- 在傾印的資料表使用自動增長(AUTO_INCREMENT)
--

--
-- 使用資料表自動增長(AUTO_INCREMENT) `online_sales_performance`
--
ALTER TABLE `online_sales_performance`
  MODIFY `onsp_sn` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
