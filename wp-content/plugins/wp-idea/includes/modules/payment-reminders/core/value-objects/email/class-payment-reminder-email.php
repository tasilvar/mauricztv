<?php

namespace bpmj\wpidea\modules\payment_reminders\core\value_objects\email;

use bpmj\wpidea\data_types\mail\Email_Address;
use bpmj\wpidea\data_types\personal_data\Full_Name;

class Payment_Reminder_Email
{
    private Email_Address $to;
    private Full_Name $name;
    private Payment_Reminder_Email_Product_Row_Collection $products;
    private string $formatted_amount;
    private int $order_id;
    private string $date;

    public function __construct(
        Email_Address $to,
        Full_Name $name,
        Payment_Reminder_Email_Product_Row_Collection $products,
        string $formatted_amount,
        int $order_id,
        string $date
    ) {
        $this->to = $to;
        $this->name = $name;
        $this->products = $products;
        $this->formatted_amount = $formatted_amount;
        $this->order_id = $order_id;
        $this->date = $date;
    }

    public function get_to(): Email_Address
    {
        return $this->to;
    }

    public function get_name(): Full_Name
    {
        return $this->name;
    }

    public function get_products(): Payment_Reminder_Email_Product_Row_Collection
    {
        return $this->products;
    }

    public function get_formatted_amount(): string
    {
        return $this->formatted_amount;
    }

    public function get_order_id(): int
    {
        return $this->order_id;
    }

    public function get_date(): string
    {
        return $this->date;
    }
}