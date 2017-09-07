<?php
namespace app\inner_api\controllers;


/**
 * 常量表
 * User: xiaoguang
 * Date: 2017/2/12
 */
class Error
{
    const success = 0;
    //业务错误
    const netError = 1000;
    const parmError = 1001;
    const invalidError = 1002;


    //学校系统错误
    const idsSysError = 2000;
    const jwSysError = 2001;
    const infoSysError = 2002;
    const opacSysError = 2003;
    const cardSysError = 2004;


    //用户错误
    const accountEmpty = 3000;
    const passwordError = 3001;

    const jwNotCommentTeacher = 3100;


    const opacBookEmpty = 3300;
    const opacRenewParmEmpty = 3301;
    const opacBookDetailIdEmpty = 3302;
    const opacAccountWithdraw = 3303;       //应届毕业生在办理离校手续后会被注销
    const cardNumEmpty = 4000;  //查询校园卡记录


    //四六级考号或名字错误
    const cetError = 5300;
    const cetAccountEmpty = 5301;

    const roomNotExist = 6300;
    const buildingError = 6301;
    public static $errorMsg = [
        0 => '',
        1000 => '网络错误',
        1001 => '参数错误',
        1002 => '非法操作',

        2000 => '统一登陆系统挂了',
        2001 => '教务系统崩啦',
        2002 => '信息门户崩啦',
        2003 => '图书馆系统崩啦',
        2004 => '一卡通系统崩啦',

        3000 => '账号或者密码为空',
        3001 => '账号或密码错误',

        3100 => '没评教，去成绩打印机处查询吧',

        3300 => '书名为空',
        3301 => '参数不完整续借无力',
        3302 => '没有macno参数你也想看书本详情？',
        3303 => '图书馆账号已被注销',

        4000 => '校园卡卡号为空',

        5300 => '手抖输错准考证号或者姓名了吧？',
        5301 => '考号或者名字为空',

        6300 => '房间号错误或者最近一周系统无记录',
        6301 => '楼号错误或者暂不支持'
    ];

}
