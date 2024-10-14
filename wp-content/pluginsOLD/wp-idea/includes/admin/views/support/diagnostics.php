<div class="diagnostics">
    <h4 class="support__title support__title--small">Diagnostyka</h4>

    <div class="diagnostics__table">
        <table>
            <?php foreach( $support->get_diagnostics()->get_items() as $item ): ?>
            <tr>
                <td><?= $item->get_name() ?></td>
                <td><?= $item->get_current_value() ?></td>
                <td><div class="dashicons dashicons-<?= $item->get_icon() ?> diagnostics__state-icon diagnostics__state-icon--<?= $item->get_status() ?>"></div></td>
                <td><?= $item->get_fix_hint() ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    
    <?php include 'copy-diagnostic-data.php'; ?>
</div>
