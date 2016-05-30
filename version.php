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
 * Defines the version of local_linkchecker_robot
 *
 * @package    local_linkchecker_robot
 * @author     Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2015053000;        // The current plugin version (Date: YYYYMMDDXX)
$plugin->release   = 2015053000;        // The current plugin version (Date: YYYYMMDDXX)
$plugin->requires  = 2013110500;        // Requires this Moodle version.
$plugin->component = 'local_linkchecker_robot'; // To check on upgrade, that module sits in correct place.
$plugin->maturity  = MATURITY_STABLE;
$plugin->dependencies = array(
    'auth_basic' => ANY_VERSION,
);

