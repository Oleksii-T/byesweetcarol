<?php

namespace App\Rules;

use App\Models\Attachment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RichImageInputRule implements ValidationRule
{
    public $required;

    public $multiple;

    public function __construct(bool $required = true, bool $multiple = false)
    {
        $this->multiple = $multiple;
        $this->required = $required;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $msg = "The $attribute field is required.";

        if ($this->required && (! $value || ! is_array($value))) {
            $fail($msg);

            return;
        }

        if (! $this->multiple) {
            $res = $this->check($value, $attribute);
            if ($res) {
                $fail($res);
            }

            return;
        }

        $value = Attachment::formatMultipleRichInputRequest($value);

        if (! $this->required && ! $value) {
            return;
        }

        foreach ($value as $file) {
            $res = $this->check($file, $attribute);
            if ($res) {
                $fail($res);

                return;
            }
        }
    }

    private function check($value, $attribute)
    {
        $msg = "The $attribute field is required.";

        // validate data format
        if (
            (! array_key_exists('file', $value) && ! array_key_exists('id', $value)) ||
            ! array_key_exists('alt', $value) ||
            ! array_key_exists('title', $value) ||
            ! array_key_exists('id_old', $value)
        ) {
            return $msg;
        }

        if (! $this->required) {
            return;
        }

        // validate data

        if (! ($value['file'] ?? false) && ! $value['id']) {
            return $msg;
        }

        if (! $value['id'] && ! $value['alt']) {
            return "The $attribute alt field is required.";
        }

        if (! $value['id'] && ! $value['title']) {
            return "The $attribute title field is required.";
        }
    }
}
