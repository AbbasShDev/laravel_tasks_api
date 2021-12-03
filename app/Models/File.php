<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model {

    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_id',
        'name'
    ];

    protected $appends = [
        'url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function getUrlAttribute()
    {
        return config('app.url') . Storage::url('tasks/' . $this->task_id . '/' . $this->name);
    }
}
