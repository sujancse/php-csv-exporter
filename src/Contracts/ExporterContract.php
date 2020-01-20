<?php


namespace Sujan\Exporter\Contracts;



use Generator;

interface ExporterContract
{
    public function set(): Generator;
    public function get(): void;
}
