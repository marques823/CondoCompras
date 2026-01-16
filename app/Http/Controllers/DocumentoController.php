<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::daEmpresa(Auth::user()->empresa_id)
            ->with(['condominio', 'demanda', 'orcamento', 'prestador'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('documentos.index', compact('documentos'));
    }
}
