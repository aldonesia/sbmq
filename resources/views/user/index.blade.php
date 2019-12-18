@extends('layouts.admin.index')

@section('content')
<div class="content">
    <h2 class="content-heading">User Management</h2>

    <!-- Dynamic Table Full -->
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Users List to Approve</h3>
        </div>
        <div class="block-content block-content-full">
            @if (session('message'))
                <div class="alert alert-success" role="alert">
                    {{ session('message') }}
                </div>
            @endif
            <!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
            <table id="user_table" class="table table-bordered table-striped table-vcenter js-dataTable-full table-responsive">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="d-none d-sm-table-cell text-center">User name</th>
                        <th class="d-none d-sm-table-cell text-center">Email</th>
                        <th class="d-none d-sm-table-cell text-center">IP</th>
                        <th class="d-none d-sm-table-cell text-center">Hostname</th>
                        <th class="d-none d-sm-table-cell text-center">Registered at</th>
                        <th class="d-none d-sm-table-cell text-center">Last Login at</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody> 
                @forelse ($users as $key => $user)
                    <tr>
                        <td class="text-center">{{ ++$key }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->ip }}</td>
                        <td>{{ $user->hostname }}</td>
                        <td>{{ date("d M y - G:i", strtotime($user->created_at)) }}</td>
                        <td>{{ date("d M y - G:i", strtotime($user->last_login_at)) }}</td>
                        @if($user->approved_at == NULL)
                        <td class="text-center"><a href="{{ route('user.approve', $user->id) }}"
                                class="btn btn-primary btn-sm">Approve</a></td>
                        @else
                        <td class="text-center">Approved</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No users found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
