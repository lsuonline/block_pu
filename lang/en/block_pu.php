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

// Block strings.
$string['pu_block_intro_one'] = 'Here is your first coupon code for <strong>{$a->coursename}</strong>';
$string['pu_block_intro_multi'] = 'Here are your {$a->numassigned} coupon codes for <strong>{$a->coursename}</strong>';
$string['pu_docs_intro'] = 'What you need to know about PU codes:';
$string['pu_docs_intronone'] = 'You have been allocated {$a->numtotal} coupon codes for this course.<br>Please click the "Request a ProctorU coupon code" button to request your first coupon code for this course.';
$string['pu_docs_allocatednum'] = 'You have <strong>requested</strong> {$a->numallocated} of the {$a->numtotal} coupon codes for this course.';
$string['pu_docs_usednum'] = 'You have <strong>used</strong> {$a->numused} of the {$a->numtotal} coupon codes for this course.';
$string['pu_docs_noneleft'] = 'You have used all of the coupon codes allocated for this course.<br>If you need another code, please contact <a href="mailto:answers@online.lsu.edu">answers@online.lsu.edu</a>.';
$string['pu_docs_used'] = 'If you have used the top (latest) code and need another for your next exam, please click the "<strong>mark used</strong>" button below:';
$string['pu_docs_requestedall'] = 'If you have used the top (latest) code, please click the "<strong>mark used</strong>" button below:';
$string['pu_docs_invalid'] = 'If the top (latest) code does not work, request a replacement code by clicking on the "<strong>request replacement</strong>" button below:';
$string['pu_docs_invalidsused'] = 'You have received {$a->numused} replacement codes from the pool of {$a->numtotal} available for this course.';
$string['pu_docs_invalidsfull'] = 'You have requested all of the available replacement codes available for this course.<br>If you need another code, please contact <a href="mailto:answers@online.lsu.edu">answers@online.lsu.edu</a>.';
$string['pu_used'] = 'Mark used';
$string['pu_new'] = 'Request a ProctorU coupon code';
$string['pu_past'] = 'Used code ';

// Interstitial strings.
$string['pu_yousure'] = 'Are you sure you want to request a replacement ProctorU coupon code?';
$string['pu_replace'] = 'Request replacement';
$string['pu_try_again'] = 'Retry latest code';


// Error strings.
$string['nopermissions'] = 'You do not have permission to modify coupon codes.';
$string['nopermission'] = 'You do not have permission to modify the requested coupon code.<br><br>Please contact an administrator if you believe you should be able to modify that specific code.';
$string['markused'] = 'Successfully marked your latest coupon code as used and requested a new code.';
$string['lastused'] = 'Successfully marked your final coupon code as used.';
$string['markinvalid'] = 'Successfully marked your latest coupon code as invalid and requested a replacement code.';
$string['assigned'] = 'You have been assigned a new ProctorU coupon code.';
$string['nothingtodo'] = 'There was nothing to do.';
