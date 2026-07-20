// ========== SELECTPICKER ==========
$(document).ready(function () {
  // Initialize selectpicker
  $('.search_select_box select').selectpicker();
});

// agar tabel berbahasa indonesia
$(document).ready(function() {
    $('.table').DataTable( {
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.22/i18n/Indonesian.json"
        }
    } );
} );


// agar table dan card memiliki nilai awal saat pertama kali
// ========== FILTER TAHUN CARD ==========
document.addEventListener("DOMContentLoaded", function () {
  const table = document.getElementById("dataTable").getElementsByTagName("tbody")[0].getElementsByTagName("tr");

  filterTable();

  function filterTable() {
    const tahunValue = filterTahun.value;
    
        let mahasiswaPenerima = 0; // Inisialisasi total jumlah mahasiswa
        let mahasiswaAktif = 0; // Inisialisasi total jumlah mahasiswa aktif
        let mahasiswaLulus = 0; // Inisialisasi total jumlah mahasiswa lulus
        let mahasiswaHenti = 0; // Inisialisasi total jumlah mahasiswa henti


    for (let i = 0; i < table.length; i++) {
      const row = table[i];
      const cells = row.getElementsByTagName("td");

      const tahunCell = cells[4].textContent.trim(); // Ganti indeks sesuai dengan kolom Tahun di tabel 

      if ((tahunValue === "" || tahunCell === tahunValue)) {
            row.style.display = ""; // Tampilkan baris yang sesuai

                // Jika sesuai, tambahkan jumlah mahasiswa ke total
                mahasiswaPenerima++;

                const statusCell = cells[6].textContent.toLowerCase().trim();

                if (statusCell === "aktif" || statusCell === "kampus merdeka" || statusCell === "menunggu ujian") {
                  mahasiswaAktif++;
                } else if (statusCell === "lulus") {
                  mahasiswaLulus++;
                } else if (statusCell === "henti" || statusCell === "non-aktif" || statusCell === "cuti"|| statusCell === "keluar") {
                  mahasiswaHenti++;
                }
      } else {
        row.style.display = "none"; // Sembunyikan baris yang tidak sesuai
      }
    }
      // Setelah menghitung total, perbarui nilai pada card-card
      document.getElementById("mahasiswaPenerima").textContent = mahasiswaPenerima;
      document.getElementById("mahasiswaAktif").textContent = mahasiswaAktif;
      document.getElementById("mahasiswaLulus").textContent = mahasiswaLulus;
      document.getElementById("mahasiswaHenti").textContent = mahasiswaHenti;
  }

});


// Javascript Filter Table
document.addEventListener("DOMContentLoaded", function () {
    
    const filterTahun = document.getElementById("filterTahun");
    // const filterPerguruanTinggi = document.getElementById("filterPerguruanTinggi");
    const table = document.getElementById("dataTable").getElementsByTagName("tbody")[0].getElementsByTagName("tr");

    filterTahun.addEventListener("change", filterTable);
    // filterPerguruanTinggi.addEventListener("change", filterTable);

    let isFilterChanged = false; // Tambahkan variabel untuk melacak perubahan filter

    // Fungsi untuk mengirim data ke controller dengan AJAX
    // function kirimDataKeController(tahunValue) {
    //     $.ajax({
    //         type: "POST",
    //         url: "/getMahasiswa",
    //         headers: {
    //             "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
    //         },
    //         data: {
    //             angkatan: tahunValue
    //         },
    //         dataType: "json",
    //         success: function (response) {
    //             // Handle respons dari controller (misalnya, mengganti data tabel)
    //             // console.log("Respons dari controller:", response);
    //             // console.log("Data Mahasiswa:", response.dataMahasiswa);
                
    //             // Setel kembali variabel isFilterChanged menjadi false
    //             isFilterChanged = false;

    //             // Selanjutnya, perbarui filterTable() sesuai dengan data yang diterima dari server
    //             filterTable(response);

    //             // Perbarui tabel dengan data yang diterima
    //             perbaruiTabel(response);

    //             // Perbarui nilai pada card-card
    //             perbaruiNilaiCard(response);
    //         }
    //     });
    // }

    function kirimDataKeController(tahunValue) {
        $.ajax({
            type: "POST",
            url: "/getMahasiswa",
            headers: {
                "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
            },
            data: {
                angkatan: tahunValue
            },
            dataType: "json",
            success: function (response) {
                // Cek apakah URL saat ini mengandung 'ubahalamat'
                if (window.location.href.indexOf('ubahalamat') > -1) {
                    // Jika ya, panggil perbaruiTabelUbahAlamat
                    perbaruiTabelUbahAlamat(response);
                } else {
                    // Jika tidak, lakukan seperti biasa
                    isFilterChanged = false;
                    filterTable(response);
                    perbaruiTabel(response);
                    perbaruiNilaiCard(response);
                }
            }
        });
    }


    function filterTable() {
        const tahunValue = filterTahun.value;
        // const perguruanTinggiValue = filterPerguruanTinggi.value;
        
        if (isFilterChanged) {
            kirimDataKeController(tahunValue);
            isFilterChanged = false;
        }
    }

    function perbaruiTabel(dataMahasiswa) {
        var table = $('#dataTable').DataTable();
        table.clear();

        for (var i = 0; i < dataMahasiswa.length; i++) {
            var rowData = dataMahasiswa[i];
            var newRow = [];

            // Mengisi array newRow sesuai dengan data yang Anda terima
            // newRow.push(rowData.kode_pt);
            // newRow.push(rowData.perguruan_tinggi);
            newRow.push(rowData.nim);
            newRow.push(rowData.nama_siswa);
            newRow.push(rowData.program_studi);
            newRow.push(rowData.jenjang.jenjang);
            newRow.push(rowData.angkatan.Angkatan); 
            newRow.push((rowData.alamat_tinggal ? rowData.alamat_tinggal : '') + " " + 
                                    (rowData.kelurahan_dan_kecamatan ? rowData.kelurahan_dan_kecamatan : '') + " " + 
                                    (rowData.kota_dan_kabupaten ? rowData.kota_dan_kabupaten : '') + " " + 
                                    (rowData.provinsi ? rowData.provinsi : ''));            
            newRow.push(rowData.status);

            table.row.add(newRow);
        }

        table.draw();
    }

    // untuk memperbarui tabel ubah alamat
    function perbaruiTabelUbahAlamat(dataMahasiswa) {
        var table = $('#dataTable').DataTable();
        table.clear();

        for (var i = 0; i < dataMahasiswa.length; i++) {
            var mahasiswa = dataMahasiswa[i];
            var newRow = [];

            newRow.push(mahasiswa.nim);
            newRow.push(mahasiswa.nama_siswa);
            newRow.push(mahasiswa.program_studi);
            newRow.push(mahasiswa.jenjang.jenjang);
            newRow.push(mahasiswa.angkatan.Angkatan);
            newRow.push((mahasiswa.alamat_tinggal ? mahasiswa.alamat_tinggal : '') + " " + 
                        (mahasiswa.kelurahan_dan_kecamatan ? mahasiswa.kelurahan_dan_kecamatan : '') + " " + 
                        (mahasiswa.kota_dan_kabupaten ? mahasiswa.kota_dan_kabupaten : '') + " " + 
                        (mahasiswa.provinsi ? mahasiswa.provinsi : ''));
            newRow.push('<button type="button" class="btn btn-success alamat" onclick="editAlamat(' + mahasiswa.id + ')"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg> Edit Alamat</button>');

            table.row.add(newRow);
        }

        table.draw();
    }




    function perbaruiNilaiCard(dataMahasiswa) {
        var mahasiswaPenerima = dataMahasiswa.length;
        var mahasiswaAktif = 0;
        var mahasiswaLulus = 0;
        var mahasiswaHenti = 0;

        for (var i = 0; i < dataMahasiswa.length; i++) {
            var status = dataMahasiswa[i].status.trim();            
            // console.log("Status mahasiswa: " + status); // Tambahkan ini untuk debug
            if (status === "Aktif" || status === "Kampus Merdeka" || status === "Menunggu Ujian") {
                mahasiswaAktif++;
            } else if (status === "Lulus") {
                mahasiswaLulus++;
            } else if (status === "Henti" || status === "Non-Aktif" || status === "Cuti" || status === "Keluar") {
                mahasiswaHenti++;
            }
        }

        // Perbarui nilai pada card-card
        document.getElementById("mahasiswaPenerima").textContent = mahasiswaPenerima;
        document.getElementById("mahasiswaAktif").textContent = mahasiswaAktif;
        document.getElementById("mahasiswaLulus").textContent = mahasiswaLulus;
        document.getElementById("mahasiswaHenti").textContent = mahasiswaHenti;
    }

    // Event listener untuk filterTahun dan filterPerguruanTinggi
    filterTahun.addEventListener("change", function () {
        isFilterChanged = true;
        filterTable();
    });
    filterPerguruanTinggi.addEventListener("change", function () {
        isFilterChanged = true;
        filterTable();
    });
});





// ========== Event listener untuk tombol "Export PDF" dengan ID "exportPDF" ==========
document.addEventListener("DOMContentLoaded", function () {
    const exportPDF = document.getElementById("exportPDF");
    if (exportPDF) {
        exportPDF.addEventListener("click", function (e) {
            e.preventDefault();
            console.log("Tombol Export PDF diklik");
            // Tindakan lain yang sesuai
        });
    }
});
 
// menampilkan detail informasi

function detail(id) {
  // Mengarahkan pengguna ke halaman detail dengan ID sebagai parameter
  window.location.href = '/dashboard/detail/' + id;
}
// menampilkan form edit alamat
function editAlamat(id) {
  // Redirect atau tampilkan tampilan edit berdasarkan ID
  window.location.href = "/dashboard/edit/" + id;

  const alamat = $('#alamat_tinggal');
  console.log("Alamatnya: ", alamat);
}




document.addEventListener("DOMContentLoaded", function () {
  $('#tambahData').on('click', function () {
    // Mengumpulkan semua data input dari setiap langkah
    var formData = {
       // Menu 1
       alamat_tinggal: $('#alamat_tinggal').val(),
               rt: $('#rt').val(),
               rw: $('#rw').val(),
               kecamatan: $('#kecamatan').val(),
               kelurahan: $('#kelurahan').val(),
               kota_dan_kabupaten: $('#kota_dan_kabupaten').val(),
               provinsi: $('#provinsi').val(),
               kodePos: $('#kodePos').val(),
    }
    console.log (formData)
    $.ajax({
      type: "POST",
      url: "/ubahalamat",
      headers: {
            "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
      },
      data: {
            formData: JSON.stringify(formData),
      },
      dataType: "json",
      success: function (response) {
         console.log("Permintaan Ajax berhasil, response:", response);
               
      },
      error: function (error) {
        console.log("Permintaan Ajax gagal, error : ", error);
      }
  });
   
    
})
})




function reset(e) {
  e.wrap('<form>').closest('form').get(0).reset();
  e.unwrap();
}

$(".dropzone").change(function(){
  readFile(this);
});

$('.dropzone-wrapper').on('dragover', function(e) {
  e.preventDefault();
  e.stopPropagation();
  $(this).addClass('dragover');
});

$('.dropzone-wrapper').on('dragleave', function(e) {
  e.preventDefault();
  e.stopPropagation();
  $(this).removeClass('dragover');
});

// Fungsi untuk menghapus tampilan pratinjau
function removePreview() {
   var boxZone = $('.preview-zone').find('.box-body');
   var previewZone = $('.preview-zone');
   var dropzone = $('.dropzone');
   boxZone.empty();
   previewZone.addClass('hidden');
   reset(dropzone);
}

// Menambahkan event click pada tombol "Hapus"
$('.remove-preview').on('click', function() {
   removePreview();
});


// ========== MULTI FORM ==========

$(document).ready(function(){
    
  var current_fs, next_fs, previous_fs; //fieldsets
  var opacity;
  
  $(".next").click(function(){
      
      current_fs = $(this).parent();
      next_fs = $(this).parent().next();
      
      //Add Class Active
      $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

      //show the next fieldset
      next_fs.show(); 
      //hide the current fieldset with style
      current_fs.animate({opacity: 0}, {
          step: function(now) {
              // for making fielset appear animation
              opacity = 1 - now;
  
              current_fs.css({
                  'display': 'none',
                  'position': 'relative'
              });
              next_fs.css({'opacity': opacity});
          }, 
          duration: 600
      });
  });
  
  $(".previous").click(function(){
      
      current_fs = $(this).parent();
      previous_fs = $(this).parent().prev();
      
      //Remove class active
      $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

      //show the previous fieldset
      previous_fs.show();
  
      //hide the current fieldset with style
      current_fs.animate({opacity: 0}, {
          step: function(now) {
              // for making fielset appear animation
              opacity = 1 - now;
  
              current_fs.css({
                  'display': 'none',
                  'position': 'relative'
              });
              previous_fs.css({'opacity': opacity});
          }, 
          duration: 600
      });
  });
  
  $('.radio-group .radio').click(function(){
      $(this).parent().find('.radio').removeClass('selected');
      $(this).addClass('selected');
  });
  
  $(".submit").click(function(){
      return false;
  })
      
  });


// ========== MODAL PEMBATALAN ==========
// Fungsi untuk menampilkan modal setelah halaman selesai dimuat
window.addEventListener('DOMContentLoaded', function () {
  var ModalPencairan = new bootstrap.Modal(document.getElementById('ModalPencairan'), {
    backdrop: 'static',
  });
  ModalPencairan.show();
});


// ========== CHECKBOX ALL ==========
// Mendapatkan referensi checkbox di header
const checkAllCheckbox = document.getElementById('checkAll');

// Mendapatkan referensi semua checkbox di seluruh baris
const rowCheckboxes = document.querySelectorAll('.checkbox-row');

// Menambahkan event listener pada checkbox di header
checkAllCheckbox.addEventListener('change', function () {
    // Ambil nilai properti checked dari checkbox di header
    const isChecked = this.checked;

    // Atur properti checked untuk semua checkbox di seluruh baris
    rowCheckboxes.forEach(function (checkbox) {
        checkbox.checked = isChecked;
    });
});


// Mendapatkan semua elemen dropdown
const dropdowns = document.querySelectorAll('.form-control');

// Tambahkan event listener untuk setiap dropdown
dropdowns.forEach(function (dropdown) {
    dropdown.addEventListener('change', function () {
        // Mendapatkan nilai opsi yang dipilih
        const selectedOption = this.value;

        // Hapus semua class latar belakang yang mungkin ada
        this.classList.remove('bg-success', 'bg-warning', 'bg-danger');

        // Tambahkan class sesuai dengan opsi yang dipilih
        if (selectedOption === 'Lulus') {
            this.classList.add('bg-success');
        } else if (selectedOption === 'Henti') {
            this.classList.add('bg-warning');
        } else if (selectedOption === 'Digantikan') {
            this.classList.add('bg-danger');
        }
    });
});


// ========== SUBMIT TOMBOL AJUKAN PEMBATALAN ==========
$(document).ready(function () {
  $("#btnAjukan").click(function () {
      // Mengambil data dari form
      var tahunAkademik = $("#tahun_akademik").val();
      var SK = $("#SK").val();
      var tglSK = $("#tgl-SK").val();
      var keterangan = $("#keterangan").val();

      // Membuat objek FormData untuk mengirim data form
      var formData = new FormData();
      formData.append('tahun_akademik', tahunAkademik);
      formData.append('SK', SK);
      formData.append('tgl-SK', tglSK);
      formData.append('keterangan', keterangan);

      // Mengirim data form ke controller dengan Ajax
      $.ajax({
          type: "POST",
          url: "/dashboard/pembatalan", // Ganti URL_TO_CONTROLLER sesuai dengan controller Anda
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {
              // Handle respon dari controller (misalnya, tampilkan pesan sukses atau lakukan redirect)
              console.log("Permintaan AJAX Berhasil", response);
              // Tampilkan modal sukses
              $('#modalSukses').modal('show');
          },
          error: function (error) {
              // Handle kesalahan jika terjadi
              console.error("Permintaan AJAX Gagal", error);
          }
      });
  });
});


// ========== BUTTON DISABLED JIKA FIELD YANG REQUIRED BELUM TERISI ==========
$(document).ready(function () {
  // Menonaktifkan tombol "Next" saat halaman selesai dimuat
  $("#btnNext").prop("disabled", true);

  // Menambahkan event handler saat input berubah
  $("input[required], select[required]").on("input change", function () {
    // Memeriksa apakah semua input yang wajib diisi sudah terisi
    if (isFormValid()) {
      // Mengaktifkan tombol "Next" jika semua input yang wajib diisi sudah terisi
      $("#btnNext").prop("disabled", false);
    } else {
      // Menonaktifkan tombol "Next" jika ada input yang belum terisi
      $("#btnNext").prop("disabled", true);
    }
  });

  // Fungsi untuk memeriksa apakah semua input yang wajib diisi sudah terisi
  function isFormValid() {
    var inputs = $("input[required], select[required]");
    var isAnyEmpty = false;

    inputs.each(function() {
      if ($(this).val() === "") {
        isAnyEmpty = true;
        return false; // Keluar dari loop jika ada input yang kosong
      }
    });

    return !isAnyEmpty;
  }

});


// ========================Button next validasi=============================
$(document).ready(function () {
    // Disable the "next-submit" button on page load
    $("#next-submit").prop("disabled", true);

    // Menangani perubahan nilai pada dropdown di tabel-validasi
    $('#tabel-validasi').on('change', 'select[name="status[]"]', function () {
        var nextButton = $("#next-submit");

        // Setiap kali nilai dropdown berubah
        var allSelected = true;

        // Loop melalui semua dropdown dengan nama "status[]"
        $('select[name="status[]"]').each(function() {
            var selectedStatus = $(this).val();
            console.log("Status: ", selectedStatus);

            var bgColorClass = getBackgroundColorClass(selectedStatus);
            $(this).removeClass().addClass('form-control ' + bgColorClass);

            // Periksa apakah setiap dropdown telah dipilih selain 'Pilih'
            if (selectedStatus == null || selectedStatus === 'Pilih') {
                allSelected = false;
                return false; // Keluar dari loop .each() jika menemukan select yang masih kosong atau 'Pilih'
            }
        });

        // Sesuaikan status tombol next-submit
        nextButton.prop('disabled', !allSelected);
    });


// Fungsi untuk mendapatkan kelas warna latar belakang berdasarkan status
function getBackgroundColorClass(status) {
    switch (status) {
        case 'Lulus':
            return 'bg-success';
        case 'Henti':
            return 'bg-warning';
        case 'Digantikan':
            return 'bg-danger';
        default:
            return '';
    }
}
    // sampai sini

    // Function to update the state of the "next-validasi" button
    function updateNextValidasiButtonState() {
        var checkedCheckboxes = $(".checkbox-row:checked");
        if (checkedCheckboxes.length > 0) {
            $(".next-validasi").prop("disabled", false);
        } else {
            $(".next-validasi").prop("disabled", true);
        }
    }

    $(".checkbox-row").change(function () {
        // Update the state of the "next-validasi" button when checkboxes are checked or unchecked
        updateNextValidasiButtonState();
    });

    // Handle checkAll checkbox
    $("#checkAll").change(function () {
        $(".checkbox-row").prop("checked", this.checked);
        // Update the state of the "next-validasi" button after checking or unchecking all checkboxes
        updateNextValidasiButtonState();
    });

    $(".next-validasi").click(function () {
        // Ambil semua checkbox yang dicheck
        var checkedCheckboxes = $(".checkbox-row:checked");

        // Buat array untuk menyimpan data checkbox
        var dataCheckboxes = [];

        // Iterasi checkbox
        checkedCheckboxes.each(function (index, checkbox) {
            // Tambahkan data checkbox ke array
            dataCheckboxes.push({
                nim: $(checkbox).closest("tr").find("td:eq(1)").text(),
                nama_siswa: $(checkbox).closest("tr").find("td:eq(2)").text(),
                program_studi: $(checkbox).closest("tr").find("td:eq(3)").text(),
                jenjang: $(checkbox).closest("tr").find("td:eq(4)").text(),
                angkatan: $(checkbox).closest("tr").find("td:eq(5)").text(),
                alamat_tinggal: $(checkbox).closest("tr").find("td:eq(6)").text(),
                status: $(checkbox).closest("tr").find("td:eq(7)").text(),
            });
        });

        // Validasi pembatalan
        $('#tabel-validasi').empty();

        if (dataCheckboxes.length > 0) {
            console.log("false");
            $.each(dataCheckboxes, function (index, mahasiswa) {
                var newRow = "<tr>" +
                "<td><input type='hidden' name='nim[]' value='" + mahasiswa.nim + "'>" + mahasiswa.nim + "</td>" +
                "<td><input type='hidden' name='nama_siswa[]' value='" + mahasiswa.nama_siswa + "'>" + mahasiswa.nama_siswa + "</td>" +
                "<td><input type='hidden' name='program_studi[]' value='" + mahasiswa.program_studi + "'>" + mahasiswa.program_studi + "</td>" +
                "<td><input type='hidden' name='jenjang[]' value='" + mahasiswa.jenjang + "'>" + mahasiswa.jenjang + "</td>" +
                "<td><input type='hidden' name='angkatan[]' value='" + mahasiswa.angkatan + "'>" + mahasiswa.angkatan + "</td>" +
                "<td><input type='hidden' name='alamat_tinggal[]' value='" + mahasiswa.alamat_tinggal + "'>" + mahasiswa.alamat_tinggal + "</td>" +
                "<td><select id='inputState' class='form-control' name='status[]'>" +
                    "<option disabled selected>Pilih</option>" +
                    "<option class='bg-success'>Lulus</option>" +
                    "<option class='bg-warning'>Henti</option>" +
                    "<option class='bg-danger'>Digantikan</option>" +
                    "</select></td>" +
                "</tr>";
                $('#tabel-validasi').append(newRow);
            });
        }

        // Validasi pencairan
        $('#tabel-validasi-pencairan').empty();

        if (dataCheckboxes.length > 0) {
            $.each(dataCheckboxes, function (index, mahasiswa) {
                var newRow = "<tr>" +
                "<td><input type='hidden' name='nim[]' value='" + mahasiswa.nim + "'>" + mahasiswa.nim + "</td>" +
                "<td><input type='hidden' name='nama_siswa[]' value='" + mahasiswa.nama_siswa + "'>" + mahasiswa.nama_siswa + "</td>" +
                "<td><input type='hidden' name='program_studi[]' value='" + mahasiswa.program_studi + "'>" + mahasiswa.program_studi + "</td>" +
                "<td><input type='hidden' name='jenjang[]' value='" + mahasiswa.jenjang + "'>" + mahasiswa.jenjang + "</td>" +
                "<td><input type='hidden' name='angkatan[]' value='" + mahasiswa.angkatan + "'>" + mahasiswa.angkatan + "</td>" +
                "<td><input type='hidden' name='alamat_tinggal[]' value='" + mahasiswa.alamat_tinggal + "'>" + mahasiswa.alamat_tinggal + "</td>" +
                "<td><input type='hidden' name='status[]' value='" + mahasiswa.status + "'>" + mahasiswa.status + "</td>"
                "</tr>";
                $('#tabel-validasi-pencairan').append(newRow);
            });
        }

        // Update the state of the "next-validasi" button after processing data
        updateNextValidasiButtonState();
    });
});

// ============Untuk mengirimkan data mahasiswa serta formulir lainnya ke server agar mengupdate data status=======================
$(document).ready(function () {
    $('.next-submit').click(function () {
        // Mendefinisikan objek FormData
        const formData = new FormData();

        // Tangkap elemen-elemen formulir dengan nama yang spesifik
        const formElements = document.querySelectorAll("input[name='tahun_akademik'], input[name='no_sk'], input[name='tanggal_surat'], textarea[name='keterangan_tambahan'], input[name='scan_permohonan_jpg'], input[name='scan_permohonan_pdf']");

        // Loop melalui elemen-elemen tersebut dan simpan nilai ke dalam objek formData
        formElements.forEach(function (element) {
            if(element.type === "file"){
                let file = element.files[0]; // Mendapatkan file dari input
                formData.append(element.name, file); // Menambahkan file ke FormData
            } else {
                formData.append(element.name, element.value); // Menambahkan teks ke FormData
            }
        });

        // Mendapatkan data data mahasiswa dari elemen yang dipilih
        const mahasiswaRows = document.querySelectorAll("#dataTable tbody#tabel-validasi tr");

        // Membuat array untuk menyimpan data mahasiswa
        const dataMahasiswa = [];

        // Loop melalui baris data mahasiswa
        mahasiswaRows.forEach(function (row) {
            const rowData = {};
            const columns = row.querySelectorAll("td");

            // Menyimpan data dari setiap kolom
            rowData.NIM = columns[0].textContent;
            rowData.Nama = columns[1].textContent;
            rowData.Prodi = columns[2].textContent;
            rowData.Jenjang = columns[3].textContent;
            rowData.TahunMasuk = columns[4].textContent;
            rowData.Alamat = columns[5].textContent;

            // Ambil nilai dari elemen <select> dengan nama "status"
            const statusSelect = row.querySelector("select[name='status']");
            rowData.Status = statusSelect.options[statusSelect.selectedIndex].text;

            // Menambahkan data mahasiswa ke dalam array
            dataMahasiswa.push(rowData);
        });

        // Menambahkan data mahasiswa ke dalam formData
        formData.append('mahasiswa', JSON.stringify(dataMahasiswa));

        // Menggunakan AJAX untuk mengirim data ke server
        // $.ajax({
        //   type: 'POST',
        //   url: '/pembatalan/ajukanPembatalan',
        //   headers: {
        //       "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
        //   },
        //   data: formData,
        //   processData: false,  // Penting untuk mengatur ini menjadi false sehingga jQuery tidak akan mencoba mengubah FormData menjadi string
        //   contentType: false,  // Penting untuk mengatur ini menjadi false sehingga server akan mengenali permintaan ini sebagai permintaan multipart/form-data.
        //   success: function (response) {
        //       var pesan = response.message; 
        //       if (pesan) {
        //           alert(pesan);
        //       }
        //       console.log(response);
        //   },
        //   error: function (error) {
        //       console.log(error);
        //   }
        // });
    });
});