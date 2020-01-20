<?php


namespace Sujan\Exporter;


use Sujan\Exporter\Contracts\ExporterContract;
use Sujan\Exporter\Traits\ExportGenerator;


class ExportFromArray extends Export implements ExporterContract
{
    use ExportGenerator;

    public function __construct($model, $columns, $filename)
    {
        $this->setConfiguration($model, $columns, $filename);
    }

    public function set()
    {
        foreach ($this->model as $data) {
            $line = $this->getLine($data, $this->columns);

            yield fputcsv($this->file, $line, $this->delimiter);
        }
    }

    public function get()
    {
        $generator = $this->set();

        while ($generator->valid()) {
            $generator->next();
        }

        die();
    }
}
