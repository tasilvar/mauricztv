<?php

namespace bpmj\wpidea\modules\opinions\infrastructure\persistence;

class Opinions_Query_Criteria
{
    private ?int $opinion_id = null;
	private ?string $user_full_name_like = null;
	private ?string $user_email_like = null;
	private ?array $product_id_in = null;
	private ?string $opinion_content_like = null;
	private ?string $date_of_issue_from = null;
	private ?string $date_of_issue_to = null;

	private ?array $statuses = null;
	private ?array $opinion_rating_in = null;

	private ?int $user_id = null;

    public function get_opinion_id(): ?int
    {
        return $this->opinion_id;
    }

    public function set_opinion_id(?int $opinion_id): void
    {
        $this->opinion_id = $opinion_id;
    }

	public function get_user_full_name_like(): ?string
	{
		return $this->user_full_name_like;
	}

	public function set_user_full_name_like( ?string $user_full_name_like ): void
	{
		$this->user_full_name_like = $user_full_name_like;
	}

	public function get_user_email_like(): ?string
	{
		return $this->user_email_like;
	}

	public function set_user_email_like( ?string $user_email_like ): void
	{
		$this->user_email_like = $user_email_like;
	}

	public function get_product_id_in(): ?array
	{
		return $this->product_id_in;
	}

	public function set_product_id_in(?array $product_id_in): void
	{
		$this->product_id_in = $product_id_in;
	}

	public function get_opinion_content_like(): ?string
	{
		return $this->opinion_content_like;
	}

	public function set_opinion_content_like( ?string $opinion_content_like ): void
	{
		$this->opinion_content_like = $opinion_content_like;
	}

	public function get_date_of_issue_from(): ?string
	{
		return $this->date_of_issue_from;
	}

	public function set_date_of_issue_from( ?string $date_of_issue_from ): void
	{
		$this->date_of_issue_from = $date_of_issue_from;
	}

	public function get_date_of_issue_to(): ?string
	{
		return $this->date_of_issue_to;
	}

	public function set_date_of_issue_to( ?string $date_of_issue_to ): void
	{
		$this->date_of_issue_to = $date_of_issue_to;
	}

	public function get_statuses(): ?array
	{
		return $this->statuses;
	}

    public function set_statuses(?array $statuses): void
	{
		$this->statuses = $statuses;
	}

	public function set_opinion_rating_in(?array $opinion_rating_in): void
	{
		$this->opinion_rating_in = $opinion_rating_in;
	}

	public function get_opinion_rating_in(): ?array
	{
		return $this->opinion_rating_in;
	}

	public function set_user_id(?int $user_id): void
	{
		$this->user_id = $user_id;
	}

	public function get_user_id(): ?int
	{
		return $this->user_id;
	}
}