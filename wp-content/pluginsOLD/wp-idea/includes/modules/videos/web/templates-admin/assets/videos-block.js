const el = wp.element.createElement;

const { registerBlockType } = wp.blocks;
const { RichText, InspectorControls } = wp.editor;
const { Fragment } = wp.element;
const { TextControl, ToggleControl, Panel, PanelBody, PanelRow, SelectControl  } = wp.components;

var _this = this;

const _block = window.BPMJ_WPI_VIDEOS_BLOCK;
const selectOptions = _block.select_options;

wp.blocks.registerBlockType(_block.name, {
    title: _block.title,
    icon: 'lightbulb',
    category: _block.cat,
    attributes: {
        videoID: {
            type: "string"
        },
    },
    edit: function(props) {
        const {
            attributes: { videoID },
            setAttributes
        } = props;

        const onSelectVideo = value => {
            setAttributes({ videoID: value });
        };

        let optionElements = [];

        optionElements.push(React.createElement("option", {value: ''}, _block.select_hint));

        for (const options of selectOptions) {
            for (const [key, value] of Object.entries(options)) {
                optionElements.push(React.createElement("option", {value: key}, value));
            }
        }

        return React.createElement(
            "div", {className: 'publigo-video-block components-placeholder is-large'},
            React.createElement("span", {
                className: 'publigo-video-block__title components-placeholder__label'
            }, _block.title),
            React.createElement("div", {
                className: 'publigo-video-block__hint components-placeholder__instructions'
            }, _block.hint),
            React.createElement("div", {
                    className: 'publigo-video-block__hint components-placeholder__fieldset'
                },
                React.createElement("select", {
                        onChange: (e) => {
                            onSelectVideo(e.target.value)
                        },
                        value: videoID,
                        required: true
                    },
                    optionElements
                )
            )
        )
    },
    save: function(props) {
        return null;
    }
})