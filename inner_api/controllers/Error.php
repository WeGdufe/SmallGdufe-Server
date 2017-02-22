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


    //学校系统错误
    const idsSysError = 2000;
    const jwSysError = 2001;
    const infoSysError = 2002;
    const opacSysError = 2003;


    //用户错误
    const passwordError = 3000;
    const accountEmpty = 3001;
    const opacBookEmpty = 3300;

    //四六级考号或名字错误
    const cetError = 5300;

    public static $errorMsg = [
        0 => '',
        1000 => '网络错误',
        1001 => '参数错误',

        2000 => '统一登陆系统挂了',
        2001 => '教务系统崩啦',
        2002 => '信息门户崩啦',
        2003 => '图书馆系统崩啦',
        2004 => ' 崩啦',

        3000 => '账号或密码错误',
        3001 => '账号或者密码为空',
        3300 => '书名为空',

        5300 => '手抖输错准考证号或者姓名了吧？',

    ];

}
