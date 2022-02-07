<?php

namespace Okipa\LaravelTable\Tests\Unit\Bootstrap5;

use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Table;
use Okipa\LaravelTable\Tests\Models\User;
use Okipa\LaravelTable\Tests\TestCase;

class NavigationStatusTest extends TestCase
{
    /** @test */
    public function it_can_display_navigation_status_with_no_result(): void
    {
        $config = new class extends AbstractTableConfiguration {
            protected function table(Table $table): void
            {
                $table->model(User::class);
            }

            protected function columns(Table $table): void
            {
                $table->column('id');
            }
        };
        Livewire::test(\Okipa\LaravelTable\Livewire\Table::class, ['config' => $config::class])
            ->call('init')
            ->assertSet('paginationTheme', 'bootstrap')
            ->assertSeeHtmlInOrder([
                '<tfoot>',
                __('Showing results <b>:start</b> to <b>:stop</b> on <b>:total</b>', [
                    'start' => 0,
                    'stop' => 0,
                    'total' => 0,
                ]),
            ]);
    }

    /** @test */
    public function it_can_display_navigation_status_with_results(): void
    {
        Config::set('laravel-table.number_of_rows_per_page', 10);
        User::factory()->count(15)->create();
        $config = new class extends AbstractTableConfiguration {
            protected function table(Table $table): void
            {
                $table->model(User::class);
            }

            protected function columns(Table $table): void
            {
                $table->column('id');
            }
        };
        Livewire::test(\Okipa\LaravelTable\Livewire\Table::class, ['config' => $config::class])
            ->call('init')
            ->assertSet('paginationTheme', 'bootstrap')
            ->assertSeeHtmlInOrder([
                '<tfoot>',
                __('Showing results <b>:start</b> to <b>:stop</b> on <b>:total</b>', [
                    'start' => 1,
                    'stop' => 10,
                    'total' => 15,
                ]),
            ]);
    }

    /** @test */
    public function it_can_display_navigation_status_on_last_page_with_results(): void
    {
        Config::set('laravel-table.number_of_rows_per_page', 10);
        User::factory()->count(15)->create();
        $config = new class extends AbstractTableConfiguration {
            protected function table(Table $table): void
            {
                $table->model(User::class);
            }

            protected function columns(Table $table): void
            {
                $table->column('id');
            }
        };
        Livewire::test(\Okipa\LaravelTable\Livewire\Table::class, ['config' => $config::class])
            ->call('setPage', 2)
            ->call('init')
            ->assertSet('paginationTheme', 'bootstrap')
            ->assertSeeHtmlInOrder([
                '<tfoot>',
                __('Showing results <b>:start</b> to <b>:stop</b> on <b>:total</b>', [
                    'start' => 11,
                    'stop' => 15,
                    'total' => 15,
                ]),
            ]);
    }
}