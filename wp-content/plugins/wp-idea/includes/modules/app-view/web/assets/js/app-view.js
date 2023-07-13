handleLoader();

function handleLoader() {
    let loader = document.createElement('div');
    loader.classList.add('lds-ring');
    loader.appendChild(document.createElement('div'));
    loader.appendChild(document.createElement('div'));
    loader.appendChild(document.createElement('div'));

    let loaderOverlay = document.createElement('div')
    loaderOverlay.appendChild(loader);
    loaderOverlay.classList.add('loader-overlay');
    loaderOverlay.style.display = 'none';
    document.body.appendChild(loaderOverlay);
    document.addEventListener(`click`, e => {
        const origin = e.target.closest(`a`) || e.target.closest(`button`);

        if (!origin) {
            return;
        }

        if(!onElementClickALoaderShouldBeShown(origin)) {
            return;
        }

        loaderOverlay.style.display = 'flex';
    });
}

function onElementClickALoaderShouldBeShown(el) {

    let showLoader = false;

    let classesToShowLoaderOnClick = [
        'lekcja_nast_pop',
        'top-bar__link',
        'modul_lekcja_link',
        'glowna_box_zdjecie_link',
        'box_glowna_tytul_link',
        'box_glowna_add_to_cart_link',
        'publigo-search__searchbar__button',
        'publigo-search__results__results__result_link'
    ];

    classesToShowLoaderOnClick.forEach(className => {
        if(el.classList.contains(className)) {
            showLoader = true;
        }
    });

    return showLoader;
}

jQuery(document).ready(function ($) {

    $('.wpi-block-page-content a, .pliki_do_pobrania a').on('click', function (event) {

       let link = $(this).attr("href");

        event.preventDefault();

        if(!isExternalLink(link)){

            let prefixViewerPdf = '';

            if (isFile(link)) {
                if(!isPDFFile(link)){
                    window.showToast(i18n.clipboard.error_format_file, 'info', 7000, true);
                    return;
                }
                prefixViewerPdf = '?' + i18n.pdf.param_name + '=';
            }

            window.location.href = prefixViewerPdf + link;
            return;
        }

        copyUrlToClipboard(link);
   });

    function isExternalLink(link) {
        return (link.indexOf(i18n.clipboard.base_url) ===-1);
    }

    function isFile(link) {
        return (new RegExp(i18n.clipboard.file_types.join('|') + '$', 'gi')).test(link);
    }

    function isPDFFile(link) {
        let ext = link.substring(link.lastIndexOf('.') + 1);
        return (ext === 'pdf');
    }


    function copyUrlToClipboard(link) {
        const el = document.createElement('input');
        el.value = link;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        if(document.execCommand('copy')){
            window.showToast(i18n.clipboard.success, 'info', 2500, true);
        }
        document.body.removeChild(el);
    }

});

