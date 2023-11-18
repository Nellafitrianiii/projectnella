<?php

namespace App\Models;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $table ='tb_m_clients';
    public function project()
    {
        return $this->belongsTo(Project::class) ;
    }
    public function delete()
    {
        parent::delete();
    }
}
