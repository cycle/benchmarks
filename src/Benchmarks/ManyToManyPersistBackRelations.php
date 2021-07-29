<?php
declare(strict_types=1);

namespace Cycle\Benchmarks\Base\Benchmarks;

use Cycle\Benchmarks\Base\Configurators\UserConfigurator;
use Cycle\Benchmarks\Base\Schemas\SchemaFactory;
use Cycle\Benchmarks\Base\Schemas\TagContextSchema;
use Cycle\Benchmarks\Base\Schemas\TagSchema;
use Cycle\Benchmarks\Base\Schemas\UserSchema;

/**
 * @method UserConfigurator getConfigurator()
 */
abstract class ManyToManyPersistBackRelations extends ManyToManyPersist
{
    public function getSchema(string $mapper): array
    {
        return SchemaFactory::create(
            $mapper,
            (new UserSchema())->withProfileRelation(),
            (new TagSchema())->withUsersRelation(),
            new TagContextSchema()
        )->toArray();
    }
}
