<?php
/**
 *  app/Models/File.php
 *
 * Date-Time: 10.06.21
 * Time: 09:55
 * @author Insite LLC <hello@insite.international>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class File
 * @package App\Models
 * @property integer $id
 * @property string $fileable_type
 * @property integer $fileable_id
 * @property string $title
 * @property string $path
 * @property string $format
 * @property integer $type
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Resume extends Model
{
    use HasFactory;



    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'resume';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_name',
        'path',
    ];

    public function vacancy():HasOne{
        return $this->hasOne(Vacancy::class);
    }

    /**
     * Get the user's file full url.
     *
     * @return string
     */
    public function getFileUrlAttribute(): string
    {
        return $this->path . '/' . $this->file_name;
    }
}
