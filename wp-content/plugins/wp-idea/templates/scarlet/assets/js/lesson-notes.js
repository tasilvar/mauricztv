class Api {

    getLessonNote(noteData) {
        const data =this.createRequestDataFromNoteData(noteData);
        const endpoint = 'get';

        return this.doRequest(endpoint, data);
    }

    addNote(value, noteData) {
        const data =this.createRequestDataFromNoteData(noteData);
        const endpoint = 'save';

        data['contents'] = value;

        return this.doRequest(endpoint, data);
    }

    deleteLessonNote(noteData) {
        const data =this.createRequestDataFromNoteData(noteData);
        const endpoint = 'delete';

        return this.doRequest(endpoint, data);
    }

    doRequest(endpoint, data) {
        return new Promise((resolve, reject) => {
            jQuery.ajax({
                type: "POST",
                data: data,
                dataType: "json",
                url: wpidea.ajaxurl + '?wpi_route=admin/notes_ajax/' + endpoint,
                success: function (response) {
                    resolve(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    reject();
                }
            });
        });
    }

    createRequestDataFromNoteData(noteData) {
        let data;
        data = {
            action: 'wpi_handler',
            id_lesson: noteData.lessonId ?? '',
            id_module: noteData.moduleId ?? '',
            id: noteData.noteId ?? ''
        };
        data[wpidea.nonce_name] = wpidea.nonce_value

        return data;
    }
}

class NotesRenderer {
    _api;

    static notesTabSelector = '.lesson-notes-tab';
    static editNoteFormWrapperSelector = '.edit-note-form';
    static notesTabLoaderSelector = '.notes-tab-loader';
    static editNoteFormSelector = NotesRenderer.editNoteFormWrapperSelector + ' form';
    static noteFieldSelector = '#lesson_note';
    static notePreviewWrapperSelector = '.note-preview';
    static notePreviewContentSelector = '.note-content';
    static editNoteButtonSelector = '.edit-note-button';
    static deleteNoteButtonSelector = '.delete-note-button';
    static saveButtonSelector = NotesRenderer.editNoteFormWrapperSelector + ' button[type="submit"]';
    static noteEditModeWrapperSelector = '.note-edit-mode';
    static spinnerTemplateSelector = 'template#spinner-template';
    static beforeDeleteMessageTemplateSelector = 'template#before-delete-message-template';

    constructor() {
        this.api = new Api();
    }

    set api(value) {
        this._api = value;
    }

    get api() {
        return this._api;
    }

    showElement(selector, parent = null) {
        this.getElement(selector, parent).style.display = 'block';
    }

    hideElement(selector, parent = null) {
        this.getElement(selector, parent).style.display = 'none';
    }

    getElement(selector, parent = null) {
        return document.querySelector(selector, parent);
    }

    setElementHtmlContent(content, selector, parent) {
        this.getElement(selector, parent).innerHTML = content;
    }

    showNoteInDisplayMode(noteData) {
        this.hideElement(NotesRenderer.editNoteFormWrapperSelector);

        this.setElementHtmlContent(
            noteData.content,
            NotesRenderer.notePreviewContentSelector,
            NotesRenderer.notePreviewWrapperSelector
        );

        this.showElement(NotesRenderer.notePreviewWrapperSelector);
    }

    showNoteInEditMode(note = null) {
        const noteField = this.getElement(
            NotesRenderer.noteFieldSelector,
            NotesRenderer.noteEditModeWrapperSelector
        );

        this.hideElement(NotesRenderer.notePreviewWrapperSelector);

        noteField.value = note ? note.note_content : null;

        this.showElement(NotesRenderer.editNoteFormWrapperSelector);
    }

    render() {
        if(!this.notesTabExistsOnPage()) {
            return;
        }

        this.bindEvents();

        this.getNoteFromApi().then(data => {
            const noteData = {
                content: data.note_content ?? null,
                id: data.note_id ?? null
            };

            if(noteData.content === null) {
                this.showNoteInEditMode();
                this.hideElement(NotesRenderer.notesTabLoaderSelector)
                return;
            }

            this.showNoteInDisplayMode(noteData)
            this.setCurrentNoteIdInHtmlData(noteData.id)
            this.hideElement(NotesRenderer.notesTabLoaderSelector)
        })
    }

    bindEvents() {
        const editNoteForm = this.getElement(NotesRenderer.editNoteFormSelector);
        const editNoteButton = this.getElement(NotesRenderer.editNoteButtonSelector);
        const deleteNoteButton = this.getElement(NotesRenderer.deleteNoteButtonSelector);

        editNoteForm.addEventListener('submit', (e) => this.handleSaveNoteAction(e))
        editNoteButton.addEventListener('click', (e) => this.handleEditNoteAction(e))
        deleteNoteButton.addEventListener('click', (e) => this.handleDeleteNoteAction(e))
    }

    handleSaveNoteAction(e) {
        e.preventDefault();

        const noteField = this.getElement(NotesRenderer.noteFieldSelector, NotesRenderer.editNoteFormSelector);
        const value = noteField.value;

        this.saveLessonNote(value)
    }

    saveLessonNote(value) {
        const formButton = this.getElement(NotesRenderer.saveButtonSelector, NotesRenderer.editNoteFormSelector);

        this.showLoaderOnElement(formButton);

        this.api.addNote(value, this.getCurrentLessonAndNoteData()).then(() => {
            this.getNoteFromApi().then(data => {
                const noteData = {
                    content: data.note_content ?? null,
                    id: data.note_id ?? null
                };

                this.showNoteInDisplayMode(noteData)
                this.setCurrentNoteIdInHtmlData(noteData.id)
                this.hideLoaderOnElement(formButton);
            });

        })
    }

    handleEditNoteAction(e) {
        const editNoteButton = this.getElement(NotesRenderer.editNoteButtonSelector);

        this.showLoaderOnElement(editNoteButton);

        this.getNoteFromApi().then(note => {
            this.showNoteInEditMode(note);
            this.hideLoaderOnElement(editNoteButton);
        })
    }

    getNoteFromApi() {
        return this.api.getLessonNote(
            this.getCurrentLessonAndNoteData()
        );
    }

    handleDeleteNoteAction(e) {
        const beforeDeleteMessage = this.getElement(NotesRenderer.beforeDeleteMessageTemplateSelector).innerHTML;
        const deleteNoteButton = this.getElement(NotesRenderer.deleteNoteButtonSelector);

        if (!window.confirm(beforeDeleteMessage)) {
            return;
        }

        this.showLoaderOnElement(deleteNoteButton);

        this.api.deleteLessonNote(this.getCurrentLessonAndNoteData()).then(() => {
            this.setCurrentNoteIdInHtmlData('')
            this.showNoteInEditMode();
            this.hideLoaderOnElement(deleteNoteButton);
        })
    }

    showLoaderOnElement(element) {
        element.dataset.originalContent = element.innerHTML;

        element.innerHTML = this.getSpinnerHtml();
    }

    hideLoaderOnElement(element) {
        element.innerHTML = element.dataset.originalContent;
    }

    getSpinnerHtml() {
        return this.getElement(NotesRenderer.spinnerTemplateSelector).innerHTML;
    }

    getCurrentLessonAndNoteData() {
        const formField = this.getElement(NotesRenderer.noteFieldSelector);
        const lessonId = formField.dataset.lessonId;
        const moduleId = formField.dataset.moduleId;
        const noteId = formField.dataset.noteId;

        return {
            lessonId,
            moduleId,
            noteId
        };
    }

    setCurrentNoteIdInHtmlData(id) {
        const formField = this.getElement(NotesRenderer.noteFieldSelector);

        formField.dataset.noteId = id;
    }

    notesTabExistsOnPage() {
        return this.getElement(NotesRenderer.notesTabSelector) !== null;
    }
}

jQuery(document).ready(function (){
    (new NotesRenderer()).render();
})