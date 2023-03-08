@extends('layouts.dashlayout')

@section('title', 'Campus Extension')

@section('nav')
    <!-- Sidebar -->
    <div class="sidebar">
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
            <li class="nav-item">
                <a href="{{ route('campus_extension.dashboard') }}" class="nav-link">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>
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
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>View Documents</p>
                        </a>     
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-check-square"></i>
                            <p>Approve/Reject Documents</p>
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
                <h2>Document View</h2>
            </div>
        </div>
    </div>
@endsection
