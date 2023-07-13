(function( $ ) {
    $.fn.generatePdfAndClose = function() {
        var $this = $(this),
            element = document.getElementById('generate-pdf-and-close'),
            orientation = $this.find('#pb-page').data('orientation'),
            opt_orientation = (orientation == 'horizontal') ? 'landscape' : 'portrait',
            opt = {
            margin:0,
            filename:     'certificate.pdf',
            image:        { type: 'jpeg', quality: 1 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        {format : 'a4', orientation  : opt_orientation}
        };

        $('.ui-icon-gripsmall-diagonal-se').hide()

        html2pdf(element, opt)
            .set({ html2canvas: { scale: 2, useCORS: true } })
            .then(function(pdf) {
            setTimeout(function () {
                window.close()
            }, 1000);
        });
    };
}( jQuery ));


jQuery( document ).ready(function() {
    if ( jQuery( "#generate-pdf-and-close" ).length > 0) {
        // setTimeout added because generatePdfAndClose will not have time to load
        setTimeout(function(){
            jQuery( "#generate-pdf-and-close" ).generatePdfAndClose();
        }, 750);
    }
});
