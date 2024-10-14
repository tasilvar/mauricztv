<?php
namespace bpmj\wpidea\modules\payment_reminders\core\value_objects\email;

class Payment_Reminder_Email_Product_Row_Collection implements \Iterator
{
    private array $products = [];
    private int $current_position = 0;

    public function add(Payment_Reminder_Email_Product_Row $product): self
    {
        $this->products[] = $product;

        return $this;
    }

    public function current(): Payment_Reminder_Email_Product_Row
    {
        return $this->products[$this->current_position];
    }

    public function next(): void
    {
        ++$this->current_position;
    }

    public function key(): int
    {
        return $this->current_position;
    }

    public function valid(): bool
    {
        return isset($this->products[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }

    public function is_empty(): bool
    {
        return empty($this->products);
    }
}