@extends('layouts.app')

@section('title', 'Configurações da Empresa')

@section('content')
<div class="py-4 container-fluid">
    <!-- Header -->
    <div class="mb-4 row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0 text-gray-800 h3">Configurações da Empresa</h1>
                    <p class="text-muted">Gerencie as informações da sua empresa</p>
                </div>
                <div>
                    <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Formulário de Configurações -->
            <div class="shadow card">
                <div class="py-3 card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building"></i> Informações da Empresa
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Nome da Empresa -->
                            <div class="mb-3 col-md-6">
                                <label for="company_name" class="form-label">
                                    <i class="fas fa-building text-primary"></i> Nome da Empresa *
                                </label>
                                <input type="text"
                                       class="form-control @error('company_name') is-invalid @enderror"
                                       id="company_name"
                                       name="company_name"
                                       value="{{ old('company_name', $settings->company_name) }}"
                                       required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- NUIT -->
                            <div class="mb-3 col-md-6">
                                <label for="tax_number" class="form-label">
                                    <i class="fas fa-id-card text-info"></i> NUIT
                                </label>
                                <input type="text"
                                       class="form-control @error('tax_number') is-invalid @enderror"
                                       id="tax_number"
                                       name="tax_number"
                                       value="{{ old('tax_number', $settings->tax_number) }}"
                                       placeholder="123456789">
                                @error('tax_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Endereço -->
                        <div class="mb-3">
                            <label for="company_address" class="form-label">
                                <i class="fas fa-map-marker-alt text-warning"></i> Endereço Completo *
                            </label>
                            <textarea class="form-control @error('company_address') is-invalid @enderror"
                                      id="company_address"
                                      name="company_address"
                                      rows="3"
                                      required>{{ old('company_address', $settings->company_address) }}</textarea>
                            @error('company_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Telefone -->
                            <div class="mb-3 col-md-6">
                                <label for="company_phone" class="form-label">
                                    <i class="fas fa-phone text-success"></i> Telefone
                                </label>
                                <input type="text"
                                       class="form-control phone-mask @error('company_phone') is-invalid @enderror"
                                       id="company_phone"
                                       name="company_phone"
                                       value="{{ old('company_phone', $settings->company_phone) }}"
                                       placeholder="(84) 1234-5678">
                                @error('company_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- E-mail -->
                            <div class="mb-3 col-md-6">
                                <label for="company_email" class="form-label">
                                    <i class="fas fa-envelope text-primary"></i> E-mail
                                </label>
                                <input type="email"
                                       class="form-control @error('company_email') is-invalid @enderror"
                                       id="company_email"
                                       name="company_email"
                                       value="{{ old('company_email', $settings->company_email) }}"
                                       placeholder="contato@empresa.com">
                                @error('company_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Website -->
                        <div class="mb-3">
                            <label for="company_website" class="form-label">
                                <i class="fas fa-globe text-info"></i> Website
                            </label>
                            <input type="url"
                                   class="form-control @error('company_website') is-invalid @enderror"
                                   id="company_website"
                                   name="company_website"
                                   value="{{ old('company_website', $settings->company_website) }}"
                                   placeholder="https://www.empresa.com">
                            @error('company_website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Logo -->
                        <div class="mb-4">
                            <label for="company_logo" class="form-label">
                                <i class="fas fa-image text-warning"></i> Logo da Empresa
                            </label>
                            <input type="file"
                                   class="form-control @error('company_logo') is-invalid @enderror"
                                   id="company_logo"
                                   name="company_logo"
                                   accept="image/*">
                            @error('company_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Formatos aceitos: JPEG, PNG, JPG, GIF. Tamanho máximo: 2MB
                            </small>

                            @if($settings->company_logo)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $settings->company_logo) }}"
                                         alt="Logo atual"
                                         class="img-thumbnail"
                                         style="max-height: 100px;">
                                    <p class="mt-1 small text-muted">Logo atual</p>
                                </div>
                            @endif
                        </div>

                        <hr>

                        <!-- Botões de Ação -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-danger" onclick="resetForm()">
                                <i class="fas fa-undo"></i> Resetar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Configurações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview da Empresa -->
        <div class="col-lg-4">
            <div class="shadow card">
                <div class="py-3 card-header">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-eye"></i> Preview
                    </h6>
                </div>
                <div class="text-center card-body">
                    <div class="company-preview">
                        @if($settings->company_logo)
                            <img src="{{ asset('storage/' . $settings->company_logo) }}"
                                 alt="Logo"
                                 class="mb-3 img-fluid"
                                 style="max-height: 80px;">
                        @else
                            <div class="mb-3 text-white bg-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                 style="width: 80px; height: 80px; font-size: 24px;">
                                {{ strtoupper(substr($settings->company_name, 0, 2)) }}
                            </div>
                        @endif

                        <h5 class="company-name">{{ $settings->company_name }}</h5>

                        <div class="company-info text-muted">
                            @if($settings->tax_number)
                                <p class="mb-1">
                                    <i class="fas fa-id-card"></i> NUIT: {{ $settings->tax_number }}
                                </p>
                            @endif

                            <p class="mb-1">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ Str::limit($settings->company_address, 50) }}
                            </p>

                            @if($settings->company_phone)
                                <p class="mb-1">
                                    <i class="fas fa-phone"></i> {{ $settings->company_phone }}
                                </p>
                            @endif

                            @if($settings->company_email)
                                <p class="mb-1">
                                    <i class="fas fa-envelope"></i> {{ $settings->company_email }}
                                </p>
                            @endif

                            @if($settings->company_website)
                                <p class="mb-1">
                                    <i class="fas fa-globe"></i>
                                    <a href="{{ $settings->company_website }}" target="_blank" class="text-decoration-none">
                                        {{ $settings->company_website }}
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-center card-footer">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Essas informações aparecerão nas suas facturas e orçamentos
                    </small>
                </div>
            </div>

            <!-- Dicas -->
            <div class="mt-4 shadow card">
                <div class="py-3 card-header">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-lightbulb"></i> Dicas
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            Use um logo de alta qualidade para melhor apresentação
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            Mantenha o endereço completo e atualizado
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            O NUIT é obrigatório para emissão de facturas
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success"></i>
                            Verifique o preview antes de salvar
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function resetForm() {
    if (confirm('Tem certeza que deseja resetar o formulário?')) {
        document.querySelector('form').reset();
    }
}

// Preview em tempo real
document.addEventListener('DOMContentLoaded', function() {
    const companyNameInput = document.getElementById('company_name');
    const companyNamePreview = document.querySelector('.company-name');

    companyNameInput.addEventListener('input', function() {
        companyNamePreview.textContent = this.value || 'Nome da Empresa';
    });

    // Máscara para telefone
    const phoneInput = document.getElementById('company_phone');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
        e.target.value = value;
    });
});
</script>
@endpush

@push('styles')
<style>
.company-preview {
    padding: 1rem;
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    background-color: #f8f9fa;
}

.company-info p {
    font-size: 0.9rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.invalid-feedback {
    display: block;
}
</style>
@endpush
@endsection
