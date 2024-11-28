/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!*******************************************!*\
  !*** ./blocks/copy_to_clipboard/index.js ***!
  \*******************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const {
  registerBlockType
} = wp.blocks;
const {
  __
} = wp.i18n;
const {
  useBlockProps,
  InspectorControls
} = wp.blockEditor;
const {
  PanelBody,
  TextControl,
  ToggleControl,
  ColorPicker
} = wp.components;

// Dashicons의 'clipboard' 아이콘 사용
const ClipboardIcon = 'clipboard'; // Dashicons 기본 제공 아이콘

registerBlockType('rena-plugin/copy-to-clipboard', {
  icon: ClipboardIcon,
  // Dashicons 아이콘 설정

  edit: ({
    attributes,
    setAttributes
  }) => {
    const {
      shortcode,
      copyButtonText,
      qrButtonText,
      showIcons,
      buttonStyle,
      containerStyle
    } = attributes;
    const blockProps = useBlockProps();
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      ...blockProps
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(InspectorControls, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelBody, {
      title: __('Button Settings', 'rena-plugin')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Copy Button Text', 'rena-plugin'),
      value: copyButtonText || '',
      onChange: value => setAttributes({
        copyButtonText: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('QR Code Button Text', 'rena-plugin'),
      value: qrButtonText || '',
      onChange: value => setAttributes({
        qrButtonText: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(ToggleControl, {
      label: __('Show Icons', 'rena-plugin'),
      checked: showIcons,
      onChange: value => setAttributes({
        showIcons: value
      })
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelBody, {
      title: __('Style Settings', 'rena-plugin')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", null, __('Button Background', 'rena-plugin')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(ColorPicker, {
      color: buttonStyle?.backgroundColor || '#007cba',
      onChangeComplete: value => setAttributes({
        buttonStyle: {
          ...buttonStyle,
          backgroundColor: value.hex
        }
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", null, __('Button Text Color', 'rena-plugin')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(ColorPicker, {
      color: buttonStyle?.textColor || '#ffffff',
      onChangeComplete: value => setAttributes({
        buttonStyle: {
          ...buttonStyle,
          textColor: value.hex
        }
      })
    }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "wp-block-rena-copy-to-clipboard",
      style: containerStyle
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Shortcode', 'rena-plugin'),
      value: shortcode || '',
      onChange: value => setAttributes({
        shortcode: value
      }),
      help: __('Enter shortcode in format: [Title]<pre>Content</pre>', 'rena-plugin')
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "button-container"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
      className: "copy-button",
      style: buttonStyle,
      disabled: true
    }, showIcons && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: "dashicon dashicons-clipboard"
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", null, copyButtonText || 'Copy')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
      className: "qr-button",
      style: buttonStyle,
      disabled: true
    }, showIcons && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: "dashicon dashicons-admin-site-alt3"
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", null, qrButtonText || 'QR Code')))));
  },
  save: ({
    attributes
  }) => {
    const {
      title,
      parsedContent,
      copyButtonText,
      qrButtonText,
      showIcons,
      buttonStyle,
      containerStyle
    } = attributes;
    const blockProps = useBlockProps.save();
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      ...blockProps
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "wp-block-rena-copy-to-clipboard",
      style: containerStyle
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("h4", null, title), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "copyable-content",
      "data-content": parsedContent
    }, parsedContent), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "button-container"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
      className: "copy-button",
      "data-clipboard-text": parsedContent,
      style: buttonStyle
    }, showIcons && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: "dashicon dashicons-clipboard"
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", null, copyButtonText || 'Copy')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
      className: "qr-button",
      "data-content": parsedContent,
      style: buttonStyle
    }, showIcons && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      className: "dashicon dashicons-admin-site-alt3"
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", null, qrButtonText || 'QR Code')))));
  }
});
})();

/******/ })()
;
//# sourceMappingURL=copy-to-clipboard.js.map