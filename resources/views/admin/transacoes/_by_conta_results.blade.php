@php
use Carbon\Carbon;
use Illuminate\Support\Str;
@endphp

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Transações da conta: {{ $conta->numero_conta }} - {{ $conta->cliente->nome ?? 'N/A' }}</h3>
    </div>
    <div class="card-body">
        @if(!isset($transacoesGrouped) || $transacoesGrouped->isEmpty())
            <p>Nenhuma transação encontrada para esta conta.</p>
        @else
            {{-- Aggregate totals for the entire account --}}
            @php
                $all = collect();
                foreach($transacoesGrouped as $items) $all = $all->concat($items);
                $grandTotal = $all->sum('valor');
                $grandCredits = $all->where('valor','>',0)->sum('valor');
                $grandDebits = $all->where('valor','<',0)->sum('valor');
            @endphp
            <div class="mb-3 d-flex align-items-center gap-3">
                <div><strong>Total geral:</strong> {{ number_format($grandTotal,2,',','.') }} AOA</div>
                <div class="text-success">Créditos: {{ number_format($grandCredits,2,',','.') }} AOA</div>
                <div class="text-danger">Débitos: {{ number_format(abs($grandDebits),2,',','.') }} AOA</div>
                <div class="ml-auto">
                    <button id="btn_expand_all" class="btn btn-sm btn-outline-primary">Expandir tudo</button>
                    <button id="btn_collapse_all" class="btn btn-sm btn-outline-secondary">Colapsar tudo</button>
                </div>
            </div>

            <div id="porContaAccordion">
                @foreach($transacoesGrouped as $ym => $items)
                    @php
                        $label = \Carbon\Carbon::parse($ym . '-01')->format('F Y');
                        $count = $items->count();
                        $total = $items->sum('valor');
                        $credits = $items->where('valor', '>', 0)->sum('valor');
                        $debits = $items->where('valor', '<', 0)->sum('valor');
                        // first loop -> most recent month (collection was ordered desc)
                        $open = $loop->first ? true : false;
                    @endphp
                    <div class="card mb-2">
                        <div class="card-header p-2" id="heading-{{ $ym }}">
                            <h5 class="mb-0 d-flex align-items-center">
                                <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#collapse-{{ $ym }}" aria-expanded="{{ $open ? 'true' : 'false' }}" aria-controls="collapse-{{ $ym }}">
                                    {{ $label }}
                                </button>
                                <div class="ml-3">
                                    <small class="text-muted">{{ $count }} txn</small>
                                </div>
                                <div class="ml-auto text-right">
                                    <div><strong>Total:</strong> {{ number_format($total,2,',','.') }} AOA</div>
                                    <div class="text-success">Créditos: {{ number_format($credits,2,',','.') }} AOA</div>
                                    <div class="text-danger">Débitos: {{ number_format(abs($debits),2,',','.') }} AOA</div>
                                </div>
                                <div class="ml-2">
                                    <a href="{{ route('transacoes.exportByContaMonthCsv', ['conta' => $conta->id, 'ym' => $ym]) }}" class="btn btn-sm btn-outline-success" target="_blank" title="Exportar CSV">CSV</a>
                                    <button class="btn btn-sm btn-outline-danger btn-export-pdf" data-ym="{{ $ym }}">PDF</button>
                                </div>
                            </h5>
                        </div>

                        <div id="collapse-{{ $ym }}" class="collapse {{ $open ? 'show' : '' }}" aria-labelledby="heading-{{ $ym }}" data-parent="#porContaAccordion">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Data / Hora</th>
                                                <th>Tipo</th>
                                                <th>Valor</th>
                                                <th>Status</th>
                                                <th>Descrição</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($items as $t)
                                            <tr>
                                                <td>{{ $t->id }}</td>
                                                <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $t->tipoTransacao->nome ?? 'N/A' }}</td>
                                                <td>{{ $t->valor > 0 ? '+' : '' }}{{ number_format($t->valor,2,',','.') }} AOA</td>
                                                <td>{{ $t->statusTransacao->nome ?? 'N/A' }}</td>
                                                <td>{{ Str::limit($t->descricao ?? '-', 120) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const accordion = document.getElementById('porContaAccordion');
    const btnExpand = document.getElementById('btn_expand_all');
    const btnCollapse = document.getElementById('btn_collapse_all');

    if(btnExpand) btnExpand.addEventListener('click', function(){
        accordion.querySelectorAll('.collapse').forEach(c => c.classList.add('show'));
    });
    if(btnCollapse) btnCollapse.addEventListener('click', function(){
        accordion.querySelectorAll('.collapse').forEach(c => c.classList.remove('show'));
    });

    // PDF export per month using pdfMake if available; otherwise warn
    document.querySelectorAll('.btn-export-pdf').forEach(btn => {
        btn.addEventListener('click', function(){
            const ym = btn.dataset.ym;
            // gather rows for this month
            const table = document.querySelector('#collapse-' + ym + ' table');
            if(!table){ alert('Tabela não encontrada para o mês.'); return; }
            const rows = Array.from(table.querySelectorAll('tbody tr'));
            const body = [['ID','Data/Hora','Tipo','Valor','Status','Descrição']];
            rows.forEach(r => {
                const cols = Array.from(r.querySelectorAll('td')).map(td => td.textContent.trim());
                body.push(cols);
            });

            if(window.pdfMake){
                const docDefinition = {
                    content: [
                        { text: 'Transações - {{ $conta->numero_conta }} - ' + ym, style: 'header' },
                        { table: { headerRows: 1, widths: ['auto','auto','*','auto','auto','*'], body: body } }
                    ],
                    styles: { header: { fontSize: 14, bold: true, margin: [0,0,0,10] } }
                };
                pdfMake.createPdf(docDefinition).download('transacoes_{{ $conta->numero_conta }}_' + ym + '.pdf');
            } else {
                alert('PDF export requires pdfMake (already included in template).');
            }
        });
    });
});
</script>
@endpush
