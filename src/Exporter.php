<?php


namespace Sujan\Exporter;


use Exception;

class Exporter
{
    protected static $exporter;

    /**
     * @param $model
     * @param array $columns
     * @param string $filename
     * @throws Exception
     */
    public static function init($model, array $columns, string $filename): void
    {
        if (gettype($model) == 'string') throw new Exception('Type string for model is not allowed.');

        self::$exporter = new Export($model, $columns, $filename);
    }

    /**
     * Export the data and die
     */
    public static function export(): void
    {
        self::$exporter->export();
    }
}
