<?php

namespace Autoborna\CoreBundle\Helper;

use Autoborna\CoreBundle\Model\AbstractCommonModel;

class DataExporterHelper
{
    /**
     * Standard function to generate an array of data via any model's "getEntities" method.
     *
     * Overwrite in your controller if required.
     *
     * @param int|null $start
     *
     * @return array
     */
    public function getDataForExport($start, AbstractCommonModel $model, array $args, callable $resultsCallback = null)
    {
        $args['limit'] = $args['limit'] < 200 ? 200 : $args['limit'];
        $args['start'] = $start;

        $results = $model->getEntities($args);
        $items   = $results['results'];
        if (0 === count($items)) {
            return null;
        }
        unset($results);

        $toExport = [];

        unset($args['withTotalCount']);

        if (is_callable($resultsCallback)) {
            foreach ($items as $item) {
                $row = array_map(function ($itemEncode) {
                    return html_entity_decode($itemEncode, ENT_QUOTES);
                }, $resultsCallback($item));

                $toExport[] = $this->secureAgainstCsvInjection($row);
            }
        } else {
            foreach ($items as $item) {
                $toExport[] = $this->secureAgainstCsvInjection((array) $item);
            }
        }

        $model->getRepository()->clear();

        return $toExport;
    }

    /**
     * @return array
     */
    private function secureAgainstCsvInjection(array $row)
    {
        foreach ($row as $colNum => $colVal) {
            if ($colVal && in_array(substr($colVal, 0, 1), ['+', '-', '=', '@'])) {
                $row[$colNum] = ' '.$colVal;
            }
        }

        return $row;
    }
}
