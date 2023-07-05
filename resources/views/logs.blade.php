@extends('layouts.app')

@section('content')

<!-- Include necessary CSS and JavaScript files for DataTables -->
<link rel="stylesheet" type="text/css" src="{{ asset('resources/css/dataTables.bootstrap4.min.css') }}">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Logs') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <table id="logs-table" class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Description</th>
                                <th>Created/Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                            <tr>
                                <td>{{ $log->user->username }}</td>
                                <td>{{ $log->description }}</td>
                                <td>{{ $log->created_at }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <script src="{{ asset('\node_modules\jquery\dist\jquery.js') }}"></script>
                    <script src="{{ asset('resources/js/jquery.dataTables.min.js') }}"></script>
                    <script src="{{ asset('resources/js/dataTables.bootstrap4.min.js') }}"></script>
                    <script>
                        $(document).ready(function() {
                            $('#logs-table').DataTable({
                                "paging": true,
                                "lengthMenu": [10, 25, 50, 100],
                                "searching": true,
                                "ordering": true,
                                "info": true,
                                "autoWidth": false,
                                "order": [[0, "desc"]],
                                "language": {
                                    "paginate": {
                                        "previous": "&laquo;",
                                        "next": "&raquo;"
                                    },
                                    "search": "Search:",
                                    "lengthMenu": "Show _MENU_ entries",
                                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                                    "infoFiltered": "(filtered from _MAX_ total entries)",
                                    "emptyTable": "No data available in table"
                                }
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection