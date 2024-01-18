<?php

namespace GraphQLScalars;

use Nuwave\Lighthouse\Schema\Types\Scalars\Date as LighthouseDate;

class Date extends LighthouseDate
{
    public ?string $description = 'A date in formnat Y-m-d';
}
