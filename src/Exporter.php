<?php


namespace Sujan\Exporter;


use Exception;
use Sujan\Exporter\Contracts\ExporterContract;

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

        self::setExporter($model, $columns, $filename);
    }

    /**
     * Export the data and die
     */
    public static function export(): void
    {
        self::$exporter->get();
    }

    /**
     * @param array | object $model
     * @param $columns
     * @param $filename
     * @return ExporterContract
     * @throws Exception
     */
    private static function setExporter($model, $columns, $filename): ExporterContract
    {
        if (is_array($model)) {
            self::$exporter = new ExportFromArray($model, $columns, $filename);
            self::$exporter->set();

            return self::$exporter;
        }

        /** @var object $model */
        $className = class_basename($model);

        switch ($className) {
            case 'Collection':
                self::$exporter = new ExportFromArray($model, $columns, $filename);
                break;
            case 'Builder':
                self::$exporter = new ExportFromQueryBuilder($model, $columns, $filename);
                break;
            case 'PDOStatement':
                self::$exporter = new ExportFromPDOStatement($model, $columns, $filename);
                break;
            default:
                throw new Exception('Type unknown');
        }

        self::$exporter->set();

        return self::$exporter;
    }
}
