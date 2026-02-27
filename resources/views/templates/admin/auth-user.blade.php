@extends('templates.after-login.layout')

@section('title', 'Auth Users')

@section('content')
    <div class="page-content">
        <div class="container-xxl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title">Manage Auth User</h4>
                                </div><!--end col-->
                                <div class="col-auto">
                                    <form class="row g-2">
                                        <div class="col-auto">
                                            <a class="btn bg-primary-subtle text-primary dropdown-toggle d-flex align-items-center arrow-none"
                                                data-bs-toggle="dropdown" href="#" role="button"
                                                aria-haspopup="false" aria-expanded="false" data-bs-auto-close="outside">
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

                                        <div class="col-auto">
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#addBoard"><i class="fa-solid fa-plus me-1"></i> Add
                                                Product</button>
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
                                                    <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                                    <a href="#"><i
                                                            class="las la-trash-alt text-secondary fs-18"></i></a>
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
@endsection
