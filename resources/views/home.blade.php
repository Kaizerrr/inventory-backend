@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            @if ($user->id !== Auth::user()->id) <!-- Exclude the current user -->
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role ? $user->role->name : 'N/A' }}</td> <!-- Add the null check here -->
                                <td>
                                    <form action="{{ route('changeRole', ['user' => $user->id]) }}" method="POST">
                                        @csrf
                                        <div class="dropdown btn-group" role="group">
                                            <button class="btn btn-primary dropdown-toggle" type="button" id="roleDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                @if ($user->role)
                                                {{ ucfirst($user->role->name) }}
                                                @else
                                                N/A
                                                @endif
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="roleDropdown">
                                                @foreach ($roles as $role)
                                                <li>
                                                    <button type="submit" name="role_id" value="{{ $role->id }}" class="dropdown-item">{{ ucfirst($role->name) }}</button>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection