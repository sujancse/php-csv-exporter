<?php


namespace Sujan\Exporter;


use Generator;
use Sujan\Exporter\Contracts\ExporterContract;
use Sujan\Exporter\Traits\ExportGenerator;


class ExportFromArray extends Export implements ExporterContract
{
    use ExportGenerator;

    public function __construct(array $model, array $columns, string $filename)
    {
        $this->setConfiguration($model, $columns, $filename);
    }

    /**
     * @return Generator
     */
    public function set(): Generator
    {
        $this->openOutputStream();

        foreach ($this->model as $data) {
            $line = $this->getLine($data, $this->columns);

            yield fputcsv($this->file, $line, $this->delimiter);
        }
    }

    /**
     * Get data out of generator
     */
    public function get(): void
    {
        $generator = $this->set();

        while ($generator->valid()) {
            $generator->next();
        }

        $this->closeOutputStream();

        die();
    }
}
