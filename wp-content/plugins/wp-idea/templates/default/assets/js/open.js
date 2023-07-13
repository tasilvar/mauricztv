$(document).ready(function(){
        $('.agreement-wrapper .agreement').addClass('closed');
        $('.agreement-wrapper .agreement .content').hide();
        $('.agreement-wrapper .agreement:first-child').removeClass('open');
        $('.agreement-wrapper .agreement:first-child').addClass('closed');
        $('.agreement-wrapper .agreement:first-child .content').hide();
        $('.agreement-wrapper .agreement .show').click(function(){
            if($(this).parents('.agreement').hasClass('open')){
                $('.agreement-wrapper .agreement').removeClass('open');
                $('.agreement-wrapper .agreement').addClass('closed');
                $('.agreement-wrapper .agreement .content').slideUp('slow');    
            }else{
            $('.agreement-wrapper .agreement').addClass('closed');
            $('.agreement-wrapper .agreement').removeClass('open');
            $('.agreement-wrapper .agreement .content').slideUp('slow');
            $(this).parents('.agreement').removeClass('closed');
            $(this).parents('.agreement').addClass('open');
            $(this).parents('.agreement').find('.content').slideDown('normal');
            }
        });
    })  

$(document).ready(function(){
        $('.code-wrapper .code').addClass('closed');
        $('.code-wrapper .code .content').hide();
        $('.code-wrapper .code:first-child').removeClass('open');
        $('.code-wrapper .code:first-child').addClass('closed');
        $('.code-wrapper .code:first-child .content').hide();
        $('.code-wrapper .code .show').click(function(){
            if($(this).parents('.code').hasClass('open')){
                $('.code-wrapper .code').removeClass('open');
                $('.code-wrapper .code').addClass('closed');
                $('.code-wrapper .code .content').slideUp('slow');    
            }else{
            $('.code-wrapper .code').addClass('closed');
            $('.code-wrapper .code').removeClass('open');
            $('.code-wrapper .code .content').slideUp('slow');
            $(this).parents('.code').removeClass('closed');
            $(this).parents('.code').addClass('open');
            $(this).parents('.code').find('.content').slideDown('normal');
            }
        });
    })  
