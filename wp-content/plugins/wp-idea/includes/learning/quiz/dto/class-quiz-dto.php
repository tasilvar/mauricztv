<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz\dto;

class Quiz_DTO
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $description = null;
    public ?string $slug = null;
    public ?string $subtitle = null;
    public ?string $level = null;
    public ?string $duration = null;
	public bool $time_mode = false;
	public ?int $time = null;
	public bool $attempts_mode = false;
	public ?int $attempts_number = null;
    public bool $evaluated_by_admin_mode = false;
    public bool $randomize_question_order = false;
    public bool $randomize_answer_order = false;
    public ?string $featured_image = null;
    public ?array $files = [];
    public array $questions = [];
	public int $points_to_pass = 0;
	public int $points_max = 0;
    public bool $can_see_answers_mode = false;
    public bool $also_show_correct_answers = false;
}