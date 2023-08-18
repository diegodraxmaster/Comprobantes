<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'name', 'description', 'price'];

    // Relación con los detalles de comprobante
    public function detallesComprobante()
    {
        return $this->hasMany(DetalleComprobante::class, 'producto_id');
    }
}
