<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

abstract class BaseController extends Controller
{
    /**
     * Redirect back with preserved query parameters
     */
    protected function redirectBackWithParams(Request $request, string $message = null, string $type = 'success')
    {
        $redirect = redirect()->back();

        // Preserve query parameters
        if ($request->hasAny(['search', 'filter', 'page', 'sort', 'direction'])) {
            $redirect = redirect()->back()->withQueryString();
        }

        if ($message) {
            $redirect = $redirect->with($type, $message);
        }

        return $redirect;
    }

    /**
     * Redirect to route with preserved query parameters
     */
    protected function redirectToRouteWithParams(string $route, Request $request, string $message = null, string $type = 'success')
    {
        $redirect = redirect()->route($route);

        // Preserve query parameters
        if ($request->hasAny(['search', 'filter', 'page', 'sort', 'direction'])) {
            $redirect = $redirect->withQueryString();
        }

        if ($message) {
            $redirect = $redirect->with($type, $message);
        }

        return $redirect;
    }

    /**
     * Get preserved query parameters for forms
     */
    protected function getPreservedParams(Request $request): array
    {
        return $request->only(['search', 'filter', 'page', 'sort', 'direction']);
    }
}
