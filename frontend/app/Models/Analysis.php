<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Inclua TODAS as colunas que o StockController tenta salvar.
     */
    protected $fillable = [
        'ticker',
        'content_full',
        'content_raw',
        'status',
        'revisor_name',
    ];

    /**
     * Indica o tipo da coluna status para o PHP
     */
    protected $casts = [
        'status' => 'string',
    ];
}