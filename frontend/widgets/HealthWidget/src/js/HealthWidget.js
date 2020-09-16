/**
 * Скрипт виджета HealthWidget
 * @author alex_ved
 */

'use strict';
class HealthWidget {

    /** @var {int} кол-во времени на работу (мсек) */
    workTime;

    /** @var {int} кол-во времени до начала отдыха (мсек) */
    healthTimeStart;

    /** @var {int} кол-во времени на отдых (мсек) */
    healthTime;

    /**
     * @param {int} healthTimeStart кол-во времени до начала отдыха (сек)
     * @param {int} workTime кол-во времени на работу (сек)
     * @param {int} healthTime кол-во времени на отдых  (сек)
     */
    constructor(healthTimeStart, workTime, healthTime) {
        this.healthTimeStart = healthTimeStart * 1000;
        this.workTime = workTime * 1000 || 30 * 60 * 1000;
        this.healthTime = healthTime * 1000 || 10 * 60 * 1000;
        this.init();
    }

    /**
     * Счетчик-таймер, отсчитывает, сколько осталось секунд на работу. Если время = 0 - то сбрасывается таймер.
     */
    workTimer = () => {
        let countTimer = this.healthTimeStart / 1000;

        const div = document.querySelector('.health_widget_timer span');
        const timeIntervalSome = setInterval(() => {
            countTimer -= 1;
            div.innerHTML = countTimer;
            if (countTimer === 0) {
                clearInterval(timeIntervalSome);
                this.healthTimeStart = this.workTime;
            }
        }, 1000)
    }

    /**
     * Счетчик-таймер, отсчитывает, сколько осталось секунд на отдых. Если время = 0 - то сбрасывается, и удаляет окно
     */
    healthTimer = () => {
        let countTimer = this.healthTime / 1000;

        const timeInterval = setInterval(() => {
            countTimer -= 1;
            document.querySelector('.health_widget span').innerHTML = countTimer;
            if (countTimer === 0) {
                clearInterval(timeInterval);
                this.removeFromDom();
            }
        }, 1000);
    };

    /**
     * Добавление маскировочного окна в DOM.
     */
    appendToDom =  () => {
        let div = document.createElement('div');
        div.innerHTML = `Берегите ваши глаза! Сделайте перерыв на 20 секунд и посмотрите на дальние объекты на расстоянии 
                        не менее 20 футов (6 метров).Это окно закроется через: <span>${this.healthTime / 1000}</span> секунд`;
        div.classList.add('health_widget');
        const body = document.querySelector('body');
        body.insertAdjacentElement('beforeend', div);
    }

    /**
     * Удаление маскировочного окна из DOM.
     */
    removeFromDom = () => {
        document.querySelector('.health_widget').remove();
    }

    /**
     * TODO: переделать
     * Инициализация скрипта.
     */
     init = () => {
        this.workTimer();
        setTimeout( () => {
            setTimeout(() => {
                this.workTimer();
            }, this.healthTime)
            this.appendToDom();
            this.healthTimer();
            setInterval(  () => {
                setTimeout(() => {
                    this.workTimer();
                }, this.healthTime)
                this.appendToDom();
                this.healthTimer();
            }, this.workTime + this.healthTime);
        }, this.healthTimeStart)
    }
}




