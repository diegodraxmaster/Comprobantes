<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ['ruc', 'name', 'address'];

    // Relación con los comprobantes
    public function comprobantes()
    {
        return $this->hasMany(Comprobante::class, 'customer_id');
    }
}
