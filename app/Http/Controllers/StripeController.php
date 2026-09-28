<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\User;
use Stripe\PaymentIntent;

class StripeController extends Controller
{
    public function crearSesionCheckout(Request $request)
    {
        $request->validate([
            'cantidadEstrellas' => 'required|integer',
            'precio' => 'required|numeric',
            'paqueteId' => 'required|string',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Intentamos obtener el usuario autenticado o por ID enviado desde el request si es necesario
        $user = $request->user();
        $userId = $user ? $user->id : ($request->input('user_id') ?? 1);

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'mxn',
                        'product_data' => [
                            'name' => "Paquete de " . $request->cantidadEstrellas . " Estrellas Mágicas",
                            'description' => "Recarga para Aventurilandia - Bosque Encantado",
                        ],
                        'unit_amount' => intval($request->precio * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => env('FRONTEND_URL') . '/tienda?success=true&estrellas=' . $request->cantidadEstrellas,
                'cancel_url' => env('FRONTEND_URL') . '/tienda?canceled=true',
                'metadata' => [
                    'user_id' => $userId,
                    'cantidad_estrellas' => $request->cantidadEstrellas,
                ]
            ]);

            return response()->json(['url' => $session->url]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function crearPaymentIntent(Request $request)
{
    $request->validate([
        'cantidadEstrellas' => 'required|integer',
        'precio' => 'required|numeric',
    ]);

    Stripe::setApiKey(env('STRIPE_SECRET'));

    try {
        $paymentIntent = PaymentIntent::create([
            'amount' => intval($request->precio * 100), // en centavos
            'currency' => 'mxn',
            'metadata' => [
                'cantidad_estrellas' => $request->cantidadEstrellas,
                'user_id' => $request->input('user_id', 1),
            ],
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}
