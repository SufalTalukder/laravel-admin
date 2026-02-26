@extends('templates.before-login.layout')

@section('title', 'Admin Register')

@section('content')
    <div class="container-xxl">
        <div class="row vh-100 d-flex justify-content-center">
            <div class="col-12 align-self-center">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mx-auto">
                            <div class="card">
                                <div class="card-body p-0 bg-black auth-header-box rounded-top">
                                    <div class="text-center p-3">
                                        <a href="index.html" class="logo logo-admin">
                                            <img src="{{ asset('assets/images/logo-sm.png') }}" height="50"
                                                alt="logo" class="auth-logo">
                                        </a>
                                        <h4 class="mt-3 mb-1 fw-semibold text-white fs-18">Create an account</h4>
                                        <p class="text-muted fw-medium mb-0">Enter your detail to Create your account today.
                                        </p>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <form class="my-4" action="https://mannatthemes.com/rizz/default/index.html">
                                        <div class="form-group mb-2">
                                            <label class="form-label" for="useremail">Email</label>
                                            <input type="email" class="form-control" id="useremail" name="user email"
                                                placeholder="Enter email">
                                        </div>

                                        <div class="form-group mb-2">
                                            <label class="form-label" for="mobileNo">Mobile Number</label>
                                            <input type="text" class="form-control" id="mobileNo" name="mobile number"
                                                placeholder="Enter Mobile Number">
                                        </div>

                                        <div class="form-group mb-2">
                                            <label class="form-label" for="userpassword">Password</label>
                                            <input type="password" class="form-control" name="password" id="userpassword"
                                                placeholder="Enter password">
                                        </div>

                                        <div class="form-group mb-2">
                                            <label class="form-label" for="Confirmpassword">ConfirmPassword</label>
                                            <input type="password" class="form-control" name="password" id="Confirmpassword"
                                                placeholder="Enter Confirm password">
                                        </div>

                                        <div class="form-group row mt-3">
                                            <div class="col-12">
                                                <div class="form-check form-switch form-switch-success">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="customSwitchSuccess">
                                                    <label class="form-check-label" for="customSwitchSuccess">By registering
                                                        you agree to the Rizz <a href="#" class="text-primary">Terms
                                                            of Use</a></label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-0 row">
                                            <div class="col-12">
                                                <div class="d-grid mt-3">
                                                    <button class="btn btn-primary" type="button">Register <i
                                                            class="fas fa-sign-in-alt ms-1"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="text-center">
                                        <p class="text-muted">Already have an account ? <a href="{{ route('adminLogin') }}"
                                                class="text-primary ms-2">Log in</a></p>
                                    </div>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-body-->
            </div><!--end col-->
        </div><!--end row-->
    </div>

@section('scripts')
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            const usernameInput = document.getElementById('username').value.trim();
            const passwordInput = document.getElementById('userpassword').value.trim();
            const loginButton = document.getElementById('clickLoginButton');

            loginButton.addEventListener('click', function(e) {
                e.preventDefault();

                if (!usernameInput || !passwordInput) {
                    showToast("Username & Password is required!", "error");
                    return;
                }
                if (username === 'admin' && password === 'admin') {
                    window.location.href = "{{ url('admin/dashboard') }}";
                } else {
                    showToast('Invalid username or password. Please try again.');
                }
            });
        });
    </script>
@endsection

@endsection
