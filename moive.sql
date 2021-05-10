/*
 Navicat Premium Data Transfer

 Source Server         : BrewMysql
 Source Server Type    : MySQL
 Source Server Version : 80023
 Source Host           : localhost:3306
 Source Schema         : moive

 Target Server Type    : MySQL
 Target Server Version : 80023
 File Encoding         : 65001

 Date: 10/05/2021 11:28:52
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for banners
-- ----------------------------
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图片链接',
  `sort` int NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否展示',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for cinemas
-- ----------------------------
DROP TABLE IF EXISTS `cinemas`;
CREATE TABLE `cinemas` (
  `cinemaId` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cityId` int DEFAULT '0' COMMENT '城市ID',
  `cinemaName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '影院名称',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '影院地址',
  `latitude` double(9,5) DEFAULT '0.00000' COMMENT '纬度',
  `longitude` double(9,5) DEFAULT '0.00000' COMMENT '经度',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '影院电话',
  `regionName` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '地区名称',
  `areaId` int DEFAULT '0' COMMENT '地区ID',
  `isAcceptSoonOrder` tinyint(1) DEFAULT '0' COMMENT '是否支持秒出票，0为不支持，1为支持',
  `upDiscountRate` double(4,2) NOT NULL DEFAULT '0.00' COMMENT '当价格大于等于39元时候',
  `downDiscountRate` double(4,2) NOT NULL DEFAULT '0.00' COMMENT '当价格小于39元时候',
  PRIMARY KEY (`cinemaId`)
) ENGINE=InnoDB AUTO_INCREMENT=88886954 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for city_areas
-- ----------------------------
DROP TABLE IF EXISTS `city_areas`;
CREATE TABLE `city_areas` (
  `areaId` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cityId` int DEFAULT '0' COMMENT '城市ID',
  `areaName` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '地区名称',
  PRIMARY KEY (`areaId`)
) ENGINE=InnoDB AUTO_INCREMENT=3780 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for city_filmes
-- ----------------------------
DROP TABLE IF EXISTS `city_filmes`;
CREATE TABLE `city_filmes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cityId` int DEFAULT '0' COMMENT '城市ID',
  `filmId` int NOT NULL DEFAULT '0' COMMENT '影片id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87574 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for citys
-- ----------------------------
DROP TABLE IF EXISTS `citys`;
CREATE TABLE `citys` (
  `cityId` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pinYin` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'pinYin',
  `regionName` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '城市名',
  PRIMARY KEY (`cityId`)
) ENGINE=InnoDB AUTO_INCREMENT=5864 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for configs
-- ----------------------------
DROP TABLE IF EXISTS `configs`;
CREATE TABLE `configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `namespace` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '空间',
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'key',
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'value',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for filmes
-- ----------------------------
DROP TABLE IF EXISTS `filmes`;
CREATE TABLE `filmes` (
  `filmId` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '影片id',
  `grade` int NOT NULL DEFAULT '0' COMMENT '评分',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '影片名',
  `duration` int NOT NULL DEFAULT '0' COMMENT '时长，分钟',
  `publishDate` timestamp NOT NULL COMMENT '影片上映日期',
  `director` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '导演',
  `cast` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '主演',
  `intro` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '简介',
  `versionTypes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '上映类型',
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '语言',
  `filmTypes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '影片类型',
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '海报URL地址',
  `like` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '想看人数',
  `showStatus` smallint NOT NULL COMMENT '放映状态：1 正在热映。2 即将上映',
  PRIMARY KEY (`filmId`)
) ENGINE=InnoDB AUTO_INCREMENT=1369778 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `thirdOrderId` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '接入方的订单号',
  `uid` int NOT NULL DEFAULT '0' COMMENT '用户id',
  `cinemaId` int NOT NULL DEFAULT '0' COMMENT '影院id',
  `filmId` int NOT NULL DEFAULT '0' COMMENT '影片id',
  `showId` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '场次标识',
  `appKey` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '下单appKey',
  `orderStatus` int NOT NULL DEFAULT '0' COMMENT '订单状态：2-受理中，3-待出票，4-已出票待结算，5-已结算，10-订单关闭',
  `orderStatusStr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '订单状态说明',
  `initPrice` int NOT NULL DEFAULT '0' COMMENT '订单市场价：分',
  `orderPrice` int NOT NULL DEFAULT '0' COMMENT '订单成本价：分，接入方可拿次字段作为下单成本价',
  `seat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '座位：英文逗号隔开',
  `orderNum` int NOT NULL DEFAULT '0' COMMENT '座位数',
  `reservedPhone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '下单预留手机号码',
  `readyTicketTime` timestamp NULL DEFAULT NULL COMMENT '待出票时间',
  `ticketTime` timestamp NULL DEFAULT NULL COMMENT '出票时间',
  `closeTime` timestamp NULL DEFAULT NULL COMMENT '关闭时间',
  `closeCause` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '关闭原因',
  `payType` smallint DEFAULT '0' COMMENT '支付方式',
  `payOrder` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '支付订单号',
  `ticketCode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '取票码，type为1时，为字符串，type为2时，为取票码原始截图。 理论上一个取票码包含各字符串和原始截图， 原始截图可能不和字符串同步返回，有滞后性。',
  `ticketImage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '取票码原始截图',
  `acceptChangeSeat` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否允许调座',
  `hallName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '影厅名',
  `showTime` timestamp NOT NULL COMMENT '放映时间',
  `showVersionType` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '场次类型',
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '语言',
  `planType` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '影厅类型 2D 3D',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '渠道ID',
  `payPrice` int NOT NULL DEFAULT '0' COMMENT '支付金额：分',
  `filmeName` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '电影名称',
  `filmePic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '电影图片',
  `cinemaName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '影院名称',
  `cinemaAddress` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '影院名称',
  `latitude` double(9,5) DEFAULT '0.00000' COMMENT '纬度',
  `longitude` double(9,5) DEFAULT '0.00000' COMMENT '经度',
  `cinemaPhone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '影院电话',
  `settle_amount` int DEFAULT '0' COMMENT '佣金',
  `real_commission` int DEFAULT '0' COMMENT '实际佣金',
  PRIMARY KEY (`thirdOrderId`)
) ENGINE=InnoDB AUTO_INCREMENT=9223372036854775808 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for show_dates
-- ----------------------------
DROP TABLE IF EXISTS `show_dates`;
CREATE TABLE `show_dates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `filmId` int NOT NULL COMMENT '影片id',
  `cinemaId` int NOT NULL DEFAULT '0' COMMENT '影院id',
  `cityId` int DEFAULT '0' COMMENT '城市ID',
  `date` char(10) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '城市ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for shows
-- ----------------------------
DROP TABLE IF EXISTS `shows`;
CREATE TABLE `shows` (
  `showId` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '场次标识',
  `cinemaId` int NOT NULL DEFAULT '0' COMMENT '影院id',
  `hallName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '影厅名',
  `filmId` int NOT NULL DEFAULT '0' COMMENT '影片id',
  `filmName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '影片名字',
  `duration` int NOT NULL DEFAULT '0' COMMENT '时长,分钟',
  `showTime` timestamp NOT NULL COMMENT '放映时间',
  `stopSellTime` timestamp NOT NULL COMMENT '停售时间',
  `showVersionType` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '场次类型',
  `netPrice` int NOT NULL COMMENT '参考价，单位：分',
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '语言',
  `planType` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '影厅类型 2D 3D',
  `payPrice` int NOT NULL DEFAULT '0' COMMENT '支付金额：分',
  `scheduleArea` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '支付金额：分',
  `cityId` int NOT NULL DEFAULT '0' COMMENT '城市ID',
  UNIQUE KEY `shows_showid_unique` (`showId`),
  KEY `cinemaId` (`cinemaId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for stak_log
-- ----------------------------
DROP TABLE IF EXISTS `stak_log`;
CREATE TABLE `stak_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '事件',
  `start_time` timestamp NULL DEFAULT NULL COMMENT '开始时间',
  `end_time` timestamp NULL DEFAULT NULL COMMENT '结束时间',
  `errorJson` json DEFAULT NULL COMMENT '错误信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `uid` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '昵称',
  `headimgurl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `openid` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'openid',
  `mini_openid` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '小程序openid',
  `unionid` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'unionid',
  `wx_id` int DEFAULT '0' COMMENT '公众号ID',
  `accounts_id` int DEFAULT '0' COMMENT '关联的账户Id',
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '默认手机号',
  `pid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '渠道ID',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=425796 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
