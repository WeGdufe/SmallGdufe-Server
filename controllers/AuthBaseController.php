<?php
/**
 * 验证登录的基类
 * User: wintercoder
 * Date: 2018/09/09
 */

namespace app\controllers;


use stdClass;
use Yii;
use yii\base\Controller;
use yii\web\Response;

//自带学号密码赋值
class AuthBaseController extends BaseController
{
    protected $sno;
    protected $pwd;
    protected $req;
    protected $data=[];

    public function checkAuth(&$req){
        if( empty($req['appid']) ) {
    //根据加密后的参数进行解码， 因遇到 签名不一致 和 强要求PHP 7.0 、 DieReturn 的data必须匹配不同接口（数组还是对象）， 放弃使用没发1.6.0版，白开发了
            return null;
        }
        if( empty($req['appid'])  || empty($req['token'])  || empty($req['timestamp'])  || empty($req['sign'])) {
            $this->DieReturn(-1,'接入参数丢失');
        }
        $secretKey = Yii::$app->params['AppIdSecretMap'][ $req['appid'] ];
        if( empty($secretKey) ){
            $this->DieReturn(-1,'请找管理员接入私钥');
        }
        if(time() - $req['timestamp'] > 500000){
            $this->DieReturn(-1,'请求过期');
        }

        //sign参数校验 BEGIN
        $userReq = $req;
        unset($userReq['r'],$userReq['sign']);
        $userReq['token'] = urlencode($userReq['token']);       //因为客户端肯定是encode后去MD5的，所以这里也得保证计算sigin时是encode的。但是因为http_build_query坑爹，所以这里得这样

        ksort($userReq);
        $userReqStr = http_build_query($userReq);
        $userReqStr = rawurldecode($userReqStr);    //http_build_query后需要decode
        if( md5($userReqStr) != $req['sign'] ){
            $this->DieReturn(-1,'签名不一致');
        }
        //sign参数校验 END

        $secretKey = Yii::$app->params['AppIdSecretMap'][ $req['appid'] ];
        $key = $secretKey . $req['timestamp'];
        $token = AesSecurity::decrypt($req['token'],$key);

        if( $token === false){
            $this->DieReturn(-1,'token无效：解密失败');
        }
        parse_str($token,$privateArr);  //http_build_query反函数，sno=1&pwd=2转成数组
        $req['sno'] = $privateArr['sno'];
        $req['pwd'] = $privateArr['pwd'];
        return $req;
    }

    public function beforeAction($action)
    {
        $this->req = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
<<<<<<< HEAD
        $this->checkAuth($this->req);
=======
//        $this->checkAuth($this->req);
>>>>>>> e64e8f09d681a89cf34bc68072c1f9570e5f99e3

        if(isset($this->req['sno']) && isset($this->req['pwd'])) {
            $this->sno = $this->req['sno'];
            $this->pwd = $this->req['pwd'];
            if($this->sno == Yii::$app->params['schoolMateSnoFlag']){
                $this->sno = Yii::$app->params['schoolMateSno'];
                $this->pwd = Yii::$app->params['schoolMatePwd'];
            }
            $this->data = [
                'sno' => $this->sno,
                'pwd' => $this->pwd,
            ];
        }else{
            //无学号密码则赋值为空，给inner_api判断
            $this->data['sno'] = $this->data['pwd'] = '';
        }

        return parent::beforeAction($action);
    }


}
