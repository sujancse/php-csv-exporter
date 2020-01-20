<?php


namespace Sujan\Exporter;


use Exception;
use Sujan\Exporter\Contracts\ExporterContract;

class Export
{
    /**
     * @var array|object
     */
    protected $model;

    /**
     * @var array
     */
    protected $columns;

    /*
     * @var array
     */
    private $heading = [];

    /**
     * @var string
     */
    protected $filename;

    /*
     * Array of content types
     */
    private $contentTypes = [
        '.csv' => 'application/csv',
        '.xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    private $contentType;

    /**
     * @var false|resource
     */
    protected $file;

    /**
     * @var string
     */
    protected $delimiter = ";";

    protected function setConfiguration($model, array $columns, string $filename)
    {
        $this->setModel($model);
        $this->setColumns($columns);
        $this->setHeading($columns);
        $this->setFilename($filename);
        $this->setContentType();
        $this->openOutputStream();
    }

    /**
     * @param object | array $model
     */
    protected function setModel($model): void
    {
        $this->model = $model;
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

    /**
     * @param array $heading
     */
    protected function setHeading(array $heading): void
    {
        array_walk_recursive($heading, function ($item) {
            array_push($this->heading, $item);
        });
    }

    /**
     * @param string $filename
     */
    protected function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * Set content type
     */
    public function setContentType()
    {
        preg_match('/(?:.csv|.xlsx)/i', $this->filename, $parts);

        if (!$parts[0]) {
            $this->filename = $this->filename . '.csv';
            $this->contentType = $this->contentTypes['.csv'];
        } else {
            $this->contentType = $this->contentTypes[strtolower($parts[0])];
        }

        header("Content-Type: {$this->contentType}");
        header("Content-Disposition: attachment; filename={$this->filename};");
    }

    private function openOutputStream()
    {
        // open the "output" stream
        $this->file = fopen('php://output', 'w');
    }
}
