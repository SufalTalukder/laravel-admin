@extends('templates.after-login.layout')

@section('title', 'Manage System Activity')

@section('content')
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
                                                <a class="btn bg-primary-subtle text-primary dropdown-toggle d-flex align-items-center arrow-none"
                                                    data-bs-toggle="dropdown" href="#" role="button"
                                                    aria-haspopup="false" aria-expanded="false"
                                                    data-bs-auto-close="outside">
                                                    <i class="iconoir-filter-alt me-1"></i> Filter
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-start">
                                                    <div class="p-2">
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input" checked
                                                                id="filter-all">
                                                            <label class="form-check-label" for="filter-all">
                                                                All
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input" checked
                                                                id="filter-one">
                                                            <label class="form-check-label" for="filter-one">
                                                                New
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input" checked
                                                                id="filter-two">
                                                            <label class="form-check-label" for="filter-two">
                                                                VIP
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input" checked
                                                                id="filter-three">
                                                            <label class="form-check-label" for="filter-three">
                                                                Repeat
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input" checked
                                                                id="filter-four">
                                                            <label class="form-check-label" for="filter-four">
                                                                Referral
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input" checked
                                                                id="filter-five">
                                                            <label class="form-check-label" for="filter-five">
                                                                Inactive
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" checked
                                                                id="filter-six">
                                                            <label class="form-check-label" for="filter-six">
                                                                Loyal
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
                                        <tbody>
                                            @forelse($systemActivitiesList as $index => $activity)
                                                <tr>
                                                    <td style="width: 16px;">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" name="check"
                                                                id="customCheck1">
                                                        </div>
                                                    </td>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $activity->browser ?? 'N/A' }}</td>
                                                    <td>{{ $activity->device_type ?? 'N/A' }}</td>
                                                    <td>{{ $activity->ip_address ?? 'N/A' }}</td>
                                                    <td>
                                                        @if ($activity->login_status == 'success')
                                                            <span class="badge bg-success">Success</span>
                                                        @else
                                                            <span class="badge bg-danger">Failed</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $activity->login_time->format('d M Y h:i A') }}
                                                    </td>
                                                    <td>{{ $activity->operating_system ?? 'N/A' }}</td>
                                                    <td>
                                                        @if ($activity->referrer_url)
                                                            <a href="{{ $activity->referrer_url }}" target="_blank">
                                                                {{ \Illuminate\Support\Str::limit($activity->referrer_url, 30) }}
                                                            </a>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <a class="view-details" data-bs-toggle="modal"
                                                            data-bs-target="#exampleModalScrollable"
                                                            data-browser="{{ $activity->browser }}"
                                                            data-browser_version="{{ $activity->browser_version }}"
                                                            data-os="{{ $activity->operating_system }}"
                                                            data-os_version="{{ $activity->os_version }}"
                                                            data-device="{{ $activity->device_type }}"
                                                            data-ip="{{ $activity->ip_address }}"
                                                            data-country="{{ $activity->country }}"
                                                            data-state="{{ $activity->state }}"
                                                            data-city="{{ $activity->city }}"
                                                            data-lat="{{ $activity->lat }}"
                                                            data-long="{{ $activity->long }}"
                                                            data-status="{{ $activity->login_status }}"
                                                            data-login_time="{{ $activity->login_time->format('d M Y h:i A') }}">
                                                            <i class="las la-eye text-secondary fs-18"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">No System Activity Found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div><!-- container -->
            <!--end footer-->
        </div>
    </div>

    {{-- view modal --}}
    <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title m-0" id="exampleModalScrollableTitle">Center Modal</h6>
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
                </div><!--end modal-body-->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm">Save changes</button>
                </div><!--end modal-footer-->
            </div><!--end modal-content-->
        </div><!--end modal-dialog-->
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".view-details").forEach(a => {
                a.addEventListener("click", function() {
                    document.getElementById("modal_browser").innerText =
                        this.dataset.browser + " " + (this.dataset.browser_version ?? '');

                    document.getElementById("modal_os").innerText =
                        this.dataset.os + " " + (this.dataset.os_version ?? '');

                    document.getElementById("modal_device").innerText =
                        this.dataset.device ?? 'N/A';

                    document.getElementById("modal_ip").innerText =
                        this.dataset.ip ?? 'N/A';

                    document.getElementById("modal_country").innerText =
                        this.dataset.country ?? 'N/A';

                    document.getElementById("modal_state").innerText =
                        this.dataset.state ?? 'N/A';

                    document.getElementById("modal_city").innerText =
                        this.dataset.city ?? 'N/A';

                    document.getElementById("modal_latlong").innerText =
                        (this.dataset.lat ?? '') + " / " + (this.dataset.long ?? '');

                    document.getElementById("modal_login_time").innerText =
                        this.dataset.login_time ?? '';

                    let status = this.dataset.status;
                    document.getElementById("modal_status").innerHTML =
                        status === 'success' ?
                        '<span class="badge bg-success">Success</span>' :
                        '<span class="badge bg-danger">Failed</span>';
                });
            });
        });
    </script>
@endsection
