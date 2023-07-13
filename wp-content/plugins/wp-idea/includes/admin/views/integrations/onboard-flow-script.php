<?php

/** @var string $site_key */
/** @var string $id */
/** @var string $email */
/** @var string $subscription_name */
/** @var string $subscription_status */
/** @var float $subscription_value */
/** @var string $subscription_interval */
/** @var int $date_start */
/** @var int $date_end */
/** @var array $data */

?>
<script type="text/javascript" id="of-loader">
    window.onboardFlowSettings = {
        "site_key": "<?= $site_key; ?>",
        "user": {
            "id": "<?= $id; ?>", // Your internal User ID for the logged in user
            "customer_id": "<?= $id; ?>", // The payment providers Customer ID for the logged in user
            "email": "<?= $email; ?>", // Email address of the logged in user (optional)
            "image_url": "", // Profile Image URL of the logged in user (optional)
            'has_payment_method': false, // Has the user added a payment card?
            'subscription': {
                'name': "<?= $subscription_name; ?>", // The name of the plan the user is on
                'status': "<?= $subscription_status; ?>", // Set to either trialing, active or cancelled
                'value': <?= $subscription_value; ?>, // The value of the plan
                'interval': "<?= $subscription_interval; ?>", // Set to either DAY, WEEK, MONTH or YEAR
                'currency': "PLN", // I.e. USD, EUR, GBP etc
                'started_at': <?= $date_start; ?>, // Date the account was created (unix timestamp format required)
                'trial_start': <?= $date_start; ?>, // Date the trial started (or is due to start) (unix timestamp format required)
                'trial_end': <?= $date_end; ?> // Date the trial is due to end (unix timestamp format required)
            }
        },
        "custom_properties": {
            <?php
                $i = 0;
                foreach($data as $custom_property) {
                    $value = is_string($custom_property['value']) ? "'" . $custom_property['value'] . "'" : $custom_property['value'];
                    echo "'" . $custom_property['name'] . "': " . $value;
                    if(++$i !== count($data)) echo ',';
                    echo PHP_EOL;
                }
            ?>
        }
    };

    <?php
     if('dev' !== $this->subscription->get_status()) {
    ?>

    ( function () {
        var po = document.createElement( "script" );
        po.type = "text/javascript";
        po.async = true;
        po.src = "https://media.onboardflow.com/gen/tracker/yYhr9zvi.min.js";
        po.onload = po.onreadystatechange = function () {
            var rs = this.readyState;
            if ( rs && rs != 'complete' && rs != 'loaded' )
                return;
            OnboardFlowLoader = new OnboardFlowLoaderClass();
            OnboardFlowLoader.identify( window.onboardFlowSettings );
        };
        var s = document.getElementsByTagName( "script" )[0];
        s.parentNode.insertBefore( po, s );
    } )();
    <?php
        }
    ?>
</script>
