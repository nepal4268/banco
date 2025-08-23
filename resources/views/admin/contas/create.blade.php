@extends('layouts.app')

@section('title', 'Nova Conta')
@section('page-title', 'Nova Conta')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.contas.index') }}">Contas</a></li>
<li class="breadcrumb-item active">Novo</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Criar Conta</h3></div>
            <form method="POST" action="{{ route('admin.contas.store') }}">
                @csrf
                <div class="card-body">
                    @if(isset($cliente))
                        <div class="alert alert-info">Criando conta para <strong>{{ $cliente->nome }}</strong> (BI: {{ $cliente->bi }})</div>
                        <input type="hidden" name="cliente_id" value="{{ $cliente->id }}" />
                    @endif
                    <div class="row">
                        @unless(isset($cliente))
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cliente_id">Cliente *</label>
                                <select name="cliente_id" id="cliente_id" class="form-control">
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endunless
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="agencia_id">Agência *</label>
                                <select name="agencia_id" id="agencia_id" class="form-control" {{ auth()->user()->isAdmin() ? '' : 'disabled' }}>
                                    @foreach($agencias as $agencia)
                                        <option value="{{ $agencia->id }}" {{ (old('agencia_id') == $agencia->id) || (!old('agencia_id') && auth()->user()->agencia_id == $agencia->id) ? 'selected' : '' }}>{{ $agencia->nome }}</option>
                                    @endforeach
                                </select>
                                @if(!auth()->user()->isAdmin())
                                    <input type="hidden" name="agencia_id" value="{{ auth()->user()->agencia_id }}">
                                @else
                                    {{-- ensure select value is submitted even if browser doesn't send disabled selects in some cases --}}
                                    <input type="hidden" id="agencia_id_hidden" name="agencia_id" value="{{ old('agencia_id', auth()->user()->agencia_id) }}">
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tipo_conta_id">Tipo de Conta *</label>
                                <select name="tipo_conta_id" id="tipo_conta_id" class="form-control">
                                    @foreach($tiposConta as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('tipo_conta_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="moeda_id">Moeda</label>
                                <select name="moeda_id" id="moeda_id" class="form-control">
                                    @foreach($moedas as $moeda)
                                        <option value="{{ $moeda->id }}">{{ $moeda->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status_conta_id">Status</label>
                                <select name="status_conta_id" id="status_conta_id" class="form-control">
                                    @foreach($statusConta as $status)
                                        <option value="{{ $status->id }}" {{ old('status_conta_id') == $status->id ? 'selected' : '' }}>{{ $status->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="numero_conta">Número da Conta</label>
                                <div class="input-group">
                                    <input type="text" name="numero_conta" id="numero_conta" class="form-control" readonly>
                                    <button type="button" id="gerarContaBtn" class="btn btn-secondary">
                                        <span id="gerarSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        <span id="gerarLabel">Gerar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="iban">IBAN</label>
                                <div class="input-group">
                                    <input type="text" name="iban" id="iban" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="saldo_inicial">Saldo Inicial</label>
                                <input type="number" step="0.01" name="saldo_inicial" id="saldo_inicial" class="form-control" value="{{ old('saldo_inicial', 0) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="limite_credito">Limite de Crédito</label>
                                <input type="number" step="0.01" name="limite_credito" id="limite_credito" class="form-control" value="{{ old('limite_credito') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary">Criar Conta</button>
                    <a href="{{ route('admin.contas.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

    @push('scripts')
    <script>
document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('gerarContaBtn');
    if (!btn) return;

    btn.addEventListener('click', function(){
    btn.disabled = true;
    const originalLabel = document.getElementById('gerarLabel');
    const spinner = document.getElementById('gerarSpinner');
    if (spinner) spinner.classList.remove('d-none');
    if (originalLabel) originalLabel.textContent = 'Gerando...';

    const agenciaSelect = document.getElementById('agencia_id');
    let agenciaId = agenciaSelect ? agenciaSelect.value : '';
    // keep hidden field in sync if present
    const agenciaHidden = document.getElementById('agencia_id_hidden');
    if (agenciaHidden && agenciaId) agenciaHidden.value = agenciaId;
        // route helper returns path like /admin/contas/generate-account
        let url = "{{ route('admin.contas.generateAccount') }}";
        if (agenciaId) url += '?agencia_id=' + encodeURIComponent(agenciaId);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' })
            .then(response => {
                if (!response.ok) throw response;
                return response.json();
            })
            .then(data => {
                // Defensive checks
                if (!data || (typeof data.numero_conta === 'undefined' && typeof data.iban === 'undefined')) {
                    console.error('Resposta inesperada:', data);
                    throw new Error('Dados inválidos retornados');
                }
                // keep spinner visible for at least 2 seconds for UX
                setTimeout(function(){
                    if (typeof data.numero_conta !== 'undefined') document.getElementById('numero_conta').value = data.numero_conta;
                    if (typeof data.iban !== 'undefined') document.getElementById('iban').value = data.iban;
                    if (spinner) spinner.classList.add('d-none');
                    if (originalLabel) originalLabel.textContent = 'Gerado';
                    btn.disabled = true;
                }, 2000);
            })
            .catch(err => {
                console.error('Erro ao gerar conta (autenticado):', err);
                if (spinner) spinner.classList.add('d-none');
                if (originalLabel) originalLabel.textContent = 'Gerar';
                btn.disabled = false;
                alert('Erro ao gerar número de conta. Verifique se está autenticado e tente novamente.');
            });
    });
});
</script>
    @endpush

@if(isset($cliente) && isset($existingContas))
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        // existingContas provided by controller
        const existingContas = {!! json_encode($existingContas) !!};
        const tipoSelect = document.getElementById('tipo_conta_id');
        const moedaSelect = document.getElementById('moeda_id');

        const aviso = document.createElement('div');
        aviso.id = 'moeda_aviso';
        aviso.className = 'text-muted small mt-2';
        moedaSelect.parentNode.appendChild(aviso);

        function refreshMoedas(){
            const selectedTipo = parseInt(tipoSelect.value || 0, 10);
            // Find moedas used by this cliente for the selected tipo
            const usadas = existingContas.filter(c => parseInt(c.tipo_conta_id) === selectedTipo).map(c => parseInt(c.moeda_id));

            // For each option in moedaSelect disable if used (do not hide)
            let selectedVal = parseInt(moedaSelect.value || 0, 10);
            Array.from(moedaSelect.options).forEach(opt => {
                const val = parseInt(opt.value, 10);
                if (usadas.includes(val)) {
                    opt.disabled = true;
                } else {
                    opt.disabled = false;
                }
            });

            // If currently selected moeda is now disabled, clear selection
            if (selectedVal && usadas.includes(selectedVal)) {
                moedaSelect.value = '';
            }

            if (usadas.length > 0) {
                aviso.textContent = 'Observação: o cliente já possui contas deste tipo nas moedas destacadas — essas opções foram desabilitadas.';
            } else {
                aviso.textContent = '';
            }
        }

        tipoSelect.addEventListener('change', refreshMoedas);
        // run once on load
        refreshMoedas();
    });
    </script>
    @endpush
@endif
@endsection

    @if(!isset($cliente))
        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function(){
            // Create and show BI modal on page load when creating a new account without selecting a client
            const modalHtml = `
            <div class="modal fade" id="biModal" tabindex="-1" role="dialog" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Procurar Cliente por BI</h5>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                      <label for="bi_input">Digite o BI do Cliente</label>
                      <input type="text" id="bi_input" class="form-control" />
                      <small id="bi_feedback" class="form-text text-danger d-none"></small>
                    </div>
                    <div id="bi_spinner" class="text-center d-none">
                      <div class="spinner-border" role="status"><span class="sr-only">Carregando...</span></div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" id="bi_search_btn" class="btn btn-primary">Procurar</button>
                    <a href="{{ route('admin.clientes.create') }}" id="bi_register_btn" class="btn btn-secondary d-none">Cadastrar Cliente</a>
                    <button type="button" id="bi_close_btn" class="btn btn-light">Fechar</button>
                  </div>
                </div>
              </div>
            </div>`;

            const wrapper = document.createElement('div');
            wrapper.innerHTML = modalHtml;
            document.body.appendChild(wrapper);

            const biModalEl = document.getElementById('biModal');
            // Use Bootstrap's modal if available
            let biModal = null;
            if (typeof bootstrap !== 'undefined') {
                biModal = new bootstrap.Modal(biModalEl, { backdrop: 'static', keyboard: false });
                biModal.show();
            } else {
                // fallback: show the modal element
                biModalEl.style.display = 'block';
            }

            const biInput = document.getElementById('bi_input');
            const biBtn = document.getElementById('bi_search_btn');
            const biClose = document.getElementById('bi_close_btn');
            const biFeedback = document.getElementById('bi_feedback');
            const biSpinner = document.getElementById('bi_spinner');
            const biRegister = document.getElementById('bi_register_btn');

            function showSpinner(show){
                if (show) {
                    biSpinner.classList.remove('d-none');
                    biBtn.disabled = true;
                } else {
                    biSpinner.classList.add('d-none');
                    biBtn.disabled = false;
                }
            }

            biBtn.addEventListener('click', function(){
                biFeedback.classList.add('d-none');
                const bi = biInput.value.trim();
                if (!bi) {
                    biFeedback.textContent = 'Por favor informe o BI.';
                    biFeedback.classList.remove('d-none');
                    return;
                }

                showSpinner(true);

                // keep spinner for 2 seconds minimum
                const start = Date.now();

                fetch("{{ route('admin.contas.findByBi') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ bi: bi }),
                    credentials: 'same-origin',
                    redirect: 'manual'
                }).then(async response => {
                    const elapsed = Date.now() - start;
                    const remaining = Math.max(0, 2000 - elapsed);

                    // examine content-type to decide how to parse
                    const contentType = (response.headers.get('content-type') || '').toLowerCase();
                    let json = {};
                    if (contentType.includes('application/json')) {
                        json = await response.json().catch(() => ({}));
                    }

                    setTimeout(function(){
                        showSpinner(false);

                        // If server returned a redirect status (non-AJAX), do NOT follow it — show message instead
                        if (response.status >= 300 && response.status < 400) {
                            biFeedback.textContent = 'Cliente não encontrado. Cadastre primeiro.';
                            biFeedback.classList.remove('d-none');
                            biRegister.classList.remove('d-none');
                            return;
                        }

                        // If not JSON, show generic message
                        if (!contentType.includes('application/json')) {
                            biFeedback.textContent = json.message || 'Resposta inesperada do servidor.';
                            biFeedback.classList.remove('d-none');
                            return;
                        }

                        // If server returned an error JSON
                        if (!response.ok) {
                            biFeedback.textContent = json.error || 'Cliente não encontrado.';
                            biFeedback.classList.remove('d-none');
                            if (json.action === 'register') biRegister.classList.remove('d-none');
                            return;
                        }

                        // Success JSON with cliente info
                        if (json.cliente_id) {
                            window.location.href = '/admin/contas/create-for-client/' + json.cliente_id;
                            return;
                        }

                        biFeedback.textContent = 'Resposta inválida do servidor.';
                        biFeedback.classList.remove('d-none');
                    }, remaining);
                }).catch(err => {
                    showSpinner(false);
                    biFeedback.textContent = 'Erro de conexão ao procurar BI.';
                    biFeedback.classList.remove('d-none');
                    console.error(err);
                });
            });

            biClose.addEventListener('click', function(){
                // If user closes without selecting, just hide modal
                if (biModal && typeof biModal.hide === 'function') biModal.hide();
                else biModalEl.style.display = 'none';
            });
        });
        </script>
        @endpush
    @endif
