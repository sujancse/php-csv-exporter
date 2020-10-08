<?php


namespace Sujan\Exporter;


use Generator;
use Illuminate\Database\Eloquent\Builder;
use Sujan\Exporter\Contracts\ExporterContract;
use Sujan\Exporter\Traits\ExportGenerator;


class ExportFromQueryBuilder extends Export implements ExporterContract
{
    use ExportGenerator;

    public function __construct(Builder $model, array $columns, string $filename)
    {
        $this->setConfiguration($model, $columns, $filename);
    }

    /**
     * @return Generator
     */
    public function makeGenerator(): Generator
    {
        $this->openOutputStream();

        foreach ($this->model->get() as $data) {
            $line = $this->getLine($data, $this->columns);

            yield fputcsv($this->file, $line, $this->delimiter);
        }
    }

    /*
     * Get data from query builder
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
