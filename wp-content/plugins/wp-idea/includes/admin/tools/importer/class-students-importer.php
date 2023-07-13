<?php

namespace bpmj\wpidea\admin\tools\importer;

use EDD_Customer;

class Students_Importer
{
    private const OPTION_IMPORT_STATUS = 'bpmj_eddcm_import_status';
    private const IMPORT_STATUS_IN_PROGRESS = 'in_progress';
    private const IMPORT_STATUS_IDLE = 'idle';
    private const IMPORT_STATUS_DONE = 'done';

    private const OPTION_IMPORT_PROGRESS_PERCENTAGE = 'bpmj_eddcm_import_progress';
    private const PROGRESS_PERCENTAGE_NO_PROGRESS = 0;

    private $import_status;

    /**
     * Error added when validating
     * @var array
     */
    private static $import_errors = [];

    private $processor;

    public function __construct()
    {
        $this->maybe_init_processor();
        $this->init_actions();
    }

    private function maybe_init_processor(): void
    {
        $is_import_requested = !empty($this->get_import_post_data());

        if (!$is_import_requested && $this->is_import_idle()) {
            return;
        }

        add_action('plugins_loaded', [$this, 'init_processor']);
    }

    public function init_processor(): void
    {
        $this->processor = new Import_Single_Student_Process($this);
    }

    private function init_actions(): void
    {
        add_action('admin_init', [$this, 'import_students']);

        add_action('admin_notices', [$this, 'import_students_notices']);
    }

    public function import_students_notices(): void
    {
        if ($this->is_import_idle()) {
            return;
        }

        if ($this->is_import_done()) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php
                    _e('Importing completed', BPMJ_EDDCM_DOMAIN); ?></p>
                <?php
                if (!empty($wpidea_settings['errors'])) : ?>
                    <p><?php
                        _e('The following lines have not been added due to an incorrect e-mail address:', BPMJ_EDDCM_DOMAIN); ?></p>
                    <ul style="list-style: disc; margin-left: 15px;">
                        <?php
                        foreach ($wpidea_settings['errors'] as $error) : ?>
                            <li><?php
                                echo $error; ?></li>
                        <?php
                        endforeach; ?>
                    </ul>
                <?php
                endif; ?>
            </div>
            <?php

            $this->reset_import_data();
        } else {
            if ($this->is_import_in_progress() && !$this->should_import_be_stopped()) {
                $progress_percentage = $this->get_import_progress();

                ?>
                <div class="notice notice-info is-dismissible">
                    <p><?php
                        _e('Due to the import of a large file, the import has been carried out in the background. Please wait.', BPMJ_EDDCM_DOMAIN); ?>
                        &nbsp;(<?= $progress_percentage ?>%)&nbsp;<?= Links::get_break_the_import_link() ?></p>
                </div>
                <?php
            }
        }
    }

    public function import_students(): void
    {
        if (isset($_GET['break-import'])) {
            $this->mark_import_as_stopped();
            $this->reset_import_data();
            $this->redirect_to_the_tools_page();
        }

        if (empty($this->get_import_post_data())) {
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'], 'import_students')) {
            return;
        }

        if (empty($this->get_import_post_data('file'))) {
            add_action('admin_notices', function () {
                ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php
                        _e('You must select a CSV file', BPMJ_EDDCM_DOMAIN); ?></p>
                </div>
                <?php
            });
            return;
        }

        $wpidea_settings = get_option('wp_idea_import', []);
        if (!empty($wpidea_settings)) {
            add_action('admin_notices', function () {
                ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php
                        _e('There is currently another import. Please try again later or finish the current import.', BPMJ_EDDCM_DOMAIN); ?>
                        &nbsp;<?= Links::get_break_the_import_link() ?></p>
                </div>
                <?php
            });
            return;
        }

        $send_accesses = !empty($this->get_import_post_data('access'));
        $send_notifications = !empty($this->get_import_post_data('notification'));
        $add_to_mailings = !empty($this->get_import_post_data('mailing'));
        $courses = $this->get_import_post_data('courses');

        $import_file = $this->get_import_post_data('file');
        $import_file_contents = file_get_contents($import_file);

        // check import file encoding
        if (!in_array(mb_detect_encoding($import_file_contents), ['UTF8', 'UTF-8', 'ASCII'])) {
            add_action('admin_notices', function () {
                ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php
                        _e('File content type must by UTF8 or ASCII', BPMJ_EDDCM_DOMAIN); ?></p>
                </div>
                <?php
            });
            return;
        }

        update_option('wp_idea_import_stop', false, false);

        // update import progress
        $this->update_progress_percentage(self::PROGRESS_PERCENTAGE_NO_PROGRESS);

        // create import id
        $import_id = md5(time() . wp_rand());

        // get import file content to array
        $students_to_import = File_Parser::get_array_from_csv($import_file);

        // delete import file after all needed data have been pulled out of it
        $this->delete_import_file();

        //prepare import data
        $import_data = new Import_Data;
        $import_data->students_to_import = $students_to_import;
        $import_data->all_lines_count = count($students_to_import);
        $import_data->send_accesses = $send_accesses;
        $import_data->send_notifications = $send_notifications;
        $import_data->add_to_mailings = $add_to_mailings;
        $import_data->courses = $courses;
        $import_data->import_id = $import_id;

        $this->set_import_status(self::IMPORT_STATUS_IN_PROGRESS);

        $this->run_students_import_queue($import_data);
    }

    public function import_user(Import_Data $import_data): bool
    {
        $user = $import_data->students_to_import[0] ?? null;

        if (empty($user)) {
            return false;
        }

        $this->update_progress_percentage(($import_data->currently_processed_user_index / $import_data->all_lines_count) * 100);

        $user_email = trim($user[0]);
        if (!is_email($user_email)) {
            $invalid_email_message = __('Invalid email', BPMJ_EDDCM_DOMAIN) . ': "' . $user_email . '"';

            $this->add_import_error($invalid_email_message);

            return false;
        }

        $first_name = '';
        $last_name = '';

        if (isset($user[1])) {
            $first_name = sanitize_text_field($user[1]);
        }

        if (isset($user[2])) {
            $last_name = sanitize_text_field($user[2]);
        }

        if (!$import_data->send_accesses) {
            add_filter('edd_get_option', [$this, 'action_edd_option_disable_new_user_email_notification'], 10, 3);
        }

        if (!$import_data->add_to_mailings) {
            add_filter('wpi_disable_mailing', function ($value) {
                return true;
            });
        }

        if (!empty($import_data->courses)) {
            $products = [];
            foreach ($import_data->courses as $course) {
                $exploded_course_id = explode('-', $course);

                $products[$exploded_course_id[0]] = [
                    'quantity' => 1,
                    'price_id' => isset($exploded_course_id[1]) ? $exploded_course_id[1] : false,
                ];
            }

            if ($import_data->send_notifications) {
                add_filter('edd_admin_notice_emails', [$this, 'action_edd_admin_notice_emails']);
            }

            WPI()->api->create_order([
                'products' => $products,
                'customer' => [
                    'email' => $user[0],
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                ],
                'options' => [
                    'disable_receipt' => !$import_data->send_notifications,
                ],
            ]);
        } else {
            $u = get_user_by('email', $user[0]);
            if (false === $u) {
                $u_id = edd_auto_register()->create_user([
                    'user_info' => [
                        'email' => $user[0],
                        'id' => 0,
                        'first_name' => '',
                        'last_name' => '',
                    ],
                ], 0);

                $u = get_user_by('ID', $u_id);
            }

            $customer = new EDD_Customer();
            $customer->create([
                'user_id' => $u->ID,
                'name' => $first_name . ' ' . $last_name,
                'email' => $user[0],
            ]);
        }

        return true;
    }

    public function action_edd_option_disable_new_user_email_notification($value, $key, $default): bool
    {
        if ('edd_auto_register_disable_user_email' === $key) {
            return true;
        }

        return $value;
    }

    public function action_edd_admin_notice_emails(): array
    {
        return [];
    }


    public function return_empty_array_for_option($option): array
    {
        return [];
    }

    private function update_import_option(string $option_name, $new_value): void
    {
        $wpidea_settings = get_option('wp_idea_import', []);
        $wpidea_settings[$option_name] = $new_value;
        update_option('wp_idea_import', $wpidea_settings, false);
    }

    private function add_import_error(string $error_message): void
    {
        self::$import_errors[] = $error_message;

        $wpidea_settings = get_option('wp_idea_import', []);
        $wpidea_settings['errors'][] = $error_message;

        $this->update_import_option('errors', $wpidea_settings['errors']);
    }

    private function update_progress_percentage(int $percent): void
    {
        update_option(self::OPTION_IMPORT_PROGRESS_PERCENTAGE, $percent, false);
    }

    public function mark_import_as_done(): void
    {
        $this->set_import_status(self::IMPORT_STATUS_DONE);
    }

    private function mark_import_as_stopped(): void
    {
        update_option('wp_idea_import_stop', true, false);
    }

    /**
     * Return true if the "stop" flag has been set
     */
    public function should_import_be_stopped(): bool
    {
        return get_option('wp_idea_import_stop', false);
    }

    /**
     * Reset import data (set 'wp_idea_import' option to empty array)
     */
    public function reset_import_data(): void
    {
        update_option('wp_idea_import', [], false);
        $this->set_import_status(self::IMPORT_STATUS_IDLE);
        $this->update_progress_percentage(self::PROGRESS_PERCENTAGE_NO_PROGRESS);
        $this->clear_the_queue();
    }

    /**
     * Get import data from $_POST
     *
     * @param string $index_name Name of the index in the $_POST['wp_idea']['tools']['import_students'] array (all data will be returned if this param is empty)
     * @return mixed
     */
    private function get_import_post_data(string $index_name = null)
    {
        // return null if no import data in $_POST
        if (empty($_POST['wp_idea']['tools']['import_students'])) {
            return null;
        }

        // get import data
        $post_data = $_POST['wp_idea']['tools']['import_students'];

        // return all data if no index provided
        if (empty($index_name)) {
            return $post_data;
        }

        // return specific index value or null if it does not exists
        return !empty($post_data[$index_name])
            ? $post_data[$index_name]
            : null;
    }

    /**
     * Clear import $_POST data
     */
    private function clear_import_file_post_data(): void
    {
        // return null if no import data in $_POST
        if (empty($_POST['wp_idea']['tools']['import_students']['file'])) {
            return;
        }

        unset($_POST['wp_idea']['tools']['import_students']['file']);
    }

    private function delete_import_file(): void
    {
        // get file url from $_POST data
        $file_url = $this->get_import_post_data('file');
        if (empty($file_url)) {
            return;
        }

        // get attachment id by file url
        $attachment_id = attachment_url_to_postid($file_url);
        if (empty($attachment_id)) {
            return;
        }

        // remove attachment with file
        wp_delete_attachment($attachment_id);

        // remove file url from $_POST
        $this->clear_import_file_post_data();
    }

    private function redirect_to_the_tools_page(): void
    {
        wp_redirect(admin_url('admin.php?page=wp-idea-tools'));
        exit;
    }

    private function is_import_done(): bool
    {
        return $this->get_import_status() === self::IMPORT_STATUS_DONE;
    }

    private function is_import_in_progress(): bool
    {
        return $this->get_import_status() === self::IMPORT_STATUS_IN_PROGRESS;
    }

    private function is_import_idle(): bool
    {
        return $this->get_import_status() === self::IMPORT_STATUS_IDLE;
    }

    private function run_students_import_queue(Import_Data $import_data): void
    {
        foreach ($import_data->students_to_import as $i => $user) {
            if (empty($user)) {
                continue;
            }

            $data = clone $import_data;
            $data->currently_processed_user_index = $i;
            unset($data->students_to_import);
            $data->students_to_import = [$user];

            $this->processor->push_to_queue($data);
        }

        $this->processor->save()->dispatch();
    }

    private function get_import_status(): string
    {
        if ($this->import_status !== null) {
            return $this->import_status;
        }

        $this->import_status = get_option(self::OPTION_IMPORT_STATUS, self::IMPORT_STATUS_IDLE);

        return $this->import_status;
    }

    private function get_import_progress(): int
    {
        return get_option(self::OPTION_IMPORT_PROGRESS_PERCENTAGE, self::PROGRESS_PERCENTAGE_NO_PROGRESS);
    }

    private function set_import_status(string $status): void
    {
        update_option(self::OPTION_IMPORT_STATUS, $status, false);
    }

    private function clear_the_queue(): void
    {
        $this->processor = $this->processor ?? new Import_Single_Student_Process($this);

        $this->processor->clear_the_queue();
    }
}

class Links
{
    public static function get_break_the_import_link()
    {
        return '<a href="' . admin_url('admin.php?page=wp-idea-tools&break-import') . '">' . __('Stop the import', BPMJ_EDDCM_DOMAIN) . '</a>';
    }
}