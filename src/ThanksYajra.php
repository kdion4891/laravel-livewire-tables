<?php

// Part of this code was "borrowed" from the laravel datatables package.
// https://github.com/yajra/laravel-datatables/blob/9.0/src/EloquentDataTable.php
// Yajra, you're a genius!

namespace Kdion4891\LaravelLivewireTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

class ThanksYajra
{
    private $query;

    public function relationship($attribute)
    {
        $parts = explode('.', $attribute);

        return (object)[
            'attribute' => array_pop($parts),
            'name' => implode('.', $parts),
        ];
    }

    public function attribute(Builder $query, $relationships, $attribute)
    {
        $table = '';
        $this->query = $last_query = $query;

        foreach (explode('.', $relationships) as $each_relationship) {
            $model = $last_query->getRelation($each_relationship);

            switch (true) {
                case $model instanceof BelongsToMany:
                    $pivot = $model->getTable();
                    $pivotPK = $model->getExistenceCompareKey();
                    $pivotFK = $model->getQualifiedParentKeyName();
                    $this->join($pivot, $pivotPK, $pivotFK);

                    $related = $model->getRelated();
                    $table = $related->getTable();
                    $tablePK = $related->getForeignKey();
                    $foreign = $pivot . '.' . $tablePK;
                    $other = $related->getQualifiedKeyName();

                    $last_query->addSelect($table . '.' . $attribute);
                    $this->join($table, $foreign, $other);

                    break;

                case $model instanceof HasOneOrMany:
                    $table = $model->getRelated()->getTable();
                    $foreign = $model->getQualifiedForeignKeyName();
                    $other = $model->getQualifiedParentKeyName();
                    break;

                case $model instanceof BelongsTo:
                    $table = $model->getRelated()->getTable();
                    $foreign = $model->getQualifiedForeignKeyName();
                    $other = $model->getQualifiedOwnerKeyName();
                    break;

                default:
                    return $attribute;
            }

            $this->join($table, $foreign, $other);
            $last_query = $model->getQuery();
        }

        return $table . '.' . $attribute;
    }

    private function join($table, $foreign, $other)
    {
        $this->query->leftJoin($table, $foreign, $other);
    }
}
