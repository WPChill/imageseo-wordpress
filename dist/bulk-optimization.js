/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/dist/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./app/javascripts/bulk-optimization.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./app/javascripts/bulk-optimization.js":
/*!**********************************************!*\
  !*** ./app/javascripts/bulk-optimization.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("document.addEventListener('DOMContentLoaded', function() {\n\tconst $ = jQuery\n\n\tif (IMAGESEO_ATTACHMENTS.length === 0) {\n\t\treturn\n\t}\n\n\tdocument\n\t\t.querySelector('#imageseo-bulk-reports')\n\t\t.addEventListener('click', function(e) {\n\t\t\te.preventDefault()\n\t\t\tdocument.querySelector('#imageseo-percent-bulk').style.display =\n\t\t\t\t'block'\n\t\t\t$(this).prop('disabled', true)\n\t\t\t$('span', $(this)).hide()\n\t\t\t$('.imageseo-loading', $(this)).show()\n\t\t\t$('#imageseo-total-already-reports span').text(0)\n\t\t\tlaunchReportImages(0)\n\t\t})\n\n\tconst getPercent = (current, total) => {\n\t\tif (current > total) {\n\t\t\treturn 100\n\t\t} else {\n\t\t\treturn Math.round((current * 100) / total)\n\t\t}\n\t}\n\n\tconst setPercentLoader = (current, total) => {\n\t\tconst percent = getPercent(current, total)\n\t\tconst el = document.querySelector(\n\t\t\t'#imageseo-percent-bulk .imageseo-percent--item'\n\t\t)\n\t\tel.style.width = `${percent}%`\n\t\tel.textContent = `${percent}% (${current}/${total})`\n\t}\n\n\tconst ReportItem = ({\n\t\tdashicons,\n\t\tsrc,\n\t\talt_generate,\n\t\tfile = ''\n\t}) => `<div class=\"imageseo-reports-body-item\">\n\t\t<div class=\"imageseo-reports--status\"><span class=\"dashicons dashicons-${dashicons}\"></span></div>\n\t\t<div class=\"imageseo-reports--image\"><div class=\"imageseo-reports--image-itm\" style=\"background-image:url('${file}')\"></div></div>\n\t\t<div class=\"imageseo-reports--src\">${src}</div>\n\t\t<div class=\"imageseo-reports--alt\">${alt_generate}</div>\n\t</div>`\n\n\tfunction launchReportImages(current) {\n\t\tconst total = IMAGESEO_ATTACHMENTS.length\n\t\tif (current > total) {\n\t\t\tfinishReportImages()\n\t\t\treturn\n\t\t}\n\n\t\tif (typeof IMAGESEO_ATTACHMENTS[current] === 'undefined') {\n\t\t\tcurrent++\n\t\t\tlaunchReportImages(current)\n\t\t\treturn\n\t\t}\n\n\t\t$.post(\n\t\t\t{\n\t\t\t\turl: ajaxurl,\n\t\t\t\tsuccess: function(res) {\n\t\t\t\t\tlet txt = `Attachment ID : ${IMAGESEO_ATTACHMENTS[current]}`\n\t\t\t\t\tif (res.data && res.data.src) {\n\t\t\t\t\t\ttxt = res.data.src\n\t\t\t\t\t}\n\t\t\t\t\tcurrent++\n\t\t\t\t\tsetPercentLoader(current, total)\n\t\t\t\t\tif (res.success) {\n\t\t\t\t\t\t$(\n\t\t\t\t\t\t\t'#imageseo-reports-js .imageseo-reports-body'\n\t\t\t\t\t\t).prepend(\n\t\t\t\t\t\t\tReportItem({\n\t\t\t\t\t\t\t\t...res.data,\n\t\t\t\t\t\t\t\tsrc: txt,\n\t\t\t\t\t\t\t\tdashicons: 'yes'\n\t\t\t\t\t\t\t})\n\t\t\t\t\t\t)\n\t\t\t\t\t} else {\n\t\t\t\t\t\t$(\n\t\t\t\t\t\t\t'#imageseo-reports-js .imageseo-reports-body'\n\t\t\t\t\t\t).prepend(\n\t\t\t\t\t\t\tReportItem({\n\t\t\t\t\t\t\t\t...res.data,\n\t\t\t\t\t\t\t\tsrc: txt,\n\t\t\t\t\t\t\t\tdashicons: 'no'\n\t\t\t\t\t\t\t})\n\t\t\t\t\t\t)\n\t\t\t\t\t}\n\t\t\t\t\t$('#imageseo-total-already-reports span').text(current)\n\t\t\t\t\tlaunchReportImages(current)\n\t\t\t\t},\n\t\t\t\terror: function(res) {\n\t\t\t\t\t$('#imageseo-reports-js .imageseo-reports-body').prepend(\n\t\t\t\t\t\tReportItem({\n\t\t\t\t\t\t\tsrc: `Attachment ID: ${\n\t\t\t\t\t\t\t\tIMAGESEO_ATTACHMENTS[current]\n\t\t\t\t\t\t\t}`,\n\t\t\t\t\t\t\talt_generate: '',\n\t\t\t\t\t\t\tdashicons: 'no'\n\t\t\t\t\t\t})\n\t\t\t\t\t)\n\t\t\t\t\tcurrent++\n\t\t\t\t\tsetPercentLoader(current, total)\n\t\t\t\t\t$('#imageseo-total-already-reports span').text(current)\n\t\t\t\t\tlaunchReportImages(current)\n\t\t\t\t}\n\t\t\t},\n\t\t\t{\n\t\t\t\taction: 'imageseo_report_attachment',\n\t\t\t\tattachment_id: IMAGESEO_ATTACHMENTS[current]\n\t\t\t}\n\t\t)\n\t}\n\n\tfunction finishReportImages() {\n\t\t$('#imageseo-bulk-reports').prop('disabled', false)\n\t\t$('#imageseo-bulk-reports .imageseo-loading').hide()\n\t\t$('#imageseo-bulk-reports span').show()\n\t}\n})\n\n\n//# sourceURL=webpack:///./app/javascripts/bulk-optimization.js?");

/***/ })

/******/ });