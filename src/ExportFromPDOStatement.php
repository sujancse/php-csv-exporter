<?php


namespace Sujan\Exporter;


use Generator;
use PDOStatement;
use Sujan\Exporter\Contracts\ExporterContract;
use Sujan\Exporter\Traits\ExportGenerator;


class ExportFromPDOStatement extends Export implements ExporterContract
{
    use ExportGenerator;

    public function __construct(PDOStatement $model, array $columns, string $filename)
    {
        $this->setConfiguration($model, $columns, $filename);
    }

    /**
     * @return Generator
     */
    public function makeGenerator(): Generator
    {
        $this->openOutputStream();

        foreach ($this->model->fetchAll() as $data) {
            $line = $this->getLine($data, $this->columns);

            yield fputcsv($this->file, $line, $this->delimiter);
        }
    }

    /**
     * Get data from PDO statement
     */
    public function export(): void
    {
        $generator = $this->makeGenerator();

        while ($generator->valid()) {
            $generator->next();
        }

        $this->closeOutputStream();

        die();
    }
}
