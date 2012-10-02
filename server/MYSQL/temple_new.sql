-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- 主機: localhost
-- 建立日期: Oct 02, 2012, 05:03 PM
-- 伺服器版本: 5.0.51
-- PHP 版本: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 資料庫: `temple`
-- 

-- --------------------------------------------------------

-- 
-- 資料表格式： `temple`
-- 

CREATE TABLE `temple` (
  `m_id` int(11) NOT NULL auto_increment,
  `m_name` varchar(20) NOT NULL,
  `m_god` varchar(20) NOT NULL,
  `m_address` varchar(80) NOT NULL,
  `m_lat` float NOT NULL,
  `m_lng` float NOT NULL,
  PRIMARY KEY  (`m_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=695 ;

-- 
-- 列出以下資料庫的數據： `temple`
-- 

