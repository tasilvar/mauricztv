<?php
namespace bpmj\wpidea\admin\menu;

use bpmj\wpidea\admin\pages\customers\Customers;

class Admin_Menu_Item_Slug
{
    public const DASHBOARD = 'index.php';
    public const COURSES = 'wp-idea-courses';
    public const EDITOR_COURSE = 'publigo-editor-course';
    public const EDITOR_QUIZ = 'publigo-editor-quiz';
    public const CREATE_COURSE = 'wp-idea-creator';
    public const EDITOR_PACKAGES = 'publigo-editor-packages';
    public const PACKAGES = 'publigo-packages';
    public const EDITOR_DIGITAL_PRODUCT = 'publigo-editor-digital-products';
    public const DIGITAL_PRODUCTS = 'publigo-digital-products';
    public const SERVICES = 'publigo-services';
    public const EDITOR_SERVICE = 'publigo-editor-service';
    public const CATEGORIES = 'edit-tags.php?taxonomy=download_category&amp;post_type=download';
    public const TAGS = 'edit-tags.php?taxonomy=download_tag&amp;post_type=download';
    public const INCREASING_SALES = 'publigo-increasing-sales';
    public const PAYMENTS_HISTORY = 'wp-idea-payment-history';
    public const DISCOUNT_CODES = 'wp-idea-discounts';
    public const CLIENTS = Customers::PAGE;
    public const USERS = 'wp-idea-users';
    public const USERS_PROXY = 'wp-idea-users-proxy';
    public const USER_NEW = 'user-new.php';
    public const USER_PROFILE = 'profile.php';
    public const QUIZZES = 'wp-idea-tests';
    public const CERTIFICATES = 'wp-idea-certificates';
    public const HELP = 'wp-idea-support';
    public const MEDIA = 'upload.php';
    public const MEDIA_ADD_NEW = 'media-new.php';
    public const VIDEOS = 'wp-idea-videos';
    public const VIDEO_UPLOADER = 'publigo-video-uploader';
    public const VIDEO_SETTINGS = 'publigo-video-settings';
    public const TEMPLATES = 'edit.php?post_type=wpi_page_templates';
    public const TEMPLATE_GROUPS = 'wp-idea-templates';
    public const TEMPLATES_LIST = 'wp-idea-templates-list';
    public const PAGES = 'edit.php?post_type=page';
    public const PAGES_ADD_NEW = 'post-new.php?post_type=page';
    public const POSTS = 'edit.php';
    public const POSTS_ADD_NEW = 'post-new.php';
    public const POSTS_CATEGORIES = 'edit-tags.php?taxonomy=category';
    public const POSTS_TAGS = 'edit-tags.php?taxonomy=post_tag';
    public const COMMENTS = 'edit-comments.php';
    public const SETTINGS = 'wp-idea-settings';
    public const TOOLS = 'wp-idea-tools';
    public const CUSTOMIZE = 'customize.php';
    public const MENU = 'nav-menus.php';
    public const LOGS = 'wp-idea-logs';
    public const WEBHOOKS = 'wp-idea-webhooks';
    public const STUDENTS = 'wp-idea-students';
    public const SWITCH_TO_WP_ADMIN = 'switch-to-wp-admin';
    public const SWITCH_TO_LMS_ADMIN = 'switch-to-lms-admin';
    public const WP_IDEA = 'wp-idea';
    public const AFFILIATE_PROGRAM = 'wp-idea-affiliate-program';
    public const AFFILIATE_PROGRAM_REDIRECTIONS = 'wp-idea-affiliate-program-redirections';
    public const AFFILIATE_PROGRAM_PARTNERS = 'wp-idea-affiliate-program-partners';
    public const PURCHASE_REDIRECTIONS = 'wp-idea-purchase-redirections';
    public const NOTIFICATIONS = 'publigo-notifications';
    public const EXPIRING_CUSTOMERS = 'wp-idea-expiring-customers';
    public const PRICE_HISTORY = 'publigo-price-history';
    public const PHYSICAL_PRODUCTS = 'publigo-physical-products';
    public const EDITOR_PHYSICAL_PRODUCT = 'publigo-editor-physical-product';
    public const OPINIONS = 'publigo-opinions';
    public const REPORTS = 'wp-idea-reports';
}