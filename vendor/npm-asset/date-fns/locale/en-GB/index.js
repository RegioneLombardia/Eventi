"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _index = _interopRequireDefault(require("../en-US/_lib/formatDistance/index.js"));

var _index2 = _interopRequireDefault(require("../en-US/_lib/formatRelative/index.js"));

var _index3 = _interopRequireDefault(require("../en-US/_lib/localize/index.js"));

var _index4 = _interopRequireDefault(require("../en-US/_lib/match/index.js"));

var _index5 = _interopRequireDefault(require("./_lib/formatLong/index.js"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * @type {Locale}
 * @category Locales
 * @summary English locale (United Kingdom).
 * @language English
 * @iso-639-2 eng
 */
var locale = {
  code: 'en-GB',
  formatDistance: _index.default,
  formatLong: _index5.default,
  formatRelative: _index2.default,
  localize: _index3.default,
  match: _index4.default,
  options: {
    weekStartsOn: 1
    /* Monday */
    ,
    firstWeekContainsDate: 4
  }
};
var _default = locale;
exports.default = _default;
module.exports = exports.default;