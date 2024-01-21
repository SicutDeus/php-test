<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\User;

final readonly class CustomSearchQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        if ($args['model'] === 'User') {
            $val = $args['value'];
            $users = User::all()->where('name', 'LIKE', "%%");
            dd($users);
            return $users->id;
        }
        return 'jopa';
    }
}
