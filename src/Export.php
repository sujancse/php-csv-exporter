<?php


namespace Sujan\Exporter;


use Exception;
use Generator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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
    private $contentType = 'application/csv';

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $delimiter = ";";

    /**
     * Export constructor.
     * @param object | array $model
     * @param array $columns
     * @param string $filename
     */
    public function __construct(
        $model,
        array $columns = [],
        string $filename = 'export.csv'
    )
    {
        $this->setModel($model);
        $this->setColumns($columns);
        $this->setHeading($columns);
        $this->setFilename($filename);
        $this->setContentType();
    }

    /**
     * Set content type
     */
    public function setContentType()
    {
        header("Content-Type: {$this->contentType}");
        header("Content-Disposition: attachment; filename={$this->filename};");
    }

    /**
     * return generated CSV
     * @throws Exception
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
     * @throws Exception
     */
    public function write()
    {
        // open the "output" stream
        $file = fopen('php://output', 'w');

        fputcsv($file, $this->heading, $this->delimiter);

        if (is_array($this->model)) {
            foreach ($this->model as $data) {
                $line = $this->getLine($data);

                yield fputcsv($file, $line, $this->delimiter);
            }
        } else {
            $className = !is_string($this->model) ? class_basename($this->model) : null;

            switch ($className) {
                case 'Collection':
                    foreach ($this->model as $data) {
                        $line = $this->getLine($data);

                        yield fputcsv($file, $line, $this->delimiter);
                    }
                    break;
                case 'Builder':
                    foreach ($this->model->get() as $data) {
                        $line = $this->getLine($data);

                        yield fputcsv($file, $line, $this->delimiter);
                    }
                    break;
                case 'PDOStatement':
                    foreach ($this->model->fetchAll() as $data) {
                        $line = $this->getLine($data);

                        yield fputcsv($file, $line, $this->delimiter);
                    }
                    break;
                default:
                    throw new Exception('Type unknown');
            }
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
                $value = is_array($data) ? $data[$key] : $data->{$key};
                array_push($line, $value);
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
            if (is_array($data)) {
                $data = isset($data[$k][$key]) ? $data[$k][$key] : '';
            } else {
                $data = isset($data->{$k}->{$key}) ? $data->{$k}->{$key} : '';
            }

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
     * @param object | array $model
     */
    protected function setModel($model): void
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
