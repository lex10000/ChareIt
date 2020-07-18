<?php
namespace frontend\modules\user\controllers;

use frontend\models\Post;
use frontend\models\User;
use frontend\modules\user\models\forms\PictureForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;
use yii\web\UploadedFile;
use yii\web\Response;


class ProfileController extends Controller
{
    public function actionIndex($id)
    {
        $currentUser = Yii::$app->user->identity;

        $modelPicture = new PictureForm();

        $user = $this->findUser($id);

        //$posts = (new Post())->getAllPosts($id);

        return $this->render('index', [
            'user' => $user,
            'currentUser' => $currentUser,
            'modelPicture'=> $modelPicture,
           // 'posts' => $posts,
        ]);
    }
    public function actionUploadPicture()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new PictureForm();

        $model->picture = UploadedFile::getInstance($model, 'picture');

        if($model->validate()){
            $user = Yii::$app->user->identity;
            $user->picture = Yii::$app->storage->saveUploadedFile($model->picture);
            if($user->save(false, ['picture'])){
                return [
                    'success' => true,
                    'pictureUri' => Yii::$app->storage->getFile($user->picture),
                ];
            }
        } else {
            return [
                'success' => false,
                'errors' => $model->getErrors(),
            ];
        }

    }


    public function findUser($id)
    {
        if($user = User::find()->where(['id' => $id])->one())
        {
            return $user;
        }
        throw new NotFoundHttpException();
    }

    public function actionSubscribe($id){
        if(Yii::$app->user->isGuest) {
            return $this->redirect(['user/default/login']);
        }

        /* @var  $currentUser User */
        $currentUser = Yii::$app->user->identity;

        $targetUser = User::findById($id);
        if(!$targetUser) Throw New NotFoundHttpException();
        if($targetUser->getId()==$currentUser->getId()) {
            return false;
        }
        $currentUser->followUser($targetUser);
        Yii::$app->session->setFlash('subscribe_result', ['Вы успешно подписались на пользователя!']);
        return $this->redirect('/site/index');
    }
}