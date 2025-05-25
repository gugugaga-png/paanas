@extends('layouts.app')

@section('content')
    <div class="container-tight py-4">

        <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-4">Create an account</h2>

            <form method="POST" action="{{ route('register') }}" autocomplete="off" novalidate>
                @csrf

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                

                <div class="mb-3">
    <label class="form-label">Role</label>
    <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
        <option value="" disabled selected>-- Select Role --</option>
        @foreach(\App\Models\Role::all() as $role)
            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                {{ ucfirst($role->name) }}
            </option>
        @endforeach
    </select>
    @error('role_id')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>


                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Create Account</button>
                </div>
            </form>
        </div>
        <div class="hr-text">or</div>
        <div class="text-center text-muted mt-3">
            Already have an account? <a href="{{ route('login') }}">Login</a>
        </div>
    </div>
    </div>
@endsection
