<?php

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Identifiants invalides.'], 401);
    }

    $token = $user->createToken('mobile')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => $user,
    ]);
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', fn (Request $request) => $request->user());

    Route::get('/products', function () {
        return Product::query()
            ->where('is_active', true)
            ->with(['category', 'unit', 'stock'])
            ->orderBy('name')
            ->paginate(50);
    });

    Route::get('/customers', function () {
        return Customer::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate(50);
    });

    Route::get('/sales', function () {
        return Sale::query()
            ->with(['items.product', 'customer', 'user'])
            ->latest('sold_at')
            ->paginate(30);
    });

    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté.']);
    });
});
