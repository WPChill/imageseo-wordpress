/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./app/javascripts/admin-bar.js":
/*!**************************************!*\
  !*** ./app/javascripts/admin-bar.js ***!
  \**************************************/
/***/ (() => {

eval("document.addEventListener('DOMContentLoaded', function () {\n  var $ = jQuery;\n  var totalAlts = 0;\n  var totalImages = 0;\n  $('body img').each(function (i, el) {\n    if ($(el).parents('#wpadminbar').length > 0) {\n      return;\n    }\n    totalImages++;\n    if ($(el).attr('alt') && $(el).attr('alt').length > 0) {\n      totalAlts++;\n    }\n  });\n  var percent = 0 !== totalAlts && 0 !== totalImages ? Math.round(totalAlts * 100 / totalImages) : 0;\n  var color = 'red';\n  if (percent > 40 && percent < 70) {\n    color = 'orange';\n  } else if (percent >= 70) {\n    color = 'green';\n  }\n  $('#wp-admin-bar-imageseo-loading-alts').html(\"\".concat(i18nImageSeo.alternative_text, \" : \").concat(totalAlts, \" / \").concat(totalImages, \" ( <span style='color:\").concat(color, \"'>\").concat(percent, \"% </span>)\"));\n});\n\n//# sourceURL=webpack://imageseo/./app/javascripts/admin-bar.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./app/javascripts/admin-bar.js"]();
/******/ 	
/******/ })()
;