<?php


namespace Sujan\Exporter;


use Exception;
use Illuminate\Database\Eloquent\Builder;
use PDOStatement;

class Exporter
{
    protected $exporter;

    /**
     * @param $model
     * @param array $columns
     * @param string $filename
     * @return Exporter
     * @throws Exception
     */
    public function build($model, array $columns, string $filename)
    {
        if (gettype($model) == 'string') throw new Exception('Type string for model is not allowed.');

        $this->setExporter($model, $columns, $filename);

        return $this;
    }

    /**
     * @param array | Builder | PDOStatement $model
     * @param $columns
     * @param $filename
     * @return Exporter
     * @throws Exception
     */
    private function setExporter($model, $columns, $filename)
    {
        if (is_array($model)) {
            $this->exporter = new ExportFromArray($model, $columns, $filename);

            return $this;
        }

        /** @var object $model */
        $className = class_basename($model);

        switch ($className) {
            case 'Collection':
                $this->exporter = new ExportFromArray($model, $columns, $filename);
                break;
            case 'Builder':
                $this->exporter = new ExportFromQueryBuilder($model, $columns, $filename);
                break;
            case 'PDOStatement':
                $this->exporter = new ExportFromPDOStatement($model, $columns, $filename);
                break;
            default:
                throw new Exception('Type unknown');
        }

        return $this;
    }

    /**
     * Export the data and die
     */
    public function export(): void
    {
        $this->exporter->export();
    }
}
