<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class UserWithoutProfile extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'user';
    protected $guarded = [];
}
