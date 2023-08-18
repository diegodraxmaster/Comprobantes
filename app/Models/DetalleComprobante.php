<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleComprobante extends Model
{
    use HasFactory;
    protected $fillable = ['id_comprobante', 'id_producto', 'descripcion', 'cantidad', 'precio_unitario', 'subtotal'];

    // Relación con el comprobante
    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class, 'id_comprobante');
    }
    // Relación con el producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
