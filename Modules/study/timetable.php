<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 06.09.2017
 * Time: 16:11
 */

final class timetable extends Base
{
    public function index()
    {
        $this->byDay();
    }

    public function byDay($day = null)
    {
        global $_BOTH;
        if (isset($_BOTH['day'])){
            $this->response->setJson($this->getTimetable(date('Y-m-d', strtotime($_BOTH['day']))));
        } else if ($day !== null ) {
            $this->response->setJson($this->getTimetable(date('Y-m-d', strtotime($day))));
        } else
            $this->response->setJson($this->getTimetable(date('Y-m-d', strtotime('today'))));
        $this->response(RESPONSE_STUDY_TIMETABLE_BY_DAY);
    }

    private function getTimetable(string $day) : array
    {
        $group_id = $this->user->getUserAcademicalGroups();

        if (is_array($group_id)) {
            $group_id = implode(',', $group_id);
        }
        return $this->db->query("
          SELECT  lesson_number, 
                  (SELECT lesson_name FROM study_lesson WHERE lesson_id = id) as lesson_name, 
                  lesson_type,
                  (SELECT CONCAT(IFNULL(last_name, ''), ' ', SUBSTR(IFNULL(first_name, ''), 1, 1), '. ', SUBSTR(IFNULL(middle_name, '') FROM 1 FOR 1), '.') FROM user_info WHERE teacher_id = id) as teacher_name, 
                  lecture_hall
          FROM study_timetable 
          WHERE `date` = '$day' AND 
                academical_group IN ('$group_id')
          ORDER BY lesson_number ASC
        ")->rows;
    }
}