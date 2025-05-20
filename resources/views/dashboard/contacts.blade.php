@extends('layouts.app')

@section('title', 'Liên hệ')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .table-responsive {
    overflow-x: auto;
    min-width: 100%;
}
table.table {
    white-space: nowrap;
}

td, th {
        white-space: normal !important;
        word-break: break-word;
    }
</style>

@section('content')
<div class="card shadow mb-4 mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Bộ lọc tìm kiếm</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('contacts.searchIndex') }}">
            <div class="row">
                <div class="col-md-3">
                    <label for="start_date">Từ ngày</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') ?? $date['firstDay'] }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date">Đến ngày</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') ?? $date['lastDay'] }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date">Sàn Sapo</label>
                    @if (isset($sapos) && $sapos->count())
                        <select id="store_sapo" name="store_sapo" class="form-control">
                            <option value="">-- Chọn sàn --</option>
                            @foreach ($sapos as $sapo)
                                <option value="{{ $sapo->store_sapo }}" {{ request('store_sapo') == $sapo->store_sapo ? 'selected' : '' }}>
                                    {{ $sapo->store_sapo }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <select class="form-control" disabled>
                            <option>Không có dữ liệu sàn</option>
                        </select>
                    @endif
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Tìm kiếm</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách liên hệ</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Mail</th>
                        <th>Số điện thoại</th>
                        <th>Sàn Sapo</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                @if (!empty($contacts) && $contacts->count())
                <tbody>
                @foreach ($contacts as $conn)
                    <tr>
                        <td>{{ $conn->FULLNAME }}</td>
                        <td>{{ $conn->email }}</td>
                        <td>{{ $conn->mobileNumber }}</td>
                        <td>{{ $conn->sapo_store }}</td>
                        <td>
                            @if ($conn->Dotdigital_Sync === 'synced')
                                {{ $conn->Dotdigital_Sync }}
                            @else
                                <button class="btn btn-danger btn-sm text-white">Sync</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
                @else
                    <div class="text-muted">Không có dữ liệu kết nối nào.</div>
                @endif
            </table>
        
        </div>
    </div>
</div>

<!-- <script src="{{ asset('assets/js/campaign.js') }}"></script> -->

@endsection