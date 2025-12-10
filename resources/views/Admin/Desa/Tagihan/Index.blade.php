@extends('Admin.Layout.Main')
@section('title', 'Tagaihan Pajak')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.dataTables.min.css">
<style>
    div.dt-buttons {
    display: flex !important;
    flex-direction: row !important;
    gap: 0.5rem; /* jarak antar tombol */
    align-items: center;
}
</style>
@endpush

@section('content')
<h1 class="text-3xl text-black pb-6">Daftar Tagihan Pajak</h1>

<div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
  <span class="block sm:inline">Ubah status dengan tekan "Setor" pada kolom aksi untuk menyelesaikan setor pajak di desa dari pemungut.</span>
  <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
    <button onclick="this.parentElement.parentElement.remove()">
      <svg class="fill-current h-6 w-6 text-blue-500" role="button" xmlns="http://www.w3.org/2000/svg"
           viewBox="0 0 20 20"><title>Close</title><path
          d="M14.348 5.652a1 1 0 00-1.414-1.414L10 7.172 7.066 4.238a1 1 0 10-1.414 1.414L8.586 8.586l-2.934 2.934a1 1 0 101.414 1.414L10 10l2.934 2.934a1 1 0 001.414-1.414L11.414 8.586l2.934-2.934z"/></svg>
    </button>
  </span>
</div>

<div class="w-full mt-6">
    <p class="text-xl pb-3 flex items-center">
        <i class="fas fa-list mr-3"></i> Table
    </p>
    <form id="importForm" action="{{ route('desa.tagihan.import') }}" method="POST" enctype="multipart/form-data" style="display:none;">
        @csrf
        <input type="file" id="importFile" name="file" required>
    </form>

    <div class="bg-white overflow-auto">
        <table class="min-w-full bg-white" id="table-tagihan">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">No.</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">NOP</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Total Tagihan (Rp)</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Sisa Tagihan (Rp)</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Uang Di Pemungut (Rp)</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Uang Di Desa (Rp)</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Status</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
            </tbody>
        </table>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.0/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.dataTables.js"></script>

<script>
$(document).ready(function() {
    let url = '{{ route("desa.tagihan.index") }}';
    var idTagihan;

    let tableTagihan = $('#table-tagihan').DataTable({
        destroy: true,
        paging: true,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: true,
        responsive: true,
        ajax: {
            url: url,
            type: "GET"
        },
        layout: {
            topStart: {
                buttons: [
                    {
                        text: '<i class="fas fa-file-export mr-2"></i> Export Data',
                        className: 'bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow-md',
                        action: function () {
                            window.location.href = "{{ route('desa.tagihan.export') }}";
                        }
                    },
                    {
                        text: '<i class="fas fa-file-import mr-2"></i> Import Data',
                        className: 'bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow-md',
                        action: function () {
                            // Klik otomatis input file
                            document.getElementById('importFile').click();
                        }
                    }

                ]
            }
        },
        columnDefs: [
            {
                targets: 0,
                data: null,
                className: 'dt-center text-center align-middle',
                render: function(data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                targets: 1,
                data: 'nop',
                className: 'dt-center text-center align-middle',
            },
            {
                targets: 2,
                data: 'jumlah',
                className: 'dt-center text-center align-middle',
                render: function(data) {
                    return parseInt(data).toLocaleString('id-ID');
                }
            },
            {
                targets: 3,
                data: 'sisa_tagihan',
                className: 'dt-center text-center align-middle',
                render: function(data) {
                    return parseInt(data).toLocaleString('id-ID');
                }
            },
            {
                targets: 4,
                data: 'uang_dipemungut',
                className: 'dt-center text-center align-middle',
                render: function(data) {
                    return parseInt(data).toLocaleString('id-ID');
                }
            },
            {
                targets: 5,
                data: 'uang_didesa',
                className: 'dt-center text-center align-middle',
                render: function(data) {
                    return parseInt(data).toLocaleString('id-ID');
                }
            },
            {
                targets: 6,
                data: 'status',
                className: 'dt-center text-center align-middle',
                render: function(data, type, row, meta) {
                    if (data === 'belum') {
                        return '<span class="px-2 py-1 rounded-full text-white text-xs font-semibold bg-red-500">Belum Bayar</span>';
                    } else if (data === 'cicilan') {
                        return '<span class="px-2 py-1 rounded-full text-white text-xs font-semibold bg-yellow-500">Di Pemungut</span>';
                    } else if (data === 'dipemungut') {
                        return '<span class="px-2 py-1 rounded-full text-white text-xs font-semibold bg-blue-500">Di Pemungut</span>';
                    } else if (data === 'didesa') {
                        return '<span class="px-2 py-1 rounded-full text-white text-xs font-semibold bg-green-500">Di Desa</span>';
                    } else if (data === 'lunas') {
                        return '<span class="px-2 py-1 rounded-full text-white text-xs font-semibold bg-yellow-500">Di Pemungut</span>';
                    }
                    return data;
                }
            },
            {
                targets: 7,
                data: 'status',
                className: 'dt-center text-center align-middle',
                render: function(data, type, row, meta) {
                    let buttonClass = "";
                    let buttonText = "Setor"; // data = status
                    let disabledAttr = "";

                    switch (data) {
                        case "cicilan":
                            buttonClass = "btn-update-cicilan bg-yellow-600";
                            break;
                        case "lunas":
                            buttonClass = "btn-update-lunas bg-yellow-600";
                            break;
                        case "belum":
                            buttonClass = "bg-gray-600 opacity-50 cursor-not-allowed";
                            disabledAttr = "disabled";
                            break;
                        case "didesa":
                            buttonClass = "btn-update-desa bg-gray-600 opacity-50 cursor-not-allowed";
                            disabledAttr = "disabled";
                            break;
                        case "dikecamatan":
                            buttonClass = "bg-gray-600 opacity-50 cursor-not-allowed";
                            disabledAttr = "disabled";
                            break;
                        default:
                            buttonClass = "bg-gray-600 opacity-50 cursor-not-allowed";
                            disabledAttr = "disabled";
                    }

                    let $button = `
                        <button class="inline-flex items-center gap-2 px-4 py-2 ${buttonClass} text-white text-sm font-medium rounded-lg shadow-md transition-colors duration-200 
                            disabled:opacity-50 disabled:cursor-not-allowed"
                            data-id="${row.id}" 
                            title="Update Status"
                            ${disabledAttr}>
                            ${buttonText} <i class="fas fa-edit"></i>
                        </button>
                    `;

                    return $button;
                }
            },
            {
                targets: 8,
                data: 'masyarakat.pemungut.nama',
                visible: false, // kolom ada di dataset tapi disembunyikan
            },
        ],
    });

    // Submit Update Status
    $(document).on('click', '.btn-update-lunas, .btn-update-cicilan, .btn-update-desa', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Update Status?',
            text: `Perhatikan tidak bisa di ubah kembali!`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim AJAX ke server
                $.ajax({
                    url: '{{ Route("desa.tagihan.updateStatus") }}', // route Laravel
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    tableTagihan.ajax.reload();
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Update gagal!'
                        });
                    }
                });
            }
        });
    });

    document.getElementById('importFile').addEventListener('change', function() {
        if (this.files.length > 0) {
            let formData = new FormData(document.getElementById('importForm'));

            Swal.fire({
                title: 'Mengupload...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
            
            fetch("{{ route('desa.tagihan.import') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                }
            })
            .then(async response => {
                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    throw new Error("Respon server tidak valid");
                }

                if (!response.ok) {
                    // ambil pesan dari controller (message atau errors)
                    let errorMessage = data.message || 
                        (data.errors ? Object.values(data.errors).join(", ") : "Gagal mengimport data");
                    throw new Error(errorMessage);
                }

                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: data.message || "Data berhasil diimport!"
                });
                tableTagihan.ajax.reload();
            })
            .catch(error => {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: error.message || "Terjadi kesalahan saat import!"
                });
            })
            .finally(() => {
                // reset input agar bisa klik file yang sama lagi
                document.getElementById('importFile').value = "";
            });
        }
    });


});
</script>
@endsection