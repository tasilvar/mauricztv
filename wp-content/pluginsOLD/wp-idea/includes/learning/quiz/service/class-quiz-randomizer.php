<?php

namespace bpmj\wpidea\learning\quiz\service;

class Quiz_Randomizer
{
    public function randomize_question_answers(array $questions): array
    {
        foreach ($questions as &$question) {
            if (empty($question['answer']) || !is_array($question['answer'])) {
                continue;
            }

            shuffle($question['answer']);
        }

        return $questions;
    }

    public function randomize_questions(array $questions): array
    {
        shuffle($questions);
        return $questions;
    }
}