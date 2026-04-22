<?php

test('web member route names resolve to web urls', function () {
    expect(route('members.index', absolute: false))->toBe('/members');
    expect(route('members.create', absolute: false))->toBe('/members/create');
    expect(route('members.print.all', absolute: false))->toBe('/members/print');
    expect(route('members.print.single', ['member' => 'GLS-S-001'], absolute: false))->toBe('/members/GLS-S-001/print');
});

test('api member route names are namespaced away from web routes', function () {
    expect(route('api.members.index', absolute: false))->toBe('/api/members');
    expect(route('api.members.show', ['member' => 'GLS-S-001'], absolute: false))->toBe('/api/members/GLS-S-001');
});
