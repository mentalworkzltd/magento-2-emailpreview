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

namespace Mentalworkz\EmailPreview\Block\Adminhtml\Template\Grid\Renderer;

use Mentalworkz\EmailPreview\Helper\Data as MentalworkzHelper;

class Action extends \Magento\Email\Block\Adminhtml\Template\Grid\Renderer\Action
{

    /**
     * @var MentalworkzHelper
     */
    protected $mwzhelper;

    /**
     * Action constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param MentalworkzHelper $mwzhelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        MentalworkzHelper $mwzhelper,
        array $data = []
    ) {
        parent::__construct($context, $jsonEncoder, $data);
        $this->mwzhelper = $mwzhelper;
    }

    /**
     * Render grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $actions = [];

        if($this->mwzhelper->getPreviewSendEnabled()) {
            $params = ['id' => $row->getId()];

            $actions[] = [
                'url' => $this->getUrl('*/*/edit', ['id' => $row->getId()]),
                'caption' => __('View/Edit')
            ];

            $actions[] = [
                'url' => $this->getUrl('mwzemail/email/previewsend', $params),
                'caption' => __('Preview/Send'),
            ];

        }else{
            $actions[] = [
                'url' => $this->getUrl('adminhtml/*/preview', ['id' => $row->getId()]),
                'popup' => true,
                'caption' => __('Preview'),
            ];
        }

        $this->getColumn()->setActions($actions);

        return \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action::render($row);
    }

}
