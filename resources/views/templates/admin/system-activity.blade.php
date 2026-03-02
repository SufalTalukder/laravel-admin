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
                                <button type="button" class="btn btn-sm btn-primary sql">Export SQL</button>
                                <button type="button" class="btn btn-sm btn-primary txt">Export TXT</button>
                                <button type="button" class="btn btn-sm btn-primary json">Export JSON</button>

                                <div class="table-responsive pt-3">
                                    <table class="table mb-0 checkbox-all" id="datatable_2">
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
                                        <tbody id="systemActivityTable">
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

            // Global variable
            let totalActivitiesLength = 0;

            // Load records with filter
            function loadSystemActivity() {
                let filters = [];

                if ($('#filterCheckbox').is(':checked')) filters.push('all');
                if ($('#filter-success').is(':checked')) filters.push('success');
                if ($('#filter-failed').is(':checked')) filters.push('failed');

                $('#systemActivityTable').html('<tr><td colspan="10" class="text-center">Loading...</td></tr>');

                $.ajax({
                    url: "{{ route('adminSystemActivityStatusView') }}",
                    type: "GET",
                    data: {
                        login_status: filters
                    },
                    success: function(response) {
                        let activities = response.systemActivitiesList;
                        // Length stored inside global variable
                        totalActivitiesLength = response.systemActivitiesList.length;
                        let tbody = '';

                        if (activities.length > 0) {
                            $.each(activities, function(index, activity) {
                                tbody += '<tr>';
                                tbody +=
                                    '<td style="width:16px;"><div class="form-check"><input type="checkbox" class="form-check-input" value="' +
                                    activity.auth_login_audit_id + '"></div></td>';
                                tbody += '<td>' + (index + 1) + '</td>';
                                tbody += '<td>' + (activity.browser ?? 'N/A') + '</td>';
                                tbody += '<td>' + (activity.device_type ?? 'N/A') + '</td>';
                                tbody += '<td>' + (activity.ip_address ?? 'N/A') + '</td>';
                                tbody += '<td>' + (activity.login_status === 'success' ?
                                    '<span class="badge bg-success">Success</span>' :
                                    '<span class="badge bg-danger">Failed</span>') + '</td>';
                                tbody += '<td>' + activity.login_time + '</td>';
                                tbody += '<td>' + (activity.operating_system ?? 'N/A') +
                                    '</td>';
                                tbody += '<td>' + (activity.referrer_url ? '<a href="' +
                                        activity.referrer_url + '" target="_blank">' + activity
                                        .referrer_url.substring(0, 30) + '</a>' : 'N/A') +
                                    '</td>';
                                tbody += '<td class="text-end">';
                                tbody +=
                                    '<a class="view-details" data-bs-toggle="modal" data-bs-target="#exampleModalScrollable" ' +
                                    'data-browser="' + (activity.browser ?? '') + '" ' +
                                    'data-browser_version="' + (activity.browser_version ??
                                        '') + '" ' +
                                    'data-os="' + (activity.operating_system ?? '') + '" ' +
                                    'data-os_version="' + (activity.os_version ?? '') + '" ' +
                                    'data-device="' + (activity.device_type ?? '') + '" ' +
                                    'data-ip="' + (activity.ip_address ?? '') + '" ' +
                                    'data-country="' + (activity.country ?? '') + '" ' +
                                    'data-state="' + (activity.state ?? '') + '" ' +
                                    'data-city="' + (activity.city ?? '') + '" ' +
                                    'data-lat="' + (activity.lat ?? '') + '" ' +
                                    'data-long="' + (activity.long ?? '') + '" ' +
                                    'data-status="' + activity.login_status + '" ' +
                                    'data-login_time="' + activity.login_time + '">' +
                                    '<i class="las la-eye text-secondary fs-18"></i></a>';
                                tbody += '</td>';
                                tbody += '</tr>';
                            });
                        } else {
                            tbody =
                                '<tr><td colspan="10" class="text-center">No System Activity Found</td></tr>';
                        }
                        $('#systemActivityTable').html(tbody);
                    },
                    error: function(xhr) {
                        console.error("Error in System Activity: ", xhr);
                        $('#systemActivityTable').html(
                            '<tr><td colspan="10" class="text-center text-danger">Error loading data</td></tr>'
                        );
                    }
                });
            }

            // View details
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
                $('#modal_status').html(status === 'success' ?
                    '<span class="badge bg-success">Success</span>' :
                    '<span class="badge bg-danger">Failed</span>');
            });

            // Delete multiple records
            // Check select all
            $('#select-all').on('change', function() {
                $('#systemActivityTable input.form-check-input')
                    .prop('checked', this.checked);

                if (this.checked) {
                    $('.delete-selected').prop('disabled', false);
                } else {
                    $('.delete-selected').prop('disabled', true);
                }
            });

            // Check each checkbox
            $(document).on('change', '.form-check-input', function() {
                let countCheckboxs = [];

                $('#systemActivityTable input.form-check-input:checked').each(function() {
                    countCheckboxs.push($(this).val());
                });

                if (countCheckboxs.length > 0) {
                    $('.delete-selected').prop('disabled', false);
                } else {
                    $('.delete-selected').prop('disabled', true);
                }

                if (totalActivitiesLength === countCheckboxs.length) {
                    $('#select-all').prop('checked', true);
                } else {
                    $('#select-all').prop('checked', false);
                }
            });

            // Delete selected records
            $(document).on('click', '.delete-selected', function() {
                let deleteCheckboxs = [];

                $('#systemActivityTable input.form-check-input:checked').each(function() {
                    deleteCheckboxs.push($(this).val());
                });

                if (!confirm('Are you sure! you want to delete selected records?')) {
                    return;
                }

                $.ajax({
                    url: "{{ route('adminSystemActivityDeleteView') }}",
                    type: "POST",
                    data: {
                        dlt_ids: deleteCheckboxs,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        alert(response.message);
                        $('.delete-selected').prop('disabled',
                            true); // Disable button after success
                        loadSystemActivity();
                    },
                    error: function(xhr) {
                        console.error("Delete Error: ", xhr);
                        alert('Error deleting records.');
                    }
                });
            });

            $('#filterCheckbox, #filter-success, #filter-failed').on('change', loadSystemActivity);

            loadSystemActivity();
        });
    </script>
@endsection
