<?php
namespace app\social\base;

/**
 * 常量表
 * User: xiaoguang
 * Date: 2017/11/12
 */
class Error
{
    const success = 0;
    const commonHit = 1;
    const commonDialog = 2;
    const forceLogout = 3;

    //业务错误
    const netError = 1000;
    const parmError = 1001;
    const invalidError = 1002;
    const parmEmpty = 1003;

}
