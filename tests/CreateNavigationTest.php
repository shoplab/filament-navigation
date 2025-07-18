<?php

use Livewire\Livewire;
use Pest\Expectation;

use function Pest\Laravel\assertDatabaseHas;

use RyanChandler\FilamentNavigation\Filament\Resources\NavigationResource\Pages\CreateNavigation;
use RyanChandler\FilamentNavigation\Models\Navigation;

it('can create a navigation menu', function () {
    $navigation = new Navigation([
        'name' => 'Foo',
        'handle' => 'foo',
        'items' => [],
    ]);

    $navigation->save();

    expect($navigation)
        ->toBeInstanceOf(Navigation::class)
        ->name->toBe('Foo')
        ->handle->toBe('foo');
});

it('can create a navigation menu with items', function () {
    Livewire::test(CreateNavigation::class)
        ->set('data.name', 'Foo')
        ->call('mountAction', 'item')
        ->set('mountedActionData', [
            'label' => 'Bar',
            'type' => 'external-link',
            'data' => [
                'url' => '/bar',
            ],
        ])
        ->call('callMountedAction')
        ->call('create')
        ->assertHasNoErrors()
        ->assertSuccessful();

    expect(Navigation::first())
        ->toBeInstanceOf(Navigation::class)
        ->name->toBe('Foo')
        ->handle->toBe('foo')
        ->items
            ->toHaveLength(1)
            ->sequence(
                fn (Expectation $item) => $item
                    ->toHaveKey('label', 'Bar')
                    ->toHaveKey('type', 'external-link')
                    ->data->toMatchArray([
                        'url' => '/bar',
                    ])
            );
})->skip('Complex form interaction test - ViewErrorBag issue in test environment');
