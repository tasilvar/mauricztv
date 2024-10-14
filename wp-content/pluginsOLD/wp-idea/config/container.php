<?php

use bpmj\wpidea\admin\integrations\{Interface_Tracker, Onboard_Flow, Tracker_Data_Collector};
use bpmj\wpidea\admin\Menu;
use bpmj\wpidea\admin\pages\bundle_creator\Bundle_Creator_Renderer;
use bpmj\wpidea\admin\pages\customers\Customers_Page_Renderer;
use bpmj\wpidea\admin\pages\digital_product_creator\Digital_Product_Creator_Renderer;
use bpmj\wpidea\admin\pages\digital_product_editor\Digital_Product_Editor_Handler;
use bpmj\wpidea\admin\pages\logs\Logs_Page_Renderer;
use bpmj\wpidea\admin\pages\payments_history\Payments_Page_Renderer;
use bpmj\wpidea\admin\pages\quizzes\Quizzes_Page_Renderer;
use bpmj\wpidea\admin\pages\students\Student_Page_Renderer;
use bpmj\wpidea\admin\pages\webhooks\{Interface_Webhooks_Form, Webhooks_Form};
use bpmj\wpidea\admin\settings\core\persistence\Interface_Settings_Persistence;
use bpmj\wpidea\admin\settings\core\services\{Interface_Settings_Tab_Scripts, Settings_Tab_Scripts};
use bpmj\wpidea\admin\settings\infrastructure\persistence\Legacy_Settings_Persistence;
use bpmj\wpidea\admin\subscription\api\{Interface_Subscription_API, Subscription_API};
use bpmj\wpidea\admin\subscription\models\{Interface_Readable_Subscription_System_Data, Metadata};
use bpmj\wpidea\admin\tables\dynamic\{Dynamic_Table_Factory,
	Interface_Dynamic_Table_Factory,
	user_settings\Interface_User_Table_Settings_Service,
	user_settings\User_Table_Settings_Service};
use bpmj\wpidea\admin\video\usage\Cached_Video_Space_Checker;
use bpmj\wpidea\admin\video\usage\Deprecated_Video_Space_Checker;
use bpmj\wpidea\certificates\{Certificate_Template,
	Certificate_Wp_Repository,
	Interface_Certificate_Repository,
	regenerator\Certificate_Wp_Regenerator,
	regenerator\Interface_Certificate_Regenerator};
use bpmj\wpidea\courses\acl\{Interface_Variable_Prices_ACL, Variable_Prices_ACL};
use bpmj\wpidea\courses\core\repositories\{Interface_Course_Structure_Repository,
	Interface_Course_With_Product_Repository};
use bpmj\wpidea\courses\infrastructure\repositories\{Course_Structure_Repository, Course_With_Product_Repository};
use bpmj\wpidea\digital_products\persistence\Digital_Product_Persistence;
use bpmj\wpidea\digital_products\repository\Digital_Product_Wp_Repository;
use bpmj\wpidea\digital_products\repository\Interface_Digital_Product_Repository;
use bpmj\wpidea\digital_products\service\Digital_Product_Creator_Service;
use bpmj\wpidea\digital_products\service\Interface_Digital_Product_Creator_Service;
use bpmj\wpidea\environment\{Interface_Site, Site};
use bpmj\wpidea\events\{Interface_Event_Emitter, Interface_Events, WP_Actions_Based_Events};
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\helpers\Interface_Debug_Helper;
use bpmj\wpidea\helpers\Interface_Response_Helper;
use bpmj\wpidea\helpers\Wp_Debug_Helper;
use bpmj\wpidea\helpers\Wp_Response_Helper;
use bpmj\wpidea\infrastructure\assets\Interface_Script_Loader;
use bpmj\wpidea\infrastructure\assets\Wp_Script_Loader;
use bpmj\wpidea\infrastructure\database\{Interface_Database, Interface_Sql_Helper, Sql_Helper, Wpdb as Wpi_Wpdb};
use bpmj\wpidea\infrastructure\io\{Disk_Space_Checker,
	Interface_Disk_Space_Checker,
	video\Interface_Video_Space_Checker};
use bpmj\wpidea\infrastructure\logs\{handler\Monolog_Db_Log_Handler,
	model\Log_Source,
	persistence\Interface_Logs_Persistence,
	persistence\Logs_Persistence,
	repository\Interface_Log_Repository,
	repository\Log_Repository};
use bpmj\wpidea\infrastructure\mail\Interface_Mailer;
use bpmj\wpidea\infrastructure\mail\WP_Mailer;
use bpmj\wpidea\infrastructure\scheduler\{Interface_Scheduler, WP_Scheduler};
use bpmj\wpidea\infrastructure\system\date\{Interface_System_Datetime_Info, System_Datetime_Info};
use bpmj\wpidea\infrastructure\theme\Interface_Theme_Support;
use bpmj\wpidea\infrastructure\theme\Wp_Theme_Support;
use bpmj\wpidea\integrations\Interface_Invoice_Service_Status_Checker;
use bpmj\wpidea\integrations\invoices\data\Interface_Invoice_Remote_Id_Storage;
use bpmj\wpidea\integrations\invoices\data\Wp_Invoice_Remote_Id_Storage;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\learning\course\{Author_Read_Only_Repository as Author_Repository,
	content\Course_Content_Wp_Read_Only_Repository,
	content\Interface_Readable_Course_Content_Repository,
	Interface_Readable_Author_Repository};
use bpmj\wpidea\learning\course\Course_Wp_Read_Only_Repository;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\learning\quiz\api\Interface_Quiz_Api;
use bpmj\wpidea\learning\quiz\api\Quiz_Api;
use bpmj\wpidea\learning\quiz\Interface_Resolved_Quiz_Repository;
use bpmj\wpidea\learning\quiz\repository\{Interface_Quiz_Settings_Repository, Quiz_Settings_Repository};
use bpmj\wpidea\learning\quiz\Resolved_Quiz_Repository;
use bpmj\wpidea\learning\services\Content_Parent_Getter;
use bpmj\wpidea\learning\services\Interface_Content_Parent_Getter;
use bpmj\wpidea\learning\services\Interface_Url_Resolver;
use bpmj\wpidea\learning\services\Url_Resolver;
use bpmj\wpidea\modules\videos\Videos_Module;
use bpmj\wpidea\options\{Interface_Options, WP_Options};
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\packages\Packages_API;
use bpmj\wpidea\physical_product\persistence\Interface_Physical_Product_Persistence;
use bpmj\wpidea\physical_product\persistence\Physical_Product_Persistence;
use bpmj\wpidea\physical_product\repository\Interface_Physical_Product_Repository;
use bpmj\wpidea\physical_product\repository\Physical_Product_Wp_Repository;
use bpmj\wpidea\physical_product\service\Interface_Physical_Product_Creator_Service;
use bpmj\wpidea\physical_product\service\Physical_Product_Creator_Service;
use bpmj\wpidea\routing\{Interface_Url_Generator, Url_Generator};
use bpmj\wpidea\sales\discount_codes\core\repositories\Interface_Discount_Repository;
use bpmj\wpidea\sales\discount_codes\infrastructure\repositories\Discount_Repository;
use bpmj\wpidea\sales\order\{Interface_Orders_Repository,
	Orders_Repository,
	services\Interface_Orders_Service,
	services\Orders_Service};
use bpmj\wpidea\sales\order\api\{Interface_Order_API, Order_API};
use bpmj\wpidea\sales\payments\Interface_Payment_Gates;
use bpmj\wpidea\sales\payments\Payment_Gates;
use bpmj\wpidea\sales\price_history\core\provider\Interface_Price_History_Provider;
use bpmj\wpidea\sales\price_history\core\provider\Interface_Product_Data_Provider;
use bpmj\wpidea\sales\price_history\infrastructure\persistence\Interface_Price_History_Persistence;
use bpmj\wpidea\sales\price_history\infrastructure\persistence\Price_History_Persistence;
use bpmj\wpidea\sales\price_history\infrastructure\provider\Price_History_Provider;
use bpmj\wpidea\sales\price_history\infrastructure\provider\Product_Data_Provider;
use bpmj\wpidea\sales\product\acl\Interface_Product_Variable_Prices_ACL;
use bpmj\wpidea\sales\product\acl\Product_Variable_Prices_ACL;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\sales\product\api\Product_API;
use bpmj\wpidea\sales\product\providers\Interface_Product_Config_Provider;
use bpmj\wpidea\sales\product\providers\Product_Config_Provider;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\sales\product\repository\Product_Wp_Repository;
use bpmj\wpidea\sales\product\service\Interface_Url_Resolver as Interface_Product_Url_Resolver;
use bpmj\wpidea\sales\product\service\Url_Resolver as Product_Url_Resolver;
use bpmj\wpidea\service\persistence\Interface_Service_Persistence;
use bpmj\wpidea\service\persistence\Service_Persistence;
use bpmj\wpidea\service\repository\Interface_Service_Repository;
use bpmj\wpidea\service\repository\Service_Wp_Repository;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\shared\infrastructure\controllers\Interface_Rest_Controller_Error_Handler;
use bpmj\wpidea\shared\infrastructure\controllers\Interface_Rest_Registration_Service;
use bpmj\wpidea\shared\infrastructure\controllers\Rest_Controller_WP_Error_Handler;
use bpmj\wpidea\shared\infrastructure\controllers\Rest_Registration_Service;
use bpmj\wpidea\students\persistence\Interface_Student_Persistence;
use bpmj\wpidea\students\persistence\Student_Persistence;
use bpmj\wpidea\students\repository\Interface_Student_Repository;
use bpmj\wpidea\students\repository\Student_Wp_Repository;
use bpmj\wpidea\templates_system\{Templates_System};
use bpmj\wpidea\templates_system\admin\{modules\Interface_Templates_System_Modules_Factory,
	modules\New_Templates_System_Modules_Factory,
	modules\Old_Templates_System_Modules_Factory};
use bpmj\wpidea\translator\{I18n_Handler, Interface_Translator};
use bpmj\wpidea\user\{Interface_Current_User_Getter,
	Interface_User,
	Interface_User_Metadata_Service,
	Interface_User_Repository,
	User,
	User_Metadata_Service,
	User_Wp_Repository,
	Wp_Current_User_Getter};
use bpmj\wpidea\user\api\{Interface_User_API, User_API};
use bpmj\wpidea\user\Interface_User_Permissions_Service;
use bpmj\wpidea\user\User_Permissions_Wp_Service;
use bpmj\wpidea\view\{Admin_Bar, Interface_Admin_Bar, Interface_View_Provider, View_Provider};
use bpmj\wpidea\wolverine\product\Repository as Product_Repository;
use bpmj\wpidea\WP_Actions;
use bpmj\wpidea\WP_Filters;
use bpmj\wpidea\WP_Redirector;
use GuzzleHttp\{Client, ClientInterface};
use ItalyStrap\Cache\SimpleCache;
use Monolog\{Logger, Processor\PsrLogMessageProcessor};
use Psr\{Container\ContainerInterface, Log\LoggerInterface, SimpleCache\CacheInterface};

return [
	Certificate_Template::class => DI\autowire()
        ->method('set_certificate_repository', DI\get(Certificate_Wp_Repository::class))
        ->method('set_course_repository', DI\get(Course_Wp_Read_Only_Repository::class))
        ->method('set_user_repository', DI\get(User_Wp_Repository::class))
        ->method('set_product_repository', DI\get(Product_Repository::class))
        ->method('set_author_repository', DI\get(Author_Repository::class)),

	Product_Repository::class => DI\create(),
	Interface_Readable_Author_Repository::class => DI\autowire(Author_Repository::class),
	Interface_Options::class => DI\autowire(WP_Options::class),

	Interface_Templates_System_Modules_Factory::class => DI\factory(function (
        Templates_System $templates_system,
        ContainerInterface $container
    ) {
        $factory_class = $templates_system->is_new_templates_system_enabled()
            ? New_Templates_System_Modules_Factory::class
            : Old_Templates_System_Modules_Factory::class;

        return $container->get($factory_class);
    }),
	Interface_Actions::class => DI\autowire(WP_Actions::class),
	Interface_Events::class => DI\autowire(WP_Actions_Based_Events::class),
	Interface_Event_Emitter::class => DI\autowire(WP_Actions_Based_Events::class),
	Interface_Disk_Space_Checker::class => DI\autowire(Disk_Space_Checker::class),
	Interface_Filters::class => DI\autowire(WP_Filters::class),
	Interface_Mailer::class => DI\autowire(WP_Mailer::class),
	CacheInterface::class => DI\autowire(SimpleCache::class),
	Interface_Redirector::class => DI\autowire(WP_Redirector::class),
	Interface_Translator::class => DI\autowire(I18n_Handler::class),
	Interface_Site::class => DI\autowire(Site::class),
	Interface_Url_Generator::class => DI\autowire(Url_Generator::class),
	Interface_Readable_Subscription_System_Data::class => DI\autowire(Metadata::class),
	Interface_Tracker::class => DI\autowire(Onboard_Flow::class),
	Interface_Database::class => DI\autowire(Wpi_Wpdb::class),
	Interface_Logs_Persistence::class => DI\autowire(Logs_Persistence::class),
	Interface_Log_Repository::class => DI\autowire(Log_Repository::class),
	Interface_Webhooks_Form::class => DI\autowire(Webhooks_Form::class),
	Interface_Settings::class => DI\autowire(LMS_Settings::class),
	Interface_Scheduler::class => DI\autowire(WP_Scheduler::class),
	ClientInterface::class => DI\autowire(Client::class),
	Tracker_Data_Collector::class => DI\autowire(),
	LoggerInterface::class => DI\factory(function (
        Monolog_Db_Log_Handler $handler
    ) {
        $logger = new Logger(Log_Source::DEFAULT);

        $logger->pushHandler($handler);
        $logger->pushProcessor(new PsrLogMessageProcessor());

        return $logger;
    }),
	Interface_View_Provider::class => DI\autowire(View_Provider::class),
	Interface_Dynamic_Table_Factory::class => DI\autowire(Dynamic_Table_Factory::class),
	Interface_System_Datetime_Info::class => DI\autowire(System_Datetime_Info::class),
	Interface_Orders_Repository::class => DI\autowire(Orders_Repository::class),
	Interface_Orders_Service::class => DI\autowire(Orders_Service::class),
	Interface_User_Table_Settings_Service::class        => DI\autowire(User_Table_Settings_Service::class),
	Interface_User_Repository::class                    => DI\autowire(User_Wp_Repository::class),
	Interface_Current_User_Getter::class                => DI\autowire(Wp_Current_User_Getter::class),
	Interface_User::class                               => DI\autowire(User::class),
	Interface_User_Metadata_Service::class              => DI\autowire(User_Metadata_Service::class),
	Interface_Readable_Course_Repository::class         => DI\autowire(Course_Wp_Read_Only_Repository::class),
	Interface_Readable_Course_Content_Repository::class => DI\autowire(Course_Content_Wp_Read_Only_Repository::class),
	Interface_Product_Repository::class                 => DI\autowire(Product_Wp_Repository::class),
	Interface_Debug_Helper::class                       => DI\autowire(Wp_Debug_Helper::class),
	Interface_Response_Helper::class                    => DI\autowire(Wp_Response_Helper::class),
	Interface_Resolved_Quiz_Repository::class           => DI\autowire(Resolved_Quiz_Repository::class),
	Interface_Quiz_Settings_Repository::class => DI\autowire(Quiz_Settings_Repository::class),
    Interface_User_Permissions_Service::class => DI\autowire(User_Permissions_Wp_Service::class),
    Interface_Certificate_Repository::class => DI\autowire(Certificate_Wp_Repository::class),
    Interface_Certificate_Regenerator::class => DI\autowire(Certificate_Wp_Regenerator::class),
    Menu::class => DI\autowire(Menu::class)
        ->constructorParameter('logs_page_renderer', DI\autowire(Logs_Page_Renderer::class))
        ->constructorParameter('payments_page_renderer', DI\autowire(Payments_Page_Renderer::class))
        ->constructorParameter('customers_page_renderer', DI\autowire(Customers_Page_Renderer::class))
        ->constructorParameter('quizzes_page_renderer', DI\autowire(Quizzes_Page_Renderer::class))
        ->constructorParameter('students_page_renderer', DI\autowire(Student_Page_Renderer::class))
        ->constructorParameter(
            'digital_product_creator_page_renderer',
            DI\autowire(Digital_Product_Creator_Renderer::class)
        )
        ->constructorParameter('bundle_creator_page_renderer', DI\autowire(Bundle_Creator_Renderer::class)),
	Interface_Digital_Product_Creator_Service::class => DI\autowire(Digital_Product_Creator_Service::class),
	Interface_Digital_Product_Repository::class => DI\autowire(Digital_Product_Wp_Repository::class)
        ->constructorParameter('persistence', DI\autowire(Digital_Product_Persistence::class)),
	Digital_Product_Editor_Handler::class => DI\autowire(Digital_Product_Editor_Handler::class),
	Interface_Video_Space_Checker::class => DI\factory(function (
        Videos_Module $videos_module,
        Deprecated_Video_Space_Checker $deprecated_video_space_checker,
        Cached_Video_Space_Checker $cached_video_space_checker
    ) {
        if ($videos_module->is_enabled()) {
            $decorated = $videos_module->get_video_space_checker();
        } else {
            $decorated = $deprecated_video_space_checker;
        }

        $cached_video_space_checker->set_decorated(
            $decorated
        );

        return $cached_video_space_checker;
    }),
	Interface_Rest_Controller_Error_Handler::class => DI\autowire(Rest_Controller_WP_Error_Handler::class),
	Interface_Rest_Registration_Service::class => DI\autowire(Rest_Registration_Service::class),
	Interface_Discount_Repository::class => DI\autowire(Discount_Repository::class),
	Interface_Invoice_Remote_Id_Storage::class => DI\autowire(
        Wp_Invoice_Remote_Id_Storage::class
    ),
	Interface_Invoice_Service_Status_Checker::class => DI\factory(function () {
        return WPI()->diagnostic;
    }),
	Interface_Service_Repository::class => DI\autowire(Service_Wp_Repository::class),
	Interface_Service_Persistence::class => DI\autowire(Service_Persistence::class),
	Interface_Settings_Persistence::class => DI\autowire(Legacy_Settings_Persistence::class),
	Interface_Url_Resolver::class => DI\autowire(Url_Resolver::class),
	Interface_User_API::class => DI\autowire(User_API::class),
	Interface_Subscription_API::class => DI\autowire(Subscription_API::class),
	Interface_Product_API::class => DI\autowire(Product_API::class),
	Interface_Product_Url_Resolver::class => DI\autowire(Product_Url_Resolver::class),
	Interface_Settings_Tab_Scripts::class => DI\autowire(Settings_Tab_Scripts::class),
	Interface_Sql_Helper::class => DI\autowire(Sql_Helper::class),
	Interface_Course_With_Product_Repository::class => DI\autowire(Course_With_Product_Repository::class),
	Interface_Payment_Gates::class => DI\autowire(Payment_Gates::class),
	Interface_Course_Structure_Repository::class => DI\autowire(Course_Structure_Repository::class),
	Interface_Variable_Prices_ACL::class => DI\autowire(Variable_Prices_ACL::class),
	Interface_Product_Variable_Prices_ACL::class => DI\autowire(Product_Variable_Prices_ACL::class),
	Interface_Price_History_Provider::class => DI\autowire(Price_History_Provider::class),
	Interface_Price_History_Persistence::class => DI\autowire(Price_History_Persistence::class),
	Interface_Product_Data_Provider::class => DI\autowire(Product_Data_Provider::class),
	Interface_Order_API::class => DI\autowire(Order_API::class),
	Interface_Script_Loader::class => DI\autowire(Wp_Script_Loader::class),
	Interface_Student_Repository::class => DI\autowire(Student_Wp_Repository::class),
	Interface_Student_Persistence::class => DI\autowire(Student_Persistence::class),
	Interface_Physical_Product_Persistence::class => DI\autowire(Physical_Product_Persistence::class),
	Interface_Physical_Product_Repository::class => DI\autowire(Physical_Product_Wp_Repository::class),
	Interface_Physical_Product_Creator_Service::class => DI\autowire(Physical_Product_Creator_Service::class),
	Interface_Packages_API::class => DI\autowire(Packages_API::class),
	Interface_Theme_Support::class => DI\autowire(Wp_Theme_Support::class),
	Interface_Content_Parent_Getter::class => DI\autowire(Content_Parent_Getter::class),
	Interface_Admin_Bar::class => DI\autowire(Admin_Bar::class),
	Interface_Product_Config_Provider::class => DI\autowire(Product_Config_Provider::class),
	Interface_Quiz_Api::class => DI\autowire(Quiz_Api::class),
];
