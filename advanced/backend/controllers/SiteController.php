<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'dev-data', 'intro-data', 'per-day-num'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['update'],
                        // 自定义一个规则，返回true表示满足该规则，可以访问，false表示不满足规则，也就不可以访问actions里面的操作啦
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->id == 1 ? true : false;
                        },
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionDevData()
    {
        $cache = Yii::$app->local_cache;
        $sql_AMT = "P_oa_New_Product_Performance";
        $today = 'dev-'.date('y-m-d');
        $ret = $cache->get($today);
        if($ret !== false){
            $DataAMT = $ret;
        }
        else {
            $DataAMT = Yii::$app->db->createCommand($sql_AMT)->queryAll();
            $cache->set($today,$DataAMT,86400);
        }
        foreach ($DataAMT as $key => $value) {
            if ($value['Distinguished'] == 'l_AMT') {
                $Data['l_AMT'][] = $value;
            } else {
                $Data['CodeNum'][] = $value;
            }
        }
        $dataAMT['salername'] = array_column($Data['l_AMT'], 'salername');
        $dataAMT['OneMonth'] = array_column($Data['l_AMT'], 'OneMonth');
        $dataAMT['ThreeMonth'] = array_column($Data['l_AMT'], 'ThreeMonth');
        $dataAMT['SixMonth'] = array_column($Data['l_AMT'], 'SixMonth');

        $dataCodeNum['salername'] = array_column($Data['CodeNum'], 'salername');
        $dataCodeNum['OneMonth'] = array_column($Data['CodeNum'], 'OneMonth');
        $dataCodeNum['ThreeMonth'] = array_column($Data['CodeNum'], 'ThreeMonth');
        $dataCodeNum['SixMonth'] = array_column($Data['CodeNum'], 'SixMonth');
//推荐人信息
        $IntroData = $this->actionIntroData();
        $introAMT['introducer'] = array_column($IntroData['l_AMT'], 'introducer');
        $introAMT['OneMonth'] = array_column($IntroData['l_AMT'], 'OneMonth');
        $introAMT['ThreeMonth'] = array_column($IntroData['l_AMT'], 'ThreeMonth');
        $introAMT['SixMonth'] = array_column($IntroData['l_AMT'], 'SixMonth');

        $introCodeNum['introducer'] = array_column($IntroData['CodeNum'], 'introducer');
        $introCodeNum['OneMonth'] = array_column($IntroData['CodeNum'], 'OneMonth');
        $introCodeNum['ThreeMonth'] = array_column($IntroData['CodeNum'], 'ThreeMonth');
        $introCodeNum['SixMonth'] = array_column($IntroData['CodeNum'], 'SixMonth');
//每天产品数
        $PerDayNum = $this->actionPerDayNum();
        $result['salername'] = $dataAMT;
        $result['codenum'] = $dataCodeNum;
        $result['introducer'] = $introAMT;
        $result['introCodeNum'] = $introCodeNum;

        $result['CreateDate'] = $PerDayNum['CreateDate'];
        $result['dev'] = $PerDayNum['SalerName'];
        $result['value'] = $PerDayNum['value'];

        //美工每天产品数
        $artPerDayNum = $this->artPerDayNum();
        $result['artDate'] = $artPerDayNum['picCompleteTime'];
        $result['art'] = $artPerDayNum['possessMan1'];
        $result['artValue'] = $artPerDayNum['value'];
        echo json_encode($result);
    }

    /**
     * Introducer data
     * @return array
     */
    public function actionIntroData()
    {
        $sql_AMT = "P_oa_Intro_Product_Performance";
        $cache = Yii::$app->local_cache;
        $today = 'intro-'.date('y-m-d');
        $ret = $cache->get($today);
        if($ret !== false){
            $DataAMT = $ret;
        }
        else {
            $DataAMT = Yii::$app->db->createCommand($sql_AMT)->queryAll();
            $cache->set($today,$DataAMT,86400);
        }
        foreach ($DataAMT as $key => $value) {
            if ($value['Distinguished'] == 'l_AMT') {
                $Data['l_AMT'][] = $value;
            } else {
                $Data['CodeNum'][] = $value;
            }
        }
        return $Data;
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionPerDayNum()
    {
        $sql = "P_oa_nearDaysCodeNum";
        $cache = Yii::$app->local_cache;
        $today = 'per-'.date('y-m-d');
        $ret = $cache->get($today);
        if($ret !== false){
            $Data = $ret;
        }
        else {
            $Data = Yii::$app->db->createCommand($sql)->queryAll();
            $cache->set($today,$Data,86400);
        }
        $SalerName = array_unique(array_column($Data, 'SalerName'));
        $CreateDate = array_unique(array_column($Data, 'CreateDate'));
        $CreateDate = array_values($CreateDate);
        $da = [];
        foreach ($SalerName as $k => $v) {
            $amt = [];
            foreach ($Data as $key => $value) {
                if ($v == $value['SalerName']) {
                    $amt[] =  empty($value['CodeNum'])?0:$value['CodeNum'];
                }
            }
                     $da [] = $amt;
        }
        $result['CreateDate'] = $CreateDate;
        $result['SalerName'] = $SalerName;
        $result['value'] = $da;
        return $result;

    }/**
     * @return string|\yii\web\Response
     */
    public function artPerDayNum()
    {
        $sql = "P_oa_art_near_days_code_num";
        $cache = Yii::$app->local_cache;
        $today = 'art-'.date('y-m-d');
        $ret = $cache->get($today);
        if($ret !== false){
            $Data = $ret;
        }
        else {
            $Data = Yii::$app->db->createCommand($sql)->queryAll();
            $cache->set($today,$Data,86400);
        }
        $possessMan1 = array_unique(array_column($Data, 'possessMan1'));
        $picCompleteTime = array_unique(array_column($Data, 'picCompleteTime'));
        $picCompleteTime = array_values($picCompleteTime);
        $da = [];
        foreach ($possessMan1 as $k => $v) {
            $amt = [];
            foreach ($Data as $key => $value) {
                if ($v == $value['possessMan1']) {
                    $amt[] =  empty($value['CodeNum'])?0:$value['CodeNum'];
                }
            }
                     $da [] = $amt;
        }
        $result['picCompleteTime'] = $picCompleteTime;
        $result['possessMan1'] = $possessMan1;
        $result['value'] = $da;
        return $result;

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
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
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
}




