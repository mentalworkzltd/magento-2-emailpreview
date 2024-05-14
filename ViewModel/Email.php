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


namespace Mentalworkz\EmailPreview\ViewModel;

use Magento\Email\Model\Template\Config as ConfigTemplates;
use Magento\Email\Model\ResourceModel\Template\CollectionFactory as TemplateCollection;
use Magento\Email\Model\TemplateFactory;
use Magento\Email\Model\ResourceModel\Template as TemplateResource;
use Magento\Framework\App\Request\Http;
use Magento\Store\Api\StoreRepositoryInterface;

class Email implements \Magento\Framework\View\Element\Block\ArgumentInterface
{

    public const ENTITIES = ['order', 'invoice', 'shipment', 'creditmemo', 'customer'];

    /**
     * @var TemplateFactory
     */
    protected $emailFactory;

    /**
     * @var TemplateResource
     */
    protected $templateResource;

    /**
     * @var array
     */
    protected $templateDetails = [];

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var TemplateCollection
     */
    protected $templateCollection;

    /**
     * @var ConfigTemplates
     */
    protected $configTemplates;

    /**
     * @var TemplateCollection
     */
    protected $customEmailTemplates;

    public function __construct(
        Http $request,
        ConfigTemplates $configTemplates,
        TemplateCollection $templateCollection,
        TemplateFactory $emailFactory,
        TemplateResource $templateResource,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->request = $request;
        $this->configTemplates = $configTemplates;
        $this->templateCollection = $templateCollection;
        $this->emailFactory = $emailFactory;
        $this->templateResource = $templateResource;
        $this->storeRepository = $storeRepository;
    }

    public function getEmailTemplateDetails(): ?array
    {
        if($this->templateDetails){
            return $this->templateDetails;
        }

        if ($templateId = $this->request->getParam('id')) {
            $emailTemplate = $this->emailFactory->create();
            $this->templateResource->load($emailTemplate, $templateId);
            if ($emailTemplate->getId()) {
                $this->templateDetails = [
                    'id' => $emailTemplate->getId(),
                    'code' => $emailTemplate->getTemplateCode()
                ];
            }
        }
        return $this->templateDetails;
    }

    public function getStores(): array
    {
        $stores = $this->storeRepository->getList();
        uasort(
            $stores,
            function ($storeA, $storeB) {
                return $storeA->getId() <=> $storeB->getId();
            }
        );
        return $stores;
    }

    public function getConfigEmailTemplates (): array
    {
        return $this->configTemplates->getAvailableTemplates();
    }

    public function getCustomEmailTemplates (): \Magento\Email\Model\ResourceModel\Template\Collection
    {
        $this->customEmailTemplates = $this->templateCollection->create();
        return $this->customEmailTemplates;
    }
}
