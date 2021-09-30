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

// Block.
$string['pluginname'] = 'ProctorU Coupon Codes';

// Tasks.

// Capabilities.
$string['pu:admin'] = 'Administer the ProctorU Coupon Code system.';
$string['pu:addinstance'] = 'Add a new ProctorU Coupon Code block to a course page';
$string['pu:myaddinstance'] = 'Add a new ProctorU Coupon Code block to the /my page';

// General terms.
$string['backtocourse'] = 'Back to course';
$string['backtohome'] = 'Back to home';

// Settings management.
$string['default_numcodes'] = '# of codes';
$string['default_numcodes_help'] = 'The default number of coupon codes issued to a user in a course';

// Configuration.
$string['override_numcodes'] = '# of exams';
$string['override_numcodes_help'] = 'Override the default number of coupon codes for this course.';
$string['override_numinvalid'] = '# of replacement codes';
$string['override_numinvalid_help'] = 'Override the default number of replacement codes for this course. By default this is capped at the number of exams in this course.';

// Block strings
$string['pu_block_intro'] = 'Your {$a->numused} coupon code(s) for <strong>{$a->coursename}</strong>';
$string['pu_docs_intro'] = 'What you need to know about PU codes:';
$string['pu_docs_usednum'] = 'You have used {$a->numused} of the {$a->numtotal} coupon codes for this course';
$string['pu_docs_used'] = 'If you have used this code and need another for your next exam, please click the "request new code" button below';
$string['pu_docs_invalid'] = 'If this code does not work, request a replacement code by clicking on the "request replacement code" button below';
