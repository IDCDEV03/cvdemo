@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">การแจ้งเตือนทั้งหมด</h4>
                </div>
            </div>

 <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">           

            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-outline-primary">
                    อ่านทั้งหมด
                </button>
            </form>
        </div>

        <div class="card-body p-0">
            @forelse($notifications as $noti)
                <a href="{{ route('notifications.read', $noti->id) }}"
                   class="list-group-item list-group-item-action {{ $noti->read_at ? '' : 'fw-bold' }}">
                    <div>{{ $noti->data['title'] ?? 'แจ้งเตือน' }}</div>
                    <small class="text-muted">
                        {{ $noti->created_at->format('d/m/Y H:i') }}
                    </small>
                </a>
            @empty
                <div class="text-center text-muted p-4">
                    ไม่มีการแจ้งเตือน
                </div>
            @endforelse
        </div>

        <div class="card-footer">
            {{ $notifications->links() }}
        </div>
    </div>


        </div>
    </div>
@endsection
