<?php

use bpmj\wpidea\controllers\admin\Admin_Bundles_Controller;
use bpmj\wpidea\controllers\admin\Admin_Certificate_Templates_Controller;
use bpmj\wpidea\controllers\admin\Admin_Certificates_Ajax_Controller;
use bpmj\wpidea\controllers\admin\Admin_Courses_Controller;
use bpmj\wpidea\controllers\admin\Admin_Creator_Controller;
use bpmj\wpidea\controllers\admin\Admin_Digital_Products_Controller;
use bpmj\wpidea\controllers\admin\Admin_Discounts_Ajax_Controller;
use bpmj\wpidea\controllers\admin\Admin_Edit_Course_Controller;
use bpmj\wpidea\controllers\admin\Admin_Edit_Product_Controller;
use bpmj\wpidea\controllers\admin\Admin_Edit_Quiz_Controller;
use bpmj\wpidea\controllers\admin\Admin_Logs_Ajax_Controller;
use bpmj\wpidea\controllers\admin\Admin_Logs_Controller;
use bpmj\wpidea\controllers\admin\Admin_Notifications_Controller;
use bpmj\wpidea\controllers\admin\Admin_Opinions_Controller;
use bpmj\wpidea\controllers\admin\Admin_Payment_History_Ajax_Controller;
use bpmj\wpidea\controllers\admin\Admin_Physical_Products_Controller;
use bpmj\wpidea\controllers\admin\Admin_Redirect_Controller;
use bpmj\wpidea\controllers\admin\Admin_Services_Controller;
use bpmj\wpidea\controllers\admin\Admin_Settings_Fields_Ajax_Controller;
use bpmj\wpidea\controllers\admin\Admin_Templates_System_Controller;
use bpmj\wpidea\controllers\admin\Admin_Users_Controller;
use bpmj\wpidea\controllers\Certificate_Controller;
use bpmj\wpidea\controllers\Courses_Controller;
use bpmj\wpidea\controllers\Notices_Controller;
use bpmj\wpidea\controllers\Orders_Controller;
use bpmj\wpidea\controllers\Payment_Controller;
use bpmj\wpidea\controllers\Quiz_Controller;
use bpmj\wpidea\controllers\S3_Controller;

return [
    'admin/bundles' => Admin_Bundles_Controller::class,
    'admin/digital_products' => Admin_Digital_Products_Controller::class,
    'admin/services' => Admin_Services_Controller::class,
    'admin/physical_products' => Admin_Physical_Products_Controller::class,
    'admin/courses' => Admin_Courses_Controller::class,
    'admin/templates_system' => Admin_Templates_System_Controller::class,
    'admin/certificate_templates' => Admin_Certificate_Templates_Controller::class,
    'admin/logs_ajax' => Admin_Logs_Ajax_Controller::class,
    'admin/logs' => Admin_Logs_Controller::class,
    'admin/payment_history_ajax' => Admin_Payment_History_Ajax_Controller::class,
    'admin/certificates_ajax' => Admin_Certificates_Ajax_Controller::class,
    'admin/discounts_ajax' => Admin_Discounts_Ajax_Controller::class,
    'admin/notifications' => Admin_Notifications_Controller::class,
    'admin/settings_fields_ajax' => Admin_Settings_Fields_Ajax_Controller::class,
    'admin/users' => Admin_Users_Controller::class,
    'admin/redirect' => Admin_Redirect_Controller::class,
    'admin/opinions' => Admin_Opinions_Controller::class,

    'courses' => Courses_Controller::class,
    'certificate' => Certificate_Controller::class,
    's3' => S3_Controller::class,
    'admin/creator' => Admin_Creator_Controller::class,
    'admin/edit_course' => Admin_Edit_Course_Controller::class,
    'admin/edit_quiz' => Admin_Edit_Quiz_Controller::class,
    'admin/edit_product' => Admin_Edit_Product_Controller::class,
    'payment' => Payment_Controller::class,
    'quiz' => Quiz_Controller::class,
    'notices' => Notices_Controller::class,
    'orders' => Orders_Controller::class
];

