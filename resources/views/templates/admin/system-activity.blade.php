@extends('templates.after-login.layout')

@section('title', 'Manage System Activity')

@section('content')
    {{-- body layout --}}
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-xxl">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">Manage System Activity</h4>
                                    </div><!--end col-->
                                    <div class="col-auto">
                                        <form class="row g-2">
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-danger delete-selected" disabled>
                                                    Delete Selected
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
                                                            <label class="form-check-label" for="filter-all">
                                                                All
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="filter-success">
                                                            <label class="form-check-label" for="filter-success">
                                                                Success
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="filter-failed">
                                                            <label class="form-check-label" for="filter-failed">
                                                                Failed
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!--end col-->
                                        </form>
                                    </div><!--end col-->
                                </div><!--end row-->
                            </div><!--end card-header-->
                            <div class="card-body pt-0">
                                <button type="button" class="btn btn-sm btn-primary csv">Export CSV</button>
                                <button type="button" class="btn btn-sm btn-primary excel">Export EXCEL</button>
                                <button type="button" class="btn btn-sm btn-primary pdf">Export PDF</button>
                                <button type="button" class="btn btn-sm btn-primary doc">Export DOC</button>

                                <div class="table-responsive pt-3">
                                    <table class="table mb-0 checkbox-all pt-2" id="datatable_2" data-title="System Activity Log">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 16px;">
                                                    <div class="form-check mb-0">
                                                        <input type="checkbox" class="form-check-input" name="select-all"
                                                            id="select-all">
                                                    </div>
                                                </th>
                                                <th>#Sr. No.</th>
                                                <th>Browser</th>
                                                <th>Device Type</th>
                                                <th>IP Address</th>
                                                <th>Login Status</th>
                                                <th>Login Time</th>
                                                <th>OS Type</th>
                                                <th class="ps-0">Referrer URL</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- dynamic content --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div>

            <!-- start footer-->
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
                                            Crafted with
                                            <i class="iconoir-heart text-danger"></i>
                                            by Mannatthemes</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end footer-->
        </div>
    </div>

    {{-- view modal --}}
    <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" data-bs-toggle="#static">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title m-0" id="exampleModalScrollableTitle">View Details</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div><!--end modal-header-->
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-6"><strong>Browser</strong></div>
                        <div class="col-6 text-end" id="modal_browser"></div>

                        <div class="col-6"><strong>Operating System</strong></div>
                        <div class="col-6 text-end" id="modal_os"></div>

                        <div class="col-6"><strong>Device Type</strong></div>
                        <div class="col-6 text-end" id="modal_device"></div>

                        <div class="col-6"><strong>IP Address</strong></div>
                        <div class="col-6 text-end" id="modal_ip"></div>

                        <div class="col-6"><strong>Country</strong></div>
                        <div class="col-6 text-end" id="modal_country"></div>

                        <div class="col-6"><strong>State</strong></div>
                        <div class="col-6 text-end" id="modal_state"></div>

                        <div class="col-6"><strong>City</strong></div>
                        <div class="col-6 text-end" id="modal_city"></div>

                        <div class="col-6"><strong>Latitude / Longitude</strong></div>
                        <div class="col-6 text-end" id="modal_latlong"></div>

                        <div class="col-6"><strong>Login Time</strong></div>
                        <div class="col-6 text-end" id="modal_login_time"></div>

                        <div class="col-6"><strong>Status</strong></div>
                        <div class="col-6 text-end" id="modal_status"></div>

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
            let table = $('#datatable_2').DataTable({
                processing: false,
                serverSide: false,
                ajax: {
                    url: "{{ route('adminSystemActivity.fetch') }}",
                    type: "GET",
                    dataSrc: "systemActivitiesList",
                    data: function(d) {
                        let filters = [];

                        let allChecked = $('#filterCheckbox').is(':checked');
                        let successChecked = $('#filter-success').is(':checked');
                        let failedChecked = $('#filter-failed').is(':checked');

                        if (allChecked) {
                            filters = ['all'];
                        } else {
                            if (successChecked) filters.push('success');
                            if (failedChecked) filters.push('failed');
                        }

                        d.login_status = filters;
                    }
                },
                columns: [{
                        data: 'auth_login_audit_id',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                                <div class="form-check">
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
                        data: 'browser',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'device_type',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'ip_address',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'login_status',
                        render: function(data) {
                            return data === 'success' ?
                                '<span class="badge bg-success">Success</span>' :
                                '<span class="badge bg-danger">Failed</span>';
                        }
                    },
                    {
                        data: 'login_time'
                    },
                    {
                        data: 'operating_system',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'referrer_url',
                        render: function(data) {
                            return data ?
                                `<a href="${data}" target="_blank">${data.substring(0, 30)}</a>` :
                                'N/A';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                                <a class="view-details"
                                    data-bs-toggle="modal"
                                    data-bs-target="#exampleModalScrollable"
                                    data-browser="${data.browser ?? ''}"
                                    data-browser_version="${data.browser_version ?? ''}"
                                    data-os="${data.operating_system ?? ''}"
                                    data-os_version="${data.os_version ?? ''}"
                                    data-device="${data.device_type ?? ''}"
                                    data-ip="${data.ip_address ?? ''}"
                                    data-country="${data.country ?? ''}"
                                    data-state="${data.state ?? ''}"
                                    data-city="${data.city ?? ''}"
                                    data-lat="${data.lat ?? ''}"
                                    data-long="${data.long ?? ''}"
                                    data-status="${data.login_status}"
                                    data-login_time="${data.login_time}">
                                    <i class="las la-eye text-secondary fs-18"></i>
                                </a>`;
                        }
                    }
                ]
            });

            // Reload table when filters change
            $('#filterCheckbox').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#filter-success, #filter-failed').prop('checked', false);
                }
                table.ajax.reload();
            });

            $('#filter-success, #filter-failed').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#filterCheckbox').prop('checked', false);
                }
                table.ajax.reload();
            });

            // View details modal
            $(document).on('click', '.view-details', function() {
                $('#modal_browser').text($(this).data('browser') + ' ' + ($(this).data('browser_version') ??
                    ''));
                $('#modal_os').text($(this).data('os') + ' ' + ($(this).data('os_version') ?? ''));
                $('#modal_device').text($(this).data('device') ?? 'N/A');
                $('#modal_ip').text($(this).data('ip') ?? 'N/A');
                $('#modal_country').text($(this).data('country') ?? 'N/A');
                $('#modal_state').text($(this).data('state') ?? 'N/A');
                $('#modal_city').text($(this).data('city') ?? 'N/A');
                $('#modal_latlong').text(($(this).data('lat') ?? '') + ' / ' + ($(this).data('long') ??
                    ''));
                $('#modal_login_time').text($(this).data('login_time') ?? '');

                let status = $(this).data('status');
                $('#modal_status').html(
                    status === 'success' ?
                    '<span class="badge bg-success">Success</span>' :
                    '<span class="badge bg-danger">Failed</span>'
                );
            });

            // Individual checkboxes
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
                let checkedCount = $('.row-checkbox:checked').length;
                $('.delete-selected').prop('disabled', checkedCount === 0);
            }

            // Delete Selected
            $(document).on('click', '.delete-selected', function() {
                let ids = [];

                $('.row-checkbox:checked').each(function() {
                    ids.push($(this).val());
                });
                if (ids.length === 0) return;
                if (!confirm('Are you sure you want to delete selected records?')) {
                    return;
                }

                $.ajax({
                    url: "{{ route('adminSystemActivity.bulkDelete') }}",
                    type: "POST",
                    data: {
                        dlt_ids: ids,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        alert(response.message);
                        table.ajax.reload();
                        $('.delete-selected').prop('disabled', true);
                        $('#select-all').prop('checked', false);
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        alert('Error deleting records.');
                    }
                });
            });
        });
    </script>
@endsection
