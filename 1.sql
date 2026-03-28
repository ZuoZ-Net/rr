-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2026-03-28 15:03:02
-- 服务器版本： 5.7.44-log
-- PHP 版本： 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `jiexi`
--
CREATE DATABASE IF NOT EXISTS `jiexi` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `jiexi`;

-- --------------------------------------------------------

--
-- 表的结构 `zhi_admin`
--

CREATE TABLE `zhi_admin` (
  `id` int(11) NOT NULL,
  `adminname` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `qq` varchar(255) DEFAULT NULL,
  `key` varchar(32) DEFAULT NULL,
  `loginip` varchar(255) DEFAULT NULL,
  `logincity` varchar(255) DEFAULT NULL,
  `logintime` datetime DEFAULT NULL,
  `loginurl` varchar(500) DEFAULT 'admin',
  `addtime` datetime DEFAULT NULL,
  `state` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zhi_admin`
--

INSERT INTO `zhi_admin` (`id`, `adminname`, `password`, `qq`, `key`, `loginip`, `logincity`, `logintime`, `loginurl`, `addtime`, `state`) VALUES
(1, 'root', '$2y$10$4ihB5daVL6zqAzlrmDK5BOj6tzgopLb5vFqoSwd78nSNkiqgT.hkG', '2985639879', '77c8148c82d3f6821537246f3056ad13', '192.168.1.100', '127.0.0.1', '2026-03-28 14:51:54', 'admin', '2024-01-01 01:01:01', '1');

-- --------------------------------------------------------

--
-- 表的结构 `zhi_cookie`
--

CREATE TABLE `zhi_cookie` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `act` varchar(255) NOT NULL,
  `yc` varchar(255) NOT NULL,
  `pt` varchar(255) NOT NULL,
  `cookie` text NOT NULL,
  `addtime` datetime DEFAULT NULL,
  `updtime` datetime DEFAULT NULL,
  `state` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zhi_cookie`
--

INSERT INTO `zhi_cookie` (`id`, `name`, `act`, `yc`, `pt`, `cookie`, `addtime`, `updtime`, `state`) VALUES
(1, 'MGTV', 'mgtv', '0', '17772221446----Brr@1214', 'uuid=102f2c69862a5d1db510d9e540a065cf; HDCN=4740199EB1AA3F7182026D14BBF7B3C1-523955787; loginAccount=17772221446; vipStatus=1', '2025-11-24 16:34:00', '2026-03-27 02:44:54', '1'),
(6, 'QQ_2025-12-20', 'qq', '0', 'qq', 'video_guid=f18502a40b29c990; qq_domain_video_guid_verify=f18502a40b29c990; _qimei_uuid42=1a319130612100e9d931ec47ba571d3007ea8c9784; _qimei_fingerprint=d402452579be4490e8c87b64a5023f18; video_platform=2; _qimei_q36=; _qimei_h38=527fd57cd931ec47ba571d3002000000e1a319; pgv_info=ssid=s2614909000; pgv_pvid=6558970603; ts_uid=975908492; bucket_id=9231006; _qimei_i_3=66f3658a975d518a90c1f8310d8626e1f7bdf1a3100e5684bc8a2a5c75c1716c686062943989e2969f88; lcad_o_minduid=i4wB6ahud3rIA7WN61qrum0bk0BD4dNo; lcad_appuser=18F7A097B967E960; lcad_Lturn=300; lcad_LKBturn=15; lcad_LPVLturn=960; lcad_LPLFturn=52; _qpsvr_localtk=0.06369709178014349; _qimei_q32=; player_rfid=077b6ad556ab5cd3e2f5d346d10e51dc_1774436781; player_spts=32|0|false|false|true|true|true; lcad_LPSJturn=266; lcad_LBSturn=546; lcad_LVINturn=671; lcad_LPHLSturn=803; lcad_LDERturn=779; tab_experiment_str=100000#111741690#112055912#112034724; ts_last=v.qq.com/; RK=OhbPmPUVWL; ptcz=5eb7b8b9bb533ce86288929b565c52ffaedf37a407826332be1fe1df179e9dfc; ptag=; main_login=qq; _new_next_refresh_time=7199; vqq_vuserid=3557414298; vqq_vusession=CI2IR6HhfSN3kVsgYZ_S9g.N; vqq_access_token=EB10BA497EEA500259E03C1461C74225; vqq_refresh_token=4582F819A7B5C4E4B386AB7AF0391137; vqq_openid=8A5AC388F11231F38A68CB441E205702; vqq_appid=101483052; v_vurefresh=BbJgrwx0dnGcs67D2zh_SS5epfxOdRSV5WXCEWdLes-mW5zQO2ilML0qe6s-oL0Do2O1vkdlnIO0lOaKwtHW-0ftMAn7; v_main_login=qq; v_t_appid=101483052; v_t_openid=8A5AC388F11231F38A68CB441E205702; v_t_access_token=6C4E046E1C802657112A75720A942E0C; v_t_refresh_token=4582F819A7B5C4E4B386AB7AF0391137; v_vuserid=3557414298; v_vusession=BOEgqXcjUUgIPC2B-aXy4QiArT8xIrgeOa7whlUHaNqW8IyDksT1lkFluyPzbmXYrOxzPlTcY3vav1LxokZeIvE8Oq0UxdTt0kRhekdgLaAetlWO4zZEewhn1cASKwVi17gNHUo15Ew2jXGXyEAUM46JDg6Y1p6aAG9lzD8VgsW-9VA4tyfN7HWjELrP.O; qq_nick=111; qq_head=https%3A%2F%2Ftvpic.gtimg.cn%2Fhead%2F1909bea96db0fa84cf7c74c2640cc736da39a3ee5e6b4b0d3255bfef95601890afd80709%2F355; v_qq_com_session_lapse_time=1774444111823; last_refresh_time=1774436912824; last_refresh_vuserid=3557414298; role=0; p_vuserid=0; cache_vip_type=1; _qimei_i_2=7ad228d69524; _qimei_i_1=46dd6487975d068fc490ad64528772b5a4eaa3a3435e0483e08c28582493206c616337c039d8e1dddfaee5ec', '2025-11-24 19:45:40', '2026-03-28 15:03:00', '1'),
(7, 'Iqiyi_2025-01-15', 'iqiyi', '0', ' ', 'P00001=48iNVxoZZVom3rdgGfrYyBwZJdchDoCoqCabSm242EebZepM5bm24glHJiPMEm2Xoz2VGn1b;', '2025-11-24 19:46:39', '2026-03-28 12:51:14', '1'),
(8, 'YouKu_2025-12-17', 'youku', '0', ' ', '_TDID_CK=1774227765265; 6333762c95037d16=FuA0E8KlQLqdCOCgjb3KC%2BdXdMbg4TyKWkBm5PHcRUV6PaRT2DHGa2HVHcI2mfor1CxAQ7qPJJDmY5ubVhWPi0XH3bZe%2Bp2aP7DX6Qle1rNtrF%2FeAb46m9zpVBzXYeUjWZW7CfDodccYFmLlPACJcDd8MWD4RyTXRg2q13RWHnr7cGSQQe9j%2BEGo6oMQMPHq7AzxMvJQl7BxbygTCTFt9j39Dk79080sR2DWt%2BAChdTBEmcGJeeAVVNzVBIhooNzdGwclQi64pwVAJ2nw9bxFMDSNpzLpf9SrPRwwwsvPB4c6l9B9%2BEcTA%3D%3D; cna=MX9HIn0Apk0BASQIgi7oJcWq; __ysuid=1774227761683jz7; __ayft=1774227761684; __aysid=1774227761684N1z; __ayscnt=1; csrfToken=tiXRMMUWephXSHfMIKnfzwG6; isI18n=false; _m_h5_tk=37bb1c3926497bab7c907b2dcf4071e3_1774685041819; _m_h5_tk_enc=c6c07d8ec37348588a1fc3f5d3bbb0c8; login_index=1_1774227765048; __arpvid=1774227765437WxTG4U-1774227765444; __aypstp=3; __ayspstp=3; xlly_s=1; __ayvstp=3; __aysvstp=3; P_sck=ZCrb2R90fJuVH2luXitZXsUPf9VNso%2Fx3ZLkSO2zXAxt4ErbTeMRHhU%2BYvNFlbBFyAlzhCU7PRXtHwHwFwRzZAdSTkvOeq%2F8cKrdw1cNcatfPHwhNaFd2BONaD3VNSw%2BoLDbf64Jy3M%2BBaeMB%2Fwlu8dvL31Ijhml8VBiTUhYf14xD3lvmX%2F5fRzD%2BVT93hLV; P_gck=NA%7CMFi5U3SP4p0IMnp32t7erQ%3D%3D%7CNA%7C1774227768940; P_pck_rm=%2BC%2BcOAhlf8bacd97975adaZBglBanmfRFgp%2Bh%2BWmJzlgzHV1Prmey%2F4MomPL9Pwy%2FYJDFdxjXWcjaZSa5XC86Wzj62EvzFF4j3cs04RdcSo0q4zIlaxQotFkXoJbVg6zcfOCf6EuJusrjBPLCn8PjGoPRdogLSi05vrnGX5gebpvzSRGJs0jUymnkGR1etdj9%2F6F%2FMvtZPOCRWbs%5FV2; disrd=67971; tfstk=ge4q3BOHaZQVKJX3TW0N4d8mnPgxqVWCQPMss5ViGxDDlE9g4SwJcqGbsAyaFY3XcqYX7cyYemZb75LZs52OMA1AVSFxWVXCd6_QMSpbCP7UuccoZfPkSdjSmGjxYiXCdw_5mQ0N39Nf-07oqfHoiE0Mm_JoGf3iinmcaYcZ9hv0IVfPafc6jfDMm40o_fgiIR0Ga7D-sVc0IVfzZYhiN_wgHUkrmsyicCe5UcnqKSDy87Uriq-YgY8MSzrr0vYsUFYgzjPMKKo28GM3vxiIK8bWuVPo_8oQ0984Q5VLj0zwnsw3ElyiwuBwbvrgGkUSVCx0aPkqxrmy66Nnkf2ibuBeRjub4Dz4D1puiJM4xqF6TtNrYuoKZ0AwmqZQOPn3oZJKFDhaUDZVLEyh4GptZ7-aWoJMbmc-av1PaaVid6ILaZEvXhnP2bkC96K9Xmc-av1PahKt4yhrd6Cd.', '2025-11-24 19:47:46', '2026-03-28 14:37:01', '1'),
(9, 'bil', 'bilibili', '0', ' ', '{\"access_token\":\"fc4a61bf15eb9a56faf313127ce47031\",\"refresh_token\":\"b19f6f36cb2945e41c6abd4ec5d05531\",\"cookie\":\"SESSDATA=ffbe366f%2C1790226041%2C2b29ea31; bili_jct=1dbb11c8267c733f5e7645884fabe584; DedeUserID=3706944143493321; DedeUserID__ckMd5=7ea19da0534beef4; sid=mqgnmdl2\"}', '2025-11-25 16:15:11', '2026-03-28 14:58:50', '0'),
(10, 'RRMJ_4', 'rrmj', '0', '13214105935----caodandeQQ123', '{\"umid\":\"abda1367-6a82-4b78-a54e-fe8051da950d\",\"deviceId\":\"abda1367-6a82-4b78-a54e-fe8051da950d\",\"aliId\":\"abda1367-6a82-4b78-a54e-fe8051da950d\",\"cookie\":\"rrtv-41e857d8982dbc3b1357bb1b448d4f0f946f9df5\"}', '2025-11-27 14:26:44', '2026-03-17 12:52:32', '0'),
(20, 'RRMJ_1', 'rrmj', '0', '13306373901----caodandeQQ123', '{\"umid\":\"8b94ac35-7a7b-4db7-8dde-20806441da6c\",\"deviceId\":\"8b94ac35-7a7b-4db7-8dde-20806441da6c\",\"aliId\":\"8b94ac35-7a7b-4db7-8dde-20806441da6c\",\"cookie\":\"rrtv-ca6eae262a60fb08aae83aff5d959aa5365e7e86\"}', '2026-03-24 20:16:35', '2026-03-24 20:18:43', '1');

-- --------------------------------------------------------

--
-- 表的结构 `zhi_ip`
--

CREATE TABLE `zhi_ip` (
  `id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `addtime` datetime DEFAULT NULL,
  `dqdtime` datetime DEFAULT NULL,
  `state` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zhi_log`
--

CREATE TABLE `zhi_log` (
  `id` int(11) NOT NULL,
  `act` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `time` varchar(255) NOT NULL,
  `addtime` datetime DEFAULT NULL,
  `state` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zhi_menu`
--

CREATE TABLE `zhi_menu` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `pid` varchar(255) NOT NULL COMMENT '父级ID',
  `is_out` varchar(255) NOT NULL COMMENT '是否外链0否|1是,外链a标签没有class=''multitabs''',
  `is_home` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zhi_menu`
--

INSERT INTO `zhi_menu` (`id`, `name`, `url`, `icon`, `pid`, `is_out`, `is_home`) VALUES
(1, '首页', 'index/main', 'mdi mdi-home-city-outline', '0', '0', '1'),
(2, '系统配置', 'set', 'mdi mdi-settings', '0', '0', '0'),
(3, '管理列表', 'admin2', 'mdi mdi-account-supervisor', '0', '0', '0'),
(4, '授权管理', 'ip', 'mdi mdi-security', '0', '0', '0'),
(5, '账号管理', 'cookie', 'mdi mdi-credit-card-multiple', '0', '0', '0'),
(6, '代理配置', 'proxy', 'mdi mdi-map-marker-circle', '0', '0', '0'),
(7, '日志管理', 'log', 'mdi mdi-calendar-clock', '0', '0', '0'),
(8, 'URL Mapping', 'urlmap', '', '2', '0', '0');

-- --------------------------------------------------------

--
-- 表的结构 `zhi_proxy`
--

CREATE TABLE `zhi_proxy` (
  `id` int(11) NOT NULL,
  `url` text NOT NULL,
  `hctime` int(255) NOT NULL,
  `uptime` varchar(255) NOT NULL,
  `addtime` datetime DEFAULT NULL,
  `state` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zhi_proxy`
--

INSERT INTO `zhi_proxy` (`id`, `url`, `hctime`, `uptime`, `addtime`, `state`) VALUES
(1, 'http://114.66.39.85:5555/random', -1, '2025-11-25 18:51:15', '2025-11-25 18:51:15', '1');

-- --------------------------------------------------------

--
-- 表的结构 `zhi_set`
--

CREATE TABLE `zhi_set` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `act` varchar(255) NOT NULL,
  `a` text NOT NULL,
  `b` text NOT NULL,
  `updtime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zhi_set`
--

INSERT INTO `zhi_set` (`id`, `name`, `act`, `a`, `b`, `updtime`) VALUES
(1, '日志开关', 'common', 'log', '0', '2026-01-02 17:41:22'),
(2, '授权开关', 'common', 'ipsq', '0', '2026-01-02 17:41:22'),
(3, '爱奇艺代理开关', 'iqiyi', 'IPKQ', '0', '2026-01-02 17:41:22'),
(4, '爱奇艺缓存时间', 'iqiyi', 'HCTIME', '7200', '2026-01-02 17:41:23'),
(5, '爱奇艺302跳转', 'iqiyi', 'tz', '0', '2026-01-02 17:41:22'),
(6, '爱奇艺缓存', 'iqiyi', 'hc', '0', '2026-01-02 17:41:22'),
(7, '腾讯缓存开关', 'qq', 'hcqq', '0', '2026-01-02 17:41:22'),
(8, '腾讯缓存时间', 'qq', 'txhctime', '7200', '2026-01-02 17:41:22'),
(9, '腾讯代理开关', 'qq', 'ipdl', '0', '2026-01-02 17:41:22'),
(10, '腾讯视频类型', 'qq', 'txact', 'm3u8', '2026-01-02 17:41:22'),
(11, '芒果缓存开关', 'mgtv', 'hcmg', '0', '2026-01-02 17:41:23'),
(12, '芒果缓存时间', 'mgtv', 'hctime', '7200', '2026-01-02 17:41:23'),
(13, '芒果输出格式', 'mgtv', 'mgtz', '0', '2026-01-02 17:41:23'),
(14, '优酷缓存开关', 'youku', 'hcyk', '0', '2026-01-02 17:41:23'),
(15, '优酷缓存时间', 'youku', 'youkuhctime', '7200', '2026-01-02 17:41:23'),
(16, '优酷302跳转开关', 'youku', 'youkutz', '0', '2026-01-02 17:41:23'),
(17, '哔哩哔哩缓存开关', 'bilibili', 'bilibilihc', '0', '2026-01-02 17:41:23'),
(18, '哔哩哔哩缓存时间', 'bilibili', 'bilibilitime', '7200', '2026-01-02 17:41:23'),
(19, '腾讯视频画质', 'qq', 'txhz', 'fhd', '2026-01-02 17:41:22'),
(20, '弹幕开关', 'dmku', 'dmkg', '1', '2026-01-02 17:41:22'),
(21, '弹幕缓存开关', 'dmku', 'dmhckg', '1', '2026-01-02 17:41:22'),
(22, '弹幕缓存时间', 'dmku', 'dmhctime', '0', '2026-01-02 17:41:22'),
(23, '爱奇艺画质', 'iqiyi', 'iqiyihz', '600', '2026-01-02 17:41:23'),
(24, '代理调用方式', 'common', 'proxyact', '0', '2026-01-02 17:41:23'),
(28, '西瓜视频缓存开关', 'xigua', 'hckg', '1', '2026-01-02 17:41:23'),
(29, '西瓜缓存时间', 'xigua', 'hctime', '7200', '2026-01-02 17:41:23'),
(30, '西瓜视频画质', 'xigua', 'hz', '4', '2026-01-02 17:41:23'),
(37, '芒果代理', 'mgtv', 'dl', '0', '2026-01-02 17:41:23'),
(38, '腾讯免ck自适应画质', 'qq', 'qqmckzsy', '0', '2026-01-02 17:41:23'),
(39, '免ck失败自动轮询ck', 'qq', 'mcklxck', '0', '2026-01-02 17:41:23'),
(40, 'system_add', 'xigua', 'xihctime', '7200', '2026-01-02 17:41:23'),
(41, 'system_add', 'common', 'cklxtime', '1 s', '2026-01-02 17:41:23'),
(42, 'system_add', 'youku', 'youkudaili', '0', '2026-01-02 17:41:23'),
(43, 'system_add', 'bilibili', 'bilibilihz', '112', '2026-01-02 17:41:23');

-- --------------------------------------------------------

--
-- 表的结构 `zhi_url_map`
--

CREATE TABLE `zhi_url_map` (
  `id` int(11) NOT NULL,
  `source_url` varchar(255) NOT NULL,
  `target_value` varchar(255) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  `updtime` datetime DEFAULT NULL,
  `status` char(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zhi_url_map`
--

INSERT INTO `zhi_url_map` (`id`, `source_url`, `target_value`, `remark`, `sort`, `addtime`, `updtime`, `status`) VALUES
(3, 'https://www.mgtv.com/b/856579/24246903.html', 'http://142.171.135.29/iyf/4c18c23ececf5e9a86543df5b61bb511.m3u8', '', 0, '2026-03-21 17:21:23', '2026-03-21 17:21:23', '1'),
(4, 'https://v.qq.com/x/cover/mzc003hrk2urh8u/w3195jrvkl2.html', 'http://142.171.135.29/iyf/1113c673a81cfa07cd0d7fbe1dfefbcf.m3u8', '默认映射', 100, '2026-03-21 17:21:29', '2026-03-21 17:21:29', '1'),
(5, 'https://v.qq.com/x/cover/mzc003hrk2urh8u/w3195bygkh5.html', 'http://142.171.135.29/iyf/9364b457278cc191069d3951aa929d39.m3u8', '默认映射', 99, '2026-03-21 17:21:33', '2026-03-21 17:21:33', '1'),
(6, 'https://v.qq.com/x/cover/mzc002007df8ccv/m4101zcvpu8.html', 'http://142.171.135.29/iyf/f72cc4cd57b029f948dacc7b0fade345.m3u8', '', 0, '2026-03-21 20:54:14', '2026-03-21 20:54:14', '1'),
(7, 'https://www.bilibili.com/bangumi/play/ep3254533', 'http://142.171.135.29/iyf/18e85a41003584d7eea3630214889500.m3u8', '', 0, '2026-03-23 08:57:39', '2026-03-23 08:57:39', '1'),
(8, 'https://www.bilibili.com/bangumi/play/ep3344081', 'http://142.171.135.29/iyf/18e85a41003584d7eea3630214889500.m3u8', '', 0, '2026-03-23 08:57:57', '2026-03-23 08:57:57', '1'),
(9, 'https://www.iqiyi.com/v_mdeq4b7jzk.html', 'http://142.171.135.29/iyf/18e85a41003584d7eea3630214889500.m3u8', '', 0, '2026-03-23 08:58:38', '2026-03-23 08:58:38', '1'),
(10, 'https://v.youku.com/v_show/id_XNjUxNjczMjIwNA==.html', 'http://142.171.135.29/iyf/18e85a41003584d7eea3630214889500.m3u8', '', 0, '2026-03-23 08:58:53', '2026-03-23 08:58:53', '1'),
(11, 'https://www.mgtv.com/b/820389/24252305.html', 'http://142.171.135.29/iyf/b16c35e28a4fdec00400e8b92e03bca6.m3u8', '', 0, '2026-03-23 19:41:50', '2026-03-23 19:41:50', '1');

--
-- 转储表的索引
--

--
-- 表的索引 `zhi_admin`
--
ALTER TABLE `zhi_admin`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `zhi_cookie`
--
ALTER TABLE `zhi_cookie`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `zhi_ip`
--
ALTER TABLE `zhi_ip`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `zhi_log`
--
ALTER TABLE `zhi_log`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `zhi_menu`
--
ALTER TABLE `zhi_menu`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `zhi_proxy`
--
ALTER TABLE `zhi_proxy`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `zhi_set`
--
ALTER TABLE `zhi_set`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `zhi_url_map`
--
ALTER TABLE `zhi_url_map`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_source_url` (`source_url`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `zhi_admin`
--
ALTER TABLE `zhi_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `zhi_cookie`
--
ALTER TABLE `zhi_cookie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- 使用表AUTO_INCREMENT `zhi_ip`
--
ALTER TABLE `zhi_ip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `zhi_log`
--
ALTER TABLE `zhi_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `zhi_menu`
--
ALTER TABLE `zhi_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 使用表AUTO_INCREMENT `zhi_proxy`
--
ALTER TABLE `zhi_proxy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `zhi_set`
--
ALTER TABLE `zhi_set`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- 使用表AUTO_INCREMENT `zhi_url_map`
--
ALTER TABLE `zhi_url_map`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
