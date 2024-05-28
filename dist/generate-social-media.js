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

/***/ "./app/javascripts/generate-social-media.js":
/*!**************************************************!*\
  !*** ./app/javascripts/generate-social-media.js ***!
  \**************************************************/
/***/ (() => {

eval("document.addEventListener(\"DOMContentLoaded\", function () {\n  var $ = jQuery;\n  var handlePingCurrent;\n  function pingCurrentProcess(postId) {\n    $.ajax({\n      url: ajaxurl,\n      method: \"POST\",\n      data: {\n        action: \"imageseo_check_current_process\",\n        post_id: postId,\n        _wpnonce: imageseo_ajax_nonce\n      },\n      success: function success(response) {\n        var _response$data = response.data,\n          current_process = _response$data.current_process,\n          url = _response$data.url;\n        if (current_process) {\n          return;\n        }\n        $(\"#imageseo-social-media-image\").attr(\"src\", url);\n        setTimeout(function () {\n          $(\"#imageseo-social-media[data-id='\".concat(postId, \"']\")).prop(\"disabled\", \"\");\n          $(\"#imageseo-social-media[data-id='\".concat(postId, \"']\")).find(\"img\").hide();\n        }, 600);\n        clearInterval(handlePingCurrent);\n      }\n    });\n  }\n  function bindSinglePostGenerateSocial() {\n    $(\"#imageseo-social-media\").on(\"click\", function (e) {\n      var _this = this;\n      e.preventDefault();\n      $(this).prop(\"disabled\", \"disabled\");\n      $(this).find(\"img\").show();\n      $.ajax({\n        url: ajaxurl,\n        method: \"POST\",\n        data: {\n          action: \"imageseo_generate_social_media\",\n          post_id: $(this).data(\"id\"),\n          _wpnonce: imageseo_ajax_nonce\n        },\n        success: function success() {\n          handlePingCurrent = setInterval(pingCurrentProcess, 2500, $(_this).data(\"id\"));\n        }\n      });\n    });\n  }\n  if ($(\"#imageseo-social-media\").length > 0) {\n    bindSinglePostGenerateSocial();\n  }\n});\n\n//# sourceURL=webpack://imageseo/./app/javascripts/generate-social-media.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./app/javascripts/generate-social-media.js"]();
/******/ 	
/******/ })()
;