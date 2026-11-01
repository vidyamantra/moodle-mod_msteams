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

namespace mod_msteams\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Meeting metadata and no-local-user-data privacy contract.
 *
 * @package mod_msteams
 * @copyright 2026 Vidya Mantra EduSystems Pvt. Ltd.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(provider::class)]
final class provider_test extends \core_privacy\tests\provider_testcase {
    /**
     * External language metadata remains declared.
     */
    public function test_external_metadata_is_preserved(): void {
        $metadata = provider::get_metadata(new collection('mod_msteams'))->get_collection();
        $this->assertCount(1, $metadata);
        $item = reset($metadata);
        $this->assertSame('msteamsapp', $item->get_name());
        $this->assertArrayHasKey('userlang', $item->get_privacy_fields());
    }

    /**
     * A real local user has no plugin-owned user contexts.
     */
    public function test_user_contexts_are_empty_and_provider_is_compliant(): void {
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $this->assertSame([], provider::get_contexts_for_userid($user->id)->get_contextids());
        $this->assertTrue((new \core_privacy\manager())->component_is_compliant('mod_msteams'));
    }

    /**
     * User requests must not erase shared activity content.
     */
    public function test_requests_export_nothing_and_preserve_shared_meeting(): void {
        global $DB;
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $id = $DB->insert_record('msteams', (object)[
            'course' => $course->id,
            'name' => 'Shared course meeting',
            'intro' => 'Course content',
            'introformat' => FORMAT_HTML,
            'externalurl' => 'https://teams.microsoft.com/l/meetup-join/local-privacy-test',
            'timemodified' => time(),
        ]);
        $original = $DB->get_record('msteams', ['id' => $id], '*', MUST_EXIST);
        $contexts = new approved_contextlist($user, 'mod_msteams', [$context->id]);
        $users = new userlist($context, 'mod_msteams');
        provider::get_users_in_context($users);
        $this->assertSame([], $users->get_userids());

        provider::export_user_data($contexts);
        $this->assertFalse(writer::with_context($context)->has_any_data());
        provider::delete_data_for_user($contexts);
        provider::delete_data_for_users(new approved_userlist($context, 'mod_msteams', [$user->id]));
        provider::delete_data_for_all_users_in_context($context);
        $this->assertEquals($original, $DB->get_record('msteams', ['id' => $id], '*', MUST_EXIST));
        $this->assertTrue($DB->record_exists('user', ['id' => $user->id]));
    }
}
