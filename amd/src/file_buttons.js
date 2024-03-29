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
 * Cross Enrollment Tool
 *
 * @package   block_pu
 * @copyright 2021 onwards Louisiana State University
 * @copyright 2021 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 define(['jquery', 'block_pu/notifications', 'block_pu/pu_lib', 'block_pu/refresh'],
    function($, noti, PULib, Refresh) {
    'use strict';
    return {

        confirmCheckExecute: function (params) {
            // First let's confirm.
            noti.callYesNoModi(params).then(function (response) {
                // console.log("yesNo Modal, what is response: ", response);
                if (response.status == true) {
                    return response;
                }

            }).then(function (response) {

                // We need to check if the file exists.
                PULib.check_file_exists(response).then(function (response) {
                    if (response.success == false) {
                        $('html,body').scrollTop(0);
                        noti.callNoti({
                            message: response.msg,
                            type: "fail"
                        });

                    } else {
                        // Get the current list of files from the folder that's indicated
                        // in the settings page.
                        PULib.get_file_list(response).then(function (response) {
                            Refresh.refreshGeneralFiles(response);
                        });
                    }
                });
            });
        },
    };
});