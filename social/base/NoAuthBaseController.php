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
class NoAuthBaseController extends Controller
{
    protected $urlConst;
    protected $arrInput;

    public function beforeAction($action)
    {
        ini_set('max_execution_time','5');
        if (parent::beforeAction($action)) {
            $this->urlConst = Yii::$app->params;
            // Yii::warning("访问");
            $this->arrInput = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
            return true;
        } else {
            return false;
        }
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