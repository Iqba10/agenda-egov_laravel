<?php
/**
 * AgendaDocument.php - Model for Agenda Documents
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AgendaDocument extends Model
{
    protected $table = 'dokumen_agenda';

    protected $fillable = [
        'agenda_id',
        'nama_file',
        'original_name',
        'content_hash',
        'content',
        'mime_type',
        'file_size',
    ];

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Agenda::class, 'agenda_id');
    }

    public function getUrlAttribute(): string
    {
        return route('documents.show', $this);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('documents.download', $this);
    }

    public function getExistsAttribute(): bool
    {
        return Storage::disk('public')->exists('agendas/documents/' . $this->nama_file);
    }

    public function getExtensionAttribute(): string
    {
        return pathinfo($this->nama_file, PATHINFO_EXTENSION);
    }

    public function getTypeAttribute(): string
    {
        $ext = strtolower($this->extension);
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return 'image';
        }
        if ($ext === 'pdf') {
            return 'pdf';
        }
        return 'other';
    }
}
