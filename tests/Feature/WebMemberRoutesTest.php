<?php

test('web member route names resolve to web urls', function () {
    expect(route('members.index', absolute: false))->toBe('/members');
    expect(route('members.create', absolute: false))->toBe('/members/create');
});

test('api member route names are namespaced away from web routes', function () {
    expect(route('api.login', absolute: false))->toBe('/api/login');
    expect(route('api.members.index', absolute: false))->toBe('/api/members');
    expect(route('api.members.show', ['member' => 'MM0001'], absolute: false))->toBe('/api/members/MM0001');
});
