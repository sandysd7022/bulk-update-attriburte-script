<?php
namespace SD\Updates\Controller\Attrupdate;

use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\Product;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Action as ProductAction;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Csv
     */
    private $fileCsv;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    private $requiredHeaders = ['sku'];

    /**
     * @var array
     */
    private $headersMap;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var Product
     */
    private $productModel;

    /**
     * ProductAttributeImportCommand constructor.
     * @param Csv $fileCsv
     * @param DirectoryList $directoryList
     * @param StoreManagerInterface $storeManager
     * @param ProductRepository $productRepository
     * @param Product $productModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        Csv $fileCsv,
        DirectoryList $directoryList,
        StoreManagerInterface $storeManager,
        ProductRepository $productRepository,
        Product $productModel,
        CollectionFactory $collection,
        ProductAction $action

    )
    {
        $this->productCollection = $collection;
        $this->productAction = $action;
        $this->fileCsv = $fileCsv;
        $this->directoryList = $directoryList;
        $this->storeManager = $storeManager;
        $this->storeManager->setCurrentStore('admin');
        $this->productRepository = $productRepository;
        $this->productModel = $productModel;
        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {
         $path = "var/import/productattributes.csv";
 $csvFilePath = $this->directoryList->getRoot() . '/' . $path;
           if (!file_exists($csvFilePath)) {
                throw new LocalizedException(__('File ' . $csvFilePath . ' does not exist!'));
            }

        $storeIds = array_keys($this->storeManager->getStores());
        $csvData = $this->fileCsv->getData($csvFilePath);
        $attributeArray = array();

        foreach ($csvData as $row => $data) {
           if ($row > 0){

           //$updateAttributes['sku'] = $data[$attributeArray['sku']];
           $updateAttributes['cross_reference'] = $data[1];
           $updateAttributes['searchable_cross_reference'] = $data[2];
           $updateAttributes['related_mpns'] = $data[3];
           $updateAttributes['related_oe_number'] = $data[4];
           $updateAttributes['searchable_sku'] = $data[5];
           $updateAttributes['searchable_mpn'] = $data[6];
           $updateAttributes['searchable_oe_number'] = $data[7];
           $updateAttributes['other_part_number'] = $data[8];
           $updateAttributes['searchable_other_part_number'] = $data[9];


           if ($this->productModel->getIdBySku($data[0])) {
            $this->productAction->updateAttributes([$this->productModel->getIdBySku($data[0])], $updateAttributes, 1);
           }       

        
           echo 'Update Complete Product: '.$data[0].'<br/>';

        
           }
        }


           echo 'All product Update complete';
        }
}