<?php

namespace bpmj\wpidea\modules\opinions\core\entities;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\modules\opinions\core\value_objects\Opinion_Content;
use bpmj\wpidea\modules\opinions\core\value_objects\Opinion_Rating;
use bpmj\wpidea\modules\opinions\core\value_objects\Opinion_Status;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\user\User_ID;
use DateTime;

class Opinion
{
    private ?ID $id;
    private product_id $product_id;
    private User_ID $user_id;
    private Opinion_Content $opinion_content;
    private DateTime $date_of_issue;
    private Opinion_Status $status;
    private Opinion_Rating $rating;
	private ?string $user_full_name;
	private ?string $product_name;
	private ?string $user_email;

	private function __construct(
        ?ID $id,
        Product_ID $product_id,
        User_ID $user_id,
        Opinion_Content $opinion_content,
        DateTime $date_of_issue,
        Opinion_Status $status,
        Opinion_Rating $rating,
	    ?string $user_full_name = null,
	    ?string $product_name = null,
	    ?string $user_email = null
    )
    {
        $this->id = $id;
        $this->product_id = $product_id;
        $this->user_id = $user_id;
        $this->opinion_content = $opinion_content;
        $this->date_of_issue = $date_of_issue;
        $this->status = $status;
        $this->rating = $rating;
	    $this->user_full_name = $user_full_name;
	    $this->product_name = $product_name;
	    $this->user_email = $user_email;
    }

    public static function load(
        ID $id,
        Product_ID $product_id,
        User_ID $user_id,
        Opinion_Content $opinion_content,
        DateTime $date_of_issue,
        Opinion_Status $status,
        Opinion_Rating $rating,
	    string $user_full_name,
	    string $product_name,
	    string $user_email
    ): self
    {
        return new self(
            $id,
            $product_id,
            $user_id,
            $opinion_content,
            $date_of_issue,
            $status,
            $rating,
	        $user_full_name,
	        $product_name,
	        $user_email
        );
    }

    public static function create(
        Product_ID $product_id,
        User_ID $user_id,
        Opinion_Content $opinion_content,
        DateTime $date_of_issue,
        Opinion_Rating $rating
    ): self
    {
        return new self(
            null,
            $product_id,
            $user_id,
            $opinion_content,
            $date_of_issue,
            new Opinion_Status(Opinion_Status::WAITING),
            $rating
        );
    }

    public function get_id(): ID
    {
        return $this->id;
    }

    public function get_product_id(): Product_ID
    {
        return $this->product_id;
    }

    public function get_user_id(): User_ID
    {
        return $this->user_id;
    }

    public function get_opinion_content(): Opinion_Content
    {
        return $this->opinion_content;
    }

    public function get_date_of_issue(): DateTime
    {
        return $this->date_of_issue;
    }

    public function get_status(): Opinion_Status
    {
        return $this->status;
    }

	public function get_rating(): Opinion_Rating
	{
		return $this->rating;
	}

	public function get_user_full_name(): string
	{
		return $this->user_full_name;
	}

	public function get_product_name(): string
	{
		return $this->product_name;
	}

	public function get_user_email(): string
	{
		return $this->user_email;
	}

    public function change_status(Opinion_Status $new_status): void
    {
        $this->status = new Opinion_Status($new_status->get_value());
    }
}