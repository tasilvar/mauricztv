<?php

namespace bpmj\wpidea\admin\tools\importer;

use bpmj\wpidea\admin\helpers\tasker\Tasker_Async_Task;

class Import_Single_Student_Process extends Tasker_Async_Task {

    /**
     * @var Students_Importer
     */
    private $importer;

    public const ACTION_NAME = 'bpmj_wpi_import_single_student';

    public function __construct(Students_Importer $importer)
    {
        $this->importer = $importer;
        
        parent::__construct();
    }

    /**
     * @var string
     */
    protected $action = self::ACTION_NAME;

    public function clear_the_queue(): void
    {
        $this->cancel_process();
    }

    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param Import_Data $import_data Queue item to iterate over
     *
     * @return mixed
     */
    protected function task($import_data) {
        $this->importer->import_user($import_data);

        return false;
    }

    protected function complete(): void
    {
       $this->importer->mark_import_as_done();

       parent::complete();
    }
}