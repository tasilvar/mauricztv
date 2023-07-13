<?php
use bpmj\wpidea\View;
?>
<div class="notice bpmj-eddcm-expiration-notice bpmj-eddcm-expiration-notice--with-timer">
    <div class="bpmj-eddcm-expiration-notice__message">
        <p>
            <?php echo $message; ?>
        </p>
    </div>
    <?php if($timestamp): ?>
        <div class="bpmj-eddcm-expiration-notice__timer">
            <div class="bpmj-eddcm-expiration-notice__timer__part">
                <span id="timer_days" class="bpmj-eddcm-expiration-notice__timer__counter bpmj-eddcm-expiration-notice__timer__counter--days">00</span>
                <span class="bpmj-eddcm-expiration-notice__timer__label"><?php _e( 'days', BPMJ_EDDCM_DOMAIN ) ?></span>
            </div>
            <div class="bpmj-eddcm-expiration-notice__timer__part">
                <span id="timer_hours" class="bpmj-eddcm-expiration-notice__timer__counter bpmj-eddcm-expiration-notice__timer__counter--hours">00</span>
                <span class="bpmj-eddcm-expiration-notice__timer__label"><?php _e( 'hr', BPMJ_EDDCM_DOMAIN ) ?></span>
            </div>
            <div class="bpmj-eddcm-expiration-notice__timer__part">
                <span id="timer_minutes" class="bpmj-eddcm-expiration-notice__timer__counter bpmj-eddcm-expiration-notice__timer__counter--minutes">00</span>
                <span class="bpmj-eddcm-expiration-notice__timer__label"><?php _e( 'min', BPMJ_EDDCM_DOMAIN ) ?></span>
            </div>
            <div class="bpmj-eddcm-expiration-notice__timer__part">
                <span id="timer_seconds" class="bpmj-eddcm-expiration-notice__timer__counter bpmj-eddcm-expiration-notice__timer__counter--seconds">00</span>
                <span class="bpmj-eddcm-expiration-notice__timer__label"><?php _e( 'sec', BPMJ_EDDCM_DOMAIN ) ?></span>
            </div>
        </div>
    <?php endif; ?>
    <?php if ( ! empty( $buttons ) ) : ?>
        <div class="bpmj-eddcm-expiration-notice__button-wrapper">
            <?php if ( ! empty( $upper_button_text ) ) : ?>
                <div class="bpmj-eddcm-expiration-notice__button-wrapper-top-text"><?= $upper_button_text; ?></div>
            <?php endif; ?>

            <div class="bpmj-eddcm-expiration-notice__button-wrapper-container">
                <?= View::get('/admin/trial-bar/notice-buttons', [
                    'buttons' => $buttons,
                ]); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if($timestamp): ?>
    <script>
        /**
         * Converts timestamp to object (eg. {days: 00, hours: 01, minutes: 12, seconds: 43})
         *
         * @param time
         * @returns {object}
         */
        function timestampToObject(time) {
            // calculate (and subtract) whole days
            var days = Math.floor(time / 86400);
            time -= days * 86400;
            days = days < 0 ? '00' : days;
            days = days < 10 && days > 0 ? '0' + days : days;

            // calculate (and subtract) whole hours
            var hours = Math.floor(time / 3600) % 24;
            time -= hours * 3600;
            hours = hours < 10 ? '0' + hours : hours;

            // calculate (and subtract) whole minutes
            var minutes = Math.floor(time / 60) % 60;
            time -= minutes * 60;
            minutes = minutes < 10 ? '0' + minutes : minutes;

            // what's left is seconds
            var seconds = time % 60;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            let formattedTime = {
                days,
                hours,
                minutes,
                seconds
            };

            return formattedTime;
        }


        /**
         * Update timer
         *
         * @param {object} time Object of itme values, eg. {days: 00, hours: 01, minutes: 12, seconds: 43}
         */
        function updateHtmlTimer(time) {
            let daysHtmlItem = document.querySelector('.bpmj-eddcm-expiration-notice__timer__counter--days');
            let hoursHtmlItem = document.querySelector('.bpmj-eddcm-expiration-notice__timer__counter--hours');
            let minutesHtmlItem = document.querySelector('.bpmj-eddcm-expiration-notice__timer__counter--minutes');
            let secondsHtmlItem = document.querySelector('.bpmj-eddcm-expiration-notice__timer__counter--seconds');

            if(!daysHtmlItem || !hoursHtmlItem || !minutesHtmlItem || !secondsHtmlItem) return;

            daysHtmlItem.innerHTML = time.days;
            hoursHtmlItem.innerHTML = time.hours;
            minutesHtmlItem.innerHTML = time.minutes;
            secondsHtmlItem.innerHTML = time.seconds;
        }

        /**
         * Update timer item content
         */
        function updateTimer() {
            let time = <?= $timestamp ?> - (new Date().getTime()/ 1000).toFixed(0)
            let formattedTime = window.timestampToObject(time);
            window.updateHtmlTimer(formattedTime);
        }

        setInterval(updateTimer, 1000);
    </script>
<?php endif; ?>

