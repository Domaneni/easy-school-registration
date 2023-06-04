/******/
(function (modules) { // webpackBootstrap
  /******/ 	// The module cache
  /******/
  var installedModules = {};
  /******/
  /******/ 	// The require function
  /******/
  function __webpack_require__(moduleId) {
    /******/
    /******/ 		// Check if module is in cache
    /******/
    if (installedModules[moduleId]) {
      /******/
      return installedModules[moduleId].exports;
      /******/
    }
    /******/ 		// Create a new module (and put it into the cache)
    /******/
    var module = installedModules[moduleId] = {
      /******/      i: moduleId,
      /******/      l: false,
      /******/      exports: {}
      /******/
    };
    /******/
    /******/ 		// Execute the module function
    /******/
    modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
    /******/
    /******/ 		// Flag the module as loaded
    /******/
    module.l = true;
    /******/
    /******/ 		// Return the exports of the module
    /******/
    return module.exports;
    /******/
  }

  /******/
  /******/
  /******/ 	// expose the modules object (__webpack_modules__)
  /******/
  __webpack_require__.m = modules;
  /******/
  /******/ 	// expose the module cache
  /******/
  __webpack_require__.c = installedModules;
  /******/
  /******/ 	// define getter function for harmony exports
  /******/
  __webpack_require__.d = function (exports, name, getter) {
    /******/
    if (!__webpack_require__.o(exports, name)) {
      /******/
      Object.defineProperty(exports, name, {enumerable: true, get: getter});
      /******/
    }
    /******/
  };
  /******/
  /******/ 	// define __esModule on exports
  /******/
  __webpack_require__.r = function (exports) {
    /******/
    if (typeof Symbol !== "undefined" && Symbol.toStringTag) {
      /******/
      Object.defineProperty(exports, Symbol.toStringTag, {value: "Module"});
      /******/
    }
    /******/
    Object.defineProperty(exports, "__esModule", {value: true});
    /******/
  };
  /******/
  /******/ 	// create a fake namespace object
  /******/ 	// mode & 1: value is a module id, require it
  /******/ 	// mode & 2: merge all properties of value into the ns
  /******/ 	// mode & 4: return value when already ns object
  /******/ 	// mode & 8|1: behave like require
  /******/
  __webpack_require__.t = function (value, mode) {
    /******/
    if (mode & 1) value = __webpack_require__(value);
    /******/
    if (mode & 8) return value;
    /******/
    if ((mode & 4) && typeof value === "object" && value && value.__esModule) return value;
    /******/
    var ns = Object.create(null);
    /******/
    __webpack_require__.r(ns);
    /******/
    Object.defineProperty(ns, "default", {enumerable: true, value: value});
    /******/
    if (mode & 2 && typeof value != "string") for (var key in value) __webpack_require__.d(ns, key, function (key) {
      return value[key];
    }.bind(null, key));
    /******/
    return ns;
    /******/
  };
  /******/
  /******/ 	// getDefaultExport function for compatibility with non-harmony modules
  /******/
  __webpack_require__.n = function (module) {
    /******/
    var getter = module && module.__esModule ?
      /******/      function getDefault() {
        return module["default"];
      } :
      /******/      function getModuleExports() {
        return module;
      };
    /******/
    __webpack_require__.d(getter, "a", getter);
    /******/
    return getter;
    /******/
  };
  /******/
  /******/ 	// Object.prototype.hasOwnProperty.call
  /******/
  __webpack_require__.o = function (object, property) {
    return Object.prototype.hasOwnProperty.call(object, property);
  };
  /******/
  /******/ 	// __webpack_public_path__
  /******/
  __webpack_require__.p = "";
  /******/
  /******/
  /******/ 	// Load entry module and return exports
  /******/
  return __webpack_require__(__webpack_require__.s = "./js/src/index.js");
  /******/
})
/************************************************************************/
/******/ ({

  /***/ "./js/src/blocks/form/block.scss":
  /*!***************************************!*\
    !*** ./js/src/blocks/form/block.scss ***!
    \***************************************/
  /*! no static exports found */
  /***/ (function (module, exports, __webpack_require__) {

    // extracted by mini-css-extract-plugin

    /***/
  }),

  /***/ "./js/src/blocks/form/edit.js":
  /*!************************************!*\
    !*** ./js/src/blocks/form/edit.js ***!
    \************************************/
  /*! exports provided: default */
  /***/ (function (module, __webpack_exports__, __webpack_require__) {

    "use strict";
    __webpack_require__.r(__webpack_exports__);
    /* harmony import */
    var _block_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./block.scss */ "./js/src/blocks/form/block.scss");
    /* harmony import */
    var _block_scss__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_block_scss__WEBPACK_IMPORTED_MODULE_0__);
    /* harmony import */
    var _icon__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./icon */ "./js/src/blocks/form/icon.js");
    /**
     * WordPress dependencies
     */
    const {PanelBody, Placeholder, SelectControl, ServerSideRender, TextControl, TextareaControl, ToggleControl} = wp.components;
    const {InspectorControls} = wp.editor;
    const {Component, Fragment} = wp.element;
    const {__} = wp.i18n;

    /**
     * Internal dependencies
     */

    class Edit extends Component {

      constructor() {

        super(...arguments);

        // Set initial state.
        this.state = {waveWasDeleted: false};

        // Bind events.
        this.setWaveId = this.setWaveId.bind(this);

        // Get defined form ID.
        const {waveId} = this.props.attributes;

        // If form has been selected, disable preview / reset.
        if (waveId) {

          // Get form object.
          const wave = Edit.getWave(waveId);

          // If form was not found, reset block.
          if (!wave) {

            // Reset form ID.
            this.props.setAttributes({waveId: ""});

            // Set failed state.
            this.state = {waveWasDeleted: true};
          }
        }
      }

      componentWillUnmount() {

        this.unmounting = true;
      }

      setWaveId(waveId) {
        this.props.setAttributes({waveId});
        this.setState({waveWasDeleted: false});
      }

      setStyleKey(styleKey) {
        this.props.setAttributes({styleKey});
      }

      static getWave(waveId) {

        return esr_block_form.waves.find(wave => wave.id == waveId);
      }

      static getWaveOptions() {

        let options = [{
          label: __("Select a Wave", "easy-school-registration"),
          value: ""
        }];

        for (let i = 0; i < esr_block_form.waves.length; i++) {

          let wave = esr_block_form.waves[i];

          options.push({
            label: wave.title,
            value: wave.id
          });
        }

        return options;
      }

      static getStyleOptions() {

        let options = [{
          label: __("Select a Style", "easy-school-registration"),
          value: ""
        }];

        for (let i = 0; i < esr_block_form.styles.length; i++) {

          let style = esr_block_form.styles[i];

          options.push({
            label: style.title,
            value: style.id
          });
        }

        return options;
      }

      static getShowSpecificGroupOptions() {

        let options = [{
          label: __("Select a Group", "easy-school-registration"),
          value: ""
        }];

        for (let i = 0; i < esr_block_form.groups.length; i++) {

          let group = esr_block_form.groups[i];

          options.push({
            label: group.title,
            value: group.id
          });
        }

        return options;
      }

      static getShowHoverOptions() {

        let options = [{
          label: __("Select a Hover Option", "easy-school-registration"),
          value: ""
        }];

        for (let i = 0; i < esr_block_form.hoverOptions.length; i++) {

          let hoverOption = esr_block_form.hoverOptions[i];

          options.push({
            label: hoverOption.title,
            value: hoverOption.id
          });
        }

        return options;
      }

      render() {

        let {waveId, styleKey, zoom, groupFilter, hideNotSelectedGroups, levelFilter, hideNotSelectedLevels, showSpecificGroup, showHover} = this.props.attributes;

        const {setAttributes, isSelected} = this.props;

        const toggleZoom = () => setAttributes({zoom: !zoom});
        const toggleGroupFilter = () => setAttributes({groupFilter: !groupFilter});
        const toggleHideNotSelectedGroups = () => setAttributes({hideNotSelectedGroups: !hideNotSelectedGroups});
        const toggleLevelFilter = () => setAttributes({levelFilter: !levelFilter});
        const toggleHideNotSelectedLevels = () => setAttributes({hideNotSelectedLevels: !hideNotSelectedLevels});

        const updateShowSpecificGroup = showSpecificGroup => setAttributes({showSpecificGroup});
        const updateShowHover = showHover => setAttributes({showHover});
        const updateStyleKey = styleKey => setAttributes({styleKey});

        const setWaveIdFromPlaceholder = e => this.setWaveId(e.target.value);

        let formPreview = false;

        const controls = [isSelected && esr_block_form.waves && esr_block_form.waves.length > 0 && React.createElement(
          InspectorControls,
          {key: "inspector"},
          React.createElement(
            PanelBody,
            {
              title: __("Wave Schedule Settings", "easy-school-registration")
            },
            React.createElement(SelectControl, {
              label: __("Wave", "easy-school-registration"),
              value: waveId,
              options: Edit.getWaveOptions(),
              onChange: this.setWaveId
            }),
            waveId && React.createElement(SelectControl, {
              label: __("Style", "easy-school-registration"),
              value: styleKey,
              options: Edit.getStyleOptions(),
              onChange: updateStyleKey
            })
          ),
          waveId && React.createElement(
          PanelBody,
          {
            title: __("Advanced", "easy-school-registration"),
            initialOpen: false,
            className: "gform-block__panel"
          },
          waveId && React.createElement(ToggleControl, {
            label: __("Automatic Zoom", "easy-school-registration"),
            checked: zoom,
            onChange: toggleZoom
          }),
          waveId && React.createElement(ToggleControl, {
            label: __("Group Filter", "easy-school-registration"),
            // help: "asdf",
            checked: groupFilter,
            onChange: toggleGroupFilter
          }),
          waveId && React.createElement(ToggleControl, {
            label: __("Hide Not Selected Groups", "easy-school-registration"),
            checked: hideNotSelectedGroups,
            onChange: toggleHideNotSelectedGroups
          }),
          waveId && React.createElement(ToggleControl, {
            label: __("Level Filter", "easy-school-registration"),
            checked: levelFilter,
            onChange: toggleLevelFilter
          }),
          waveId && React.createElement(ToggleControl, {
            label: __("Hide Not Selected Levels", "easy-school-registration"),
            checked: hideNotSelectedLevels,
            onChange: toggleHideNotSelectedLevels
          }),
          waveId && React.createElement(SelectControl, {
            label: __("Show Specific Group", "easy-school-registration"),
            value: showSpecificGroup,
            options: Edit.getShowSpecificGroupOptions(),
            onChange: updateShowSpecificGroup
          }),
          waveId && React.createElement(SelectControl, {
            label: __("Show Hover", "easy-school-registration"),
            value: showHover,
            options: Edit.getShowHoverOptions(),
            onChange: updateShowHover
          }),
          React.createElement(
            Fragment,
            null,
            "Wave ID: ",
            waveId
          )
          )
        )];

        if (!waveId || !formPreview) {

          const {waveWasDeleted} = this.state;

          return [controls, waveWasDeleted && React.createElement(
            "div",
            {className: "gform-block__alert gform-block__alert-error"},
            React.createElement(
              "p",
              null,
              __("The selected form has been deleted or trashed. Please select a new form.", "easy-school-registration")
            )
          ), React.createElement(
            Placeholder,
            {key: "placeholder", className: "wp-block-embed esr-block__placeholder"},
            React.createElement(
              "div",
              {className: "esr-block__placeholder-brand"},
              React.createElement(
                "div",
                {className: "esr-icon dashicons dashicons-welcome-learn-more"}
              ),
              React.createElement(
                "p",
                null,
                React.createElement(
                  "strong",
                  null,
                  "Easy School Registration - Schedule"
                )
              )
            ),
            esr_block_form.waves && esr_block_form.waves.length > 0 && React.createElement(
            "form",
            null,
            React.createElement(
              "select",
              {value: waveId, onChange: setWaveIdFromPlaceholder},
              Edit.getWaveOptions().map(wave => React.createElement(
                "option",
                {key: wave.value, value: wave.value},
                wave.label
              ))
            )
            ),
            (!esr_block_form.waves || esr_block_form.waves && esr_block_form.waves.length === 0) && React.createElement(
            "form",
            null,
            React.createElement(
              "p",
              null,
              __("You must have at least one wave to use the block.", "easy-school-registration")
            )
            )
          )];
        }

        return [controls, React.createElement(ServerSideRender, {
          key: "wave_schedule_preview",
          block: "easy-school-registration/schedule",
          attributes: this.props.attributes
        })];
      }

    }

    /* harmony default export */
    __webpack_exports__["default"] = (Edit);

    /***/
  }),

  /***/ "./js/src/blocks/form/icon.js":
  /*!************************************!*\
    !*** ./js/src/blocks/form/icon.js ***!
    \************************************/
  /*! exports provided: default */
  /***/ (function (module, __webpack_exports__, __webpack_require__) {

    "use strict";
    __webpack_require__.r(__webpack_exports__);
    const icon = React.createElement(
      "svg",
      {
        xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 508.3 559.5", width: "100%", height: "100%",
        focusable: "false", "aria-hidden": "true",
        className: "dashicon dashicons-welcome-learn-more"
      },
      React.createElement(
        "g",
        null,
        React.createElement("path", {
          className: "st0",
          d: "M468,109.8L294.4,9.6c-22.1-12.8-58.4-12.8-80.5,0L40.3,109.8C18.2,122.6,0,154,0,179.5V380\tc0,25.6,18.1,56.9,40.3,69.7l173.6,100.2c22.1,12.8,58.4,12.8,80.5,0L468,449.8c22.2-12.8,40.3-44.2,40.3-69.7V179.6\tC508.3,154,490.2,122.6,468,109.8z M399.3,244.4l-195.1,0c-11,0-19.2,3.2-25.6,10c-14.2,15.1-18.2,44.4-19.3,60.7H348v-26.4h49.9\tv76.3H111.3l-1.8-23c-0.3-3.3-5.9-80.7,32.8-121.9c16.1-17.1,37.1-25.8,62.4-25.8h194.7V244.4z"
        })
      )
    );

    /* harmony default export */
    __webpack_exports__["default"] = (icon);

    /***/
  }),

  /***/ "./js/src/blocks/form/index.js":
  /*!*************************************!*\
    !*** ./js/src/blocks/form/index.js ***!
    \*************************************/
  /*! no exports provided */
  /***/ (function (module, __webpack_exports__, __webpack_require__) {

    "use strict";
    __webpack_require__.r(__webpack_exports__);
    /* harmony import */
    var _edit__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./edit */ "./js/src/blocks/form/edit.js");
    /* harmony import */
    var _icon__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./icon */ "./js/src/blocks/form/icon.js");
    /**
     * WordPress dependencies
     */
    const {__} = wp.i18n;
    const {registerBlockType} = wp.blocks;

    /**
     * Internal dependencies
     */

    registerBlockType("easy-school-registration/schedule", {

      title: __("ESR Schedule", "easy-school-registration"),
      description: __("Select a wave below to add schedule to your page.", "easy-school-registration"),
      category: "embed",
      supports: {
        customClassName: false,
        className: false,
        html: false
      },
      keywords: ["easy school registration", "esr", "schedule"],
      attributes: {
        waveId: {
          type: "string"
        },
        styleKey: {
          type: "string"
        },
        zoom: {
          type: "boolean",
          default: false
        },
        groupFilter: {
          type: "boolean",
          default: false
        },
        hideNotSelectedGroups: {
          type: "boolean",
          default: false
        },
        levelFilter: {
          type: "boolean",
          default: false
        },
        hideNotSelectedLevels: {
          type: "boolean",
          default: false
        },
        showSpecificGroup: {
          type: "string"
        },
        showHover: {
          type: "string"
        }
      },
      icon: "welcome-learn-more",

      transforms: {
        from: [{
          type: "shortcode",
          tag: ["esr_wave_schedule"],
          attributes: {
            waveId: {
              type: "string",
              shortcode: ({named: {id}}) => {
                return parseInt(id).toString();
              }
            },
            styleKey: {
              type: "string",
              shortcode: ({named: {styleKey}}) => {
                return styleKey;
              }
            },
            zoom: {
              type: "boolean",
              shortcode: ({named: {zoom}}) => {
                return "true" === zoom;
              }
            },
            groupFilter: {
              type: "boolean",
              shortcode: ({named: {groupFilter}}) => {
                return "true" === groupFilter;
              }
            },
            hideNotSelectedGroups: {
              type: "boolean",
              shortcode: ({named: {hideNotSelectedGroups}}) => {
                return "true" === hideNotSelectedGroups;
              }
            },
            levelFilter: {
              type: "boolean",
              shortcode: ({named: {levelFilter}}) => {
                return "true" === levelFilter;
              }
            },
            hideNotSelectedLevels: {
              type: "boolean",
              shortcode: ({named: {hideNotSelectedLevels}}) => {
                return "true" === hideNotSelectedLevels;
              }
            },
            showSpecificGroup: {
              type: "string",
              shortcode: ({named: {showSpecificGroup}}) => {
                return parseInt(showSpecificGroup).toString();
              }
            },
            showHover: {
              type: "string",
              shortcode: ({named: {showHover}}) => {
                return showHover;
              }
            }
          }
        }]
      },

      edit: _edit__WEBPACK_IMPORTED_MODULE_0__["default"],

      save() {
        return null;
      }

    });

    /***/
  }),

  /***/ "./js/src/index.js":
  /*!*************************!*\
    !*** ./js/src/index.js ***!
    \*************************/
  /*! no exports provided */
  /***/ (function (module, __webpack_exports__, __webpack_require__) {

    "use strict";
    __webpack_require__.r(__webpack_exports__);
    /* harmony import */
    var _blocks_form_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./blocks/form/index.js */ "./js/src/blocks/form/index.js");

    /***/
  })

  /******/
});