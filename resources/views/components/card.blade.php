@props([
    'title' => '',
    'tools' => '',
    'footer' => '',
    'collapsed' => false,
    'removable' => false,
    'maximizable' => false,
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title || $tools)
        <div class="card-header">
            <h3 class="card-title">{{ $title }}</h3>
            <div class="card-tools">
                {{ $tools }}
                @if($maximizable)
                    <button type="button" class="btn btn-tool" data-card-widget="maximize">
                        <i class="fas fa-expand"></i>
                    </button>
                @endif
                @if($collapsed)
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                @endif
                @if($removable)
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                @endif
            </div>
        </div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
