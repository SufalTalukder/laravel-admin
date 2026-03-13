@extends('templates.after-login.layout')

@section('title', 'Auth Users')

@section('content')
    {{-- BODY LAYOUT --}}
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-xxl">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">Manage Auth User</h4>
                                    </div>
                                    <div class="col-auto">
                                        <form class="row g-2">
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-danger delete-selected" disabled>
                                                    Delete Selected
                                                </button>
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-primary" id="addNewBtn"
                                                    data-bs-toggle="modal" data-bs-target="#addUpdateRecord">
                                                    <i class="fa-solid fa-plus me-1"></i> Add Record
                                                </button>
                                            </div>
                                            <div class="col-auto">
                                                <a class="btn bg-primary-subtle text-primary dropdown-toggle d-flex align-items-center arrow-none"
                                                    data-bs-toggle="dropdown" href="#" role="button"
                                                    aria-haspopup="false" aria-expanded="false"
                                                    data-bs-auto-close="outside">
                                                    <i class="iconoir-filter-alt me-1"></i> Filter
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-start">
                                                    <div class="p-2">
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="filterCheckbox" checked>
                                                            <label class="form-check-label" for="filterCheckbox">All</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="filter-super-admin">
                                                            <label class="form-check-label" for="filter-super-admin">Super
                                                                Admin</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="filter-admin">
                                                            <label class="form-check-label" for="filter-admin">Admin</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body pt-0">
                                <button type="button" class="btn btn-sm btn-primary csv">Export CSV</button>
                                <button type="button" class="btn btn-sm btn-primary excel">Export EXCEL</button>
                                <button type="button" class="btn btn-sm btn-primary pdf">Export PDF</button>
                                <button type="button" class="btn btn-sm btn-primary doc">Export DOC</button>

                                <div class="table-responsive pt-3">
                                    <table class="table mb-0 checkbox-all pt-2" id="datatable_2" data-title="Auth Users">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 16px;">
                                                    <div class="form-check mb-0">
                                                        <input type="checkbox" class="form-check-input" name="select-all"
                                                            id="select-all">
                                                    </div>
                                                </th>
                                                <th>#Sr. No.</th>
                                                <th class="ps-0">Auth Image</th>
                                                <th>Email Address</th>
                                                <th>Phone Number</th>
                                                <th>Auth Name</th>
                                                <th>Status</th>
                                                <th>Auth Type</th>
                                                <th>Action By</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Dynamic content --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <footer class="footer text-center text-sm-start d-print-none">
                <div class="container-xxl">
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-0 rounded-bottom-0">
                                <div class="card-body">
                                    <p class="text-muted mb-0">
                                        ©
                                        <script>
                                            document.write(new Date().getFullYear())
                                        </script>
                                        Rizz
                                        <span class="text-muted d-none d-sm-inline-block float-end">
                                            Crafted with <i class="iconoir-heart text-danger"></i> by Mannatthemes
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    {{-- ADD / UPDATE MODAL --}}
    <div class="modal fade bd-example-modal-lg" id="addUpdateRecord" tabindex="-1" role="dialog"
        aria-labelledby="addUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title m-0" id="addUpdateModalLabel">Add / Update Details</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="authUserId" value="">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="authUserName" maxlength="100"
                                autocomplete="new-name" placeholder="Full name" />
                            <div class="invalid-feedback" id="err_auth_user_name"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="authUserEmail" maxlength="50"
                                autocomplete="new-email" placeholder="email@example.com" />
                            <div class="invalid-feedback" id="err_auth_user_email"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="authUserPhoneNumber" minlength="10"
                                maxlength="10" autocomplete="new-phone-number" placeholder="10-digit number" />
                            <div class="invalid-feedback" id="err_auth_user_phone_number"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Password
                                <small class="text-muted">(Leave blank to keep unchanged)</small>
                            </label>
                            <input type="password" class="form-control" maxlength="50" id="authUserPassword"
                                autocomplete="new-password" placeholder="Min 8 characters" />
                            <div class="invalid-feedback" id="err_auth_user_password"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select form-control" id="authUserType">
                                <option value="">-- Select --</option>
                                <option value="SUPER_ADMIN">Super Admin</option>
                                <option value="ADMIN">Admin</option>
                            </select>
                            <div class="invalid-feedback" id="err_auth_user_type"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Active <span class="text-danger">*</span></label>
                            <select class="form-select form-control" id="authUserActive">
                                <option value="">-- Select --</option>
                                <option value="YES">Yes</option>
                                <option value="NO">No</option>
                            </select>
                            <div class="invalid-feedback" id="err_auth_user_status"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Upload Image
                                <small class="text-muted">(.jpg, .jpeg, .png)</small>
                            </label>
                            <input type="file" class="form-control" id="authUserImage" accept=".jpg,.jpeg,.png" />
                            <div class="invalid-feedback" id="err_auth_user_image"></div>
                        </div>
                        {{-- Image preview (shown when editing) --}}
                        <div class="col-md-4 d-none" id="currentImageWrap">
                            <label class="form-label">Current Image</label><br>
                            <img src="" id="currentImagePreview" alt="current"
                                class="thumb-lg rounded-circle border">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" id="saveAuthUserBtn">
                        <span id="saveAuthUserBtnText">Save</span>
                        <span id="saveAuthUserBtnSpinner" class="spinner-border spinner-border-sm ms-1 d-none"
                            role="status"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- VIEW MODAL --}}
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title m-0" id="viewDetailsModalTitle">View User Details</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img id="view_image" src="" alt="" class="rounded-circle"
                            style="width:80px;height:80px;object-fit:cover;">
                    </div>
                    <div class="row g-3">
                        <div class="col-6"><strong>Name</strong></div>
                        <div class="col-6 text-end" id="view_name"></div>

                        <div class="col-6"><strong>Email</strong></div>
                        <div class="col-6 text-end" id="view_email"></div>

                        <div class="col-6"><strong>Phone</strong></div>
                        <div class="col-6 text-end" id="view_phone"></div>

                        <div class="col-6"><strong>Type</strong></div>
                        <div class="col-6 text-end" id="view_type"></div>

                        <div class="col-6"><strong>Status</strong></div>
                        <div class="col-6 text-end" id="view_status"></div>

                        <div class="col-6"><strong>Action By</strong></div>
                        <div class="col-6 text-end" id="view_action_by"></div>

                        <div class="col-6"><strong>Created At</strong></div>
                        <div class="col-6 text-end" id="view_created_at"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {

            // Base URL for uploaded images
            const uploadBase = "{{ asset('vendor/upload') }}";
            // IF FALL BACK
            const avatarFallback = "{{ asset('assets/images/users/avatar-1.jpg') }}";

            let table = $('#datatable_2').DataTable({
                processing: false,
                serverSide: false,
                ajax: {
                    url: "{{ route('adminAuthUser.list') }}",
                    type: "GET",
                    dataSrc: "authUsersList",
                    data: function(d) {
                        let filters = [];
                        let allChecked = $('#filterCheckbox').is(':checked');
                        let superAdminChecked = $('#filter-super-admin').is(':checked');
                        let adminChecked = $('#filter-admin').is(':checked');

                        if (allChecked) {
                            filters = ['all'];
                        } else {
                            if (superAdminChecked) filters.push('SUPER_ADMIN');
                            if (adminChecked) filters.push('ADMIN');
                        }
                        d.auth_user_type = filters;
                    }
                },
                columns: [{
                        data: 'auth_user_id',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<div class="form-check">
                                        <input type="checkbox" class="form-check-input row-checkbox" value="${data}">
                                    </div>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'auth_user_image',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            let src = (data && data !== '') ?
                                `${uploadBase}/${data}` :
                                avatarFallback;
                            return `<img src="${src}" alt="" class="thumb-lg rounded-circle">`;
                        }
                    },
                    {
                        data: 'auth_user_email',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'auth_user_phone_number',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'auth_user_name',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'auth_user_status',
                        render: function(data) {
                            return data === 'YES' ?
                                '<span class="badge bg-info">Active</span>' :
                                '<span class="badge bg-danger">Inactive</span>';
                        }
                    },
                    {
                        data: 'auth_user_type',
                        render: function(data) {
                            return data === 'SUPER_ADMIN' ?
                                '<span class="badge bg-primary">Super Admin</span>' :
                                '<span class="badge bg-secondary">Admin</span>';
                        }
                    },
                    {
                        data: 'actionByName',
                        defaultContent: 'N/A'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="javascript:void(0);" class="view-auth-user"
                                        title="View"
                                        data-id="${data.auth_user_id}"
                                        data-name="${data.auth_user_name ?? ''}"
                                        data-email="${data.auth_user_email ?? ''}"
                                        data-phone="${data.auth_user_phone_number ?? ''}"
                                        data-type="${data.auth_user_type ?? ''}"
                                        data-status="${data.auth_user_status ?? ''}"
                                        data-action_by="${data.actionByName ?? 'N/A'}"
                                        data-created_at="${data.created_at ?? ''}"
                                        data-image="${data.auth_user_image ?? ''}">
                                        <i class="las la-eye text-secondary fs-18"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="edit-auth-user"
                                        title="Edit"
                                        data-id="${data.auth_user_id}"
                                        data-name="${data.auth_user_name ?? ''}"
                                        data-email="${data.auth_user_email ?? ''}"
                                        data-phone="${data.auth_user_phone_number ?? ''}"
                                        data-type="${data.auth_user_type ?? ''}"
                                        data-status="${data.auth_user_status ?? ''}"
                                        data-image="${data.auth_user_image ?? ''}">
                                        <i class="las la-pen text-primary fs-18"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="delete-auth-user"
                                        title="Delete"
                                        data-id="${data.auth_user_id}">
                                        <i class="las la-trash-alt text-danger fs-18"></i>
                                    </a>
                                </div>`;
                        }
                    }
                ]
            });

            $('#filterCheckbox').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#filter-super-admin, #filter-admin').prop('checked', false);
                }
                table.ajax.reload();
            });

            $('#filter-super-admin, #filter-admin').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#filterCheckbox').prop('checked', false);
                }
                table.ajax.reload();
            });

            // RESET MODAL DATA
            $('#addNewBtn').on('click', function() {
                resetModal();
                $('#addUpdateModalLabel').text('Add New Auth User');
                $('#saveAuthUserBtnText').text('Save');
            });

            function resetModal() {
                $('#authUserId').val('');
                $('#authUserName').val('').removeClass('is-invalid');
                $('#authUserEmail').val('').removeClass('is-invalid');
                $('#authUserPhoneNumber').val('').removeClass('is-invalid');
                $('#authUserPassword').val('').removeClass('is-invalid');
                $('#authUserType').val('').removeClass('is-invalid');
                $('#authUserActive').val('').removeClass('is-invalid');
                $('#authUserImage').val('').removeClass('is-invalid');
                $('#currentImageWrap').addClass('d-none');
                clearErrors();
            }

            function clearErrors() {
                $('[id^="err_"]').text('');
                $('.is-invalid').removeClass('is-invalid');
            }

            // VIEW DETAILS
            $(document).on('click', '.view-auth-user', function() {
                let el = $(this);
                let image = el.data('image');
                let type = el.data('type');
                let status = el.data('status');

                $('#view_image').attr('src', image ? `${uploadBase}/${image}` : avatarFallback);
                $('#view_name').text(el.data('name') || 'N/A');
                $('#view_email').text(el.data('email') || 'N/A');
                $('#view_phone').text(el.data('phone') || 'N/A');
                $('#view_type').html(
                    type === 'SUPER_ADMIN' ?
                    '<span class="badge bg-primary">Super Admin</span>' :
                    '<span class="badge bg-secondary">Admin</span>'
                );
                $('#view_status').html(
                    status === 'YES' ?
                    '<span class="badge bg-info">Active</span>' :
                    '<span class="badge bg-danger">Inactive</span>'
                );
                $('#view_action_by').text(el.data('action_by') || 'N/A');
                $('#view_created_at').text(el.data('created_at') || 'N/A');

                let viewModal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
                viewModal.show();
            });

            // EDIT PREVIEW DATA
            $(document).on('click', '.edit-auth-user', function() {
                let el = $(this);
                resetModal();

                $('#addUpdateModalLabel').text('Edit Auth User');
                $('#saveAuthUserBtnText').text('Update');

                $('#authUserId').val(el.data('id'));
                $('#authUserName').val(el.data('name'));
                $('#authUserEmail').val(el.data('email'));
                $('#authUserPhoneNumber').val(el.data('phone'));
                $('#authUserType').val(el.data('type'));
                $('#authUserActive').val(el.data('status'));

                let image = el.data('image');
                if (image) {
                    $('#currentImagePreview').attr('src', `${uploadBase}/${image}`);
                    $('#currentImageWrap').removeClass('d-none');
                }

                let editModal = new bootstrap.Modal(document.getElementById('addUpdateRecord'));
                editModal.show();
            });

            // SAVE / UPDATE DATA
            $('#saveAuthUserBtn').on('click', function() {
                let errors = [];

                let name = $('#authUserName').val().trim();
                let email = $('#authUserEmail').val().trim();
                let phone = $('#authUserPhoneNumber').val().trim();
                let pass = $('#authUserPassword').val();
                let type = $('#authUserType').val();
                let status = $('#authUserActive').val();
                let isUpdate = $('#authUserId').val() !== '';

                // VALIDATIONS
                if (!name) errors.push('Name is required.');

                if (!email) errors.push('Email is required.');
                else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))
                    errors.push('Enter a valid email address.');

                if (!phone) errors.push('Phone number is required.');
                else if (!/^\d{10}$/.test(phone)) errors.push('Phone number must be exactly 10 digits.');

                if (!isUpdate && !pass) errors.push('Password is required.');
                else if (pass && pass.length < 8) errors.push('Password must be at least 8 characters.');

                if (!type) errors.push('Auth type is required.');

                if (!status) errors.push('Active status is required.');

                // Image validation
                let imageFile = $('#authUserImage')[0].files[0];
                if (imageFile) {
                    let allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    let maxSize = 2 * 1024 * 1024;

                    if (!allowedTypes.includes(imageFile.type))
                        errors.push('Image must be a .jpg, .jpeg, or .png file.');
                    if (imageFile.size > maxSize)
                        errors.push('Image size must not exceed 2MB.');
                }

                if (errors.length > 0) {
                    errors.forEach(msg => toastr.error(msg));
                    return;
                }

                clearErrors();
                let btn = $(this);
                let spinner = $('#saveAuthUserBtnSpinner');

                let formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('auth_user_id', $('#authUserId').val());
                formData.append('auth_user_name', name);
                formData.append('auth_user_email', email);
                formData.append('auth_user_phone_number', phone);
                formData.append('auth_user_password', pass);
                formData.append('auth_user_type', type);
                formData.append('auth_user_status', status);
                if (imageFile) formData.append('auth_user_image', imageFile);

                btn.prop('disabled', true);
                spinner.removeClass('d-none');

                $.ajax({
                    url: "{{ route('adminAuthUser.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.message);
                        setTimeout(() => {
                            bootstrap.Modal.getInstance(
                                document.getElementById('addUpdateRecord')
                            ).hide();
                            table.ajax.reload(null, false);
                        }, 1000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON?.errors;

                            if (errors) {
                                $.each(errors, function(field, messages) {
                                    messages.forEach(msg => toastr.error(msg));
                                });
                            } else {
                                toastr.error(xhr.responseJSON?.message ?? 'Validation failed.');
                            }

                        } else {
                            toastr.error(xhr.responseJSON?.message ?? 'Something went wrong.');
                        }
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                        spinner.addClass('d-none');
                    }
                });
            });

            // SINGLE DELETE
            $(document).on('click', '.delete-auth-user', function() {
                let id = $(this).data('id');
                if (!confirm('Are you sure you want to delete this record?')) return;

                $.ajax({
                    url: "{{ route('adminAuthUser.bulkDelete') }}",
                    type: "POST",
                    data: {
                        dlt_ids: [id],
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        alert(response.message);
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message ?? 'Error deleting record.');
                    }
                });
            });

            // BULK DELETE
            $('#select-all').on('change', function() {
                $('.row-checkbox').prop('checked', this.checked);
                toggleDeleteButton();
            });

            $(document).on('change', '.row-checkbox', function() {
                let total = $('.row-checkbox').length;
                let checked = $('.row-checkbox:checked').length;
                $('#select-all').prop('checked', total === checked);
                toggleDeleteButton();
            });

            function toggleDeleteButton() {
                $('.delete-selected').prop('disabled', $('.row-checkbox:checked').length === 0);
            }

            // Re-bind checkboxes after DataTable redraws rows
            table.on('draw', function() {
                toggleDeleteButton();
                $('#select-all').prop('checked', false);
            });

            $(document).on('click', '.delete-selected', function() {
                let ids = [];
                $('.row-checkbox:checked').each(function() {
                    ids.push($(this).val());
                });

                if (ids.length === 0) return;
                if (!confirm('Are you sure you want to delete selected records?')) return;

                $.ajax({
                    url: "{{ route('adminAuthUser.bulkDelete') }}",
                    type: "POST",
                    data: {
                        dlt_ids: ids,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        alert(response.message);
                        table.ajax.reload(null, false);
                        $('.delete-selected').prop('disabled', true);
                        $('#select-all').prop('checked', false);
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message ?? 'Error deleting records.');
                    }
                });
            });
        });
    </script>
@endsection
