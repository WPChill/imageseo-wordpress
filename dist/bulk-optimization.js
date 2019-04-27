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

eval("document.addEventListener('DOMContentLoaded', function() {\n\tconst $ = jQuery\n\tlet _execution = false\n\tif (IMAGESEO_ATTACHMENTS.length === 0) {\n\t\treturn\n\t}\n\n\tdocument\n\t\t.querySelector('#imageseo-bulk-reports--stop')\n\t\t.addEventListener('click', function(e) {\n\t\t\te.preventDefault()\n\t\t\t$(this).html('Current shutdown ...')\n\t\t\t_execution = false\n\t\t})\n\n\tdocument\n\t\t.querySelector('#imageseo-bulk-reports--start')\n\t\t.addEventListener('click', function(e) {\n\t\t\te.preventDefault()\n\t\t\tdocument.querySelector('#imageseo-percent-bulk').style.display =\n\t\t\t\t'block'\n\n\t\t\t$(this).prop('disabled', true)\n\t\t\t$('#imageseo-bulk-reports--preview').prop('disabled', true)\n\t\t\t$('#option-update-alt').prop('disabled', true)\n\t\t\t$('#option-update-alt-not-empty').prop('disabled', true)\n\t\t\t$('#option-rename-file').prop('disabled', true)\n\t\t\t$('span', $(this)).hide()\n\t\t\t$('.imageseo-loading', $(this)).show()\n\t\t\t$('#imageseo-reports-js .imageseo-reports-body').html('')\n\t\t\t$('#imageseo-bulk-reports--stop').prop('disabled', false)\n\n\t\t\tconst val = $(\"input[name='method']:checked\").val()\n\t\t\tlet total, start, add\n\n\t\t\tif (val === 'new') {\n\t\t\t\ttotal = IMAGESEO_ATTACHMENTS.length\n\t\t\t\tstart = 0\n\t\t\t\tadd = 0\n\t\t\t} else {\n\t\t\t\ttotal =\n\t\t\t\t\tIMAGESEO_ATTACHMENTS.length - (IMAGESEO_CURRENT_PROCESS + 1)\n\t\t\t\tstart = IMAGESEO_CURRENT_PROCESS\n\t\t\t\tadd = 1\n\t\t\t}\n\n\t\t\t_execution = true\n\t\t\tlaunchReportImages(start, 0, total, add)\n\t\t})\n\n\tdocument\n\t\t.querySelector('#imageseo-bulk-reports--preview')\n\t\t.addEventListener('click', function(e) {\n\t\t\te.preventDefault()\n\t\t\tdocument.querySelector('#imageseo-percent-bulk').style.display =\n\t\t\t\t'block'\n\n\t\t\t$(this).prop('disabled', true)\n\t\t\t$('#option-update-alt')\n\t\t\t\t.prop('disabled', true)\n\t\t\t\t.prop('checked', false)\n\t\t\t$('#option-update-alt-not-empty')\n\t\t\t\t.prop('disabled', true)\n\t\t\t\t.prop('checked', false)\n\t\t\t$('#option-rename-file')\n\t\t\t\t.prop('disabled', true)\n\t\t\t\t.prop('checked', false)\n\t\t\t$('span', $(this)).hide()\n\n\t\t\t$('#imageseo-bulk-reports--start').prop('disabled', true)\n\t\t\t$('.imageseo-loading', $(this)).show()\n\t\t\t$('#imageseo-reports-js .imageseo-reports-body').html('')\n\t\t\t$('#imageseo-bulk-reports--stop').prop('disabled', false)\n\n\t\t\tconst val = $(\"input[name='method']:checked\").val()\n\t\t\tlet total, start, add\n\n\t\t\tif (val === 'new') {\n\t\t\t\ttotal = IMAGESEO_ATTACHMENTS.length\n\t\t\t\tstart = 0\n\t\t\t\tadd = 0\n\t\t\t} else {\n\t\t\t\ttotal =\n\t\t\t\t\tIMAGESEO_ATTACHMENTS.length - (IMAGESEO_CURRENT_PROCESS + 1)\n\t\t\t\tstart = IMAGESEO_CURRENT_PROCESS\n\t\t\t\tadd = 1\n\t\t\t}\n\n\t\t\t_execution = true\n\t\t\tlaunchReportImages(start, 0, total, add)\n\t\t})\n\n\tconst getPercent = (current, total) => {\n\t\tif (current > total) {\n\t\t\treturn 100\n\t\t} else {\n\t\t\treturn Math.round((current * 100) / total)\n\t\t}\n\t}\n\n\tconst setPercentLoader = (current, total) => {\n\t\tconst percent = getPercent(current, total)\n\t\tconst el = document.querySelector(\n\t\t\t'#imageseo-percent-bulk .imageseo-percent--item'\n\t\t)\n\t\tel.style.width = `${percent}%`\n\t\tel.textContent = `${percent}% (${current}/${total})`\n\t}\n\n\tconst ReportItem = ({\n\t\tdashicons,\n\t\tcurrent_name_file,\n\t\tname_file,\n\t\tcurrent_alt,\n\t\talt_generate,\n\t\tfile = ''\n\t}) => `<div class=\"imageseo-reports-body-item\">\n\t\t<div class=\"imageseo-reports--status\"><span class=\"dashicons dashicons-${dashicons}\"></span></div>\n\t\t<div class=\"imageseo-reports--image\"><div class=\"imageseo-reports--image-itm\" style=\"background-image:url('${file}')\"></div></div>\n\t\t<div class=\"imageseo-reports--src\"><div>Current name file : ${current_name_file}<hr /> <strong>ImageSEO AI suggestion</strong> : ${name_file}</div></div>\n\t\t<div class=\"imageseo-reports--alt\"><div>Current alt : ${current_alt} <hr />  <strong>ImageSEO AI suggestion</strong> : ${alt_generate}</div></div>\n\t</div>`\n\n\tconst ReportItemError = ({\n\t\tdashicons,\n\t\tsrc\n\t}) => `<div class=\"imageseo-reports-body-item imageseo-reports-body-item--error\">\n\t\t<div class=\"imageseo-reports--status\"><span class=\"dashicons dashicons-${dashicons}\"></span></div>\n\t\t<div class=\"imageseo-reports--src\">${src}</div>\n\t\t<div class=\"imageseo-reports--error\">Impossible to generate the report of this image. Remember to check your remaining credits</div>\n\t</div>`\n\n\tfunction launchReportImages(start, current, total, add = 0) {\n\t\tconst index = start + current + add\n\n\t\tif (current > total || !_execution) {\n\t\t\tfinishReportImages()\n\t\t\treturn\n\t\t}\n\n\t\tif (typeof IMAGESEO_ATTACHMENTS[index] === 'undefined') {\n\t\t\tcurrent++\n\t\t\tlaunchReportImages(start, current, total, add)\n\t\t\treturn\n\t\t}\n\t\tconst updateAlt = $('#option-update-alt').is(':checked')\n\t\tconst updateAltNotEmpty = $('#option-update-alt-not-empty').is(\n\t\t\t':checked'\n\t\t)\n\t\tconst renameFile = $('#option-rename-file').is(':checked')\n\n\t\tconst _errorReportAttachment = res => {\n\t\t\t$('#imageseo-reports-js .imageseo-reports-body').prepend(\n\t\t\t\tReportItem({\n\t\t\t\t\tsrc: `Attachment ID: ${IMAGESEO_ATTACHMENTS[index]}`,\n\t\t\t\t\tname_file: '',\n\t\t\t\t\talt_generate: '',\n\t\t\t\t\tdashicons: 'no'\n\t\t\t\t})\n\t\t\t)\n\t\t\tcurrent++\n\t\t\tsetPercentLoader(current, total)\n\t\t\tlaunchReportImages(start, current, total, add)\n\t\t}\n\n\t\tconst _successReportAttachment = res => {\n\t\t\tIMAGESEO_CURRENT_PROCESS = current + 1\n\t\t\tlet txt = `Attachment ID : ${IMAGESEO_ATTACHMENTS[index]}`\n\t\t\tif (res.data && res.data.src) {\n\t\t\t\ttxt = res.data.src\n\t\t\t}\n\t\t\tcurrent++\n\t\t\tsetPercentLoader(current, total)\n\n\t\t\tif (res.success) {\n\t\t\t\t$('#imageseo-reports-js .imageseo-reports-body').prepend(\n\t\t\t\t\tReportItem({\n\t\t\t\t\t\t...res.data,\n\t\t\t\t\t\tsrc: txt,\n\t\t\t\t\t\tdashicons: 'yes'\n\t\t\t\t\t})\n\t\t\t\t)\n\t\t\t} else {\n\t\t\t\t$('#imageseo-reports-js .imageseo-reports-body').prepend(\n\t\t\t\t\tReportItemError({\n\t\t\t\t\t\t...res.data,\n\t\t\t\t\t\tsrc: txt,\n\t\t\t\t\t\tdashicons: 'no'\n\t\t\t\t\t})\n\t\t\t\t)\n\t\t\t}\n\t\t\tlaunchReportImages(start, current, total, add)\n\t\t}\n\n\t\t$.post(\n\t\t\t{\n\t\t\t\turl: ajaxurl,\n\t\t\t\tsuccess: _successReportAttachment,\n\t\t\t\terror: _errorReportAttachment\n\t\t\t},\n\t\t\t{\n\t\t\t\taction: 'imageseo_report_attachment',\n\t\t\t\tupdate_alt: updateAlt,\n\t\t\t\tupdate_alt_not_empty: updateAltNotEmpty,\n\t\t\t\trename_file: renameFile,\n\t\t\t\ttotal: IMAGESEO_ATTACHMENTS.length,\n\t\t\t\tcurrent: index,\n\t\t\t\tattachment_id: IMAGESEO_ATTACHMENTS[index]\n\t\t\t}\n\t\t)\n\t}\n\n\tfunction finishReportImages() {\n\t\t_execution = false\n\t\t$('#imageseo-bulk-reports--start').prop('disabled', false)\n\t\t$('#imageseo-bulk-reports--start .imageseo-loading').hide()\n\t\t$('#imageseo-bulk-reports--start span').show()\n\n\t\t$('#imageseo-bulk-reports--preview').prop('disabled', false)\n\t\t$('#imageseo-bulk-reports--preview .imageseo-loading').hide()\n\t\t$('#imageseo-bulk-reports--preview span').show()\n\n\t\t$('#option-update-alt').prop('disabled', false)\n\t\t$('#option-update-alt-not-empty').prop('disabled', false)\n\t\t$('#option-rename-file').prop('disabled', false)\n\t\t$('#imageseo-bulk-reports--stop')\n\t\t\t.prop('disabled', true)\n\t\t\t.html('Stop')\n\t}\n})\n\n\n//# sourceURL=webpack:///./app/javascripts/bulk-optimization.js?");

/***/ })

/******/ });