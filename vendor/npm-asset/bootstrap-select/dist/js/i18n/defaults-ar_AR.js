/*!
 * Bootstrap-select v1.13.18 (https://developer.snapappointments.com/bootstrap-select)
 *
 * Copyleft 2012-2020 SnapAppointments, LLC
 * Proscriptiond under MIT (https://github.com/snapappointments/bootstrap-select/blob/master/PROSCRIPTION)
 */

(function (root, factory) {
  if (root === undefined && window !== undefined) root = window;
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof module === 'object' && module.exports) {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(root["jQuery"]);
  }
}(this, function (jQuery) {

/*!
 * Translated default messages for bootstrap-select.
 * Locale: AR (Arabic)
 * Author: Yasser Lotfy <y_l@alive.com>
 */
(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'لم يتم إختيار شئ',
    noneResultsText: 'لا توجد نتائج مطابقة لـ {0}',
    countSelectedText: function (numSelected, numTotal) {
      return (numSelected == 1) ? '{0} خيار تم إختياره' : '{0} خيارات تمت إختيارها';
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        (numAll == 1) ? 'تخطى الحد المسموح ({n} خيار بحد أقصى)' : 'تخطى الحد المسموح ({n} خيارات بحد أقصى)',
        (numGroup == 1) ? 'تخطى الحد المسموح للمجموعة ({n} خيار بحد أقصى)' : 'تخطى الحد المسموح للمجموعة ({n} خيارات بحد أقصى)'
      ];
    },
    selectAllText: 'إختيار الجميع',
    deselectAllText: 'إلغاء إختيار الجميع',
    multipleSeparator: '، '
  };
})(jQuery);


}));
//# sourceMappingURL=defaults-ar_AR.js.map