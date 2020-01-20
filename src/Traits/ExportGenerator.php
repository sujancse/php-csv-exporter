<?php


namespace Sujan\Exporter\Traits;


trait ExportGenerator
{
    /**
     * @param $data
     * @param $columns
     * @return array
     */
    protected function getLine($data, $columns)
    {
        $line = [];

        foreach ($columns as $k => $key) {
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
    protected function getNestedData($data, $keys, $k)
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
}
