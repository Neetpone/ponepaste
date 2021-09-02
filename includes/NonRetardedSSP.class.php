<?php

use Illuminate\Database\Eloquent\Builder;

class NonRetardedSSP {
    public static function run(DatabaseHandle $conn, array $request, Builder $builder) {
        /* Some of these parameters might not be passed; zero is an OK default */
        $draw = (int) @$request['draw'];
        $start = (int) @$request['start'];
        $length = (int) @$request['length'];


        /* figure out total records */
        $recordsTotal = $builder->count();

        /* build query */
        $params = [];

        if ($length != 0) {
            $builder = $builder->limit($length);

            if ($start != 0) {
                $builder = $builder->offset($start);
            }
        }

        /* fire it off */
        $data = $builder->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => ($recordsTotal - count($data)),
            'data' => $data
        ];
    }
}
