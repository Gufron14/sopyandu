<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnthropometricStandard;
use Illuminate\Http\Request;

class AnthropometricStandardController extends Controller
{
    public function index()
    {
        // Get all standards and organize them by type and gender
        $standards = AnthropometricStandard::all();

        $organized = [
            'BB/U' => [
                'L' => $standards->where('type', 'BB/U')->where('gender', 'L')->values(),
                'P' => $standards->where('type', 'BB/U')->where('gender', 'P')->values(),
            ],
            'TB/U' => [
                'L' => $standards->where('type', 'TB/U')->where('gender', 'L')->values(),
                'P' => $standards->where('type', 'TB/U')->where('gender', 'P')->values(),
            ],
            'BB/TB' => [
                'L' => $standards->where('type', 'BB/TB')->where('gender', 'L')->values(),
                'P' => $standards->where('type', 'BB/TB')->where('gender', 'P')->values(),
            ],
            'IMT/U' => [
                'L' => $standards->where('type', 'IMT/U')->where('gender', 'L')->values(),
                'P' => $standards->where('type', 'IMT/U')->where('gender', 'P')->values(),
            ],
        ];

        return response()->json($organized);
    }
}
