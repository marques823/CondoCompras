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

    /**
     * Visualiza o documento em nova aba
     */
    public function visualizar(Documento $documento)
    {
        // Verifica se o documento pertence à empresa do usuário
        if ($documento->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        // Verifica se o arquivo existe
        if (!Storage::disk('public')->exists($documento->caminho)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $caminhoCompleto = Storage::disk('public')->path($documento->caminho);

        return response()->file($caminhoCompleto, [
            'Content-Type' => $documento->mime_type,
            'Content-Disposition' => 'inline; filename="' . $documento->nome_original . '"',
        ]);
    }

    /**
     * Faz o download do documento
     */
    public function download(Documento $documento)
    {
        // Verifica se o documento pertence à empresa do usuário
        if ($documento->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        // Verifica se o arquivo existe
        if (!Storage::disk('public')->exists($documento->caminho)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::disk('public')->download($documento->caminho, $documento->nome_original);
    }
}
