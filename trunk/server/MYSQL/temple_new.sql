-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- 主機: localhost
-- 建立日期: Sep 28, 2012, 08:07 AM
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
  `m_name` varchar(20) default NULL,
  `m_god` varchar(20) default NULL,
  `m_address` varchar(80) default NULL,
  `m_lat` float default NULL,
  `m_lng` float default NULL,
  PRIMARY KEY  (`m_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=695 ;
