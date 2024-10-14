<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz\model;

use bpmj\wpidea\learning\quiz\Quiz_ID;
use bpmj\wpidea\learning\quiz\value_object\Quiz_Answers_Preview_Settings;
use bpmj\wpidea\learning\quiz\value_object\Quiz_Attempts_Limit_Settings;
use bpmj\wpidea\learning\quiz\value_object\Quiz_Randomization_Settings;
use bpmj\wpidea\learning\quiz\value_object\Quiz_Time_Limit_Settings;

class Quiz
{
    private Quiz_ID $id;
    private ?string $name;
	private ?string $description;
	private ?string $slug;
	private ?string $subtitle;
	private ?string $level;
	private ?string $duration;
	private Quiz_Time_Limit_Settings $time_limit_settings;
	private Quiz_Attempts_Limit_Settings $attempts_limit_settings;
	private bool $evaluated_by_admin;
	private Quiz_Randomization_Settings $randomization_settings;
	private ?string $featured_image;
	private ?Quiz_File_Collection $files;
	private array $questions;
	private int $points_to_pass;
	private int $points_max;
	private Quiz_Answers_Preview_Settings $answers_preview_settings;

	public function __construct(
        Quiz_ID $id,
        ?string $name,
        ?string $description,
        ?string $slug,
        ?string $subtitle,
        ?string $level,
        ?string $duration,
        Quiz_Time_Limit_Settings $time_limit_settings,
		Quiz_Attempts_Limit_Settings $attempts_limit_settings,
        bool $evaluated_by_admin,
		Quiz_Randomization_Settings $randomization_settings,
        ?string $featured_image,
        ?Quiz_File_Collection $files,
	    array $questions,
        int $points_to_pass,
        int $points_max,
		Quiz_Answers_Preview_Settings $answers_preview_settings
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->slug = $slug;
        $this->subtitle = $subtitle;
        $this->level = $level;
        $this->duration = $duration;
		$this->time_limit_settings = $time_limit_settings;
		$this->attempts_limit_settings = $attempts_limit_settings;
		$this->evaluated_by_admin = $evaluated_by_admin;
		$this->randomization_settings = $randomization_settings;
		$this->featured_image = $featured_image;
		$this->files = $files;
		$this->questions = $questions;
		$this->points_to_pass = $points_to_pass;
		$this->points_max = $points_max;
		$this->answers_preview_settings = $answers_preview_settings;
	}


    public function get_id(): Quiz_ID
    {
        return $this->id;
    }

    public function get_name(): ?string
    {
        return $this->name;
    }

    public function get_description(): ?string
    {
        return $this->description;
    }

    public function get_slug(): ?string
    {
        return $this->slug;
    }

    public function get_subtitle(): ?string
    {
        return $this->subtitle;
    }

    public function get_level(): ?string
    {
        return $this->level;
    }

    public function get_duration(): ?string
    {
        return $this->duration;
    }

	public function get_time_limit_settings(): Quiz_Time_Limit_Settings
	{
		return $this->time_limit_settings;
	}

	public function get_attempts_limit_settings(): Quiz_Attempts_Limit_Settings
	{
		return $this->attempts_limit_settings;
	}

    public function get_evaluated_by_admin(): bool
    {
        return $this->evaluated_by_admin;
    }

	public function get_randomization_settings(): Quiz_Randomization_Settings
	{
		return $this->randomization_settings;
	}

    public function get_featured_image(): ?string
    {
        return $this->featured_image;
    }

    public function get_files(): ?Quiz_File_Collection
    {
        return $this->files;
    }

	public function get_questions(): array
	{
		return $this->questions;
	}

	public function change_questions(array $new_questions): void
	{
		$this->questions = $new_questions;
	}

	public function get_points_to_pass(): int
	{
		return $this->points_to_pass;
	}

	public function change_points_to_pass(int $new_points_to_pass): void
	{
		$this->points_to_pass = $new_points_to_pass;
	}

	public function get_points_max(): int
	{
		return $this->points_max;
	}

	public function change_points_max(int $new_points_max): void
	{
		$this->points_max = $new_points_max;
	}

	public function get_answers_preview_settings(): Quiz_Answers_Preview_Settings
	{
		return $this->answers_preview_settings;
	}
}