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
        $this->byDay('today');
    }

    public function byDay($day = null)
    {

    }
}