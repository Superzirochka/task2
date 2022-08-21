<?php

namespace app\controllers;

use app\models\AutoForm;
use app\models\Cities;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Countries;
use app\models\Regions;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
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
        $countries = Countries::find()->all();
        $model = new AutoForm();

        if (Yii::$app->request->isAjax) {

            if (!empty($_GET['data'])) {
                $id = ($_GET['data']);
                $option = '';
                $regions =  Regions::find()->select(['region', 'id'])
                    ->where(['id_country' => $id])
                    ->orderBy('region')
                    ->all();


                $option .= '<option >Оберіть...</option>';
                foreach ($regions as $region) {
                    $option .= '<option value="' . $region->id . '">' . $region->region . '</option>';
                }


                return $option;
                // 'Запрос принят!' . $_GET['data'];
            }

            if (!empty($_GET['regions'])) {
                $id = ($_GET['regions']);
                $option = '';
                $cities =  Cities::find()->select(['city', 'id'])
                    ->where(['id_region' => $id])
                    ->all();


                $option .= '<option> Оберіть...</option>';
                foreach ($cities as $city) {
                    $option .= '<option value="' . $city->id . '">' . $city->city . '</option>';
                }

                return $option; // 'Запрос принят!'.$_GET['data'];
            }
        }


        return $this->render(
            'index',
            [
                'countries' => $countries,
                'model' => $model
            ]
        );
    }

    /**
     * Login action.
     *
     * @return Response|string
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

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
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
