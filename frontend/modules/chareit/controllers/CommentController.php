<?php


namespace frontend\modules\chareit\controllers;


use frontend\modules\chareit\models\Comment;
use Yii;
use yii\web\Controller;

class CommentController extends Controller
{
    public function actionCreateComment()
    {
        $commentMessage = Yii::$app->request->post('message');
        $post_id = intval(Yii::$app->request->post('postId'));
        $commentModel = new Comment($post_id);
        if ($comment = $commentModel->createComment($commentMessage)) {
            return $this->asJson([
                'status' => 'success',
                'comment' => $comment
            ]);
        } else return $this->asJson(['status' => $commentModel->getErrors('comment')]);
    }

    /**
     * @return \yii\web\Response
     * @throws \Throwable
     */
    public function actionDeleteComment()
    {
        $comment_id = Yii::$app->request->post('commentId');
        $post_id = intval(Yii::$app->request->post('postId'));
        $commentModel = new Comment($post_id);
        if ($commentModel->deleteComment($comment_id)) {
            return $this->asJson([
                'status' => 'success'
            ]);
        } else return $this->asJson(['status' => 'fail']);
    }

    public function actionGetComments()
    {
        $post_id = intval(Yii::$app->request->get('postId'));
        $commentModel = new Comment($post_id);
        if ($comments = $commentModel->getComments()) {
            return $this->asJson([
                'status' => 'success',
                'comments' => $comments
            ]);
        } else return $this->asJson(['status' => 'empty']);
    }
}