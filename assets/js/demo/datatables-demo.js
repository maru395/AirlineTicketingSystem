$(document).ready(function() {
  var table = $('#dataTable').DataTable({
    scrollY: "200px", // placeholder, gets overridden immediately below
    scrollCollapse: true,
    autoWidth: false,
    paging: true
  });

  function resizeTableToFit() {
    var wrapperTop = $('#dataTable_wrapper').offset().top;
    var footerHeight = $('footer.sticky-footer').outerHeight() || 0;
    var windowHeight = $(window).height();

    // leave a bit of breathing room below the table (e.g. pagination controls, margins)
    var reserved = 90;

    var available = windowHeight - wrapperTop - footerHeight - reserved;

    // don't let it collapse to something unusably small
    if (available < 150) {
      available = 150;
    }

    table.settings()[0].oScroll.sY = available + "px";
    $('.dataTables_scrollBody').css('max-height', available + 'px');
    table.columns.adjust();
  }

  resizeTableToFit();
  $(window).on('resize', resizeTableToFit);
});