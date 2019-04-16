<?php
namespace Vendor\Module\Model\Csv;

class Import
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    private $readDir;
    /**
     * @var \Magento\Framework\File\Csv
     */
    private $csvProcessor;

    /**
     * @var ResourceModel\ZipCode
     */
    private $zipCodeResource;

    /**
     * @var ZipCodeFactory
     */
    private $zipCodeFactory;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $filesystem;

    /**
     * ZipCodeImporter constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Directory\Model\Region $region
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\Module\Dir\Reader $readDir
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param ResourceModel\ZipCode $zipCodeResource
     * @param ZipCodeFactory $zipCodeFactory
     * @param \Magento\Framework\Filesystem\Driver\File $filesystem
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Module\Dir\Reader $readDir,
        \Magento\Framework\File\Csv $csvProcessor,
        \Vendor\Module\Model\ResourceModel\ZipCode $zipCodeResource,
        \Vendor\Module\Model\ZipCodeFactory $zipCodeFactory,
        \Magento\Framework\Filesystem\Driver\File $filesystem
    ) {
        $this->logger = $logger;
        $this->dateTime = $dateTime;
        $this->readDir = $readDir;
        $this->csvProcessor = $csvProcessor;
        $this->zipCodeResource = $zipCodeResource;
        $this->zipCodeFactory = $zipCodeFactory;
        $this->filesystem = $filesystem;
    }

    public function import()
    {
        $filePath = $this->readDir->getModuleDir('etc', 'Vendor_Module') . '/' . 'zipcode-database.csv';

        if ($this->filesystem->isExists($filePath) && $this->filesystem->isFile($filePath)
                && $this->filesystem->isReadable($filePath)) {
            $rows = $this->csvProcessor->getData($filePath);

            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];

                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }

                $zipCode = $this->zipCodeFactory->create();

                if (isset($data['Zipcode']) && isset($data['State'])) {
                    $zipCode->setZipCode($data['Zipcode']);
                    $zipCode->setState($data['State']);
                    $zipCode->setCreatedAt($this->dateTime->date());

                    try {
                        $this->zipCodeResource->save($zipCode);
                    } catch (\Exception $e) {
                        $this->logger->critical($e);
                    }
                }
            }
        }
    }
}