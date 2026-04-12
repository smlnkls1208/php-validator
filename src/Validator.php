<?php

namespace PhpValidator;

class Validator
{
    private array $errors = [];
    private array $customMessages = [];
    private static array $ruleMap = [];

    public function __construct(
        private array $data,
        private array $rules,
        array $messages = []
    ) {
        $this->customMessages = $messages;
        $this->registerDefaultRules();
    }

    public static function make(array $data, array $rules, array $messages = []): self
    {
        return new self($data, $rules, $messages);
    }

    public function validate(): bool
    {
        foreach ($this->rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                if (!isset(self::$ruleMap[$rule])) {
                    throw new \InvalidArgumentException("Rule {$rule} not found");
                }

                $ruleClass = self::$ruleMap[$rule];
                if (!$ruleClass::validate($this->data[$field] ?? null)) {
                    $this->addError($field, $rule);
                }
            }
        }
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->validate();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function addError(string $field, string $rule): void
    {
        $this->errors[$field][] = $this->customMessages[$rule] ?? "{$field} validation failed: {$rule}";
    }

    private static function registerDefaultRules(): void
    {
        self::registerRule(\PhpValidator\Rules\Required::class);
    }

    public static function registerRule(string $ruleClass): void
    {
        self::$ruleMap[$ruleClass::name()] = $ruleClass;
    }
}
