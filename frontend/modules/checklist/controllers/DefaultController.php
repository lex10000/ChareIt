<?php

namespace frontend\modules\checklist\controllers;

use yii\web\Controller;
use frontend\modules\checklist\models\Checklist;
use Yii;
use frontend\modules\checklist\models\ChecklistForm;
use frontend\modules\checklist\models\ChecklistItems;
use yii\web\Response;
use Faker\Factory;

class DefaultController extends Controller
{

//    public function beforeAction($action)
//    {
//        if(Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }
//    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Получает все чек-листы пользователя
     * @return array список чек-листов пользователя, либо статус empty, в случае отсутствия чек-листов
     */
    public function actionGetAllChecklists()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checklists = Checklist::findAllChecklists(Yii::$app->user->getId());

        if($checklists) return [
            'status' => 'success',
            'checklists' => $checklists
        ]; else return [
            'status' => 'empty',
        ];
    }

    /**
     * Создает новый чек-лист
     * @return string форма для создания чек-листа
     */
    public function actionCreateChecklist()
    {
        $model = new ChecklistForm();
        if ($model->load(Yii::$app->request->post()) && $model->saveChecklist()) {
            if (Yii::$app->request->isAjax) {
                return '<a href="/checklist/default/create-checklist" class="create-form">Добавить чек-лист</a>';
            }
        }

        return $this->renderAjax('checklistForm', [
            'model' => $model,
        ]);
    }

    /**
     * Метод удаления чек-листа. При удалении так же удаляются все пункты данного чек-листа.
     * @return string[] статус результата работы метода
     */
    public function actionDeleteChecklist()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checklist_id = intval(Yii::$app->request->post('checklist_id'));
        $user_id = Yii::$app->user->getId();
        if (Checklist::deleteChecklist($checklist_id, $user_id)) {
            $checklist_items = ChecklistItems::getChecklistItems($checklist_id, $user_id);

            foreach ($checklist_items as $checklist_item) {
                ChecklistItems::deleteChecklistItem($checklist_item['id'], $user_id);
            }
            return [
                'status' => 'success',
                'message' => "чек-лист успешно удален"
            ];
        } else return [
            'status' => 'error or not found',
        ];
    }

    /**
     * Получает чек-лист, выбранный пользователем
     */
    public function actionSetupChecklist()
    {
        $checklist_id = intval(Yii::$app->request->post('checklist_id'));

        $checklist_options = ChecklistItems::getChecklistItems($checklist_id, Yii::$app->user->getId());

        $checklist_items = new ChecklistItems();
        if ($checklist_options) {
            return $this->renderAjax('checklist_items', [
                'checklist_options' => $checklist_options,
                'checklist_id' => $checklist_id,
                'checklist_items' => $checklist_items
            ]);
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'status' => 'empty'
            ];
        }
    }

    /**
     * удаляет поле из чек-листа
     * @return string[] возвращает success в случае успеха, error or not found в случае ошибки
     */
    public function actionDeleteChecklistItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checklist_item_id = intval(Yii::$app->request->post('checklist_item_id'));

        $result = ChecklistItems::deleteChecklistItem($checklist_item_id, Yii::$app->user->getId());

        if ($result) return [
            'status' => 'success',
            'message' => "пункт успешно удален"
        ]; else return [
            'status' => 'error or not found',
        ];
    }

    public function actionAddChecklistItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checklist_id = intval(Yii::$app->request->post('checklist_id'));
        $item_name = Yii::$app->request->post('item_name');
        var_dump($checklist_id,$item_name);
        var_dump(Yii::$app->request->post());die();
        $item_required = 1;

        $model = new ChecklistItems();

        $model->name = $item_name;
        $model->extra = $item_required;
        $model->checklist_id = $checklist_id;
        if ($model->save()) {
            return [
                'status' => 'success',
                'checklist_options' => [
                    'checklist_id' => $checklist_id,
                    'id' => $model->id,
                    'name' => $item_name,
                    'required' => 1,
                ],
            ];
        } else return [
            'status' => 'error'
        ];
    }

    public function actionCompleteChecklist()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checklist_id = intval(Yii::$app->request->post('checklist_id'));
        $checklist = Checklist::findOne($checklist_id);
        $checklist->status = Checklist::STATUS_DONE;
        if ($checklist->save()) {
            return [
                'status' => 'success',
            ];
        };
    }
//    public function actionFaker()
//    {
//        $faker = Factory::create();
//
//        for($i = 0; $i < 1000; $i++)
//        {
//            $post = new ChecklistItems();
//            $post->name = $faker->text(30);
//            $post->checklist_id = $faker->numberBetween(0,30);
//            $post->extra = $faker->numberBetween(0,1);
//            $post->user_id = $faker->numberBetween(1,100);
//            $post->save(false);
//        }
//        die('Data generation is complete!');
//    }
}
