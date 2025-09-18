
<aside class="relative bg-sidebar h-screen w-64 hidden sm:block shadow-xl">
    <div class="p-6">
        @if (Auth::user()->role === 'admin_kecamatan')
        <a href="index.html" class="text-white text-3xl font-semibold uppercase hover:text-gray-300">Admin Kecamatan</a>
        @else
        <a href="#" class="text-white text-3xl font-semibold uppercase hover:text-gray-300">Admin {{ Auth::user()?->adminDesa?->desa?->name }}</a>
        @endif
        <button class="w-full bg-white cta-btn font-semibold py-2 mt-5 rounded-br-lg rounded-bl-lg rounded-tr-lg shadow-lg hover:shadow-xl hover:bg-gray-300 flex items-center justify-center">
            <i class="fas fa-plus mr-3"></i> New Report
        </button>
    </div>
    <nav class="text-white text-base font-semibold pt-3">
        <a href="{{ Route('dashboard') }}" class="flex items-center text-white py-4 pl-6 nav-item">
            <i class="fas fa-tachometer-alt mr-3"></i>
            Dashboard
        </a>
        @if (Auth::user()->role === 'admin_desa')
        <a href="{{ Route('desa.managePemungut.index') }}" class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
            <i class="fas fa-table mr-3"></i>
            Manage Akun Pemungut
        </a>
        <a href="{{ Route('desa.tagihan.index') }}" class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
            <i class="fas fa-align-left mr-3"></i>
            Tagihan
        </a>
        @else
        <a href="{{ Route('kecamatan.tagihan.index') }}" class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
            <i class="fas fa-align-left mr-3"></i>
            Tagihan
        </a>
        @endif
    </nav>
    <a href="{{ Route('auth.logout') }}" class="absolute w-full upgrade-btn bottom-0 active-nav-link text-white flex items-center justify-center py-4">
        <i class="fas fa-arrow-circle-up mr-3"></i>
        Log Out
    </a>
</aside>