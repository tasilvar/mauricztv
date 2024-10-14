<script>
    dataLayer = [{
        'order': '<?= $payment_id; ?>',
        'order_price': '<?= $payment->total; ?>',
        'order_email': '<?= $payment->email; ?>',
        'products': [
            <?php
            foreach ( $payment->downloads as $download ) {
                $d = [];
                $d['id'] = $download['id'];
                $d['name'] = edd_get_download( $download['id'] )->post_title;
                echo json_encode($d) . ',';
            }
            ?>
        ],
    }];
</script>