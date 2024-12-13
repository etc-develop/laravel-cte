<?php

namespace Staudenmeir\LaravelCte\Connections;

use Staudenmeir\LaravelCte\Query\Grammars\OracleGrammar;
use Staudenmeir\LaravelCte\Query\OracleBuilder;
use Yajra\Oci8\Connectors\OracleConnector;
use Yajra\Oci8\Oci8Connection;

class OracleConnection extends Oci8Connection
{
    public function __construct($pdo, $database = '', $tablePrefix = '', array $config = [])
    {
        $pdo = (new OracleConnector())->connect($config);
        parent::__construct($pdo, $database, $tablePrefix, $config);
        $sessionVars = [
            'NLS_TIME_FORMAT' => 'HH24:MI:SS',
            'NLS_DATE_FORMAT' => 'YYYY-MM-DD HH24:MI:SS',
            'NLS_TIMESTAMP_FORMAT' => 'YYYY-MM-DD HH24:MI:SS',
            'NLS_TIMESTAMP_TZ_FORMAT' => 'YYYY-MM-DD HH24:MI:SS TZH:TZM',
            'NLS_NUMERIC_CHARACTERS' => '.,',
            ...($config['sessionVars'] ?? []),
        ];

        // Like Postgres, Oracle allows the concept of "schema"
        if (isset($config['schema'])) {
            $sessionVars['CURRENT_SCHEMA'] = $config['schema'];
        }

        if (isset($config['session'])) {
            $sessionVars = array_merge($sessionVars, $config['session']);
        }

        if (isset($config['edition'])) {
            $sessionVars = array_merge(
                $sessionVars,
                ['EDITION' => $config['edition']]
            );
        }

        $this->setSessionVars($sessionVars);
    }

    public function query()
    {
        return new OracleBuilder(
            $this,
            new OracleGrammar(),
            $this->getPostProcessor()
        );
    }
}
