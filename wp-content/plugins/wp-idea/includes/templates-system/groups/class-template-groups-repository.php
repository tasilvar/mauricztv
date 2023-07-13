<?php

namespace bpmj\wpidea\templates_system\groups;

use bpmj\wpidea\templates_system\groups\helpers\Active_Template_Group_Handler;

class Template_Groups_Repository
{
    private const GROUPS_OPTION_NAME = 'bpmj_wpi_template_groups';


    /**
     * @var Groups_Collection
     */
    private $groups;

    private $active_group_handler;

    /**
     * Template_Groups_Repository constructor.
     */
    public function __construct(
        Active_Template_Group_Handler $active_group_handler
    ) {
        $this->active_group_handler = $active_group_handler;
    }

    public function find_all(): Groups_Collection
    {
        if (isset($this->groups)) {
            return $this->groups;
        }

        $fetched_groups = $this->fetch_groups_from_the_db();

        $this->groups = $fetched_groups;

        return $this->groups;
    }

    public function find(Template_Group_Id $id): ?Template_Group
    {
        $groups = $this->find_all();

        $matches = array_filter($groups->getArrayCopy(), static function($group) use ($id) {
            /** @var Template_Group $group */
            return $group->get_id()->equals($id);
        });

        return !empty($matches) ? reset($matches)  : null;
    }

    public function find_active(): ?Template_Group
    {
        $active_group_id = $this->active_group_handler->get_active_group_id();

        if ($active_group_id === null) {
            return null;
        }

        return $this->find($active_group_id);
    }

    public function add(Template_Group $group): void
    {
        $collection = $this->find_all();

        $collection->append($group);

        $this->groups = $collection;

        $this->save();
    }

    private function save(): void
    {
        update_option(self::GROUPS_OPTION_NAME, $this->groups);
    }

    private function fetch_groups_from_the_db(): Groups_Collection
    {
        $fetched_groups = get_option(self::GROUPS_OPTION_NAME, null) ?? new Groups_Collection();

        if(is_string($fetched_groups)) {
            $unserialized = @unserialize($fetched_groups, ['allowed_classes' => true]);

            if ($unserialized instanceof Groups_Collection) {
                $fetched_groups = $unserialized;
            }
        }

        return $fetched_groups;
    }
}
