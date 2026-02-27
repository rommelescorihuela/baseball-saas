<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Super admins always bypass
        if ($user && $user->hasRole('super_admin')) {
            return $next($request);
        }

        $subscription = $user?->currentSubscription;

        if (!$subscription || !$subscription->isActive()) {
            abort(402, 'Suscripción Comercial Expirada o Inactiva. Para recuperar el acceso a su Base de Datos Deportiva (DiamondOS), por favor procese el depósito o pago móvil correspondiente a su tarifa y envíe el comprobante al equipo de Soporte Técnico.');
        }

        return $next($request);
    }
}
