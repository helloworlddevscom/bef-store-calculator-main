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
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/src/admin.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/src/admin.js":
/*!*************************!*\
  !*** ./js/src/admin.js ***!
  \*************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _scss_admin_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./../../scss/admin.scss */ \"./scss/admin.scss\");\n/* harmony import */ var _scss_admin_scss__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_scss_admin_scss__WEBPACK_IMPORTED_MODULE_0__);\n\n\nwindow.addEventListener('load', () => {\n\n\tconst $dropzone = document.querySelector('.fillablepdfs-dropzone'),\n\t      $templateInfo = document.querySelector('.fillablepdfs-template-info'),\n\t      $name = document.getElementById('name');\n\n\t// Handle file uploads.\n\tif ($dropzone) {\n\n\t\tconst $file = document.getElementById($dropzone.dataset.file),\n\t\t      strings = forgravity_fillablepdfs_admin_strings;\n\n\t\t$dropzone.ondragover = e => {\n\t\t\t$dropzone.classList.add('fillablepdfs-dropzone--dropping');\n\t\t\te.preventDefault();\n\t\t};\n\n\t\t$dropzone.ondragstart = e => {\n\t\t\t$dropzone.classList.add('fillablepdfs-dropzone--dropping');\n\t\t\te.preventDefault();\n\t\t};\n\n\t\t$dropzone.ondragend = e => {\n\t\t\t$dropzone.classList.remove('fillablepdfs-dropzone--dropping');\n\t\t\te.preventDefault();\n\t\t};\n\n\t\t$dropzone.ondrop = e => {\n\n\t\t\te.preventDefault();\n\t\t\t$dropzone.classList.remove('fillablepdfs-dropzone--dropping');\n\n\t\t\t// Attach file to file input.\n\t\t\t$file.files = e.dataTransfer.files;\n\t\t\t$file.dispatchEvent(new Event('change', e));\n\t\t};\n\n\t\t$dropzone.addEventListener('click', e => {\n\n\t\t\t$file.click();\n\t\t});\n\n\t\t$file.addEventListener('change', e => {\n\n\t\t\t// If more than one file was dropped, display error.\n\t\t\tif (e.target.files.length > 1) {\n\t\t\t\talert(strings.too_many_files);\n\t\t\t\te.target.value = null;\n\t\t\t\treturn false;\n\t\t\t}\n\n\t\t\t// If file is not a PDF, display error.\n\t\t\tif (e.target.files[0].type !== 'application/pdf') {\n\t\t\t\talert(strings.illegal_file_type);\n\t\t\t\te.target.value = null;\n\t\t\t\treturn false;\n\t\t\t}\n\n\t\t\t// Set changed flag.\n\t\t\tdocument.querySelector('.fillablepdfs-dropzone__changed').value = '1';\n\n\t\t\t// If this is the import form, submit form.\n\t\t\tif ($dropzone.dataset.import) {\n\t\t\t\t$dropzone.parentElement.submit();\n\t\t\t}\n\n\t\t\tif ($templateInfo) {\n\n\t\t\t\t// Set file name in template info.\n\t\t\t\t$templateInfo.querySelector('.fillablepdfs-template-info__file-name').innerHTML = e.target.files[0].name;\n\n\t\t\t\t// Hide drop zone, show template info.\n\t\t\t\t$dropzone.style.display = 'none';\n\t\t\t\t$templateInfo.style.display = 'flex';\n\t\t\t}\n\n\t\t\t// Replace template name.\n\t\t\tif ($name && $name.value.length === 0) {\n\t\t\t\t$name.value = e.target.files[0].name.replace(/\\.[^/.]+$/, '');\n\t\t\t\t$name.dispatchEvent(new Event('keyup'));\n\t\t\t}\n\t\t});\n\t}\n\n\t// Update template name.\n\tif ($name && document.querySelector('.fillablepdfs-template-info__name')) {\n\t\t$name.addEventListener('keyup', e => {\n\t\t\tdocument.querySelector('.fillablepdfs-template-info__name').innerHTML = e.target.value;\n\t\t});\n\t}\n\n\t// Reset template file.\n\tif (document.querySelector('.fillablepdfs-template-info__action--remove')) {\n\t\tdocument.querySelector('.fillablepdfs-template-info__action--remove').addEventListener('click', e => {\n\n\t\t\te.preventDefault();\n\n\t\t\t// Reset file input.\n\t\t\tdocument.getElementById($dropzone.dataset.file).value = null;\n\n\t\t\t// Set changed flag.\n\t\t\tdocument.querySelector('.fillablepdfs-dropzone__changed').value = '1';\n\n\t\t\t// Hide template info, show drop zone.\n\t\t\t$templateInfo.style.display = 'none';\n\t\t\t$dropzone.style.display = 'block';\n\t\t});\n\t}\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9qcy9zcmMvYWRtaW4uanMuanMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vanMvc3JjL2FkbWluLmpzPzNlMTUiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0ICcuLy4uLy4uL3Njc3MvYWRtaW4uc2Nzcyc7XHJcblxyXG53aW5kb3cuYWRkRXZlbnRMaXN0ZW5lciggJ2xvYWQnLCAoKSA9PiB7XHJcblxyXG5cdGNvbnN0ICRkcm9wem9uZSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoICcuZmlsbGFibGVwZGZzLWRyb3B6b25lJyApLFxyXG5cdFx0JHRlbXBsYXRlSW5mbyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoICcuZmlsbGFibGVwZGZzLXRlbXBsYXRlLWluZm8nICksXHJcblx0XHQkbmFtZSA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAnbmFtZScgKTtcclxuXHJcblx0Ly8gSGFuZGxlIGZpbGUgdXBsb2Fkcy5cclxuXHRpZiAoICRkcm9wem9uZSApIHtcclxuXHJcblx0XHRjb25zdCAkZmlsZSA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAkZHJvcHpvbmUuZGF0YXNldC5maWxlICksXHJcblx0XHQgICAgICBzdHJpbmdzID0gZm9yZ3Jhdml0eV9maWxsYWJsZXBkZnNfYWRtaW5fc3RyaW5ncztcclxuXHJcblx0XHQkZHJvcHpvbmUub25kcmFnb3ZlciA9ICggZSApID0+IHtcclxuXHRcdFx0JGRyb3B6b25lLmNsYXNzTGlzdC5hZGQoICdmaWxsYWJsZXBkZnMtZHJvcHpvbmUtLWRyb3BwaW5nJyApO1xyXG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XHJcblx0XHR9O1xyXG5cclxuXHRcdCRkcm9wem9uZS5vbmRyYWdzdGFydCA9ICggZSApID0+IHtcclxuXHRcdFx0JGRyb3B6b25lLmNsYXNzTGlzdC5hZGQoICdmaWxsYWJsZXBkZnMtZHJvcHpvbmUtLWRyb3BwaW5nJyApO1xyXG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XHJcblx0XHR9O1xyXG5cclxuXHRcdCRkcm9wem9uZS5vbmRyYWdlbmQgPSAoIGUgKSA9PiB7XHJcblx0XHRcdCRkcm9wem9uZS5jbGFzc0xpc3QucmVtb3ZlKCAnZmlsbGFibGVwZGZzLWRyb3B6b25lLS1kcm9wcGluZycgKTtcclxuXHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG5cdFx0fTtcclxuXHJcblx0XHQkZHJvcHpvbmUub25kcm9wID0gKCBlICkgPT4ge1xyXG5cclxuXHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG5cdFx0XHQkZHJvcHpvbmUuY2xhc3NMaXN0LnJlbW92ZSggJ2ZpbGxhYmxlcGRmcy1kcm9wem9uZS0tZHJvcHBpbmcnICk7XHJcblxyXG5cdFx0XHQvLyBBdHRhY2ggZmlsZSB0byBmaWxlIGlucHV0LlxyXG5cdFx0XHQkZmlsZS5maWxlcyA9IGUuZGF0YVRyYW5zZmVyLmZpbGVzO1xyXG5cdFx0XHQkZmlsZS5kaXNwYXRjaEV2ZW50KCBuZXcgRXZlbnQoICdjaGFuZ2UnLCBlICkgKTtcclxuXHJcblx0XHR9O1xyXG5cclxuXHRcdCRkcm9wem9uZS5hZGRFdmVudExpc3RlbmVyKCAnY2xpY2snLCAoIGUgKSA9PiB7XHJcblxyXG5cdFx0XHQkZmlsZS5jbGljaygpO1xyXG5cclxuXHRcdH0gKTtcclxuXHJcblx0XHQkZmlsZS5hZGRFdmVudExpc3RlbmVyKCAnY2hhbmdlJywgKCBlICkgPT4ge1xyXG5cclxuXHRcdFx0Ly8gSWYgbW9yZSB0aGFuIG9uZSBmaWxlIHdhcyBkcm9wcGVkLCBkaXNwbGF5IGVycm9yLlxyXG5cdFx0XHRpZiAoIGUudGFyZ2V0LmZpbGVzLmxlbmd0aCA+IDEgKSB7XHJcblx0XHRcdFx0YWxlcnQoIHN0cmluZ3MudG9vX21hbnlfZmlsZXMgKTtcclxuXHRcdFx0XHRlLnRhcmdldC52YWx1ZSA9IG51bGw7XHJcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBJZiBmaWxlIGlzIG5vdCBhIFBERiwgZGlzcGxheSBlcnJvci5cclxuXHRcdFx0aWYgKCBlLnRhcmdldC5maWxlc1swXS50eXBlICE9PSAnYXBwbGljYXRpb24vcGRmJyApIHtcclxuXHRcdFx0XHRhbGVydCggc3RyaW5ncy5pbGxlZ2FsX2ZpbGVfdHlwZSApO1xyXG5cdFx0XHRcdGUudGFyZ2V0LnZhbHVlID0gbnVsbDtcclxuXHRcdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIFNldCBjaGFuZ2VkIGZsYWcuXHJcblx0XHRcdGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoICcuZmlsbGFibGVwZGZzLWRyb3B6b25lX19jaGFuZ2VkJyApLnZhbHVlID0gJzEnO1xyXG5cclxuXHRcdFx0Ly8gSWYgdGhpcyBpcyB0aGUgaW1wb3J0IGZvcm0sIHN1Ym1pdCBmb3JtLlxyXG5cdFx0XHRpZiAoICRkcm9wem9uZS5kYXRhc2V0LmltcG9ydCApIHtcclxuXHRcdFx0XHQkZHJvcHpvbmUucGFyZW50RWxlbWVudC5zdWJtaXQoKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0aWYgKCAkdGVtcGxhdGVJbmZvICkge1xyXG5cclxuXHRcdFx0XHQvLyBTZXQgZmlsZSBuYW1lIGluIHRlbXBsYXRlIGluZm8uXHJcblx0XHRcdFx0JHRlbXBsYXRlSW5mby5xdWVyeVNlbGVjdG9yKCAnLmZpbGxhYmxlcGRmcy10ZW1wbGF0ZS1pbmZvX19maWxlLW5hbWUnICkuaW5uZXJIVE1MID0gZS50YXJnZXQuZmlsZXNbIDAgXS5uYW1lO1xyXG5cclxuXHRcdFx0XHQvLyBIaWRlIGRyb3Agem9uZSwgc2hvdyB0ZW1wbGF0ZSBpbmZvLlxyXG5cdFx0XHRcdCRkcm9wem9uZS5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xyXG5cdFx0XHRcdCR0ZW1wbGF0ZUluZm8uc3R5bGUuZGlzcGxheSA9ICdmbGV4JztcclxuXHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIFJlcGxhY2UgdGVtcGxhdGUgbmFtZS5cclxuXHRcdFx0aWYgKCAkbmFtZSAmJiAkbmFtZS52YWx1ZS5sZW5ndGggPT09IDAgKSB7XHJcblx0XHRcdFx0JG5hbWUudmFsdWUgPSBlLnRhcmdldC5maWxlc1swXS5uYW1lLnJlcGxhY2UoIC9cXC5bXi8uXSskLywgJycgKTtcclxuXHRcdFx0XHQkbmFtZS5kaXNwYXRjaEV2ZW50KCBuZXcgRXZlbnQoICdrZXl1cCcgKSApO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0fSApO1xyXG5cclxuXHR9XHJcblxyXG5cdC8vIFVwZGF0ZSB0ZW1wbGF0ZSBuYW1lLlxyXG5cdGlmICggJG5hbWUgJiYgZG9jdW1lbnQucXVlcnlTZWxlY3RvciggJy5maWxsYWJsZXBkZnMtdGVtcGxhdGUtaW5mb19fbmFtZScgKSApIHtcclxuXHRcdCRuYW1lLmFkZEV2ZW50TGlzdGVuZXIoICdrZXl1cCcsICggZSApID0+IHtcclxuXHRcdFx0ZG9jdW1lbnQucXVlcnlTZWxlY3RvciggJy5maWxsYWJsZXBkZnMtdGVtcGxhdGUtaW5mb19fbmFtZScgKS5pbm5lckhUTUwgPSBlLnRhcmdldC52YWx1ZTtcclxuXHRcdH0gKTtcclxuXHR9XHJcblxyXG5cdC8vIFJlc2V0IHRlbXBsYXRlIGZpbGUuXHJcblx0aWYgKCBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCAnLmZpbGxhYmxlcGRmcy10ZW1wbGF0ZS1pbmZvX19hY3Rpb24tLXJlbW92ZScgKSApIHtcclxuXHRcdGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoICcuZmlsbGFibGVwZGZzLXRlbXBsYXRlLWluZm9fX2FjdGlvbi0tcmVtb3ZlJyApLmFkZEV2ZW50TGlzdGVuZXIoICdjbGljaycsICggZSApID0+IHtcclxuXHJcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcclxuXHJcblx0XHRcdC8vIFJlc2V0IGZpbGUgaW5wdXQuXHJcblx0XHRcdGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAkZHJvcHpvbmUuZGF0YXNldC5maWxlICkudmFsdWUgPSBudWxsO1xyXG5cclxuXHRcdFx0Ly8gU2V0IGNoYW5nZWQgZmxhZy5cclxuXHRcdFx0ZG9jdW1lbnQucXVlcnlTZWxlY3RvciggJy5maWxsYWJsZXBkZnMtZHJvcHpvbmVfX2NoYW5nZWQnICkudmFsdWUgPSAnMSc7XHJcblxyXG5cdFx0XHQvLyBIaWRlIHRlbXBsYXRlIGluZm8sIHNob3cgZHJvcCB6b25lLlxyXG5cdFx0XHQkdGVtcGxhdGVJbmZvLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XHJcblx0XHRcdCRkcm9wem9uZS5zdHlsZS5kaXNwbGF5ID0gJ2Jsb2NrJztcclxuXHJcblx0XHR9ICk7XHJcblx0fVxyXG5cclxufSApO1xyXG4iXSwibWFwcGluZ3MiOiJBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFBQTtBQUFBO0FBQ0E7QUFHQTtBQUNBO0FBQ0E7QUFDQTtBQUFBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFFQSIsInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./js/src/admin.js\n");

/***/ }),

/***/ "./scss/admin.scss":
/*!*************************!*\
  !*** ./scss/admin.scss ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9zY3NzL2FkbWluLnNjc3MuanMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zY3NzL2FkbWluLnNjc3M/MjZjZiJdLCJzb3VyY2VzQ29udGVudCI6WyIvLyBleHRyYWN0ZWQgYnkgbWluaS1jc3MtZXh0cmFjdC1wbHVnaW4iXSwibWFwcGluZ3MiOiJBQUFBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./scss/admin.scss\n");

/***/ })

/******/ });