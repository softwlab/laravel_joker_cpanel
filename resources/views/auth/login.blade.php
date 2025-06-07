@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-sign-in-alt"></i> Login</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ url('auth/login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="user" class="form-label">Usuário</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control @error('user') is-invalid @enderror"
                                id="user" name="user" value="{{ old('user') }}" required autofocus>
                        </div>
                        @error('user')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control @error('senha') is-invalid @enderror"
                                id="senha" name="senha" required>
                        </div>
                        @error('senha')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Entrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection