<?php

namespace App\Model\SRO\Account;

use App\Model\SRO\Shard\Char;
use Illuminate\Database\Eloquent\Model;

class UniqueKillLog extends Model
{

    /**
     * The Database connection name for the model.
     *
     * @var string
     */
    protected $connection = 'log';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dbo.uniquekilllogs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'CharName16',
        'UniqueName'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'Points' => 'integer'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getCharacter()
    {
        return $this->belongsTo(Char::class, 'CharName16', 'CharName16')
            ->whereNotNull('CharName16');
//            ->select(['CharID', 'CharName16', 'CurLevel', 'GuildID']);
    }
}
