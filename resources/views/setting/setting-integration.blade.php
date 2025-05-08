@extends('layouts.app')

@section('title', 'Trang chủ')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .table-responsive {
    overflow-x: auto;
    min-width: 100%;
}
table.table {
    white-space: nowrap; /* Ngăn nội dung bị xuống dòng */
}
</style>


@section('content')
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách liên kết</h6>
        <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addNewSettingModal">Thêm mới liên kết</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Sapo store</th>
                        <th>Tài khoản Sapo</th>
                        <th>Tài khoản Dotdigital</th>
                        <th>Người tạo</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                @if (!empty($connections) && $connections->count())
                <tbody>
                @foreach ($connections as $conn)
                    <tr>
                        <td>
                            <a href="javascript:void(0)" 
                            class="open-edit-modal" 
                            data-id="{{ $conn->id }}">
                            {{ $conn->store_sapo }}
                            </a>
                        </td>
                        <td>{{ $conn->username_sapo }}</td>
                        <td>{{ $conn->username_dotdigital }}</td>
                        <td>{{ $conn->creator_name ?? 'Không rõ' }}</td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input switch-status" 
                                    id="switchStatus{{ $conn->id }}" 
                                    data-id="{{ $conn->id }}"
                                    {{ $conn->active_status ? 'checked' : '' }}>
                                <label class="custom-control-label" for="switchStatus{{ $conn->id }}">Kích hoạt</label>
                            </div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($conn->created_at)->format('d/m/Y H:i') }}</td>
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

<!-- Add New Setting Modal-->
<div class="modal fade" id="addNewSettingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="position: relative;">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Thêm mới liên kết</h5>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="activateSwitch">
                    <label class="custom-control-label" for="activateSwitch">Kích hoạt</label>
                </div>
            </div>
        </div>
        <form id="addConnectionForm">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="username1">Tài khoản Sapo<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username1" name="username1" placeholder="Nhập tài khoản">
                        </div>
                        <div class="form-group">
                            <label for="password1">Mật khẩu Sapo<span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password1" name="password1" placeholder="Nhập mật khẩu">
                        </div>
                        <div class="form-group">
                            <label for="store1">Tên cửa hàng (subdomain Sapo)<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="store1" name="store1" placeholder="VD: jblonlinestore">
                        </div>
                        <div class="form-group text-right">
                            <button type="button" id="btnTestConnection" class="btn btn-outline-primary btn-sm">Kiểm tra kết nối</button>
                        </div>
                    </div>
                    <!-- Form bên phải -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="username2">Tài khoản Dotdigital<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username2" name="username2" placeholder="Nhập tài khoản">
                        </div>
                        <div class="form-group">
                            <label for="password2">Mật khẩu Dotdigital<span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password2" name="password2" placeholder="Nhập mật khẩu">
                        </div>
                        <div class="form-group text-right">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnTestDotdigital">Kiểm tra kết nối</button>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="activeStatus" name="activeStatus">
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Hủy</button>
                <button id="btnSave" class="btn btn-primary" type="submit">
                    <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true" style="display: none;" id="btnSaveSpinner"></span>
                    Lưu
                </button>

            </div>
        </form>
    </div>
</div>
</div>

<!-- Edit Setting Modal -->
<div class="modal fade" id="editSettingModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="position: relative;">
      <div id="edit-modal-loading-overlay" style="display: none; position: absolute; top: 0; left: 0; z-index: 1051; width: 100%; height: 100%; background: rgba(255,255,255,0.6); display: flex; justify-content: center; align-items: center;">
        <div class="spinner-border text-primary" role="status"><span class="sr-only">Đang tải...</span></div>
      </div>

      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Chỉnh sửa liên kết</h5>
        <div class="form-group">
          <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="editActivateSwitch1">
            <label class="custom-control-label" for="editActivateSwitch1">Kích hoạt</label>
          </div>
        </div>
      </div>

      <form id="editConnectionForm">
        <div class="modal-body">
          <div class="row">
            <!-- Form bên trái -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="editUsername1">Tài khoản Sapo<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="editUsername3" name="username3">
              </div>
              <div class="form-group">
                <label for="editPassword1">Mật khẩu Sapo<span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="editPassword3" name="password3">
              </div>
              <div class="form-group">
                <label for="editStore1">Tên cửa hàng<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="editStore3" name="store3">
              </div>
            </div>
            <!-- Form bên phải -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="editUsername2">Tài khoản Dotdigital<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="editUsername4" name="username4">
              </div>
              <div class="form-group">
                <label for="editPassword2">Mật khẩu Dotdigital<span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="editPassword4" name="password4">
              </div>
            </div>
          </div>
        </div>
        <input type="hidden" id="editConnectionId1" name="connectionId1">
        <input type="hidden" id="editActiveStatus1" name="activeStatus1">
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Hủy</button>
          <button id="btnSave1" class="btn btn-primary" type="submit">
                    <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true" style="display: none;" id="btnSaveSpinner1"></span>
                    Cập nhật
            </button>
        </div>
      </form>
    </div>
  </div>
</div>


<script src="{{ asset('assets/js/connection.js') }}"></script>


@endsection