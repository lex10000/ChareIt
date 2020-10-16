<?php


namespace frontend\modules\chareit\controllers;


use frontend\modules\chareit\models\Comment;
use Yii;
use yii\web\Controller;

class CommentController extends Controller
{
    public function actionCreateComment()
    {
        if(Yii::$app->request->isAjax) {
            $comment = Yii::$app->request->post('comment');
            $post_id = intval(Yii::$app->request->post('postId'));
            $commentModel = new Comment($post_id);
            if($comment_id = $commentModel->createComment($comment)) {
                return $this->asJson([
                    'status' => 'success',
                    'commentId' =>$comment_id
                ]);
            } else return $this->asJson(['status' => 'fail']);
        }
    }

    public function actionDeleteComment()
    {
        if(Yii::$app->request->isAjax) {
            $comment_id = Yii::$app->request->post('commentId');
            $post_id = intval(Yii::$app->request->post('postId'));
            $commentModel = new Comment($post_id);
            if ($commentModel->deleteComment($comment_id)) {
                return $this->asJson([
                    'status' => 'success'
                ]);
            } else return $this->asJson(['status' => 'fail']);
        }
    }

    public function actionGetComments()
    {
        return $this->asJson([
           'comments' => [
               1 => [
                   'user_id' => 44,
                   'message' => 'qweqweq',
                   'id' => 312,
               ],
               2 => [
                   'user_id' => 55,
                   'message' => 'sdsdfsdfsdfsd',
                   'id' => 432,
               ]
           ],
        ]);
        if(Yii::$app->request->isAjax) {
            $post_id = intval(Yii::$app->request->post('postId'));
            $commentModel = new Comment($post_id);
            if ($comments = $commentModel->getComments()) {
                return $this->asJson([
                    'status' => 'success',
                    'comments' => $comments
                ]);
            } else return $this->asJson(['status' => 'fail']);
        }
    }
}