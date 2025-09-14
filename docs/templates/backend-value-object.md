# Backend - Value Object Template

```php
// Template: src/Domain/ValueObjects/{ValueObjectName}.php
<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use App\Domain\Exceptions\Invalid{ValueObjectName}Exception;

final class {ValueObjectName}
{
    private {type} $value;

    public function __construct({type} $value)
    {
        $this->guardValid{ValidationRule}($value);
        $this->value = $this->normalize($value);
    }

    public static function from{Source}({sourceType} ${source}): self
    {
        $value = self::convert{Source}To{ValueObjectName}(${source});
        return new self($value);
    }

    public function getValue(): {type}
    {
        return $this->value;
    }

    public function equals({ValueObjectName} $other): bool
    {
        return $this->value === $other->value;
    }

    private function guardValid{ValidationRule}({type} $value): void
    {
        if (/* validation condition */) {
            throw new Invalid{ValueObjectName}Exception($value);
        }
    }

    private function normalize({type} $value): {type}
    {
        // Apply normalization logic
        return $value;
    }

    private static function convert{Source}To{ValueObjectName}({sourceType} ${source}): {type}
    {
        // Conversion logic
        return /* converted value */;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
```
