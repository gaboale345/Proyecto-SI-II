<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = optional($request->user()->role)->nombre_rol;

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Redirect based on role if access denied
        return match ($userRole) {
            'ADMIN' => redirect()->route('admin.dashboard')->with('error', 'No tiene permisos para acceder a esa sección.'),
            'MEDICO' => redirect()->route('medico.agenda')->with('error', 'No tiene permisos para acceder a esa sección.'),
            'CALL_CENTER' => redirect()->route('ventanilla.dashboard')->with('error', 'No tiene permisos para acceder a esa sección.'),
            default => redirect()->route('paciente.dashboard')->with('error', 'No tiene permisos para acceder a esa sección.'),
        };
    }
}
