<?php

$baseUrls = [
    'ids' => 'http://ids.gdufe.edu.cn/amserver/UI/Login?goto=',
    'jw' => 'http://jwxt.gdufe.edu.cn/jsxsd/',
    'info' => 'http://my.gdufe.edu.cn',
    'sztz'=>'http://sztz.gdufe.edu.cn/sztz/index.jsp',

];
return $urlConst = [
    'base' => $baseUrls,
    'jw' => [
        'login' => $baseUrls['jw'] .'xk/LoginToXkLdap',
        'grade' => $baseUrls['jw'] . 'kscj/cjcx_list',
        'schedule' => '',
        'cet' => '',
    ],
    'info' => [
        'login' => $baseUrls['ids'] . $baseUrls['info'],
    ]
];