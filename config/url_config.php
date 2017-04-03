<?php

$baseUrls = [
    'ids' => 'http://ids.gdufe.edu.cn/amserver/UI/Login?goto=',
    'jw' => 'http://jwxt.gdufe.edu.cn/jsxsd',
    'jwc' => 'http://jwc.gdufe.edu.cn',
    'info' => 'http://my.gdufe.edu.cn',
    'sztz' => 'http://sztz.gdufe.edu.cn/sztz/index.jsp',
    'opac' => 'http://opac.library.gdufe.edu.cn',
    'cet' => 'http://www.chsi.com.cn/cet',
    'card' => 'http://cardinfo.gdufe.edu.cn',
];
return $urlConst = [
    'base' => $baseUrls,
    'jw' => [
        'login' => $baseUrls['jw'] . '/xk/LoginToXkLdap',
        'grade' => $baseUrls['jw'] . '/kscj/cjcx_list',
        'schedule' => $baseUrls['jw'] . '/xskb/xskb_list.do',
        'cet' => '',
        'basicInfo' => $baseUrls['jw'] . '/grxx/xsxx',
    ],
    'jwc' => [
        'xiaoLi' => $baseUrls['jwc'] . '/html/xl/2015/10/12/c1304008-1dcf-4d89-bb58-f945db0bc677.html',
        'cet' => 'http://www.chsi.com.cn/cet/query',
        //http://chaxun.neea.edu.cn/cet/query_cet.html也可以查，不过要改代码
    ],
    'info' => [
        'ids' => $baseUrls['ids'] . $baseUrls['info'],
        'sztz' => $baseUrls['info'] . '/index.portal?.pn=p501',
        'tips' => $baseUrls['info'] . '/pnull.portal?.f=f385&.pmn=view&action=informationCenterAjax&.ia=false&.pen=pe344',
    ],
    'opac' => [
        'login' => $baseUrls['opac'] . '/reader/hwthau.php',
        'search' => $baseUrls['opac'] . '/opac/openlink.php',
        'currentBook' => $baseUrls['opac'] . '/reader/book_lst.php',
        'borrowedBook' => $baseUrls['opac'] . '/reader/book_hist.php',
        'renewBookVerify' => $baseUrls['opac'] . '/reader/captcha.php',
        'renewBook' => $baseUrls['opac'] . '/reader/ajax_renew.php',
        'bookDetail' => $baseUrls['opac'] . '/opac/item.php',
    ],
    'card' => [
        'home' => $baseUrls['card'] . '/gdcjportalHome.action',//获取cookie用
        'currentCash' => $baseUrls['card'] . '/accountcardUser.action',
        'consumeToday' => $baseUrls['card'] . '/accounttodatTrjnObject.action',
    ],
];
