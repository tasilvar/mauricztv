/*! @license https://github.com/onury/invert-color */
(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define(factory) :
    (global.invert = factory());
}(this, (function () { 'use strict';

    // -------------------------------
    // TYPES / INTERFACES
    // -------------------------------
    // -------------------------------
    // CONSTANTS
    // -------------------------------
    var DEFAULT_THRESHOLD = Math.sqrt(1.05 * 0.05) - 0.05;
    var RE_HEX = /^(?:[0-9a-f]{3}){1,2}$/i;
    var DEFAULT_BW = {
        black: '#000000',
        white: '#ffffff',
        threshold: DEFAULT_THRESHOLD
    };
    // -------------------------------
    // HELPER METHODS
    // -------------------------------
    function padz(str, len) {
        if (len === void 0) { len = 2; }
        return (new Array(len).join('0') + str).slice(-len);
    }
    function hexToRgbArray(hex) {
        if (hex.slice(0, 1) === '#')
            hex = hex.slice(1);
        if (!RE_HEX.test(hex))
            throw new Error("Invalid HEX color: \"" + hex + "\"");
        // normalize / convert 3-chars hex to 6-chars.
        if (hex.length === 3) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        return [
            parseInt(hex.slice(0, 2), 16),
            parseInt(hex.slice(2, 4), 16),
            parseInt(hex.slice(4, 6), 16) // b
        ];
    }
    function toRGB(c) {
        return { r: c[0], g: c[1], b: c[2] };
    }
    function toRgbArray(c) {
        if (!c)
            throw new Error('Invalid color value');
        if (Array.isArray(c))
            return c;
        return typeof c === 'string' ? hexToRgbArray(c) : [c.r, c.g, c.b];
    }
    // http://stackoverflow.com/a/3943023/112731
    function getLuminance(c) {
        var i, x;
        var a = []; // so we don't mutate
        for (i = 0; i < c.length; i++) {
            x = c[i] / 255;
            a[i] = x <= 0.03928 ? x / 12.92 : Math.pow((x + 0.055) / 1.055, 2.4);
        }
        return 0.2126 * a[0] + 0.7152 * a[1] + 0.0722 * a[2];
    }
    function invertToBW(color, bw, asArr) {
        var options = (bw === true)
            ? DEFAULT_BW
            : Object.assign({}, DEFAULT_BW, bw);
        return getLuminance(color) > options.threshold
            ? (asArr ? hexToRgbArray(options.black) : options.black)
            : (asArr ? hexToRgbArray(options.white) : options.white);
    }
    // -------------------------------
    // PUBLIC MEMBERS
    // -------------------------------
    /**
     *  Generates inverted (opposite) version of the given color.
     *  @param {Color} color - Color to be inverted.
     *  @param {BlackWhite|boolean} [bw=false] - Whether to amplify the inversion to
     *  black or white. Provide an object to customize black/white colors.
     *  @returns {HexColor} - Hexadecimal representation of the inverted color.
     */
    function invert(color, bw) {
        if (bw === void 0) { bw = false; }
        color = toRgbArray(color);
        if (bw)
            return invertToBW(color, bw);
        return '#' + color.map(function (c) { return padz((255 - c).toString(16)); }).join('');
    }

    /**
     *  Utility methods to generate inverted version of a color.
     *  @namespace
     */
    (function (invert) {
        /**
         *  Generates inverted (opposite) version of the given color, as a RGB object.
         *  @alias invert.asRgbObject
         *  @param {Color} color - Color to be inverted.
         *  @param {BlackWhite|boolean} [bw] - Whether to amplify the inversion to
         *  black or white. Provide an object to customize black/white colors.
         *  @returns {RGB} - RGB object representation of the inverted color.
         */
        function asRGB(color, bw) {
            color = toRgbArray(color);
            var list = bw
                ? invertToBW(color, bw, true)
                : color.map(function (c) { return 255 - c; });
            return toRGB(list);
        }
        invert.asRGB = asRGB;
        /**
         *  Generates inverted (opposite) version of the given color, as a RGB array.
         *  @param {Color} color - Color to be inverted.
         *  @param {BlackWhite|boolean} [bw] - Whether to amplify the inversion to
         *  black or white. Provide an object to customize black/white colors.
         *  @returns {RGB} - RGB array representation of the inverted color.
         */
        function asRgbArray(color, bw) {
            color = toRgbArray(color);
            return bw
                ? invertToBW(color, bw, true)
                : color.map(function (c) { return 255 - c; });
        }
        invert.asRgbArray = asRgbArray;
        /**
         *  Default luminance threshold used for amplifying inversion to black and
         *  white.
         *  @type {number}
         */
        invert.defaultThreshold = DEFAULT_THRESHOLD;
        /**
         *  Alias of `.asRGB()`
         */
        invert.asRgbObject = asRGB;
    })(invert || (invert = {}));
    // -------------------------------
    // EXPORT
    // -------------------------------
    var invert$1 = invert;

    return invert$1;

})));
//# sourceMappingURL=invert.js.map

/* hexToComplimentary : Converts hex value to HSL, shifts
 * hue by 180 degrees and then converts hex, giving complimentary color
 * as a hex value
 * @param  [String] hex : hex value  
 * @return [String] : complimentary color as hex value
 */
function hexToComplimentary(hex){

    // Convert hex to rgb
    // Credit to Denis http://stackoverflow.com/a/36253499/4939630
    var rgb = 'rgb(' + (hex = hex.replace('#', '')).match(new RegExp('(.{' + hex.length/3 + '})', 'g')).map(function(l) { return parseInt(hex.length%2 ? l+l : l, 16); }).join(',') + ')';

    // Get array of RGB values
    rgb = rgb.replace(/[^\d,]/g, '').split(',');

    var r = rgb[0], g = rgb[1], b = rgb[2];

    // Convert RGB to HSL
    // Adapted from answer by 0x000f http://stackoverflow.com/a/34946092/4939630
    r /= 255.0;
    g /= 255.0;
    b /= 255.0;
    var max = Math.max(r, g, b);
    var min = Math.min(r, g, b);
    var h, s, l = (max + min) / 2.0;

    if(max == min) {
        h = s = 0;  //achromatic
    } else {
        var d = max - min;
        s = (l > 0.5 ? d / (2.0 - max - min) : d / (max + min));

        if(max == r && g >= b) {
            h = 1.0472 * (g - b) / d ;
        } else if(max == r && g < b) {
            h = 1.0472 * (g - b) / d + 6.2832;
        } else if(max == g) {
            h = 1.0472 * (b - r) / d + 2.0944;
        } else if(max == b) {
            h = 1.0472 * (r - g) / d + 4.1888;
        }
    }

    h = h / 6.2832 * 360.0 + 0;

    // Shift hue to opposite side of wheel and convert to [0-1] value
    h+= 180;
    if (h > 360) { h -= 360; }
    h /= 360;

    // Convert h s and l values into r g and b values
    // Adapted from answer by Mohsen http://stackoverflow.com/a/9493060/4939630
    if(s === 0){
        r = g = b = l; // achromatic
    } else {
        var hue2rgb = function hue2rgb(p, q, t){
            if(t < 0) t += 1;
            if(t > 1) t -= 1;
            if(t < 1/6) return p + (q - p) * 6 * t;
            if(t < 1/2) return q;
            if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
            return p;
        };

        var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        var p = 2 * l - q;

        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }

    r = Math.round(r * 255);
    g = Math.round(g * 255); 
    b = Math.round(b * 255);

    // Convert r b and g values to hex
    rgb = b | (g << 8) | (r << 16); 
    return "#" + (0x1000000 | rgb).toString(16).substring(1);
}  