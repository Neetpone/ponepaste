<?php
class NonRetardedSSP {
    public static function run(DatabaseHandle $conn, array $request, string $countQuery, string $query) {
        /* Some of these parameters might not be passed; zero is an OK default */
        $draw = (int) @$request['draw'];
        $start = (int) @$request['start'];
        $length = (int) @$request['length'];

        /* figure out total records */
        $recordsTotal = (int) $conn->querySelectOne($countQuery, [], PDO::FETCH_NUM)[0];

        /* build query */
        $params = [];

        if ($length != 0) {
            $query .= ' LIMIT ?';
            array_push($params, $length);

            if ($start != 0) {
                $query .= ' OFFSET ?';
                array_push($params, $start);
            }
        }

        /* fire it off */
        $stmt = $conn->query($query, $params);
        $data = $stmt->fetchAll(PDO::FETCH_NUM);

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => ($recordsTotal - count($data)),
            'data' => $data
        ];
    }
}
