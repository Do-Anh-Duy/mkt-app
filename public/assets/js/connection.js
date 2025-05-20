$(document).ready(function() {
    $('#btnTestConnection').on('click', function() {
        var $btn = $(this);
        var username = $('#username1').val().trim();
        var password = $('#password1').val().trim();
        var store = $('#store1').val().trim();

        if (!username || !password || !store) {
            alert('Vui lòng nhập đầy đủ thông tin.');
            return;
        }

        $btn.prop('disabled', true).text('Đang kiểm tra...');

        var apiUrl = `/sapo/check-connection`;

        $.ajax({
            url: apiUrl,
            method: 'POST',
            data: {
                username: username,
                password: password,
                store: store
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success) {
                    $btn.removeClass('btn-outline-primary').addClass('btn-success')
                        .text('✅ Kết nối thành công');
                } else {
                    let countdown = 3;
                    $btn.removeClass('btn-outline-primary').addClass('btn-danger')
                        .text(`❌ Kết nối thất bại (${countdown}s)`);
    
                    let interval = setInterval(function () {
                        countdown--;
                        if (countdown > 0) {
                            $btn.text(`❌ Kết nối thất bại (${countdown}s)`);
                        } else {
                            clearInterval(interval);
                            $btn.prop('disabled', false)
                                .removeClass('btn-success btn-danger')
                                .addClass('btn-outline-primary')
                                .text('Kiểm tra kết nối');
                        }
                    }, 1000);
                }
            },
            error: function() {
                let countdown = 3;
                $btn.removeClass('btn-outline-primary').addClass('btn-danger')
                    .text(`❌ Kết nối thất bại (${countdown}s)`);

                let interval = setInterval(function () {
                    countdown--;
                    if (countdown > 0) {
                        $btn.text(`❌ Kết nối thất bại (${countdown}s)`);
                    } else {
                        clearInterval(interval);
                        $btn.prop('disabled', false)
                            .removeClass('btn-success btn-danger')
                            .addClass('btn-outline-primary')
                            .text('Kiểm tra kết nối');
                    }
                }, 1000);
            }
        });
    });
});

$(document).ready(function () {
    $('#btnTestDotdigital').on('click', function () {
        var $btn = $(this);
        var username = $('#username2').val().trim();
        var password = $('#password2').val().trim();

        if (!username || !password) {
            alert('Vui lòng nhập đầy đủ thông tin Dotdigital.');
            return;
        }

        $btn.prop('disabled', true).text('Đang kiểm tra...');

        $.ajax({
            url: '/dotdigital/check-connection',
            method: 'POST',
            data: {
                username: username,
                password: password
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success) {
                    $btn.removeClass('btn-outline-primary').addClass('btn-success')
                        .text('✅ Kết nối thành công');
                } else {
                    let countdown = 3;
                    $btn.removeClass('btn-outline-primary').addClass('btn-danger')
                        .text(`❌ Kết nối thất bại (${countdown}s)`);
    
                    let interval = setInterval(function () {
                        countdown--;
                        if (countdown > 0) {
                            $btn.text(`❌ Kết nối thất bại (${countdown}s)`);
                        } else {
                            clearInterval(interval);
                            $btn.prop('disabled', false)
                                .removeClass('btn-success btn-danger')
                                .addClass('btn-outline-primary')
                                .text('Kiểm tra kết nối');
                        }
                    }, 1000);
                }
            },
            error: function () {
                let countdown = 3;
                $btn.removeClass('btn-outline-primary').addClass('btn-danger')
                    .text(`❌ Kết nối thất bại (${countdown}s)`);

                let interval = setInterval(function () {
                    countdown--;
                    if (countdown > 0) {
                        $btn.text(`❌ Kết nối thất bại (${countdown}s)`);
                    } else {
                        clearInterval(interval);
                        $btn.prop('disabled', false)
                            .removeClass('btn-success btn-danger')
                            .addClass('btn-outline-primary')
                            .text('Kiểm tra kết nối');
                    }
                }, 1000);
            }
        });
    });
});

$(document).ready(function () {
    // Khi người dùng nhấn nút Lưu
    $('#addConnectionForm').submit(function (e) {
        $('#btnSaveSpinner').show();
        $('#btnSave').prop('disabled', true);
        e.preventDefault(); // Ngăn chặn form gửi tự động

        // Lấy dữ liệu từ các trường
        var nameconverted = $('#nameconverted').val();
        var gidconverted = $('#gidconverted').val();
        var username1 = $('#username1').val();
        var password1 = $('#password1').val();
        var store1 = $('#store1').val();
        var username2 = $('#username2').val();
        var password2 = $('#password2').val();
        var activeStatus = $('#activateSwitch').prop('checked') ? 1 : 0; // Kiểm tra trạng thái kích hoạt

        // Gửi dữ liệu đến backend
        $.ajax({
            url: '/save-connection', // Đường dẫn đến controller Laravel
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'), // Thêm CSRF token
                username1: username1,
                password1: password1,
                store1: store1,
                username2: username2,
                password2: password2,
                activeStatus: activeStatus,
                nameconverted: nameconverted,
                gidconverted: gidconverted,
            },
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Đã có lỗi xảy ra!1111');
                    $('#btnSaveSpinner').hide();
                    $('#btnSave').prop('disabled', false);
                }
            },
            error: function (xhr, status, error) {
                console.log('Lỗi chi tiết:', xhr.responseText); // Xem lỗi trả về từ server
                console.log('Status:', status);
                console.log('Error:', error);
                $('#btnSaveSpinner').hide();
                $('#btnSave').prop('disabled', false);
            }
        });
    });
});

$(document).ready(function () {
    $('.switch-status').change(function () {
        let id = $(this).data('id');
        let status = $(this).is(':checked') ? 1 : 0;
        $.ajax({
            url: '/update-status',
            method: 'POST',
            data: {
                id: id,
                status: status,
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (res) {
                if (res.success) {
                    console.log('Cập nhật trạng thái thành công');
                } else {
                    alert('Cập nhật thất bại');
                }
            },
            error: function () {
                alert('Có lỗi xảy ra khi cập nhật');
            }
        });
    });
});

$(document).on('click', '.open-edit-modal', function () {
    const id = $(this).data('id');
    
    // Hiện modal trước nhưng ẩn nội dung, hiện overlay
    $('#editSettingModal').modal('show');
    $('#editSettingModal .modal-body, #editSettingModal .modal-footer').hide();
    $('#edit-modal-loading-overlay').show();

    $.get('/connection/' + id, function (res) {
        if (res.success) {
            const d = res.connection[0];
            $('#nameconverted1').val(d.name_converted);
            $('#gidconverted2').val(d.gid_converted);
            $('#editUsername3').val(d.username_sapo);
            $('#editPassword3').val(d.password_sapo);
            $('#editStore3').val(d.store_sapo);
            $('#editUsername4').val(d.username_dotdigital);
            $('#editPassword4').val(d.password_dotdigital);
            $('#editActivateSwitch1').prop('checked', d.active_status === 1);
            $('#editActiveStatus1').val(d.active_status);
            $('#editConnectionId1').val(d.id);

            // Show nội dung, ẩn overlay
            $('#editSettingModal .modal-body, #editSettingModal .modal-footer').fadeIn();
            $('#edit-modal-loading-overlay').hide();
        } else {
            alert('Không lấy được dữ liệu');
            $('#editSettingModal').modal('hide');
        }
    });
});

$(document).ready(function () {
    // Khi người dùng nhấn nút Lưu
    $('#editConnectionForm').submit(function (e) {
        e.preventDefault();
        $('#btnSaveSpinner1').show();
        $('#btnSave1').prop('disabled', true);
        // Lấy dữ liệu từ các trường
        var connectionId1 = $('#editConnectionId1').val();
        var username3 = $('#editUsername3').val();
        var password3 = $('#editPassword3').val();
        var store3 = $('#editStore3').val();
        var username4 = $('#editUsername4').val();
        var password4 = $('#editPassword4').val();
        var activeStatus1 = $('#editActivateSwitch1').prop('checked') ? 1 : 0; // Kiểm tra trạng thái kích hoạt
        var nameconverted1 = $('#nameconverted1').val();
        var gidconverted2 = $('#gidconverted2').val();
        
        // Gửi dữ liệu đến backend
        $.ajax({
            url: '/connection/update', // Đường dẫn đến controller Laravel
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'), // Thêm CSRF token
                username3: username3,
                password3: password3,
                store3: store3,
                username4: username4,
                password4: password4,
                activeStatus1: activeStatus1,
                connectionId1: connectionId1,
                nameconverted1: nameconverted1,
                gidconverted2: gidconverted2,
            },
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Đã có lỗi xảy ra!');
                    $('#btnSaveSpinner').hide();
                    $('#btnSave').prop('disabled', false);
                }
            },
            error: function () {
                alert('Đã có lỗi xảy ra!');
                $('#btnSaveSpinner').hide();
                $('#btnSave').prop('disabled', false);
            }
        });
    });
});