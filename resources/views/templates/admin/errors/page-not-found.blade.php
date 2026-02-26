@extends('templates.before-login.layout')

@section('title', 'Page Not Found')

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
                                        <a href="javascript:void(0);" class="logo logo-admin" id="returnBack">
                                            <img src="{{ asset('assets/images/logo-sm.png') }}" height="50"
                                                alt="logo" class="auth-logo">
                                        </a>
                                        <h4 class="mt-3 mb-1 fw-semibold text-white fs-18">This page is under construction
                                        </h4>
                                        <p class="text-muted fw-medium mb-0">Back to dashboard of Rizz</p>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="ex-page-content text-center">
                                        <img src="{{ asset('assets/images/extra/error.svg') }}" alt="0"
                                            class="" height="170">
                                        <h1 class="my-2">404!</h1>
                                        <h5 class="fs-16 text-muted mb-3">Somthing went wrong</h5>
                                    </div>
                                    <button type="button" class="btn btn-primary w-100" onclick=returnBack();
                                        id="returnBack">Return Back
                                        <i class="fas fa-redo ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script type="text/javascript">
    function returnBack() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = "{{ url('/admin/dashboard') }}";
        }
    }
</script>
