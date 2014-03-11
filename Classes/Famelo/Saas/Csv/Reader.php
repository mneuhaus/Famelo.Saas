<?php
namespace Famelo\Saas\Csv;


/**
 */
class Reader extends \League\Csv\Reader {
    /**
     * Iterator Query Trait
     */
    use IteratorQuery;

    /**
     * Intelligent Array Combine
     *
     * @param array $keys
     * @param array $value
     *
     * @return array
     */
    private static function combineArray(array $keys, array $value)
    {
        $nbKeys = count($keys);
        $diff = $nbKeys - count($value);
        if ($diff > 0) {
            $value = array_merge($value, array_fill(0, $diff, null));
        } elseif ($diff < 0) {
            $value = array_slice($value, 0, $nbKeys);
        }

        return array_combine($keys, $value);
    }

    /**
     * Return a Filtered Iterator
     *
     * @param callable $callable a callable function to be applied to each Iterator item
     *
     * @return \Iterator
     */
    public function query(callable $callable = null)
    {
        $iterator = new CallbackFilterIterator($this->getIterator(), function ($row) {
            return is_array($row);
        });

        return $this->execute($iterator, $callable);
    }

    /**
     * Apply a callback function on the CSV
     *
     * @param callable $callable The callback function to call on every element.
     *                           The function must return TRUE in order to continue
     *                           iterating over the iterator.
     *
     * @return integer the iteration count
     */
    public function each(callable $callable)
    {
        $iterator = new CallbackFilterIterator($this->getIterator(), function ($row) {
            return is_array($row);
        });

        $iterator = $this->execute($iterator);
        $index = 0;
        foreach ($iterator as $rowIndex => $row) {
            if (! $callable($row, $rowIndex, $iterator)) {
                break;
            }
            $index++;
        }

        return $index;
    }

    /**
     * Return a single row from the CSV
     *
     * @param integer $offset
     *
     * @return array
     *
     * @throws \InvalidArgumentException If the $offset is not a valid Integer
     */
    public function fetchOne($offset = 0)
    {
        $this->setOffset($offset);
        $this->setLimit(1);
        $iterator = $this->query();
        $iterator->rewind();
        $res = $iterator->getInnerIterator()->current();
        if (! is_array($res)) {
            return [];
        }

        return $res;
    }

    /**
     * Return a sequential array of all CSV lines
     *
     * @param callable $callable a callable function to be applied to each Iterator item
     *
     * @return array
     */
    public function fetchAll(callable $callable = null)
    {
        $iterator = $this->query($callable);

        return iterator_to_array($iterator, false);
    }

    /**
     * Return a sequential array of all CSV lines; the rows are presented as associated arrays
     *
     * @param array    $keys     the name for each key member
     * @param callable $callable a callable function to be applied to each Iterator item
     *
     * @return array
     *
     * @throws \InvalidArgumentException If the submitted keys are not integer or strng
     */
    public function fetchAssoc(array $keys, callable $callable = null)
    {
        $validKeys = array_unique(array_filter($keys, function ($value) {
            return self::isValidString($value);
        }));

        if ($keys !== $validKeys) {
            throw new InvalidArgumentException(
                'The named keys should be unique strings Or integer'
            );
        }

        $iterator = $this->query($callable);
        $iterator = new MapIterator($iterator, function ($row) use ($keys) {
            return self::combineArray($keys, $row);
        });

        return iterator_to_array($iterator, false);
    }

    /**
     * Return a single column from the CSV data
     *
     * @param integer  $fieldIndex field Index
     * @param callable $callable   a callable function to be applied to each value to be return
     *
     * @return array
     *
     * @throws \InvalidArgumentException If the column index is not a positive integer or 0
     */
    public function fetchCol($columnIndex = 0, callable $callable = null)
    {
        if (false === filter_var($columnIndex, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]])) {
            throw new InvalidArgumentException(
                'the column index must be a positive integer or 0'
            );
        }

        $iterator = $this->query($callable);
        $iterator = new MapIterator($iterator, function ($row) use ($columnIndex) {
            if (! array_key_exists($columnIndex, $row)) {
                return null;
            }

            return $row[$columnIndex];
        });

        return iterator_to_array($iterator, false);
    }

    /**
     * Instantiate a {@link Writer} class from the current {@link Reader}
     *
     * @param string $open_mode the file open mode flag
     *
     * @return \League\Csv\Writer
     */
    public function getWriter($open_mode = 'w')
    {
        $obj = $this->csv;
        if (! $obj instanceof SplTempFileObject) {
            $obj = new SplFileObject($obj->getRealPath(), $open_mode);
        }
        $csv = new Writer($obj);
        $csv->setDelimiter($this->delimiter);
        $csv->setEnclosure($this->enclosure);
        $csv->setEscape($this->escape);
        $csv->setFlags($this->flags);
        $csv->setEncoding($this->encoding);

        return $csv;
    }
}
