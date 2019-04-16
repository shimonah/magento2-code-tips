<?php
namespace Vendor\Module\Model\Csv;

class Export
{
    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    public function __construct(
        \Magento\Framework\File\Csv $csvProcessor
    ) {
        $this->csvProcessor = $csvProcessor;
    }

    public function importFromCsvFile()
    {
        $filePath = '/var/www/magento2/project/media/test.csv';

        $data = [
            ['sku', 'title', 'description'],
            ['CP-123', 'DELL 24 Intel', 'good product'],
            ['CP-101', 'DELL 42 AMD', 'good product too']
        ];

        $this->csvProcessor->saveData($filePath, $data);
    }
}