@include('layouts.header')

@section('title', 'Đăng ký người dùng')

@section('content')
<style>
    .bg-login-image {
        background-image: url('/assets/img/pgi-logo.png');
        background-repeat: no-repeat;
        background-position: center;
        background-size: cover;
    }

    .bg-gradient-primary {
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

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Đăng ký người dùng!</h1>
                                    </div>
                                    <form class="user" id="loginForm" method="POST" action="{{ route('register') }}">
                                        @csrf
                                        @if ($errors->any())
                                        <div class="alert alert-danger text-center small">
                                            {{ $errors->first() }}
                                        </div>
                                        @endif
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" name="name" class="form-control form-control-user" id="exampleLastName"
                                                placeholder="Enter Name">
                                            <div class="text-danger small" id="nameError"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email Address</label>
                                            <input type="email" name="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address...">
                                            <div class="text-danger small" id="emailError"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" name="password" class="form-control form-control-user"
                                                id="exampleInputPassword" placeholder="Password">
                                            <div class="text-danger small" id="passwordError"></div>
                                        </div>
                                        <button id="loginButton" type="submit" class="btn btn-primary btn-user btn-block">
                                            Register
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
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            let email = document.getElementById('exampleInputEmail').value.trim();
            let password = document.getElementById('exampleInputPassword').value.trim();
            let name = document.getElementById('exampleLastName').value.trim();
            let isValid = true;

            document.getElementById('emailError').innerText = '';
            document.getElementById('passwordError').innerText = '';
            document.getElementById('nameError').innerText = '';

            if (!name) {
                document.getElementById('nameError').innerText = 'Vui lòng nhập tên.';
                isValid = false;
            }

            if (!email) {
                document.getElementById('emailError').innerText = 'Vui lòng nhập email.';
                isValid = false;
            }

            if (!password) {
                document.getElementById('passwordError').innerText = 'Vui lòng nhập mật khẩu.';
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginButton');
            const email = document.getElementById('exampleInputEmail').value.trim();
            const password = document.getElementById('exampleInputPassword').value.trim();
            const name = document.getElementById('exampleLastName').value.trim();

            if (!email || !password) {
                e.preventDefault();
                return;
            }

            btn.disabled = true;
            btn.innerHTML = `
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
    `;
        });
    </script>


@include('layouts.footer')