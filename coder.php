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

require_once('../../config.php');

// Authentication.
require_login();

// Set the user object.
global $USER;

// Inlcude the requisite helpers functionality.
require_once('classes/helpers.php');

// Set up the page params.
$pageparams = [
    'courseid' => required_param('courseid', PARAM_INT),
       'pcmid' => required_param('pcmid', PARAM_INT),
    'function' => required_param('function', PARAM_TEXT)
];

// Map the params to some variables for usability.
$courseid = $pageparams['courseid'];
$pcmid    = $pageparams['pcmid'];
$function = $pageparams['function'];

// Map the userid.
$userid   = $USER->id;

$usedorinvalid = $function == 'used' ? get_string('markused', 'block_pu') : get_string('markinvalid', 'block_pu');

// Security check making sure you have access to the PCMID in question.
if (!block_pu_helpers::guilduser_check($params = array('course_id' => $courseid, 'user_id' => $userid, 'pcmid'=> $pcmid))) {
    $url = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($url, get_string('nopermission', 'block_pu'), null, \core\output\notification::NOTIFY_ERROR);

// If you are who you claim to be and have an associated PCMid for the course in question, mark it used / invalid.
} else if (block_pu_helpers::pu_mark($params = array('course_id' => $courseid, 'user_id' => $userid, 'pcmid'=> $pcmid, 'function' => $function))) {
    $url = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($url, $usedorinvalid, null, \core\output\notification::NOTIFY_SUCCESS);

// If something goes horribly wrong.
} else {
    $url = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($url, get_string('nothingtodo', 'block_pu'), null, \core\output\notification::NOTIFY_NOTICE);
}
