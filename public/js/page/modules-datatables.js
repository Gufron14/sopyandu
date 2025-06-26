"use strict";

$("[data-checkboxes]").each(function () {
  var me = $(this),
    group = me.data('checkboxes'),
    role = me.data('checkbox-role');

  me.change(function () {
    var all = $('[data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"])'),
      checked = $('[data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"]):checked'),
      dad = $('[data-checkboxes="' + group + '"][data-checkbox-role="dad"]'),
      total = all.length,
      checked_length = checked.length;

    if (role == 'dad') {
      if (me.is(':checked')) {
        all.prop('checked', true);
      } else {
        all.prop('checked', false);
      }
    } else {
      if (checked_length >= total) {
        dad.prop('checked', true);
      } else {
        dad.prop('checked', false);
      }
    }
  });
});

// $("#table-1").dataTable({
//   "columnDefs": [
//     { "sortable": false, "targets": [2,3] }
//   ]
// });

$("#table-1").dataTable({
  "language": {
    "sEmptyTable": "Tidak ada data tersedia di tabel",
    "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
    "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 entri",
    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
    "sInfoPostFix": "",
    "sLengthMenu": "Tampilkan _MENU_ entri",
    "sLoadingRecords": "Memuat...",
    "sProcessing": "Memproses...",
    "sSearch": "Cari:",
    "sZeroRecords": "Tidak ada data yang cocok ditemukan",
    "oPaginate": {
      "sFirst": "Pertama",
      "sLast": "Terakhir",
      "sNext": "Berikutnya",
      "sPrevious": "Sebelumnya"
    },
    "oAria": {
      "sSortAscending": ": aktifkan untuk mengurutkan kolom ini secara ascending",
      "sSortDescending": ": aktifkan untuk mengurutkan kolom ini secara descending"
    }
  }
});

$("#table-2").dataTable({
  "columnDefs": [
    { "sortable": false, "targets": [0, 2, 3] }
  ]
});
