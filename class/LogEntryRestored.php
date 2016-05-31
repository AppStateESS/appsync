<?php

namespace AppSync;

/**
 * Object class for restoring the LogEntry objs after retrieving them from the database.
 *
 * @author Chris Detsch
 * @package appsync
 *
 */

class LogEntryRestored extends LogEntry {
	public function __construct(){} // Empty constructor for loading from DB
}
