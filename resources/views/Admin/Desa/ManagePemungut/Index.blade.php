@extends('Admin.Layout.Main')
@section('title', 'Manage Akun Pemungut')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.dataTables.min.css">
@endpush

@section('content')
<h1 class="text-3xl text-black pb-6">Manage Akun Pemungut</h1>

<div class="w-full mt-6">
    <p class="text-xl pb-3 flex items-center">
        <i class="fas fa-list mr-3"></i> Table
    </p>
    <div class="bg-white overflow-auto">
        <table class="min-w-full bg-white" id="table-pemungut">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">No.</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Nama</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">NIK</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Kontak</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Alamat</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Aksi</td>
                </tr>
            </thead>
            <tbody class="text-gray-700">
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create/Update -->
<div id="modal-pemungut" 
     class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <!-- Modal Box -->
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        
        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-3">
            <h5 class="text-xl font-semibold">Akun Pemungut</h5>
            <button data-action="close" class="close-modal text-gray-500 hover:text-gray-700 text-2xl leading-none">
                ✕
            </button>
        </div>

        <!-- Form -->
        <form id="form-pemungut" class="space-y-4 mt-4">
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

            <!-- Nama -->
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700">
                    Nama <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama" id="nama" 
                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 
                           focus:ring-blue-500 focus:border-blue-500">
                <p class="text-red-500 text-sm nama_error"></p>
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

            <!-- Alamat -->
            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700">
                    Alamat <span class="text-red-500">*</span>
                </label>
                <textarea name="alamat" id="alamat" rows="3"
                    class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 
                           focus:ring-blue-500 focus:border-blue-500"></textarea>
                <p class="text-red-500 text-sm alamat_error"></p>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-2 pt-4 border-t">
                <button data-action="close" type="button"
                    class="close-modal px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
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

<!-- Modal Plotting -->
<div id="modal-plotting" 
     class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">

    <!-- Modal Box -->
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col max-h-[80vh] overflow-hidden">

        <!-- Header -->
        <div class="flex justify-between items-center p-4 flex-shrink-0 bg-white shadow-md rounded-t-2xl z-10">
            <h5 class="text-xl font-semibold text-gray-800">Atur Plotting Pemungut</h5>
            <button class="text-gray-500 hover:text-gray-700 text-2xl leading-none" data-action="close">✕</button>
        </div>

        <!-- Konten scrollable -->
        <div class="p-6 overflow-y-auto flex-1 bg-gray-50 shadow-inner" style="max-height: calc(80vh - 120px);">

            <!-- Table -->
            <p class="text-xl pb-3 flex items-center font-semibold border-b border-gray-200 text-gray-700 shadow-sm">
                <i class="fas fa-list mr-3 text-gray-500"></i> Daftar Plotting
            </p>
            
            <table id="table-plotting" class="min-w-full bg-white mt-4 shadow-sm rounded-lg overflow-hidden">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm border-r border-gray-700">No.</th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm border-r border-gray-700">Nama</th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm border-r border-gray-700">NOP</th>
                        <th class="text-left uppercase font-semibold text-sm"><input type="checkbox" id="checkAll" class="form-checkbox text-red-600">Check All</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    <!-- Isi via AJAX -->
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-2 p-4 border-t flex-shrink-0 bg-white shadow-inner rounded-b-2xl">
            <button id="btnSelesai" data-action="close" type="button" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
                Selesai
            </button>
        </div>

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
        $('#modal-pemungut').on('click', '.close-modal', function () {
            $('#form-pemungut')[0].reset();
            $('input[name="id"]').val('');
        });


        let url = '{{ route("desa.managePemungut.index") }}';
        var idPemungut;

        let tablePemungut = $('#table-pemungut').DataTable({
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
                            text: '<i class="fas fa-plus mr-2"></i> Tambah Data',
                            className: 'bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow-md',
                            action: function () {
                                // Show modal saat tombol diklik
                                $('#modal-pemungut').removeClass('hidden');
                            }
                        }
                    ]
                },
            },
            columnDefs: [
                {
                    targets: 0,
                    data: null,
                    className: 'text-center align-middle',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    targets: 1,
                    data: 'pemungut_data.nama',
                    className: 'text-center align-middle',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    targets: 2,
                    data: 'nik',
                    className: 'text-center align-middle',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    targets: 3,
                    data: 'pemungut_data.telepon',
                    className: 'text-center align-middle',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    targets: 4,
                    data: 'pemungut_data.alamat',
                    className: 'text-center align-middle',
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    targets: 5,
                    data: null,
                    className: 'text-center align-middle',
                    render: function(data, type, row, meta) {
                        $button = `
                            <button class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-md transition-colors duration-200 btn-plotting" 
                            title="Plotting">
                            Plotting
                            </button>

                            <button class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg shadow-md transition-colors duration-200 btn-update" 
                            title="Update Data">
                            Update
                            </button>

                            <button class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow-md transition-colors duration-200 btn-hapus" 
                            title="Hapus Data">
                            Hapus
                            </button>
                        `;

                        return $button;
                    }
                },
            ],
        });

        // Submit Form Create/edit
        $('#form-pemungut').submit(function(e) {
            e.preventDefault();

            if(idPemungut !== undefined){
                url = "{{ route('desa.managePemungut.update', ['id' => ':id']) }}";
                url = url.replace(':id', idPemungut)
            }else{
                url = "{{ route('desa.managePemungut.tambah') }}";
            }

            var formData = new FormData($("#form-pemungut")[0]);

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
                    $('#modal-pemungut').addClass('hidden');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Tersimpan!',
                        text: response.meta.message,
                    });
                    tablePemungut.ajax.reload();
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
        $('#table-pemungut tbody').on('click', '.btn-update', function() {
            var data = tablePemungut.row($(this).parents('tr')).data();
            idPemungut = data.id;

            // set form action
            $('input[name="nama"]').val(data.pemungut_data.nama);
            $('input[name="nik"]').val(data.nik);
            $('input[name="telepon"]').val(data.pemungut_data.telepon);
            $('textarea[name="alamat"]').val(data.pemungut_data.alamat);

            // show modal
            $('#modal-pemungut').removeClass('hidden');
        });

        // Hapus Data 
        $('#table-pemungut tbody').on('click', '.btn-hapus', function() {
            var data = tablePemungut.row($(this).parents('tr')).data();
            let urlDestroy = "{{ route('desa.managePemungut.hapus', ['id' => ':id']) }}"
            urlDestroy = urlDestroy.replace(':id', data.id);

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                $.ajax({
                    type: "GET",
                    url: urlDestroy,
                    beforeSend: function() {
                    },
                    success: function(data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil dihapus!',
                    })
                    tablePemungut.ajax.reload();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                    switch (xhr.status) {
                        case 500:
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Server Error!',
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
                }
            });
        });

        // PLotting
        // Inisialisasi Select2 untuk masyarakat
        $('#masyarakat').select2({
            placeholder: '-- Pilih Masyarakat --',
            allowClear: true,
            width: '100%' // penting supaya lebar mengikuti container
        });

        let tablePlotting;
        $('#table-pemungut tbody').on('click', '.btn-plotting', function() {
            var data = tablePemungut.row($(this).parents('tr')).data();
            idPemungut = data.pemungut_data.id;

            let urlPlotting = '{{ route("desa.managePemungut.plotting.index", ":idPemungut") }}';
            urlPlotting = urlPlotting.replace(':idPemungut', idPemungut);

            tablePlotting = $('#table-plotting').DataTable({
                destroy: true,
                paging: true,
                lengthChange: false,
                searching: true,
                ordering: false,
                info: true,
                autoWidth: true,
                responsive: true,
                ajax: {
                    url: urlPlotting,
                    type: "GET"
                },
                columnDefs: [
                    {
                        targets: 0,
                        data: null,
                        className: 'text-center align-middle',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        targets: 1,
                        data: 'nama',
                        className: 'text-center align-middle',
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        targets: 2,
                        data: 'tagihan',
                        className: 'text-center align-middle',
                        render: function(data, type, row, meta) {
                            return data[0].nop;
                        }
                    },
                    {
                        targets: 3,
                        data: null,
                        className: 'text-center align-middle',
                        render: function(data, type, row, meta) {
                            let checked = row.is_plotted ? 'checked' : '';
                            return `
                                <input type="checkbox" class="row-check form-checkbox h-5 w-5 text-red-600" ${checked}>
                            `;
                        }
                    }
                ],
            });

            let selectedMasyarakat = new Set();

            $('#checkAll').on('click', function() {
                let isChecked = $(this).is(':checked');
                let ids = [];

                tablePlotting.rows().every(function() {
                    let id = this.data().id;
                    ids.push(id);
                });

                if (isChecked) {
                    ids.forEach(id => selectedMasyarakat.add(id));
                } else {
                    selectedMasyarakat.clear();
                }

                // centang UI saja
                $('#table-plotting .row-check').prop('checked', isChecked);
            });


            $('#table-plotting tbody').on('change', '.row-check', function() {
                let row = tablePlotting.row($(this).closest('tr')).data();
                let isChecked = $(this).is(':checked');

                if (isChecked) {
                    selectedMasyarakat.add(row.id);
                } else {
                    selectedMasyarakat.delete(row.id);
                }
            });

            $('#btnSelesai').off('click').on('click', function () {
                if (!idPemungut) {
                    Swal.fire({ icon:'error', title:'Gagal', text:'Pemungut belum dipilih.' });
                    return;
                }

                if (selectedMasyarakat.size === 0) {
                    Swal.fire({ icon:'warning', title:'Perhatian', text:'Pilih minimal 1 masyarakat.' });
                    return;
                }

                const payload = {
                    masyarakat_ids: Array.from(selectedMasyarakat),
                    pemungut_id: idPemungut,
                    checked: 1, // <-- penting: sesuai kebutuhan server (1 = ter-plot)
                };

                $.ajax({
                    url: "{{ route('desa.managePemungut.toggleAll') }}",
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data berhasil disimpan!'
                        }).then(() => {
                            $('#modal-plotting').addClass('hidden');

                            // Reset data
                            selectedMasyarakat.clear();
                            tablePlotting.ajax.reload();
                        });
                    },
                    error: function(err) {
                        console.error(err);
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menyimpan data!' });
                    }
                });
            });


            // show modal
            $('#modal-plotting').removeClass('hidden');
        });

        // Fungsi umum untuk menutup modal
        function setupModal(modalId) {
            const $modal = $(modalId);

            $modal.find('button[data-action="close"]').on('click', function() {
                try {
                    resetModal(modalId);  // <--- FIX: kirim string
                } catch(e) {
                    console.warn('resetModal skip:', e.message);
                }
                $modal.addClass('hidden');
            });
        }

        // Fungsi reset modal
        function resetModal(modalId) {
            let $modal = $(modalId);

            $modal.find('form')[0]?.reset();
            $modal.find('*').removeClass('is-invalid');
            $modal.find('.custom-file-label').html('Pilih file...');

            $modal.find('select').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).val(null).trigger('change');
                }
            });

            // Reset ID hanya untuk modal pemungut
            if (modalId === '#modal-pemungut') {
                idPemungut = null;
            }
        }

        // Setup kedua modal
        setupModal('#modal-pemungut');
        setupModal('#modal-plotting');

    })
</script>
@endsection