@include('layouts.header')

@section('title', 'Đăng nhập')

@section('content')
<style>
.bg-login-image {
  background-image: url('/assets/img/pgi-logo.png');
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
}

.bg-gradient-primary{
    background-image: url('/assets/img/860.jpg');
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
}

.bg-white {
    background-color: transparent !important;
    color: #fff !important;
}
</style>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-8 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome !</h1>
                                    </div>
                                    <form class="user" id="loginForm" method="POST" action="{{ route('login') }}">
                                    @csrf
                                    @if ($errors->any())
                                        <div class="alert alert-danger text-center small">
                                            {{ $errors->first() }}
                                        </div>
                                    @endif
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address...">
                                                <div class="text-danger small" id="emailError"></div>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control form-control-user"
                                                id="exampleInputPassword" placeholder="Password">
                                                <div class="text-danger small" id="passwordError"></div>
                                        </div>
                                        <button id="loginButton" type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">Forgot Password?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script>
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        let email = document.getElementById('exampleInputEmail').value.trim();
        let password = document.getElementById('exampleInputPassword').value.trim();
        let isValid = true;

        // Xóa lỗi cũ
        document.getElementById('emailError').innerText = '';
        document.getElementById('passwordError').innerText = '';

        // Kiểm tra email
        if (!email) {
            document.getElementById('emailError').innerText = 'Vui lòng nhập email.';
            isValid = false;
        }

        // Kiểm tra mật khẩu
        if (!password) {
            document.getElementById('passwordError').innerText = 'Vui lòng nhập mật khẩu.';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault(); // Ngăn submit nếu có lỗi
        }
    });

    document.getElementById('loginForm').addEventListener('submit', function (e) {
    const btn = document.getElementById('loginButton');
    const email = document.getElementById('exampleInputEmail').value.trim();
    const password = document.getElementById('exampleInputPassword').value.trim();

    // Nếu thiếu email hoặc password thì ngăn submit
    if (!email || !password) {
        e.preventDefault(); // Ngăn form submit
        return;
    }

    // Nếu hợp lệ, disable nút và hiển thị spinner
    btn.disabled = true;
    btn.innerHTML = `
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
    `;
});
</script>

@include('layouts.footer')