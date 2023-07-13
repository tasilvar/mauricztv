class OpinionsApi {

    addOpinion(product, rating, content) {
        const data =this.createRequestData();
        const endpoint = 'save';

        data['product'] = product;
        data['rating'] = rating;
        data['content'] = content;

        return this.doRequest(endpoint, data);
    }

    doRequest(endpoint, data) {
        return new Promise((resolve, reject) => {
            jQuery.ajax({
                type: "POST",
                data: data,
                dataType: "json",
                url: wpidea.ajaxurl + '?wpi_route=admin/opinions_ajax/' + endpoint,
                success: function (response) {
                    resolve(response);
                },
                error: function (response) {
                    reject(response.responseJSON.message ?? '');
                }
            });
        });
    }

    createRequestData(noteData) {
        let data;
        data = {
            action: 'wpi_handler',
        };
        data[wpidea.nonce_name] = wpidea.nonce_value

        return data;
    }
}

class OpinionsFormHandler {
    _api;

    static addOpinionFormWrapperSelector = '.add-opinion-wrapper';
    static addOpinionFormSelector = 'form.add-opinion';
    static noProductsToRateSelector = '.no-products-to-rate';
    static productFieldSelector = '[name=product]';

    static productRatingFieldSelector = '[name=product-rating]:checked';

    static opinionContentFieldSelector = '[name=opinion-content]';
    static saveButtonSelector = OpinionsFormHandler.addOpinionFormSelector + ' button[type="submit"]';
    static spinnerTemplateSelector = 'template#spinner-template';

    constructor() {
        this.api = new OpinionsApi();
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

    render() {
        if(!this.opinionsFormExistsOnPage()) {
            return;
        }

        this.bindEvents();
    }

    bindEvents() {
        const addOpinionForm = this.getElement(OpinionsFormHandler.addOpinionFormSelector);

        addOpinionForm.addEventListener('submit', (e) => this.handleSaveOpinionAction(e))
    }

    handleSaveOpinionAction(e) {
        e.preventDefault();

        const product = this.getElement(OpinionsFormHandler.productFieldSelector, OpinionsFormHandler.addOpinionFormSelector).value;
        const rating = this.getElement(OpinionsFormHandler.productRatingFieldSelector, OpinionsFormHandler.addOpinionFormSelector).value;
        const opinionContent = this.getElement(OpinionsFormHandler.opinionContentFieldSelector, OpinionsFormHandler.addOpinionFormSelector).value;

        this.saveLessonNote(product, rating, opinionContent)
    }

    saveLessonNote(product, rating, opinionContent) {
        const formButton = this.getElement(OpinionsFormHandler.saveButtonSelector, OpinionsFormHandler.addOpinionFormSelector);

        this.showLoaderOnElement(formButton);

        this.api.addOpinion(product, rating, opinionContent).then((response) => {
            this.hideElement('.error', OpinionsFormHandler.addOpinionFormWrapperSelector);

            this.setElementHtmlContent(response.message,'.success', OpinionsFormHandler.addOpinionFormWrapperSelector)

            this.showElement('.success', OpinionsFormHandler.addOpinionFormWrapperSelector);

            this.hideLoaderOnElement(formButton);

            this.getElement(OpinionsFormHandler.productRatingFieldSelector, OpinionsFormHandler.addOpinionFormSelector).checked = false;
            this.getElement(OpinionsFormHandler.opinionContentFieldSelector, OpinionsFormHandler.addOpinionFormSelector).value = '';

            if(!response.newProducts || response.newProducts.length === 0) {
                this.hideElement(OpinionsFormHandler.addOpinionFormSelector);
                this.showElement(OpinionsFormHandler.noProductsToRateSelector);
                return;
            }

            //new select options
            const select = this.getElement(OpinionsFormHandler.productFieldSelector, OpinionsFormHandler.addOpinionFormSelector);

            // remove all
            while (select.options.length > 0) {
                select.remove(0);
            }

            let newOption = new Option(response.selectProductMessage ?? '', '');
            select.add(newOption);
            response.newProducts.forEach(product => {
                let newOption = new Option(product.name, product.id);
                select.add(newOption);
            })

        }, (error) => {
            if(error && error.length > 0) {
                this.setElementHtmlContent(error,'.error', OpinionsFormHandler.addOpinionFormWrapperSelector)
            }
            this.showElement('.error', OpinionsFormHandler.addOpinionFormWrapperSelector);
            this.hideLoaderOnElement(formButton)
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
        return this.getElement(OpinionsFormHandler.spinnerTemplateSelector).innerHTML;
    }

    opinionsFormExistsOnPage() {
        return this.getElement(OpinionsFormHandler.addOpinionFormSelector) !== null;
    }
}

jQuery(document).ready(function (){
    (new OpinionsFormHandler()).render();
})