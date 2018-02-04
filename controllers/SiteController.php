<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

//yii默认的Controller，各种默认处理
class SiteController extends Controller
{
    // public function behaviors()
    // {
        // return [
        //     'access' => [
        //         'class' => AccessControl::className(),
        //         'only' => ['logout'],
        //         'rules' => [
        //             [
        //                 'actions' => ['logout'],
        //                 'allow' => true,
        //                 'roles' => ['@'],
        //             ],
        //         ],
        //     ],
        //     'verbs' => [
        //         'class' => VerbFilter::className(),
        //         'actions' => [
        //             'logout' => ['post'],
        //         ],
        //     ],
        // ];
    // }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    /**
     * 错误处理，返回异常码对应的json，如缺少参数
     * {"code":400,"msg":"Missing required parameters: sno","data":[]}
     */
    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        // $err['code'] = $exception->statusCode;
        $err['code'] = 1;   //Error::commonHit
        $err['msg'] = $exception->getMessage();
        $err['data'] = [];
        return json_encode($err);
    }
    /**
     * 默认
     *
     * @return string
     */
    public function actionIndex()
    {
        return '{"code":404,"msg":"走错路啦","data":{}}"';
        // return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
