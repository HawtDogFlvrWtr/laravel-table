<div wire:key="filter-{{ Str::slug($filter->identifier) }}">
    <select wire:model="selectedFilters.{{ $filter->identifier }}"
            @class(['form-select', $filter->class => $filter->class])
            {!! $filter->multiple ? 'multiple' : null !!}
            aria-label="{{ $filter->label }}">
        <option wire:key="filter-option-{{ Str::slug($filter->identifier) }}-placeholder" value="" selected>{{ $filter->label }}</option>
        @foreach($filter->options as $optionValue => $optionLabel)
            <option wire:key="filter-option-{{ Str::slug($filter->identifier) }}-{{ Str::slug($optionValue) }}" value="{{ $optionValue }}">{{ $optionLabel }}</option>
        @endforeach
    </select>
</div>
