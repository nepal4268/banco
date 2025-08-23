@props([
    'id',
    'title',
    'size' => 'md', // sm, md, lg, xl
    'centered' => false,
    'scrollable' => false,
    'static' => false
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}Label" aria-hidden="true" 
    @if($static) data-backdrop="static" data-keyboard="false" @endif>
    <div class="modal-dialog modal-{{ $size }} @if($centered) modal-dialog-centered @endif @if($scrollable) modal-dialog-scrollable @endif" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if(isset($footer))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
