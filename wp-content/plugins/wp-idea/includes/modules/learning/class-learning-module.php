<?php

namespace bpmj\wpidea\modules\learning;

use bpmj\wpidea\modules\learning\notes\api\controllers\Notes_Ajax_Controller;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;

class Learning_Module implements Interface_Module
{
    public function init(): void
    {

    }

    public function get_routes(): array
    {
        return [
            'admin/notes_ajax' => Notes_Ajax_Controller::class
        ];
    }

    public function get_translations(): array
    {
        return [
            'pl_PL' => [
                'notes.actions.get.error' => 'Wystąpił błąd podczas pobierania danych!',
                'notes.actions.add.success' => 'Notatka została dodana!',
                'notes.actions.add.error' => 'Wystąpił błąd podczas dodawania notatki!',
                'notes.actions.save.success' => 'Zmiany zostały zapisane!',
                'notes.actions.save.error' => 'Wystąpił błąd podczas zapisywania!',
                'notes.actions.delete.success' => 'Notatka została pomyślnie usunięta!',
                'notes.actions.delete.error' => 'Wystąpił błąd podczas usuwania notatki!'
            ],
            'en_US' => [
                'notes.actions.get.error' => 'There was an error retrieving the data!',
                'notes.actions.add.success' => 'The note has been added!',
                'notes.actions.add.error' => 'There was an error adding your note!',
                'notes.actions.save.success' => 'Changes have been saved!',
                'notes.actions.save.error' => 'There was an error saving!',
                'notes.actions.delete.success' => 'The note has been successfully deleted!',
                'notes.actions.delete.error' => 'There was an error deleting the note!'
            ]
        ];
    }
}