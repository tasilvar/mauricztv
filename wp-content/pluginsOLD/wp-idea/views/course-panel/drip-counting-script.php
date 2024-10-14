<script>
    jQuery( document ).ready(function( $ ) {
        setInterval(function(){
            var blocks = $( '.lekcja_niedostepna' );
            blocks.each(function( index ) {
                var drip = $(this).data('drip');
                if(drip > 0) {
                    drip--;
                    if(0 === drip) location.reload();

                    $(this).data('drip', drip);

                    var d = parseInt(drip / (24 * 60 * 60));
                    drip -= (d * 24 * 60 * 60);

                    var h = parseInt(drip / (60 * 60));
                    drip -= (h * 60 * 60);

                    var m = parseInt(drip / 60);
                    drip -= (m * 60);

                    $(this).find( '.drip_time_d' ).html(d);
                    $(this).find( '.drip_time_h' ).html(h);
                    $(this).find( '.drip_time_m' ).html(m);
                    $(this).find( '.drip_time_s' ).html(drip);
                }
            });
        },1000);
    });
</script>
