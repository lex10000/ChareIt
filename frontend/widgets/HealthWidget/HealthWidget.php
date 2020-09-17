<?php
declare(strict_types=1);
/**
 * Виджет для безопасной работы за компьютером. По истечению заданного времени появляется предупреждение о необходимости
 * сделать перерыв в работе. Работает только в debug режиме.
 * @author alex_ved
 * @inspiredBy Dayana_Sadykova
 */

namespace frontend\widgets\HealthWidget;

use Yii;
use yii\base\Widget;
use frontend\widgets\HealthWidget\HealthWidgetAsset;
class HealthWidget extends Widget
{
    /**
     * @var int кол-во времени на работу (сек)
     */
    public $workTime;

    /**
     * @var int кол-во времени до начала отдыха (сек)
     */
    public $healthTimeStart;

    /**
     * @var int кол-во времени на отдых (сек)
     */
    public $healthTime;

    /**
     * Инициализация параметров виджета. Работает только в debug режиме.
     */
    public function init()
    {
        if(YII_DEBUG) {
            $this->workTime = $this->workTime * 60 ?? 50 * 60;
            $this->healthTime = $this->healthTime * 60 ?? 10 * 60;
            $this->healthTimeStart = $this->getHealthTimeStart();

            HealthWidgetAsset::register($this->view);
        }
    }

    /**
     * TODO: переделать
     * Рассчитывает время, которое осталось до следующего запуска скрипта (в секундах)
     * @return int кол-во секунд до запуска скрипта
     */
    private function getHealthTimeStart() : int
    {
        if((!Yii::$app->session->has('HealthTimeStart'))) {
            Yii::$app->session->set('HealthTimeStart', time());
        }

        $diff = time() - Yii::$app->session->get('HealthTimeStart');

        return intval(($this->workTime + $this->healthTime) - ($diff % ($this->workTime + $this->healthTime)));
    }

    /**
     * Инициализация виджета на клиенте.
     * @return string
     */
    public function run()
    {
        if(YII_DEBUG) {
            $this->view->registerJs("const healthWidget = 
                new HealthWidget($this->healthTimeStart, $this->workTime, $this->healthTime);"
                ,$this->view::POS_END);
            return '<div class="health_widget_timer">Перерыв через <span>'.$this->getHealthTimeStart().'</span> секунд</div>';
        }
    }
}