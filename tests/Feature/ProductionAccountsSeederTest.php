<?php

use App\Models\User;
use Database\Seeders\ProductionAccountsSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
});

test('production accounts seeder creates 3 accounts founder tatausaha and keuangan', function () {
    $this->artisan('db:seed', ['--class' => 'ProductionAccountsSeeder']);

    // 1. Founder
    $founder = User::where('username', 'marwansyah')->first();
    expect($founder)->not->toBeNull();
    expect($founder->role->nama)->toEqual('super_admin');
    expect(Hash::check('password123', $founder->password))->toBeTrue();
    expect($founder->status)->toEqual('aktif');

    // 2. Tata Usaha
    $tatausaha = User::where('username', 'tatausaha')->first();
    expect($tatausaha)->not->toBeNull();
    expect($tatausaha->role->nama)->toEqual('tata_usaha');
    expect(Hash::check('tatausaha123', $tatausaha->password))->toBeTrue();
    expect($tatausaha->status)->toEqual('aktif');

    // 3. Keuangan
    $keuangan = User::where('username', 'keuangan')->first();
    expect($keuangan)->not->toBeNull();
    expect($keuangan->role->nama)->toEqual('finance');
    expect(Hash::check('finance123', $keuangan->password))->toBeTrue();
    expect($keuangan->status)->toEqual('aktif');
});
