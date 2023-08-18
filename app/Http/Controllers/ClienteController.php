<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function registrar(Request $request)
    {
        try {
            $cliente = new Cliente();
            $cliente->tipo_documento = $request->input('tipo_documento');
            $cliente->numero_documento = $request->input('numero_documento');
            $cliente->nombre = $request->input('nombre');
            $cliente->direccion = $request->input('direccion');
            $cliente->save();

            return response()->json([
                'status' => true,
                'message' => 'Cliente registrado exitosamente',
                'cliente' => $cliente,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el servidor'], 500);
        }
    }

    public function show(Cliente $cliente)
    {
        //
    }

    public function edit(Cliente $cliente)
    {
        //
    }

    public function update(Request $request, Cliente $cliente)
    {
        //
    }

    public function destroy(Cliente $cliente)
    {
        //
    }
}
