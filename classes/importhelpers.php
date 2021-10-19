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
 * CSV import of ProctorU coupon codes and GUILD user mapping.
 *
 * @package   block_pu
 * @copyright 2021 onwards LSUOnline & Continuing Education
 * @copyright 2021 onwards Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/pu/classes/helpers.php');

/**
 * Building the class for the task to be run during scheduled tasks.
 */
class pu_import_helper {

    /**
     * Base function for importing the coupon code data.
     *
     * @package   block_pu
     * @return    @bool
     *
     */
    public static function block_pu_codeimport() {
        // For later.
        global $CFG;

        // Set the filename variable from CFG.
        $filename = $CFG->block_pu_ccfile;
    
        // Load the content based on the filename / location.
        $content = self::block_pu_getcccontent($filename);
    
        // Import the CSV into the DB.
        $success = self::block_pu_ccimport($content);
    
        return $success;
    }

    /**
     * Base function for importing the coupon code data.
     *
     * @package   block_pu
     * @return    @bool
     *
     */
    public static function block_pu_guildimporter() {
        // For later.
        global $CFG;

        // Set the filename variable from CFG.
        $filename = $CFG->block_pu_guildfile;
    
        // Load the content based on the filename / location.
        $content = self::block_pu_getguildcontent($filename);
    
        // Import the CSV into the DB.
        $success = self::block_pu_guildimport($content);
    
        return $success;
    }

    /**
     * Loops through data and calls block_pu_ccfield2db.
     *
     * @package   block_pu
     * @param     @array $content
     *
     */
    public static function block_pu_ccimport($content) {
    
        // Set the counter for later.
        $counter = 0;
    
        // Set the start time for later.
        $starttime = microtime(true);
    
        // Start the cli log.
        echo("Importing coupon code data\n");
    
        // Loop through the content.
        foreach ($content as $line) {
    
            // Set the fields based on data from the line.
            $fields = array_map('trim', $line);
    
            // If we have an empty bit, skip it.
            if (!empty($fields[0])) {
    
                // Add the data to the DB.
                $success = self::block_pu_ccfield2db($fields);
    
                if ($success) {
                    // Increment the counter by one.
                    $counter++;
                }
            }
        }
    
        // Calculate the elapsed time.
        $elapsedtime = round(microtime(true) - $starttime, 1);
    
        // Finish the log, letting me know how many we did and how long it took.
        echo("Completed importing " . $counter . " rows of data in " . $elapsedtime . " seconds.\n");
    
        return $success;
    }

    /**
     * Marks everybody inactive so we can re-import them 
     * and set their status accordingly.
     *
     * @package   block_pu
     * @return    @bool
     *
     */
    public static function block_pu_guild_inactivate() {
        global $DB;

        // Set up the SQL for updating the table.
        $sql = 'UPDATE {block_pu_guildmaps}
                SET current = 0';

        // Run it.
        $return = $DB->execute($sql);

        return $return;
    }

    /**
     * Reset unused ProctorU coupon codes for invalid users.
     *
     * @package   block_pu
     * @return    @bool
     *
     */
    public static function block_pu_code_unmap() {
        global $DB;

        // Set the table.
        $maptable = 'mdl_block_pu_guildmaps';
        $cmtable = 'mdl_block_pu_codemaps';
        $codetable = 'mdl_block_pu_codes';

        // SQL to grab data.
        $sql = 'SELECT pcm.id AS pcmid,
                       pgm.user AS pgmuser,
                       pgm.course AS pgmcourse,
                       pc.id AS pcid,
                       pc.couponcode AS pccode,
                       pc.used AS pcused,
                       pc.valid AS pcvalid
                FROM mdl_block_pu_guildmaps pgm
                    INNER JOIN mdl_block_pu_codemaps pcm ON pgm.id = pcm.guild
                    INNER JOIN mdl_block_pu_codes pc ON pc.id = pcm.code
                WHERE pgm.current = 0
                    AND pc.used = 0';


        // Get array of userids who are not current GUILD students.
        $orphans = $DB->get_records_sql($sql);
 
        // Set up the SQL for updating the table.
        foreach ($orphans as $orphan) {

            // Delete rows with this.
            $dsql = "DELETE FROM {$cmtable} WHERE id = $orphan->pcmid";

            // Update rows with this.
            $usql = "UPDATE {$codetable} SET valid = 1 WHERE id = $orphan->pcid";

            // Update the unused 
            $freeme   = $DB->execute($usql);

            if (isset($freeme)) {
                echo("Dissacociated ProctorU coupon code: $orphan->pccode with id: $orphan->pcid and marked it valid.\n");
            }

            // Delete any unused coupon code mappings for non-current GUILD students.
            $deleteme = $DB->execute($dsql);

            if ($deleteme) {
                echo("Deleted non-current code mapping: $orphan->pcmid for userid: $orphan->pgmuser in course: $orphan->pgmcourse.\n");
            }
        }

        return true;
    }

    /**
     * Loops through data and calls block_pu_guildfield2db.
     *
     * @package   block_pu
     * @param     @array $content
     *
     */
    public static function block_pu_guildimport($content) {
        // Set the counter for later.
        $counter = 0;
    
        // Set the start time for later.
        $starttime = microtime(true);

        // Deactivate everybody prior to import.
        $inactivate = self::block_pu_guild_inactivate();

        // Let the log know what we've done.
        if ($inactivate) {
            echo("Deactivated existing GUILD mappings\n");
        }
    
        // Start the log.
        echo("Importing GUILD mapping data\n");
    
        // Loop through the content.
        foreach ($content as $line) {
    
            // Set the fields based on data from the line.
            $fields = array_map('trim', $line);
    
            // If we have an empty bit, skip it.
            if (!empty($fields[0]) && !empty($fields[1])) {
    
                // Add the data to the DB.
                $success = self::block_pu_guildfield2db($fields);
    
                if ($success) {
                    // Increment the counter by one.
                    $counter++;
                }
            }
        }
    
        // Calculate the elapsed time.
        $elapsedtime = round(microtime(true) - $starttime, 1);
    
        // Finish the log, letting me know how many we did and how long it took.
        echo("Completed importing " . $counter . " rows of data in " . $elapsedtime . " seconds.\n");
    
        return $success;
    }

    /**
     * Gets the content from the filename and location.
     *
     * @package   block_pu
     * @param     @string $filename
     * @return    @array $content
     *
     */
    public static function block_pu_getcccontent($filename) {
            // Grab the CSV from the file specified.
            $content = array_map('str_getcsv', file($filename));
    
            return $content;
    }
    
    /**
     * Gets the content from the filename and location.
     *
     * @package   block_pu
     * @param     @string $filename
     * @return    @array $content
     *
     */
    public static function block_pu_getguildcontent($filename) {
            // Grab the CSV from the file specified.
            $content = array_map('str_getcsv', file($filename));
    
            return $content;
    }

    /**
     * Maps the fields to the data object for insert_record.
     *
     * @package   block_pu
     * @param     @array $fields
     * @return    @int $return
     *
     */
    public static function block_pu_ccfield2db($fields) {
        global $DB;
    
        $return = '';
        // Set this up for later.
        $data = array();
    
        // Populate the data.
        $data['couponcode'] = $fields[0];
    
        // What table do we want the data in.
        $table = 'block_pu_codes';
    
        $exists = $DB->get_record($table, $data);
    
        if (!$exists) {
            // Insert the data and return the id of the newly inserted row.
            $return = $DB->insert_record($table, $data, $returnid = true, $bulk = false);
    
            // Log the imports.
            echo("  Imported coupon code: " .
                $data['couponcode'] .
                " into block_pu_codes id: " .
                $return .
                ".\n");
        } else {
    
            // Log the skipped ones too.
            echo("  Skipped existing coupon code: " .
                $data['couponcode'] .
                "\n");
        }
    
        // Return the block_pu_codes row id even though we don't use it.
        return $return;
    }

    /**
     * Maps the fields to the data object for insert_record.
     *
     * @package   block_pu
     * @param     @array $fields
     * @return    @int
     *
     */
    public static function block_pu_guildfield2db($fields) {
        global $CFG, $DB;

        // Set this up for later.
        $d = array();
        $data = array();

        // Populate the data.
        $d['courseidnumber'] = $fields[0];
        $d['useridnumber'] = $fields[1];
    
        if ($CFG->block_pu_profile_field == 'pu_idnumber') {

            $usersql = 'SELECT u.id AS userid
                        FROM mdl_user u
                        WHERE u.idnumber = ' . $d['useridnumber'];

        } else {
            $field = $DB->get_record('user_info_field', array('shortname' => $CFG->block_pu_profile_field));

        // Build some sql for grabbing users with a custom profile field based identifier.
            $usersql = 'SELECT u.id AS userid
                            FROM mdl_user u
                        INNER JOIN mdl_user_info_data ud ON ud.userid = u.id
                            AND ud.fieldid = ' . $field->id .
                          ' AND ud.data <> ""
                        WHERE ud.data = ' . $d['useridnumber'];
        }

        $return = '';
    
        // Set this for later. 
        $coursetable = 'course';
    
        // Get the course object based on the identifier.
        $course = $DB->get_record($coursetable, array('shortname' => $d['courseidnumber']));

        // Get the user object based on the identifier.
        $user = $DB->get_record_sql($usersql);

        if (!isset($user->userid)) {
            return false;
        }
    
        // Start to build the $data array.
        $data['user'] = $user->userid;
        $data['course'] = $course->id;
    
        // What table do we want the data in.
        $table = 'block_pu_guildmaps';
    
        // Check to see if the course / user pair exists in the table.
        $exists = $DB->get_record($table, $data);

        // If they do not exist.
        if (!$exists) {
            // Insert the data and return the id of the newly inserted row.
            $return = $DB->insert_record($table, $data, $returnid = true, $bulk = false);
    
            // Log the imports.
            echo("  Imported GUILD course: " .
                $data['course'] .
                " with student: " .
                $data['user'] .
                " into block_pu_guildmaps id: " .
                $return .
                ".\n");

        // If they do exist.
        } else {
            // They exist but will not be current. Here, we make sure they are.
            $data['id'] = $exists->id;
            $data['current'] = 1;
    
            // Update the record.
            $return = $DB->update_record($table, $data, $bulk = false);
    
            // Log the updated ones too.
            echo("  Updated existing GUILD course: " .
                $data['course'] .
                " with student: " .
                $data['user'] .
                " and marked them current." .
                "\n");
        }

        // Return the block_pu_guildmaps row id even though we don't use it.
        return $return;
    }
}
