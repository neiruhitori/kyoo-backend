<?php

namespace App\Imports;

use App\ScheduleTemplateDetail;
use Maatwebsite\Excel\Concerns\ToModel;

class ScheduleTemplateDetailImport implements ToModel
{
    private $id = null;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ScheduleTemplateDetail([
            'schedule_template_id' => $this->id,
            'date' => $row[0],
            'description' => $row[1],
        ]);
    }
}
