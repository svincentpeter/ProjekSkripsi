@extends('backend.app')
@section('title', 'Audit Log')

@section('content')
<div class="container-fluid pt-4 px-4">
    <h2 class="mb-4"><i class="fas fa-clipboard-list me-2"></i>Audit Log</h2>
    <div class="card shadow mb-3">
        <div class="card-body">
            <form class="row g-2 align-items-end mb-2" method="GET" action="">
                <div class="col-md-3">
                    <label class="form-label">Tipe Data</label>
                    <select name="model_type" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($modelTypes as $modelType)
                        <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>
                            {{ ucfirst($modelType) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Aksi</label>
                    <select name="aksi" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($aksiTypes as $aksi)
                        <option value="{{ $aksi }}" {{ request('aksi') == $aksi ? 'selected' : '' }}>
                            {{ ucfirst($aksi) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-filter"></i> Filter</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>User</th>
                            <th>IP</th>
                            <th>Tipe Data</th>
                            <th>ID Data</th>
                            <th>Aksi</th>
                            <th>Data Sebelum</th>
                            <th>Data Sesudah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $i => $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $i }}</td>
                            <td>{{ $log->created_at }}</td>
                            <td>
                                {{ $log->user->name ?? 'Sistem' }}
                                <span class="d-block text-muted" style="font-size:0.85em">{{ $log->user?->email }}</span>
                            </td>
                            <td>{{ $log->ip }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($log->model_type) }}</span></td>
                            <td>{{ $log->model_id }}</td>
                            <td>
                                @if($log->aksi == 'create')
                                <span class="badge bg-success">CREATE</span>
                                @elseif($log->aksi == 'update')
                                <span class="badge bg-warning text-dark">UPDATE</span>
                                @elseif($log->aksi == 'delete')
                                <span class="badge bg-danger">DELETE</span>
                                @else
                                <span class="badge bg-secondary">{{ strtoupper($log->aksi) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($log->old_data)
                                <pre class="mb-0" style="max-width:260px; max-height:120px; overflow:auto; font-size:0.93em;">
{{ json_encode($log->old_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}
                                </pre>
                                @endif
                            </td>
                            <td>
                                @if($log->new_data)
                                <pre class="mb-0" style="max-width:260px; max-height:120px; overflow:auto; font-size:0.93em;">
{{ json_encode($log->new_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}
                                </pre>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada data audit log.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $logs->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
