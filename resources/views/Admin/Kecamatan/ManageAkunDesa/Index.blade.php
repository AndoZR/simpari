@extends('Admin.Layout.Main')
@section('title', 'Manage Akun Desa')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.dataTables.min.css">
@endpush

@section('content')
<h1 class="text-3xl text-black pb-6">Manage Akun Desa</h1>

<div class="w-full mt-6">
    <p class="text-xl pb-3 flex items-center">
        <i class="fas fa-list mr-3"></i> Table
    </p>
    <div class="bg-white overflow-auto">
        <table class="min-w-full bg-white" id="table-akun-desa">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">No.</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Nama Desa</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">NIK</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Kontak</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Aksi</td>
                </tr>
            </thead>
            <tbody class="text-gray-700">
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create/Update -->
<div id="modal-akun-desa" 
     class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <!-- Modal Box -->
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        
        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-3">
            <h5 class="text-xl font-semibold">Akun Desa`</h5>
            <button data-action="close" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">
                ✕
            </button>
        </div>

        <!-- Form -->
        <form id="form-akun-desa" class="space-y-4 mt-4">
            @csrf
            <!-- NIK -->
            <div>
                <label for="nik" class="block text-sm font-medium text-gray-700">
                    NIk <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nik" id="nik" 
                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 
                           focus:ring-blue-500 focus:border-blue-500">
                <p class="text-red-500 text-sm nik_error"></p>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" id="password" 
                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 
                           focus:ring-blue-500 focus:border-blue-500">
                <p class="text-red-500 text-sm password_error"></p>
            </div>

            <!-- Pilih Desa -->
            <div>
                <label for="village_id" class="block text-sm font-medium text-gray-700">
                    Nama Desa <span class="text-red-500">*</span>
                </label>

                <select name="village_id" id="village_id"
                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2
                        focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Desa --</option>

                    @foreach ($villages as $village)
                        <option value="{{ $village->id }}">{{ $village->name }}</option>
                    @endforeach
                </select>

                <p class="text-red-500 text-sm village_id_error"></p>
            </div>


            <!-- Telepon -->
            <div>
                <label for="telepon" class="block text-sm font-medium text-gray-700">
                    Telepon <span class="text-red-500">*</span>
                </label>
                <input type="text" name="telepon" id="telepon" 
                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 
                           focus:ring-blue-500 focus:border-blue-500">
                <p class="text-red-500 text-sm telepon_error"></p>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-2 pt-4 border-t">
                <button data-action="close" type="button"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.0/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.dataTables.js"></script>

<script>
    $(document).ready(function () {
        let url = '{{ route("kecamatan.manageAkunDesa.index") }}';
        var idAkunDesa;

        let tableAkunAdminDesa = $('#table-akun-desa').DataTable({
            destroy: true,
            paging: true,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: url,
                type: "GET"
            },
            layout: {
                topStart: {
                    buttons: [
                        {
                            text: '<i class="fas fa-plus mr-2"></i> Tambah Data',
                            className: 'bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow-md',
                            action: function () {
                                // Show modal saat tombol diklik
                                $('#modal-akun-desa').removeClass('hidden');
                            }
                        }
                    ]
                },
            },
            columnDefs: [
                {
                    targets: 0,
                    width: "10px",
                    data: null,
                    className: 'text-center align-middle',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    targets: 1,
                    width: "250px",
                    data: 'desa.name',
                    className: 'text-center align-middle',
                },
                {
                    targets: 2,
                    width: "100px",
                    data: 'user.nik',
                    className: 'text-center align-middle',
                },
                {
                    targets: 3,
                    width: "100px",
                    data: 'telepon',
                    className: 'text-center align-middle',
                },
                {
                    targets: 4,
                    width: "150px",
                    data: null,
                    className: 'text-center align-middle',
                    render: function(data, type, row, meta) {
                        $button = `
                            <button class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg shadow-md transition-colors duration-200 btn-update" 
                            title="Update Data">
                            Update
                            </button>
                        `;

                        return $button;
                    }
                },
            ],
        });

        // Submit Form Create/edit
        $('#form-akun-desa').submit(function(e) {
            e.preventDefault();

            if(idAkunDesa !== undefined){
                url = "{{ route('kecamatan.manageAkunDesa.update', ['id' => ':id']) }}";
                url = url.replace(':id', idAkunDesa)
            }else{
                url = "{{ route('kecamatan.manageAkunDesa.tambah') }}";
            }

            var formData = new FormData($("#form-akun-desa")[0]);

            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('*').removeClass('is-invalid');
                },
                success: function(response) {
                    $('#modal-akun-desa').addClass('hidden');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Tersimpan!',
                        text: response.meta.message,
                    });
                    tableAkunAdminDesa.ajax.reload();
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    switch (xhr.status) {
                        case 422:
                        var errors = xhr.responseJSON.meta.message;
                        var message = '';
                        $.each(errors, function(key, value) {
                            message = value;
                            $('*[name="' + key + '"]').addClass('is-invalid');
                            $('.invalid-feedback.' + key + '_error').html(value);
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: message,
                        })
                        break;
                        default:
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan!',
                        })
                        break;
                    }
                }
            });
        });

        // Edit Data
        $('#table-akun-desa tbody').on('click', '.btn-update', function() {

            var data = tableAkunAdminDesa.row($(this).closest('tr')).data();

            idAkunDesa = data.id;

            // Pastikan akses datanya benar
            $('#village_id').val(data.desa.id).trigger('change');
            $('input[name="nik"]').val(data.user.nik || '');
            $('input[name="telepon"]').val(data.telepon || '');

            // Show modal
            $('#modal-akun-desa').removeClass('hidden');
        });

        // Fungsi umum untuk menutup modal
        function setupModal(modalId) {
            const $modal = $(modalId);

            $modal.find('button[data-action="close"]').on('click', function() {
                try {
                    resetModal($modal);
                } catch(e) {
                    console.warn('resetModal skip:', e.message);
                }
                $modal.addClass('hidden');
            });
        }

        // Fungsi reset modal
        function resetModal(modalId) {
            let $modal = $(modalId);
            $modal.find('form')[0].reset(); // reset semua input
            $modal.find('*').removeClass('is-invalid'); // hapus validasi
            $modal.find('.custom-file-label').html('Pilih file...'); // reset label file
            $modal.find('select').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).val(null).trigger('change'); // reset Select2
                }
            });
        }

        // Setup kedua modal
        setupModal('#modal-akun-desa');
    })
</script>
@endsection