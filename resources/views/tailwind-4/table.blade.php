<div wire:init="init">
    @if($initialized)
        @if($orderColumn)
            <div class="alert alert-info" role="alert">
                {{ __('You can rearrange the order of the items in this list using a drag and drop action.') }}
            </div>
        @endif
        <div class="">
            <table class="table-auto">
                {{-- Table header--}}
                <thead>
                    {{-- Filters --}}
                    @if($filtersArray)
                        <tr>
                            <td class="px-0 pb-0"{!! $columnsCount > 1 ? ' colspan="' . $columnsCount . '"' : null !!}>
                                <div class="flex flex-wrap items-center justify-end -mt-2">
                                    <div class="dark:text-gray-300 mt-2">
                                        {!! config('laravel-table.icon.filter') !!}
                                    </div>
                                    @foreach($filtersArray as $filterArray)
                                        @unless($resetFilters)
                                            <div wire:ignore>
                                        @endif
                                            {!! Okipa\LaravelTable\Abstracts\AbstractFilter::make($filterArray)->render() !!}
                                        @unless($resetFilters)
                                            </div>
                                        @endif
                                    @endforeach
                                    @if(collect($this->selectedFilters)->filter(fn(mixed $filter) => isset($filter) && $filter !== '' && $filter !== [])->isNotEmpty())
                                        <a wire:click.prevent="resetFilters()"
                                           class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 ms-3 mt-2"
                                           title="{{ __('Reset filters') }}">
                                            {!! config('laravel-table.icon.reset') !!}
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                    {{-- Search/Number of rows per page/Head action --}}
                    <tr>
                        <td class="px-0"{!! $columnsCount > 1 ? ' colspan="' . $columnsCount . '"' : null !!}>
                            <div class="flex flex-col xl:flex-row">
                                {{-- Search --}}
                                <div class="flex-grow">
                                    @if($searchableLabels)
                                        <div class="flex-grow pe-xl-3 py-1">
                                            <form wire:submit.prevent="$refresh">
                                                <div class="input-group">
                                                    <span id="search-for-rows" class="input-group-text">
                                                        {!! config('laravel-table.icon.search') !!}
                                                    </span>
                                                    <input wire:model="searchBy"
                                                           class="form-input"
                                                           placeholder="{{ __('Search by:') }} {{ $searchableLabels }}"
                                                           aria-label="{{ __('Search by:') }} {{ $searchableLabels }}"
                                                           aria-describedby="search-for-rows">
                                                    <span class="input-group-text">
                                                        <button class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 p-0"
                                                                type="submit"
                                                                title="{{ __('Search by:') }} {{ $searchableLabels }}">
                                                            {!! config('laravel-table.icon.validate') !!}
                                                        </button>
                                                    </span>
                                                    @if($searchBy)
                                                        <span class="input-group-text">
                                                            <a wire:click.prevent="$set('searchBy', ''), $refresh"
                                                               class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 p-0"
                                                               title="{{ __('Reset research') }}">
                                                                {!! config('laravel-table.icon.reset') !!}
                                                            </a>
                                                        </span>
                                                    @endif
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex justify-between">
                                    {{-- Number of rows per page --}}
                                    @if($numberOfRowsPerPageChoiceEnabled)
                                        <div wire:ignore @class(['px-xl-3' => $headActionArray, 'ps-xl-3' => ! $headActionArray, 'py-1'])>
                                            <div class="input-group">
                                                <span id="rows-number-per-page-icon" class="form-input text-gray-700">
                                                    {!! config('laravel-table.icon.rows_number') !!}
                                                </span>
                                                <select wire:change="changeNumberOfRowsPerPage($event.target.value)" class="form-select block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {!! (new \Illuminate\View\ComponentAttributeBag())->merge([
                                                    'placeholder' => __('Number of rows per page'),
                                                    'aria-label' => __('Number of rows per page'),
                                                    'aria-describedby' => 'rows-number-per-page-icon',
                                                    ...config('laravel-table.html_select_components_attributes'),
                                                ])->toHtml() !!}>
                                                    <option wire:key="rows-number-per-page-option-placeholder" value="" disabled>{{ __('Number of rows per page') }}</option>
                                                    @foreach($numberOfRowsPerPageOptions as $numberOfRowsPerPageOption)
                                                        <option wire:key="rows-number-per-page-option-{{ $numberOfRowsPerPageOption }}" value="{{ $numberOfRowsPerPageOption }}"{{ $numberOfRowsPerPageOption === $numberOfRowsPerPage ? ' selected' : null}}>
                                                            {{ $numberOfRowsPerPageOption }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    {{-- Head action --}}
                                    @if($headActionArray)
                                        <div class="flex items-center ps-3 py-1">
                                            {{ Okipa\LaravelTable\Abstracts\AbstractHeadAction::make($headActionArray)->render() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    {{-- Column headings --}}
                    <tr class="table-light border-top border-bottom">
                        {{-- Bulk actions --}}
                        @if($tableBulkActionsArray)
                            <th wire:key="bulk-actions" class="items-center" scope="col">
                                <div class="flex items-center">
                                    {{-- Bulk actions select all --}}
                                    <input wire:model.live="selectAll" class="me-1" type="checkbox" aria-label="Check all displayed lines">
                                    {{-- Bulk actions dropdown --}}
                                    <div class="dropdown" title="{{ __('Bulk Actions') }}">
                                        <a id="bulk-actions-dropdown"
                                           class=""
                                           type="button"
                                           aria-expanded="false">
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="bulk-actions-dropdown">
                                            @foreach($tableBulkActionsArray as $bulkActionArray)
                                                {{ Okipa\LaravelTable\Abstracts\AbstractBulkAction::make($bulkActionArray)->render() }}
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </th>
                        @endif
                        {{-- Sorting/Column titles --}}
                        @foreach($columns as $column)
                            <th wire:key="column-{{ Str::of($column->getAttribute())->snake('-')->slug() }}" class="items-center" scope="col">
                                @if($column->isSortable($orderColumn))
                                    @if($sortBy === $column->getAttribute())
                                        <a wire:click.prevent="sortBy('{{ $column->getAttribute() }}')"
                                           class="flex items-center"
                                           href=""
                                           title="{{ $sortDir === 'asc' ? __('Sort descending') : __('Sort ascending') }}">
                                            {!! $sortDir === 'asc'
                                                ? config('laravel-table.icon.sort_desc')
                                                : config('laravel-table.icon.sort_asc') !!}
                                            <span class="ms-2">{{ $column->getTitle() }}</span>
                                        </a>
                                    @else
                                        <a wire:click.prevent="sortBy('{{ $column->getAttribute() }}')"
                                           class="flex items-center"
                                           href=""
                                           title="{{ __('Sort ascending') }}">
                                            {!! config('laravel-table.icon.sort') !!}
                                            <span class="ms-2">{{ $column->getTitle() }}</span>
                                        </a>
                                    @endif
                                @else
                                    {{ $column->getTitle() }}
                                @endif
                            </th>
                        @endforeach
                        {{-- Row actions --}}
                        @if($tableRowActionsArray)
                            <th wire:key="row-actions" class="items-center text-end" scope="col">
                                {{ __('Actions') }}
                            </th>
                        @endif
                    </tr>
                </thead>
                {{-- Table body--}}
                <tbody{!! $orderColumn ? ' wire:sortable="reorder"' : null !!}>
                    {{-- Rows --}}
                    @forelse($rows as $model)
                        <tr wire:key="row-{{ $model->getKey() }}"{!! $orderColumn ? ' wire:sortable.item="' . $model->getKey() . '"' : null !!} @class(array_merge(Arr::get($tableRowClass, $model->laravel_table_unique_identifier, []), ['border-bottom']))>
                            {{-- Row bulk action selector --}}
                            @if($tableBulkActionsArray)
                                <td class="items-center">
                                    <input wire:model.live="selectedModelKeys" type="checkbox" value="{{ $model->getKey() }}" aria-label="Check line {{ $model->getKey() }}">
                                </td>
                            @endif
                            {{-- Row columns values --}}
                            @foreach($columns as $column)
                                @if($loop->first)
                                    <th wire:key="cell-{{ Str::of($column->getAttribute())->snake('-')->slug() }}-{{ $model->getKey() }}"{!! $orderColumn ? ' wire:sortable.handle style="cursor: move;"' : null !!} class="items-center" scope="row">
                                        {!! $orderColumn ? '<span class="me-2">' . config('laravel-table.icon.drag_drop') . '</span>' : null !!}{{ $column->getValue($model, $tableColumnActionsArray) }}
                                    </th>
                                @else
                                    <td wire:key="cell-{{ Str::of($column->getAttribute())->snake('-')->slug() }}-{{ $model->getKey() }}" class="items-center">
                                        {{ $column->getValue($model, $tableColumnActionsArray) }}
                                    </td>
                                @endif
                            @endforeach
                            {{-- Row actions --}}
                            @if($tableRowActionsArray)
                                <td class="items-center text-end">
                                    <div class="flex items-center justify-end">
                                        @if($rowActionsArray = Okipa\LaravelTable\Abstracts\AbstractRowAction::retrieve($tableRowActionsArray, $model->getKey()))
                                            @foreach($rowActionsArray as $rowActionArray)
                                                {{ Okipa\LaravelTable\Abstracts\AbstractRowAction::make($rowActionArray)->render($model) }}
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr class="border-bottom">
                            <th class="fw-normal text-center items-center p-3" scope="row"{!! $columnsCount > 1 ? ' colspan="' . $columnsCount . '"' : null !!}>
                                <span class="text-info">
                                    {!! config('laravel-table.icon.info') !!}
                                </span>
                                {{ __('No results were found.') }}
                            </th>
                        </tr>
                    @endforelse
                </tbody>
                {{-- Table footer--}}
                <tfoot class="table-light">
                    {{-- Results --}}
                    @foreach($results as $result)
                        <tr wire:key="result-{{ Str::of($result->getTitle())->snake('-')->slug() }}" class="border-bottom">
                            <td class="items-center fw-bold"{!! $columnsCount > 1 ? ' colspan="' . $columnsCount . '"' : null !!}>
                                <div class="flex flex-wrap justify-between">
                                    <div class="px-2 py-1">{{ $result->getTitle() }}</div>
                                    <div class="px-2 py-1">{{ $result->getValue() }}</div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="items-center"{!! $columnsCount > 1 ? ' colspan="' . $columnsCount . '"' : null !!}>
                            <div class="flex flex-wrap justify-between">
                                <div class="flex items-center p-2">
                                    <div wire:key="navigation-status">{!! $navigationStatus !!}</div>
                                </div>
                                <div class="flex items-center mb-n3 p-2">
                                    {!! $rows->links() !!}
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="flex items-center py-3">
            <div class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin" role="status">
                <span class="visually-hidden">{{ __('Loading in progress...') }}</span>
            </div>
            {{ __('Loading in progress...') }}
        </div>
    @endif
</div>
