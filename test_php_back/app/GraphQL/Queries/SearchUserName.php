<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

final readonly class SearchUserName
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $users = User::all()->where("name", "like", "%$args[name]%");
        return $users;
    }
}
