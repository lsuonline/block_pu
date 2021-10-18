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

// Set the string for use later.
$fn = new lang_string('foldername', 'block_pu');

// Create the folder / submenu.
$ADMIN->add('blocksettings', new admin_category('blockpufolder', $fn));

// Create the settings block.
$settings = new admin_settingpage($section, get_string('settings'));

// Make sure only admins see this one.
if ($ADMIN->fulltree) {
    // Default coupon codes per course
    $settings->add(
        new admin_setting_configtext(
            'block_pu_defaultcodes',
            get_string('default_numcodes', 'block_pu'),
            get_string('default_numcodes_help', 'block_pu'),
            3 // Default.
        )
    );
}

// Add the folder.
$ADMIN->add('blockpufolder', $settings);

// Set the url for the ProctorU override tool.
$puoverride = new admin_externalpage('manage_overrides',
              new lang_string('manage_overrides', 'block_pu'),
              "$CFG->wwwroot/blocks/pu/overrides.php"
);

// Add the ProctorU override tool url.
$context = \context_system::instance();

// Add the link for those who have access.
if (has_capability('block/pu:admin', $context)) {
    $ADMIN->add('blockpufolder', $puoverride);
}

// Prevent Moodle from adding settings block in standard location.
$settings = null;
