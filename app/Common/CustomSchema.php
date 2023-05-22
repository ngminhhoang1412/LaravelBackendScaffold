<?php

namespace App\Common;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

class CustomSchema extends Schema
{
    /**
     * Get a schema builder instance for a connection.
     *
     * @param  string|null  $name
     * @return Builder
     */
    public static function connection($name): Builder
    {
        /** @var Builder $builder */
        $builder = static::$app['db']->connection($name)->getSchemaBuilder();
        $builder->blueprintResolver(static function($table, $callback) {
            return new CustomBlueprint($table, $callback);
        });
        return $builder;
    }

    /**
     * Get a schema builder instance for the default connection.
     *
     * @return Builder
     */
    protected static function getFacadeAccessor(): Builder
    {
        /** @var Builder $builder */
        $builder = static::$app['db']->connection()->getSchemaBuilder();
        $builder->blueprintResolver(static function($table, $callback) {
            return new CustomBlueprint($table, $callback);
        });
        return $builder;
    }
}
