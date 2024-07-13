 <div class="sidebar pe-4 pb-3">
     <nav class="navbar bg-light navbar-light">
         <a href="index.html" class="navbar-brand mx-4 mb-3">
             <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>KOPERASI</h3>
         </a>
         <div class="d-flex align-items-center ms-4 mb-4">
             <div class="position-relative">
                 <img class="rounded-circle" src="{{ asset('assets/backend/img/' . auth()->user()->image) }}" alt="" style="width: 40px; height: 40px;">
                 <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
             </div>
             <div class="ms-3">
                 <h6 class="mb-0">{{ auth()->user()->name}}</h6>

                 <span>{{ auth()->user()->roles->pluck('name')->implode(', ') }}</span>

             </div>
         </div>
         <div class="navbar-nav w-100">
             <a href="{{route('home')}}" class="nav-item nav-link {{ (request()->is('home*')) ? 'active' : '' }}"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
             <a href="{{route('user')}}" class="nav-item nav-link {{ (request()->is('user*')) ? 'active' : '' }}"><i class="fa fa-user me-2"></i>User</a>
             <a href="{{route('nasabah')}}" class="nav-item nav-link {{ (request()->is('nasabah*')) ? 'active' : '' }}"><i class="fa fa-user-friends me-2"></i>Nasabah</a>
             <a href="{{route('simpanan')}}" class="nav-item nav-link {{ (request()->is('simpanan*')) ? 'active' : '' }}"><i class="far fa-money-bill-alt me-2"></i>Simpanan</a>
             <a href="{{route('pinjaman')}}" class="nav-item nav-link {{ (request()->is('pinjaman*')) ? 'active' : '' }}"><i class=" far fa-money-bill-alt me-2"></i>Pinjaman</a>
             <a href="{{route('penarikan')}}" class="nav-item nav-link {{ (request()->is('penarikan*')) ? 'active' : '' }}"><i class=" far fa-money-bill-alt me-2"></i>Penarikan</a>
             <!-- <a href="{{route('angsuran')}}" class="nav-item nav-link {{ (request()->is('angsuran*')) ? 'active' : '' }}"><i class=" far fa-money-bill-alt me-2"></i>Angsuran</a> -->
             <div class="nav-item dropdown">
                 <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="far fa-file-alt me-2"></i>Laporan</a>
                 <div class="dropdown-menu bg-transparent border-0">
                     <a href="{{route('laporanSimpanan')}}" class="dropdown-item">Laporan Simpanan</a>
                     <a href="{{route('laporanPinjaman')}}" class="dropdown-item">Laporan Pinjaman</a>
                     <a href="{{route('laporanPenarikan')}}" class="dropdown-item">Laporan Penarikan</a>
                 </div>
             </div>
             <a href="{{ URL('show-roles') }}" class="nav-item nav-link {{ (request()->is('show-roles*')) ? 'active' : '' }}"><i class="fa fa-user-shield me-2"></i>Role</a>
         </div>
     </nav>
 </div>