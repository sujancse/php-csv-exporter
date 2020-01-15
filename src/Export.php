<?php


namespace Sujan\Exporter;


use Generator;
use Illuminate\Database\Eloquent\Builder;

class Export
{
    /**
     * @var Builder|null
     */
    private $model;

    /**
     * @var array
     */
    private $heading = [];

    /**
     * @var array
     */
    private $columns;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $delimiter;

    /**
     * Export constructor.
     * @param Builder|null $model
     * @param array $columns
     * @param string $contentType
     * @param string $filename
     * @param string $delimiter
     */
    public function __construct(
        Builder $model = null,
        array $columns = [],
        string $contentType = 'application/csv',
        string $filename = 'export.csv',
        string $delimiter = ";"
    )
    {
        $this->setModel($model);
        $this->setColumns($columns);
        $this->setHeading($columns);
        $this->setFilename($filename);
        $this->setContentType($contentType);
        $this->setDelimiter($delimiter);
    }

    /**
     * @param $contentType
     */
    public function setContentType($contentType)
    {
        header("Content-Type: {$contentType}");
        header("Content-Disposition: attachment; filename={$this->filename};");
    }

    /**
     * return generated CSV
     */
    public function export()
    {
        $generator = $this->write();

        while ($generator->valid()) {
            $generator->next();
        }

        die();
    }

    /**
     * @return Generator
     */
    public function write()
    {
        // open the "output" stream
        $file = fopen('php://output', 'w');

        fputcsv($file, $this->heading, $this->delimiter);

        foreach ($this->model->get() as $data) {
            $line = $this->getLine($data);

            yield fputcsv($file, $line, $this->delimiter);
        }

        fclose($file);
    }

    /**
     * @param $data
     * @return array
     */
    public function getLine($data)
    {
        $line = [];

        foreach ($this->columns as $k => $key) {
            if (is_array($key)) {
                $value = $this->getNestedData($data, $key, $k);
                array_push($line, $value);
            } else {
                array_push($line, $data->{$key});
            }
        }

        return $line;
    }

    /**
     * @param $data
     * @param $keys
     * @param $k
     * @return string
     */
    public function getNestedData($data, $keys, $k)
    {
        foreach ($keys as $kk => $key) {
            $data = isset($data->{$k}->{$key}) ? $data->{$k}->{$key} : '';

            if (is_array($data)) {
                $this->getNestedData($data, $key, $kk);
            }
        }

        return $data;
    }

    /**
     * @param string $delimiter
     */
    protected function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @param Builder $model
     */
    protected function setModel(Builder $model): void
    {
        $this->model = $model;
    }

    /**
     * @param string $filename
     */
    protected function setFilename(string $filename): void
    {
        $this->filename = $filename;
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
     * @param array $columns
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }
}
