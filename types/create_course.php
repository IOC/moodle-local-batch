<?php

// Local batch plugin for Moodle
// Copyright © 2012,2013 Institut Obert de Catalunya
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// Ths program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.

require_once('base.php');

class batch_type_create_course extends batch_type_base {

    function execute($jobid, $categoryid, $params) {
        global $CFG, $USER, $DB;
        $context = context_coursecat::instance($categoryid);
        $fs = get_file_storage();
        $af = $fs->get_area_files($context->id, 'local_batch', 'job', $jobid, 'filename', false);
        if ($af) {
            $file = array_shift($af);
            $params->courseid = batch_course::restore_backup($file, $context, $params);
        }
    }

    function params_info($params, $jobid) {
        global $DB, $PAGE;

        $context = context_coursecat::instance($params->category);
        $fs = get_file_storage();
        $af = $fs->get_area_files($context->id, 'local_batch', 'job', $jobid, 'filename', false);
        $attach = $filename = $fileurl = '';
        if ($af) {
            $attach = array_shift($af);
            $filename = $attach->get_filename();
            $path = '/'.$attach->get_contextid().'/local_batch/job/'.$attach->get_itemid().$attach->get_filepath().$filename;
            $fileurl = moodle_url::make_file_url('/pluginfile.php', $path, true);
        }
        $categoryname = $DB->get_field('course_categories', 'name' , array('id' => $params->category));
        $user = batch_get_user($params->user);
        $url = new moodle_url('/course/category.php', array('id' => $params->category));

        $batchoutput = $PAGE->get_renderer('local_batch');

        return $batchoutput->print_info_create_courses(
            array(
                'attach'       => $attach,
                'categoryname' => $categoryname,
                'courseid'     => (isset($params->courseid)?$params->courseid:''),
                'fileurl'      => $fileurl,
                'filename'     => $filename,
                'fullname'     => (isset($params->fullname)?$params->fullname:''),
                'shortname'    => $params->shortname,
                'startday'     => $params->startday,
                'startmonth'   => $params->startmonth,
                'startyear'    => $params->startyear,
                'url'          => $url,
                'user'         => $user
            )
        );
    }
}
