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

class block_pu extends block_list {

    public $course;
    public $user;
    public $content;
    public $coursecontext;

    public function init() {
        $this->title = get_string('pluginname', 'block_pu');
        $this->set_course();
        $this->set_user();
        $this->set_system_context();
        $this->set_course_context();
    }

    /**
     * Returns the course object
     *
     * @return object
     */
    public function set_course() {
        global $COURSE;
        $this->course = $COURSE;
    }

    /**
     * Returns the user object
     *
     * @return object
     */
    public function set_user() {
        global $USER;
        $this->user = $USER;
    }

    /**
     * Returns the system context
     *
     * @return context
     */
    private function set_system_context() {
        $this->system_context = context_system::instance();
    }

    /**
     * Returns this course's context
     *
     * @return context
     */
    private function set_course_context() {
        $this->course_context = context_course::instance($this->course->id);
    }

    /**
     * Indicates which pages types this block may be added to
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
             'site-index' => false,
            'course-view' => true 
        );
    }

    /**
     * Indicates that this block has its own configuration settings
     *
     * @return bool
     */
    public function has_config() {
        return true;
    }

    /**
     * Sets the content to be rendered when displaying this block
     *
     * @return object
     */
    public function get_content() {
        if (!empty($this->content)) {
            return $this->content;
        }

        // Create a fresh content container.
        $this->content = $this->get_new_content_container();

        $coursecontext = context_course::instance($this->course->id);
        $systemcontext = context_system::instance();

        // Course-level Features.
        $this->add_item_to_content([
            'lang_key' => get_string('pu_block_intro', 'block_pu')
        ]);

        $this->add_item_to_content([
            'lang_key' => get_string('pu_docs_intro', 'block_pu'),
            'attributes' => array('class' => 'intro')
        ]);

        /*
        $this->add_item_to_content([
            'lang_key' => get_string('pu_block_intro', 'block_pu'),
            'icon_key' => 't/email',
            'page' => 'compose'
            'query_string' => ['courseid' => $this->course->id, 'userid' => $this->user->id]
        ]);
        */

        return $this->content;
    }

    /**
     * Builds and adds an item to the content container for the given params
     *
     * @param  array $params  [lang_key, icon_key, page, query_string]
     * @return void
     */
    private function add_item_to_content($params) {
        if (!array_key_exists('query_string', $params)) {
            $params['query_string'] = [];
        }

        $item = $this->build_item($params);

        $this->content->items[] = $item;
    }

    /**
     * Builds a content item (link) for the given params
     *
     * @param  array $params  [lang_key, icon_key, page, query_string]
     * @return string
     */
    private function build_item($params) {
        global $OUTPUT;

        $label = $params['lang_key'];

        $icon = isset($params['icon_key']) ? $icon = $OUTPUT->pix_icon($params['icon_key'], $label, 'moodle', ['class' => 'icon']) : null;

        $attrs = isset($params['attributes']) ? $params['attributes'] : null;

        if (isset($params['page'])) {
            return html_writer::link(
                new moodle_url('/blocks/pu/' . $params['page'] . '.php', $params['query_string']),
                $icon . $label
            );
	} else {
             return html_writer::tag(
                'span',
                $icon . $label,
                $attrs
            );
        }
    }

    /**
     * Returns an empty "block list" content container to be filled with content
     *
     * @return object
     */
    private function get_new_content_container() {
        $content = new stdClass;
        $content->items = [];
        $content->icons = [];
        $content->footer = '';

        return $content;
    }
}
