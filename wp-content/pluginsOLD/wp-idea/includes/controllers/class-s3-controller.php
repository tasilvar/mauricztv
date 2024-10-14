<?php
namespace bpmj\wpidea\controllers;

use bpmj\wpidea\Caps;
use bpmj\wpidea\assets\Assets_Dir;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\mods\S3_File_Storage_Handler;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\Interface_Redirector;
use http\Exception;

class S3_Controller extends Base_Controller
{
    private S3_File_Storage_Handler $s3_file_storage_handler;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        S3_File_Storage_Handler $s3_file_storage_handler
    ) {
        $this->s3_file_storage_handler = $s3_file_storage_handler;
        parent::__construct($access_control, $translator, $redirector);
    }
    
    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT_SUBSCRIBER,
            'allowed_methods' => [Request_Method::GET],
            'rules' => [
                'certificate_image_action' => [
                    'caps'  => [Caps::CAP_VIEW_SENSITIVE_DATA],
                ]
            ]
        ];
    }

    public function certificate_image_action(Current_Request $current_request): string
    {
        if( !isset($_GET['img']) || $_GET['img'] === '') {
            header("HTTP/1.1 400 Bad Request");
            exit;
        }

        $file_name = $_GET['img'];
        $local_path = dirname(__FILE__) . '/../../../../' . Assets_Dir::EXTERNAL_DIR_NAME . '/cert_images/' . str_replace('/', '_', $file_name);
        $remote_path = $this->s3_file_storage_handler->get_remote_url() . '/' . $file_name;

        if( strpos( $remote_path, 'https://' ) !== 0 && strpos( $remote_path, 'http://' ) !== 0 ) {
            header("HTTP/1.1 400 Bad Request");
            exit;
        }

        if( !file_exists($local_path) ) {
            $file_contents = false;
            try {
                $file_contents = @file_get_contents($remote_path);
            } catch (\Exception $e) {}

            if($file_contents === false || strlen($file_contents) == 0) {
                header("HTTP/1.0 404 Not Found");
                exit;
            }
            
            if( !file_exists(dirname($local_path)) ) {
                mkdir(dirname($local_path), 0700);
            }
            file_put_contents($local_path, $file_contents);
        }

        $finfo = finfo_open(FILEINFO_MIME);
        header('Content-type: ' . finfo_file($finfo, $local_path));
        echo file_get_contents($local_path);
        
        exit;
    }

}
