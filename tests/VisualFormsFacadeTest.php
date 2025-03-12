<?php

use Coolsam\VisualForms\Facades\VisualForms;

it('returns the correct facade accessor', function () {
    $accessor = VisualForms::accessor();
    expect($accessor)->toEqual(\Coolsam\VisualForms\VisualForms::class);
});

it('throws an exception when calling a non-existent method', function () {
    $this->expectException(Error::class);
    VisualForms::nonExistentMethod();
});

it('calls the underlying method on the VisualForms class', function () {
    $mock = Mockery::mock(\Coolsam\VisualForms\VisualForms::class);
    $mock->shouldReceive('someMethod')->once()->andReturn('result');
    $this->app->instance(\Coolsam\VisualForms\VisualForms::class, $mock);

    $result = VisualForms::someMethod();
    expect($result)->toEqual('result');
});
