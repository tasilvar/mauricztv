<h3><?= __( 'Security', BPMJ_EDDCM_DOMAIN) ?></h3>
<table class="form-table">
    <tr id="ban">
        <th><label for="pass1"><?= __( 'Ban', BPMJ_EDDCM_DOMAIN) ?></label></th>
        <td>
            <?php if($user->isBanned()) { ?>
                <a href="#" class="button change-ban remove" data-type="remove"><?= __( 'Remove ban', BPMJ_EDDCM_DOMAIN) ?></a>
            <?php } else { ?>
                <a href="#" class="button change-ban add"  data-type="add"><?= __( 'Ban user account', BPMJ_EDDCM_DOMAIN) ?></a>
            <?php } ?>
            <p class="description ban-description" <?= ($user->isBanned()) ? '' : 'style="display:none"' ?>>
                <?php if($user->isBanned()) { ?>
                    <?= ($user->isBannedForever()) ? __( 'User banned forever', BPMJ_EDDCM_DOMAIN) : __( 'User banned to ', BPMJ_EDDCM_DOMAIN).date('H:i:s d-m-Y', $user->getBanDate()) ?>
                <?php } ?>
            </p>


        </td>
    </tr>
</table>

<script type="text/javascript">
    jQuery( function ( $ ) {
        $('.change-ban').click(function (e) {
            e.preventDefault();
            var $this = $(this), action;
            if($this.data('type') === 'remove'){
                action = 'remove-user-ban'
            } else {
                action = 'add-user-ban'
            }

            $.post( "#", {
                'action':action,
                'user_id': "<?= $user->getId() ?>",
                'nonce': "<?= $nonce ?>"
            }, function( data ) {

                if($this.data('type') == 'remove'){
                    $this.html("<?= __( 'Ban user account', BPMJ_EDDCM_DOMAIN) ?>")
                    $this.data('type', 'add')
                    $('.ban-description').hide()
                } else {
                    $this.html("<?= __( 'Remove ban', BPMJ_EDDCM_DOMAIN) ?>")
                    $this.data('type', 'remove')
                    $('.ban-description').show()
                    $('.ban-description').html(data.message)
                }

            }, "json");
        })
    });
</script>
