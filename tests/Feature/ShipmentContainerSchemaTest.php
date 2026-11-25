<?php

use Illuminate\Support\Facades\Schema;

test('container size accepts missing optional shipment data', function () {
    $containerSize = collect(Schema::getColumns('shipment_containers'))
        ->firstWhere('name', 'container_size');

    expect($containerSize)->not->toBeNull()
        ->and($containerSize['nullable'])->toBeTrue();
});
