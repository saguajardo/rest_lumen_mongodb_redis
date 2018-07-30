<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Task extends Eloquent {
	protected $collection = 'task';

	public $timestamps = false;

	public $primaryKey = '_id';

	protected $fillable = ["_id", "title", "description", "due_date", "completed", "created_at", "updated_at"];
}
