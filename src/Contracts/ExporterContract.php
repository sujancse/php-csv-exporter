<?php


namespace Sujan\Exporter\Contracts;


interface ExporterContract
{
    public function makeGenerator(): \Generator;
    public function export(): void;
}
