<?php

namespace Okipa\LaravelTable\Tests\Unit\Bootstrap5;

use Illuminate\View\ViewException;
use Livewire\Livewire;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Table;
use Okipa\LaravelTable\Tests\Models\User;
use Okipa\LaravelTable\Tests\TestCase;

class ColumnsDeclarationTest extends TestCase
{
    /** @test */
    public function it_cant_generate_table_without_columns(): void
    {
        $config = new class extends AbstractTableConfiguration {
            protected function table(Table $table): void
            {
                $table->model(User::class);
            }

            protected function columns(Table $table): void
            {
                //
            }
        };
        $this->expectException(ViewException::class);
        $this->expectExceptionMessage('No columns are declared for ' . User::class . ' table.');
        Livewire::test(\Okipa\LaravelTable\Livewire\Table::class, ['config' => $config::class])->call('init');
    }

    /** @test */
    public function it_can_declare_columns(): void
    {
        $users = User::factory()->count(2)->create();
        $config = new class extends AbstractTableConfiguration {
            protected function table(Table $table): void
            {
                $table->model(User::class);
            }

            protected function columns(Table $table): void
            {
                $table->column('id');
                $table->column('name');
            }
        };
        Livewire::test(\Okipa\LaravelTable\Livewire\Table::class, ['config' => $config::class])
            ->call('init')
            ->assertSeeInOrder([
                'validation.attributes.id',
                'validation.attributes.name',
                $users->first()->id,
                $users->first()->name,
                $users->last()->id,
                $users->last()->name,
            ]);
    }
}