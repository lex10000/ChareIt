<?php

namespace frontend\modules\checklist\controllers;

use yii\db\StaleObjectException;
use yii\web\Controller;
use frontend\modules\checklist\models\Checklist;
use Yii;
use frontend\modules\checklist\models\ChecklistForm;
use frontend\modules\checklist\models\ChecklistItems;
use yii\web\Response;
use Faker\Factory;

class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $model = new Checklist();

        $checklists = $model::findAllChecklists(Yii::$app->user->getId());

        return $this->render('index',
            [
                'checklists' => $checklists,
            ]);
    }

    /**
     * Создает новый чек-лист
     * @return string форма для создания чек-листа
     */
    public function actionCreateChecklist()
    {
        $model = new ChecklistForm();
        if ($model->load(Yii::$app->request->post()) && $model->saveChecklist()) {
            return 'success';
        }

        return $this->render('checklistForm', [
            'model' => $model,
        ]);
    }

    public function actionDeleteChecklist()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checklist_id = intval(Yii::$app->request->post('checklist_id'));

        return Checklist::findOne($checklist_id)->delete() ? [
            'status' => 'success',
        ] : [
            'status' => 'error or not found',
        ];
    }

    /**
     * Получает чек-лист, выбранный пользователем
     * TODO Сделать проверку на пользователя
     * @return array|string[]
     */
    public function actionSetupChecklist()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checklist_id = intval(Yii::$app->request->post('checklist_id'));

        $checklist_options = ChecklistItems::getChecklistItems($checklist_id);

        return [
            'checklist_options' => $checklist_options,
            'status' => 'success',
        ];
    }

    /**
     * удаляет поле из чек-листа
     * TODO Сделать проверку на пользователя
     * @return string[] возвращает success в случае успеха, error or not found в случае ошибки
     * @throws StaleObjectException|\Throwable
     */
    public function actionDeleteChecklistItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checklist_item_id = intval(Yii::$app->request->post('checklist_item_id'));

        return ChecklistItems::findOne($checklist_item_id)->delete() ? [
            'status' => 'success',
        ] : [
            'status' => 'error or not found',
        ];
    }

    public function actionAddChecklistItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $checklist_id = intval(Yii::$app->request->post('checklist_id'));
        $item_name = Yii::$app->request->post('item_name');
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
//            $post->save(false);
//        }
//        die('Data generation is complete!');
//    }
}
