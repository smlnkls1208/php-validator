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
            $this->validateField($field, $fieldRules);
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

    private function validateField(string $field, array $rules): void
    {
        foreach ($rules as $rule) {
            $this->applyRule($field, $rule);
        }
    }

    private function parseRule(string $rule): array
    {
        $parts = explode(':', $rule, 2);
        $name = $parts[0];
        $params = isset($parts[1]) ? explode(',', $parts[1]) : [];

        return [$name, $params];
    }

    private function applyRule(string $field, string $rule): void
    {
        [$ruleName, $params] = $this->parseRule($rule);

        if (!isset(self::$ruleMap[$ruleName])) {
            throw new \InvalidArgumentException("Rule {$ruleName} not found");
        }

        $ruleClass = self::$ruleMap[$ruleName];
        if (!$ruleClass::validate($this->data[$field] ?? null, $params)) {
            $this->addError($field, $ruleName, $params);
        }
    }

    private function addError(string $field, string $rule, array $params): void
    {
        $message = $this->customMessages[$rule] ?? $this->getDefaultMessage($rule);
        $replacements = array_merge([':field' => $field], $params);

        $this->errors[$field][] = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $message
        );
    }

    private static function registerDefaultRules(): void
    {
        self::registerRule(\PhpValidator\Rules\Required::class);
        self::registerRule(\PhpValidator\Rules\Min::class);
        self::registerRule(\PhpValidator\Rules\Max::class);
        self::registerRule(\PhpValidator\Rules\Unique::class);
    }

    public static function registerRule(string $ruleClass): void
    {
        self::$ruleMap[$ruleClass::name()] = $ruleClass;
    }
}