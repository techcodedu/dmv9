@extends('layouts.dashlayout')
@section('title', 'Document History')

@section('styles')
    <style>
    .dataTables_wrapper .dataTables_processing {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 100%;
        height: 40px;
        margin-left: -50%;
        margin-top: -25px;
        padding-top: 20px;
        text-align: center;
        font-size: 1.2em;
        background-color: transparent;
        background: rgba(255, 255, 255, 0.8);
        color: #333;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_processing,
    .dataTables_wrapper .dataTables_paginate {
        color: #333;
    }
</style>

@endsection
@section('nav')
    <!-- Sidebar -->
    <div class="sidebar">
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-folder-open"></i>
                    <p>
                        Document
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('campus_records.incoming') }}" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Incoming Documents</p>
                        </a>     
                    </li>
                    <li class="nav-item">
                         <a href="{{ route('campus_records.outgoing') }}" class="nav-link">
                            <i class="nav-icon fas fa-check-square"></i>
                            <p>Outgoing Documents</p>
                        </a>
                    </li>
                    <li class="nav-item">
                         <a href="{{ route('document_history') }}" class="nav-link">
                            <i class="nav-icon fas fa-check-square"></i>
                            <p>Document History</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>Settings</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-bell"></i>
                    <p>Notifications</p>
                </a>
            </li>
        </ul>
    </nav>
</div>

    <!-- /.sidebar -->
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
            <h2>Document History</h2>
                <table id="document-history" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Document ID</th>
                            <th>Department</th>
                            <th>From Office</th>
                            <th>To Office</th>
                            <th>Forwarded By</th>
                            <th>Status</th>
                            <th>Date Forwarded</th>
                            <th>Date Endorsed</th>
                            <th>Date Approved</th>
                            <th>Date Signed</th>
                            <th>Date Released</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
        <script>
        function fromOfficeRenderer(data, type, row) {
            return '<span class="badge bg-success">' + data + '</span>';
        }

        function toOfficeRenderer(data, type, row) {
            return '<span class="badge bg-primary">' + data + '</span>';
        }

        function formatDate(date) {
            const currentDate = moment();
            const dateMoment = moment(date);
            const duration = moment.duration(currentDate.diff(dateMoment));
            const days = duration.asDays();

            let dateStr = dateMoment.format('MMM D, YYYY');
            let timeAgoStr = dateMoment.fromNow();

            if (days < 1) {
                timeAgoStr = timeAgoStr.charAt(0).toUpperCase() + timeAgoStr.slice(1);
                return `<span class="text-success small">${dateStr} (${timeAgoStr}) at ${dateMoment.format('h:mm A')}</span>`;
            } else if (days < 2) {
                return `${dateStr} (Yesterday)`;
            } else {
                return dateStr;
            }
        }



        $(document).ready(function() {
            $('#document-history').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("document_history.data") }}',
                columns: [
                    { data: 'document.document_id', name: 'document.document_id' },
                    { data: 'document.department.name', name: 'document.department.name' },
                    { data: 'fromOffice.name', name: 'fromOffice.name', render: fromOfficeRenderer },
                    { data: 'toOffice.name', name: 'toOffice.name', render: toOfficeRenderer },
                    { data: 'forwardedBy.name', name: 'forwardedBy.name' },
                    { data: 'status', name: 'status' },
                    { data: 'date_forwarded', name: 'date_forwarded', render: function(data, type, row) {
                        return formatDate(data);
                    }},

                    { data: 'date_endorsed', name: 'date_endorsed', render: function(data, type, row) {
                        return formatDate(data);
                    }},
                    { data: 'date_approved', name: 'date_approved', render: function(data, type, row) {
                        return formatDate(data);
                    }},
                    { data: 'date_signed', name: 'date_signed', render: function(data, type, row) {
                        return formatDate(data);
                    }},
                { data: 'date_released', name: 'date_released', render: function(data, type, row) {
                        return formatDate(data);
                    }}
                ]
            });
        });
        </script>
    @endsection
@endsection




