@include('templates.before-login.header', ['title' => 'Admin Login'])
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
                                    <h4 class="mt-3 mb-1 fw-semibold text-white fs-18">Let's Get Started Rizz</h4>
                                    <p class="text-muted fw-medium mb-0">Sign in to continue to Rizz.</p>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <form class="my-4" action="https://mannatthemes.com/rizz/default/index.html">
                                    <div class="form-group mb-2">
                                        <label class="form-label" for="username">Username</label>
                                        <input type="text" class="form-control" id="username" name="username"
                                            placeholder="Enter username">
                                    </div><!--end form-group-->

                                    <div class="form-group">
                                        <label class="form-label" for="userpassword">Password</label>
                                        <input type="password" class="form-control" name="password" id="userpassword"
                                            placeholder="Enter password">
                                    </div><!--end form-group-->

                                    <div class="form-group row mt-3">
                                        <div class="col-sm-6">
                                            <div class="form-check form-switch form-switch-success">
                                                <input class="form-check-input" type="checkbox"
                                                    id="customSwitchSuccess">
                                                <label class="form-check-label" for="customSwitchSuccess">Remember
                                                    me</label>
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-sm-6 text-end">
                                            <a href="auth-recover-pw.html" class="text-muted font-13"><i
                                                    class="dripicons-lock"></i> Forgot password?</a>
                                        </div><!--end col-->
                                    </div><!--end form-group-->

                                    <div class="form-group mb-0 row">
                                        <div class="col-12">
                                            <div class="d-grid mt-3">
                                                <button class="btn btn-primary" type="button" id="clickLoginButton">Log In <i
                                                        class="fas fa-sign-in-alt ms-1"></i></button>
                                            </div>
                                        </div><!--end col-->
                                    </div> <!--end form-group-->
                                </form><!--end form-->
                                <div class="text-center  mb-2">
                                    <p class="text-muted">Don't have an account ? <a href="{{ url('admin/register') }}"
                                            class="text-primary ms-2">Free Resister</a></p>
                                </div>
                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end card-body-->
        </div><!--end col-->
    </div><!--end row-->
</div>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        const loginButton = document.getElementById("clickLoginButton");
        loginButton.addEventListener("click", function() {
            // Simulate a successful login by redirecting to the dashboard
            window.location.href = "{{ url('admin/dashboard') }}";
        });
    });
</script>
