<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OpdGroup;
use Illuminate\Http\Request;

class OpdGroupController extends Controller
{
    public function index()
    {
        $groups = OpdGroup::active()->get(['id', 'name', 'group_id', 'is_active']);

        return response()->json([
            'success' => true,
            'groups' => $groups,
        ]);
    }
}
