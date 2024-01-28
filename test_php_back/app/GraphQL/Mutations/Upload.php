<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Image;
use Error;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

final readonly class Upload
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $validator = \Validator::make($args, [
            'file' => ['required', 'image'],
        ]);
        if ($validator->fails()) {
            throw new BadRequestException('Not a image');
        }
        $file = $args['file'];

        Storage::put(Image::getPathOfImage(null, null), $file);

        $image = new Image();
        $image->name = $file->getFileName();
        $image->hash = $file->hashName();
        $image->save();
        return $image;

    }
}
