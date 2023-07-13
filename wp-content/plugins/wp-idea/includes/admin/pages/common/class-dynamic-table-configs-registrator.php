<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\common;

use bpmj\wpidea\admin\pages\affiliate_program\Affiliate_Program_Partners_Table_Config_Provider;
use bpmj\wpidea\admin\pages\affiliate_program\Affiliate_Table_Config_Provider;
use bpmj\wpidea\admin\pages\affiliate_program_redirections\Affiliate_Redirections_Table_Config_Provider;
use bpmj\wpidea\admin\pages\bundle_list\Bundles_Table_Config_Provider;
use bpmj\wpidea\admin\pages\certificates\Certificates_Table_Config_Provider;
use bpmj\wpidea\admin\pages\course_list\Course_List_Table_Config_Provider;
use bpmj\wpidea\admin\pages\customers\Customer_Table_Config_Provider;
use bpmj\wpidea\admin\pages\digital_products_list\Digital_Products_Table_Config_Provider;
use bpmj\wpidea\admin\pages\discount_codes\Discounts_Table_Config_Provider;
use bpmj\wpidea\admin\pages\expiring_customers\Expiring_Customers_Table_Config_Provider;
use bpmj\wpidea\admin\pages\increasing_sales\Increasing_Sales_Table_Config_Provider;
use bpmj\wpidea\admin\pages\logs\Log_Table_Config_Provider;
use bpmj\wpidea\admin\pages\opinions\Opinions_Table_Config_Provider;
use bpmj\wpidea\admin\pages\payments_history\Order_Table_Config_Provider;
use bpmj\wpidea\admin\pages\physical_products\Physical_Products_Table_Config_Provider;
use bpmj\wpidea\admin\pages\price_history\Price_History_Table_Config_Provider;
use bpmj\wpidea\admin\pages\quizzes\Quiz_Table_Config_Provider;
use bpmj\wpidea\admin\pages\services\Services_Table_Config_Provider;
use bpmj\wpidea\admin\pages\students\Student_Table_Config_Provider;
use bpmj\wpidea\admin\pages\users\User_Table_Config_Provider;
use bpmj\wpidea\admin\pages\videos\Videos_Table_Config_Provider;
use bpmj\wpidea\admin\pages\webhooks\Webhook_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config_Provider_Registry;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\templates_system\admin\Templates_List_Table_Config_Provider;

class Dynamic_Table_Configs_Registrator
{
    private Dynamic_Table_Config_Provider_Registry $dynamic_table_config_registry;
    private Student_Table_Config_Provider $student_table_config_provider;
    private Log_Table_Config_Provider $log_table_config_provider;
    private Order_Table_Config_Provider $order_table_config_provider;
    private Customer_Table_Config_Provider $customer_table_config_provider;
    private Quiz_Table_Config_Provider $quiz_table_config_provider;
    private Certificates_Table_Config_Provider $certificates_table_config_provider;
    private Webhook_Table_Config_Provider $webhook_table_config_provider;
    private Discounts_Table_Config_Provider $discounts_table_config_provider;
    private Affiliate_Table_Config_Provider $affiliate_table_config_provider;
    private Affiliate_Program_Partners_Table_Config_Provider $affiliate_program_partners_table_config_provider;
    private Videos_Table_Config_Provider $videos_table_config_provider;
    private Affiliate_Redirections_Table_Config_Provider $affiliate_redirections_table_config_provider;
    private Templates_List_Table_Config_Provider $templates_list_table_config_provider;
    private Course_List_Table_Config_Provider $course_list_table_config_provider;
    private Services_Table_Config_Provider $services_table_config_provider;
    private Digital_Products_Table_Config_Provider $digital_products_table_config_provider;
    private User_Table_Config_Provider $user_table_config_provider;
    private Expiring_Customers_Table_Config_Provider $expiring_customers_table_config_provider;
    private Increasing_Sales_Table_Config_Provider $increasing_sales_table_config_provider;
    private Bundles_Table_Config_Provider $bundles_table_config_provider;
    private Price_History_Table_Config_Provider $price_history_table_config_provider;
    private Physical_Products_Table_Config_Provider $physical_products_table_config_provider;
    private Opinions_Table_Config_Provider $opinions_table_config_provider;

    public function __construct(
        Dynamic_Table_Config_Provider_Registry $dynamic_table_config_registry,
        Student_Table_Config_Provider $student_table_config_provider,
        Log_Table_Config_Provider $log_table_config_provider,
        Order_Table_Config_Provider $order_table_config_provider,
        Customer_Table_Config_Provider $customer_table_config_provider,
        Quiz_Table_Config_Provider $quiz_table_config_provider,
        Certificates_Table_Config_Provider $certificates_table_config_provider,
        Webhook_Table_Config_Provider $webhook_table_config_provider,
        Discounts_Table_Config_Provider $discounts_table_config_provider,
        Affiliate_Table_Config_Provider $affiliate_table_config_provider,
        Affiliate_Program_Partners_Table_Config_Provider $affiliate_program_partners_table_config_provider,
        Videos_Table_Config_Provider $videos_table_config_provider,
        Affiliate_Redirections_Table_Config_Provider $affiliate_redirections_table_config_provider,
        Templates_List_Table_Config_Provider $templates_list_table_config_provider,
        Services_Table_Config_Provider $services_table_config_provider,
        Course_List_Table_Config_Provider $course_list_table_config_provider,
        Digital_Products_Table_Config_Provider $digital_products_table_config_provider,
        User_Table_Config_Provider $user_table_config_provider,
        Expiring_Customers_Table_Config_Provider $expiring_customers_table_config_provider,
        Increasing_Sales_Table_Config_Provider $increasing_sales_table_config_provider,
        Bundles_Table_Config_Provider $bundles_table_config_provider,
        Price_History_Table_Config_Provider $price_history_table_config_provider,
        Physical_Products_Table_Config_Provider $physical_products_table_config_provider,
        Opinions_Table_Config_Provider $opinions_table_config_provider
    ) {
        $this->dynamic_table_config_registry = $dynamic_table_config_registry;
        $this->student_table_config_provider = $student_table_config_provider;
        $this->log_table_config_provider = $log_table_config_provider;
        $this->order_table_config_provider = $order_table_config_provider;
        $this->customer_table_config_provider = $customer_table_config_provider;
        $this->quiz_table_config_provider = $quiz_table_config_provider;
        $this->certificates_table_config_provider = $certificates_table_config_provider;
        $this->webhook_table_config_provider = $webhook_table_config_provider;
        $this->discounts_table_config_provider = $discounts_table_config_provider;
        $this->affiliate_table_config_provider = $affiliate_table_config_provider;
        $this->affiliate_program_partners_table_config_provider = $affiliate_program_partners_table_config_provider;
        $this->videos_table_config_provider = $videos_table_config_provider;
        $this->affiliate_redirections_table_config_provider = $affiliate_redirections_table_config_provider;
        $this->templates_list_table_config_provider = $templates_list_table_config_provider;
        $this->course_list_table_config_provider = $course_list_table_config_provider;
        $this->services_table_config_provider = $services_table_config_provider;
        $this->digital_products_table_config_provider = $digital_products_table_config_provider;
        $this->user_table_config_provider = $user_table_config_provider;
        $this->expiring_customers_table_config_provider = $expiring_customers_table_config_provider;
        $this->increasing_sales_table_config_provider = $increasing_sales_table_config_provider;
        $this->bundles_table_config_provider = $bundles_table_config_provider;
        $this->price_history_table_config_provider = $price_history_table_config_provider;
        $this->physical_products_table_config_provider = $physical_products_table_config_provider;
        $this->opinions_table_config_provider = $opinions_table_config_provider;
    }

    public function init(): void
    {
        $this->register($this->student_table_config_provider);
        $this->register($this->log_table_config_provider);
        $this->register($this->order_table_config_provider);
        $this->register($this->customer_table_config_provider);
        $this->register($this->quiz_table_config_provider);
        $this->register($this->certificates_table_config_provider);
        $this->register($this->webhook_table_config_provider);
        $this->register($this->discounts_table_config_provider);
        $this->register($this->affiliate_table_config_provider);
        $this->register($this->videos_table_config_provider);
        $this->register($this->affiliate_redirections_table_config_provider);
        $this->register($this->affiliate_program_partners_table_config_provider);
        $this->register($this->templates_list_table_config_provider);
        $this->register($this->services_table_config_provider);
        $this->register($this->course_list_table_config_provider);
        $this->register($this->digital_products_table_config_provider);
        $this->register($this->user_table_config_provider);
        $this->register($this->expiring_customers_table_config_provider);
        $this->register($this->increasing_sales_table_config_provider);
        $this->register($this->bundles_table_config_provider);
        $this->register($this->price_history_table_config_provider);
        $this->register($this->physical_products_table_config_provider);
        $this->register($this->opinions_table_config_provider);
    }

    private function register(Interface_Dynamic_Table_Config_Provider $provider): void
    {
        $this->dynamic_table_config_registry->register_provider($provider);
    }
}