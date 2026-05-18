@props([
    'canvasId',
    'hiddenInputId',
    'hiddenInputName',
    'formId' => null,
    'label' => 'Ou signer ci-dessous',
    'width' => 480,
    'height' => 160,
    'showLoadButton' => true,
])

<div
    data-cofina-signature-pad
    data-canvas-id="{{ $canvasId }}"
    data-hidden-id="{{ $hiddenInputId }}"
    @if($formId) data-form-id="{{ $formId }}" @endif
    {{ $attributes->merge(['class' => '']) }}
>
    @if($label)
        <label class="block text-sm text-gray-600 mb-1">{{ $label }}</label>
    @endif
    <div class="border border-gray-300 rounded-lg bg-white overflow-hidden max-w-lg">
        <canvas
            id="{{ $canvasId }}"
            width="{{ $width }}"
            height="{{ $height }}"
            class="w-full touch-none cursor-crosshair"
            style="max-height:{{ $height }}px;"
        ></canvas>
    </div>
    <input type="hidden" name="{{ $hiddenInputName }}" id="{{ $hiddenInputId }}" value="">
    <div class="flex flex-wrap gap-2 mt-2">
        <button
            type="button"
            data-signature-clear
            class="px-3 py-1.5 text-xs bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800"
        >
            Effacer
        </button>
        @if($showLoadButton)
            <button
                type="button"
                data-signature-load-profile
                class="px-3 py-1.5 text-xs bg-indigo-100 hover:bg-indigo-200 text-indigo-900 rounded-lg font-medium"
            >
                Charger ma signature
            </button>
        @endif
    </div>
</div>
