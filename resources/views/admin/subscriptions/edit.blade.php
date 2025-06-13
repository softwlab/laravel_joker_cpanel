@extends('layouts.admin')

@section('title', 'Editar Assinatura')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Editar Assinatura</h1>
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Voltar
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Editar Assinatura #{{ $subscription->id }}</h3>
        </div>
        <form action="{{ route('admin.subscriptions.update', $subscription->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="user_id">Usuário</label>
                    <select name="user_id" id="user_id" class="form-control select2 @error('user_id') is-invalid @enderror" required>
                        <option value="">Selecione um usuário</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $subscription->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name">Nome da Assinatura</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name', $subscription->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Descrição</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3">{{ old('description', $subscription->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="value">Valor</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">R$</span>
                        </div>
                        <input type="text" name="value" id="value" class="form-control money @error('value') is-invalid @enderror" 
                               value="{{ old('value', number_format($subscription->value, 2, ',', '.')) }}" required>
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">Data Inicial</label>
                            <input type="datetime-local" name="start_date" id="start_date" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   value="{{ old('start_date', $subscription->start_date->format('Y-m-d\TH:i')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">Data Final</label>
                            <input type="datetime-local" name="end_date" id="end_date" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   value="{{ old('end_date', $subscription->end_date->format('Y-m-d\TH:i')) }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $subscription->status) == 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ old('status', $subscription->status) == 'inactive' ? 'selected' : '' }}>Inativo</option>
                        <option value="expired" {{ old('status', $subscription->status) == 'expired' ? 'selected' : '' }}>Expirado</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Registros DNS Associados</label>
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Selecione os registros DNS</h3>
                        </div>
                        <div class="card-body">
                            <div class="row dns-records-list">
                                @foreach($dnsRecords as $dnsRecord)
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" 
                                                   name="dns_records[]" 
                                                   value="{{ $dnsRecord->id }}" 
                                                   id="dns_record_{{ $dnsRecord->id }}" 
                                                   class="custom-control-input"
                                                   {{ in_array($dnsRecord->id, old('dns_records', $subscription->dnsRecords->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="dns_record_{{ $dnsRecord->id }}">
                                                {{ $dnsRecord->name }} 
                                                <small class="text-muted">({{ $dnsRecord->record_type }}: {{ $dnsRecord->content }})</small>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('dns_records')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Atualizar
                </button>
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            
            // Configuração da máscara de dinheiro
            $('.money').maskMoney({
                prefix: '',
                thousands: '.',
                decimal: ',',
                precision: 2,
                allowZero: true
            });
        });
    </script>
@stop
