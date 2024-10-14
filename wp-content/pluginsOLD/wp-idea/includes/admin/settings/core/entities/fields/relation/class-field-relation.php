<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields\relation;

class Field_Relation
{
	public const TYPE_DEPENDS_ON_RELATED_TOGGLE_CHECKED = 'checked';
	public const TYPE_DEPENDS_ON_RELATED_TOGGLE_NOT_CHECKED = 'not-checked';
	public const TYPE_DEPENDS_ON_SELECT_VALUE_EQUALS = 'select-value-equals';
	public const TYPE_DEPENDS_ON_SELECT_VALUE_NOT_EQUALS = 'select-value-not-equals';

	private string $related_field_name;
	private string $relation_type;
	private ?string $select_field_value;

	private function __construct(
		string $related_field_name,
		string $relation_type,
		?string $select_field_value = null
	)
	{
		$this->related_field_name = $related_field_name;
		$this->relation_type = $relation_type;
		$this->select_field_value = $select_field_value;
	}

	public static function create(string $related_field_name, string $relation_type, ?string $select_field_value = null): self
	{
		return new self($related_field_name, $relation_type, $select_field_value);
	}

	public function get_related_field_name(): string
	{
		return $this->related_field_name;
	}

	public function get_relation_type(): string
	{
		return $this->relation_type;
	}

	public function get_select_field_value(): ?string
	{
		return $this->select_field_value;
	}
}
