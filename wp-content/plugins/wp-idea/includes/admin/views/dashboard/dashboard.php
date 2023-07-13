<div class="wrap wpi-dashboard">
    <h2 style="margin: 30px 0 30px;">{greeting}</h2>

    <div class="wpi-columns">
        <div class="wpi-column wpi-column--half wpi-dashboard__left-column">
            <?= $view::get('stats') ?>
        </div>

        <div class="wpi-column wpi-column--half">
            <?= $view::get('shortcuts', ['courses_functionality_enabled' => $courses_functionality_enabled]) ?>
            <?= $view::get('changelog') ?>
        </div>
    </div>
</div>
