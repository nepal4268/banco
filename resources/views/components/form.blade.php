@props([
    'method' => 'POST',
    'action' => '',
    'hasFiles' => false,
])

<form method="{{ $method === 'GET' ? 'GET' : 'POST' }}" 
      action="{{ $action }}" 
      @if($hasFiles) enctype="multipart/form-data" @endif
      {{ $attributes }}>
    @csrf
    @if(!in_array($method, ['GET', 'POST']))
        @method($method)
    @endif
    
    {{ $slot }}
</form>

@push('scripts')
<script>
    $(function() {
        // Inicializa Select2 em todos os selects do formulário
        $('select').select2({
            theme: 'bootstrap4'
        });

        // Inicializa máscara de data
        $('.date').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD/MM/YYYY'
            }
        });

        // Inicializa máscara de moeda
        $('.money').inputmask('currency', {
            radixPoint: ',',
            groupSeparator: '.',
            allowMinus: false,
            prefix: 'AOA ',
            digits: 2,
            digitsOptional: false,
            rightAlign: false,
            placeholder: '0'
        });

        // Inicializa máscara de telefone
        $('.phone').inputmask('(999) 999-999-999');
    });
</script>
@endpush
