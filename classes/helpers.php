<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block_pu
 * @copyright  2021 onwards LSU Online & Continuing Education
 * @copyright  2021 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_pu_helpers {

    /**
     * Checks to see if a user is a GUILD user in the given course context
     *
     * @param  array $params  [user_id, course_id]
     * @return bool
     */
    public static function guilduser_check($params) {
        global $DB, $USER;
        // Check to see if the user is who they say they are.
        if (!isset($params['pcmid']) && $USER->id === $params['user_id']) {
            // Now check to see if the user (verified) is a guild user in the course in question.
            if ($DB->get_record('block_pu_guildmaps', array('course' => $params['course_id'], 'user' => $USER->id))) {
                return true;
            }
        } else if (isset($params['pcmid']) && $USER->id === $params['user_id']) {
            if (self::codemappings(array('course_id' => $params['course_id'], 'user_id' => $params['user_id'], 'pcmid' => $params['pcmid']))) {
                return true;
            }
        } else {
           return false;
        }
    }

    /**
     * Retreives the code mappings for a user/course and a given coupon code mapping.
     *
     * @return array of objects containing
                      [ pcmid,
                        coursefullname,
                        userfirstname,
                        userlastname,
                        username,
                        LSUID,
                        useremail,
                        couponcode,
                        used,
                        valid ]
     */
    public static function codemappings($params) {
        // Needed to invoke the DB.
        global $DB;

        // Set up the course id for later.
        $cid = $params['course_id'];

        // Set up the user id for later.
        $uid = $params['user_id'];

        $ands = isset($params['pcmid']) ? 'AND pcm.id = ' . $params['pcmid'] : '';

        // The SQL.
        $mappedsql = "SELECT pcm.id AS pcmid,
               c.fullname AS coursefullname,
               u.firstname AS userfirstname,
               u.lastname AS userlastname,
               u.username AS username,
               u.idnumber AS LSUID,
               u.email AS useremail,
               pc.couponcode AS couponcode,
               pc.used AS used,
               pc.valid AS valid
        FROM mdl_block_pu_guildmaps pgm
            INNER JOIN mdl_course c ON c.id = pgm.course
            INNER JOIN mdl_user u ON u.id = pgm.user
            INNER JOIN mdl_block_pu_codemaps pcm ON pcm.guild = pgm.id
            INNER JOIN mdl_block_pu_codes pc ON pcm.code = pc.id
        WHERE u.deleted = 0
            AND pgm.current = 1
            AND c.id = $cid
            AND u.id = $uid
            $ands";

        // Build the array(s).
        $mapped = $DB->get_records_sql($mappedsql);

        // Return the data.
        return $mapped;
    }

    /**
     * Marks a given code as used or invalif.
     *
     * @return bool
     */
    public static function pu_mark($params) {
        // Needed to invoke the DB.
        global $DB;

        // Set up these for later.
        $cid   = $params['course_id'];
        $uid   = $params['user_id'];
        $pcmid = $params['pcmid'];
        $func  = $params['function'];

        // Build the setter.
        $setval = $func == 'used' ? 1 : 0;
        $setter = $func == 'used' ? "SET pc.used=$setval" : "SET pc.valid=$setval";

        // The SQL.
        $usedsql = "UPDATE mdl_block_pu_guildmaps pgm
            INNER JOIN mdl_course c ON c.id = pgm.course
            INNER JOIN mdl_user u ON u.id = pgm.user
            INNER JOIN mdl_block_pu_codemaps pcm ON pcm.guild = pgm.id
            INNER JOIN mdl_block_pu_codes pc ON pcm.code = pc.id
            $setter
        WHERE u.deleted = 0
            AND pgm.current = 1
            AND c.id = $cid
            AND u.id = $uid
            AND pcm.id = $pcmid";

        // Build the array(s).
        $used = $DB->execute($usedsql);

        // Return the data.
        return $used;
    }
}
