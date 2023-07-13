<?php
namespace bpmj\wpidea\admin\tools\importer;

class Import_Data{
    /**
     * Array of students to import (non-associative)
     * 
     * Each array element contains email, first name and last name (in this order)
     *
     * @var array
     */
    public $students_to_import;

    /**
     * Last imported line
     *
     * @var int
     */
    public $actual_line = 0;

    /**
     * Count $this->students_to_import
     *
     * @var int
     */
    public $all_lines_count;

    /**
     * Should the access data (login, password etc.) be send to just created user?
     *
     * @var bool
     */
    public $send_accesses;

    /**
     * Should the order confirmation be send to just created user and site admin?
     *
     * @var bool
     */
    public $send_notifications;

    /**
     * Should just created user be subscribed to the mailing list that belongs to selected course?
     *
     * @var bool
     */
    public $add_to_mailings;

    /**
     * List of courses that user should be added to
     *
     * @var array
     */
    public $courses;

    /**
     * Unique generated import ID
     * 
     * Currently md5( time() . wp_rand() )
     *
     * @var string
     */
    public $import_id;

    /**
     * How many lines should be skipped? 
     * 
     * @note This offset is a multiplier, so actual offset equals: $offset * Students_Importer::IMPORT_LINES_PER_CRON_ACTION)
     *
     * @var int
     */
    public $offset = 0;

    /**
     * Chunk of data to import (so we don't have to import all lines at once nor modify the original students_to_import[] array )
     *
     * @var array
     */
    public $chunk;

    /**
     * @var int
     */
    public $currently_processed_user_index;
}