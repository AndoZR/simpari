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

{{-- <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
  <span class="block sm:inline">Ubah status "didesa" menjadi "selesai" untuk menyelesaikan setor pajak di Kecamatan.</span>
  <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
    <button onclick="this.parentElement.parentElement.remove()">
      <svg class="fill-current h-6 w-6 text-blue-500" role="button" xmlns="http://www.w3.org/2000/svg"
           viewBox="0 0 20 20"><title>Close</title><path
          d="M14.348 5.652a1 1 0 00-1.414-1.414L10 7.172 7.066 4.238a1 1 0 10-1.414 1.414L8.586 8.586l-2.934 2.934a1 1 0 101.414 1.414L10 10l2.934 2.934a1 1 0 001.414-1.414L11.414 8.586l2.934-2.934z"/></svg>
    </button>
  </span>
</div> --}}

<div class="w-full mt-6">
    <p class="text-xl pb-3 flex items-center">
        <i class="fas fa-list mr-3"></i> Table
    </p>
    <div class="bg-white overflow-auto">
        <table class="min-w-full bg-white" id="table-tagihan">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">No.</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Desa</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Jumlah Tagihan</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Diterima Desa</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Diterima Kecamatan</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Capaian Tagihan</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Input Setor</th>
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
    let url = '{{ route("kecamatan.tagihan.index") }}';
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
                data: 'desa.name',
                className: 'dt-center text-center align-middle',
                render: function(data, type, row, meta) {
                    return data;
                }
            },
            {
                targets: 2,
                data: 'tagihan',
                className: 'dt-center text-center align-middle',
                render: function(data, type, row, meta) {
                    return data;
                }
            },
            {
                targets: 3,
                data: null,
                className: 'dt-center text-center align-middle',
                render: function(data, type, row, meta) {
                    return data.tagihan - data.sisa_tagihan;
                }
            },
            {
                targets: 4,
                data: 'diterima_kec',
                className: 'dt-center text-center align-middle',
                render: function(data, type, row, meta) {
                    return data;
                }
            },
            {
                targets: 5,
                data: null,
                className: 'dt-center text-center align-middle',
                render: function(data, type, row, meta) {
                    let capaian = 0;
                    if (data.tagihan > 0) {
                        capaian = (data.diterima_kec / data.tagihan) * 100;
                    }

                    let colorClass = (capaian >= 100) ? 'bg-green-500' : 'bg-blue-500';

                    return '<span class="px-2 py-1 rounded-full text-white text-xs font-semibold ' 
                        + colorClass + '">' + capaian.toFixed(1) + '%</span>';
                }
            },
            {
                targets: 6,
                data: null,
                className: 'dt-center text-center align-middle',
                render: function(data, type, row, meta) {
                    return `
                        <div class="flex flex-col items-center space-y-2">
                            <input type="number" 
                                class="input-nominal w-28 px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" 
                                placeholder="Nominal" />
                            <button class="btn-setor bg-blue-500 hover:bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded-md" data-id="${row.id}"">
                                Setor
                            </button>
                        </div>
                    `;
                }
            }
        ],
    });

    // Submit
    $(document).on('click', '.btn-setor', function () {
        let id = $(this).data('id');
        // cari input di dalam row yang sama
        let nominal = $(this).closest('tr').find('.input-nominal').val();

        Swal.fire({
            title: 'Setor Uang Desa?',
            text: `Silahkan Masukkan nominal uang setor!`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim AJAX ke server
                $.ajax({
                    url: '{{ Route("kecamatan.tagihan.updateStatus") }}', // route Laravel
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        nominal: nominal,
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

});
</script>
@endsection