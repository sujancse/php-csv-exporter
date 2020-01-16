<?php


namespace Sujan\Exporter\contracts;



interface ExporterContract
{
    public function init($model, array $columns, $filename): void;
    public function export(): void;
}
