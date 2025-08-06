@extends('layouts.app')

@section('title', 'Chiến dịch')
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
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách chiến dịch</h6>
        <a href="#" class="btn btn-primary btn-sm" id="openSyncCampaign">Đồng bộ chiến dịch</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tên chiến dịch</th>
                        <th>link Sapo</th>
                    </tr>
                </thead>
                @if (!empty($connections) && $connections->count())
                <tbody>
                @foreach ($connections as $conn)
                    <tr>
                        <td>
                            <a href="#" 
                            class="open-edit-modal" 
                            data-id="{{ $conn->id }}">
                            {{ $conn->campaigns_name }}
                            </a>
                        </td>
                        <td>{{ $conn->link_sapo }}</td>
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

<!-- Edit Setting Modal -->
<div class="modal fade" id="editCampaignModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="position: relative;">
      <div id="edit-modal-loading-overlay" style="display: none; position: absolute; top: 0; left: 0; z-index: 1051; width: 100%; height: 100%; background: rgba(255,255,255,0.6); display: flex; justify-content: center; align-items: center;">
        <div class="spinner-border text-primary" role="status"><span class="sr-only">Đang tải...</span></div>
      </div>

      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Chỉnh sửa liên kết</h5>
      </div>

      <form id="editConnectionForm">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="campaignName">Tên chiến dịch<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="campaignName" name="campaignName">
              </div>
              <div class="form-group" id="sapo-links">
                <label>Link Sapo <span class="text-danger">*</span></label>
                <div class="input-group mb-2">
                  <input type="text" class="form-control" name="link_sapo[]">
                  <div class="input-group-append">
                    <button class="btn btn-danger" type="button" onclick="removeLink(this)">❌</button>
                  </div>
                </div>
              </div>
              <button type="button" class="btn btn-secondary" onclick="addSapoLink()">Thêm link</button>
            </div>
          </div>
        </div>
        <input type="hidden" id="editConnectionId1" name="connectionId1">
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Hủy</button>
          <button id="btnSave1" class="btn btn-primary" type="submit">
                    <span href="javascript:void(0);" class="btn btn-primary btn-sm btn-sync-campaign spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true" style="display: none;" id="btnSaveSpinner1"></span>
                    Cập nhật
            </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Sync Campaign -->
<div class="modal fade" id="syncCampaign" tabindex="-1" role="dialog" aria-labelledby="syncCampaign">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="position: relative;">
    <div id="sync-campaign" style="display: none; position: absolute; top: 0; left: 0; z-index: 1051; width: 100%; height: 100%; background: rgba(255,255,255,0.6); display: flex; justify-content: center; align-items: center;">
        <div class="spinner-border text-primary" role="status"><span class="sr-only">Đang tải...</span></div>
      </div>
      <div class="modal-header">
        <h5 class="modal-title" id="syncCampaign">Đồng bộ campaign</h5>
      </div>
      <form id="syncCampaignForm">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
            <div class="form-group">
              <label>Tên liên kết<span class="text-danger">*</span></label>
              <select id="campaignNameSelect" class="form-control" required></select>
            </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Hủy</button>
          <button id="btnSave1" class="btn btn-primary" type="submit">
            <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true" style="display: none;" id="btnSyncSpinner1"></span>
            Đồng bộ
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="{{ asset('assets/js/campaign.js') }}"></script>
<script>
  function addSapoLink() {
    const container = document.getElementById('sapo-links');

    const div = document.createElement('div');
    div.className = 'input-group mb-2';

    div.innerHTML = `
      <input type="text" class="form-control" name="link_sapo[]">
      <div class="input-group-append">
        <button class="btn btn-danger" type="button" onclick="removeLink(this)">❌</button>
      </div>
    `;

    container.appendChild(div);
  }

  function removeLink(button) {
    button.closest('.input-group').remove();
  }
</script>

@endsection