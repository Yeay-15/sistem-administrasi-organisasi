<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterTemplate extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'letter_type',
        'subject',
        'file_name',
        'number_pattern',
        'destination_pattern',
        'signatory_label',
        'signers',
        'fields',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'signers' => 'array',
        'fields' => 'array',
        'is_active' => 'boolean',
    ];

    public const CATEGORY_COMMITTEE = 'kepanitiaan';
    public const CATEGORY_ORGANIZATION = 'organisasi';

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function isCommitteeBased(): bool
    {
        return $this->category === self::CATEGORY_COMMITTEE;
    }

    /** Lokasi file .docx template (sudah berisi token ${...}). */
    public function path(): string
    {
        return resource_path('letter_templates/' . $this->file_name);
    }
}
