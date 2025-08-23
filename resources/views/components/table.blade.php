@props([
    'id' => null,
    'striped' => false,
    'hover' => true,
    'bordered' => true,
    'small' => false,
    'responsive' => true,
    'headers' => [],
])

<div class="table-responsive-sm">
    <table class="table @if($striped) table-striped @endif @if($hover) table-hover @endif @if($bordered) table-bordered @endif @if($small) table-sm @endif" @if($id) id="{{ $id }}" @endif>
        @if(count($headers) > 0)
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>

@push('scripts')
@if($id)
<script>
    $(function () {
        $('#{{ $id }}').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": {{ $responsive ? 'true' : 'false' }},
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
            }
        });
    });
</script>
@endif
@endpush
