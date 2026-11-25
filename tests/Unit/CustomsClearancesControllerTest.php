<?php

use App\Http\Controllers\CustomsClearancesController;

test('customs clearance controller resolves from its PSR-4 path', function () {
    $controllerPath = (new ReflectionClass(CustomsClearancesController::class))->getFileName();

    expect($controllerPath)->toEndWith(DIRECTORY_SEPARATOR.'CustomsClearancesController.php');
});
