var BlocksCreator = {
    FIELD_VALIDATION_WARNING_CLASS_NAME: 'field_validation_warning',

    init: function(){
        this.createBlocks();
    },

    getAttsControls: function(atts, props, setAttributes){
      const el = wp.element.createElement;
      const { TextControl, ToggleControl, SelectControl, ColorPalette, PanelRow, __experimentalAlignmentMatrixControl } = wp.components;

      var controls = [];
      atts = toArray(atts);

      for (var key in atts) {
        let _el = null;

          if(atts[key].input_type === 'number'){
              _el = el( TextControl,
                  {
                      label: atts[key].label,
                      help: atts[key].help ? atts[key].help : false,
                      type: 'number',
                      name: key,
                      min: atts[key].min ? atts[key].min.value : false,
                      min_warning: atts[key].min ? atts[key].min.warning : false,
                      onChange: ( value ) => {
                          if(_el.props.min && value < _el.props.min){
                              this.displayInputHint(_el.props.name, _el.props.min_warning);
                          } else {
                              setAttributes( { [_el.props.name]: parseInt(value) } );
                              this.hideInputHint(_el.props.name);
                          }
                      },
                      value: props.attributes[key]
                  }
              )
          }

          if(atts[key].input_type === 'select'){
              _el = el( SelectControl,
                  {
                      label: atts[key].label,
                      help: atts[key].help ? atts[key].help : false,
                      name: key,
                      options: atts[key].options,
                      value: props.attributes[key],
                      onChange: ( value ) => {
                          setAttributes( { [_el.props.name]: value } );
                      },
                  }
              );
          }

          if(atts[key].input_type === 'boolean'){
              _el = el( ToggleControl,
                  {
                      label: atts[key].label,
                      help: atts[key].help ? atts[key].help : false,
                      name: key,
                      options: atts[key].options,
                      checked: props.attributes[key],
                      onChange: ( value ) => {
                          setAttributes( { [_el.props.name]: value } );
                      },
                  }
              )
          }

          if(atts[key].input_type === 'alignment-matrix'){
              _el = el(
                  'div', {
                      className: 'wpi-alignment-matrix wpi-block-attribute',
                      name: key
                  }, [
                      el('span', {
                          className: 'wpi-alignment-matrix-title wpi-block-attribute-title'
                      }, atts[key].label),
                      atts[key].help ? el('span', {
                          className: 'wpi-alignment-matrix-help wpi-block-attribute-help'
                      }, atts[key].help) : null,
                      el( __experimentalAlignmentMatrixControl,
                          {
                              label: atts[key].label,
                              value: props.attributes[key],
                              name: key,
                              onChange: ( value ) => {
                                  setAttributes( { [_el.props.name]: value } );
                              }
                          }
                      ),
                  ]
              )
          }

          if(atts[key].input_type === 'color-picker'){
              _el = el(
                  'div', {
                      className: 'wpi-color-picker wpi-block-attribute',
                      name: key
                  }, [
                      el('span', {
                          className: 'wpi-color-picker-title wpi-block-attribute-title'
                      }, atts[key].label),
                      atts[key].help ? el('span', {
                          className: 'wpi-color-picker-help wpi-block-attribute-help'
                      }, atts[key].help) : null,
                      el( ColorPalette,
                          {
                              value: props.attributes[key],
                              name: key,
                              clearable: false,
                              onChange: ( value ) => {
                                  setAttributes( { [_el.props.name]: value } );
                              }
                          }
                      ),
                  ]
              )
          }

        if(_el) {
            controls.push(el( PanelRow, {}, _el))
        }
      }

      return controls;
    },

    displayInputHint: function(fieldName, hint){
      let el = document.querySelector(`[name=${fieldName}]`);

      if(el == null) return;

      if(el.parentNode.querySelector(`.${this.FIELD_VALIDATION_WARNING_CLASS_NAME}`) == null){
        let warningEl = document.createElement('span');
        warningEl.classList.add(this.FIELD_VALIDATION_WARNING_CLASS_NAME);
        warningEl.innerText = hint;

        el.parentNode.append(warningEl);
      }
    },

    hideInputHint: function(fieldName){
      let el = document.querySelector(`[name=${fieldName}]`);

      if(el == null) return;

      let warningEl = el.parentNode.querySelector(`.${this.FIELD_VALIDATION_WARNING_CLASS_NAME}`);
      if(warningEl != null){
        warningEl.remove();
      }
    },

    createBlocks: function(){
        if(!window.BPMJ_WPI_TEMPLATE_BLOCKS) return;

        const el = wp.element.createElement;

        const { registerBlockType } = wp.blocks;

        const { RichText, InspectorControls } = wp.editor;
        const { Fragment } = wp.element;
        const { TextControl, ToggleControl, Panel, PanelBody, PanelRow } = wp.components;

        var _this = this;

        window.BPMJ_WPI_TEMPLATE_BLOCKS.forEach(_block => {

            wp.blocks.registerBlockType(_block.name, {
                title: _block.title,
                icon: 'lightbulb',
                category: _block.cat,
                attributes: _block.attributes,
                edit: function(props) {
                  return(
                  el( Fragment, {},
                    el( InspectorControls, {},
                      el( PanelBody, { title: _block.title, initialOpen: true },
                        _this.getAttsControls(_block.attributes, props, props.setAttributes)
                      ),

                    ),
                    React.createElement(
                      "div", {className: 'wpi-placeholder wpi-placeholder--' +/[^/]*$/.exec( _block.name)[0]}, //remove 'wpi/' from block name
                      React.createElement("span", null, _block.title)
                    )
                  )
                  )
                },
                save: function(props) {
                    return null;
                }
              })
        });
    }
}

BlocksCreator.init();

function toArray(_Object){
  var _Array = new Array();
  for(var name in _Object){
          _Array[name] = _Object[name];
  }
  return _Array;
}
