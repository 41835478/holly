;
(function($) {
  "use strict";

  $.extend($.fn.dataTable.defaults, {
    "processing": true,
    "serverSide": true,
    "fixedHeader": true,
    "lengthMenu": [20, 30, 50, 100],
    "pageLength": 20
  });
})(jQuery);
