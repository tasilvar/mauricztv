<?php

/** @var array $headers */
/** @var array $items */
/** @var string $class */

?>

<table class="wpi-simple-table <?= $class ?>">
    <thead>
        <tr>
            <?php foreach ($headers as $index => $header): ?>
                <th class="header-<?= $index ?>"><?= $header ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>

    <tbody>

    <?php foreach ($items as $index => $data): ?>
    <tr class="item-<?= $index+1 ?>">
        <?php foreach ($data as $value): ?>
            <td><?= $value ?></td>
        <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>