<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditorialController;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\GeneroController;
use App\Http\Controllers\ResenaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpKernel\Profiler\Profile;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [LibroController::class, 'unLibroPorISBN'])->name('home');;
Route::get('/filtrar', [FormularioController::class, 'index']);

Route::get('/infoLibro/{isbn}', [LibroController::class, 'infoLibro'])->name('info.libro');
Route::post('/infoLibro/{isbn}/crearResena', [ResenaController::class, 'create'])->name('crear.reseña');
Route::get('/eliminarReseña/{id}', [ResenaController::class, 'destroy']);

Route::get('/google-auth/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/google-auth/callback', function () {
    $user = Socialite::driver('google')->user();
    $user = User::updateOrCreate([
        'google_id' => $user->id,
    ], [
        'name' => $user->name,
        'email' => $user->email,
    ]);

    //return $user->hasRole('admin').' hola';
    if (!$user->hasRole('admin') ){
        $user->assignRole(2);
    }

    Auth::login($user);

    if (!$user->hasRole('admin') ){
        return redirect('/');
    }else{
        return redirect('/dashboard');
    }
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [ProfileController::class, 'gestion'])->name('dashboard');

    Route::post('/dashboard/añadirLibro', [LibroController::class, 'create'])->name('crearLibro');
    Route::get('/eliminarLibro/{id}', [LibroController::class, 'destroy']);
    Route::get('/dashboard/mostrarLibro/{id}', [LibroController::class, 'getLibro']);
    Route::put('/dashboard/editarLibro/{id}', [LibroController::class, 'update'])->name('updateLibro');

    Route::get('/eliminarAutor/{id}', [AutorController::class, 'destroy']);
    Route::get('/dashboard/mostrarAutor/{id}', [AutorController::class, 'getAutor']);
    Route::put('/dashboard/editarAutor/{id}', [AutorController::class, 'update'])->name('updateAutor');
    Route::post('/dashboard/añadirAutor', [AutorController::class, 'create'])->name('crearAutor');

    Route::get('/eliminarGenero/{id}', [GeneroController::class, 'destroy']);
    Route::get('/dashboard/mostrarGenero/{id}', [GeneroController::class, 'getGenero']);
    Route::put('/dashboard/editarGenero/{id}', [GeneroController::class, 'update'])->name('updateGenero');
    Route::post('/dashboard/añadirGenero', [GeneroController::class, 'create'])->name('crearGenero');

    Route::get('/eliminarEditorial/{id}', [EditorialController::class, 'destroy']);
    Route::get('/dashboard/mostrarEditorial/{id}', [EditorialController::class, 'getEditorial']);
    Route::put('/dashboard/editarEditorial/{id}', [EditorialController::class, 'update'])->name('updateEditorial');
    Route::post('/dashboard/añadirEditorial', [EditorialController::class, 'create'])->name('crearEditorial');

    Route::get('/eliminarUsuario/{id}', [ProfileController::class, 'destroy2']);
    Route::get('/dashboard/mostrarUsuario/{id}', [ProfileController::class, 'getUsuario']);
    Route::put('/dashboard/editarUsuario/{id}', [ProfileController::class, 'update2'])->name('updateUsuario');
    Route::get('/dashboard/bloquearUsuario/{email}', [ProfileController::class, 'bloquearUsuario'])->name('bloquear.usuario');
    //Route::post('/dashboard/añadirUsuario', [ProfileController::class, 'create'])->name('crearUsuario');
    // FALTA LA RUTA DE BLOQUEAR USUARIO
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
