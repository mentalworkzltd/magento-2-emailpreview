<?php
/**
 * Mentalworkz
 *
 * @category    Mentalworkz
 * @package     Mentalworkz_EmailPreview
 * @copyright   Copyright (c) Mentalworkz (https://www.mentalworkz.co.uk/)
 * @author      Shaun Clifford
 */
declare(strict_types=1);

namespace Mentalworkz\EmailPreview\Model\Config\Backend\Serialized;

use Mentalworkz\EmailPreview\Helper\Data as MentalworkzHelper;
use Magento\Framework\App\Request\Http;

class DeviceSizeMap extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{

    /**
     * @var MentalworkzHelper
     */
    protected $mwzHelper;

    /**
     * @var Http
     */
    protected $request;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        MentalworkzHelper $mwzHelper,
        Http $request,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data,
            $serializer);

        $this->mwzHelper = $mwzHelper;
        $this->request = $request;
    }

    /**
     * Unset array element with '__empty' key
     *
     * @return $this
     */
    public function beforeSave()
    {
        $params = $this->request->getParams();
        $configValue = $this->getValue();
        $this->setValue($configValue);

        return parent::beforeSave();
    }

    /**
     * Processing object after load data
     *
     * @return void
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
    }


}
