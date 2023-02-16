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
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/src/metabox/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/src/metabox/index.js":
/*!*********************************!*\
  !*** ./js/src/metabox/index.js ***!
  \*********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _scss_metabox_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./../../../scss/metabox.scss */ \"./scss/metabox.scss\");\n/* harmony import */ var _scss_metabox_scss__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_scss_metabox_scss__WEBPACK_IMPORTED_MODULE_0__);\n\n\n/**\r\n * WordPress dependencies\r\n */\nconst { __ } = wp.i18n;\n\nwindow.addEventListener('load', () => {\n\n\tlet $delete = document.querySelectorAll('.fillablepdfs-metabox__delete');\n\n\tif ($delete.length === 0) {\n\t\treturn;\n\t}\n\n\t$delete.forEach($del => {\n\n\t\t$del.addEventListener('click', async e => {\n\n\t\t\te.preventDefault();\n\n\t\t\tif (!confirm(__('Are you sure you want to delete this PDF?', 'forgravity_fillablepdfs'))) {\n\t\t\t\treturn false;\n\t\t\t}\n\n\t\t\tlet formData = new FormData();\n\t\t\tformData.append('action', 'fg_fillablepdfs_metabox_delete');\n\t\t\tformData.append('pdfId', e.target.dataset.pdfId);\n\t\t\tformData.append('nonce', e.target.dataset.nonce);\n\n\t\t\tconst fetchParams = {\n\t\t\t\tmethod: 'POST',\n\t\t\t\tbody: formData\n\t\t\t};\n\n\t\t\tconsole.log(fetchParams);\n\n\t\t\tawait fetch(ajaxurl, fetchParams).then(response => response.json()).then(response => {\n\n\t\t\t\tif (response.success) {\n\t\t\t\t\te.target.parentNode.parentNode.remove();\n\t\t\t\t} else {\n\t\t\t\t\talert(response.data);\n\t\t\t\t}\n\t\t\t});\n\t\t});\n\t});\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9qcy9zcmMvbWV0YWJveC9pbmRleC5qcy5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9qcy9zcmMvbWV0YWJveC9pbmRleC5qcz9mOTIwIl0sInNvdXJjZXNDb250ZW50IjpbImltcG9ydCAnLi8uLi8uLi8uLi9zY3NzL21ldGFib3guc2Nzcyc7XHJcblxyXG4vKipcclxuICogV29yZFByZXNzIGRlcGVuZGVuY2llc1xyXG4gKi9cclxuY29uc3QgeyBfXyB9ID0gd3AuaTE4bjtcclxuXHJcbndpbmRvdy5hZGRFdmVudExpc3RlbmVyKCAnbG9hZCcsICgpID0+IHtcclxuXHJcblx0bGV0ICRkZWxldGUgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCAnLmZpbGxhYmxlcGRmcy1tZXRhYm94X19kZWxldGUnICk7XHJcblxyXG5cdGlmICggJGRlbGV0ZS5sZW5ndGggPT09IDAgKSB7XHJcblx0XHRyZXR1cm47XHJcblx0fVxyXG5cclxuXHQkZGVsZXRlLmZvckVhY2goICggJGRlbCApID0+IHtcclxuXHJcblx0XHQkZGVsLmFkZEV2ZW50TGlzdGVuZXIoICdjbGljaycsIGFzeW5jICggZSApID0+IHtcclxuXHJcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcclxuXHJcblx0XHRcdGlmICggISBjb25maXJtKCBfXyggJ0FyZSB5b3Ugc3VyZSB5b3Ugd2FudCB0byBkZWxldGUgdGhpcyBQREY/JywgJ2ZvcmdyYXZpdHlfZmlsbGFibGVwZGZzJyApICkgKSB7XHJcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRsZXQgZm9ybURhdGEgPSBuZXcgRm9ybURhdGEoKTtcclxuXHRcdFx0Zm9ybURhdGEuYXBwZW5kKCAnYWN0aW9uJywgJ2ZnX2ZpbGxhYmxlcGRmc19tZXRhYm94X2RlbGV0ZScgKTtcclxuXHRcdFx0Zm9ybURhdGEuYXBwZW5kKCAncGRmSWQnLCBlLnRhcmdldC5kYXRhc2V0LnBkZklkICk7XHJcblx0XHRcdGZvcm1EYXRhLmFwcGVuZCggJ25vbmNlJywgZS50YXJnZXQuZGF0YXNldC5ub25jZSApO1xyXG5cclxuXHRcdFx0Y29uc3QgZmV0Y2hQYXJhbXMgPSB7XHJcblx0XHRcdFx0bWV0aG9kOiAnUE9TVCcsXHJcblx0XHRcdFx0Ym9keTogICBmb3JtRGF0YVxyXG5cdFx0XHR9O1xyXG5cclxuXHRcdFx0Y29uc29sZS5sb2coIGZldGNoUGFyYW1zICk7XHJcblxyXG5cdFx0XHRhd2FpdCBmZXRjaCggYWpheHVybCwgZmV0Y2hQYXJhbXMgKVxyXG5cdFx0XHRcdC50aGVuKCAoIHJlc3BvbnNlICkgPT4gcmVzcG9uc2UuanNvbigpIClcclxuXHRcdFx0XHQudGhlbiggKCByZXNwb25zZSApID0+IHtcclxuXHJcblx0XHRcdFx0XHRpZiAoIHJlc3BvbnNlLnN1Y2Nlc3MgKSB7XHJcblx0XHRcdFx0XHRcdGUudGFyZ2V0LnBhcmVudE5vZGUucGFyZW50Tm9kZS5yZW1vdmUoKTtcclxuXHRcdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRcdGFsZXJ0KCByZXNwb25zZS5kYXRhICk7XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdH0gKTtcclxuXHJcblx0XHR9ICk7XHJcblxyXG5cdH0gKTtcclxuXHJcbn0gKTtcclxuIl0sIm1hcHBpbmdzIjoiQUFBQTtBQUFBO0FBQUE7QUFBQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRkE7QUFDQTtBQUlBO0FBQ0E7QUFDQTtBQUNBO0FBR0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBRUE7QUFFQTtBQUVBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./js/src/metabox/index.js\n");

/***/ }),

/***/ "./scss/metabox.scss":
/*!***************************!*\
  !*** ./scss/metabox.scss ***!
  \***************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9zY3NzL21ldGFib3guc2Nzcy5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL3Njc3MvbWV0YWJveC5zY3NzPzlhY2UiXSwic291cmNlc0NvbnRlbnQiOlsiLy8gZXh0cmFjdGVkIGJ5IG1pbmktY3NzLWV4dHJhY3QtcGx1Z2luIl0sIm1hcHBpbmdzIjoiQUFBQSIsInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./scss/metabox.scss\n");

/***/ })

/******/ });