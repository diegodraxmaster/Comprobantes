<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;
    protected $fillable = ['tipo_comprobante', 'numero_comprobante', 'fecha_emision', 'monto_total', 'supplier_id', 'customer_id'];

    // Relación con el proveedor (supplier)
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Relación con el cliente (customer)
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Relación con los detalles de comprobante
    public function detallesComprobante()
    {
        return $this->hasMany(DetalleComprobante::class, 'id_comprobante');
    }
}
