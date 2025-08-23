@php
use Carbon\Carbon;
use Illuminate\Support\Str;
@endphp

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Transações da conta: {{ $conta->numero_conta }} - {{ $conta->cliente->nome ?? 'N/A' }}</h3>
    </div>
    <div class="card-body">
        @if(empty($transacoes) || count($transacoes) == 0)
            <p>Nenhuma transação encontrada para esta conta.</p>
        @else
            <h5 class="mt-3">{{ \Carbon\Carbon::parse(($currentYm ?? $transacoes->first()->created_at->format('Y-m')) . '-01')->format('F Y') }}</h5>
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
                    @foreach($transacoes as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $t->tipoTransacao->nome ?? 'N/A' }}</td>
                            <td>{{ $t->valor > 0 ? '+' : '' }}{{ number_format($t->valor,2,',','.') }} AOA</td>
                            <td>{{ $t->statusTransacao->nome ?? 'N/A' }}</td>
                            <td>{{ Str::limit($t->descricao ?? '-', 80) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @if(isset($monthsPaginator) && $monthsPaginator)
                <nav aria-label="Month pagination">
                    <ul class="pagination">
                        {{-- render simple prev/next and pages --}}
                        @if($monthsPaginator->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="?page={{ $monthsPaginator->currentPage() - 1 }}">&laquo;</a></li>
                        @endif

                        @foreach($monthsPaginator->items() as $idx => $ym)
                            @php $pageNum = $monthsPaginator->currentPage() - ($idx); @endphp
                            <li class="page-item {{ $ym == ($currentYm ?? '') ? 'active' : '' }}">
                                <a class="page-link" href="?page={{ $monthsPaginator->currentPage() - $idx }}">{{ \Carbon\Carbon::parse($ym . '-01')->format('M Y') }}</a>
                            </li>
                        @endforeach

                        @if($monthsPaginator->currentPage() >= $monthsPaginator->lastPage())
                            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="?page={{ $monthsPaginator->currentPage() + 1 }}">&raquo;</a></li>
                        @endif
                    </ul>
                </nav>
            @endif
        @endif
    </div>
</div>
