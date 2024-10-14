(function( $ ) {


    const TYPE_TEXT_INPUT = 'text_input';
    const TYPE_IMAGE = 'image';

    const HALF_WIDTH_ORIENTATION_HORIZONTAL = 397;
    const HALF_WIDTH_ORIENTATION_VERTICAL = 561;

    const SELECTOR_NAME_COMPONENT_BOX = 'component-box'
    const SELECTOR_COMPONENT_BOX = '.' + SELECTOR_NAME_COMPONENT_BOX

    const SELECTOR_NAME_PAGE = 'pb-page'
    const SELECTOR_PAGE = '#' + SELECTOR_NAME_PAGE

    const SELECTOR_HELPER_X = '.helperx';
    const SELECTOR_HELPER_Y = '.helpery';

    const ORIENTATION_VERTICAL = 'vertical';
    const ORIENTATION_HORIZONTAL = 'horizontal';

    $.fn.setStyles = function(styles) {
        var $this = this;
        $.each(styles, function( name, value ) {
            $this.css(name, value);
        });
    }

    $.fn.setDraggable = function(isReadonly) {
        var x, y,
            draggableElement = this

        this.draggable({
            snap: true,
            handle:'.draggable-icon',
            tolerance:5,
            cancel: (isReadonly || typeof(isReadonly) == 'undefined') ? "" : ".component-box",
            containment: SELECTOR_PAGE,
            start: function(event) {
                x = event.originalEvent.pageX;
                y = event.originalEvent.pageY;
            },
            drag: function(event, ui) {
                if (event.shiftKey) {
                    if (x && y) {
                        axis = Math.abs(event.originalEvent.pageX - x) > Math.abs(event.originalEvent.pageY - y) ? 'x' : 'y';
                        draggableElement.draggable('option', 'axis', axis);
                        x = y = null;
                    }
                }
                var d = 1;

                var l = parseInt(draggableElement.css('left')), r = l + parseInt(draggableElement.css('width')),
                    t = parseInt(draggableElement.css('top')), b = t + parseInt(draggableElement.css('height')),
                    hc = (l + r) / 2, vc = (t + b) / 2;

                $(SELECTOR_HELPER_X).css({"display":"none"});
                $(SELECTOR_HELPER_Y).css({"display":"none"});


                $(".ui-draggable").each(function (idx, other) {
                    if( draggableElement.find('.component-box').attr('id') != $(this).find('.component-box').attr('id')){

                        var x1 = parseInt($(this).css('left')), x2 = x1 + parseInt($(this).css('width')),
                            y1 = parseInt($(this).css('top')), y2 = y1 + parseInt($(this).css('height')),
                            xc = (x1 + x2) / 2, yc = (y1 + y2) / 2;

                        var ls = Math.abs(l - x2) <= d;
                        var rs = Math.abs(r - x1) <= d;
                        var ts = Math.abs(t - y2) <= d;
                        var bs = Math.abs(b - y1) <= d;
                        var hs = Math.abs(hc - xc) <= d;
                        var vs = Math.abs(vc - yc) <= d;
                        if(ls) {
                            $(SELECTOR_HELPER_X).css({"left":l-d,"display":"block"});
                        }
                        if(rs) {
                            $(SELECTOR_HELPER_X).css({"left":r-d,"display":"block"});
                        }
                        if(ts) {
                            $(SELECTOR_HELPER_Y).css({"top":t-d,"display":"block"});
                        }
                        if(bs) {
                            $(SELECTOR_HELPER_Y).css({"top":b-d,"display":"block"});
                        }
                        if(hs) {
                            $(SELECTOR_HELPER_X).css({"left":hc-d,"display":"block"});
                        }
                        if(vs) {
                            $(SELECTOR_HELPER_Y).css({"top":vc-d,"display":"block"});
                        }
                    }

                });
            },
            stop: function() {
                x = y = null;
                draggableElement.draggable('option', 'axis', false);
                console.log('de', draggableElement.find(SELECTOR_COMPONENT_BOX).data('disable-editor'));
                if(!draggableElement.find(SELECTOR_COMPONENT_BOX).data('disable-editor')){
                    draggableElement.css('height', 'auto')

                }
                $(SELECTOR_HELPER_X).css({"display":"none"});
                $(SELECTOR_HELPER_Y).css({"display":"none"});
            },
            distance: 20
        });
    }

    $.fn.setResizable = function(options, onlyWidthResizable = true) {
        this.resizable({
            handles: (onlyWidthResizable) ? 'e, w' : 's, n, e, w, nw, ne, sw, se',
            maxWidth: options.orientation_style.width,
            maxHeight: options.orientation_style.height,
            containment: SELECTOR_PAGE
        });
    }

    $.fn.appendElement = function($element) {
        this.append($element.html())
    };

    $.fn.setTextEditor = function(readonly, options, isNew = false) {
        var $this = this,
            selector = $this.find(SELECTOR_COMPONENT_BOX).attr("id"),
            toolbarButtons = [
                {
                    type: 'resize100',
                    text: '100%',
                    icon: 'resize',
                    tooltip: pdf_builder_text.stretch_100,
                    onAction: function () {
                        $this.css('width', '100%')
                        $this.css('left', '0')
                        $this.css('right', 'auto')
                    }
                },
                {
                    type: 'resize50',
                    icon:'resize',
                    text: '50%',
                    tooltip:pdf_builder_text.stretch_50,
                    onAction: function () {
                        $this.css('width', '50%')
                        $this.css('left', '0')
                        $this.css('right', 'auto')
                    }
                },
                {
                    type: 'toleft',
                    text: pdf_builder_text.to_left,
                    tooltip: pdf_builder_text.move_to_left,
                    onAction: function () {
                        $this.css('left', '0')
                        $this.css('right', 'auto')
                    }
                },
                {
                    type: 'toright',
                    text: pdf_builder_text.to_right,
                    tooltip:pdf_builder_text.move_to_right,
                    onAction: function () {
                        $this.css('left', 'auto')
                        $this.css('right', '0')
                    }
                },
                {
                    type: 'tocenter',
                    text: pdf_builder_text.center,
                    tooltip:pdf_builder_text.center_element,
                    onAction: function () {
                        var halfWidth = 0;

                        if(options.orientation == ORIENTATION_VERTICAL){
                            halfWidth = HALF_WIDTH_ORIENTATION_HORIZONTAL
                        } else {
                            halfWidth = HALF_WIDTH_ORIENTATION_VERTICAL

                        }
                        var left = halfWidth - ($this.width() / 2);

                        $this.css('left', left+'px')
                        $this.css('right', 'auto')
                        $this.css('margin-left', 'auto')
                        $this.css('margin-right', 'auto')
                        $this.css('text-align', 'center')
                    }
                },
                {
                    type: 'delete',
                    text: pdf_builder_text.delete,
                    tooltip: pdf_builder_text.delete,
                    onAction: function () {
                        $this.remove()
                    }
                }
            ];

        var textTypes = '';
        $.each(toolbarButtons, function( index, value ) {
            textTypes = textTypes + ' ' + value.type;
        });


        tinymce.init({
            selector: '#' + selector,
            inline: true,
            menubar:false,
            toolbar: 'backcolor forecolor fontsizeselect fontselect  bold italic | alignleft aligncenter alignright alignjustify | ' + textTypes ,
            fontsize_formats:
                "8pt 9pt 10pt 11pt 12pt 14pt 18pt 20pt 24pt 26pt 28pt 30pt 34pt 36pt 40pt 45pt 50pt 55pt 60pt 65pt 70pt 80pt 90pt",
            setup: function(editor) {
                const mutationObserver = new MutationObserver(changeColorPickerValue)
                mutationObserver.observe(document, { attributes: true, characterData: true, childList: true, subtree: true })


                $.each(toolbarButtons, function( index, value ) {
                    editor.ui.registry.addButton(value.type, value);
                });

                if(readonly){

                    editor.on('click', function(e) {
                        editor.focus()
                        editor.selection.select(editor.getBody(), true);
                    });
                    editor.on('keydown', function(e) {
                        e.preventDefault()
                    });
                } else {

                    editor.on('click', function(e) {
                        editor.focus()
                    });
                }

                editor.on('focus', function(e) {
                    $(editor.getElement()).parents('.ui-draggable').addClass('selected')
                });
                editor.on('blur', function(e) {
                    $(editor.getElement()).parents('.ui-draggable').removeClass('selected')
                });


                if(isNew){
                    editor.on('init', function (e) {
                        $this.find('p').css('font-size', '24pt')
                        $this.find('p').css('margin', '0')
                        $this.find('p').css('line-height', '1.5')
                    });
                }
            }
        });
    }

    $.fn.pageBuilder = function(callbackOnSave, callbackOnExit) {

        const STYLES = {
            'HORIZONTAL_PAGE':{
                'width': '1122px',
                'height': '794px',
                'position': 'relative',
                'overflow': 'hidden',
                'border':'none',
                'margin': '0',
                'display': 'inline-block',
            },
            'VERTICAL_PAGE':{
                'width': '794px',
                'height': '1122px',
                'position': 'relative',
                'overflow': 'hidden',
                'border':'none',
                'margin': '0',
                'display': 'inline-block',
            },
            'BLUE_BUTTON':{
                'background': '#007cba',
                'border-color': '#007cba',
                'color': '#fff',
                'text-decoration': 'none',
                'text-shadow': 'none',
                'width':'100%'
            },
            'GREEN_BUTTON':{
                'background': '#13ba00',
                'border-color': '#12a701',
                'color': '#fff',
                'text-decoration': 'none',
                'text-shadow': 'none',
                'width':'100%'
            },
            'BUTTON_SECONDARY': {
                'color': '#0071a1',
                'border-color': '#0071a1',
                'background': '#f3f5f6',
                'vertical-align': 'top',
                'border-width': '1px',
                'border-style': 'solid',
                'border-radius': '3px',
                'width':'100%'
            }
        };

        //elements
        var elements = {
                $this: $(this),
                $container:null,
                $page:null,
                $toolBox:null,
                $leftColumn:null,
                $middleColumn:null,
                $leftToolBox:null,
                $topToolBox:null,
                $rightToolBox:null,
                $topBar: null,
                $topBarToolBox: null,
                $boxComponentsButtons:null,
                $boxReadonlyComponentsButtons:null,
                $boxTemplates:null,
                $popupTemplates:null,
                $popupContainerTemplates:null,
                $popupContainerTemplatesBlocker:null,
                $boxCollapse:null,
                $boxCollapseElement:null
            },
            templates = {
                'basic': {
                    name:pdf_builder_text.classic,
                    html:'<div id="pb-page" data-orientation="horizontal" style="width: 1122px;display: inline-block; margin: 0; height: 794px; position: relative; overflow: hidden; border: none; background: url('+pdf_builder_text.home_url+'/wp-content/plugins/wp-idea/assets/imgs/cert01.jpeg) 0% 0% / contain;"><div class="helperx" style="display: none; left: 554.5px;"></div><div class="helpery" style="display: none; top: 460px;"></div><div data-readonly="true" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 809px; top: 413.212px; left: 150.979px; right: auto; bottom: auto;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="pY5HYCuQCx" data-name="course_name" data-readonly="true" title="Variable (the content will be dynamically replaced when generating the certificate)" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px;"><span style="font-family: arial, helvetica, sans-serif;" data-mce-style="font-family: arial, helvetica, sans-serif;">Przykładowy tytuł kursu</span></p></div><input type="hidden" name="pY5HYCuQCx"><input type="hidden" name="pY5HYCuQCx"><div class="component-elements-bg"  style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div data-readonly="true" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 814px; top: 333.215px; left: 149.99px; right: auto; bottom: auto;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="NYvZMMPJOv" data-name="student_name" data-readonly="true" title="Variable (the content will be dynamically replaced when generating the certificate)" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px;"><span style="font-family: arial, helvetica, sans-serif;" data-mce-style="font-family: arial, helvetica, sans-serif;">Jan Nowak</span></p></div><input type="hidden" name="NYvZMMPJOv"><input type="hidden" name="NYvZMMPJOv"><div class="component-elements-bg"  style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div data-readonly="true" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 200px; top: 576.222px; left: 756.535px; right: auto; bottom: auto;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="gfbPPwmLEx" data-name="certificate_date" data-readonly="true" title="Variable (the content will be dynamically replaced when generating the certificate)" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;"><span style="font-family: arial, helvetica, sans-serif;" data-mce-style="font-family: arial, helvetica, sans-serif;">23.09.2020</span></p></div><input type="hidden" name="gfbPPwmLEx"><input type="hidden" name="gfbPPwmLEx"><div class="component-elements-bg"  style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div style="width: 232px; height: 83px; position: absolute; min-height: 30px; min-width: 50px; right: auto; bottom: auto; left: 144px; top: 547.979px;" class="ui-draggable ui-resizable"><div class="component-box" data-disable-editor="true" id="wCCbFdKT3b" style="width:100%;height:100%;background:url('+pdf_builder_text.home_url+'/wp-content/plugins/wp-idea/assets/imgs/wp-idea-logo.png);background-size: contain;background-repeat: no-repeat"></div><div class="component-elements-bg" style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div></div>',
                    image:pdf_builder_text.home_url+'/wp-content/plugins/wp-idea/assets/imgs/classic.png',
                    element: '$popupContainerTemplates'
                },
                'basic2': {
                    name:pdf_builder_text.golden,
                    html: '<div id="pb-page" data-orientation="horizontal" style="width: 1122px;display: inline-block; margin: 0; height: 794px; position: relative; overflow: hidden; border: none; background: url('+pdf_builder_text.home_url+'/wp-content/plugins/wp-idea/assets/imgs/cert02.png) 0% 0% / contain;"><div class="helperx" style="display: none; left: 559px;"></div><div class="helpery" style="display: none; top: 678px;"></div><div data-readonly="false" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 1118px; top: 232.212px; left: 1.97919px; right: auto; margin-left: auto; margin-right: auto; text-align: center; bottom: auto;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="WT4Gn47sAu" data-name="text_input" data-readonly="false" title="" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;"><span style="font-size: 30pt;" data-mce-style="font-size: 30pt;"><strong><span style="font-family: tahoma, arial, helvetica, sans-serif;" data-mce-style="font-family: tahoma, arial, helvetica, sans-serif;">Certyfikat ukończenia</span></strong></span></p></div><input type="hidden" name="WT4Gn47sAu"><input type="hidden" name="WT4Gn47sAu"><div class="component-elements-bg"  style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div data-readonly="true" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 1108px; top: 327.205px; left: 6.97919px; right: auto; margin-left: auto; margin-right: auto; text-align: center; bottom: auto;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="7KQ6a3NXzI" data-name="course_name" data-readonly="true" title="Variable (the content will be dynamically replaced when generating the certificate)" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px;"><span style="font-size: 40pt;" data-mce-style="font-size: 40pt;"><strong><span style="font-family: arial, helvetica, sans-serif; color: rgb(238, 214, 165);" data-mce-style="font-family: arial, helvetica, sans-serif; color: #eed6a5;"><em>Przykładowy tytuł kursu</em></span></strong></span></p></div><input type="hidden" name="7KQ6a3NXzI"><input type="hidden" name="7KQ6a3NXzI"><div class="component-elements-bg"  style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div data-readonly="false" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 1100px; top: 426.222px; left: 10.9896px; right: auto; margin-left: auto; margin-right: auto; text-align: center; bottom: auto;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="s6tELNYP3Z" data-name="text_input" data-readonly="false" title="" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;"><span style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 30pt;" data-mce-style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 30pt;">dla</span></p></div><input type="hidden" name="s6tELNYP3Z"><input type="hidden" name="s6tELNYP3Z"><div class="component-elements-bg"  style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div data-readonly="true" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 1109px; top: 507.212px; left: 7.48267px; right: auto; margin-left: auto; margin-right: auto; text-align: center; bottom: auto;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="Et5VW5bnAE" data-name="student_name" data-readonly="true" title="Variable (the content will be dynamically replaced when generating the certificate)" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;"><span style="color: rgb(238, 214, 165); font-family: arial black, sans-serif; font-size: 34pt;" data-mce-style="color: #eed6a5; font-family: \'arial black\', sans-serif; font-size: 34pt;">Jan Nowak</span></p></div><input type="hidden" name="Et5VW5bnAE"><input type="hidden" name="Et5VW5bnAE"><div class="component-elements-bg"  style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div style="width: 269px; height: 100px; position: absolute; min-height: 30px; min-width: 50px; right: auto; bottom: auto; left: 786px; top: 630.986px;" class="ui-draggable ui-resizable"><div class="component-box" data-disable-editor="true" id="9L5Z90w70u" style="width:100%;height:100%;background:url('+pdf_builder_text.home_url+'/wp-content/plugins/wp-idea/assets/imgs/wp-idea-logo.png);background-size: contain;background-repeat: no-repeat"></div><div class="component-elements-bg" style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div data-readonly="true" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 200px; right: auto; bottom: auto; left: 86.9965px; top: 655.976px;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="zsmBzdcc7V" data-name="certificate_date" data-readonly="true" title="Variable (the content will be dynamically replaced when generating the certificate)" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;"><em><span style="font-family: tahoma, arial, helvetica, sans-serif;" data-mce-style="font-family: tahoma, arial, helvetica, sans-serif;">23.09.2020</span></em></p></div><input type="hidden" name="zsmBzdcc7V"><input type="hidden" name="zsmBzdcc7V"><div class="component-elements-bg"  style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div></div>',
                    image:pdf_builder_text.home_url+'/wp-content/plugins/wp-idea/assets/imgs/gold.png',
                    element: '$popupContainerTemplates'
                }
            },
            options = {
                scale: 1.0,
                orientation: ORIENTATION_VERTICAL,
                orientation_style: STYLES.VERTICAL_PAGE,
                topToolBoxText: pdf_builder_text.cert_preview,
                infoComponentsText: pdf_builder_text.tips,
                infoComponentsDescription: '<span class="pb-circle">●</span> '+pdf_builder_text.draggable_elements+' <br /><span class="pb-circle">●</span> '+pdf_builder_text.hold_shift+' <br /><span class="pb-circle">●</span> '+pdf_builder_text.use_help_Lines+' <br /><span class="pb-circle">●</span> '+pdf_builder_text.click_to_style+'<br /> <span class="pb-circle">●</span> '+pdf_builder_text.use_flags+' <br /><span class="pb-circle">●</span> '+pdf_builder_text.see_full_page,

                readonlyReadonlyComponentsText: '<span class="pg-flag">⚑</span> '+pdf_builder_text.variables_available,
                readonlyReadonlyComponentsDescription: pdf_builder_text.dynamically_generating_info,
                readonlyComponentsText: pdf_builder_text.custom_fields,
                readonlyComponentsDescription: pdf_builder_text.use_own_fields,
                templatesText: pdf_builder_text.default_templates,
                templatesTextButton: pdf_builder_text.choose_template,
                templatesDescription: pdf_builder_text.choose_available_template,
                popupTextTemplates: pdf_builder_text.choose_template,
                collapseText:'<span class="pb-collapse" aria-hidden="true"></span> '+pdf_builder_text.collapse,
                expandText:'<span class="pb-collapse expand" aria-hidden="true"></span> <span class="expand-text">'+pdf_builder_text.expand+'</span>',
                readonlyPageComponentTitle: pdf_builder_text.page_component_title_readonly,
                selectTemplateQuestion: pdf_builder_text.select_template_question,
                smallDeviceInfo: pdf_builder_text.small_device_info,
                clearPageQuestion: pdf_builder_text.clear_page_question,
                exitPageQuestion: pdf_builder_text.exit_page_question,
            },
            helpersElements = {
                changeOrientation:{
                    'id':'change_orientation',
                    'action': 'changeOrientation()',
                    'text': pdf_builder_text.change_positions,
                    'element': '$topBarToolBox',
                    'class': 'builder-btn builder-btn--standard',
                    'type':'button'
                },
                pdfPreview:{
                    'id':'pdf_preview',
                    'action': 'getPdfPreview()',
                    'text': pdf_builder_text.download_sample_pdf,
                    'element': '$topBarToolBox',
                    'class': 'builder-btn builder-btn--blue',
                    'type':'button'
                },
                clearPage:{
                    'id':'clear_page',
                    'action': 'clearPage()',
                    'text': pdf_builder_text.clear,
                    'element': '$topBarToolBox',
                    'class': 'builder-btn builder-btn--blue',
                    'type':'button'
                },
                separate:{
                    'id':'separate_1',
                    'element': '$topBarToolBox',
                    'type':'separate'
                },
                exit:{
                    'id':'exit',
                    'action': 'exit()',
                    'text': pdf_builder_text.exit,
                    'element': '$topBarToolBox',
                    'class': 'builder-btn builder-btn--grey',
                    'type':'button'
                },
                savePage:{
                    'id':'save_page',
                    'action': 'savePage()',
                    'text': pdf_builder_text.save,
                    'element': '$topBarToolBox',
                    'class': 'builder-btn builder-btn--green',
                    'type':'button'
                },
            },
            componentsButtons = {
                certificate_number:{
                    'name':'certificate_number',
                    'type': TYPE_TEXT_INPUT,
                    'readonly': true,
                    'button_name': '✚ '+pdf_builder_text.certificate_number,
                    'class': 'blue-button builder-btn builder-btn--add-item pb-btn',
                    'value':'1423 / 2021',
                    'processing_string': '{certificate_number}',
                    'element': '$boxReadonlyComponentsButtons'
                },
                course_name:{
                    'name':'course_name',
                    'type': TYPE_TEXT_INPUT,
                    'readonly': true,
                    'button_name': '✚ '+pdf_builder_text.course_name,
                    'class': 'blue-button builder-btn builder-btn--add-item pb-btn',
                    'value':'Przykładowy tytuł kursu',
                    'processing_string': '{course_name}',
                    'element': '$boxReadonlyComponentsButtons'
                },
                student_first_name:{
                    'name':'student_first_name',
                    'type': TYPE_TEXT_INPUT,
                    'readonly': true,
                    'button_name': '✚ '+pdf_builder_text.student_first_name,
                    'class': 'blue-button builder-btn builder-btn--add-item pb-btn',
                    'value':'Janusz',
                    'processing_string': '{student_first_name}',
                    'element': '$boxReadonlyComponentsButtons'
                },
                student_last_name:{
                    'name':'student_last_name',
                    'type': TYPE_TEXT_INPUT,
                    'readonly': true,
                    'button_name': '✚ '+pdf_builder_text.student_last_name,
                    'class': 'blue-button builder-btn builder-btn--add-item pb-btn',
                    'value':'Nowakowski',
                    'processing_string': '{student_last_name}',
                    'element': '$boxReadonlyComponentsButtons'
                },
                student_name:{
                    'name':'student_name',
                    'type': TYPE_TEXT_INPUT,
                    'readonly': true,
                    'button_name': '✚ '+pdf_builder_text.student_first_name_last_name,
                    'class': 'blue-button builder-btn builder-btn--add-item pb-btn',
                    'value':'Jan Nowak',
                    'processing_string': '{student_name}',
                    'element': '$boxReadonlyComponentsButtons'
                },
                certificate_date:{
                    'name':'certificate_date',
                    'type': TYPE_TEXT_INPUT,
                    'readonly': true,
                    'button_name': '✚ '+pdf_builder_text.certificate_creation_date,
                    'class': 'blue-button builder-btn builder-btn--add-item pb-btn',
                    'value':'23.09.2020',
                    'processing_string': '{certificate_date}',
                    'element': '$boxReadonlyComponentsButtons'
                },
                course_price:{
                    'name':'course_price',
                    'type': TYPE_TEXT_INPUT,
                    'readonly': true,
                    'button_name': '✚ '+pdf_builder_text.course_price,
                    'class': 'blue-button builder-btn builder-btn--add-item pb-btn',
                    'value':'19,99',
                    'processing_string': '{course_price}',
                    'element': '$boxReadonlyComponentsButtons'
                },
                coach_name:{
                    'name':'coach_name',
                    'type': TYPE_TEXT_INPUT,
                    'readonly': true,
                    'button_name': '✚ '+pdf_builder_text.coach_name,
                    'class': 'blue-button builder-btn builder-btn--add-item pb-btn',
                    'value':'Jastrzębski Piotr',
                    'processing_string': '{coach_name}',
                    'element': '$boxReadonlyComponentsButtons'
                },
                background:{
                    'name':'background',
                    'type': TYPE_IMAGE,
                    'readonly': false,
                    'button_name': pdf_builder_text.choose_background,
                    'class': 'secondary-button pb-btn',
                    'value':'Wpisz treść',
                    'element': '$boxComponentsButtons',
                    'icon': 'wp-media-buttons-icon',
                    'action': 'setBackground'
                },
                text_input:{
                    'name':'text_input',
                    'type': TYPE_TEXT_INPUT,
                    'readonly': false,
                    'button_name': '✚ '+pdf_builder_text.add_text_field,
                    'class': 'secondary-button pb-btn',
                    'value':pdf_builder_text.enter_content,
                    'element': '$boxComponentsButtons'
                },
                image:{
                    'name':'image',
                    'type': TYPE_IMAGE,
                    'readonly': false,
                    'button_name': '✚ '+pdf_builder_text.picture,
                    'class': 'secondary-button pb-btn',
                    'value':'empty',
                    'element': '$boxComponentsButtons',
                    'icon': 'wp-media-buttons-icon'
                },
            },

            // add componets functions
            addImageComponent = function (button) {
                var self = $( this ),
                    id = getComponentId();
                // Create the media frame.
                var file_frame = wp.media.frames.file_frame = wp.media( {
                    title: self.data( 'uploader_title' ),
                    button: {
                        text: self.data( 'uploader_button_text' ),
                    },
                    multiple: false,

                } );

                file_frame.on( 'select', function () {
                    var attachment = file_frame.state().get( 'selection' ).first().toJSON();


                    if(button.action){
                        window[button.action](attachment.url)
                    } else {
                        var $componentPage = createImageComponent(id,attachment.url,attachment.width, attachment.height);
                        elements.$page.append($componentPage)
                        $componentPage.setDraggable(true);
                        $componentPage.setResizable(options, false)
                    }

                } );

                file_frame.open();
            },
            addTextComponent = function (name, type, value, readonly, position) {
                var $componentPage = createPageComponent(getComponentId(name),value,name, readonly);
                elements.$page.append($componentPage)

                $componentPage.setDraggable(readonly);
                $componentPage.setResizable(options)
                $componentPage.setTextEditor(readonly, options, true);

                if(position){
                    $componentPage.css('top', position.y)
                    $componentPage.css('left', position.x)
                }

            },

            // additional funcions
            getComponentId = function(name){
                return name + '-' + $('[data-name="course_name"]').length;
            },
            setHorizontalOrientation = function(){
                setOrientation(ORIENTATION_HORIZONTAL)
            },
            setVerticalOrientation = function(){
                setOrientation(ORIENTATION_VERTICAL)
            }
            setOrientation = function(dataOrientation){
                options.orientation = dataOrientation
                if(dataOrientation == ORIENTATION_HORIZONTAL){
                    options.orientation_style = STYLES.HORIZONTAL_PAGE
                    elements.$page.setStyles(STYLES.HORIZONTAL_PAGE)
                } else {
                    options.orientation_style = STYLES.VERTICAL_PAGE
                    elements.$page.setStyles(STYLES.VERTICAL_PAGE)
                }
                elements.$page.attr('data-orientation', dataOrientation)
            },
            replaceDefaultStringsToPorcessingString = function(callback){
                var itemsProcessed = 0,
                    replacedPage = $('<div/>').append(elements.$page.clone()).html();

                replacedPage = replacedPage.replaceAll('&quot;', '');

                $.each(componentsButtons, function( name, value ) {
                    replacedPage = replacedPage.replaceAll(value.value, value.processing_string);
                    itemsProcessed++;
                    if(itemsProcessed === Object.keys(componentsButtons).length) {
                        callback(replacedPage);
                    }
                });
            },

            //button function
            hideTemplatesPopup = function(){
                elements.$popupTemplates.hide();
            },
            showTemplatesPopup = function(){
                elements.$popupTemplates.show();
            },
            changeOrientation = function (e) {
                if(options.orientation === ORIENTATION_HORIZONTAL){
                    setVerticalOrientation()
                    return;
                }
                setHorizontalOrientation()
            },
            setBackground = function(url) {
                elements.$page.css('background', 'url('+url+')')
                elements.$page.css('background-size', 'contain')
            },
            getPdfPreview = function (e){

                showPbLoader()
                var $preview = $('<div/>', {
                    id: 'pb-preview',
                    style:'all:initial; display:flex',
                }).append(elements.$page.clone())

                $('#pb-container').append($preview)


                var element = document.getElementById('pb-preview')
                var opt = {
                    margin:0,
                    filename:     'certificate.pdf',
                    image:        { type: 'jpeg', quality: 1 },
                    html2canvas:  { scale: 2, useCORS: true },
                    jsPDF:        {format : 'a4', orientation  : (options.orientation == ORIENTATION_HORIZONTAL) ? 'landscape' : 'portrait'}
                };
                $('#pb-preview .ui-icon-gripsmall-diagonal-se').hide()
                html2pdf(element, opt);

                $preview.remove()
                hidePbLoader()
            },
            savePage = function (e){
                showPbLoader()
                replaceDefaultStringsToPorcessingString(function (page) {
                    elements.$this.val(page)
                    callbackOnSave()
                })
            },
            exit = function(e){
                callbackOnExit()
            },
            clearPage = function(e){
                var r = confirm(options.clearPageQuestion);
                if (r == true) {
                    $(SELECTOR_PAGE).html(' ')
                    elements.$page.css('background', '')
                }
            },

            // building functions
            buildBuilder = function (){
                createContainer()
                createTopBarToolBox()
                createLeftToolBox()
                createTopToolBox()
                createPage()
                addhelpersElements()
                addComponentsButtons()
                addTemplates()
            },
            createContainer = function () {
                elements.$container = createDiv('pb-container');

                elements.$leftColumn = createDiv('pb-column-left');
                elements.$container.append(elements.$leftColumn)

                elements.$middleColumn = createDiv('pb-column-middle');
                elements.$container.append(elements.$middleColumn)

            },
            createTopBarToolBox = function () {
                elements.$topBarToolBox = createDiv('pb-topbar-toolbox');
                $('.certificate-builder-wrapper__top-bar__column--right').append(elements.$topBarToolBox);
            },
            createLeftToolBox = function () {


                elements.$boxCollapse = createDiv('pb-collapse-box');
                elements.$boxCollapseElement = $('<div/>', {
                    id: 'pb-collapse-text',
                    html: options.collapseText,
                    click: function(e) {
                        e.preventDefault();
                        if($('#pb-container').hasClass('collapse')){
                            expand()
                        } else{
                            collapse()
                        }

                    }
                })
                elements.$boxCollapse.append(elements.$boxCollapseElement)
                elements.$leftColumn.append(elements.$boxCollapse)

                elements.$boxTemplates = createDiv('pb-templates-box');
                elements.$boxTemplates.append(createDiv('pb-templates-text', options.templatesText))
                elements.$boxTemplates.append(createDiv('pb-templates-description', options.templatesDescription))
                elements.$boxTemplates.append(createButton({'class':'secondary-button','action':'showTemplatesPopup()', 'text':options.templatesTextButton}));


                elements.$popupTemplates = createDiv('pb-templates-popup');
                elements.$popupContainerTemplates = createDiv('pb-templates-popup-container');
                elements.$popupContainerTemplates.append(createDiv('pb-templates-text', options.popupTextTemplates))
                elements.$popupContainerTemplates.append(createButton({'class':'pb-templates-popup-close','action':'hideTemplatesPopup()', 'text':'✖'}))
                elements.$popupContainerTemplatesBlocker = $('<div/>', {
                    id: 'pb-templates-popup-blocker',
                    click: function(e) {
                        e.preventDefault();
                        hideTemplatesPopup()
                    }
                });
                elements.$popupTemplates.append(elements.$popupContainerTemplatesBlocker)
                elements.$popupTemplates.append(elements.$popupContainerTemplates)

                elements.$container.append(elements.$popupTemplates)
                elements.$leftColumn.append(elements.$boxTemplates)


                elements.$boxReadonlyComponentsButtons = createDiv('pb-readonly-buttons-box');
                elements.$boxReadonlyComponentsButtons.append(createDiv('pb-readonly-buttons-text', options.readonlyReadonlyComponentsText))
                elements.$boxReadonlyComponentsButtons.append(createDiv('pb-readonly-buttons-description', options.readonlyReadonlyComponentsDescription))
                elements.$leftColumn.append(elements.$boxReadonlyComponentsButtons)

                elements.$boxComponentsButtons = createDiv('pb-components-buttons-box');
                elements.$boxComponentsButtons.append(createDiv('pb-components-buttons-text', options.readonlyComponentsText))
                elements.$boxComponentsButtons.append(createDiv('pb-components-buttons-description', options.readonlyComponentsDescription))
                elements.$leftColumn.append(elements.$boxComponentsButtons)

                elements.boxInfo = createDiv('pb-info-box');
                elements.boxInfo.append(createDiv('pb-info-text', options.infoComponentsText))
                elements.boxInfo.append(createDiv('pb-info-description', options.infoComponentsDescription))
                elements.$leftColumn.append(elements.boxInfo)

                elements.$leftToolBox = createDiv('pb-left-toolbox');
                elements.$leftColumn.append(elements.$leftToolBox)
            },
            createTopToolBox = function () {
                elements.$middleColumn.append(createDiv('pb-top-toolbox-text', options.topToolBoxText))

                elements.$topToolBox = createDiv('pb-top-toolbox-box');
                elements.$topToolBox.css('all', 'initial');
                elements.$middleColumn.append(elements.$topToolBox)
            },
            createPage = function () {
                if(elements.$this.val()){
                    elements.$page = $(elements.$this.val())
                    setOrientation(elements.$page.data('orientation'))
                } else {
                    elements.$page = $('<div/>', {
                        id: SELECTOR_NAME_PAGE,
                        'data-orientation': options.orientation
                    });
                    elements.$page.setStyles(options.orientation_style)
                }
                elements.$container.append(createDiv(null,'<div class="lds-ring"><div></div><div></div><div></div><div></div></div>', 'pb-loader'));
                elements.$page.append(createDiv(null,null, 'helperx'));
                elements.$page.append(createDiv(null,null, 'helpery'));

                elements.$topToolBox.append(elements.$page)

            },
            addhelpersElements = function () {
                $.each(helpersElements, function( name, value ) {
                    if(value.type == 'button'){
                        elements[value.element].append(createButton(value))
                    }
                    if(value.type == 'separate'){
                        elements[value.element].append(createSeparate(value))
                    }
                });
            },
            addTemplates = function(){
                $.each(templates, function( name, value ) {
                    elements[value.element].append(createTemplate(value))
                });
            },
            addComponentsButtons = function () {
                $.each(componentsButtons, function( name, value ) {
                    elements[value.element].append(createComponentButton(value))
                });
            },
            loadContainer = function(){
                var body = createDiv('pb-body');
                elements.$this.before(body)
                elements.$container.appendTo(body)
            },
            loadPage = function(replaceStrings = true){
                $('.ui-resizable-handle').remove()
                $( SELECTOR_COMPONENT_BOX ).each(function( index ) {
                    var readonly = $(this).data('readonly'),
                        name = $(this).data('name'),
                        disableEditor = $(this).data('disable-editor');

                    if(readonly && replaceStrings){
                        var replaceString = $(this).html().replace(componentsButtons[name].processing_string, componentsButtons[name].value);
                        $(this).html(replaceString)
                    }

                    $(this).parent().data("readonly", true)
                    $(this).parent().setDraggable(readonly)
                    if(!disableEditor){
                        $(this).parent().setResizable(options,true)
                        $(this).parent().setTextEditor(readonly, options);
                    } else{
                        $(this).parent().setResizable(options,false)
                    }

                });
            },

            //helpers function
            createPageComponent = function(id, value,name, readonly) {

                var componentElementsBg = $('<div/>', {
                    class: 'component-elements-bg',
                    'style': 'display:none'
                });
                componentElementsBg.append(
                    $('<div/>', {
                        class: 'draggable-icon',
                        html: '<span class="dashicons dashicons-move"></span>',
                        title: pdf_builder_text.move
                    })
                )
                componentElementsBg.append(
                    $('<div/>', {
                        class: 'remove-element',
                        html: '<span class="dashicons dashicons-no-alt"></span>',
                        title: pdf_builder_text.delete
                    })
                )

                return $('<div/>', {
                    'data-readonly':readonly,
                    'style':'position:absolute; height:auto; width:200px; min-height: 30px; min-width: 50px;'
                }).append(
                    $('<div/>', {
                        class: SELECTOR_NAME_COMPONENT_BOX,
                        id: getRandomString(),
                        'data-name':name,
                        'data-readonly':readonly,
                        'title': (readonly) ? options.readonlyPageComponentTitle : '',
                        text: value
                    })
                ).append(
                    componentElementsBg
                )
            },
            createImageComponent = function(id, value, width, height) {

                var componentElementsBg = $('<div/>', {
                    class: 'component-elements-bg',
                    'style': 'display:none'
                });
                componentElementsBg.append(
                    $('<div/>', {
                        class: 'draggable-icon',
                        html: '<span class="dashicons dashicons-move"></span>',
                        title: pdf_builder_text.move
                    })
                )
                componentElementsBg.append(
                    $('<div/>', {
                        class: 'remove-element',
                        html: '<span class="dashicons dashicons-no-alt"></span>',
                        title: pdf_builder_text.delete
                    })
                )
                return $('<div/>', {
                    'style':'width:'+width+'px;height:'+height+'px;position:absolute;min-height: 30px; min-width: 50px;'

                }).append(
                    $('<div/>', {
                        class: SELECTOR_NAME_COMPONENT_BOX,
                        'data-disable-editor':true,
                        id: getRandomString(),
                        style: 'width:100%;height:100%;background:url('+value+');background-size: contain;background-repeat: no-repeat'
                    })
                ).append(
                    componentElementsBg
                )
            },
            createDiv = function(id, text, cssClass) {
                return $('<div/>', {
                    id: id,
                    html: text,
                    class: cssClass
                });
            },
            createSeparate = function(separate) {
                var $separate = $('<div/>', {
                    id: separate.id,
                    class: 'pb-separate',
                })
                return $separate;
            },
            createButton = function(button) {
                var $button = $('<button/>', {
                    id: button.id,
                    class: button.class,
                    click: function(e) {
                        e.preventDefault();
                        eval(button.action);
                    }
                }).append(
                    $('<i/>', {
                        class: button.icon,
                    })
                ).append(
                    $('<span/>', {
                        text: button.text,
                    })
                )
                $button.setStyles(STYLES[button.styles]);
                return $button;
            },
            createTemplate = function(template){
                var $template = $('<div/>', {
                    class: 'pb-template',
                    click: function(e) {
                        e.preventDefault();

                        var txt;
                        var r = confirm(options.selectTemplateQuestion);
                        if (r == true) {
                            elements.$page.remove();
                            elements.$page = $(template.html);
                            setOrientation(elements.$page.data('orientation'));
                            elements.$topToolBox.append( elements.$page);
                            loadPage(true);
                            hideTemplatesPopup();
                        }
                    }
                }).append(
                    $('<span/>', {
                        text: template.name,
                    })
                ).append(
                    $('<img/>', {
                        src: template.image,
                    })
                );
                return $template;
            },
            createComponentButton = function (button) {
                var $button = $('<div/>', {
                    class: button.class,
                    click: function(e) {
                        e.preventDefault();
                        if(button.type === TYPE_TEXT_INPUT){
                            addTextComponent(button.name, button.type, button.value, button.readonly, null);
                        } else if(button.type === TYPE_IMAGE){
                            addImageComponent(button);
                        }
                    }
                }).append(
                    $('<i/>', {
                        class: button.icon,
                    })
                ).append(
                    $('<span/>', {
                        text: button.button_name,
                    })
                );
                $button.setStyles(STYLES[button.styles]);
                if(button.type === TYPE_TEXT_INPUT){
                    $button.draggable({
                        opacity: 0.7,
                        containment: SELECTOR_PAGE,
                        appendTo: "body",
                        helper: function( event ) {
                            return $( "<div class='dragging-helper-element'><p>"+button.value+"</p></div>" );
                        },
                        stop: function(e) {
                            var x = e.pageX - elements.$page.offset().left,
                                y = e.pageY - elements.$page.offset().top;
                            addTextComponent(button.name, button.type, button.value, button.readonly, {x:x,y:y});
                        },
                    })
                }

                return $button;
            }, getRandomString = function(length = 10) {
                var result           = '';
                var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                var charactersLength = characters.length;
                for ( var i = 0; i < length; i++ ) {
                    result += characters.charAt(Math.floor(Math.random() * charactersLength));
                }
                return result;
            }, collapse = function(){
                $('#pb-container').addClass('collapse')
                elements.$boxCollapseElement.html(options.expandText)
            }, expand = function(){
                $('#pb-container').removeClass('collapse')
                elements.$boxCollapseElement.html(options.collapseText)
            }, smallDeviceInfo = function () {
                if($( window ).width() < 860){
                    alert(options.smallDeviceInfo)
                }
            }, smallDeviceCollapse = function () {
                if($( window ).width() < 1023){
                    collapse()
                }
            }, showPbLoader = function () {
                $('.pb-loader').show();
            }, hidePbLoader = function () {
                $('.pb-loader').hide();
            }, closeWindowAlert = function () {
                $(window).bind('beforeunload', function(e){
                    var element = e.originalEvent.target.activeElement;
                    if($(element).attr('id') != 'save_page'){
                        return '';
                    }
                });
            }


        function init() {
            buildBuilder()
            loadContainer();
            loadPage();
            smallDeviceInfo();
            smallDeviceCollapse();
            closeWindowAlert();


            $('body').on('click', '.remove-element', function () {
                $(this).parents('.ui-draggable').remove()
            })
        }

        init();
    };

}( jQuery ));


jQuery( document ).ready(function() {
    if(jQuery('#certificate-builder').length > 0){

        var callbackSave = function () {
            var data = jQuery('#certificate-form').serialize(),
                certificate_template_action = jQuery('#certificate-form').find('input[name="certificate_template_action"]').val(),
                url = jQuery('#certificate-form').attr('action');

            if(certificate_template_action === 'add'){
                showPbLoader()

                jQuery.ajax({
                    data:data,
                    dataType: 'json',
                    type: 'POST',
                    url: url,
                    success: function (response) {
                        window.location = response.redirect_url
                        hidePbLoader()
                    },
                    error: function ( jqXHR, textStatus, errorThrown ) {
                        window.snackbar.show(jqXHR.responseJSON.error_message, 'error');
                        hidePbLoader()
                    }
                })
            }

            if(certificate_template_action === 'edit'){
                showPbLoader()

                jQuery.ajax({
                    data:data,
                    dataType: 'json',
                    type: 'POST',
                    url: url,
                    success: function (response) {
                        hidePbLoader()
                        window.snackbar.show(pdf_builder_text.certificate_template_edited);
                    },
                    error: function ( jqXHR, textStatus, errorThrown ) {
                        window.snackbar.show(jqXHR.responseJSON.error_message, 'error');
                        hidePbLoader()
                    }
                })
            }

        }, callbackExit = function () {
            location.href = pdf_builder_text.settings_page_url;
        }

        jQuery('#certificate-builder').pageBuilder(
            callbackSave,
            callbackExit,
        );
    }
});
    var styleToEdit;


    var colorToHex = function(color) {

        return  ( '0' + parseInt(color[0], 10).toString(16) ).slice(-2)
        + ( '0' + parseInt(color[1], 10).toString(16) ).slice(-2)
        + ( '0' + parseInt(color[2], 10).toString(16) ).slice(-2);

    };


    var getRgbOfElementOrParent = function(element, color){
        let rgbOfElementOrParent  = element.style[color]  ? element.style[color]  : element.parentNode.style[color] ;

        let rgbArray = rgbOfElementOrParent ? rgbOfElementOrParent : '(0,0,0)';

        rgbArray = rgbArray
            .replace("rgba", "")
            .replace("rgb", "")
            .replace("(", "")
            .replace(")", "");

        rgbArray = rgbArray.split(","); // get Array["R","G","B"]

        return rgbArray;

    }


    var changeColorPickerValue = function(mutationsList, observer) {

        mutationsList.forEach(mutation => {

            if (mutation.target.title == 'Background color') {

                styleToEdit = 'background-color';

            } else if (mutation.target.title == 'Text color') {

                styleToEdit = 'color';

            }


            if (mutation.attributeName === 'class' && mutation.target.getAttribute(mutation.attributeName).includes('tox-dialog__disable-scroll')) {

               let element = tinymce.activeEditor.selection.getNode();
               let rgbColor = getRgbOfElementOrParent(element, styleToEdit);

                document.querySelectorAll("[aria-label='Hex color code']")[0].nextSibling.value = colorToHex(rgbColor);

            }

        });

    }


