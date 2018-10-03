<?php
namespace app\social\base;

use stdClass;
use yii\web\Controller;
use Yii;
use Curl\Curl;
use yii\web\Response;

/**
 * User: xiaoguang
 * Date: 2017/11/12
 */
class AuthController extends BaseController
{
    protected $urlConst;
    protected $arrInput;
    //放弃使用，如果需要，保持跟 AuthBaseController 的原一样就好
    public function checkAuth(&$req){
        if( empty($req['appid']) ) {
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
        parent::beforeAction($action);
//        $this->checkAuth($this->arrInput);
        $this->arrInput['user_id'] = isset($this->arrInput['sno']) ? $this->arrInput['sno'] : 0;

        //意外情况可以返回这个强制重新登陆
        // return $this->getReturn(Error::forceLogout,"认证过期",[]);
    }

    /**
     *
     * 组成json格式返回内容
     * @param $code
     * @param string $msg
     * @param $data object|array|string 出现错误的情况填你业务对应正常返回的类型，如是空json数组[]还是空json对象{}
     * @return string json {"code":0,"data":}
     */
    public function getReturn($code,$msg='',$data=[])
    {
        return \Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => Response::FORMAT_JSON,
            'data' => [
                'code' => $code,
                'msg' => $msg,
                'data' => $data,
            ],
        ]);
    }


}