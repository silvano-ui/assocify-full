<?php

namespace Modules\Reports\Services;

class ChartService
{
    public function generateChartData(array $data, array $config): array
    {
        // Transform raw data into Chart.js format based on config
        $labels = [];
        $datasets = [];

        // Simple implementation assuming 'label' and 'value' keys in data
        $labelKey = $config['label_key'] ?? 'label';
        $valueKey = $config['value_key'] ?? 'value';

        $values = [];
        foreach ($data as $item) {
            $labels[] = $item[$labelKey] ?? '';
            $values[] = $item[$valueKey] ?? 0;
        }

        $datasets[] = [
            'label' => $config['dataset_label'] ?? 'Data',
            'data' => $values,
            'backgroundColor' => $config['color'] ?? '#36A2EB',
        ];

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }
}
