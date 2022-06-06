<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\Result;
use Okipa\LaravelTable\Table;
use Tests\Models\User;
use Tests\TestCase;

class TableResultsTest extends TestCase
{
    /** @test */
    public function it_can_set_column_titles(): void
    {
        User::factory()->count(6)->state(new Sequence(
            ['email_verified_at' => Date::now(), 'active' => true],
            ['email_verified_at' => null, 'active' => false]
        ))->create();
        $config = new class extends AbstractTableConfiguration {
            protected function table(): Table
            {
                return Table::make()->model(User::class)->numberOfRowsPerPageOptions([2]);
            }

            protected function columns(): array
            {
                return [
                    Column::make('Id'),
                ];
            }

            protected function results(): array
            {
                return [
                    Result::make('Total of users with unverified email')
                        ->value(static fn(Builder $totalRowsQuery) => $totalRowsQuery
                            ->whereNull('email_verified_at')
                            ->count()),
                    Result::make('Displayed inactive users')
                        ->value(static fn(
                            Builder $totalRowsQuery,
                            Collection $displayedRowsCollection
                        ) => $displayedRowsCollection->where('active', false)->count()),
                ];
            }
        };
        Livewire::test(\Okipa\LaravelTable\Livewire\Table::class, ['config' => $config::class])
            ->call('init')
            ->assertSeeHtmlInOrder([
                '<tfoot class="table-light">',
                '<tr wire:key="result-total-of-users-with-unverified-email" class="border-bottom">',
                '<td class="align-middle fw-bold">',
                '<div class="d-flex flex-wrap justify-content-between">',
                '<div>Total of users with unverified email</div>',
                '<div class="ms-3">3</div>',
                '</div>',
                '</td>',
                '</tr>',
                '<tr wire:key="result-displayed-inactive-users" class="border-bottom">',
                '<td class="align-middle fw-bold">',
                '<div class="d-flex flex-wrap justify-content-between">',
                '<div>Displayed inactive users</div>',
                '<div class="ms-3">1</div>',
                '</div>',
                '</td>',
                '</tr>',
                '</tfoot>',
            ]);
    }
}