import formatDistance from "../en-US/_lib/formatDistance/index.js";
import formatRelative from "../en-US/_lib/formatRelative/index.js";
import localize from "../en-US/_lib/localize/index.js";
import match from "../en-US/_lib/match/index.js";
import formatLong from "../en-GB/_lib/formatLong/index.js";
/**
 * @type {Locale}
 * @category Locales
 * @summary English locale (Ireland).
 * @language English
 * @iso-639-2 eng
 */

var locale = {
  code: 'en-IE',
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