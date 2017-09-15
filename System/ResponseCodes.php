<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 15.09.2017
 * Time: 10:04
 */

//Statement of work
const RESPONSE_OK                                   = 0x00;
const RESPONSE_ERROR_ON_WORK                        = 0x01;
const RESPONSE_NOT_FOUND                            = 0x02;

//Statement of User
const RESPONSE_USER_BAD_LOGIN                       = 0x03;
const RESPONSE_USER_ALREADY_LOGGED_IN               = 0x04;
const RESPONSE_USER_NOT_LOGGED_IN                   = 0x05;
const RESPONSE_USER_LOGGED_IN                       = 0x06;
const RESPONSE_USER_LOGIN_FAILED                    = 0x07;
const RESPONSE_USER_INFO                            = 0x08;
const RESPONSE_USER_INFO_GET                        = 0x09;
const RESPONSE_USER_ACCESS_DENIED                   = 0x0A;

//Statement of Study
const RESPONSE_STUDY_TIMETABLE_BY_DAY               = 0x100;