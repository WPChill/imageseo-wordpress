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
/******/ 	return __webpack_require__(__webpack_require__.s = "./app/javascripts/media-upload.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./app/javascripts/media-upload.js":
/*!*****************************************!*\
  !*** ./app/javascripts/media-upload.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("jQuery(document).ready(function ($) {\n  function imageseo_alt_field(attachment) {\n    var alt_text = $('#imageseo-alt-' + attachment).val();\n    $.post(ajaxurl, {\n      action: 'imageseo_media_alt_update',\n      post_id: attachment,\n      alt: alt_text,\n      success: function success() {\n        setTimeout(function () {\n          $('#wrapper-imageseo-' + attachment + ' .imageseo-loading').hide();\n          $('#wrapper-imageseo-' + attachment + ' button span').show();\n        }, 500);\n      }\n    });\n  }\n\n  $(this).on('keydown', 'input.imageseo-alt-ajax', function (event) {\n    if (event.keyCode === 13) {\n      $(this).blur();\n      return false;\n    }\n  }).on('blur', 'input.imageseo-alt-ajax', function () {\n    var id = $(this).data('id');\n    $('#wrapper-imageseo-' + id + ' button span').hide();\n    $('#wrapper-imageseo-' + id + ' .imageseo-loading').show();\n    imageseo_alt_field(id);\n    return false;\n  });\n  $('.wrapper-imageseo-input-alt button').on('click', function (e) {\n    e.preventDefault();\n    var id = $(this).data('id');\n    $('#wrapper-imageseo-' + id + ' button span').hide();\n    $('#wrapper-imageseo-' + id + ' .imageseo-loading').show();\n    imageseo_alt_field(id);\n  });\n});\n\n//# sourceURL=webpack:///./app/javascripts/media-upload.js?");

/***/ })

/******/ });