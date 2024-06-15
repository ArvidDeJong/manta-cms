<?php

namespace Manta\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VacancyReaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'created_by',
        'updated_by',
        'deleted_by',
        'vacancy_id',
        'company_id',
        'host',
        'pid',
        'locale',
        'company',
        'title',
        'sex',
        'firstname',
        'lastname',
        'email',
        'phone',
        'address',
        'zipcode',
        'city',
        'country',
        'birthdate',
        'subject',
        'comments',
        'internal_contact',
        'ip',
    ];
}
