<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class Comment extends Model
{
    use SoftDeletes;

    public $table = 'comments';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'order_application_id',
        'user_id',
        'comment_text',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function order_application()
    {
        return $this->belongsTo(OrderApplication::class, 'order_application_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
