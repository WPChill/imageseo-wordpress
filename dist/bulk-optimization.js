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

eval("function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }\n\nfunction _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }\n\nfunction _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }\n\ndocument.addEventListener('DOMContentLoaded', function () {\n  var $ = jQuery;\n  var _execution = false;\n\n  if (IMAGESEO_ATTACHMENTS.length === 0 || IMAGESEO_ATTACHMENTS_WITH_TAG_EMPTY.length === 0) {\n    return;\n  }\n\n  var getImageAttachmentsList = function getImageAttachmentsList() {\n    var updateAlt = $('#option-update-alt').is(':checked');\n    return updateAlt ? IMAGESEO_ATTACHMENTS_WITH_TAG_EMPTY : IMAGESEO_ATTACHMENTS;\n  };\n\n  document.querySelector('#imageseo-bulk-reports--stop').addEventListener('click', function (e) {\n    e.preventDefault();\n    $(this).html('Current shutdown ...');\n    _execution = false;\n  });\n  document.querySelector('#imageseo-bulk-reports--start').addEventListener('click', function (e) {\n    e.preventDefault();\n    document.querySelector('#imageseo-percent-bulk').style.display = 'block';\n    $(this).prop('disabled', true);\n    $('#imageseo-bulk-reports--preview').prop('disabled', true);\n    $('#option-update-alt').prop('disabled', true);\n    $('#option-update-alt-not-empty').prop('disabled', true);\n    $('#option-rename-file').prop('disabled', true);\n    $('span', $(this)).hide();\n    $('.imageseo-loading', $(this)).show();\n    $('#imageseo-reports-js .imageseo-reports-body').html('');\n    $('#imageseo-bulk-reports--stop').prop('disabled', false);\n    var val = $(\"input[name='method']:checked\").val();\n    var total, start, add;\n\n    if (val === 'new') {\n      total = getImageAttachmentsList().length;\n      start = 0;\n      add = 0;\n    } else {\n      total = getImageAttachmentsList().length - (IMAGESEO_CURRENT_PROCESS + 1);\n      start = IMAGESEO_CURRENT_PROCESS;\n      add = 1;\n    }\n\n    _execution = true;\n    launchReportImages(start, 0, total, add);\n  });\n  document.querySelector('#imageseo-bulk-reports--preview').addEventListener('click', function (e) {\n    e.preventDefault();\n    document.querySelector('#imageseo-percent-bulk').style.display = 'block';\n    $(this).prop('disabled', true);\n    $('#option-update-alt').prop('disabled', true).prop('checked', false);\n    $('#option-update-alt-not-empty').prop('disabled', true).prop('checked', false);\n    $('#option-rename-file').prop('disabled', true).prop('checked', false);\n    $('span', $(this)).hide();\n    $('#imageseo-bulk-reports--start').prop('disabled', true);\n    $('.imageseo-loading', $(this)).show();\n    $('#imageseo-reports-js .imageseo-reports-body').html('');\n    $('#imageseo-bulk-reports--stop').prop('disabled', false);\n    var val = $(\"input[name='method']:checked\").val();\n    var total, start, add;\n\n    if (val === 'new') {\n      total = getImageAttachmentsList().length;\n      start = 0;\n      add = 0;\n    } else {\n      total = getImageAttachmentsList().length - (IMAGESEO_CURRENT_PROCESS + 1);\n      start = IMAGESEO_CURRENT_PROCESS;\n      add = 1;\n    }\n\n    _execution = true;\n    launchReportImages(start, 0, total, add);\n  });\n\n  var getPercent = function getPercent(current, total) {\n    if (current > total) {\n      return 100;\n    } else {\n      return Math.round(current * 100 / total);\n    }\n  };\n\n  var setPercentLoader = function setPercentLoader(current, total) {\n    var percent = getPercent(current, total);\n    var el = document.querySelector('#imageseo-percent-bulk .imageseo-percent--item');\n    el.style.width = \"\".concat(percent, \"%\");\n    el.textContent = \"\".concat(percent, \"% (\").concat(current, \"/\").concat(total, \")\");\n  };\n\n  var ReportItem = function ReportItem(_ref) {\n    var dashicons = _ref.dashicons,\n        current_name_file = _ref.current_name_file,\n        file_generate = _ref.file_generate,\n        current_alt = _ref.current_alt,\n        alt_generate = _ref.alt_generate,\n        _ref$file = _ref.file,\n        file = _ref$file === void 0 ? '' : _ref$file;\n    return \"<div class=\\\"imageseo-reports-body-item\\\">\\n\\t\\t<div class=\\\"imageseo-reports--status\\\"><span class=\\\"dashicons dashicons-\".concat(dashicons, \"\\\"></span></div>\\n\\t\\t<div class=\\\"imageseo-reports--image\\\"><div class=\\\"imageseo-reports--image-itm\\\" style=\\\"background-image:url('\").concat(file, \"')\\\"></div></div>\\n\\t\\t<div class=\\\"imageseo-reports--src\\\"><div>Current name file : \").concat(current_name_file, \"<hr /> <strong>ImageSEO AI suggestion</strong> : \").concat(file_generate, \"</div></div>\\n\\t\\t<div class=\\\"imageseo-reports--alt\\\"><div>Current alt : \").concat(current_alt, \" <hr />  <strong>ImageSEO AI suggestion</strong> : \").concat(alt_generate, \"</div></div>\\n\\t</div>\");\n  };\n\n  var ReportItemError = function ReportItemError(_ref2) {\n    var dashicons = _ref2.dashicons,\n        src = _ref2.src;\n    return \"<div class=\\\"imageseo-reports-body-item imageseo-reports-body-item--error\\\">\\n\\t\\t<div class=\\\"imageseo-reports--status\\\"><span class=\\\"dashicons dashicons-\".concat(dashicons, \"\\\"></span></div>\\n\\t\\t<div class=\\\"imageseo-reports--src\\\">\").concat(src, \"</div>\\n\\t\\t<div class=\\\"imageseo-reports--error\\\">Impossible to generate the report of this image. Remember to check your remaining credits</div>\\n\\t</div>\");\n  };\n\n  function launchReportImages(start, current, total) {\n    var add = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 0;\n    var index = start + current + add;\n\n    if (current > IMAGESEO_LIMIT_IMAGES) {\n      $('#imageseo-reports-js .imageseo-reports-body').prepend(\"<div class=\\\"imageseo-reports-body-item imageseo-reports-body-item--error\\\">\\n\\t\\t<div class=\\\"imageseo-reports--status\\\"><span class=\\\"dashicons dashicons-no\\\"></span></div>\\n\\t\\t<div class=\\\"imageseo-reports--src\\\"></div>\\n\\t\\t<div class=\\\"imageseo-reports--error\\\">You have exceeded your image limit</div>\\n\\t</div>\");\n      finishReportImages();\n      return;\n    }\n\n    if (current > total || !_execution) {\n      finishReportImages();\n      return;\n    }\n\n    if (typeof getImageAttachmentsList()[index] === 'undefined') {\n      current++;\n      launchReportImages(start, current, total, add);\n      return;\n    }\n\n    var updateAlt = $('#option-update-alt').is(':checked');\n    var updateAltNotEmpty = $('#option-update-alt-not-empty').is(':checked');\n    var renameFile = $('#option-rename-file').is(':checked');\n\n    var _errorReportAttachment = function _errorReportAttachment(res) {\n      $('#imageseo-reports-js .imageseo-reports-body').prepend(ReportItem({\n        src: \"Attachment ID: \".concat(getImageAttachmentsList()[index]),\n        name_file: '',\n        alt_generate: '',\n        dashicons: 'no'\n      }));\n      current++;\n      setPercentLoader(current, total);\n      launchReportImages(start, current, total, add);\n    };\n\n    var _successReportAttachment = function _successReportAttachment(res) {\n      IMAGESEO_CURRENT_PROCESS = current + 1;\n      var txt = \"Attachment ID : \".concat(getImageAttachmentsList()[index]);\n\n      if (res.data && res.data.src) {\n        txt = res.data.src;\n      }\n\n      current++;\n      setPercentLoader(current, total);\n\n      if (res.success) {\n        $('#imageseo-reports-js .imageseo-reports-body').prepend(ReportItem(_objectSpread({}, res.data, {\n          src: txt,\n          dashicons: 'yes'\n        })));\n      } else {\n        $('#imageseo-reports-js .imageseo-reports-body').prepend(ReportItemError(_objectSpread({}, res.data, {\n          src: txt,\n          dashicons: 'no'\n        })));\n      }\n\n      launchReportImages(start, current, total, add);\n    };\n\n    $.post({\n      url: ajaxurl,\n      success: _successReportAttachment,\n      error: _errorReportAttachment\n    }, {\n      action: 'imageseo_report_attachment',\n      update_alt: updateAlt,\n      update_alt_not_empty: updateAltNotEmpty,\n      rename_file: renameFile,\n      total: getImageAttachmentsList().length,\n      current: index,\n      attachment_id: getImageAttachmentsList()[index]\n    });\n  }\n\n  function finishReportImages() {\n    _execution = false;\n    $('#imageseo-bulk-reports--start').prop('disabled', false);\n    $('#imageseo-bulk-reports--start .imageseo-loading').hide();\n    $('#imageseo-bulk-reports--start span').show();\n    $('#imageseo-bulk-reports--preview').prop('disabled', false);\n    $('#imageseo-bulk-reports--preview .imageseo-loading').hide();\n    $('#imageseo-bulk-reports--preview span').show();\n    $('#option-update-alt').prop('disabled', false);\n    $('#option-update-alt-not-empty').prop('disabled', false);\n    $('#option-rename-file').prop('disabled', false);\n    $('#imageseo-bulk-reports--stop').prop('disabled', true).html('Stop');\n  }\n});\n\n//# sourceURL=webpack:///./app/javascripts/bulk-optimization.js?");

/***/ })

/******/ });