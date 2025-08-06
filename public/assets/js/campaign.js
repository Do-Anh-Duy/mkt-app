$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

        $('#syncCampaignForm').submit(function (e) {
        e.preventDefault();
        $('#btnSyncSpinner1').show();
        let campaignId = $('#campaignNameSelect').val();
        $.ajax({
            url: '/sync-campaign',
            type: 'POST',
            data: {
                campaignId: campaignId 
            },
            beforeSend: function () {
                $('.btn-sync-campaign').prop('disabled', true).text('Đang đồng bộ...');
            },
            success: function (res) {
                if (res.success) {
                    location.reload();
                    $('#btnSyncSpinner1').hide();
                } else {
                    $('#btnSyncSpinner1').hide();
                    $('.btn-sync-campaign').prop('disabled', false).text('Đồng bộ chiến dịch');
                }
            },
            error: function (xhr) {
                $('#btnSyncSpinner1').hide();
                $('.btn-sync-campaign').prop('disabled', false).text('Đồng bộ chiến dịch');
            }
        });
    });
});

$(document).ready(function () {
    $('.open-edit-modal').click(function (e) {
        e.preventDefault();

        let campaignId = $(this).data('id');
        
        $('#edit-modal-loading-overlay').show();
        $('#editCampaignModal').modal('show');
        $.ajax({
            url: '/api/campaign/' + campaignId,  
            method: 'GET',
            success: function (response) {
                
                $('#edit-modal-loading-overlay').hide();
                
                if (response.success) {
                $('#campaignName').val(response.data.campaigns_name);
                $('#editConnectionId1').val(response.data.id);

                // ������ Clear các input link cũ (trừ label)
                $('#sapo-links .input-group').remove();

                // Nếu là JSON string thì cần parse trước
                let links = response.data.link_sapo;
                if (typeof links === 'string') {
                    try {
                        links = JSON.parse(links);
                    } catch (e) {
                        links = [];
                    }
                }

                // Nếu mảng rỗng thì thêm 1 input trống
                if (!links || links.length === 0) {
                    $('#sapo-links').append(`
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="link_sapo[]">
                            <div class="input-group-append">
                                <button class="btn btn-danger" type="button" onclick="removeLink(this)">❌</button>
                            </div>
                        </div>
                    `);
                } else {
                    // ������ Thêm lại input cho từng link
                    links.forEach(function (link) {
                        $('#sapo-links').append(`
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="link_sapo[]" value="${link}">
                                <div class="input-group-append">
                                    <button class="btn btn-danger" type="button" onclick="removeLink(this)">❌</button>
                                </div>
                            </div>
                        `);
                    });
                }

            } else {
                alert('Không tìm thấy chiến dịch');
            }      
            },
            error: function () {
                $('#edit-modal-loading-overlay').hide();
                alert('Có lỗi xảy ra khi lấy dữ liệu');
            }
        });
    });
});


$(document).ready(function () {
    $('#openSyncCampaign').click(function (e) {
        e.preventDefault(); 
        $('#sync-campaign').show(); 
        $('#syncCampaign').modal('show');
        $.ajax({
            url: '/api/storeName',  
            method: 'GET',
            success: function (response) {
                $('#sync-campaign').hide(); 
                if (response.success) {
                    const stores = response.data.stores;
                    $.each(stores, function(index, store) {
                        $('#campaignNameSelect').append(`<option value="${store.id}">${store.store_sapo}</option>`);
                    });
                } else {
                    alert('Không tìm thấy chiến dịch');
                }
            },
            error: function () {
                $('#sync-campaign').hide();
                alert('Có lỗi xảy ra khi lấy dữ liệu');
            }
        });
    });
});

$(document).ready(function () {
    $('#editConnectionForm').on('submit', function (e) {
        e.preventDefault();
        $('#btnSaveSpinner1').show();
        let id = $('#editConnectionId1').val();
        let name = $('#campaignName').val();


        let links = $("input[name='link_sapo[]']")
        .map(function () {
            return $(this).val().trim();
        })
        .get(); // => array các link



        $.ajax({
            url: '/api/campaign',
            method: 'POST',
            data: {
                id: id,
                campaigns_name: name,
                link_sapo: links,
                _token: $('meta[name="csrf-token"]').attr('content') 
            },
            success: function (res) {
                $('#btnSaveSpinner1').hide();
                if (res.success) {
                    location.reload();
                } else {
                    $('#btnSaveSpinner1').hide();
                    alert('Cập nhật thất bại');
                }
            },
            error: function () {
                $('#btnSaveSpinner1').hide();
                alert('Có lỗi xảy ra: ' + xhr.responseText);
            }
        });
    });
});

