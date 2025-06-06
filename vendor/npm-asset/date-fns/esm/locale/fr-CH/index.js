// Same as fr
import formatDistance from "../fr/_lib/formatDistance/index.js";
import localize from "../fr/_lib/localize/index.js";
import match from "../fr/_lib/match/index.js";
// Unique for fr-CH
import formatLong from "./_lib/formatLong/index.js";
import formatRelative from "./_lib/formatRelative/index.js";
/**
 * @type {Locale}
 * @category Locales
 * @summary French locale (Switzerland).
 * @language French
 * @iso-639-2 fra
 */

var locale = {
  code: 'fr-CH',
  formatDistance: formatDistance,
  formatLong: formatLong,
  formatRelative: formatRelative,
  localize: localize,
  match: match,
  options: {
    weekStartsOn: 1
    /* Monday */
    ,
    firstWeekContainsDate: 4
  }
};
export default locale;