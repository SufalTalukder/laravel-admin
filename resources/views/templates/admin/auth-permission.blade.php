@extends('templates.after-login.layout')

@section('title', 'Auth Permissions')

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
                                        <h4 class="card-title">Manage Auth Permissions</h4>
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
                                                <th>#Sr. No.</th>
                                                <th class="ps-0">Auth Image</th>
                                                <th>Auth Name</th>
                                                <th>Create Permission</th>
                                                <th>View All Permission</th>
                                                <th>View Permission</th>
                                                <th>Update Permission</th>
                                                <th>Delete Permission</th>
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

    <script type="text/javascript">
        $(document).ready(function() {
            // Get super admin ID
            const authUserId = <?php echo $authData->auth_user_id; ?>;

            // Base URL for uploaded images
            const uploadBase = "{{ asset('vendor/upload') }}";
            // IF FALL BACK
            const avatarFallback = "{{ asset('assets/images/users/avatar-1.jpg') }}";

            let table = $('#datatable_2').DataTable({
                processing: false,
                serverSide: false,
                ajax: {
                    url: "{{ route('adminAuthPermission.list') }}",
                    type: "GET",
                    dataSrc: "authUsersList",
                    data: function(d) {}
                },
                columns: [{
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
                            let src = (data && data !== '') ? `${uploadBase}/${data}` :
                                avatarFallback;
                            return `<img src="${src}" alt="" class="thumb-lg rounded-circle">`;
                        }
                    },
                    {
                        data: 'auth_user_name',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'add_permission',
                        render: function(data, type, row) {
                            let checked = data === 'YES' ? 'checked' : '';
                            let disabled = row.auth_user_id === authUserId ? 'disabled' : '';
                            return `<div class="form-check mb-0">
                                        <input type="checkbox" class="form-check-input permission-checkbox"
                                            data-id="${row.auth_user_id}"
                                            data-type="add_permission" ${checked} ${disabled}>
                                    </div>`;
                        }
                    },
                    {
                        data: 'view_all_permission',
                        render: function(data, type, row) {
                            let checked = data === 'YES' ? 'checked' : '';
                            let disabled = row.auth_user_id === authUserId ? 'disabled' : '';
                            return `<div class="form-check mb-0">
                                        <input type="checkbox" class="form-check-input permission-checkbox"
                                            data-id="${row.auth_user_id}"
                                            data-type="view_all_permission" ${checked} ${disabled}>
                                    </div>`;
                        }
                    },
                    {
                        data: 'view_permission',
                        render: function(data, type, row) {
                            let checked = data === 'YES' ? 'checked' : '';
                            let disabled = row.auth_user_id === authUserId ? 'disabled' : '';
                            return `<div class="form-check mb-0">
                                        <input type="checkbox" class="form-check-input permission-checkbox"
                                            data-id="${row.auth_user_id}"
                                            data-type="view_permission" ${checked} ${disabled}>
                                    </div>`;
                        }
                    },
                    {
                        data: 'edit_permission',
                        render: function(data, type, row) {
                            let checked = data === 'YES' ? 'checked' : '';
                            let disabled = row.auth_user_id === authUserId ? 'disabled' : '';
                            return `<div class="form-check mb-0">
                                        <input type="checkbox" class="form-check-input permission-checkbox"
                                            data-id="${row.auth_user_id}"
                                            data-type="edit_permission" ${checked} ${disabled}>
                                    </div>`;
                        }
                    },
                    {
                        data: 'delete_permission',
                        render: function(data, type, row) {
                            let checked = data === 'YES' ? 'checked' : '';
                            let disabled = row.auth_user_id === authUserId ? 'disabled' : '';
                            return `<div class="form-check mb-0">
                                        <input type="checkbox" class="form-check-input permission-checkbox"
                                            data-id="${row.auth_user_id}"
                                            data-type="delete_permission" ${checked} ${disabled}>
                                    </div>`;
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
                        render: function(data, type, row) {
                            let disabled = row.auth_user_id === authUserId ? 'disabled' : '';
                            return `<div class="d-flex justify-content-end gap-2">
                                        <button type="button"
                                            class="btn btn-sm btn-primary save-permission-btn"
                                            data-apid="${data.auth_permission_id}"
                                            data-aid="${data.auth_user_id}"
                                            ${disabled}>
                                            <i class="las la-save"></i> Save
                                        </button>
                                    </div>`;
                        }
                    },
                ]
            });

            // SAVE / UPDATE DATA
            $(document).on('click', '.save-permission-btn', function() {
                let btn = $(this);
                let authPermissionId = btn.data('apid');
                let userId = btn.data('aid');
                let row = btn.closest('tr');

                let permissions = {};
                row.find('.permission-checkbox').each(function() {
                    let type = $(this).data('type');
                    permissions[type] = $(this).is(':checked') ? 'YES' : 'NO';
                });

                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Saving...');

                $.ajax({
                    url: "{{ route('adminAuthPermission.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        auth_permission_id: authPermissionId,
                        auth_user_id: userId,
                        add_permission: permissions.add_permission,
                        view_all_permission: permissions.view_all_permission,
                        view_permission: permissions.view_permission,
                        edit_permission: permissions.edit_permission,
                        delete_permission: permissions.delete_permission,
                    },
                    success: function(response) {
                        toastr.success(response.message ??
                            'Permissions modified successfully.');
                        table.ajax.reload(null, false);
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
                        btn.prop('disabled', false).html('<i class="las la-save"></i> Save');
                    }
                });
            });
        });
    </script>
@endsection
