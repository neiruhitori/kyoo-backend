// Call the dataTables jQuery plugin
$(function() {
  $('#dataTable').DataTable({
    "language": {
      "emptyTable": "Tidak ada data",
      "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
      "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
      "infoFiltered": "(ter-filter dari _MAX_ total data)",
      "infoPostFix": "",
      "thousands": ",",
      "lengthMenu": "Tampilkan _MENU_ data",
      "loadingRecords": "Memuat...",
      "processing": "Memproses...",
      "search": "Cari:",
      "zeroRecords": "Tidak ada data yang ditemukan",
      "paginate": {
          "first": "Awal",
          "last": "Akhir",
          "next": "Berikutnya",
          "previous": "Sebelum"
      },
      "aria": {
          "sortAscending": ": aktifkan untuk mengurutkan kolom menaik",
          "sortDescending": ": aktifkan untuk mengurutkan kolom menurun"
      }
    }
  });
});
