"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _index = _interopRequireDefault(require("../fr/_lib/formatDistance/index.js"));

var _index2 = _interopRequireDefault(require("../fr/_lib/formatRelative/index.js"));

var _index3 = _interopRequireDefault(require("../fr/_lib/localize/index.js"));

var _index4 = _interopRequireDefault(require("../fr/_lib/match/index.js"));

var _index5 = _interopRequireDefault(require("./_lib/formatLong/index.js"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

// Same as fr
// Unique for fr-CA

/**
 * @type {Locale}
 * @category Locales
 * @summary French locale (Canada).
 * @language French
 * @iso-639-2 fra
 */
var locale = {
  code: 'fr-CA',
  formatDistance: _index.default,
  formatLong: _index5.default,
  formatRelative: _index2.default,
  localize: _index3.default,
  match: _index4.default,
  // Unique for fr-CA
  options: {
    weekStartsOn: 0
    /* Sunday */
    ,
    firstWeekContainsDate: 1
  }
};
var _default = locale;
exports.default = _default;
module.exports = exports.default;