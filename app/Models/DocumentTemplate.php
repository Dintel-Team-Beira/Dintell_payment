<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
       protected $fillable = [
        'name',
        'type',
        'html_template',
        'css_styles',
        'is_default',
        'company_id'
    ];

    protected $casts = [
        // 'css_styles' => 'array',
        'is_default' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function getFullHtmlAttribute()
    {
        $css = '';
        if ($this->css_styles) {
            $css = '<style>' . implode("\n", $this->css_styles) . '</style>';
        }
        
        return $css . $this->html_template;
    }
}
