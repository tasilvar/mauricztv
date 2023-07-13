<?php

namespace bpmj\wpidea\modules\videos;

use bpmj\wpidea\infrastructure\io\video\Interface_Video_Space_Checker;
use bpmj\wpidea\modules\videos\api\controllers\Video_Controller;
use bpmj\wpidea\modules\videos\core\providers\Interface_Video_Config_Provider;
use bpmj\wpidea\modules\videos\core\services\Bunny_Net_Video_List_Sync_Service;
use bpmj\wpidea\modules\videos\core\services\Media_Video_Format_Blocker;
use bpmj\wpidea\modules\videos\infrastructure\space_checker\Video_Space_Checker;
use bpmj\wpidea\modules\videos\web\templates_admin\{Old_Videos_Block, Videos_Block};
use bpmj\wpidea\modules\videos\web\video_changes_info\Video_Changes_Info;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;

class Videos_Module implements Interface_Module
{
    public const MESSAGE_NOT_ALLOWED_FORMAT = 'media.video_format_blocker.error';

    private Videos_Block $videos_block;
    private Old_Videos_Block $old_videos_block;
    private Bunny_Net_Video_List_Sync_Service $sync_service;
    private Interface_Video_Config_Provider $video_config_provider;
    private Media_Video_Format_Blocker $media_video_format_blocker;
    private Video_Changes_Info $video_changes_info;
    private Video_Space_Checker $video_space_checker;

    public function __construct(
        Videos_Block $videos_block,
        Old_Videos_Block $old_videos_block,
        Bunny_Net_Video_List_Sync_Service $sync_service,
        Interface_Video_Config_Provider $video_config_provider,
        Media_Video_Format_Blocker $media_video_format_blocker,
        Video_Changes_Info $video_changes_info,
        Video_Space_Checker $video_space_checker
    ) {
        $this->videos_block = $videos_block;
        $this->old_videos_block = $old_videos_block;
        $this->sync_service = $sync_service;
        $this->video_config_provider = $video_config_provider;
        $this->media_video_format_blocker = $media_video_format_blocker;
        $this->video_changes_info = $video_changes_info;
        $this->video_space_checker = $video_space_checker;
    }

    public function init(): void
    {
        if ($this->is_enabled()) {
            $this->videos_block->init();
            $this->old_videos_block->init();
            $this->sync_service->init();
            $this->media_video_format_blocker->init();
            $this->video_changes_info->init();
        }
    }

    public function is_enabled(): bool
    {
        return $this->video_config_provider->is_set();
    }

    public function get_video_space_checker(): Interface_Video_Space_Checker
    {
        return $this->video_space_checker;
    }

    public function get_routes(): array
    {
        return [
            'video' => Video_Controller::class,
        ];
    }

    public function get_translations(): array
    {
        return [
            'pl_PL' => [
                'edit_post.block.video.title' => 'Wideo',
                'edit_post.block.video.hint' => 'Poniżej możesz wybrać plik wideo spośród tych, które są widoczne w tabeli na podstronie: Media -> Wideo.',
                'edit_post.block.video.select_hint' => 'Wybierz...',
                'videos.actions.delete.error' => 'Wystąpił błąd podczas usuwania pliku wideo!',
                'videos.actions.delete.success' => 'Plik wideo został pomyślnie usunięty!',
                'videos.actions.delete.bulk.success' => 'Wybrane plik wideo zostały pomyślnie usunięte!',
                'video_uploader.drop_files_here_or_click' => 'Upuść pliki wideo tutaj lub kliknij, aby przesłać je na serwer.',
                'video_uploader.invalid_file_type' => 'Nieprawidłowy typ pliku. Dozwolone są tylko pliki .mp4, .wmv, .avi oraz .mov.',
                'video_uploader.storage_not_enough_space' => 'Z powodu przekroczenia dostępnego limitu, Twój obecny pakiet Publigo nie jest w stanie obsłużyć kolejnych plików wideo. Skontaktuj się z supportem Publigo, aby omówić możliwe rozwiązania.',
                'video_uploader.storage_not_enough_space.popup.title' => 'Plik nie został przesłany',
                'video_uploader.file_too_big' => 'Plik jest zbyt duży ({{filesize}}MiB). Maksymalny dozwolony rozmiar pliku to {{maxFilesize}}MiB.',
                'video_uploader.upload_still_in_progress_text' => 'Przesyłanie plików jest w trakcie. Jeśli opuścisz teraz stronę, nie zostanie ono poprawnie ukończone.',
                'video_uploader.go_back' => 'Wróć',
                'video_uploader.page_title' => 'Prześlij pliki wideo',
                'video_uploader.response_error' => 'Serwer odpowiedział kodem {{statusCode}}',
                'media.video_format_blocker.understand' => 'Rozumiem',
                'video_settings.page_title' => 'Edytuj ustawienia',
                'video_settings.form.return' => 'Wróć',
                'video_settings.form.choose_picture' => 'Wybierz obrazek',
                'video_settings.form.change_cover' => 'Zmień okładkę',
                'video_settings.form.remove_cover' => 'Usuń okładkę',
                'video_settings.form.video_cover' => 'Okładka wideo',
                'video_settings.form.no_cover' => 'Aktualnie nie została ustawiona żadna okładka dla tego wideo.',
                'video_settings.form.title' => 'Nazwa pliku',
                'video_settings.form.save' => 'Zapisz',
                'video_settings.form.while_saving' => 'Zapisuję...',
                'videos.actions.settings.success' => 'Zmiany zostały zapisane!',
                'video.course_page.video_is_processing' => 'To wideo jest aktualnie przetwarzane i będzie dostępne po zakończeniu tego procesu.',
            ],
            'en_US' => [
                'edit_post.block.video.title' => 'Video',
                'edit_post.block.video.hint' => 'Below you can select a video file from those shown in the table on the subpage: Media -> Videos.',
                'edit_post.block.video.select_hint' => 'Select...',
                'videos.actions.delete.error' => 'An error occurred while deleting the video file!',
                'videos.actions.delete.success' => 'The video file has been successfully deleted!',
                'videos.actions.delete.bulk.success' => 'The selected video file has been successfully deleted!',
                'video_uploader.drop_files_here_or_click' => 'Drop video files here or click to upload them to the server.',
                'video_uploader.invalid_file_type' => 'Invalid file type. Valid file types are: .mp4, .wmv, .avi, .mov.',
                'video_uploader.storage_not_enough_space' => 'Due to the limit being exceeded, your current Publigo package is not able to handle more files of the transferred type. Please contact Publigo Technical Support to present possible solutions.',
                'video_uploader.storage_not_enough_space.popup.title' => 'The file was not transferred',
                'video_uploader.file_too_big' => 'Your file is too big ({{filesize}}MiB). Max allowed file size is {{maxFilesize}}MiB.',
                'video_uploader.upload_still_in_progress_text' => 'Uploading files is in progress. If you leave the website now, it will not be completed correctly.',
                'video_uploader.go_back' => 'Go back',
                'video_uploader.page_title' => 'Upload video files',
                'video_uploader.response_error' => 'Server responded with {{statusCode}} code',
                'media.video_format_blocker.understand' => 'I understand',
                'video_settings.page_title' => 'Edit the settings',
                'video_settings.form.return' => 'Come back',
                'video_settings.form.choose_picture' => 'Choose a picture',
                'video_settings.form.change_cover' => 'Change cover',
                'video_settings.form.remove_cover' => 'Remove cover',
                'video_settings.form.video_cover' => 'Video cover',
                'video_settings.form.no_cover' => 'Currently no cover art has been set for this video.',
                'video_settings.form.title' => 'File name',
                'video_settings.form.save' => 'Save',
                'video_settings.form.while_saving' => 'I save...',
                'videos.actions.settings.success' => 'Changes have been saved!',
                'video.course_page.video_is_processing' => 'This video is currently being processed and will be available when this process is completed.',
            ]
        ];
    }
}