<?php

namespace App\Http\Controllers;

use App\Models\autor;
use App\Models\editorial;
use App\Models\libro;
use App\Models\resena;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LibroController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function unLibroPorISBN()
     {
         // Desactivar temporalmente el modo estricto
         DB::statement("SET SESSION sql_mode=''");

         $libros = Libro::select('libros.*')
             ->where('disponible', true)
             ->groupBy('isbn')
             ->get();

         return view('inicio', ['libros' => $libros]);
     }



    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        $nuevoLibro = new Libro();

        $nuevoLibro->autor_id = $request->autor;
        $nuevoLibro->editorial_id = $request->editorial;
        $nuevoLibro->ano_publicacion = $request->ano_publicacion;
        $nuevoLibro->isbn = $request->isbn;
        $nuevoLibro->titulo = $request->titulo;
        $nuevoLibro->sinopsis = $request->sinopsis;
        $nuevoLibro->precio = $request->precio;
        $path = $request->file('imagen')->store('libro', 'public');
        $nuevoLibro->imagen = 'storage/'.$path;
        $nuevoLibro->disponible = true;

        $nuevoLibro->save();
        return redirect('dashboard');
    }

    public function getLibro($id)
    {
        $libro = Libro::findOrFail($id);
        return response()->json($libro);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, libro $libro)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(libro $libro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $libro = Libro::findOrFail($id);
        $libro->titulo = $request->input('titulo');
        $libro->sinopsis = $request->input('sinopsis');
        $libro->ano_publicacion = $request->input('ano_publicacion');
        $libro->autor_id = $request->input('autor');
        //$libro->genero_id = $request->input('genero');
        $libro->editorial_id = $request->input('editorial');
        $libro->isbn = $request->input('isbn');
        $libro->precio = $request->input('precio');
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('libro', 'public');
            $libro->imagen = 'storage/'.$path;
        }
        $libro->save();
        return redirect('dashboard');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Intenta eliminar el libro
            Libro::findOrFail($id)->delete();

            // Si no hay excepción, redirige o muestra un mensaje de éxito
            return redirect()->back()->with('success', 'Libro eliminado correctamente');
        } catch (QueryException $e) {
            // Captura la excepción de integridad referencial
            return redirect()->back()->with('error', 'No se puede eliminar el libro. Tiene relaciones con otras tablas.');
        }
    }

    public function infoLibro(Request $request)
    {

        $libro = Libro::where('isbn', $request->isbn)->first();
        $autor = Autor::find($libro->autor_id);
        $editorial = Editorial::find($libro->editorial_id);
        $reseñas = resena::where('isbn', $libro->isbn)->get();
        $user = Auth::user();
        return view('infoLibro', ['libro' => $libro, 'autor' => $autor, 'editorial' => $editorial, 'reseñas' => $reseñas, 'user' => $user]);
    }
}
