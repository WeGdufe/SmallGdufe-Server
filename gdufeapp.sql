/*

Source Database       : gdufeapp

*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for feedback
-- ----------------------------
DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sno` bigint(11) DEFAULT NULL COMMENT '学号',
  `content` varchar(500) DEFAULT NULL COMMENT '内容',
  `contact` varchar(50) DEFAULT NULL COMMENT '联系方式',
  `createTime` datetime DEFAULT NULL COMMENT '反馈创建时间',
  `fix` tinyint(4) DEFAULT '0' COMMENT '是否已解决，0为待处理，1为已解决，2为忽略不处理',
  `devBrand` varchar(50) DEFAULT NULL COMMENT '手机品牌',
  `devModel` varchar(50) DEFAULT NULL COMMENT '手机型号',
  `osVersion` varchar(50) DEFAULT NULL COMMENT '手机系统版本号',
  `comment` varchar(255) DEFAULT NULL COMMENT '管理员对该反馈的评论注释(用户不可见)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of feedback
-- ----------------------------
INSERT INTO `feedback` VALUES ('1', '13251102210', '\'ceshi\'', '6666', null, '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('2', '13251102210', '\'测试3月13日\'', '测试', null, '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('5', '15250204443', '\'7.1.1居然用的了，要不要这么感人哈哈，加油！\'', '', null, '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('6', '15250204443', '\'7.1.1居然用的了，要不要这么感人哈哈，加油！\'', '', null, '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('7', '16250813107', '\'加入课程小部件\'', '13242309700', null, '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('8', '15251101142', '\'\'', '', null, '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('9', '15251101142', '\'null\'', '', null, '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('10', '15251104134', '\'\'', '', null, '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('11', '13251102210', '\'233\'', '', '2017-04-17 22:10:12', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('12', '13251102239', '没意见很好', '', '2017-04-18 12:47:16', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('13', '14251101101', 'drcom总是连上一分钟就重连，用哆点不会有这个问题', '', '2017-04-18 18:33:35', '0', null, null, null, null);
INSERT INTO `feedback` VALUES ('14', '15251104134', '饭卡余额那里会突然显示null好久一段时间', '', '2017-04-18 21:46:06', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('15', '16251104230', '可以把排课表加到课程表里，就写在星期六日，这样可以一起看要上的课程跟时间', '1574033296', '2017-04-19 08:20:47', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('16', '16251104230', '可以把排课表加到课程表里，就写在星期六日，这样可以一起看要上的课程跟时间', '1574033296', '2017-04-19 08:20:53', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('17', '16251104230', '可以把排课表加到课程表里，就写在星期六日，这样可以一起看要上的课程跟时间', '1574033296', '2017-04-19 08:20:55', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('18', '16251104230', '可以把排课表加到课程表里，就写在星期六日，这样可以一起看要上的课程跟时间', '1574033296', '2017-04-19 08:21:00', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('19', '16251104230', '可以把排课表加到课程表里，就写在星期六日，这样可以一起看要上的课程跟时间', '1574033296', '2017-04-19 08:21:12', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('20', '16251104230', '可以把排课表加到课程表里，就写在星期六日，这样可以一起看要上的课程跟时间', '1574033296', '2017-04-19 08:21:15', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('21', '16251104230', '可以把排课表加到课程表里，就写在星期六日，这样可以一起看要上的课程跟时间', '1574033296', '2017-04-19 08:21:20', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('22', '16251104230', '可以把排课表加到课程表里，就写在星期六日，这样可以一起看要上的课程跟时间', '1574033296', '2017-04-19 08:21:43', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('23', '16251104230', '可以把排课表加到课程表里，就写在星期六日，这样可以一起看要上的课程跟时间', '1574033296', '2017-04-19 08:21:53', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('24', '16251104230', '可以把排课表加到课程表里，就写在星期六日，这样可以一起看要上的课程跟时间', '1574033296', '2017-04-19 08:22:04', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('25', '15251104218', '课表不准唉，今天要上机的，结果显示一教上课', '', '2017-04-19 08:25:31', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('26', '15251104218', '课表不准唉，今天要上机的，结果显示一教上课', '', '2017-04-19 08:25:32', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('27', '15251104218', '现在第8周，显示了第9周的课', '', '2017-04-19 08:27:24', '0', null, null, null, null);
INSERT INTO `feedback` VALUES ('28', '15251104134', '单双周的课没有显示', '', '2017-04-19 10:06:15', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('29', '15251104134', '单双周的课没有显示', '', '2017-04-19 10:06:15', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('30', '15251104134', '小米手机会闪退', '', '2017-04-19 10:29:27', '0', null, null, null, null);
INSERT INTO `feedback` VALUES ('31', '16251002240', '第二学期校历错了喔。暑假从第二十周就开始了。(即7号就全部考完试啦)', '微信15521945512', '2017-04-19 10:47:15', '1', null, null, null, '他看的是广财助手的图');
INSERT INTO `feedback` VALUES ('32', '16251104218', '', '792875586', '2017-04-19 10:54:42', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('33', '16251104218', '测试字段', '792875586', '2017-04-19 10:55:20', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('34', '14251104250', '233333333', '֟光֟辉֟岁֟', '2017-04-19 10:56:47', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('35', '16251102250', 'bug：默认首页设置为功能，进入app后仍是课表，复现率100%', '', '2017-04-19 10:57:31', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('36', '13251102210', '光继续测试', '792875586', '2017-04-19 23:25:42', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('37', '15251102210', '桌面显示有bug，周一出现了两个3.4.5.6节', 'CZQ-12138', '2017-04-24 13:47:47', '1', null, null, null, '不是BUG，是单双周各一个课程的情况发生');
INSERT INTO `feedback` VALUES ('38', '15251102210', '桌面显示有bug，周一出现了两个3.4.5.6节', 'CZQ-12138', '2017-04-24 13:47:48', '1', null, null, null, '不是BUG，是单双周各一个课程的情况发生');
INSERT INTO `feedback` VALUES ('39', '15251102210', '常用电话排版有点乱（华为）', '', '2017-04-24 14:03:03', '0', null, null, null, null);
INSERT INTO `feedback` VALUES ('40', '14250301108', '热点经常掉', 'mo__1024', '2017-04-26 06:57:54', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('41', '14250301101', '你们给我搞的这个软件啊，一颗赛艇的！', '', '2017-04-28 19:57:33', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('42', '14250301101', '你们给我搞的这个软件啊，一颗赛艇的！', '', '2017-04-28 19:57:35', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('43', '15251102210', '课程表单双周会混在一起', '', '2017-05-10 10:05:17', '0', null, null, null, null);
INSERT INTO `feedback` VALUES ('44', '16250813107', '好感动 居然有庆祝生日功能', '13242309700', '2017-05-24 21:41:30', '2', null, null, null, null);
INSERT INTO `feedback` VALUES ('45', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:20:58', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('46', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:20:58', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('47', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:20:58', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('48', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:00', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('49', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:01', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('50', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:01', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('51', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:01', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('52', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:02', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('53', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:02', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('54', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:02', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('55', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:02', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('56', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:02', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('57', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:03', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('58', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:03', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('59', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:03', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('60', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:04', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('61', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:04', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('62', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:04', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('63', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:04', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('64', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:04', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('65', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:05', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('66', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:05', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('67', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:05', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('68', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:05', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('69', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:05', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('70', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:07', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('71', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:07', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('72', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:07', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('73', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:07', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('74', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:08', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('75', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:08', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('76', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:08', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('77', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:08', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('78', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:09', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('79', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:09', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('80', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:09', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('81', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:09', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('82', '15250502252', '有bug,常用电话-时间，下拉会自动弹上去，安卓', '', '2017-05-27 14:21:10', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('83', '88888888888', '校友反馈测试', '792875586', '2017-05-30 10:40:28', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('84', '88888888888', '校友反馈测试', '792875586', '2017-05-30 10:48:43', '1', null, null, null, null);
INSERT INTO `feedback` VALUES ('85', '88888888888', '型号获取测试', '', '2017-05-30 13:44:13', '1', 'Android', 'Android SDK built for x86_64', '7.1.1', null);
INSERT INTO `feedback` VALUES ('86', '13251102210', '老版本测试', '', '2017-05-30 13:59:32', '1', null, null, null, null);
