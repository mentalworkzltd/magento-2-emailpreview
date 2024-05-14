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

namespace Mentalworkz\EmailPreview\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Escaper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const SENDTEST_EMAILADDRESS         = 'emailpreview@moduletest.com';
    const CONFIG_PATH_PREVIEW_ENABLED   = 'mwz_emailpreview/preview_send/isenabled';
    const CONFIG_PATH_PREVIEW_DEVICES   = 'mwz_emailpreview/preview_send/preview_device/preview_device_sizes';

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Escaper
     */
    protected $escaper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        ProductMetadataInterface $productMetadata,
        ModuleManager $moduleManager,
        Session $session,
        StoreManagerInterface $storeManager,
        Escaper $escaper
    )
    {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->moduleManager = $moduleManager;
        $this->productMetadata = $productMetadata;
        $this->storeManager = $storeManager;
        $this->session = $session;
        $this->escaper = $escaper;
    }

    /**
     * @return null|string
     */
    public function getMagentoVersion(): ?string
    {
        return $this->productMetadata->getVersion();
    }

    /**
     * Returns if module exists or not
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isModuleEnabled($moduleName): bool
    {
        return $this->moduleManager->isEnabled($moduleName);
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getCurrentStore(): \Magento\Store\Api\Data\StoreInterface
    {
        return $this->storeManager->getStore();
    }

    /**
     * @return bool
     */
    public function isAdminLoggedin(): bool
    {
        return $this->session->getUser() && $this->session->getUser()->getId();
    }

    /**
     * @return null|string
     */
    protected function getStoreEmail(): ?string
    {
        return $this->scopeConfig->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    protected function getSender(): array
    {
        return [
            'name' => $this->escaper->escapeHtml('Website Email Preview'),
            'email' => $this->getStoreEmail(),
        ];
    }

    /**
     * @return int
     */
    public function getPreviewSendEnabled(): int
    {
        return (int)$this->scopeConfig->getValue(self::CONFIG_PATH_PREVIEW_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array|null
     */
    public function getPreviewDevices(): ?array
    {
        $deviceList = [];
        $deviceMap = $this->scopeConfig->getValue(self::CONFIG_PATH_PREVIEW_DEVICES, ScopeInterface::SCOPE_STORE);
        if($deviceMap){
            $arrDeviceMap = json_decode($deviceMap, true);
            foreach($arrDeviceMap as $device){
                if($device['enabled']){
                    $value = $device['width'] . 'x' . $device['height'];
                    $deviceList[] = [
                        'label' => $device['label'] . '(' . $value . ')',
                        'value' => $value
                    ];
                }
            }
        }
        return $deviceList;
    }

    /**
     * @param $emailAddress
     * @param $templateId
     * @param $templateVars
     * @param int $storeId
     * @param string $area
     * @return \Magento\Email\Model\Transport
     */
    public function prepareEmail ($emailAddress, $templateId, $templateVars, $storeId = 0, $area = \Magento\Framework\App\Area::AREA_FRONTEND): \Magento\Email\Model\Transport
    {

        $this->inlineTranslation->suspend();

        $emailAddresses = array_map('trim', explode(',', $emailAddress));

        $transport = $this->transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area' => $area,
                    'store' => $storeId,
                ]
            )
            ->setTemplateVars($templateVars)
            ->setFrom($this->getSender())
            ->addTo($emailAddresses)
            ->getTransport();

        $this->inlineTranslation->resume();

        return $transport;
    }

}