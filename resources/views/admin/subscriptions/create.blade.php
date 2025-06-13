@extends('layouts.admin')

@section('title', 'Nova Assinatura')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Nova Assinatura</h1>
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Voltar
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Cadastrar Nova Assinatura</h3>
        </div>
        <form action="{{ route('admin.subscriptions.store') }}" method="POST">
            @csrf
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
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
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
                           value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Descrição</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3">{{ old('description') }}</textarea>
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
                               value="{{ old('value') }}" required>
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
                                   value="{{ old('start_date') ?? date('Y-m-d\TH:i') }}" required>
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
                                   value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                        <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expirado</option>
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
                                                   {{ in_array($dnsRecord->id, old('dns_records', [])) ? 'checked' : '' }}>
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
                    <i class="fas fa-save mr-1"></i> Salvar
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
            
            // Configuração da data inicial e final
            $('#start_date').change(function() {
                let startDate = new Date($(this).val());
                let endDate = new Date(startDate);
                endDate.setMonth(endDate.getMonth() + 1);
                
                // Formata a data para o formato aceito pelo input datetime-local
                let year = endDate.getFullYear();
                let month = String(endDate.getMonth() + 1).padStart(2, '0');
                let day = String(endDate.getDate()).padStart(2, '0');
                let hours = String(endDate.getHours()).padStart(2, '0');
                let minutes = String(endDate.getMinutes()).padStart(2, '0');
                
                $('#end_date').val(`${year}-${month}-${day}T${hours}:${minutes}`);
            });
        });
    </script>
@stop
