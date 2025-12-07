<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Check if the authenticated user has admin access.
     */
    protected function checkAdminAccess()
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access. Admin privileges required.');
        }
    }

    /**
     * Return admin view with proper layout.
     */
    protected function adminView($view, $data = [])
    {
        $data['admin_layout'] = true;
        return view($view, $data);
    }
}