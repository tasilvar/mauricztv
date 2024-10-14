/**
 * jQuery serializeObject
 * @copyright 2014, macek <paulmacek@gmail.com>
 * @link https://github.com/macek/jquery-serialize-object
 * @license BSD
 * @version 2.5.0
 */
!function(e,i){if("function"==typeof define&&define.amd)define(["exports","jquery"],function(e,r){return i(e,r)});else if("undefined"!=typeof exports){var r=require("jquery");i(exports,r)}else i(e,e.jQuery||e.Zepto||e.ender||e.$)}(this,function(e,i){function r(e,r){function n(e,i,r){return e[i]=r,e}function a(e,i){for(var r,a=e.match(t.key);void 0!==(r=a.pop());)if(t.push.test(r)){var u=s(e.replace(/\[\]$/,""));i=n([],u,i)}else t.fixed.test(r)?i=n([],r,i):t.named.test(r)&&(i=n({},r,i));return i}function s(e){return void 0===h[e]&&(h[e]=0),h[e]++}function u(e){switch(i('[name="'+e.name+'"]',r).attr("type")){case"checkbox":return"on"===e.value?!0:e.value;default:return e.value}}function f(i){if(!t.validate.test(i.name))return this;var r=a(i.name,u(i));return l=e.extend(!0,l,r),this}function d(i){if(!e.isArray(i))throw new Error("formSerializer.addPairs expects an Array");for(var r=0,t=i.length;t>r;r++)this.addPair(i[r]);return this}function o(){return l}function c(){return JSON.stringify(o())}var l={},h={};this.addPair=f,this.addPairs=d,this.serialize=o,this.serializeJSON=c}var t={validate:/^[a-z_][a-z0-9_]*(?:\[(?:\d*|[a-z0-9_]+)\])*$/i,key:/[a-z0-9_]+|(?=\[\])/gi,push:/^$/,fixed:/^\d+$/,named:/^[a-z0-9_]+$/i};return r.patterns=t,r.serializeObject=function(){return new r(i,this).addPairs(this.serializeArray()).serialize()},r.serializeJSON=function(){return new r(i,this).addPairs(this.serializeArray()).serializeJSON()},"undefined"!=typeof i.fn&&(i.fn.serializeObject=r.serializeObject,i.fn.serializeJSON=r.serializeJSON),e.FormSerializer=r,r});
(function($){$.md5=function(string){function RotateLeft(lValue,iShiftBits){return(lValue<<iShiftBits)|(lValue>>>(32-iShiftBits));}
function AddUnsigned(lX,lY){var lX4,lY4,lX8,lY8,lResult;lX8=(lX&0x80000000);lY8=(lY&0x80000000);lX4=(lX&0x40000000);lY4=(lY&0x40000000);lResult=(lX&0x3FFFFFFF)+(lY&0x3FFFFFFF);if(lX4&lY4){return(lResult^0x80000000^lX8^lY8);}
if(lX4|lY4){if(lResult&0x40000000){return(lResult^0xC0000000^lX8^lY8);}else{return(lResult^0x40000000^lX8^lY8);}}else{return(lResult^lX8^lY8);}}
function F(x,y,z){return(x&y)|((~x)&z);}
function G(x,y,z){return(x&z)|(y&(~z));}
function H(x,y,z){return(x^y^z);}
function I(x,y,z){return(y^(x|(~z)));}
function FF(a,b,c,d,x,s,ac){a=AddUnsigned(a,AddUnsigned(AddUnsigned(F(b,c,d),x),ac));return AddUnsigned(RotateLeft(a,s),b);};function GG(a,b,c,d,x,s,ac){a=AddUnsigned(a,AddUnsigned(AddUnsigned(G(b,c,d),x),ac));return AddUnsigned(RotateLeft(a,s),b);};function HH(a,b,c,d,x,s,ac){a=AddUnsigned(a,AddUnsigned(AddUnsigned(H(b,c,d),x),ac));return AddUnsigned(RotateLeft(a,s),b);};function II(a,b,c,d,x,s,ac){a=AddUnsigned(a,AddUnsigned(AddUnsigned(I(b,c,d),x),ac));return AddUnsigned(RotateLeft(a,s),b);};function ConvertToWordArray(string){var lWordCount;var lMessageLength=string.length;var lNumberOfWords_temp1=lMessageLength+8;var lNumberOfWords_temp2=(lNumberOfWords_temp1-(lNumberOfWords_temp1%64))/64;var lNumberOfWords=(lNumberOfWords_temp2+1)*16;var lWordArray=Array(lNumberOfWords-1);var lBytePosition=0;var lByteCount=0;while(lByteCount<lMessageLength){lWordCount=(lByteCount-(lByteCount%4))/4;lBytePosition=(lByteCount%4)*8;lWordArray[lWordCount]=(lWordArray[lWordCount]|(string.charCodeAt(lByteCount)<<lBytePosition));lByteCount++;}
lWordCount=(lByteCount-(lByteCount%4))/4;lBytePosition=(lByteCount%4)*8;lWordArray[lWordCount]=lWordArray[lWordCount]|(0x80<<lBytePosition);lWordArray[lNumberOfWords-2]=lMessageLength<<3;lWordArray[lNumberOfWords-1]=lMessageLength>>>29;return lWordArray;};function WordToHex(lValue){var WordToHexValue="",WordToHexValue_temp="",lByte,lCount;for(lCount=0;lCount<=3;lCount++){lByte=(lValue>>>(lCount*8))&255;WordToHexValue_temp="0"+lByte.toString(16);WordToHexValue=WordToHexValue+WordToHexValue_temp.substr(WordToHexValue_temp.length-2,2);}
return WordToHexValue;};function Utf8Encode(string){string=string.replace(/\r\n/g,"\n");var utftext="";for(var n=0;n<string.length;n++){var c=string.charCodeAt(n);if(c<128){utftext+=String.fromCharCode(c);}
else if((c>127)&&(c<2048)){utftext+=String.fromCharCode((c>>6)|192);utftext+=String.fromCharCode((c&63)|128);}
else{utftext+=String.fromCharCode((c>>12)|224);utftext+=String.fromCharCode(((c>>6)&63)|128);utftext+=String.fromCharCode((c&63)|128);}}
return utftext;};var x=Array();var k,AA,BB,CC,DD,a,b,c,d;var S11=7,S12=12,S13=17,S14=22;var S21=5,S22=9,S23=14,S24=20;var S31=4,S32=11,S33=16,S34=23;var S41=6,S42=10,S43=15,S44=21;string=Utf8Encode(string);x=ConvertToWordArray(string);a=0x67452301;b=0xEFCDAB89;c=0x98BADCFE;d=0x10325476;for(k=0;k<x.length;k+=16){AA=a;BB=b;CC=c;DD=d;a=FF(a,b,c,d,x[k+0],S11,0xD76AA478);d=FF(d,a,b,c,x[k+1],S12,0xE8C7B756);c=FF(c,d,a,b,x[k+2],S13,0x242070DB);b=FF(b,c,d,a,x[k+3],S14,0xC1BDCEEE);a=FF(a,b,c,d,x[k+4],S11,0xF57C0FAF);d=FF(d,a,b,c,x[k+5],S12,0x4787C62A);c=FF(c,d,a,b,x[k+6],S13,0xA8304613);b=FF(b,c,d,a,x[k+7],S14,0xFD469501);a=FF(a,b,c,d,x[k+8],S11,0x698098D8);d=FF(d,a,b,c,x[k+9],S12,0x8B44F7AF);c=FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);b=FF(b,c,d,a,x[k+11],S14,0x895CD7BE);a=FF(a,b,c,d,x[k+12],S11,0x6B901122);d=FF(d,a,b,c,x[k+13],S12,0xFD987193);c=FF(c,d,a,b,x[k+14],S13,0xA679438E);b=FF(b,c,d,a,x[k+15],S14,0x49B40821);a=GG(a,b,c,d,x[k+1],S21,0xF61E2562);d=GG(d,a,b,c,x[k+6],S22,0xC040B340);c=GG(c,d,a,b,x[k+11],S23,0x265E5A51);b=GG(b,c,d,a,x[k+0],S24,0xE9B6C7AA);a=GG(a,b,c,d,x[k+5],S21,0xD62F105D);d=GG(d,a,b,c,x[k+10],S22,0x2441453);c=GG(c,d,a,b,x[k+15],S23,0xD8A1E681);b=GG(b,c,d,a,x[k+4],S24,0xE7D3FBC8);a=GG(a,b,c,d,x[k+9],S21,0x21E1CDE6);d=GG(d,a,b,c,x[k+14],S22,0xC33707D6);c=GG(c,d,a,b,x[k+3],S23,0xF4D50D87);b=GG(b,c,d,a,x[k+8],S24,0x455A14ED);a=GG(a,b,c,d,x[k+13],S21,0xA9E3E905);d=GG(d,a,b,c,x[k+2],S22,0xFCEFA3F8);c=GG(c,d,a,b,x[k+7],S23,0x676F02D9);b=GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);a=HH(a,b,c,d,x[k+5],S31,0xFFFA3942);d=HH(d,a,b,c,x[k+8],S32,0x8771F681);c=HH(c,d,a,b,x[k+11],S33,0x6D9D6122);b=HH(b,c,d,a,x[k+14],S34,0xFDE5380C);a=HH(a,b,c,d,x[k+1],S31,0xA4BEEA44);d=HH(d,a,b,c,x[k+4],S32,0x4BDECFA9);c=HH(c,d,a,b,x[k+7],S33,0xF6BB4B60);b=HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);a=HH(a,b,c,d,x[k+13],S31,0x289B7EC6);d=HH(d,a,b,c,x[k+0],S32,0xEAA127FA);c=HH(c,d,a,b,x[k+3],S33,0xD4EF3085);b=HH(b,c,d,a,x[k+6],S34,0x4881D05);a=HH(a,b,c,d,x[k+9],S31,0xD9D4D039);d=HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);c=HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);b=HH(b,c,d,a,x[k+2],S34,0xC4AC5665);a=II(a,b,c,d,x[k+0],S41,0xF4292244);d=II(d,a,b,c,x[k+7],S42,0x432AFF97);c=II(c,d,a,b,x[k+14],S43,0xAB9423A7);b=II(b,c,d,a,x[k+5],S44,0xFC93A039);a=II(a,b,c,d,x[k+12],S41,0x655B59C3);d=II(d,a,b,c,x[k+3],S42,0x8F0CCC92);c=II(c,d,a,b,x[k+10],S43,0xFFEFF47D);b=II(b,c,d,a,x[k+1],S44,0x85845DD1);a=II(a,b,c,d,x[k+8],S41,0x6FA87E4F);d=II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);c=II(c,d,a,b,x[k+6],S43,0xA3014314);b=II(b,c,d,a,x[k+13],S44,0x4E0811A1);a=II(a,b,c,d,x[k+4],S41,0xF7537E82);d=II(d,a,b,c,x[k+11],S42,0xBD3AF235);c=II(c,d,a,b,x[k+2],S43,0x2AD7D2BB);b=II(b,c,d,a,x[k+9],S44,0xEB86D391);a=AddUnsigned(a,AA);b=AddUnsigned(b,BB);c=AddUnsigned(c,CC);d=AddUnsigned(d,DD);}
var temp=WordToHex(a)+WordToHex(b)+WordToHex(c)+WordToHex(d);return temp.toLowerCase();};})(jQuery);
/*!
 * JavaScript Cookie v2.1.1
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
;(function (factory) {
	if (typeof define === 'function' && define.amd) {
		define(factory);
	} else if (typeof exports === 'object') {
		module.exports = factory();
	} else {
		var OldCookies = window.Cookies;
		var api = window.Cookies = factory();
		api.noConflict = function () {
			window.Cookies = OldCookies;
			return api;
		};
	}
}(function () {
	function extend () {
		var i = 0;
		var result = {};
		for (; i < arguments.length; i++) {
			var attributes = arguments[ i ];
			for (var key in attributes) {
				result[key] = attributes[key];
			}
		}
		return result;
	}

	function init (converter) {
		function api (key, value, attributes) {
			var result;
			if (typeof document === 'undefined') {
				return;
			}

			// Write

			if (arguments.length > 1) {
				attributes = extend({
					path: '/'
				}, api.defaults, attributes);

				if (typeof attributes.expires === 'number') {
					var expires = new Date();
					expires.setMilliseconds(expires.getMilliseconds() + attributes.expires * 864e+5);
					attributes.expires = expires;
				}

				try {
					result = JSON.stringify(value);
					if (/^[\{\[]/.test(result)) {
						value = result;
					}
				} catch (e) {}

				if (!converter.write) {
					value = encodeURIComponent(String(value))
						.replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);
				} else {
					value = converter.write(value, key);
				}

				key = encodeURIComponent(String(key));
				key = key.replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent);
				key = key.replace(/[\(\)]/g, escape);

				return (document.cookie = [
					key, '=', value,
					attributes.expires && '; expires=' + attributes.expires.toUTCString(), // use expires attribute, max-age is not supported by IE
					attributes.path    && '; path=' + attributes.path,
					attributes.domain  && '; domain=' + attributes.domain,
					attributes.secure ? '; secure' : ''
				].join(''));
			}

			// Read

			if (!key) {
				result = {};
			}

			// To prevent the for loop in the first place assign an empty array
			// in case there are no cookies at all. Also prevents odd result when
			// calling "get()"
			var cookies = document.cookie ? document.cookie.split('; ') : [];
			var rdecode = /(%[0-9A-Z]{2})+/g;
			var i = 0;

			for (; i < cookies.length; i++) {
				var parts = cookies[i].split('=');
				var name = parts[0].replace(rdecode, decodeURIComponent);
				var cookie = parts.slice(1).join('=');

				if (cookie.charAt(0) === '"') {
					cookie = cookie.slice(1, -1);
				}

				try {
					cookie = converter.read ?
						converter.read(cookie, name) : converter(cookie, name) ||
						cookie.replace(rdecode, decodeURIComponent);

					if (this.json) {
						try {
							cookie = JSON.parse(cookie);
						} catch (e) {}
					}

					if (key === name) {
						result = cookie;
						break;
					}

					if (!key) {
						result[name] = cookie;
					}
				} catch (e) {}
			}

			return result;
		}

		api.set = api;
		api.get = function (key) {
			return api(key);
		};
		api.getJSON = function () {
			return api.apply({
				json: true
			}, [].slice.call(arguments));
		};
		api.defaults = {};

		api.remove = function (key, attributes) {
			api(key, '', extend(attributes, {
				expires: -1
			}));
		};

		api.withConverter = init;

		return api;
	}

	return init(function () {});
}));
!function(a){var b=new Array,c=new Array;a.fn.doAutosize=function(b){var c=a(this).data("minwidth"),d=a(this).data("maxwidth"),e="",f=a(this),g=a("#"+a(this).data("tester_id"));if(e!==(e=f.val())){var h=e.replace(/&/g,"&amp;").replace(/\s/g," ").replace(/</g,"&lt;").replace(/>/g,"&gt;");g.html(h);var i=g.width(),j=i+b.comfortZone>=c?i+b.comfortZone:c,k=f.width(),l=k>j&&j>=c||j>c&&d>j;l&&f.width(j)}},a.fn.resetAutosize=function(b){var c=a(this).data("minwidth")||b.minInputWidth||a(this).width(),d=a(this).data("maxwidth")||b.maxInputWidth||a(this).closest(".tagsinput").width()-b.inputPadding,e=a(this),f=a("<tester/>").css({position:"absolute",top:-9999,left:-9999,width:"auto",fontSize:e.css("fontSize"),fontFamily:e.css("fontFamily"),fontWeight:e.css("fontWeight"),letterSpacing:e.css("letterSpacing"),whiteSpace:"nowrap"}),g=a(this).attr("id")+"_autosize_tester";!a("#"+g).length>0&&(f.attr("id",g),f.appendTo("body")),e.data("minwidth",c),e.data("maxwidth",d),e.data("tester_id",g),e.css("width",c)},a.fn.addTag=function(d,e){return e=jQuery.extend({focus:!1,callback:!0},e),this.each(function(){var f=a(this).attr("id"),g=a(this).val().split(b[f]);if(""==g[0]&&(g=new Array),d=jQuery.trim(d),e.unique){var h=a(this).tagExist(d);1==h&&a("#"+f+"_tag").addClass("not_valid")}else var h=!1;if(""!=d&&1!=h){if(a("<span>").addClass("tag").append(a("<span>").text(d).append("&nbsp;&nbsp;"),a("<a>",{href:"#",title:"Removing tag",text:"x"}).click(function(){return a("#"+f).removeTag(escape(d))})).insertBefore("#"+f+"_addTag"),g.push(d),a("#"+f+"_tag").val(""),e.focus?a("#"+f+"_tag").focus():a("#"+f+"_tag").blur(),a.fn.tagsInput.updateTagsField(this,g),e.callback&&c[f]&&c[f].onAddTag){var i=c[f].onAddTag;i.call(this,d)}if(c[f]&&c[f].onChange){var j=g.length,i=c[f].onChange;i.call(this,a(this),g[j-1])}}}),!1},a.fn.removeTag=function(d){return d=unescape(d),this.each(function(){var e=a(this).attr("id"),f=a(this).val().split(b[e]);for(a("#"+e+"_tagsinput .tag").remove(),str="",i=0;i<f.length;i++)f[i]!=d&&(str=str+b[e]+f[i]);if(a.fn.tagsInput.importTags(this,str),c[e]&&c[e].onRemoveTag){var g=c[e].onRemoveTag;g.call(this,d)}}),!1},a.fn.tagExist=function(c){var d=a(this).attr("id"),e=a(this).val().split(b[d]);return jQuery.inArray(c,e)>=0},a.fn.importTags=function(b){var c=a(this).attr("id");a("#"+c+"_tagsinput .tag").remove(),a.fn.tagsInput.importTags(this,b)},a.fn.tagsInput=function(e){var f=jQuery.extend({interactive:!0,defaultText:"add a tag",minChars:0,width:"300px",height:"100px",autocomplete:{selectFirst:!1},hide:!0,delimiter:",",unique:!0,removeWithBackspace:!0,placeholderColor:"#666666",autosize:!0,comfortZone:20,inputPadding:12},e),g=0;return this.each(function(){if("undefined"==typeof a(this).attr("data-tagsinput-init")){a(this).attr("data-tagsinput-init",!0),f.hide&&a(this).hide();var e=a(this).attr("id");(!e||b[a(this).attr("id")])&&(e=a(this).attr("id","tags"+(new Date).getTime()+g++).attr("id"));var h=jQuery.extend({pid:e,real_input:"#"+e,holder:"#"+e+"_tagsinput",input_wrapper:"#"+e+"_addTag",fake_input:"#"+e+"_tag"},f);b[e]=h.delimiter,(f.onAddTag||f.onRemoveTag||f.onChange)&&(c[e]=new Array,c[e].onAddTag=f.onAddTag,c[e].onRemoveTag=f.onRemoveTag,c[e].onChange=f.onChange);var i='<div id="'+e+'_tagsinput" class="tagsinput"><div id="'+e+'_addTag">';if(f.interactive&&(i=i+'<input id="'+e+'_tag" value="" data-default="'+f.defaultText+'" />'),i+='</div><div class="tags_clear"></div></div>',a(i).insertAfter(this),a(h.holder).css("width",f.width),a(h.holder).css("min-height",f.height),a(h.holder).css("height",f.height),""!=a(h.real_input).val()&&a.fn.tagsInput.importTags(a(h.real_input),a(h.real_input).val()),f.interactive){if(a(h.fake_input).val(a(h.fake_input).attr("data-default")),a(h.fake_input).css("color",f.placeholderColor),a(h.fake_input).resetAutosize(f),a(h.holder).bind("click",h,function(b){a(b.data.fake_input).focus()}),a(h.fake_input).bind("focus",h,function(b){a(b.data.fake_input).val()==a(b.data.fake_input).attr("data-default")&&a(b.data.fake_input).val(""),a(b.data.fake_input).css("color","#000000")}),void 0!=f.autocomplete_url){autocomplete_options={source:f.autocomplete_url};for(attrname in f.autocomplete)autocomplete_options[attrname]=f.autocomplete[attrname];void 0!==jQuery.Autocompleter?(a(h.fake_input).autocomplete(f.autocomplete_url,f.autocomplete),a(h.fake_input).bind("result",h,function(b,c,d){c&&a("#"+e).addTag(c[0]+"",{focus:!0,unique:f.unique})})):void 0!==jQuery.ui.autocomplete&&(a(h.fake_input).autocomplete(autocomplete_options),a(h.fake_input).bind("autocompleteselect",h,function(b,c){return a(b.data.real_input).addTag(c.item.value,{focus:!0,unique:f.unique}),!1}))}else a(h.fake_input).bind("blur",h,function(b){var c=a(this).attr("data-default");return""!=a(b.data.fake_input).val()&&a(b.data.fake_input).val()!=c?b.data.minChars<=a(b.data.fake_input).val().length&&(!b.data.maxChars||b.data.maxChars>=a(b.data.fake_input).val().length)&&a(b.data.real_input).addTag(a(b.data.fake_input).val(),{focus:!0,unique:f.unique}):(a(b.data.fake_input).val(a(b.data.fake_input).attr("data-default")),a(b.data.fake_input).css("color",f.placeholderColor)),!1});a(h.fake_input).bind("keypress",h,function(b){return d(b)?(b.preventDefault(),b.data.minChars<=a(b.data.fake_input).val().length&&(!b.data.maxChars||b.data.maxChars>=a(b.data.fake_input).val().length)&&a(b.data.real_input).addTag(a(b.data.fake_input).val(),{focus:!0,unique:f.unique}),a(b.data.fake_input).resetAutosize(f),!1):void(b.data.autosize&&a(b.data.fake_input).doAutosize(f))}),h.removeWithBackspace&&a(h.fake_input).bind("keydown",function(b){if(8==b.keyCode&&""==a(this).val()){b.preventDefault();var c=a(this).closest(".tagsinput").find(".tag:last").text(),d=a(this).attr("id").replace(/_tag$/,"");c=c.replace(/[\s]+x$/,""),a("#"+d).removeTag(escape(c)),a(this).trigger("focus")}}),a(h.fake_input).blur(),h.unique&&a(h.fake_input).keydown(function(b){(8==b.keyCode||String.fromCharCode(b.which).match(/\w+|[áéíóúÁÉÍÓÚñÑ,/]+/))&&a(this).removeClass("not_valid")})}}}),this},a.fn.tagsInput.updateTagsField=function(c,d){var e=a(c).attr("id");a(c).val(d.join(b[e]))},a.fn.tagsInput.importTags=function(d,e){a(d).val("");var f=a(d).attr("id"),g=e.split(b[f]);for(i=0;i<g.length;i++)a(d).addTag(g[i],{focus:!1,callback:!1});if(c[f]&&c[f].onChange){var h=c[f].onChange;h.call(d,d,g[i])}};var d=function(b){var c=!1;return 13==b.which?!0:("string"==typeof b.data.delimiter?b.which==b.data.delimiter.charCodeAt(0)&&(c=!0):a.each(b.data.delimiter,function(a,d){b.which==d.charCodeAt(0)&&(c=!0)}),c)}}(jQuery);
/* ========================================================================
 * Bootstrap: collapse.js v3.3.6
 * http://getbootstrap.com/javascript/#collapse
 * ========================================================================
 * Copyright 2011-2015 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // COLLAPSE PUBLIC CLASS DEFINITION
  // ================================

  var Collapse = function (element, options) {
    this.$element      = $(element)
    this.options       = $.extend({}, Collapse.DEFAULTS, options)
    this.$trigger      = $('[data-toggle="collapse"][href="#' + element.id + '"],' +
                           '[data-toggle="collapse"][data-target="#' + element.id + '"]')
    this.transitioning = null

    if (this.options.parent) {
      this.$parent = this.getParent()
    } else {
      this.addAriaAndCollapsedClass(this.$element, this.$trigger)
    }

    if (this.options.toggle) this.toggle()
  }

  Collapse.VERSION  = '3.3.6'

  Collapse.TRANSITION_DURATION = 350

  Collapse.DEFAULTS = {
    toggle: true
  }

  Collapse.prototype.dimension = function () {
    var hasWidth = this.$element.hasClass('width')
    return hasWidth ? 'width' : 'height'
  }

  Collapse.prototype.show = function () {
    if (this.transitioning || this.$element.hasClass('in')) return

    var activesData
    var actives = this.$parent && this.$parent.children('.panel').children('.in, .collapsing')

    if (actives && actives.length) {
      activesData = actives.data('bs.collapse')
      if (activesData && activesData.transitioning) return
    }

    var startEvent = $.Event('show.bs.collapse')
    this.$element.trigger(startEvent)
    if (startEvent.isDefaultPrevented()) return

    if (actives && actives.length) {
      Plugin.call(actives, 'hide')
      activesData || actives.data('bs.collapse', null)
    }

    var dimension = this.dimension()

    this.$element
      .removeClass('collapse')
      .addClass('collapsing')[dimension](0)
      .attr('aria-expanded', true)

    this.$trigger
      .removeClass('collapsed')
      .attr('aria-expanded', true)

    this.transitioning = 1

    var complete = function () {
      this.$element
        .removeClass('collapsing')
        .addClass('collapse in')[dimension]('')
      this.transitioning = 0
      this.$element
        .trigger('shown.bs.collapse')
    }

    if (!$.support.transition) return complete.call(this)

    var scrollSize = $.camelCase(['scroll', dimension].join('-'))

    this.$element
      .one('bsTransitionEnd', $.proxy(complete, this))
      .emulateTransitionEnd(Collapse.TRANSITION_DURATION)[dimension](this.$element[0][scrollSize])
  }

  Collapse.prototype.hide = function () {
    if (this.transitioning || !this.$element.hasClass('in')) return

    var startEvent = $.Event('hide.bs.collapse')
    this.$element.trigger(startEvent)
    if (startEvent.isDefaultPrevented()) return

    var dimension = this.dimension()

    this.$element[dimension](this.$element[dimension]())[0].offsetHeight

    this.$element
      .addClass('collapsing')
      .removeClass('collapse in')
      .attr('aria-expanded', false)

    this.$trigger
      .addClass('collapsed')
      .attr('aria-expanded', false)

    this.transitioning = 1

    var complete = function () {
      this.transitioning = 0
      this.$element
        .removeClass('collapsing')
        .addClass('collapse')
        .trigger('hidden.bs.collapse')
    }

    if (!$.support.transition) return complete.call(this)

    this.$element
      [dimension](0)
      .one('bsTransitionEnd', $.proxy(complete, this))
      .emulateTransitionEnd(Collapse.TRANSITION_DURATION)
  }

  Collapse.prototype.toggle = function () {
    this[this.$element.hasClass('in') ? 'hide' : 'show']()
  }

  Collapse.prototype.getParent = function () {
    return $(this.options.parent)
      .find('[data-toggle="collapse"][data-parent="' + this.options.parent + '"]')
      .each($.proxy(function (i, element) {
        var $element = $(element)
        this.addAriaAndCollapsedClass(getTargetFromTrigger($element), $element)
      }, this))
      .end()
  }

  Collapse.prototype.addAriaAndCollapsedClass = function ($element, $trigger) {
    var isOpen = $element.hasClass('in')

    $element.attr('aria-expanded', isOpen)
    $trigger
      .toggleClass('collapsed', !isOpen)
      .attr('aria-expanded', isOpen)
  }

  function getTargetFromTrigger($trigger) {
    var href
    var target = $trigger.attr('data-target')
      || (href = $trigger.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '') // strip for ie7

    return $(target)
  }


  // COLLAPSE PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.collapse')
      var options = $.extend({}, Collapse.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data && options.toggle && /show|hide/.test(option)) options.toggle = false
      if (!data) $this.data('bs.collapse', (data = new Collapse(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.collapse

  $.fn.collapse             = Plugin
  $.fn.collapse.Constructor = Collapse


  // COLLAPSE NO CONFLICT
  // ====================

  $.fn.collapse.noConflict = function () {
    $.fn.collapse = old
    return this
  }


  // COLLAPSE DATA-API
  // =================

  $(document).on('click.bs.collapse.data-api', '[data-toggle="collapse"]', function (e) {
    var $this   = $(this)

    if (!$this.attr('data-target')) e.preventDefault()

    var $target = getTargetFromTrigger($this)
    var data    = $target.data('bs.collapse')
    var option  = data ? 'toggle' : $this.data()

    Plugin.call($target, option)
  })

}(jQuery);

/* ========================================================================
 * Bootstrap: transition.js v3.3.6
 * http://getbootstrap.com/javascript/#transitions
 * ========================================================================
 * Copyright 2011-2015 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
  // ============================================================

  function transitionEnd() {
    var el = document.createElement('bootstrap')

    var transEndEventNames = {
      WebkitTransition : 'webkitTransitionEnd',
      MozTransition    : 'transitionend',
      OTransition      : 'oTransitionEnd otransitionend',
      transition       : 'transitionend'
    }

    for (var name in transEndEventNames) {
      if (el.style[name] !== undefined) {
        return { end: transEndEventNames[name] }
      }
    }

    return false // explicit for ie8 (  ._.)
  }

  // http://blog.alexmaccaw.com/css-transitions
  $.fn.emulateTransitionEnd = function (duration) {
    var called = false
    var $el = this
    $(this).one('bsTransitionEnd', function () { called = true })
    var callback = function () { if (!called) $($el).trigger($.support.transition.end) }
    setTimeout(callback, duration)
    return this
  }

  $(function () {
    $.support.transition = transitionEnd()

    if (!$.support.transition) return

    $.event.special.bsTransitionEnd = {
      bindType: $.support.transition.end,
      delegateType: $.support.transition.end,
      handle: function (e) {
        if ($(e.target).is(this)) return e.handleObj.handler.apply(this, arguments)
      }
    }
  })

}(jQuery);

// get form fields as object
jQuery.fn.getFormData = function () {
    var formData = {};
    this.find('[name]').each(function () {
        if(this.type === 'checkbox') {
            formData[this.name] = this.checked ? 'on' : 'off';
            return;
        }

        formData[this.name] = this.value;
    })
    return formData;
};
+jQuery(document).ready(function($) {
    'use strict';

    window.snackbar = {
        htmlElement: null,

        show: function (text, type) {
            if(typeof type === 'undefined') type = 'default';

            this.createHtmlElement();

            this.htmlElement.text(text);
            this.htmlElement.addClass('type-' + type);
            this.htmlElement.addClass('show');

            var _this = this;
            setTimeout(function(){ _this.htmlElement.removeClass('show'); }, 3500);
        },

        createHtmlElement: function () {
            if(this.htmlElement != null) return;

            var el = document.createElement('div');
            el.classList.add('wpi-snackbar');

            $('body').append(el);

            this.htmlElement = $(el);
        },

        maybeShowOnInit: function () {
            if(typeof snackbarDataObject === 'undefined') return null;

            window.snackbar.createHtmlElement();

            if(document.readyState === 'complete') {
                window.snackbar.show(snackbarDataObject.message, snackbarDataObject.type);

                return;
            }

            document.addEventListener('readystatechange', (event) => {
                if(document.readyState !== 'complete') {
                    return;
                }

                window.snackbar.show(snackbarDataObject.message, snackbarDataObject.type);
            });
        }
    };

    window.snackbar.maybeShowOnInit();
});
+function ( $ ) {
    'use strict';

    var previous_form_json = '';

    var alreadyInitialized = false;

    var Creator = {

        /**
         * Init
         */
        init: function ( mode ) {
            if(alreadyInitialized) {
                Creator.listeners.switchVariableNumberingPattern();
                Creator.listeners.reenableInputsInModule();
                Creator.listeners.moveModule();

                Creator.functions.rebulidModulesKeys();
                Creator.functions.rebuildTestKeys();
                Creator.functions.enableModulesSave();

                Creator.listeners.changePointsEvent();
                return;
            }

            /** Creator actions **/
            Creator.listeners.enableSelectables();
            Creator.listeners.switchVariableNumberingPattern();

            /** Module actions **/
            Creator.listeners.reenableInputsInModule();
            Creator.listeners.moveModule();
            Creator.listeners.addModule();
            Creator.listeners.addQuestion();
            Creator.listeners.addAnswer();
            Creator.listeners.changePointsEvent();
            Creator.listeners.removeQuestion();
            Creator.listeners.removeAnswer();
            Creator.listeners.setDripValues();

            /** Lesson actions **/
            Creator.listeners.addLesson();

            /** Module / Lesson actions **/
            Creator.listeners.remove();
            Creator.listeners.uploadFiles();
            Creator.listeners.changeDripUnit();
            /** + Search Pages functions **/
            Creator.listeners.removePage();


            /** Modal (content editor) actions **/
            Creator.listeners.openModal();
            Creator.listeners.closeModal();

            Creator.listeners.triggerUnsavedDataWarning();

            Creator.listeners.quizQuestionActions();

            Creator.functions.rebulidModulesKeys();
            Creator.functions.rebuildTestKeys();
            Creator.functions.enableModulesSave();
            Creator.saving = false;
            Creator.bundleMode = (mode === 'bundle');
            Creator.mode = mode;

            alreadyInitialized = true;
        },

        /**
         * Creator listeners
         */
        listeners: {
            // Move module
            moveModule: function () {

                $( '#bpmj_eddcm_modules_list' ).sortable( {
                    cursor: "move",
                    connectWith: '.module-submodules',
                    placeholder: 'eddcm-sortable-placeholder',
                    stop: function ( event, ui ) {
                        Creator.functions.rebulidModulesKeys();
                        window.wpi_tab_save_data_checker.addUnsavedData('course_structure')
                    }
                } ).disableSelection();

                $( '.module-submodules' ).sortable( {
                    cursor: "move",
                    connectWith: '#bpmj_eddcm_modules_list, .module-submodules',
                    stop: function ( event, ui ) {
                        Creator.functions.rebulidModulesKeys();
                        window.wpi_tab_save_data_checker.addUnsavedData('course_structure')
                    },
                    receive: function ( event, ui ) {
                        if ( ui.item.hasClass( 'full' ) )
                            ui.sender.sortable( 'cancel' );
                    }
                } ).disableSelection();


                $( '#bpmj_eddcm_questions_list.modules' ).sortable( {
                    cursor: "move",
                    handle: ".question-header",
                    stop: function ( event, ui ) {
                        Creator.functions.rebuildTestKeys();
                        window.wpi_tab_save_data_checker.addUnsavedData('course_structure')
                    }
                } );

                $( '.answers' ).sortable( {
                    cursor: "move",
                    connectWith: '.answers',
                    items: "li:not(.add-answer)",
                    stop: function ( event, ui ) {
                        Creator.functions.rebuildTestKeys();
                        window.wpi_tab_save_data_checker.addUnsavedData('course_structure')
                    }
                } );

            },

            switchVariableNumberingPattern: function () {

                let enable_certificate_numbering = $( '#enable-certificate-numbering' ).is( ':checked' );

                let lock_post_saving = function() {
                    wp.data.dispatch('core/editor').lockPostSaving('empty_certyficate_number');
                }

                let unlock_post_saving = function() {
                    wp.data.dispatch( 'core/editor' ).unlockPostSaving( 'empty_certyficate_number' );
                }

                let checking_certificate_numbering_pattern = function() {

                    let pattern = "[a-zA-Z//0-1 ]*X[a-zA-Z //]*";

                    $(document).on('keyup', '#certificate_numbering_pattern', function () {

                        if(!preg_match(pattern, $(this).val())){
                            lock_post_saving();
                            $(this).addClass('border-error-red');
                            $( '#error-numbering-pattern' ).removeClass('hidden-error');
                        }else{
                            unlock_post_saving();
                            $(this).removeClass('border-error-red');
                            $( '#error-numbering-pattern' ).addClass('hidden-error');

                        }

                    });

                };

                if(enable_certificate_numbering){
                    checking_certificate_numbering_pattern();
                }

                $( document ).on( 'click', '#enable-certificate-numbering', function () {

                    let checked = $( this ).is( ':checked' );

                    if ( checked ) {
                        let certificate_numbering_pattern = $( '#certificate_numbering_pattern' ).val();

                        $( '#numbering-pattern' ).show();

                        if(!certificate_numbering_pattern) {
                            lock_post_saving();
                        }

                        checking_certificate_numbering_pattern();

                    } else {
                        $( '#numbering-pattern' ).hide();
                        $( '#certificate_numbering_pattern' ).val('');
                        unlock_post_saving();
                    }
                } );
            },

            // Remove module / lesson
            remove: function () {
                $( document ).on( 'click', '[data-action="remove"]', function ( e ) {
                    e.preventDefault();

                    var modal_id = $( this ).parent().find( '[data-action="open-modal"]' ).data( 'id' );
                    $( document ).find( '.bpmj-eddcm-modal[data-modal="' + modal_id + '"]' ).remove();
                    $( 'html, body' ).css( 'overflow', 'visible' );

                    if ( $( this ).parent().find( '.lessons li.lesson' ).length ) {
                        $( this ).parent().find( '.lesson' ).each( function () {
                            var modal_id = $( this ).find( '[data-action="open-modal"]' ).data( 'id' );
                            $( document ).find( '.bpmj-eddcm-modal[data-modal="' + modal_id + '"]' ).remove();
                            $( 'html, body' ).css( 'overflow', 'visible' );
                        } );
                    }

                    $( this ).parent().remove();
                    Creator.functions.rebulidModulesKeys();

                    window.wpi_tab_save_data_checker.addUnsavedData('course_structure')
                } );
            },

            /**
             * Dodawanie modułów
             * wersja tymczasowa
             */
            addModule: function () {
                $( document ).on( 'click', '[data-action="add-module"]', function ( e ) {
                    e.preventDefault();

                    // Spinner
                    Creator.functions.removeSearchPages();
                    $( this ).html( '<span class="dashicons dashicons-update"></span>' );

                    // HTML Modułu
                    var mode = $( this ).data( 'mode' ),
                        html = '',
                        placeholder;

                    if ( mode == 'full' ) {
                        placeholder = bpmj_eddcm.creator.placeholder_module;
                    } else if ( mode == 'test' ) {
                        placeholder = bpmj_eddcm.creator.placeholder_test;
                    } else {
                        placeholder = bpmj_eddcm.creator.placeholder_lesson;
                    }

                    html += $( '#bpmj_eddcm_new_module_' + mode + '_template' ).html().replace( /id="eddcm_title"/, 'id="eddcm_title" class="focus-me"' );

                    var module_modules = $( this ).parent( '.creator-buttons' ).prev( '.modules' );
                    if ( module_modules.length > 0 ) {
                        module_modules.append( html );
                        module_modules.sortable( {
                            cursor: "move",
                            connectWith: '#bpmj_eddcm_modules_list, .module-submodules',
                            stop: function ( event, ui ) {
                                Creator.functions.rebulidModulesKeys();
                            },
                            receive: function ( event, ui ) {
                                if ( ui.item.hasClass( 'full' ) )
                                    ui.sender.sortable( 'cancel' );
                            }
                        } ).disableSelection();
                    } else {
                        $( '#bpmj_eddcm_modules_list' ).append( html )
                            .find( '.module-submodules' ).sortable( {
                            cursor: "move",
                            connectWith: '#bpmj_eddcm_modules_list, .module-submodules',
                            stop: function ( event, ui ) {
                                Creator.functions.rebulidModulesKeys();
                            },
                            receive: function ( event, ui ) {
                                if ( ui.item.hasClass( 'full' ) )
                                    ui.sender.sortable( 'cancel' );
                            }
                        } ).disableSelection();
                    }

                    $( '.modules .focus-me' ).removeClass( 'focus-me' ).focus();

                    // Dodatkowe funkcje
                    Creator.functions.rebulidModulesKeys();

                    var $new_module = $( module_modules ).find( '.module:last' ).length ? $( module_modules ).find( '.module:last' ) : $( '#bpmj_eddcm_modules_list .module:last' );

                    $new_module.find( '> .drip_unit_label' ).each( function () {
                        $( this ).text( $( ".drip-unit-field option:selected" ).text() );
                    } );
                    $new_module.find( '> input[name$="[drip_unit]"]' ).each( function () {
                        $( this ).val( $( '#bpmj_eddcm_drip_unit' ).val() );
                    } );
                    $new_module.find( '> input#eddcm_title' ).each( function () {
                        $( this ).trigger('change')
                    } );
                    Creator.functions.autodrip( $new_module.get( 0 ) );
                    Creator.listeners.reenableInputsInModule();

                    if ( mode == 'full' ) {
                        $( this ).html( bpmj_eddcm.creator.add_module );
                    } else if ( mode == 'test' ) {
                        $( this ).html( bpmj_eddcm.creator.add_test );
                    } else {
                        $( this ).html( bpmj_eddcm.creator.add_lesson );
                    }
                } );
            },
            addQuestion: function () {
                $( document ).on( 'click', '[data-action="add-question"]', function ( e ) {
                    e.preventDefault();

                    // Spinner
                    Creator.functions.removeSearchPages();
                    $( this ).html( '<span class="dashicons dashicons-update"></span>' );

                    var html = '';

                    html += $( '#bpmj_eddcm_new_test_question_template' ).html().replace( /id="eddcm_title"/, 'id="eddcm_title" class="focus-me"' );

                    $( '#bpmj_eddcm_questions_list' ).append( html );
                    $( '#bpmj_eddcm_questions_list .focus-me' ).removeClass( 'focus-me' ).focus();

                    $( '.eddcm-test-question-type.template' ).removeClass( 'template' )
                        .on( 'change', Creator.listeners.changeQuestionTypeEvent );

                    // Dodatkowe funkcje
                    Creator.functions.rebuildTestKeys();

                    $( this ).html( bpmj_eddcm.creator.add_question );
                } );
            },

            addAnswer: function () {
                $( document ).on( 'click', '[data-action="add-answer"]', function ( e ) {
                    e.preventDefault();

                    switch ( $( this ).data( 'type' ) ) {
                        case 'single':
                        case 'multi':
                            var html = $( '#bpmj_eddcm_new_test_question_single_answer_template' ).html();
                            $( this ).before( html );
                            break;
                    }

                    if ( 'single' === $( this ).data( 'type' ) ) {

                    }

                    Creator.functions.rebuildTestKeys();
                } );
            },

            changePointsEvent: function () {
                if(alreadyInitialized) {
                    return;
                }

                $( document ).on( 'change', '.points-value', Creator.functions.recalculateQuizPoints);

            },

            quizQuestionActions: function () {
                $( document ).on( 'click', '.question__show_comment_field_button', function (e) {
                    $( this ).closest( '.question-header' )
                        .next( '.question-body' )
                        .find( '.question-comment-field-wrapper' )
                        .slideToggle();
                });
            },

            changeQuestionTypeEvent: function () {
                var value = $( this ).val();

                $( this ).closest( '.question-header' )
                    .next( '.question-body' )
                    .find( '.question-type-tab' )
                    .css( 'display', 'none' );

                switch ( value ) {
                    case 'single_radio':
                    case 'single_select':
                    case 'multiple':
                        $( this ).closest( '.question-header' )
                            .next( '.question-body' )
                            .find( '.question-type-single-answer-tab, .question-type-single-tab' )
                            .css( 'display', 'block' );
                        break;
                    case 'text':
                        $( this ).closest( '.question-header' )
                            .next( '.question-body' )
                            .find( '.question-type-text-answer-tab, .question-type-text-tab' )
                            .css( 'display', 'block' );
                        break;
                    case 'file':
                        $( this ).closest( '.question-header' )
                            .next( '.question-body' )
                            .find( '.question-type-file-answer-tab, .question-type-file-tab' )
                            .css( 'display', 'block' );
                        break;
                }

                $( '.question-type-text-answer-tab textarea' ).each( function () {
                    $( this ).height( 0 ).height( this.scrollHeight );
                } );

                Creator.functions.recalculateQuizPoints();
            },

            removeQuestion: function () {
                $( document ).on( 'click', '[data-action="remove-question"]', function ( e ) {
                    e.preventDefault();

                    $( this ).closest( '.module.question' )
                        .remove();

                    Creator.functions.rebuildTestKeys();
                    Creator.functions.recalculateQuizPoints();
                } );
            },

            removeAnswer: function () {
                $( document ).on( 'click', '[data-action="remove-answer"]', function ( e ) {
                    e.preventDefault();

                    $( this ).closest( '.answer' )
                        .remove();

                    Creator.functions.rebuildTestKeys();
                    Creator.functions.recalculateQuizPoints();
                } );
            },

            /**
             * Umożliwiamy z powrotem korzystanie z inputów
             * w nazwach modułów
             */
            reenableInputsInModule: function () {
                $( '.modules input:not(.drag-fixed)' ).addClass( 'drag-fixed' ).mousedown( function ( event ) {
                    event.stopPropagation();
                } );
            },

            setDripValues: function () {
                $( document ).on( 'click', '[data-action="set-drip-value"]', function ( e ) {
                    e.preventDefault();
                    let drip_value = parseInt( jQuery( '.drip-value-field' ).val() );
                    $( '#bpmj_eddcm_drip_value' ).val(drip_value).trigger('change');
                    let current_drip_value = 0;
                    $( '#bpmj_eddcm_modules_list .module, #bpmj_eddcm_modules_list .lesson' ).each( function () {
                        $( this ).find( '> input[name$="[drip_value]"]' ).val( drip_value && current_drip_value ? current_drip_value : '0' );
                        if ( !$( this ).hasClass( 'full' ) ) {
                            current_drip_value += drip_value;
                        }
                    } );
                } );
            },

            /**
             * Dodawanie lekcji
             * wersja tymczasowa
             */
            addLesson: function () {
                $( document ).on( 'click', '[data-action="add-lesson"]', function ( e ) {
                    e.preventDefault();

                    // HTML Lekcji
                    var html = '';

                    html += $( '#bpmj_eddcm_new_lesson_template' ).html().replace( /id="eddcm_title"/, 'id="eddcm_title" class="focus-me"' );

                    // Dodatkowe funkcje
                    $( this ).before( html );
                    $( '.modules .focus-me' ).removeClass( 'focus-me' ).focus();
                    Creator.functions.rebulidModulesKeys();

                    var $new_lesson = $( this ).prev( '.lesson' );
                    $new_lesson.find( '> .drip_unit_label' ).each( function () {
                        $( this ).text( $( '#bpmj_eddcm_drip_unit_label' ).text() );
                    } );
                    $new_lesson.find( '> input[name$="[drip_unit]"]' ).each( function () {
                        $( this ).val( $( '#bpmj_eddcm_drip_unit' ).val() );
                    } );
                    $new_lesson.find( '> input#eddcm_title' ).each( function () {
                        $( this ).trigger('change')
                    } );
                    Creator.functions.autodrip( $new_lesson.get( 0 ) );
                    Creator.listeners.reenableInputsInModule();

                } );
            },

            // Open modal with content editor
            openModal: function () {
                $( document ).on( 'click', '[data-action="open-modal"]', function ( e ) {
                    e.preventDefault();
                    if ( !$( this ).hasClass( 'disabled' ) ) {
                        var id = $( this ).data( 'id' );
                        tinymce.execCommand( 'mceAddEditor', true, 'content_' + id );
                        $( '.bpmj-eddcm-modal[data-modal="' + id + '"]' ).css( 'display', 'flex' ).css( 'opacity', '1' );
                    }
                } );
            },

            // Close modal with content editor
            closeModal: function () {
                $( document ).on( 'click', '[data-action="close-modal"]', function ( e ) {
                    e.preventDefault();
                    var id = $( this ).data( 'id' );
                    $( '.bpmj-eddcm-modal[data-modal="' + id + '"]' ).remove();
                    $( 'html, body' ).css( 'overflow', 'visible' );
                } );

                $( document ).on( 'click', '.bpmj-eddcm-modal', function ( e ) {
                    if ( e.target != this )
                        return;

                    var id = $( this ).find( '[data-action="close-modal"]' ).data( 'id' );
                    $( '.bpmj-eddcm-modal[data-modal="' + id + '"]' ).remove();
                    $( 'html, body' ).css( 'overflow', 'visible' );
                } );

                $( document ).on( 'click', '.bpmj-eddcm-modal > .row', function ( e ) {
                    if ( e.target != this )
                        return;

                    var id = $( this ).parent().find( '[data-action="close-modal"]' ).data( 'id' );
                    $( '.bpmj-eddcm-modal[data-modal="' + id + '"]' ).remove();
                    $( 'html, body' ).css( 'overflow', 'visible' );
                } );
                $( document ).on( 'click', '.bpmj-eddcm-modal > .row > .container', function ( e ) {
                    if ( e.target != this )
                        return;

                    var id = $( this ).parent().parent().find( '[data-action="close-modal"]' ).data( 'id' );
                    $( '.bpmj-eddcm-modal[data-modal="' + id + '"]' ).remove();
                    $( 'html, body' ).css( 'overflow', 'visible' );
                } );
            },

            // Remove page
            removePage: function () {
                $( document ).on( 'click', '[data-action="remove-page"]', function ( e ) {
                    e.preventDefault();
                    var container = $( this ).parent();

                    container.find( '#eddcm_id' ).remove();
                    $( this ).remove();

                    container.find( '#eddcm_title' ).removeClass( 'choosen' ).attr( 'readonly', false ).val( '' ).focus();
                } );
            },

            // Upload files
            uploadFiles: function () {

                // Set all variables to be used in scope
                var frame;

                // ADD FILES
                $( document ).on( 'click', '#uploadFiles', function ( event ) {
                    event.preventDefault();

                    // Files upload container
                    var container = $( this ).parent(),
                        files = container.find( '.wp-idea-files' );


                    // If the media frame already exists, reopen it.
                    if ( frame ) {
                        frame.open();
                        return;
                    }

                    // Create a new media frame
                    frame = wp.media( {
                        title: 'Select or Upload Media Of Your Chosen Persuasion',
                        button: {
                            text: 'Use this media'
                        },
                        multiple: true
                    } );


                    // When an image is selected in the media frame...
                    frame.on( 'select', function () {

                        // Get media attachment details from the frame state
                        var attachment = frame.state().get( 'selection' ).toJSON(),
                            file,
                            style;

                        if ( $.isArray( attachment ) ) {
                            for ( var i = 0, len = attachment.length; i < len; i++ ) {
                                if ( !container.find( '.file[data-id="' + attachment[i].id + '"]' ).length ) {
                                    console.log( attachment[i] );

                                    if ( attachment[i].type == 'image' ) {
                                        style = 'background-image: url(' + attachment[i].url + '); background-size: cover; background-position: center center';
                                    } else {
                                        style = 'background-image: url(' + attachment[i].icon + '); background-position: center 5px;';
                                    }

                                    file = '<div class="file" data-id="' + attachment[i].id + '" style="' + style + '">';
                                    file += '<span class="dashicons dashicons-no remove-file"></span>';

                                    if ( attachment[i].type != 'image' ) {
                                        file += '<span class="title">' + attachment[i].filename + '</span>';
                                    }

                                    file += '<input type="text" id="files" name="bpmj_wpidea[files][' + attachment[i].id + '][desc]" placeholder="Short file description">';
                                    file += '</div>';

                                    files.append( file );
                                    //console.log( file + "     " + files);
                                }
                            }
                            Creator.functions.rebulidModulesKeys();
                        }
                    } );

                    // Finally, open the modal on click
                    frame.open();
                } );


                // Remove files
                $( document ).on( 'click', '.remove-file', function ( event ) {
                    event.preventDefault();
                    $( this ).parent().remove();
                } );
            },
            enableSelectables: function () {
                $( document ).on( 'click', '.selectable', function ( e ) {
                    var input = $( this ).find( 'input[type="checkbox"]' ).get( 0 );
                    if ( input && input !== e.target ) {
                        input.checked = !input.checked;
                    }
                    if ( input.checked ) {
                        $( this ).addClass( 'selected' );
                    } else {
                        $( this ).removeClass( 'selected' );
                    }
                    if ( Creator.bundleMode ) {
                        Creator.functions.updateBundleInfo();
                    }
                } );
            },
            changeDripUnit: function () {
                $( document ).on( 'click', '[data-action="change-drip-unit-do"]', function ( e ) {
                    e.preventDefault();
                    let unit_value = $( '.drip-unit-field' ).val();
                    let unit_label = $( ".drip-unit-field option:selected" ).text();

                    $( '#bpmj_eddcm_drip_unit' ).val( unit_value );
                    $( '#bpmj_eddcm_modules_list .drip_unit_label' ).each( function () {
                        $( this ).text( unit_label );
                    } );
                    $( '#bpmj_eddcm_modules_list input[name$="[drip_unit]"]' ).each( function () {
                        $( this ).val( unit_value ).trigger('change');
                    } );
                } );
            },

            triggerUnsavedDataWarning() {
                $( document ).on( 'change', '#form-course-structure :input', function ( e ) {
                    window.wpi_tab_save_data_checker.addUnsavedData('course_structure');
                } );
            }
        }, // Listeners END



        /**
         * Creator functions
         */
        functions: {
            // Rebulid test keys
            rebuildTestKeys: function () {
                var i = 0;
                $( '.modules li.module.question' ).each( function () {
                    if ( !$( this ).hasClass( 'ui-sortable-placeholder' ) ) {

                        $( this ).attr( 'data-question', i );

                        $( this ).find( '.eddcm-test-question-id' ).attr( 'name', 'bpmj_eddcm_test_questions[' + i + '][id]' )
                            .attr( 'value', i );
                        $( this ).find( '.eddcm-test-question-title' ).attr( 'name', 'bpmj_eddcm_test_questions[' + i + '][title]' );
                        $( this ).find( '.eddcm-test-question-type' ).attr( 'name', 'bpmj_eddcm_test_questions[' + i + '][type]' );
                        $( this ).find( '.eddcm-test-question-question_comment' ).attr( 'name', 'bpmj_eddcm_test_questions[' + i + '][question_comment]' );

                        if ( $( this ).find( '.answers' ).length ) {
                            var j = 0;

                            $( this ).find( '.answer' ).each( function () {
                                $( this ).find( '.eddcm-test-question-answer-id' ).attr( 'name', 'bpmj_eddcm_test_questions[' + i + '][answer][' + j + '][id]' )
                                    .attr( 'value', j );
                                $( this ).find( '.eddcm-test-question-answer-title' ).attr( 'name', 'bpmj_eddcm_test_questions[' + i + '][answer][' + j + '][title]' );
                                $( this ).find( '.eddcm-test-question-answer-points' ).attr( 'name', 'bpmj_eddcm_test_questions[' + i + '][answer][' + j + '][points]' );

                                j++;
                            } );
                        }

                        i++;
                    }
                } );
            },

            // Rebulid modules keys
            rebulidModulesKeys: function () {
                var i = 0;

                // Keys for every single module
                $( '#bpmj_eddcm_modules_list > li.module' ).not( '.question' ).each( function () {
                    if ( !$( this ).hasClass( 'ui-sortable-placeholder' ) ) {
                        $( this ).attr( 'data-module', i );
                        $( this ).find( '> input#eddcm_id' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][id]' );
                        $( this ).find( '> input#eddcm_created_id' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][created_id]' );
                        $( this ).find( '> input#eddcm_cloned_from_id' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][cloned_from_id]' );
                        $( this ).find( '> input#eddcm_title' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][title]' );
                        $( this ).find( '> input#eddcm_mode' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][mode]' );
                        $( this ).find( '> input.drip_value' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][drip_value]' );
                        $( this ).find( '> input[name$="[drip_unit]"]' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][drip_unit]' );
                        $( this ).find( '> .variable-prices input[name$="[variable_prices][]"]' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][variable_prices][]' );

                        var modal_id = $( this ).find( '[data-action="open-modal"]' ).data( 'id' );
                        $( document ).find( '.bpmj-eddcm-modal[data-modal="' + modal_id + '"] textarea' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][content]' );
                        $( document ).find( '.bpmj-eddcm-modal[data-modal="' + modal_id + '"] #subtitle' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][subtitle]' );
                        $( document ).find( '.bpmj-eddcm-modal[data-modal="' + modal_id + '"] #level' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][level]' );
                        $( document ).find( '.bpmj-eddcm-modal[data-modal="' + modal_id + '"] #duration' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][duration]' );
                        $( document ).find( '.bpmj-eddcm-modal[data-modal="' + modal_id + '"] #shortdesc' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][shortdesc]' );

                        $( document ).find( '.bpmj-eddcm-modal[data-modal="' + modal_id + '"] #files' ).each( function () {
                            $( this ).attr( 'name', 'bpmj_eddcm_module[' + i + '][files][' + $( this ).data( 'id' ) + '][desc]' );
                        } );

                        // Keys for every single lessons
                        if ( $( this ).find( '.modules li.module' ).length ) {
                            var l = 0;
                            $( this ).find( '.module' ).each( function () {
                                $( this ).attr( 'data-module', i );
                                $( this ).attr( 'data-submodule', l );
                                $( this ).find( '> input#eddcm_id' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][module][' + l + '][id]' );
                                $( this ).find( '> input#eddcm_created_id' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][module][' + l + '][created_id]' );
                                $( this ).find( '> input#eddcm_cloned_from_id' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][module][' + l + '][cloned_from_id]' );
                                $( this ).find( '> input#eddcm_title' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][module][' + l + '][title]' );
                                $( this ).find( '> input#eddcm_mode' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][module][' + l + '][mode]' );
                                $( this ).find( '> input.drip_value' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][module][' + l + '][drip_value]' );
                                $( this ).find( '> input[name$="[drip_unit]"]' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][module][' + l + '][drip_unit]' );
                                $( this ).find( '> .variable-prices input[name$="[variable_prices][]"]' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][module][' + l + '][variable_prices][]' );

                                var modal_id = $( this ).find( '[data-action="open-modal"]' ).data( 'id' );
                                $( document ).find( '.bpmj-eddcm-modal[data-modal="' + modal_id + '"] textarea' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][module][' + l + '][content]' );
                                $( document ).find( '.bpmj-eddcm-modal[data-modal="' + modal_id + '"] #subtitle' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][module][' + l + '][subtitle]' );
                                $( document ).find( '.bpmj-eddcm-modal[data-modal="' + modal_id + '"] #level' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][module][' + l + '][level]' );
                                $( document ).find( '.bpmj-eddcm-modal[data-modal="' + modal_id + '"] #duration' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][module][' + l + '][duration]' );
                                $( document ).find( '.bpmj-eddcm-modal[data-modal="' + modal_id + '"] #shortdesc' ).attr( 'name', 'bpmj_eddcm_module[' + i + '][module][' + l + '][shortdesc]' );

                                $( document ).find( '.bpmj-eddcm-modal[data-modal="' + modal_id + '"] #files' ).each( function () {
                                    $( this ).attr( 'name', 'bpmj_eddcm_module[' + i + '][module][' + l + '][files][' + $( this ).data( 'id' ) + '][desc]' );
                                } );

                                l++;
                            } );
                        }
                        i++;
                    }
                } );
            },

            // Modal
            modal: function ( id, heading, content, classes, close, modal_classes ) {

                $( 'html, body' ).css( 'overflow', 'hidden' );

                var html = '<div class="modal bpmj-eddcm-modal ' + ( modal_classes || '' ) + '" data-modal="' + id + '"><div class="row"><div class="container"><div class="panel">';
                html += '<div class="panel-heading">' + heading;

                if ( close ) {
                    html += '<span class="dashicons dashicons-no-alt close-modal" data-action="close-modal" data-id="' + id + '"></span>';
                }

                html += '</div>';
                html += '<div class="panel-body ' + ( classes || '' ) + '">' + content + '</div>';
                html += '</div></div></div></div>';

                var $container = $( '.edd-courses-manager-creator-steps-form, #bpmj_eddcm_modules, .edd-courses-manager, .bpmj-eddcm-cs-section-body:visible' );
                if ( 0 === $container.length ) {
                    $container = $( document.body );
                }
                $container.append( html );
                $( '.bpmj-eddcm-modal[data-modal="' + id + '"]' ).fadeIn();
            },

            enableModulesSave: function () {
                $( '#bpmj_eddcm_save_modules' ).val( '1' );
            },

            // Autodrip modules and lessons starting from current element
            autodrip: function ( new_element ) {
                var drip_value = parseInt( jQuery( '#bpmj_eddcm_drip_value' ).val() );
                var current_drip_value = 0;
                var $previous_element = null;
                var autodripping = false;
                if ( !drip_value ) {
                    return;
                }
                $( '#bpmj_eddcm_modules_list .module, #bpmj_eddcm_modules_list .lesson' ).each( function () {
                    if ( autodripping ) {
                        // We are adjusting drip values of modules/lessons below $new_element
                        current_drip_value = parseInt( $( this ).find( '> input[name$="[drip_value]"]' ).val(), 10 );
                        $( this ).find( '> input[name$="[drip_value]"]' ).val( drip_value + (
                            current_drip_value || 0
                        ) );
                    } else if ( new_element === $( this ).get( 0 ) ) {
                        current_drip_value = parseInt( $previous_element.find( '> input[name$="[drip_value]"]' ).val(), 10 );
                        if ( $previous_element.hasClass( 'module' ) && !$( this ).hasClass( 'module' ) ) {
                            // $previous_element is a module, but this one is a lesson in that module
                            // so we assume it's the first lesson in the module
                            $( this ).find( '> input[name$="[drip_value]"]' ).val( current_drip_value || 0 );
                            return false;
                        }
                        // $new_element found - start autodripping
                        autodripping = true;
                        $( this ).find( '> input[name$="[drip_value]"]' ).val(
                            drip_value + (
                                current_drip_value || 0
                            ) );
                    } else {
                        // Capture previous element so its drip_value can be used for $new_element
                        $previous_element = $( this );
                    }
                } );
            },

            // Remove all search-pages containers
            removeSearchPages: function () {
                $( '.search-pages' ).each( function () {
                    $( this ).fadeOut( 'fast', function () {
                        $( this ).remove();
                    } );
                } );
            },

            updateBundleInfo: function () {
                var $question = $( '.question' );
                var selectedCount = $( 'input[name="bundled_courses[]"]:checked' ).length;
                var selectedCountMin = 2;
                var numbers = [ selectedCountMin, selectedCount ];
                $question.html( bpmj_eddcm.creator.bundle_requirement.replace( /\{(\d+)\}/g, function ( m, n ) {
                    return numbers[n];
                } ) );
            },

            getSeeItemListButtonUrl(creatorForm) {
                let data = creatorForm.data('see-item-list-button-url');

                if(typeof data !== 'undefined') {
                    return data;
                }

                return Creator.bundleMode ? bpmj_eddcm.creator.modal_save_bundle.btn1.url : bpmj_eddcm.creator.modal_save.btn1.url;
            },

            getEditItemButtonText(creatorForm) {
                let data = creatorForm.data('edit-item-button-label');

                if(typeof data !== 'undefined') {
                    return data;
                }

                return Creator.bundleMode ? bpmj_eddcm.creator.modal_save_bundle.btn2.title : bpmj_eddcm.creator.modal_save.btn2.title;
            },
            recalculateQuizPoints() {
                var points = 0;

                $( 'select.eddcm-test-question-type' ).each( function () {
                    var question_type = $( this ).val();

                    if ( 'single_radio' === question_type || 'single_select' === question_type ) {
                        var single_points_elements = $( this ).closest( '.module.question' ).find( '.points-value' ),
                            high_score = parseInt( $( single_points_elements ).first().val() );

                        if ( isNaN( high_score ) ) {
                            high_score = 0;
                        }

                        single_points_elements.each( function () {
                            var int_val = parseInt( $( this ).val() );

                            if ( isNaN( int_val ) )
                                int_val = 0;

                            if ( int_val > high_score ) {
                                high_score = int_val;
                            }
                        } );
                        points += high_score;
                    } else if ( 'multiple' === question_type ) {
                        $( this ).closest( '.module.question' ).find( '.points-value' ).each( function () {
                            var int_val = parseInt( $( this ).val() );

                            if ( isNaN( int_val ) || int_val < 0)
                                int_val = 0;

                            points += int_val;
                        } );
                    }
                } );

                $( '#pass-condition-points' ).text( points );
                $( '#pass-condition-points-input' ).attr( 'max', points );
                $( '#pass-condition-points-input-all' ).attr( 'value', points );

                if ($('#pass-condition-points-input').val() > points) {
                    $('#pass-condition-points-input').val(points);
                }
            }
        } // Functions END

    } // Creator END


    $( document ).ready( function () {
        var mode = 'course';
        var init = false;

        // Switch on autosave if in new course creator (unless the creator is in bundle mode)
        if ( $( '.edd-courses-manager-creator-steps-form' ).length ) {
            var modeData = $( '.edd-courses-manager-creator-steps-form' ).data( 'mode' );
            init = true;

            mode = modeData ?? mode;
        }

        if ( $( '#bpmj_eddcm_modules' ).length || $( '#bpmj_eddcm_options' ).length ) {
            init = true;
        }

        // Run creator if form present
        if ( init ) {
            Creator.init( mode );
        } else {
            // We call this listener explicitly because we might want to use modals elsewhere
            Creator.listeners.closeModal();
        }

        $( '.eddcm-test-question-type' ).on( 'change', Creator.listeners.changeQuestionTypeEvent )
            .trigger( 'change' );
    } );

    // function exports
    window.bpmj_eddcm_open_modal = Creator.functions.modal;
    window.bpmj_eddcm_course_structure_creator_init = Creator.init;
    window.bpmj_eddcm_quiz_questions_structure_creator_init = function () {
        Creator.init();

        $( '.eddcm-test-question-type' ).on( 'change', Creator.listeners.changeQuestionTypeEvent )
            .trigger( 'change' );
    }

}( jQuery );

function preg_match (regex, str) {
    return (new RegExp(regex).test(str));
}
+ function ( $ ) {
	'use strict';

	var UsersManager = {

		/**
		 * Init
		 */
		init: function () {
			UsersManager.listeners.setAccessDueDate();
			UsersManager.listeners.setTotalTime();
			UsersManager.listeners.removeUserFromCourse();
			UsersManager.listeners.cancelSubscription();
			UsersManager.listeners.addUserToCourse();
			UsersManager.listeners.showCourseProgressPopup();
		},

		listeners: {
			setAccessDueDate: function () {
				$( document ).on( 'click', '[data-action="set-access-time-popup"]', function ( e ) {
					e.preventDefault();
					var access_time = $( this ).data( 'accessTime' );
					var user_id = $( this ).data( 'userId' );
					var product_id = $( this ).data( 'productId' );
					var course_id = $( this ).data( 'courseId' );
					var html = $( '#bpmj_eddcm_edit_access_time' ).html();

					UsersManager.functions.modal( 'set-access-time', bpmj_eddcm.users_manager.set_access_time_popup.title, html, 'text-center', true );
					var $modal = $( '.bpmj-eddcm-modal[data-modal="set-access-time"]' );
					/**
					 * @type {HTMLFormElement|{
					 *    user_id: HTMLInputElement,
				     *    product_id: HTMLInputElement,
				     *    course_id: HTMLInputElement,
				     *    no_limit: HTMLInputElement,
				     *    access_due_date: HTMLInputElement,
				     *    access_due_hh: HTMLSelectElement,
				     *    access_due_mm: HTMLSelectElement,
					 * }}
					 */
					var form = $modal.find( 'form' ).get( 0 );
					form.user_id.value = user_id;
					form.product_id.value = product_id;
					form.course_id.value = course_id;
					$( form.no_limit ).click( function () {
						$( '#bpmj_eddcm_edit_access_time_details' ).toggle();
					} );
					bpmj_eddcm_enable_datepickers();
					if ( access_time ) {
						form.no_limit.checked = false;
						$( '#bpmj_eddcm_edit_access_time_details' ).show();
						var access_time_date = new Date( access_time * 1000 );
						form.access_due_date.value = [
							access_time_date.getUTCFullYear(),
							UsersManager.functions.zeropad( access_time_date.getUTCMonth() + 1 ),
							UsersManager.functions.zeropad( access_time_date.getUTCDate() )
						].join( '-' );
						form.access_due_hh.value = UsersManager.functions.zeropad( access_time_date.getUTCHours() );
						form.access_due_mm.value = UsersManager.functions.zeropad( access_time_date.getUTCMinutes() );
					} else {
						form.no_limit.checked = true;
						var today = new Date();
						form.access_due_date.value = [
							today.getFullYear(),
							UsersManager.functions.zeropad( today.getMonth() + 1 ),
							UsersManager.functions.zeropad( today.getDate() )
						].join( '-' );
						$( '#bpmj_eddcm_edit_access_time_details' ).hide();
					}
				} );
				$( document ).on( 'click', '[data-action="set-access-time"]', function ( e ) {
					e.preventDefault();
					var $form = $( this ).closest( 'form' );
					var $button = $( this );
					/**
					 * @type {{
					 *    no_limit: boolean,
					 *    access_due_date: string,
					 *    access_due_hh: string,
					 *    access_due_mm: string,
					 *    access_time: int,
					 *    course_id: int,
					 *    action: string,
					 * }}
					 */
					var data = $form.serializeObject();
					data.action = 'bpmj_eddcm_set_access_time';
					data._ajax_nonce = bpmj_eddcm_users_manager_nonce;
					var course_id = data.course_id;
					$.ajax( {
						type: "POST",
						data: data,
						dataType: "json",
						url: bpmj_eddcm.ajax,
						beforeSend: function () {
							$button.data( 'action', '' );
							$button.html( '<span class="dashicons dashicons-update"></span>' );
						},
						success: function ( response ) {
							$( '.bpmj-eddcm-modal[data-modal="set-access-time"]' ).remove();
							$( 'html, body' ).css( 'overflow', 'visible' );
							$( '#access_time_' + course_id ).html( response.cell_html );
						}
					} ).fail( function ( data ) {
						if ( window.console && window.console.log ) {
							console.log( data );
						}
					} );
				} );
			},

			setTotalTime: function () {
				$( document ).on( 'click', '[data-action="set-total-time-popup"]', function ( e ) {
					e.preventDefault();
					var user_id = $( this ).data( 'userId' );
					var product_id = $( this ).data( 'productId' );
					var course_id = $( this ).data( 'courseId' );
					var total_time = $( '#total_time_' + course_id ).text();
					var html = $( '#bpmj_eddcm_edit_total_time' ).html();

					UsersManager.functions.modal( 'set-total-time', bpmj_eddcm.users_manager.set_total_time_popup.title, html, 'text-center', true );
					var $modal = $( '.bpmj-eddcm-modal[data-modal="set-total-time"]' );
					/**
					 * @type {HTMLFormElement|{
					 *    user_id: HTMLInputElement,
				     *    product_id: HTMLInputElement,
				     *    course_id: HTMLInputElement,
				     *    total_time_sign: HTMLInputElement,
				     *    total_time_dd: HTMLInputElement,
				     *    total_time_hh: HTMLInputElement,
				     *    total_time_mm: HTMLInputElement,
				     *    total_time_ss: HTMLInputElement,
					 * }}
					 */
					var form = $modal.find( 'form' ).get( 0 );
					form.user_id.value = user_id;
					form.product_id.value = product_id;
					form.course_id.value = course_id;

					var total_time_parts = total_time.split( ':' );

					form.total_time_sign.value = parseInt( total_time_parts[ 0 ] ) < 0 ? '-' : '+';
					form.total_time_dd.value = parseInt( total_time_parts[ 0 ] ) < 0 ? String( - 1 * total_time_parts[ 0 ] ) : total_time_parts[ 0 ];
					form.total_time_hh.value = total_time_parts[ 1 ];
					form.total_time_mm.value = total_time_parts[ 2 ];
					form.total_time_ss.value = total_time_parts[ 3 ];
				} );
				$( document ).on( 'click', '[data-action="set-total-time"]', function ( e ) {
					e.preventDefault();
					var $form = $( this ).closest( 'form' );
					var $button = $( this );
					/**
					 * @type {{
					 *    total_time_sign: string,
					 *    total_time_dd: int,
					 *    total_time_hh: int,
					 *    total_time_mm: int,
					 *    total_time_ss: int,
					 *    course_id: int,
					 *    action: string,
					 * }}
					 */
					var data = $form.serializeObject();
					data.action = 'bpmj_eddcm_set_total_time';
					data._ajax_nonce = bpmj_eddcm_users_manager_nonce;
					var course_id = data.course_id;
					$.ajax( {
						type: "POST",
						data: data,
						dataType: "json",
						url: bpmj_eddcm.ajax,
						beforeSend: function () {
							$button.data( 'action', '' );
							$button.html( '<span class="dashicons dashicons-update"></span>' );
						},
						success: function ( response ) {
							/** @var {number} response.total_time */
							$( '.bpmj-eddcm-modal[data-modal="set-total-time"]' ).remove();
							$( 'html, body' ).css( 'overflow', 'visible' );
							window.bpmj_eddcm_total_timers[ 'total_time_' + course_id ] = response.total_time;
						}
					} ).fail( function ( data ) {
						if ( window.console && window.console.log ) {
							console.log( data );
						}
					} );
				} );
			},

			removeUserFromCourse: function () {
				$( document ).on( 'click', '[data-action="remove-from-course"]', function ( e ) {
					if ( ! confirm( bpmj_eddcm.users_manager.remove_user_from_course_confirm ) ) {
						return false;
					}
                                        e.preventDefault();
					var user_id = $( this ).data( 'userId' );
					var product_id = $( this ).data( 'productId' );
					var data = {
						user_id: user_id,
						product_id: product_id,
						action: 'bpmj_eddcm_remove_from_course',
						_ajax_nonce: bpmj_eddcm_users_manager_nonce
					};
					var $button = $( this );
					$.ajax( {
						type: "POST",
						data: data,
						dataType: "json",
						url: bpmj_eddcm.ajax,
						beforeSend: function () {
							$button.data( 'action', '' );
							$button.html( '<span class="dashicons dashicons-update"></span>' );
						},
						success: function ( response ) {
							$button.closest( 'tr' ).remove();
                                                        location.reload();
						}
					} ).fail( function ( data ) {
						if ( window.console && window.console.log ) {
							console.log( data );
						}
					} );
				} );
			},

			cancelSubscription: function () {
				$( document ).on( 'click', '.btn-eddcm-cancel-subscription', function ( e ) {
					if ( ! confirm( bpmj_eddcm.users_manager.cancel_subscription_confirm ) ) {
						return false;
					}
				} );
			},

			addUserToCourse: function () {
				$( document ).on( 'click', '[data-action="add-to-course"]', function ( e ) {
					e.preventDefault();
					var html = $( '#bpmj_eddcm_add_user_to_a_course' ).html();

					UsersManager.functions.modal( 'add-to-course', bpmj_eddcm.users_manager.add_to_course.title, html, 'text-center', true );
					var $modal = $( '.bpmj-eddcm-modal[data-modal="add-to-course"]' );
				} );
				$( document ).on( 'click', '[data-action="add-to-course-do"]', function ( e ) {
					e.preventDefault();
					var user_id = $( this ).data( 'userId' );
					var product_id = $( this ).data( 'productId' );
					var price_id = $( this ).data( 'priceId' ) || null;
					var data = {
						user_id: user_id,
						product_id: product_id,
						price_id: price_id,
						action: 'bpmj_eddcm_add_to_course',
						_ajax_nonce: bpmj_eddcm_users_manager_nonce
					};
					var $button = $( this );
					$.ajax( {
						type: "POST",
						data: data,
						dataType: "json",
						url: bpmj_eddcm.ajax,
						beforeSend: function () {
							$button.data( 'action', '' );
							$button.html( '<span class="dashicons dashicons-update"></span>' );
						},
						success: function () {
							$button.closest( 'tr' ).remove();
							window.location.reload();
						}
					} ).fail( function ( data ) {
						if ( window.console && window.console.log ) {
							console.log( data );
						}
					} );
				} );
			},

			showCourseProgressPopup: function () {
				$( document ).on( 'click', '[data-action="show-course-progress-popup"]', function ( e ) {
					e.preventDefault();
					var html = $( '#bpmj_eddcm_course_progress' ).html();

					UsersManager.functions.modal( 'course-progress', bpmj_eddcm.users_manager.course_progress_popup.title, html, 'text-center', true );
					var $modal = $( '.bpmj-eddcm-modal[data-modal="course-progress"]' );
					$modal.find( '.container' ).css( {width: '800px', maxWidth: '800px'} );
					var $modalDiv = $modal.find( '._put_content_here' );
					var user_id = $( this ).data( 'userId' );
					var course_id = $( this ).data( 'courseId' );
					var data = {
						user_id: user_id,
						course_id: course_id,
						action: 'bpmj_eddcm_show_course_progress',
						_ajax_nonce: bpmj_eddcm_users_manager_nonce
					};
					$.ajax( {
						type: "POST",
						data: data,
						dataType: "html",
						url: bpmj_eddcm.ajax,
						success: function ( response ) {
							$modalDiv.html( response );
						}
					} ).fail( function ( data ) {
						if ( window.console && window.console.log ) {
							console.log( data );
						}
					} );
				} );
			}
		},

		functions: {
			modal: bpmj_eddcm_open_modal,
			zeropad: function ( number ) {
				return (
					"00" + number
				).slice( - 2 );
			}
		}
	};

	$( document ).ready( function () {
		UsersManager.init();
	} );
}( jQuery );
+ function ( $ ) {
	'use strict';

	var Courses = {

		/**
		 * Init
		 */
		init: function () {
			Courses.listeners.variablePricesSwitch();
			Courses.listeners.variablePricesOpenModal();
			Courses.listeners.variablePricesSave();
			Courses.listeners.variablePricesValidator();
			Courses.listeners.variablePricesSwitchCheckbox();
			Courses.listeners.purchaseLimitKeepSynced();
			Courses.listeners.addToCartLink();
			Courses.listeners.courseStructureSave();
			Courses.listeners.quizStructureSave();
			Courses.listeners.timeFieldValidator();
			Courses.listeners.numberTestAttemptsValidator();

			Courses.functions.refresh_variable_prices_checkboxes();
			Courses.init_autotips();
		},

		listeners: {
			addToCartLink: function () {
				$( document ).on( 'keyup change', '.bpmj-eddcm-add-to-cart-link-creator input', function () {

					$('.bpmj-eddcm-add-to-cart-link-copied').removeClass('copied');

					var $container = $( this ).closest( '.bpmj-eddcm-add-to-cart-link-creator' );
					var link_info = {};
					$container.find( ':input' ).each( function ( key, input ) {
						if ( input.name && (
								'checkbox' !== input.type || input.checked
							) ) {
							link_info[ input.name.replace( /^atc_/, '' ) ] = input.value;
						}
					} );
					var url = link_info.link_base;
					url += url.indexOf( '?' ) === - 1 ? '?' : '&';
					url += 'add-to-cart={product_id}';
					if ( parseInt( link_info.quantity ) > 1 ) {
						url += '&quantity=' + link_info.quantity;
					}
					if ( link_info.discount ) {
						url += '&discount=' + link_info.discount;
					}
					if ( link_info.gift ) {
						url += '&gift=1';
					}
					$container.find( '.bpmj-eddcm-add-to-cart-link' ).each( function ( key, input ) {
						var product_id = $( input ).data( 'productId' );
						var price_id = $( input ).data( 'priceId' );
						var url_to_product = url.replace( /\{product_id\}/, product_id );
						if ( price_id ) {
							url_to_product += '&price-id=' + price_id;
						}
						input.value = url_to_product;
					} );
				} );
			},
			variablePricesSwitch: function () {
				$( document ).on( 'click', '#eddcm-variable-pricing', function ( e ) {
					var checked = $( this ).is( ':checked' );
					if ( checked ) {
						$( '.bpmj-eddcm-single-price' ).hide();
						$( '.bpmj-eddcm-variable-prices' ).show();
					} else {
						$( '.bpmj-eddcm-single-price' ).show();
						$( '.bpmj-eddcm-variable-prices' ).hide();
					}
				} );
			},
			variablePricesOpenModal: function () {
				$( document ).on( 'click', '[data-action="edit-variable-prices"]', function ( e ) {
					e.preventDefault();
					Courses.functions.modal( 'bpmj-eddcm-variable-prices-modal', bpmj_eddcm.courses.variable_prices_popup.title, bpmj_eddcm.courses.variable_prices_popup.placeholder, '', true, 'ultra-wide' );
					var post_id = $( this ).data( 'postId' );
					var post_data = {
						action: 'wpi_handler',
						post_id: post_id
					};
					post_data[bpmj_eddcm.nonce_name] = bpmj_eddcm.nonce_value
					$.ajax( {
						type: "POST",
						data: post_data,
						dataType: "json",
						url: bpmj_eddcm.ajax+'?wpi_route=admin/edit_course/get_variable_prices',
						success: function ( response ) {
							var $placeholder = $( '#bpmj-eddcm-variable-prices-placeholder' );
							$placeholder.html( response.html );
							$placeholder.find( '#edd_variable_price_fields' ).show();
							$placeholder.find(".edd_repeatable_table tbody").sortable({
								handle: '.edd_draghandle', items: '.edd_repeatable_row', opacity: 0.6, cursor: 'move', axis: 'y', update: function() {
									var count  = 0;
									$(this).find( 'tr' ).each(function() {
										$(this).find( 'input.edd_repeatable_index' ).each(function() {
											$( this ).val( count );
										});
										count++;
									});
								}
							});
						},
						error: function ( jqXHR, textStatus, errorThrown ) {
							console.log('Error: ' + jqXHR.responseJSON.error_message)
						}
					} ).fail( function ( data ) {
						if ( window.console && window.console.log ) {
							console.log( data );
						}
					} );
				} );
			},
			variablePricesSave: function () {
				$( document ).on( 'click', '[data-action="save-variable-prices"]', function ( e ) {
					e.preventDefault();
					if ( '1' === $( this ).data( 'saving' ) ) {
						return;
					}
					$( this ).data( 'saving', '1' );
					var post_data = $( '#bpmj-eddcm-variable-prices-placeholder' ).find( ':input' ).serializeObject();
					post_data.variable_sale_price_from_date = $('#variable_sale_price_from_date').val();
					post_data.variable_sale_price_from_hour = $('#variable_sale_price_from_hour').val();
					post_data.action = 'wpi_handler'
					post_data[bpmj_eddcm.nonce_name] = bpmj_eddcm.nonce_value;
					$( this ).html( '<span class="dashicons dashicons-update"></span>' );
					$.ajax( {
						type: "POST",
						data: post_data,
						dataType: "json",
						url: bpmj_eddcm.ajax+'?wpi_route=admin/edit_course/save_variable_prices',
						success: function ( response ) {
							if ( response ) {
								$( '#bpmj-eddcm-variable-prices' ).html( response.variable_prices_summary_html );

								if(!response.no_variable_prices){
									$('#sales_disabled').prop('disabled',false);
									$('#sales_disabled_notice').text('');
								}

								window.snackbar.show(response.message);
							}
							$( '[data-action="close-modal"]' ).trigger( 'click' );
						},
						error: function ( jqXHR, textStatus, errorThrown ) {
							console.log('Error: ' + jqXHR.responseJSON.error_message)
						}
					} ).fail( function ( data ) {
						if ( window.console && window.console.log ) {
							console.log( data );
						}
					} );
				} );
			},
			variablePricesValidator: function () {
				$( document ).on( 'keyup change', '.bpmj-eddpc-access-time input[type=number]', function () {
					let removeDashAndLetters = $(this).val().replace(/[^0-9]/g,"");
					$(this).val(removeDashAndLetters);
				});

				$( document ).on( 'keyup change', '.edd-sale-price-field, .edd-price-field', function () {
					let removeDashAndLetters = $(this).val().replace(/[^0-9\.,]/g,"");
					$(this).val(removeDashAndLetters);
				});
			},
			variablePricesSwitchCheckbox: function () {
				$( document ).on( 'click', '.module.full > .variable-prices input', Courses.functions.variable_prices_checkbox_onclick );
			},
			purchaseLimitKeepSynced: function () {
				var $limit = $( '#bpmj_eddcm_purchase_limit' );
				var $limit_items_left = $( '#bpmj_eddcm_purchase_limit_items_left' );
				if ( $limit.val() === $limit_items_left.val() ) {
					// If items are the same (or blank) keep them in sync
					$limit.change( function () {
						$limit_items_left.val( $limit.val() );
					} );
				}
			},
			courseStructureSave: function () {
				$( document ).on( 'click', '#save-course-structure', function ( e ) {
					e.preventDefault();
					$(this).prop('disabled', true).html(settings_tab.saving_course_structure);

					let post_data = $( '#form-course-structure' ).serializeObject();
					post_data.action = 'wpi_handler'
					post_data[bpmj_eddcm.nonce_name] = bpmj_eddcm.nonce_value;

					$.ajax( {
						type: "POST",
						data: post_data,
						dataType: "json",
						url: bpmj_eddcm.ajax+'?wpi_route=admin/edit_course/save_course_structure',
						success: function ( response ) {

							let errorMessage = response.errorMessage ?? [];

							if (errorMessage.length > 0) {
								window.snackbar.show(errorMessage);
								setTimeout(function(){ $('#save-course-structure').prop('disabled', false).html(settings_tab.save_course_structure); }, 1000);
								return;
							}

							let courseId = response.courseId ?? null;

							window.wpi_tab_save_data_checker.resetUnsavedData();

							Courses.functions.reload_structure_tab( courseId );

							window.snackbar.show(response.successMessage);

						},
						error: function ( jqXHR, textStatus, errorThrown ) {
							console.log('Error: ' + jqXHR.responseJSON.error_message)
						}
					} ).fail( function ( data ) {
						if ( window.console && window.console.log ) {
							console.log( data );
						}
					} );
				} );
			},
			quizStructureSave: function () {
				$( document ).on( 'click', '#save-quiz-structure', function ( e ) {
					e.preventDefault();
					$(this).prop('disabled', true).html(settings_tab.saving_quiz_structure);

					let post_data = $( '#form-quiz-structure' ).serializeObject();
					post_data.action = 'wpi_handler'
					post_data[bpmj_eddcm.nonce_name] = bpmj_eddcm.nonce_value;

					$.ajax( {
						type: "POST",
						data: post_data,
						dataType: "json",
						url: bpmj_eddcm.ajax+'?wpi_route=admin/edit_quiz/save_quiz_structure',
						success: function ( response ) {

							let errorMessage = response.errorMessage ?? [];

							if (errorMessage.length > 0) {
								window.snackbar.show(errorMessage);
								setTimeout(function(){ $('#save-quiz-structure').prop('disabled', false).html(settings_tab.save_course_structure); }, 1000);
								return;
							}

							let quizId = response.quizId ?? null;

							window.wpi_tab_save_data_checker.resetUnsavedData();

							Courses.functions.reload_quiz_structure_tab( quizId );

							window.snackbar.show(response.successMessage);

						},
						error: function ( jqXHR, textStatus, errorThrown ) {
							console.log('Error: ' + jqXHR.responseJSON.error_message)
						}
					} ).fail( function ( data ) {
						if ( window.console && window.console.log ) {
							console.log( data );
						}
					} );
				} );
			},
			timeFieldValidator: function () {
				$( document ).on( 'keyup change', '#time', function () {
					let removeDashAndLetters = $(this).val().replace(/[^0-9]/g,"");
					$(this).val(removeDashAndLetters);
				});
			},
			numberTestAttemptsValidator: function () {
				$( document ).on( 'keyup change', '#number_test_attempts', function () {
					let removeDashAndLetters = $(this).val().replace(/[^1-9]/g,"");
					$(this).val(removeDashAndLetters);
				});
			},
		},

		functions: {
			modal: bpmj_eddcm_open_modal,
			interpolate_variable_prices_template: function ( template, price_ids, input_key ) {
				return template.replace( /{([a-z0-9\-]+)}/g, function ( full_match, key ) {
					var checked_matches;
					if ( 'key' === key ) {
						return input_key;
					} else if ( (
						            checked_matches = /checked-(\d+)/.exec( key )
					            ) && - 1 !== $.inArray( parseInt( checked_matches[ 1 ] ), price_ids ) ) {
						return ' checked="checked" ';
					}

					return '';
				} );
			},
			reload_structure_tab: function ( courseId ) {

				if(!courseId){
					return;
				}

				let url = bpmj_eddcm.ajax+'?_wpnonce='+bpmj_eddcm.nonce_value+'&edit_course_id='+courseId+'&action=wpi_handler&wpi_route=admin/settings_fields_ajax/get_tab_content';

				$('.settings-content').html(
					$('template#loader').html()
				);

				$.ajax({
					method: "POST",
					url: url,
					data: {
						tab: 'structure'
					},
					dataType: "html"
				})
					.success(res => {
						res = JSON.parse(res);

						$( ".settings-content" ).html( res.content );
						$('.settings-content').trigger('tab_loaded', 'structure')
					});
			},
			reload_quiz_structure_tab: function ( quizId ) {

				if(!quizId){
					return;
				}

				let url = bpmj_eddcm.ajax+'?_wpnonce='+bpmj_eddcm.nonce_value+'&edit_quiz_id='+quizId+'&action=wpi_handler&wpi_route=admin/settings_fields_ajax/get_tab_content';

				$('.settings-content').html(
					$('template#loader').html()
				);

				$.ajax({
					method: "POST",
					url: url,
					data: {
						tab: 'structure'
					},
					dataType: "html"
				})
					.success(res => {
						res = JSON.parse(res);

						$( ".settings-content" ).html( res.content );
						$('.settings-content').trigger('tab_loaded', 'structure')
					});
			},
			refresh_variable_prices_checkboxes: function () {
				$( '.module.full > .variable-prices input' ).each( function () {
					Courses.functions.variable_prices_checkbox_onclick.bind( this )();
				} );
			},
			variable_prices_checkbox_onclick: function () {
				var $this = $( this );
				var is_checked = $this.is( ':checked' );
				var value = $this.val();
				$this.closest( '.module.full' ).find( '.lessons input._price_id_' + value ).each( function () {
					$( this ).prop( 'disabled', ! is_checked );
				} );
			}
		},

		init_autotips: function () {
			$( '.bpmj-eddcm-autotip' ).each( function () {
				var $this = $( this );
				var $label = $this.prevAll( 'label' ).last();
				var text = $this.text();
				var $help_icon = $( '<span class="bpmj-eddcm-autotip-icon dashicons dashicons-editor-help"></span>' );
				if ( ! $label.attr( 'title' ) ) {
					$label.attr( 'title', text );
				} else {
					$help_icon.attr( 'title', text );
				}
				$label.append( $help_icon );
			} );
		}
	};

	$( document ).ready( function () {
		Courses.init();

        $( 'select[name="wp_idea[tools][import_students][courses][]"]' ).select2();

        $( '.bpmj-break-import' ).on( 'click', function( e ) {
        	e.preventDefault();

        	if ( window.confirm( 'Are you sure you want to stop the import?' ) ) {
				$.ajax( {
					type: "POST",
					data: {
						action: 'bpmj_eddcm_stop_the_import'
					},
					dataType: "json",
					url: bpmj_eddcm.ajax,
					complete: function() {

					}
				} );
        		// window.location.assign(window.location.href + '&bpmj_stop_import=true');
			}
		} );

		$( 'input[name="wp_idea[enable_certificates]"]' ).on( 'click', function() {
			if ( this.checked ) {
				$( '#courses_certificates table tr:not(:first-child)' ).css( 'display', 'table-row' );
			} else {
				$( '#courses_certificates table tr:not(:first-child)' ).css( 'display', 'none' );
			}
		} ).trigger( 'click' );

		$( '#bpmj-eddcm-clear-quizes-filters' ).on( 'click', function () {
			$( '#bpmj-eddcm-quizz-resolved-type' ).val( '' );
			$( '#bpmj-eddcm-quizz-resolved-by' ).val( '' );
			$( '#bpmj-eddcm-quizz-resolved-by-text' ).val( '' );
		});

		$('body').on( 'click','.subscription-exceeded-popup-core__cta, .bpmj-eddcm-expiration-notice .bpmj-eddcm-expiration-notice__button.go, .bpmj-eddcm-expiration-notice .bpmj-eddcm-expiration-notice__button.box', function ( e ) {
			e.preventDefault();

			Courses.functions.modal( 'bpmj-eddcm-wpidea-prices-modal', $( this ).data( 'modal-title' ), '', '', true, 'wp-idea-prices' );

			var modal_body_element = $( 'div.modal[data-modal="bpmj-eddcm-wpidea-prices-modal"] .panel-body' );
			modal_body_element.height($( window ).height() - 100);

			var src = $( this ).data( 'buy-url' );

			src += '?uid=' + $( this ).data( 'uid' ) + '&domain=' + $( this ).data( 'domain' );

			$( '<iframe>', {
				src: src,
			} ).css( 'width', '100%' )
				.css( 'height', '100%' )
				.appendTo(modal_body_element);
		} );

		$( '.copy-diagnostic-button-wrapper a' ).on( 'click', function ( e ) {
			e.preventDefault();

			var diagnostic_string = $('#diagnostics_data__table').html();

			diagnostic_string = diagnostic_string.replace(/<br>/g, '').replace('\t','');

			var element = $( '<textarea>' );
			element.val( diagnostic_string )
				.attr( 'readonly', true );
			$( 'body' ).append( element );
			element.select();
			document.execCommand( 'copy' );
			element.remove();
			$('.diagnostic-data-copied').show();
		} );

		var first_list_element_event = function () {
			$('.wpi-add-mailing-list').trigger( 'click' );
			$('.wpi-mailing-lists').children().addClass('first-list');
			$('.wpi-add-mailing-list[data-mailer="' + $('.wpi-mailing-lists').data('mailer') + '"]').addClass('none');
		};

		var remove_mailing_list = function ( e ) {
			$( this ).parent( 'div' ).remove();

			if ($( '.wpi-mailing-lists' ).children().length === 0) {
				first_list_element_event();
			}
		};

		$('.wpi-add-mailing-list').on( 'click', function ( e ) {
			e.preventDefault();

			var mailer_name = $(this).data('mailer'),
				template = $('.wpi-mailing-list-template[data-mailer="' + mailer_name + '"]').clone();

			template.removeClass('wpi-mailing-list-template');
			var select = template.find('select');
			select.attr('name', select.data('name'));
			select.on('change', function () {
				$('.wpi-mailing-lists').children().removeClass('first-list');
				$('.wpi-add-mailing-list[data-mailer="' + $('.wpi-mailing-lists').data('mailer') + '"]').removeClass('none');
			});

			template.find( '.wpi-remove-mailing-list' ).on( 'click', remove_mailing_list );

			$('.wpi-mailing-lists[data-mailer="' + mailer_name + '"]').append(template);
		} );

		$('.wpi-remove-mailing-list').on( 'click', remove_mailing_list);

		if ($( '.wpi-mailing-lists' ).children().length === 0) {
			first_list_element_event();
		}
	} );

	// function exports
	window.bpmj_eddcm_refresh_variable_prices_checkboxes = Courses.functions.refresh_variable_prices_checkboxes;
}( jQuery );

+ function ( $ ) {
	'use strict';

	var Settings = {

		/**
		 * Init
		 */
		init: function () {
			Settings.listeners.voucherPreview();
			Settings.listeners.certificatePreview();
			Settings.listeners.showHidePrivacyPolicySettings();
			Settings.listeners.deleteCertificateTemplate();
			Settings.listeners.messagesSettings();
			Settings.listeners.toggleDefaultVatRate();
			Settings.listeners.affiliateSettings();
			Settings.listeners.activeSessionsLimiterSettings();
		},

		listeners: {
			affiliateSettings: function() {
				if(document.getElementById('wpuf-wp_idea[partner-program]')) {

					let showOrHideCommissionField = () => {
						let shouldShow = document.getElementById('wpuf-wp_idea[partner-program]').checked;

						if (shouldShow) {
							jQuery('.commission_hideable_setting').show();
						} else {
							jQuery('.commission_hideable_setting').hide();
						}
					}

					showOrHideCommissionField();

					document.getElementById('wpuf-wp_idea[partner-program]').addEventListener('change', () => {
						showOrHideCommissionField();
					});
				}
			},

			activeSessionsLimiterSettings: function() {
				if(document.getElementById('wpuf-wp_idea[enable_active_sessions_limiter]')) {

					let showOrHideCommissionField = () => {
						let shouldShow = document.getElementById('wpuf-wp_idea[enable_active_sessions_limiter]').checked;

						if (shouldShow) {
							jQuery('.session_limit_hideable_setting').show();
						} else {
							jQuery('.session_limit_hideable_setting').hide();
						}
					}

					showOrHideCommissionField();

					document.getElementById('wpuf-wp_idea[enable_active_sessions_limiter]').addEventListener('change', () => {
						showOrHideCommissionField();
					});
				}
			},

			messagesSettings: function() {
				if(document.getElementById('wpuf-payment_reminders[enable_payment_reminders]')) {

					let showOrHideRecoveryShoppingCartFields = () => {
						let shouldShow = document.getElementById('wpuf-payment_reminders[enable_payment_reminders]').checked;

						if (shouldShow) {
							jQuery('.recovery_shopping_cart_hideable_setting').show();
						} else {
							jQuery('.recovery_shopping_cart_hideable_setting').hide();
						}
					}

					showOrHideRecoveryShoppingCartFields();

					document.getElementById('wpuf-payment_reminders[enable_payment_reminders]').addEventListener('change', () => {
						showOrHideRecoveryShoppingCartFields();
					});

				}

				if(document.getElementById('wpuf-wp_idea[allow_user_notice]')) {

					jQuery('.user-notice-settings-description-alert').click((e)=>{e.preventDefault()});

					let showOrHideNoticeFields = () => {
						let shouldShow = document.getElementById('wpuf-wp_idea[allow_user_notice]').checked;

						if (shouldShow) {
							jQuery('.notice_hideable_setting').show();
						} else {
							jQuery('.notice_hideable_setting').hide();
						}
					}

					showOrHideNoticeFields();

					document.getElementById('wpuf-wp_idea[allow_user_notice]').addEventListener('change', () => {
						showOrHideNoticeFields();
					});

				}
				
				jQuery( "<button class='hideable_toggle button' type='button'>" + bpmj_eddcm.settings.show + "</button>" ).insertBefore( ".hideable_container" );

				jQuery(document).on('click', '.hideable_toggle', (e)=>{
					if (jQuery(e.target).html() === bpmj_eddcm.settings.show) {
						jQuery(e.target).html(bpmj_eddcm.settings.hide);
						jQuery(e.target).after('<hr />');
						jQuery(e.target).parent().find('.hideable_container').toggle();
					} else {
						jQuery(e.target).parent().find('hr').remove();
						jQuery(e.target).html(bpmj_eddcm.settings.show);
						jQuery(e.target).parent().find('.hideable_container').toggle();
					}
				});
			},
			voucherPreview: function () {
				$( document ).on( 'click', '#wp_idea_gift_pdf_voucher_preview_html,#wp_idea_gift_pdf_voucher_preview_pdf', function ( e ) {
					e.preventDefault();
					var $this = $( this );
					var url = document.URL + '&action=gift_pdf_voucher_preview&type=' + $this.val();
					var template_content = '';
					var tinymce_editor = tinyMCE.get( 'wp_idea-gift_pdf_voucher_template' );
					var $voucher_template_textarea_field = $( '#wp_idea-gift_pdf_voucher_template' );
					if ( $voucher_template_textarea_field.is( ':visible' ) || ! tinymce_editor ) {
						template_content = btoa( encodeURIComponent( $voucher_template_textarea_field.val() ) );
					} else {
						template_content = btoa( encodeURIComponent( tinymce_editor.getContent() ) );
					}

					var template_styles_element = document.getElementById( 'wp_idea[gift_pdf_voucher_styles]' );
					var template_styles = template_styles_element ? template_styles_element.value : '';

					var template_orientation = $( '[name="wp_idea[gift_pdf_voucher_orientation]"]:checked' ).val();

                    var template_bg = $( '[name="wp_idea[voucher_bg]"]' ).val();
                    var template_styles_bg = '#bpmj_eddcm_page {' + ( ( template_orientation === 'landscape' ) ? 'height: 794px; width: 1122px;' : 'height: 1122px; width: 794px;' ) + ';}';
                    if ( template_bg != '' ) {
                        template_styles_bg += '#bpmj_eddcm_page {background: url("' + template_bg + '") no-repeat center;}';

                        template_styles = template_styles_bg + template_styles;
                    }

                    var hidden_form = $( '#bpmj-eddcm-pdf-preview-hidden-form' );
                    hidden_form.find( '[name="type"]' ).val( $this.val() );
                    hidden_form.find( '[name="content"]' ).val( template_content );
                    hidden_form.find( '[name="styles"]' ).val( btoa( encodeURIComponent( template_styles ) ) );
                    hidden_form.find( '[name="orientation"]' ).val( template_orientation );
                    hidden_form.find( '[name="pdftype"]' ).val( 'voucher' );
                    hidden_form.trigger( 'submit' );
				} );
			},
			certificatePreview: function() {
				$( document ).on( 'click', '#wp_idea_certificate_pdf_preview_html, #wp_idea_certificate_pdf_preview_pdf', function(e) {
					e.preventDefault();



					var $this = $( this );
					var url = document.URL + '&action=gift_pdf_voucher_preview&type=' + $this.val();
					var template_content = '';
					var tinymce_editor = tinyMCE.get( 'wp_idea-certificate_template' );
					var $voucher_template_textarea_field = $( '#wp_idea-certificate_template' );
					if ( $voucher_template_textarea_field.is( ':visible' ) || ! tinymce_editor ) {
						template_content = btoa( encodeURIComponent( $voucher_template_textarea_field.val() ) );
					} else {
						template_content = btoa( encodeURIComponent( tinymce_editor.getContent() ) );
					}

					var template_styles_element = document.getElementById( 'wp_idea[certificate_template_styles]' );
					var template_styles = template_styles_element ? template_styles_element.value : '';

					var template_orientation = $( '[name="wp_idea[certificate_orientation]"]:checked' ).val();

					var template_bg = $( '[name="wp_idea[certificates_bg]"]' ).val();
					var template_styles_bg = '#bpmj_eddcm_page {' + ( ( template_orientation === 'landscape' ) ? 'height: 794px; width: 1122px;' : 'height: 1122px; width: 794px;' ) + ';}';
					if ( template_bg != '' ) {
					    template_styles_bg += '#bpmj_eddcm_page {background: url("' + template_bg + '") no-repeat center;}';

					    template_styles = template_styles_bg + template_styles;
                    }

					var hidden_form = $( '#bpmj-eddcm-pdf-preview-hidden-form' );
					hidden_form.find( '[name="type"]' ).val( $this.val() );
					hidden_form.find( '[name="content"]' ).val( template_content );
					hidden_form.find( '[name="styles"]' ).val( btoa( encodeURIComponent( template_styles ) ) );
					hidden_form.find( '[name="orientation"]' ).val( template_orientation );
					hidden_form.find( '[name="pdftype"]' ).val( 'cert' );
					hidden_form.trigger( 'submit' );
				});
			},
			showHidePrivacyPolicySettings: function () {
				$( 'input[name="wp_idea[cookie-bar]"]' ).on( 'change', function () {
					var fieldsToHide = $( this ).closest( 'tr.privacy-policy' )
						.siblings( '.privacy-policy-show-hide' );

					if ( this.checked ) {
						fieldsToHide.show();
					} else {
						fieldsToHide.hide();
					}
				} ).trigger( 'change' );
			},

			deleteCertificateTemplate: function () {
				$( document ).on('click', '.delete-certificate-template', function (e) {
					e.preventDefault();
					if (confirm(bpmj_eddcm.remove_certificate_template)) {
						showLoader()

						var data = {
							'id':$(this).data('id'),
							'certificate_action':'delete',
							'action' : 'wpi_handler'
						};
						data[bpmj_eddcm.nonce_name] = bpmj_eddcm.nonce_value

						$.ajax({
							data:data,
							dataType: 'json',
							type: 'POST',
							url: bpmj_eddcm.ajax+'?wpi_route=admin/certificate_templates/delete',
							success: function (jqXHR, textStatus) {
								window.location.reload();
							},
							error: function ( jqXHR, textStatus, errorThrown ) {
								console.log('Error: ' + jqXHR.responseJSON.error_message)
							}
						})
					}



				});
			},

			toggleDefaultVatRate: function () {
				$( 'select[name="wp_idea[invoices_is_vat_payer]"]' ).on('change', function () {

					if ( 'yes' === $(this).val() ) {
						$(this).closest('tr').next('tr').show();
					} else {
						$(this).closest('tr').next('tr').hide();
					}
				}).trigger('change');
			},
		}
	};

	$( document ).ready( function () {
		Settings.init();
	} );

}( jQuery );

+jQuery(document).ready(function($) {
    'use strict';

    var VimeoUpload = {
        displayToggle: function(){
            if(typeof BPMJ_HIDE_UPLOAD_TOGGLE === 'undefined') return false;
            
            return BPMJ_HIDE_UPLOAD_TOGGLE ? false : true;
        }
    }
    
    var UploadToggle = {
        toggleClassName: "bpmj_vimeo_upload_toggle",

        hasToggleClassName: 'has-video-upload-toggle',

        elementsToHideOnUploadActive: [],
    
        checkStatusAndRender: function( appendTo ){
            var isUploadEnabled = Media_Toolbar.isUploadEnabled();
            var _this = this;
    
            isUploadEnabled.then(function( response, error ){
                var uploadEnabled = response[ Media_Toolbar.ACTION_CHANGE_VIMEO_UPLOAD_ENABLED_PARAM_NAME ] == true;

                var toggleHtmlSturcture = _this.getHtml( uploadEnabled );
                
                /**
                 * Render toggle
                 */
                _this.render( toggleHtmlSturcture, appendTo );

                if( uploadEnabled ){
                    _this.hideElementsThatShouldNotBeVisible();
                } else {
                    _this.restoreVisiblityOfTheElementsThatShouldNotBeVisible();
                }
    
                /**
                 * Watch for toggle state change
                 */
                Media_Toolbar.toggleVimeoUploadEnabledOnToggleChange();
            })
        },
    
        render: function( html, appendTo ){            
            var innerElement = appendTo ? appendTo : Media_Toolbar.getInnerElement();

            if( innerElement.parent().hasClass( UploadToggle.hasToggleClassName ) ) return;

            innerElement.parent().addClass( UploadToggle.hasToggleClassName );
            innerElement.append( html );
        },
    
        getHtml: function( checked ){
            var checkedAttr = checked ? 'checked="true"' : '';
    
            return '<div class="' + this.toggleClassName + '"><span>'+BPMJ_WPI_VIDEOS_I18N.upload_to_vimeo+'</span><label class="switch"><input type="checkbox" ' + checkedAttr + '><span class="slider round"></span></label></span>';
        },
    
        isOn: function(){
            var toggleVal = $('.' + this.toggleClassName + ' input[type="checkbox"]').attr('checked');
            return toggleVal ? true : false;
        },

        registerElementToHideIfUploadActive: function( el ){
            this.elementsToHideOnUploadActive.push(el);
        },

        hideElementsThatShouldNotBeVisible: function(){
            this.elementsToHideOnUploadActive.forEach(function(el){
                $(el).hide();
            });
        },

        restoreVisiblityOfTheElementsThatShouldNotBeVisible: function(){
            this.elementsToHideOnUploadActive.forEach(function(el){
                $(el).show();
            });
        }
    }

	var Media_Toolbar = {

        ACTION_CHANGE_VIMEO_UPLOAD_ENABLED: 'change_vimeo_upload_enabled',
        ACTION_GET_VIMEO_UPLOAD_ENABLED: 'get_vimeo_upload_enabled',

        ACTION_CHANGE_VIMEO_UPLOAD_ENABLED_PARAM_NAME: 'vimeo_upload_enabled',
        VIMEO_UPLOAD_ON: 'on',
        VIMEO_UPLOAD_OFF: 'off',

        init: function(){
            Media_Toolbar.addUploadToggle();
        },

        addUploadToggle: function(){
            UploadToggle.checkStatusAndRender();
        },

        toggleVimeoUploadEnabledOnToggleChange: function(){
            var _this = this;

            $('.' + UploadToggle.toggleClassName).on('change', function(){
                _this.toggleVimeoUploadEnabled();
                
                if( UploadToggle.isOn() ){
                    UploadToggle.hideElementsThatShouldNotBeVisible();
                } else {
                    UploadToggle.restoreVisiblityOfTheElementsThatShouldNotBeVisible();
                }
            })
        },

        toggleVimeoUploadEnabled: function(){
            var paramName = this.ACTION_CHANGE_VIMEO_UPLOAD_ENABLED_PARAM_NAME;
            var data = {};

            data[ paramName ] =  UploadToggle.isOn() ? this.VIMEO_UPLOAD_ON : this.VIMEO_UPLOAD_OFF;

            window.wp.ajax.send(this.ACTION_CHANGE_VIMEO_UPLOAD_ENABLED, {
                data: data,
                error: function( response ) {
                    console.log('%c%s', 'color: #ff0000', 'error:' + response)
                }
            })
        },

        isUploadEnabled: function() {
            return window.wp.ajax.send(this.ACTION_GET_VIMEO_UPLOAD_ENABLED, {
                type: 'GET'
            })
        },

        getInnerElement: function(){
            return $('.attachments-browser > .media-toolbar > .media-toolbar-secondary');
        }
    }

    var StatusCheckQueue = {
        queued: [],

        add: function( id ){
            this.queued.push( id );
        },

        remove: function( id ){
            this.queued.splice( this.queued.indexOf(id), 1 );
        },

        has: function( id ){
            return this.queued.indexOf(id) != -1;
        }
    }

    var Attachment = {
        VIDEO_ID_FIELD_NAME: 'video_id',
        VIMEO_UPLOAD_STATUS_FIELD_NAME: 'video_upload_status',

        VIDEO_UPLOAD_STATUS_IN_PROGRESS: 'in_progress',
        VIDEO_UPLOAD_STATUS_COMPLETE: 'complete',

        GENERAL_ATTACHMENT_CLASS: 'bpmj_vimeo_attachment',
        IN_PROGRESS_CLASS: 'bpmj_vimeo_attachment--upload_in_progress',
        COMPLETE_CLASS: 'bpmj_vimeo_attachment--upload_complete',
        IN_PROGRESS_ICON_CLASS: 'bpmj_vimeo_attachment__upload_in_progress_icon',
        COMPLETE_ICON_CLASS: 'bpmj_vimeo_attachment__upload_complete_icon',
        ATTACHMENT_LABEL_CLASS: 'bpmj_vimeo_attachment__label',
        ATTACHMENT_CHECKED_CLASS: 'bpmj_vimeo_attachment--checked',

        ACTION_GET_VIMEO_UPLOAD_STATUS: 'get_vimeo_upload_status',

        id: null,
        inputsStringContent: null,
        
        setId: function( id ){
            this.id = id;
        },

        setInputsStringContent: function( content ){
            this.inputsStringContent = content;
        },

        isOnVimeo: function(){
            if( ! this.id ) return;

            var inputs = $(this.inputsStringContent);

            var input = $('#attachments-' + this.id + '-' + this.VIDEO_ID_FIELD_NAME, inputs);
            var inputValue = input ? input.val() : null;

            return inputValue ? true : false;
        },

        isUploadToRemoteInProgress: function(){
            if( ! this.id ) return;

            var inputs = $(this.inputsStringContent);

            var input = $('#attachments-' + this.id + '-' + this.VIMEO_UPLOAD_STATUS_FIELD_NAME, inputs);
            var inputValue = input ? input.val() : null;

            return inputValue == this.VIDEO_UPLOAD_STATUS_IN_PROGRESS;
        },

        isUploadToRemoteComplete: function(){
            if( ! this.id ) return;

            var inputs = $(this.inputsStringContent);

            var input = $('#attachments-' + this.id + '-' + this.VIMEO_UPLOAD_STATUS_FIELD_NAME, inputs);
            var inputValue = input ? input.val() : null;

            return inputValue == this.VIDEO_UPLOAD_STATUS_COMPLETE;
        },

        checkStatusChangeUntilItsCompleted: function(){
            var _this = this;
            var id = _this.id;

            if( !StatusCheckQueue.has( id ) ){

                var interval = setInterval(function(){
                    _this.getVimeoUploadStatus(id).then(function(status){
                        if( status == _this.VIDEO_UPLOAD_STATUS_COMPLETE ){
                            clearInterval(interval);
                            _this.markAttachmentAsCompleted();
                            WpJsApi.refreshMediaLibrary();
                            StatusCheckQueue.remove( id )
                        }
                    });
                }, 10000)

                StatusCheckQueue.add( id );
            }

            this.markAttachmentAsInProgress();

        },

        getVimeoUploadStatus: function(id){
            var data = {};

            data[ 'attachment_id' ] = id;

            return window.wp.ajax.send(this.ACTION_GET_VIMEO_UPLOAD_STATUS, {
                data: data,
                type: 'GET'
            })
        },

        isInitialized: function(){
            var attachment = this.getAttachmentNode();

            return attachment.hasClass( Attachment.ATTACHMENT_CHECKED_CLASS );
        },

        getAttachmentNode: function(){
            return $('li[data-id="' + this.id + '"]');
        },

        markAsInitialized: function(){
            var attachment = this.getAttachmentNode();

            attachment.addClass(this.ATTACHMENT_CHECKED_CLASS);
        },

        markAttachmentAsCompleted: function(){
            var attachment = this.getAttachmentNode();

            attachment.removeClass(this.IN_PROGRESS_CLASS);

            var label = attachment.find('.' + this.ATTACHMENT_LABEL_CLASS);
            var icon = attachment.find('.' + this.IN_PROGRESS_ICON_CLASS);

            icon.remove();
            label.remove();            

            attachment.addClass(this.GENERAL_ATTACHMENT_CLASS);
            attachment.addClass(this.COMPLETE_CLASS);

            var completeIconNotAppendedYet = ! $('.' + this.COMPLETE_ICON_CLASS, attachment).length;
            if( completeIconNotAppendedYet ){
                attachment.append('<span class="dashicons dashicons-yes-alt ' + this.COMPLETE_ICON_CLASS + '"></span>');
                attachment.append('<span class="' + this.ATTACHMENT_LABEL_CLASS + '">'+BPMJ_WPI_VIDEOS_I18N.upload_complete+'</span>')
            }
        },

        markAttachmentAsInProgress: function(){
            var attachment = this.getAttachmentNode();

            attachment.addClass(this.GENERAL_ATTACHMENT_CLASS);
            attachment.addClass(this.IN_PROGRESS_CLASS);

            var inProgressIconNotAppendedYet = ! $('.' + this.IN_PROGRESS_ICON_CLASS, attachment).length;
            if( inProgressIconNotAppendedYet ){
                attachment.append('<span class="dashicons dashicons-update-alt ' + this.IN_PROGRESS_ICON_CLASS + '"></span>');
                attachment.append('<span class="' + this.ATTACHMENT_LABEL_CLASS + '">'+BPMJ_WPI_VIDEOS_I18N.upload_in_progress+'</span>')
            }

            WpJsApi.resetSelection();
        }
    }

    var attachmentsCache = {
        items: null,

        set: function(items){
            this.items = items.slice(0);
        },

        hasItems: function(){
            return this.items && this.items.length > 0;
        },

        get: function(){
            return this.items;
        }
    }

    var UploadStatusChecker = {

        init: function(){
            this.checkStatusPeriodically()
        },

        checkStatusPeriodically: function(){

            if( WpJsApi.hasAttachments() ) attachmentsCache.set( WpJsApi.getAttachmentsModels() );

            attachmentsCache.get().forEach( function(model) {
                if( ! model.attributes.compat ) return;
                
                var attachmentID = model.id;
                var attachmentInputsString = model.attributes.compat.item;


                Attachment.setId( attachmentID );
                
                if( Attachment.isInitialized() ) return;

                Attachment.setInputsStringContent( attachmentInputsString );
                Attachment.markAsInitialized();

                if( Attachment.isOnVimeo() && Attachment.isUploadToRemoteInProgress() ){
                    Attachment.checkStatusChangeUntilItsCompleted();
                }

                if( Attachment.isOnVimeo() && Attachment.isUploadToRemoteComplete() ){
                    Attachment.markAttachmentAsCompleted();
                }
            });

        }
    }

    var MediaTextBlock = {
        isMediaTextBlock: function(el){
            return el.dataset.type && el.dataset.type == "core/media-text";
        },

        removeUnwantedElements: function(el){
            var blockInner = this.getBlockInner(el);

            var directUploadButton = $(el).find('.components-form-file-upload');

            if( blockInner ){
                UploadToggle.registerElementToHideIfUploadActive( directUploadButton );
            }

            if(!VimeoUpload.displayToggle()) UploadToggle.hideElementsThatShouldNotBeVisible();
        },

        appendToggle: function(el){
            var blockInner = this.getBlockInner(el);

            if( blockInner ){
                UploadToggle.checkStatusAndRender( blockInner );
            }
        },

        getBlockInner: function(el){
            return $(el).find('.wp-block-media-text .components-placeholder');
        }
    }

    var VideoBlock = {
        isVideoBlock: function(el){
            return el.dataset & el.dataset.type && el.dataset.type == "core/video";
        },

        appendToggle: function(el){
            var videoBlockInner = this.getVideoBlockInner(el);

            if( videoBlockInner ){
                UploadToggle.checkStatusAndRender( videoBlockInner );
            }
        },

        removeUnwantedElements: function(el){
            var videoBlockInner = this.getVideoBlockInner(el);

            var directUploadButton = $(el).find('.components-form-file-upload');
            var urlInsertButton = $(el).find('.block-editor-media-placeholder__url-input-container');

            if( videoBlockInner ){
                UploadToggle.registerElementToHideIfUploadActive( directUploadButton );
                UploadToggle.registerElementToHideIfUploadActive( urlInsertButton );
            }

            if(!VimeoUpload.displayToggle()) UploadToggle.hideElementsThatShouldNotBeVisible();
        },

        getVideoBlockInner: function(el){
            return $(el).find('.wp-block-video .components-placeholder__instructions');
        }
    }

    var MediaFrame = {
        appendToggle: function(el){
            var mediaFrameToolbarInner = Media_Toolbar.getInnerElement();

            if( mediaFrameToolbarInner.length > 0 ){
                mediaFrameToolbarInner.each(function() {
                    UploadToggle.checkStatusAndRender( $(this) );
                });
            }
        }
    }

    var WpJsApi = {
        hasAttachments: function(){
            return this.getAttachmentsCount() && this.getAttachmentsCount() > 0;
        },

        getAttachmentsCount: function(){
            return wp.media.model.Attachments.all.length;
        },

        getAttachmentsModels: function(){
            return wp.media.model.Attachments.all.models;
        },

        getSelectionObject: function(){
            return wp.media.frame.state().get('selection');
        },

        resetSelection: function(){
            this.getSelectionObject().reset();
        },

        refreshMediaLibrary: function(){
            wp.media.frame.content.get().collection.props.set({ignore: (+ new Date())});
        }
    }

    var AttachmentNode = {
        isAttachmentNode: function( node ){
            return node.dataset.id ? true : false;
        }
    }

    var AttachmentsInitializer = {
        MEDIA_FRAME_CONTENT_CLASS: 'media-frame-content',

        attachmentsCount: 0,

        init: function(){
            var attachmetsInitializedCount = 0;
            var _this = this;

            $('.' + _this.MEDIA_FRAME_CONTENT_CLASS).on('DOMNodeInserted', function(e){
                _this.attachmentsCount = WpJsApi.hasAttachments() ? WpJsApi.getAttachmentsCount() : _this.attachmentsCount;
                
                if( !_this.attachmentsCount ) return;

                if( !AttachmentNode.isAttachmentNode( e.target ) ) return;

                attachmetsInitializedCount = attachmetsInitializedCount + 1;

                // init only if not initialized or after new upload
                if( attachmetsInitializedCount >= _this.attachmentsCount ){
                    UploadStatusChecker.init();
                }

                if( _this.someUninitializedAttachmentsLeft() ){
                    UploadStatusChecker.init();
                }
            })
        },

        someUninitializedAttachmentsLeft: function(){
            return $('.attachment:not(.' + Attachment.ATTACHMENT_CHECKED_CLASS + ')').length > 0;
        }
    }

    let MediaLimitChecker = {
        render: function () {
            try {
                $('body.post-type-attachment, body.post-type-page, body.post-type-courses').append(BPMJ_WPI_MEDIA_I18N.media_limit_popup_html);
            } catch (e) {

            }
        },
    }

    let MediaVideoFormatBlocker = {
        render: function () {
            try {
                $('body.post-type-attachment, body.post-type-page, body.post-type-courses').append(BPMJ_WPI_MEDIA_VIDEO_FORMAT_BLOCKER_I18N.media_video_format_blocker_popup_html);
            } catch (e) {

            }
        },
    }

	$( document ).ready( function () {

        if( VimeoUpload.displayToggle() ) Media_Toolbar.init();

        AttachmentsInitializer.init();

        $(document).on('DOMNodeInserted', '.wp-admin', function(e){
            if( VimeoUpload.displayToggle() ){
                //@todo: za duzo powtórzonej treści, docelowo wyciągnąć do jednej klasy
                //append modal in the gutenberg video block
                if( VideoBlock.isVideoBlock( e.target ) ){
                    VideoBlock.appendToggle( e.target );
                    VideoBlock.removeUnwantedElements( e.target );
                }
                //append modal in the gutenberg media-text block
                if( MediaTextBlock.isMediaTextBlock( e.target ) ){
                    MediaTextBlock.appendToggle( e.target );
                    MediaTextBlock.removeUnwantedElements( e.target );
                }

                if( ! e.target.classList.contains('browser') ) return;

                // append toggle in the media modal
                MediaFrame.appendToggle( e.target );
            } else {
                //@todo: za duzo powtórzonej treści, docelowo wyciągnąć do jednej klasy
                if( VideoBlock.isVideoBlock( e.target ) ){
                    VideoBlock.removeUnwantedElements( e.target );
                }
                if( MediaTextBlock.isMediaTextBlock( e.target ) ){
                    MediaTextBlock.removeUnwantedElements( e.target );
                }
            }

            if( ! e.target.classList.contains('browser') ) return;
            //reinitialize after media modal opening
            AttachmentsInitializer.init();
        });

        wp.Uploader.errors.on('add', function(data){

            let errorMessage = data.attributes.message ?? '';
            let uploadLimitExceededErrorMessage = bpmj_eddcm.media_limit_checker_popup_message;
            let uploadNotAllowedFormatErrorMessage = bpmj_eddcm.media_video_format_blocker_error;

            if (errorMessage === uploadLimitExceededErrorMessage || errorMessage === uploadNotAllowedFormatErrorMessage) {
                var popup = $('.wpi-popup');
                popup.css('z-index', '999999');
                popup.trigger('open');
                $('.media-sidebar').remove();
            }

        });

	} );

    MediaLimitChecker.render();
    MediaVideoFormatBlocker.render();
});
+jQuery(document).ready(function($) {
    'use strict';

    var NoticeDismisser = {
        init: function(){
            this.callApiOnDismiss();
        },

        callApiOnDismiss: function(){
            $('.bpmj-wpi-notice').on('click', $('.notice-dismiss'), function(){
                
                window.wp.ajax.send('wpi_notices_dismiss', {
                    data: {
                        id: $(this).attr('data-id')
                    }
                })
            })
        }
    }

    NoticeDismisser.init();
});
jQuery(document).ready(function($){
    function loadStats(){
        $.ajax({
            type: "GET",
            data: {
                action: 'edd_load_dashboard_widget'
            },
            url: ajaxurl,
            success: function (response) {
                $('.wpi-stats__content').html( response );
            }
        });
    }
    
    if($('.wpi-stats').length){
        loadStats();
    }
    
    if($("[data-load-with-ajax='bpmj_wpi_changelog']").length){
        if(typeof bpmj_changelog === 'undefined') return false;

        var securityToken = bpmj_changelog && bpmj_changelog.security ? bpmj_changelog.security : null;

        $.ajax({
            type: "GET",
            data: {
                action: 'bpmj_get_changelog',
                security: securityToken
            },
            url: ajaxurl,
            success: function (response) {
                $("[data-load-with-ajax='bpmj_wpi_changelog']").html( response.data );
            }
        });
    }
});
+jQuery(document).ready(function($) {
    'use strict';

    if(typeof disable_profile_inputs !== 'undefined' && disable_profile_inputs.value == true){
        $('#your-profile .form-table :input, #your-profile .submit :input').prop("disabled", true);
        $('tr#password, tr.pw-weak').hide();
    }
})
jQuery(document).ready(function($) {
    $('button.wpi-button[data-action="open_popup"]').on('click', function (){
        var popup_id = $(this).data('popup-id');
        var popup = $('#' + popup_id);

        if(popup.length === 0) return;

        popup.trigger('open');
    })

    var wpiPopup = $('.wpi-popup');

    wpiPopup.on('open', function (){
        var _this = $(this);

        _this.find('.wpi-popup__content').html(_this.data('loading'));
        _this.show();
        _this.addClass('open');


        var timeout = _this.data('timeout');

        if(timeout){
            setTimeout(function(){
                $('.wpi-popup.open').trigger('close');
            }, timeout);
        }

        if(_this.data('type') !== 'ajax-popup') return;

        var data = {
            action: _this.data('action')
        };

        var params = _this.data('params');

        $.ajax({
            type: "GET",
            data: $.extend({}, data, params),
            url: ajaxurl,
            success: function (response) {
                _this.find('.wpi-popup__content').html(response.data);
                _this.trigger('ajax-content-loaded');
            }
        });
    });

    wpiPopup.on('close', function (){
        var _this = $(this);

        _this.hide();
        _this.removeClass('open');
        _this.removeClass('ajax-content-loaded');
    })

    wpiPopup.on('ajax-content-loaded', function () {
        var _this = $(this);

        _this.addClass('ajax-content-loaded');
    });

    wpiPopup.on('click', '[data-close-popup-on-click]', function () {
        $('.wpi-popup.open').trigger('close');
    })

    $('.wpi-popup__back_overlay').on('click', function (){
        $('.wpi-popup.open').trigger('close');
    })

    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            $('.wpi-popup.open').trigger('close');
        }
    });

    wpiPopup.on('click', '[data-loading]', function () {
        $(this).html($(this).data('loading'));
    })
});

jQuery(document).ready(function($) {
    window.bind_template_group_setting = function () {
        var popup = $('.wpi-popup');
        popup.on('click', 'button.template-group-settings-cancel-button', function (){
            var popup = $(this).closest('.wpi-popup');

            if(popup.length === 0) return;

            popup.remove()
        });

        popup.on('click', 'button.template-group-settings-save-button', function (){
            var _this = $(this);
            var form_id = _this.data('form-id');

            if(!form_id) return;

            $('#' + form_id).trigger('submit');
        });

        popup.on('submit', '.wpi_group_settings_form', function (e){
            e.preventDefault();
            var _this = $(this);

            // show loader on the button
            var saveButton = _this.closest('.wpi-popup').find('button.template-group-settings-save-button');
            var saveButtonContent = saveButton.html();
            if(saveButton.data('loading')) saveButton.html(saveButton.data('loading'));

            // trigger html validation
            var all_fields_valid = _this.get(0).reportValidity();
            if(!all_fields_valid){
                // hide loader on the button
                saveButton.html(saveButtonContent);
                return;
            }

            // submit form fields
            $.ajax({
                type: "POST",
                data: {
                    action: 'bpmj_save_template_group_settings',
                    _ajax_nonce: _this.find('#_wpnonce').val(),
                    data: _this.getFormData()
                },
                url: ajaxurl,
                success: function (response) {
                    var popup = _this.closest('.wpi-popup');

                    popup.remove()

                    window.snackbar.show(saveButton.data('success'));
                }
            });
        });

        function initFontSelect()
        {
            $('.wpi-select.font_select').select2({
                ajax: {
                    url: bpmj_eddcm.ajax,
                    dataType: 'json',
                    delay: 250,
                    data: function ( params ) {
                        return {
                            action: $(this).data('get-fonts-ajax-action'),
                            _ajax_nonce: $(this).data('nonce-field'),
                            term: params.term
                        };
                    },
                    minimumInputLength: 2,
                    cache: true,
                    processResults: function (data) {
                        return {
                            results: data.data
                        };
                    }
                },
            });
        }

        var groupSettingsPopup = $('.group-settings-popup');

        initFontSelect();

        groupSettingsPopup.on('ajax-content-loaded', function () {
            initFontSelect();
        });

        groupSettingsPopup.closest('.wpi-popup').addClass('templates-settings')

        groupSettingsPopup.on('click', '.wpi-browse-media-library-button',function (event) {
            event.preventDefault();

            var self = $(this);

            // Create the media frame.
            var file_frame = wp.media.frames.file_frame = wp.media({
                title: self.data('uploader_title'),
                button: {
                    text: self.data('uploader_button_text'),
                },
                multiple: false
            });

            file_frame.on('select', function () {
                var attachment = file_frame.state().get('selection').first().toJSON();

                self.prev(self.data('input-iname')).val(attachment.url);
            });

            // Finally, open the modal
            file_frame.open();
        });
    }
});

jQuery(document).ready(function($) {
    var enableNewTemplatesButton = $('.enable-new-templates-system');
    var disableNewTemplatesButton = $('.disable-new-templates-system');
    var disableNewTemplatesAsADevButton = $('.disable-new-templates-system-dev');
    var hideNewTemplatesInfoButton = $('.hide-new-templates-info');
    var newTemplatesInfo = $('.new-templates-enabled-info');

    enableNewTemplatesButton.on('click', function () {
        var loadingText = $(this).data('loading');

        $(this).text(loadingText);

        changeNewTemplatesStatus('enable')
    });

    disableNewTemplatesButton.on('click', function () {
        var loadingText = $(this).data('loading');

        $(this).text(loadingText);

        changeNewTemplatesStatus('disable')
    });


    disableNewTemplatesAsADevButton.on('click', function () {
        var loadingText = $(this).data('loading');

        $(this).text(loadingText);

        changeNewTemplatesStatus('disable_as_dev')
    });

    hideNewTemplatesInfoButton.on('click', function () {
        var loadingText = $(this).data('loading');

        $(this).text(loadingText);

        hideNewTemplatesInfo();
    });

    function changeNewTemplatesStatus(status) {
        if(status !== 'enable' && status !== 'disable' && status !== 'disable_as_dev') {
            return;
        }

        var route = 'admin/templates_system/' + status + '_new_templates_system';

        doPostRequest(route, true);
    }

    function hideNewTemplatesInfo() {
        var route = 'admin/templates_system/hide_new_templates_info';

        doPostRequest(route, false, function () {
            newTemplatesInfo.slideUp();

            window.snackbar.show(newTemplatesInfo.data('message-your-are-redy-to-go'))
        });
    }

    function doPostRequest(route, reloadOnSuccess, onSuccess) {
        var postData = {
            action: 'wpi_handler'
        };

        postData[bpmj_eddcm.nonce_name] = bpmj_eddcm.nonce_value

        $.ajax({
            type: "POST",
            data: postData,
            dataType: "json",
            url: bpmj_eddcm.ajax + '?wpi_route=' + route,
            success: function () {
                if(reloadOnSuccess) {
                    window.location.reload();
                }

                if(onSuccess) {
                    onSuccess();
                }
            }
        });
    }
});

+function ( $ ) {
	'use strict';

	/**
	 * Delete course
	 */
	$( document ).on( 'click', 'button[data-action="delete-course"],button[data-action="delete-bundle"]', function ( e ) {
		e.preventDefault();

		var button = $( this );
		var type = $( this ).data( 'action' ).replace( 'delete-', '' );

		var confirm_text = bpmj_eddcm.creator['delete_confirm_' + type];
		if ( !confirm( confirm_text ) ) {
			return;
		}

		var postData = {
			action: 'wpi_handler',
			id: button.data( 'id' )
		};

		postData[bpmj_eddcm.nonce_name] = bpmj_eddcm.nonce_value

		$.ajax( {
			type: "POST",
			data: postData,
			dataType: "json",
			url: bpmj_eddcm.ajax+'?wpi_route=admin/courses/delete_'+ type,
			beforeSend: function () {
				button.html( '<span class="dashicons dashicons-update"></span>' );
			},
			success: function ( response ) {
				button.parent().parent().fadeOut( 'slow', function () {
					$( this ).remove();

					if ( $( '.edd-courses-manager-dashboard table tbody tr' ).length < 1 ) {
						location.reload();
					}
				} );
			},
			error: function ( jqXHR, textStatus, errorThrown ) {
				if (jqXHR.responseJSON.hasOwnProperty('show_message') && jqXHR.responseJSON.show_message) {
					alert(jqXHR.responseJSON.error_message);
					location.reload();
				}
				console.log('Error: ' + jqXHR.responseJSON.error_message)
			}
		} ).fail( function ( data ) {
			if ( window.console && window.console.log ) {
				console.log( data );
			}
		} );

	} );

	/**
	 * Click on the "show add to cart link" button
	 */
	$( document ).on( 'click', 'button[data-action="toggle-add-to-cart-link"]', function () {
		var add_to_cart_popup_html = $( this ).data( 'addToCartPopupHtml' );
		var course_title = $( this ).data( 'courseTitle' );

		bpmj_eddcm_open_modal( 'bpmj-eddcm-variable-prices-links-html', course_title, add_to_cart_popup_html, '', true );
	} );

	$( document ).on( 'click', 'button[data-action="toggle-show-users-stats"]', function () {
		var add_to_cart_popup_html = $( this ).data( 'showStatsPopupHtml' );
		var course_title = $( this ).data( 'courseTitle' );

		bpmj_eddcm_open_modal( 'bpmj-eddcm-users-stats-html', course_title, add_to_cart_popup_html, '', true );

		if ( $( this ).data( 'fromajax' ) === 0 )
			return;

		var data = {
				action: 'get_users_stats_for_course_lessons',
				course: $( this ).data( 'course' )
			};

		$.ajax( {
			type: "POST",
			data: data,
			dataType: "html",
			url: bpmj_eddcm.ajax,
			success: function ( response ) {
				$( '.bpmj-eddcm-modal' ).find( '.panel-body' ).html(response);
			}
		});
	} );

	$( document ).on( 'click', 'button[data-action="load-users-stats"]', function() {
		var button = $( this ),
			data = {
				action: 'get_users_stats_for_course',
				course: $( this ).data( 'course' )
			};

		button.find( '.dashicons' ).remove();
		button.html( '<span class="dashicons dashicons-update"></span>' );

		$.ajax( {
			type: "POST",
			data: data,
			dataType: "html",
			url: bpmj_eddcm.ajax,
			success: function ( response ) {
				button.after( response )
					.remove();
			}
		});
	} );

	$( document ).on( 'click', '.admin-courses-users-stats-toggle', function(e) {
		e.preventDefault();
		$( this ).closest( 'tr' ).next( '.course-stats-users' ).toggle();
	} );

	$( document ).on( 'click', '._payu_api_type input', function ( e ) {
		var payu_api_type = $( this ).val();
		if ( 'rest' === payu_api_type ) {
			$( '._payu_return_url_failure, ._payu_return_url_success, ._payu_return_url_reports' ).hide();
		} else {
			$( '._payu_return_url_failure, ._payu_return_url_success, ._payu_return_url_reports' ).show();
		}
	} );

	/**
	 * Wyłączanie i włączanie pól
	 */
	$( document ).on( 'change', '.input-group-addon input', function ( e ) {
		e.preventDefault();
		$( this ).parent().parent().find( '.input-group' ).prop( 'disabled', function ( i, v ) {
			return !v;
		} );
	} );

	$( document ).ready( function ( $ ) {
		$( document ).on( 'click', '.select-on-focus', function () {
			$( this ).select();
		} );
		$( document ).on( 'click', '.bpmj-eddcm-add-to-cart-link-copy', function () {
			$( this ).parent().find('.select-on-focus').select();
			if( document.execCommand('copy', false, "") ){
				$('.bpmj-eddcm-add-to-cart-link-copied').removeClass('copied');
				$( this ).parent().find('.bpmj-eddcm-add-to-cart-link-copied').addClass('copied');
			}
		} );
	} );

	$( document ).on( 'click', '[name^="disable_course_sales"]', function ( e ) {
		var course_id = $( this ).data( 'courseId' );
		var sales_disabled = $( this ).val() === 'on' ? 'on' : 'off';
		var postData = {
			action: 'wpi_handler',
			id: course_id,
			value: sales_disabled
		};
		postData[bpmj_eddcm.nonce_name] = bpmj_eddcm.nonce_value

		$.ajax( {
			type: "POST",
			data: postData,
			dataType: "json",
			url: bpmj_eddcm.ajax+'?wpi_route=admin/courses/disable_sales',
			error: function ( jqXHR, textStatus, errorThrown ) {
				console.log('Error: ' + jqXHR.responseJSON.error_message)
			}
		} );
	} );

	$(document).on('keyup', 'input[name="bpmj_edd_invoice_nip"]', (e) => {
		let currentVal = $(e.target).val();
		$('input[name="bpmj_edd_invoice_nip"]').each((k, v) => {
			$(v).val(currentVal);
		});
	});

}( jQuery );

function bpmj_eddcm_enable_datepickers() {
	if ( jQuery.fn.datepicker ) {
		jQuery( '.wp-datepicker-field:not(.wp-datepicker-enabled)' ).datepicker( {
			dateFormat: 'yy-mm-dd'
		} ).addClass( 'wp-datepicker-enabled' );
	}
}

jQuery( document ).ready( function ( $ ) {
	// Uruchamianie select2 dla tagów
	$( '.bpmj_eddcm_tags' ).tagsInput( {
		'height': '65px',
		'width': 'auto',
		'interactive': true,
		'defaultText': bpmj_eddcm.add_tag,
		'removeWithBackspace': true,
		'placeholderColor': '#666666'
	} );

	$( '.wp-color-picker-field' ).wpColorPicker();

	bpmj_eddcm_enable_datepickers( );

	/**
	 * GetResponse checkbox functions
	 */
	$( document ).on( 'change', '.checkbox-double', function () {
		checkboxDisable( this );
	} );

	$( '.checkbox-double' ).each( function () {
		checkboxDisable( this );
	} );

	function checkboxDisable( el ) {
		var $el = $( el );
		if ( $el.prop( 'checked' ) ) {
			$( '#' + $el.data( 'pair' ) ).prop( 'disabled', true );
		} else {
			$( '#' + $el.data( 'pair' ) ).prop( 'disabled', false );
		}
	}

	$( '._payu_api_type input:checked' ).click();

	$( '._payu_return_url_failure input, ._payu_return_url_success input, ._payu_return_url_reports input' ).focusin( function () {
		$( this ).get( 0 ).select();
	} );


} );

function showLoader() {
	var loader = document.createElement("div");
	loader.id = 'wpi-loader';
	loader.innerHTML = '<div class="loader-ring"><div></div><div></div><div></div><div></div></div>';
	document.body.prepend(loader);
}
function hideLoader() {
	var loader = document.getElementById("div");
	element.parentNode.removeChild(loader);
}

+function ($) {
    'use strict';

    let Product_Variable_Prices = {
        init: function () {
            Product_Variable_Prices.listeners.variablePricesSwitch();
            Product_Variable_Prices.listeners.variablePricesOpenModal();
            Product_Variable_Prices.listeners.variablePricesSave();
            Product_Variable_Prices.listeners.variablePricesValidator();
        },

        listeners: {
            variablePricesSwitch: function () {
                $(document).on('click', '#eddcm-variable-pricing', function (e) {
                    var checked = $(this).is(':checked');
                    if (checked) {
                        $('.bpmj-eddcm-single-price').hide();
                        $('.bpmj-eddcm-variable-prices').show();
                    } else {
                        $('.bpmj-eddcm-single-price').show();
                        $('.bpmj-eddcm-variable-prices').hide();
                    }
                });
            },
            variablePricesOpenModal: function () {
                $(document).on('click', '[data-action="edit-product-variable-prices"]', function (e) {
                    e.preventDefault();
                    Product_Variable_Prices.functions.modal('bpmj-eddcm-variable-prices-modal', bpmj_eddcm.courses.variable_prices_popup.title, bpmj_eddcm.courses.variable_prices_popup.placeholder, '', true, 'ultra-wide');
                    var post_id = $(this).data('postId');
                    var post_data = {
                        action: 'wpi_handler',
                        post_id: post_id
                    };
                    post_data[bpmj_eddcm.nonce_name] = bpmj_eddcm.nonce_value
                    $.ajax({
                        type: "POST",
                        data: post_data,
                        dataType: "json",
                        url: bpmj_eddcm.ajax + '?wpi_route=admin/edit_product/get_variable_prices',
                        success: function (response) {
                            var $placeholder = $('#bpmj-eddcm-variable-prices-placeholder');
                            $placeholder.html(response.html);
                            $placeholder.find('#edd_variable_price_fields').show();
                            $placeholder.find(".edd_repeatable_table tbody").sortable({
                                handle: '.edd_draghandle',
                                items: '.edd_repeatable_row',
                                opacity: 0.6,
                                cursor: 'move',
                                axis: 'y',
                                update: function () {
                                    var count = 0;
                                    $(this).find('tr').each(function () {
                                        $(this).find('input.edd_repeatable_index').each(function () {
                                            $(this).val(count);
                                        });
                                        count++;
                                    });
                                }
                            });
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log('Error: ' + jqXHR.responseJSON.error_message)
                        }
                    }).fail(function (data) {
                        if (window.console && window.console.log) {
                            console.log(data);
                        }
                    });
                });
            },
            variablePricesSave: function () {
                $(document).on('click', '[data-action="save-product-variable-prices"]', function (e) {
                    e.preventDefault();
                    if ('1' === $(this).data('saving')) {
                        return;
                    }
                    $(this).data('saving', '1');
                    var post_data = $('#bpmj-eddcm-variable-prices-placeholder').find(':input').serializeObject();
                    post_data.variable_sale_price_from_date = $('#variable_sale_price_from_date').val();
                    post_data.variable_sale_price_from_hour = $('#variable_sale_price_from_hour').val();
                    post_data.action = 'wpi_handler'
                    post_data[bpmj_eddcm.nonce_name] = bpmj_eddcm.nonce_value;
                    $(this).html('<span class="dashicons dashicons-update"></span>');
                    $.ajax({
                        type: "POST",
                        data: post_data,
                        dataType: "json",
                        url: bpmj_eddcm.ajax + '?wpi_route=admin/edit_product/save_variable_prices',
                        success: function (response) {
                            if (response) {
                                $('#bpmj-eddcm-variable-prices').html(response.variable_prices_summary_html);

                                if(!response.no_variable_prices){
                                    $('#sales_disabled').prop('disabled',false);
                                    $('#sales_disabled_notice').text('');
                                }

                                window.snackbar.show(response.message);

                            }
                            $('[data-action="close-modal"]').trigger('click');
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log('Error: ' + jqXHR.responseJSON.error_message)
                        }
                    }).fail(function (data) {
                        if (window.console && window.console.log) {
                            console.log(data);
                        }
                    });
                });
            },
            variablePricesValidator: function () {
                $(document).on('keyup change', '.bpmj-eddpc-access-time input[type=number]', function () {
                    let removeDashAndLetters = $(this).val().replace(/[^0-9]/g, "");
                    $(this).val(removeDashAndLetters);
                });

                $(document).on('keyup change', '.edd-sale-price-field, .edd-price-field', function () {
                    let removeDashAndLetters = $(this).val().replace(/[^0-9\.,]/g, "");
                    $(this).val(removeDashAndLetters);
                });
            },
        },

        functions: {
            modal: bpmj_eddcm_open_modal,
        },

    }
    $(document).ready(function () {
        Product_Variable_Prices.init();
    });
}(jQuery);

var addButtonsToWpiPopup = function (popupElement, buttons, popupScope) {
    if (! Array.isArray(buttons)) {
        return;
    }

    if (buttons.length === 0) {
        popupElement.find('.wpi-popup__content > .wpi-popup__footer').remove();
        return;
    }

    jQuery.each(buttons, function (index, b) {
        var button = jQuery.extend({
            type: 'default',
            text: '',
            action: function (e) {
                popupScope.close();
            },
        }, b);

        var buttonStyleClass = 'default' === button.type ? 'secondary' : 'main';
        var buttonClass = 'wpi-popup-buton-' + index;
        popupElement.find('.wpi-popup__footer').append(jQuery('<button class="wpi-button wpi-button--' + buttonStyleClass + ' ' + buttonClass + '">' + button.text + '</button>'));
        popupElement.find('.' + buttonClass).on('click', button.action);
    });
}

var createWpiPopup = function () {
    var popupElement = jQuery('#wpi-popup').clone();
    popupElement.removeAttr('id');
    popupElement.appendTo('body');
    popupElement.css('display', 'flex');
    return popupElement;
}

window.wpi_popup = {
    create: function () {
        var popupElement = null;

        return {
            /**
             * @param {string} content
             * @param {object[]} buttons
             * @param {string} buttons.text
             * @param {string} [buttons.type] Default button is gray button
             * @param {function} [buttons.action] Default action is close popup
             */
            open: function (
                content,
                buttons
            ) {
                popupElement = createWpiPopup();

                popupElement.find('.wpi-popup__core').html(
                    jQuery('<p class="wpi-popup__core-message">' + content + '</p>')
                );

                addButtonsToWpiPopup(popupElement, buttons, this);
            },
            /**
             * @param {string} url
             * @param {object} data
             * @param {string} requestType Values GET|POST
             * @param {object[]} buttons
             * @param {string} buttons.text
             * @param {string} [buttons.type] Default button is gray button
             * @param {function} [buttons.action] Default action is close popup
             * @param {string} response_property_with_content Which property from response contains data for popup
             */
            open_with_ajax: function (url, data, requestType, buttons, response_property_with_content) {
                this.open_with_loading();

                var popupScope = this;

                jQuery.ajax({
                    type: requestType,
                    data: data,
                    url: url,
                    success: function (response) {
                        var content = response;

                        if (response_property_with_content !== undefined) {
                            content = response[response_property_with_content];
                        }

                        popupScope.end_loading_with_success(content, buttons);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        popupScope.end_loading_with_error();
                    },
                })
            },

            open_with_loading: function (loadingMessage) {
                popupElement = createWpiPopup();
                popupElement.addClass('loading');

                popupElement.find('.wpi-popup__core').html(
                    jQuery('<p class="wpi-popup__core-message">' + loadingMessage + '</p>')
                );
            },
            end_loading_with_success: function (content, buttons) {
                popupElement.removeClass('loading');

                popupElement.find('.wpi-popup__core').html(
                    jQuery(content)
                );

                addButtonsToWpiPopup(popupElement, buttons, this);
            },
            end_loading_with_error: function () {
                popupElement.removeClass('loading');
                popupElement.find('.wpi-popup__core').html(
                    jQuery('<p class="wpi-popup__core-message">Coś poszło nie tak. Skontakuj się z administratorem!!...</p>')
                );

                addButtonsToWpiPopup(popupElement, [{text: "Ok!!"}], this);
            },
            /**
             * @param {boolean} remove Remove popup from DOM. Default: true
             */
            close: function (remove) {
                popupElement.css('display', 'none');

                if (remove !== false) {
                    this.remove();
                }
            },
            remove: function () {
                popupElement.remove();
            },

        }
    },
}

/**
 *
 * -----------------------------------------------------------
 *
 * Codestar Framework
 * A Lightweight and easy-to-use WordPress Options Framework
 *
 * -----------------------------------------------------------
 *
 */
;(function ( $, window, document, undefined ) {
  'use strict';

  $.CSFRAMEWORK = $.CSFRAMEWORK || {};

  // caching selector
  var $cs_body = $('body');

  // caching variables
  var cs_is_rtl  = $cs_body.hasClass('rtl');

  // ======================================================
  // CSFRAMEWORK TAB NAVIGATION
  // ------------------------------------------------------
  $.fn.CSFRAMEWORK_TAB_NAVIGATION = function() {
    return this.each(function() {

      var $this   = $(this),
          $nav    = $this.find('.cs-nav'),
          $reset  = $this.find('.cs-reset'),
          $expand = $this.find('.cs-expand-all');

      $nav.find('ul:first a').on('click', function (e) {

        e.preventDefault();

        var $el     = $(this),
            $next   = $el.next(),
            $target = $el.data('section');

        if( $next.is('ul') ) {

          $next.slideToggle( 'fast' );
          $el.closest('li').toggleClass('cs-tab-active');

        } else {

          $('#cs-tab-'+$target).show().siblings().hide();
          $nav.find('a').removeClass('cs-section-active');
          $el.addClass('cs-section-active');
          $reset.val($target);

        }

      });

      $expand.on('click', function (e) {
        e.preventDefault();
        $this.find('.cs-body').toggleClass('cs-show-all');
        $(this).find('.fa').toggleClass('fa-eye-slash' ).toggleClass('fa-eye');
      });

    });
  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK DEPENDENCY
  // ------------------------------------------------------
  $.CSFRAMEWORK.DEPENDENCY = function( el, param ) {

    // Access to jQuery and DOM versions of element
    var base     = this;
        base.$el = $(el);
        base.el  = el;

    base.init = function () {

      base.ruleset = $.deps.createRuleset();

      // required for shortcode attrs
      var cfg = {
        show: function( el ) {
          el.removeClass('hidden');
        },
        hide: function( el ) {
          el.addClass('hidden');
        },
        log: false,
        checkTargets: false
      };

      if( param !== undefined ) {
        base.depSub();
      } else {
        base.depRoot();
      }

      $.deps.enable( base.$el, base.ruleset, cfg );

    };

    base.depRoot = function() {

      base.$el.each( function() {

        $(this).find('[data-controller]').each( function() {

          var $this       = $(this),
              _controller = $this.data('controller').split('|'),
              _condition  = $this.data('condition').split('|'),
              _value      = $this.data('value').toString().split('|'),
              _rules      = base.ruleset;

          $.each(_controller, function(index, element) {

            var value     = _value[index] || '',
                condition = _condition[index] || _condition[0];

            _rules = _rules.createRule('[data-depend-id="'+ element +'"]', condition, value);
            _rules.include($this);

          });

        });

      });

    };

    base.depSub = function() {

      base.$el.each( function() {

        $(this).find('[data-sub-controller]').each( function() {

          var $this       = $(this),
              _controller = $this.data('sub-controller').split('|'),
              _condition  = $this.data('sub-condition').split('|'),
              _value      = $this.data('sub-value').toString().split('|'),
              _rules      = base.ruleset;

          $.each(_controller, function(index, element) {

            var value     = _value[index] || '',
                condition = _condition[index] || _condition[0];

            _rules = _rules.createRule('[data-sub-depend-id="'+ element +'"]', condition, value);
            _rules.include($this);

          });

        });

      });

    };


    base.init();
  };

  $.fn.CSFRAMEWORK_DEPENDENCY = function ( param ) {
    return this.each(function () {
      new $.CSFRAMEWORK.DEPENDENCY( this, param );
    });
  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK CHOSEN
  // ------------------------------------------------------
  $.fn.CSFRAMEWORK_CHOSEN = function() {
    return this.each(function() {
      $(this).chosen({allow_single_deselect: true, disable_search_threshold: 15, width: parseFloat( $(this).actual('width') + 25 ) +'px'});
    });
  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK IMAGE SELECTOR
  // ------------------------------------------------------
  $.fn.CSFRAMEWORK_IMAGE_SELECTOR = function() {
    return this.each(function() {

      $(this).find('label').on('click', function () {
        $(this).siblings().find('input').prop('checked', false);
      });

    });
  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK SORTER
  // ------------------------------------------------------
  $.fn.CSFRAMEWORK_SORTER = function() {
    return this.each(function() {

      var $this     = $(this),
          $enabled  = $this.find('.cs-enabled'),
          $disabled = $this.find('.cs-disabled');

      $enabled.sortable({
        connectWith: $disabled,
        placeholder: 'ui-sortable-placeholder',
        update: function( event, ui ){

          var $el = ui.item.find('input');

          if( ui.item.parent().hasClass('cs-enabled') ) {
            $el.attr('name', $el.attr('name').replace('disabled', 'enabled'));
          } else {
            $el.attr('name', $el.attr('name').replace('enabled', 'disabled'));
          }

        }
      });

      // avoid conflict
      $disabled.sortable({
        connectWith: $enabled,
        placeholder: 'ui-sortable-placeholder'
      });

    });
  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK MEDIA UPLOADER / UPLOAD
  // ------------------------------------------------------
  $.fn.CSFRAMEWORK_UPLOADER = function() {
    return this.each(function() {

      var $this  = $(this),
          $add   = $this.find('.cs-add'),
          $input = $this.find('input'),
          wp_media_frame;

      $add.on('click', function( e ) {

        e.preventDefault();

        // Check if the `wp.media.gallery` API exists.
        if ( typeof wp === 'undefined' || ! wp.media || ! wp.media.gallery ) {
          return;
        }

        // If the media frame already exists, reopen it.
        if ( wp_media_frame ) {
          wp_media_frame.open();
          return;
        }

        // Create the media frame.
        wp_media_frame = wp.media({

          // Set the title of the modal.
          title: $add.data('frame-title'),

          // Tell the modal to show only images.
          library: {
            type: $add.data('upload-type')
          },

          // Customize the submit button.
          button: {
            // Set the text of the button.
            text: $add.data('insert-title'),
          }

        });

        // When an image is selected, run a callback.
        wp_media_frame.on( 'select', function() {

          // Grab the selected attachment.
          var attachment = wp_media_frame.state().get('selection').first();
          $input.val( attachment.attributes.url ).trigger('change');

        });

        // Finally, open the modal.
        wp_media_frame.open();

      });

    });

  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK IMAGE UPLOADER
  // ------------------------------------------------------
  $.fn.CSFRAMEWORK_IMAGE_UPLOADER = function() {
    return this.each(function() {

      var $this    = $(this),
          $add     = $this.find('.cs-add'),
          $preview = $this.find('.cs-image-preview'),
          $remove  = $this.find('.cs-remove'),
          $input   = $this.find('input'),
          $img     = $this.find('img'),
          wp_media_frame;

      $add.on('click', function( e ) {

        e.preventDefault();

        // Check if the `wp.media.gallery` API exists.
        if ( typeof wp === 'undefined' || ! wp.media || ! wp.media.gallery ) {
          return;
        }

        // If the media frame already exists, reopen it.
        if ( wp_media_frame ) {
          wp_media_frame.open();
          return;
        }

        // Create the media frame.
        wp_media_frame = wp.media({
          library: {
            type: 'image'
          }
        });

        // When an image is selected, run a callback.
        wp_media_frame.on( 'select', function() {

          var attachment = wp_media_frame.state().get('selection').first().attributes;
          var thumbnail = ( typeof attachment.sizes !== 'undefined' && typeof attachment.sizes.thumbnail !== 'undefined' ) ? attachment.sizes.thumbnail.url : attachment.url;

          $preview.removeClass('hidden');
          $img.attr('src', thumbnail);
          $input.val( attachment.id ).trigger('change');

        });

        // Finally, open the modal.
        wp_media_frame.open();

      });

      // Remove image
      $remove.on('click', function( e ) {
        e.preventDefault();
        $input.val('').trigger('change');
        $preview.addClass('hidden');
      });

    });

  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK IMAGE GALLERY
  // ------------------------------------------------------
  $.fn.CSFRAMEWORK_IMAGE_GALLERY = function() {
    return this.each(function() {

      var $this   = $(this),
          $edit   = $this.find('.cs-edit'),
          $remove = $this.find('.cs-remove'),
          $list   = $this.find('ul'),
          $input  = $this.find('input'),
          $img    = $this.find('img'),
          wp_media_frame;

      $this.on('click', '.cs-add, .cs-edit', function( e ) {

        var $el   = $(this),
            ids   = $input.val(),
            what  = ( $el.hasClass('cs-edit') ) ? 'edit' : 'add',
            state = ( what === 'add' && !ids.length ) ? 'gallery' : 'gallery-edit';

        e.preventDefault();

        // Check if the `wp.media.gallery` API exists.
        if ( typeof wp === 'undefined' || ! wp.media || ! wp.media.gallery ) { return; }

         // Open media with state
        if( state === 'gallery' ) {

          wp_media_frame = wp.media({
            library: {
              type: 'image'
            },
            frame: 'post',
            state: 'gallery',
            multiple: true
          });

          wp_media_frame.open();

        } else {

          wp_media_frame = wp.media.gallery.edit( '[gallery ids="'+ ids +'"]' );

          if( what === 'add' ) {
            wp_media_frame.setState('gallery-library');
          }

        }

        // Media Update
        wp_media_frame.on( 'update', function( selection ) {

          $list.empty();

          var selectedIds = selection.models.map( function ( attachment ) {

            var item  = attachment.toJSON();
            var thumb = ( typeof item.sizes.thumbnail !== 'undefined' ) ? item.sizes.thumbnail.url : item.url;

            $list.append('<li><img src="'+ thumb +'"></li>');

            return item.id;

          });

          $input.val( selectedIds.join( ',' ) ).trigger('change');
          $remove.removeClass('hidden');
          $edit.removeClass('hidden');

        });

      });

      // Remove image
      $remove.on('click', function( e ) {
        e.preventDefault();
        $list.empty();
        $input.val('').trigger('change');
        $remove.addClass('hidden');
        $edit.addClass('hidden');
      });

    });

  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK TYPOGRAPHY
  // ------------------------------------------------------
  $.fn.CSFRAMEWORK_TYPOGRAPHY = function() {
    return this.each( function() {

      var typography      = $(this),
          family_select   = typography.find('.cs-typo-family'),
          variants_select = typography.find('.cs-typo-variant'),
          typography_type = typography.find('.cs-typo-font');

      family_select.on('change', function() {

        var _this     = $(this),
            _type     = _this.find(':selected').data('type') || 'custom',
            _variants = _this.find(':selected').data('variants');

        if( variants_select.length ) {

          variants_select.find('option').remove();

          $.each( _variants.split('|'), function( key, text ) {
            variants_select.append('<option value="'+ text +'">'+ text +'</option>');
          });

          variants_select.find('option[value="regular"]').attr('selected', 'selected').trigger('chosen:updated');

        }

        typography_type.val(_type);

      });

    });
  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK GROUP
  // ------------------------------------------------------
  $.fn.CSFRAMEWORK_GROUP = function() {
    return this.each(function() {

      var _this           = $(this),
          field_groups    = _this.find('.cs-groups'),
          accordion_group = _this.find('.cs-accordion'),
          clone_group     = _this.find('.cs-group:first').clone();

      if ( accordion_group.length ) {
        accordion_group.accordion({
          header: '.cs-group-title',
          collapsible : true,
          active: false,
          animate: 250,
          heightStyle: 'content',
          icons: {
            'header': 'dashicons dashicons-arrow-right',
            'activeHeader': 'dashicons dashicons-arrow-down'
          },
          beforeActivate: function( event, ui ) {
            $(ui.newPanel).CSFRAMEWORK_DEPENDENCY( 'sub' );
          }
        });
      }

      field_groups.sortable({
        axis: 'y',
        handle: '.cs-group-title',
        helper: 'original',
        cursor: 'move',
        placeholder: 'widget-placeholder',
        start: function( event, ui ) {
          var inside = ui.item.children('.cs-group-content');
          if ( inside.css('display') === 'block' ) {
            inside.hide();
            field_groups.sortable('refreshPositions');
          }
        },
        stop: function( event, ui ) {
          ui.item.children( '.cs-group-title' ).triggerHandler( 'focusout' );
          accordion_group.accordion({ active:false });
        }
      });

      var i = 0;
      $('.cs-add-group', _this).on('click', function( e ) {

        e.preventDefault();

        clone_group.find('input, select, textarea').each( function () {
          this.name = this.name.replace(/\[(\d+)\]/,function(string, id) {
            return '[' + (parseInt(id,10)+1) + ']';
          });
        });

        var cloned = clone_group.clone().removeClass('hidden');
        field_groups.append(cloned);

        if ( accordion_group.length ) {
          field_groups.accordion('refresh');
          field_groups.accordion({ active: cloned.index() });
        }

        field_groups.find('input, select, textarea').each( function () {
          this.name = this.name.replace('[_nonce]', '');
        });

        // run all field plugins
        cloned.CSFRAMEWORK_DEPENDENCY( 'sub' );
        cloned.CSFRAMEWORK_RELOAD_PLUGINS();

        i++;

      });

      field_groups.on('click', '.cs-remove-group', function(e) {
        e.preventDefault();
        $(this).closest('.cs-group').remove();
      });

    });
  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK RESET CONFIRM
  // ------------------------------------------------------
  $.fn.CSFRAMEWORK_CONFIRM = function() {
    return this.each( function() {
      $(this).on('click', function( e ) {
        if ( !confirm('Are you sure?') ) {
          e.preventDefault();
        }
      });
    });
  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK SAVE OPTIONS
  // ------------------------------------------------------
  $.fn.CSFRAMEWORK_SAVE = function() {
    return this.each( function() {

      var $this  = $(this),
          $text  = $this.data('save'),
          $value = $this.val(),
          $ajax  = $('#cs-save-ajax');

      $(document).on('keydown', function(event) {
        if (event.ctrlKey || event.metaKey) {
          if( String.fromCharCode(event.which).toLowerCase() === 's' ) {
            event.preventDefault();
            $this.trigger('click');
          }
        }
      });

      $this.on('click', function ( e ) {

        if( $ajax.length ) {

          if( typeof tinyMCE === 'object' ) {
            tinyMCE.triggerSave();
          }

          $this.prop('disabled', true).attr('value', $text);

          var serializedOptions = $('#csframework_form').serialize();

          $.post( 'options.php', serializedOptions ).error( function() {
            alert('Error, Please try again.');
          }).success( function() {
            $this.prop('disabled', false).attr('value', $value);
            $ajax.hide().fadeIn().delay(250).fadeOut();
          });

          e.preventDefault();

        } else {

          $this.addClass('disabled').attr('value', $text);

        }

      });

    });
  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK SAVE TAXONOMY CLEAR FORM ELEMENTS
  // ------------------------------------------------------
  $.fn.CSFRAMEWORK_TAXONOMY = function() {
    return this.each( function() {

      var $this   = $(this),
          $parent = $this.parent();

      // Only works in add-tag form
      if( $parent.attr('id') === 'addtag' ) {

        var $submit  = $parent.find('#submit'),
            $name    = $parent.find('#tag-name'),
            $wrap    = $parent.find('.cs-framework'),
            $clone   = $wrap.find('.cs-element').clone(),
            $list    = $('#the-list'),
            flooding = false;

        $submit.on( 'click', function() {

          if( !flooding ) {

            $list.on( 'DOMNodeInserted', function() {

              if( flooding ) {

                $wrap.empty();
                $wrap.html( $clone );
                $clone = $clone.clone();

                $wrap.CSFRAMEWORK_RELOAD_PLUGINS();
                $wrap.CSFRAMEWORK_DEPENDENCY();

                flooding = false;

              }

            });

          }

          flooding = true;

        });

      }

    });
  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK UI DIALOG OVERLAY HELPER
  // ------------------------------------------------------
  if( typeof $.widget !== 'undefined' && typeof $.ui !== 'undefined' && typeof $.ui.dialog !== 'undefined' ) {
    $.widget( 'ui.dialog', $.ui.dialog, {
        _createOverlay: function() {
          this._super();
          if ( !this.options.modal ) { return; }
          this._on(this.overlay, {click: 'close'});
        }
      }
    );
  }

  // ======================================================
  // CSFRAMEWORK ICONS MANAGER
  // ------------------------------------------------------
  $.CSFRAMEWORK.ICONS_MANAGER = function() {

    var base   = this,
        onload = true,
        $parent;

    base.init = function () {

      $cs_body.on('click', '.cs-icon-add', function( e ) {

        e.preventDefault();

        var $this   = $(this),
            $dialog = $('#cs-icon-dialog'),
            $load   = $dialog.find('.cs-dialog-load'),
            $select = $dialog.find('.cs-dialog-select'),
            $insert = $dialog.find('.cs-dialog-insert'),
            $search = $dialog.find('.cs-icon-search');

        // set parent
        $parent = $this.closest('.cs-icon-select');

        // open dialog
        $dialog.dialog({
          width: 850,
          height: 700,
          modal: true,
          resizable: false,
          closeOnEscape: true,
          position: {my: 'center', at: 'center', of: window},
          open: function() {

            // fix scrolling
            $cs_body.addClass('cs-icon-scrolling');

            // fix button for VC
            $('.ui-dialog-titlebar-close').addClass('ui-button');

            // set viewpoint
            $(window).on('resize', function () {

              var height      = $(window).height(),
                  load_height = Math.floor( height - 237 ),
                  set_height  = Math.floor( height - 125 );

              $dialog.dialog('option', 'height', set_height).parent().css('max-height', set_height);
              $dialog.css('overflow', 'auto');
              $load.css( 'height', load_height );

            }).resize();

          },
          close: function() {
            $cs_body.removeClass('cs-icon-scrolling');
          }
        });

        // load icons
        if( onload ) {

          $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
              action: 'cs-get-icons'
            },
            success: function( content ) {

              $load.html( content );
              onload = false;

              $load.on('click', 'a', function( e ) {

                e.preventDefault();

                var icon = $(this).data('cs-icon');

                $parent.find('i').removeAttr('class').addClass(icon);
                $parent.find('input').val(icon).trigger('change');
                $parent.find('.cs-icon-preview').removeClass('hidden');
                $parent.find('.cs-icon-remove').removeClass('hidden');
                $dialog.dialog('close');

              });

              $search.keyup( function(){

                var value  = $(this).val(),
                    $icons = $load.find('a');

                $icons.each(function() {

                  var $ico = $(this);

                  if ( $ico.data('cs-icon').search( new RegExp( value, 'i' ) ) < 0 ) {
                    $ico.hide();
                  } else {
                    $ico.show();
                  }

                });

              });

              $load.find('.cs-icon-tooltip').cstooltip({html:true, placement:'top', container:'body'});

            }
          });

        }

      });

      $cs_body.on('click', '.cs-icon-remove', function( e ) {

        e.preventDefault();

        var $this   = $(this),
            $parent = $this.closest('.cs-icon-select');

        $parent.find('.cs-icon-preview').addClass('hidden');
        $parent.find('input').val('').trigger('change');
        $this.addClass('hidden');

      });

    };

    // run initializer
    base.init();
  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK SHORTCODE MANAGER
  // ------------------------------------------------------
  $.CSFRAMEWORK.SHORTCODE_MANAGER = function() {

    var base = this, deploy_atts;

    base.init = function () {

      var $dialog          = $('#cs-shortcode-dialog'),
          $insert          = $dialog.find('.cs-dialog-insert'),
          $shortcodeload   = $dialog.find('.cs-dialog-load'),
          $selector        = $dialog.find('.cs-dialog-select'),
          shortcode_target = false,
          shortcode_name,
          shortcode_view,
          shortcode_clone,
          $shortcode_button,
          editor_id;

      $cs_body.on('click', '.cs-shortcode', function( e ) {

        e.preventDefault();

        // init chosen
        $selector.CSFRAMEWORK_CHOSEN();

        $shortcode_button = $(this);
        shortcode_target  = $shortcode_button.hasClass('cs-shortcode-textarea');
        editor_id         = $shortcode_button.data('editor-id');

        $dialog.dialog({
          width: 850,
          height: 700,
          modal: true,
          resizable: false,
          closeOnEscape: true,
          position: {my: 'center', at: 'center', of: window},
          open: function() {

            // fix scrolling
            $cs_body.addClass('cs-shortcode-scrolling');

            // fix button for VC
            $('.ui-dialog-titlebar-close').addClass('ui-button');

            // set viewpoint
            $(window).on('resize', function () {

              var height      = $(window).height(),
                  load_height = Math.floor( height - 281 ),
                  set_height  = Math.floor( height - 125 );

              $dialog.dialog('option', 'height', set_height).parent().css('max-height', set_height);
              $dialog.css('overflow', 'auto');
              $shortcodeload.css( 'height', load_height );

            }).resize();

          },
          close: function() {
            shortcode_target = false;
            $cs_body.removeClass('cs-shortcode-scrolling');
          }
        });

      });

      $selector.on( 'change', function() {

        var $elem_this     = $(this);
            shortcode_name = $elem_this.val();
            shortcode_view = $elem_this.find(':selected').data('view');

        // check val
        if( shortcode_name.length ){

          $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
              action: 'cs-get-shortcode',
              shortcode: shortcode_name
            },
            success: function( content ) {

              $shortcodeload.html( content );
              $insert.parent().removeClass('hidden');

              shortcode_clone = $('.cs-shortcode-clone', $dialog).clone();

              $shortcodeload.CSFRAMEWORK_DEPENDENCY();
              $shortcodeload.CSFRAMEWORK_DEPENDENCY('sub');
              $shortcodeload.CSFRAMEWORK_RELOAD_PLUGINS();

            }
          });

        } else {

          $insert.parent().addClass('hidden');
          $shortcodeload.html('');

        }

      });

      $insert.on('click', function ( e ) {

        e.preventDefault();

        var send_to_shortcode = '',
            ruleAttr          = 'data-atts',
            cloneAttr         = 'data-clone-atts',
            cloneID           = 'data-clone-id';

        switch ( shortcode_view ){

          case 'contents':

            $('[' + ruleAttr + ']', '.cs-dialog-load').each( function() {
              var _this = $(this), _atts = _this.data('atts');
              send_to_shortcode += '['+_atts+']';
              send_to_shortcode += _this.val();
              send_to_shortcode += '[/'+_atts+']';
            });

          break;

          case 'clone':

            send_to_shortcode += '[' + shortcode_name; // begin: main-shortcode

            // main-shortcode attributes
            $('[' + ruleAttr + ']', '.cs-dialog-load .cs-element:not(.hidden)').each( function() {
              var _this_main = $(this), _this_main_atts = _this_main.data('atts');
              send_to_shortcode += base.validate_atts( _this_main_atts, _this_main );  // validate empty atts
            });

            send_to_shortcode += ']'; // end: main-shortcode attributes

            // multiple-shortcode each
            $('[' + cloneID + ']', '.cs-dialog-load').each( function() {

                var _this_clone = $(this),
                    _clone_id   = _this_clone.data('clone-id');

                send_to_shortcode += '[' + _clone_id; // begin: multiple-shortcode

                // multiple-shortcode attributes
                $('[' + cloneAttr + ']', _this_clone.find('.cs-element').not('.hidden') ).each( function() {

                  var _this_multiple = $(this), _atts_multiple = _this_multiple.data('clone-atts');

                  // is not attr content, add shortcode attribute else write content and close shortcode tag
                  if( _atts_multiple !== 'content' ){
                    send_to_shortcode += base.validate_atts( _atts_multiple, _this_multiple ); // validate empty atts
                  }else if ( _atts_multiple === 'content' ){
                    send_to_shortcode += ']';
                    send_to_shortcode += _this_multiple.val();
                    send_to_shortcode += '[/'+_clone_id+'';
                  }
                });

                send_to_shortcode += ']'; // end: multiple-shortcode

            });

            send_to_shortcode += '[/' + shortcode_name + ']'; // end: main-shortcode

          break;

          case 'clone_duplicate':

            // multiple-shortcode each
            $('[' + cloneID + ']', '.cs-dialog-load').each( function() {

              var _this_clone = $(this),
                  _clone_id   = _this_clone.data('clone-id');

              send_to_shortcode += '[' + _clone_id; // begin: multiple-shortcode

              // multiple-shortcode attributes
              $('[' + cloneAttr + ']', _this_clone.find('.cs-element').not('.hidden') ).each( function() {

                var _this_multiple = $(this),
                    _atts_multiple = _this_multiple.data('clone-atts');


                // is not attr content, add shortcode attribute else write content and close shortcode tag
                if( _atts_multiple !== 'content' ){
                  send_to_shortcode += base.validate_atts( _atts_multiple, _this_multiple ); // validate empty atts
                }else if ( _atts_multiple === 'content' ){
                  send_to_shortcode += ']';
                  send_to_shortcode += _this_multiple.val();
                  send_to_shortcode += '[/'+_clone_id+'';
                }
              });

              send_to_shortcode += ']'; // end: multiple-shortcode

            });

          break;

          default:

            send_to_shortcode += '[' + shortcode_name;

            $('[' + ruleAttr + ']', '.cs-dialog-load .cs-element:not(.hidden)').each( function() {

              var _this = $(this), _atts = _this.data('atts');

              // is not attr content, add shortcode attribute else write content and close shortcode tag
              if( _atts !== 'content' ){
                send_to_shortcode += base.validate_atts( _atts, _this ); // validate empty atts
              }else if ( _atts === 'content' ){
                send_to_shortcode += ']';
                send_to_shortcode += _this.val();
                send_to_shortcode += '[/'+shortcode_name+'';
              }

            });

            send_to_shortcode += ']';

          break;

        }

        if( shortcode_target ) {
          var $textarea = $shortcode_button.next();
          $textarea.val( base.insertAtChars( $textarea, send_to_shortcode ) ).trigger('change');
        } else {
          base.send_to_editor( send_to_shortcode, editor_id );
        }

        deploy_atts = null;

        $dialog.dialog( 'close' );

      });

      // cloner button
      var cloned = 0;
      $dialog.on('click', '#shortcode-clone-button', function( e ) {

        e.preventDefault();

        // clone from cache
        var cloned_el = shortcode_clone.clone().hide();

        cloned_el.find('input:radio').attr('name', '_nonce_' + cloned);

        $('.cs-shortcode-clone:last').after( cloned_el );

        // add - remove effects
        cloned_el.slideDown(100);

        cloned_el.find('.cs-remove-clone').show().on('click', function( e ) {

          cloned_el.slideUp(100, function(){ cloned_el.remove(); });
          e.preventDefault();

        });

        // reloadPlugins
        cloned_el.CSFRAMEWORK_DEPENDENCY('sub');
        cloned_el.CSFRAMEWORK_RELOAD_PLUGINS();
        cloned++;

      });

    };

    base.validate_atts = function( _atts, _this ) {

      var el_value;

      if( _this.data('check') !== undefined && deploy_atts === _atts ) { return ''; }

      deploy_atts = _atts;

      if ( _this.closest('.pseudo-field').hasClass('hidden') === true ) { return ''; }
      if ( _this.hasClass('pseudo') === true ) { return ''; }

      if( _this.is(':checkbox') || _this.is(':radio') ) {
        el_value = _this.is(':checked') ? _this.val() : '';
      } else {
        el_value = _this.val();
      }

      if( _this.data('check') !== undefined ) {
        el_value = _this.closest('.cs-element').find('input:checked').map( function() {
         return $(this).val();
        }).get();
      }

      if( el_value !== null && el_value !== undefined && el_value !== '' && el_value.length !== 0 ) {
        return ' ' + _atts + '="' + el_value + '"';
      }

      return '';

    };

    base.insertAtChars = function( _this, currentValue ) {

      var obj = ( typeof _this[0].name !== 'undefined' ) ? _this[0] : _this;

      if ( obj.value.length && typeof obj.selectionStart !== 'undefined' ) {
        obj.focus();
        return obj.value.substring( 0, obj.selectionStart ) + currentValue + obj.value.substring( obj.selectionEnd, obj.value.length );
      } else {
        obj.focus();
        return currentValue;
      }

    };

    base.send_to_editor = function( html, editor_id ) {

      var tinymce_editor;

      if ( typeof tinymce !== 'undefined' ) {
        tinymce_editor = tinymce.get( editor_id );
      }

      if ( tinymce_editor && !tinymce_editor.isHidden() ) {
        tinymce_editor.execCommand( 'mceInsertContent', false, html );
      } else {
        var $editor = $('#'+editor_id);
        $editor.val( base.insertAtChars( $editor, html ) ).trigger('change');
      }

    };

    // run initializer
    base.init();
  };
  // ======================================================

  // ======================================================
  // CSFRAMEWORK COLORPICKER
  // ------------------------------------------------------
  if( typeof Color === 'function' ) {

    // adding alpha support for Automattic Color.js toString function.
    Color.fn.toString = function () {

      // check for alpha
      if ( this._alpha < 1 ) {
        return this.toCSS('rgba', this._alpha).replace(/\s+/g, '');
      }

      var hex = parseInt( this._color, 10 ).toString( 16 );

      if ( this.error ) { return ''; }

      // maybe left pad it
      if ( hex.length < 6 ) {
        for (var i = 6 - hex.length - 1; i >= 0; i--) {
          hex = '0' + hex;
        }
      }

      return '#' + hex;

    };

  }

  $.CSFRAMEWORK.PARSE_COLOR_VALUE = function( val ) {

    var value = val.replace(/\s+/g, ''),
        alpha = ( value.indexOf('rgba') !== -1 ) ? parseFloat( value.replace(/^.*,(.+)\)/, '$1') * 100 ) : 100,
        rgba  = ( alpha < 100 ) ? true : false;

    return { value: value, alpha: alpha, rgba: rgba };

  };

  $.fn.CSFRAMEWORK_COLORPICKER = function() {

    return this.each(function() {

      var $this = $(this);

      // check for rgba enabled/disable
      if( $this.data('rgba') !== false ) {

        // parse value
        var picker = $.CSFRAMEWORK.PARSE_COLOR_VALUE( $this.val() );

        // wpColorPicker core
        $this.wpColorPicker({

          // wpColorPicker: clear
          clear: function() {
            $this.trigger('keyup');
          },

          // wpColorPicker: change
          change: function( event, ui ) {

            var ui_color_value = ui.color.toString();

            // update checkerboard background color
            $this.closest('.wp-picker-container').find('.cs-alpha-slider-offset').css('background-color', ui_color_value);
            $this.val(ui_color_value).trigger('change');

          },

          // wpColorPicker: create
          create: function() {

            // set variables for alpha slider
            var a8cIris       = $this.data('a8cIris'),
                $container    = $this.closest('.wp-picker-container'),

                // appending alpha wrapper
                $alpha_wrap   = $('<div class="cs-alpha-wrap">' +
                                  '<div class="cs-alpha-slider"></div>' +
                                  '<div class="cs-alpha-slider-offset"></div>' +
                                  '<div class="cs-alpha-text"></div>' +
                                  '</div>').appendTo( $container.find('.wp-picker-holder') ),

                $alpha_slider = $alpha_wrap.find('.cs-alpha-slider'),
                $alpha_text   = $alpha_wrap.find('.cs-alpha-text'),
                $alpha_offset = $alpha_wrap.find('.cs-alpha-slider-offset');

            // alpha slider
            $alpha_slider.slider({

              // slider: slide
              slide: function( event, ui ) {

                var slide_value = parseFloat( ui.value / 100 );

                // update iris data alpha && wpColorPicker color option && alpha text
                a8cIris._color._alpha = slide_value;
                $this.wpColorPicker( 'color', a8cIris._color.toString() );
                $alpha_text.text( ( slide_value < 1 ? slide_value : '' ) );

              },

              // slider: create
              create: function() {

                var slide_value = parseFloat( picker.alpha / 100 ),
                    alpha_text_value = slide_value < 1 ? slide_value : '';

                // update alpha text && checkerboard background color
                $alpha_text.text(alpha_text_value);
                $alpha_offset.css('background-color', picker.value);

                // wpColorPicker clear for update iris data alpha && alpha text && slider color option
                $container.on('click', '.wp-picker-clear', function() {

                  a8cIris._color._alpha = 1;
                  $alpha_text.text('').trigger('change');
                  $alpha_slider.slider('option', 'value', 100).trigger('slide');

                });

                // wpColorPicker default button for update iris data alpha && alpha text && slider color option
                $container.on('click', '.wp-picker-default', function() {

                  var default_picker = $.CSFRAMEWORK.PARSE_COLOR_VALUE( $this.data('default-color') ),
                      default_value  = parseFloat( default_picker.alpha / 100 ),
                      default_text   = default_value < 1 ? default_value : '';

                  a8cIris._color._alpha = default_value;
                  $alpha_text.text(default_text);
                  $alpha_slider.slider('option', 'value', default_picker.alpha).trigger('slide');

                });

                // show alpha wrapper on click color picker button
                $container.on('click', '.wp-color-result', function() {
                  $alpha_wrap.toggle();
                });

                // hide alpha wrapper on click body
                $cs_body.on( 'click.wpcolorpicker', function() {
                  $alpha_wrap.hide();
                });

              },

              // slider: options
              value: picker.alpha,
              step: 1,
              min: 1,
              max: 100

            });
          }

        });

      } else {

        // wpColorPicker default picker
        $this.wpColorPicker({
          clear: function() {
            $this.trigger('keyup');
          },
          change: function( event, ui ) {
            $this.val(ui.color.toString()).trigger('change');
          }
        });

      }

    });

  };
  // ======================================================

  // ======================================================
  // ON WIDGET-ADDED RELOAD FRAMEWORK PLUGINS
  // ------------------------------------------------------
  $.CSFRAMEWORK.WIDGET_RELOAD_PLUGINS = function() {
    $(document).on('widget-added widget-updated', function( event, $widget ) {
      $widget.CSFRAMEWORK_RELOAD_PLUGINS();
      $widget.CSFRAMEWORK_DEPENDENCY();
    });
  };

  // ======================================================
  // TOOLTIP HELPER
  // ------------------------------------------------------
  $.fn.CSFRAMEWORK_TOOLTIP = function() {
    return this.each(function() {
      var placement = ( cs_is_rtl ) ? 'right' : 'left';
      $(this).cstooltip({html:true, placement:placement, container:'body'});
    });
  };

  // ======================================================
  // RELOAD FRAMEWORK PLUGINS
  // ------------------------------------------------------
  $.fn.CSFRAMEWORK_RELOAD_PLUGINS = function() {
    return this.each(function() {
      $('.chosen', this).CSFRAMEWORK_CHOSEN();
      $('.cs-field-image-select', this).CSFRAMEWORK_IMAGE_SELECTOR();
      $('.cs-field-image', this).CSFRAMEWORK_IMAGE_UPLOADER();
      $('.cs-field-gallery', this).CSFRAMEWORK_IMAGE_GALLERY();
      $('.cs-field-sorter', this).CSFRAMEWORK_SORTER();
      $('.cs-field-upload', this).CSFRAMEWORK_UPLOADER();
      $('.cs-field-typography', this).CSFRAMEWORK_TYPOGRAPHY();
      $('.cs-field-color-picker', this).CSFRAMEWORK_COLORPICKER();
      $('.cs-help', this).CSFRAMEWORK_TOOLTIP();
    });
  };

  // ======================================================
  // JQUERY DOCUMENT READY
  // ------------------------------------------------------
  $(document).ready( function() {
    $('.cs-framework').CSFRAMEWORK_TAB_NAVIGATION();
    $('.cs-reset-confirm, .cs-import-backup').CSFRAMEWORK_CONFIRM();
    $('.cs-content, .wp-customizer, .widget-content, .cs-taxonomy').CSFRAMEWORK_DEPENDENCY();
    $('.cs-field-group').CSFRAMEWORK_GROUP();
    $('.cs-save').CSFRAMEWORK_SAVE();
    $('.cs-taxonomy').CSFRAMEWORK_TAXONOMY();
    $('.cs-framework, #widgets-right').CSFRAMEWORK_RELOAD_PLUGINS();
    $.CSFRAMEWORK.ICONS_MANAGER();
    $.CSFRAMEWORK.SHORTCODE_MANAGER();
    $.CSFRAMEWORK.WIDGET_RELOAD_PLUGINS();
  });

})( jQuery, window, document );
