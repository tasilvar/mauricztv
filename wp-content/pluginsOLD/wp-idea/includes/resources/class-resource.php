<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\resources;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\routing\Url_Generator;

class Resource
{
    private const DIGITAL_PRODUCT = 'digital_product';
    private const COURSE = 'course';
    private const SERVICE = 'service';
    private const PHYSICAL_PRODUCT = 'physical_product';
    private const BUNDLE = 'bundle';
    
    private int $id;
    private Resource_Type $type;

    public function __construct(int $resource_id) {
        $this->id = $resource_id;
        $this->type = self::recognize_type($resource_id);
    }
    
    public static function recognize_type(int $resource_id): Resource_Type
    {
        $wpi_resource_type = get_post_meta($resource_id, 'wpi_resource_type', true);
        if(self::SERVICE === $wpi_resource_type) {
            return new Resource_Type(Resource_Type::SERVICE);
        }
        elseif(self::DIGITAL_PRODUCT === $wpi_resource_type ) {
            return new Resource_Type(Resource_Type::DIGITAL_PRODUCT);
        }
        elseif(self::PHYSICAL_PRODUCT === $wpi_resource_type) {
            return new Resource_Type(Resource_Type::PHYSICAL_PRODUCT);
        }
        elseif(self::BUNDLE === get_post_meta($resource_id, '_edd_product_type', true)) {
            return new Resource_Type(Resource_Type::BUNDLE);
        }
        else {
            $course = WPI()->courses->get_course_by_product($resource_id);
            return $course ? new Resource_Type(Resource_Type::COURSE) : null;
        }
    }

    public function generate_editor_url(Interface_Url_Generator $url_generator): string
    {
        
        switch ($this->type->get_name()) {
            case Resource_Type::SERVICE:
                return $url_generator->generate_admin_page_url('admin.php', [
                    'page' => Admin_Menu_Item_Slug::EDITOR_SERVICE,
                    \bpmj\wpidea\admin\pages\service_editor\Service_Editor_Page_Renderer::SERVICE_ID_QUERY_ARG_NAME => $this->id
                ]);
            
            case Resource_Type::DIGITAL_PRODUCT:
                return $url_generator->generate_admin_page_url('admin.php', [
                    'page' => Admin_Menu_Item_Slug::EDITOR_DIGITAL_PRODUCT,
                    \bpmj\wpidea\admin\pages\digital_product_editor\Digital_Product_Editor_Page_Renderer::DIGITAL_PRODUCT_ID_QUERY_ARG_NAME => $this->id
                ]);
                
            case Resource_Type::PHYSICAL_PRODUCT:
                return $url_generator->generate_admin_page_url('admin.php', [
                    'page' => Admin_Menu_Item_Slug::EDITOR_PHYSICAL_PRODUCT,
                    \bpmj\wpidea\admin\pages\physical_product_editor\Physical_Product_Editor_Page_Renderer::PHYSICAL_PRODUCT_ID_QUERY_ARG_NAME => $this->id
                ]);
                
            case Resource_Type::BUNDLE:
                return $url_generator->generate_admin_page_url('admin.php', [
                    'page' => Admin_Menu_Item_Slug::EDITOR_PACKAGES,
                    \bpmj\wpidea\admin\pages\bundle_editor\Bundle_Editor_Page_Renderer::BUNDLE_ID_QUERY_ARG_NAME => $this->id
                ]);
                
            case Resource_Type::COURSE:
                $course = WPI()->courses->get_course_by_product($this->id);
                return $url_generator->generate_admin_page_url('admin.php', [
                    'page' => Admin_Menu_Item_Slug::EDITOR_COURSE,
                    \bpmj\wpidea\admin\pages\course_editor\Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME => $course->ID
                ]);
        }

        return '';
    }

}