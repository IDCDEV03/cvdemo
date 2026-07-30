<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use Illuminate\Support\Facades\File;

class DocumentController extends Controller
{
     public function __construct()
    {
        $this->middleware(['auth', 'role:user']);
    }

    public function doc_list()
    {
        return view('pages.user.VehiclesDocList');
    }
}
