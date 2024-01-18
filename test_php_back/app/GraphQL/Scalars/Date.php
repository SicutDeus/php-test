<?php declare(strict_types=1);

namespace App\GraphQL\Scalars;

use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use Nuwave\Lighthouse\Schema\Types\Scalars\Date as LighthouseDate;

/** Read more about scalars here: https://webonyx.github.io/graphql-php/type-definitions/scalars. */
final class Date extends LighthouseDate
{
    public ?string $description = 'A date in formnat Y-m-d';
}

