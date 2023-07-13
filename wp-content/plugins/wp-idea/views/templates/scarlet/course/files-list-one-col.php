<?php
/** @var array $files */
/** @var int $lesson_page_id */
/** @var bool $app_view_is_active */
?>

<div class='row'>
    <div class='col-sm-12'>
        <ul>
            <?php foreach ($files as $file_id => $file): ?>
                <?php

                $file_name = !empty($file['desc']) ? $file['desc'] : get_the_title($file_id);
                ?>
                <li><a href="<?= bpmj_eddpc_encrypt_link(wp_get_attachment_url($file_id), $lesson_page_id) ?>"
                        <?php if(!$app_view_is_active){ ?> target="_blank" <?php } ?>><i
                            class="far fa-file-<?php WPI()->templates->the_file_icon($file_id); ?>"></i><?= $file_name ?>
                </a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>