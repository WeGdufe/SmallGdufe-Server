<?php

$baseUrls = [
    'ids' => 'http://ids.gdufe.edu.cn/amserver/UI/Login?goto=',
    // 'ids_nologin' => 'http://ids.gdufe.edu.cn/amserver/UI/Login',
    'jw' => 'http://jwxt.gdufe.edu.cn/jsxsd/',
    'info' => 'http://my.gdufe.edu.cn',
    'sztz'=>'http://sztz.gdufe.edu.cn/sztz/index.jsp',
    'opac'=>'http://opac.library.gdufe.edu.cn/opac',

];
return $urlConst = [
    'base' => $baseUrls,
    'jw' => [
        'login' => $baseUrls['jw'] .'xk/LoginToXkLdap',
        'grade' => $baseUrls['jw'] . 'kscj/cjcx_list',
        'schedule' => $baseUrls['jw'] . 'xskb/xskb_list.do',
        'cet' => '',
    ],
    'info' => [
        'idsLogin' => $baseUrls['ids'] . $baseUrls['info'],
        'sztz' => $baseUrls['info']. '/index.portal?.pn=p501',
        'tips' => $baseUrls['info']. '/pnull.portal?.f=f385&.pmn=view&action=informationCenterAjax&.ia=false&.pen=pe344',
    ],
    'opac' =>[
        'search' => $baseUrls['opac'].'/openlink.php',

    ]
];