@if($logs->isEmpty())
    <div class="text-center text-muted py-4">Tidak ada riwayat perubahan.</div>
@else
    <ul class="list-group">
        @foreach($logs as $log)
        <li class="list-group-item">
            <div>
                <strong>{{ ucfirst($log->aksi) }}</strong>
                oleh {{ \App\Models\User::find($log->user_id)?->name ?? 'Sistem' }},
                <small>{{ $log->created_at }}</small>
            </div>
            <div>
                <pre>{{ json_encode(['old'=>$log->old_data, 'new'=>$log->new_data], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </li>
        @endforeach
    </ul>
@endif
