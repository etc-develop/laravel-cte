<?php

namespace Staudenmeir\LaravelCte\Query\Grammars;

use Staudenmeir\LaravelCte\Query\Grammars\Traits\CompilesOracleExpressions;
use Yajra\Oci8\Query\Grammars\OracleGrammar as Base;

class OracleGrammar extends Base implements ExpressionGrammar
{
    use CompilesOracleExpressions {
        // 將 Trait 裡面原有的 prepareBindingsForUpdate 重命名，避免直接衝突
        prepareBindingsForUpdate as traitPrepareBindingsForUpdate;
    }

    /**
     * Prepare the bindings for an update statement.
     *
     * @param array{expressions: list<mixed>, select: list<mixed>, from: list<mixed>, join: list<mixed>,
     *     where: list<mixed>, having: list<mixed>, order: list<mixed>, union: list<mixed>,
     *     unionOrder: list<mixed>} $bindings
     * @param array<string, mixed> $values
     * @return array<int, mixed>
     */
    public function prepareBindingsForUpdate(array $bindings, array $values): array
    {
        $values = array_merge($bindings['expressions'], $values);

        unset($bindings['expressions']);

        /** @var array<int, mixed> $bindings */
        $bindings = parent::prepareBindingsForUpdate($bindings, $values);

        return $bindings;
    }
}
