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
 * Privacy Subsystem implementation for mod_msteams.
 *
 * @package    mod_msteams
 * @copyright  2020 Robert Schrenk (Center for Learning Management)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_msteams\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * Describes the meeting app transfer and the absence of locally stored user data.
 *
 * Meeting links belong to a course activity, not to an individual user. Core
 * providers handle activity completion, logs and files independently.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Retain the external meeting app's language-transfer metadata.
     *
     * @param \core_privacy\local\metadata\collection $collection Metadata collection.
     * @return \core_privacy\local\metadata\collection The populated collection.
     */
    public static function get_metadata(\core_privacy\local\metadata\collection $collection): \core_privacy\local\metadata\collection {
        $collection->add_external_location_link('msteamsapp', [
            'userlang' => 'privacy:metadata:msteamsapp:userlang',
        ], 'privacy:metadata:msteamsapp');

        return $collection;
    }

    /**
     * There are no locally stored records associated with a user.
     *
     * @param int $userid The user whose contexts are requested.
     * @return \core_privacy\local\request\contextlist Empty context list.
     */
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        return new \core_privacy\local\request\contextlist();
    }

    /**
     * There are no locally stored users to add to the context list.
     *
     * @param \core_privacy\local\request\userlist $userlist The requested context.
     */
    public static function get_users_in_context(\core_privacy\local\request\userlist $userlist) {
    }

    /**
     * There is no local user data to export.
     *
     * @param \core_privacy\local\request\approved_contextlist $contextlist Approved contexts.
     */
    public static function export_user_data(\core_privacy\local\request\approved_contextlist $contextlist) {
    }

    /**
     * Preserve shared activity content when erasing user data in a context.
     *
     * @param \context $context The requested context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
    }

    /**
     * There is no local user data to delete.
     *
     * @param \core_privacy\local\request\approved_contextlist $contextlist Approved contexts.
     */
    public static function delete_data_for_user(\core_privacy\local\request\approved_contextlist $contextlist) {
    }

    /**
     * There is no local data associated with the approved users.
     *
     * @param \core_privacy\local\request\approved_userlist $userlist Approved users.
     */
    public static function delete_data_for_users(\core_privacy\local\request\approved_userlist $userlist) {
    }
}
