<?php
declare(strict_types=1);

namespace Mentalworkz\EmailPreview\Block\Adminhtml\Template;

use Mentalworkz\EmailPreview\Helper\Data as MentalworkzHelper;

class Edit extends \Magento\Email\Block\Adminhtml\Template\Edit
{

    /**
     * @var MentalworkzHelper
     */
    protected $mwzhelper;

    /**
     * Edit constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\Menu\Config $menuConfig
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param \Magento\Email\Model\Template\Config $emailConfig
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
     * @param \Magento\Backend\Block\Widget\Button\ToolbarInterface $toolbar
     * @param MentalworkzHelper $mwzhelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\Menu\Config $menuConfig,
        \Magento\Config\Model\Config\Structure $configStructure,
        \Magento\Email\Model\Template\Config $emailConfig,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList,
        \Magento\Backend\Block\Widget\Button\ToolbarInterface $toolbar,
        MentalworkzHelper $mwzhelper,
        array $data = []
    ) {
        parent::__construct($context, $jsonEncoder, $registry, $menuConfig, $configStructure, $emailConfig, $jsonHelper,
            $buttonList, $toolbar, $data);
        $this->mwzhelper = $mwzhelper;

    }

    /**
     * Prepare layout
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareLayout()
    {
        if ($this->mwzhelper->getPreviewSendEnabled()) {
            $params = ['id' => $this->getRequest()->getParam('id')];
            $this->buttonList->add(
                'previewsend',
                [
                    'label' => __('Preview/Send Test'),
                    'on_click' => "window.open('" . $this->getUrl('mwzemail/email/popup',
                            $params) . "','popup','width=1100,height=800'); return false;",
                    'popup' => true,
                ],
                0,
                80
            );

        }

        return parent::_prepareLayout();
    }

}
