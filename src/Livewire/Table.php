<?php

namespace Okipa\LaravelTable\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Exceptions\InvalidTableConfiguration;

class Table extends Component
{
    use WithPagination;

    public bool $initialized = false;

    public string $config;

    public array $configParams = [];

    public \Okipa\LaravelTable\Table $table;

    public string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $searchableLabels;

    public bool $searchInProgress;

    public int $numberOfRowsPerPage;

    public string|null $sortBy;

    public bool $sortAsc;

    protected $listeners = ['search:executed' => '$refresh'];

    public function init(): void
    {
        $this->initPaginationTheme();
        $this->initialized = true;
    }

    protected function initPaginationTheme(): void
    {
        $this->paginationTheme = Str::contains(Config::get('laravel-table.ui'), 'bootstrap')
            ? 'bootstrap'
            : 'tailwind';
    }

    /**
     * @throws \Okipa\LaravelTable\Exceptions\InvalidTableConfiguration
     * @throws \Okipa\LaravelTable\Exceptions\NoColumnsDeclared
     */
    public function render(): View
    {
        $config = $this->buildConfig();

        return view('laravel-table::' . Config::get('laravel-table.ui') . '.table', $this->buildTable($config));
    }

    /** @throws \Okipa\LaravelTable\Exceptions\InvalidTableConfiguration */
    protected function buildConfig(): AbstractTableConfiguration
    {
        if (! app($this->config) instanceof AbstractTableConfiguration) {
            throw new InvalidTableConfiguration('The given ' . $this->config
                . ' table config should extend ' . AbstractTableConfiguration::class . '.');
        }

        return app($this->config, $this->configParams);
    }

    /** @throws \Okipa\LaravelTable\Exceptions\NoColumnsDeclared */
    protected function buildTable(AbstractTableConfiguration $config): array
    {
        /** @var \Okipa\LaravelTable\Table $table */
        $table = app(\Okipa\LaravelTable\Table::class);
        $config->setup($table);
        $columns = $table->getColumns();
        // Search
        $this->searchableLabels = $table->getSearchableLabels();
        // Sort
        $columnSortedByDefault = $table->getColumnSortedByDefault();
        $this->sortBy = $this->sortBy ?? $columnSortedByDefault?->getKey();
        $this->sortAsc = $this->sortAsc ?? (bool) $columnSortedByDefault?->isSortedAscByDefault();
        $sortableClosure = $this->sortBy ? $table->getColumn($this->sortBy)->getSortableClosure() : null;
        // Paginate
        $numberOfRowsPerPageOptions = $table->getNumberOfRowsPerPageOptions();
        $this->numberOfRowsPerPage = $this->numberOfRowsPerPage ?? Arr::first($numberOfRowsPerPageOptions);
        // Generate
        $table->generateRows(
            $this->search,
            $sortableClosure ?: $this->sortBy,
            $this->sortAsc,
            $this->numberOfRowsPerPage
        );

        return [
            'columns' => $columns,
            'columnsCount' => $columns->count(),
            'rows' => $table->getRows(),
            'numberOfRowsPerPageChoiceEnabled' => $table->isNumberOfRowsPerPageChoiceEnabled(),
            'numberOfRowsPerPageOptions' => $numberOfRowsPerPageOptions,
            'navigationStatus' => $table->getNavigationStatus(),
        ];
    }

    public function searchForRows(): void
    {
        $this->emitSelf('search:executed');
    }

    public function changeNumberOfRowsPerPage(int $numberOfRowsPerPage): void
    {
        $this->numberOfRowsPerPage = $numberOfRowsPerPage;
    }

    public function sortBy(string $columnKey): void
    {
        $this->sortAsc = $this->sortBy !== $columnKey || ! $this->sortAsc;
        $this->sortBy = $columnKey;
    }
}
