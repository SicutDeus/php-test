<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\User;

final readonly class CreateUser
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $user = new User();
        $user->name = $args['name'];
        $user->email = $args['email'];
        $user->password = \Hash::make($args['email']);
        $user->save();
        return $user;
    }
}
