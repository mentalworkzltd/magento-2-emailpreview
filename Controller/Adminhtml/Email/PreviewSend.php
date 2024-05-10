<?php
declare(strict_types=1);

namespace Mentalworkz\EmailPreview\Controller\Adminhtml\Email;

use Magento\Framework\Controller\ResultFactory;
use Mentalworkz\EmailPreview\Helper\Data as MentalworkzHelper;

class PreviewSend extends \Magento\Backend\App\Action
{
    /**
     * @var resultFactory
     */
    protected $resultFactory;

    /**
     * @var MentalworkzHelper
     */
    protected $mwzhelper;

    /**
     * PreviewSend constructor.
     * @param ResultFactory $resultFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param MentalworkzHelper $mwzhelper
     */
    public function __construct(
        ResultFactory $resultFactory,
        \Magento\Backend\App\Action\Context $context,
        MentalworkzHelper $mwzhelper
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultFactory;
        $this->mwzhelper = $mwzhelper;
    }

    /**
     * Preview transactional email action.
     */
    public function execute()
    {
        try {

            if(!$this->mwzhelper->getPreviewSendEnabled()){
                $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
                $resultForward->forward('noroute');
                return $resultForward;
            }

            $this->_view->loadLayout();
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Email - Preview and Send'));
            $this->_view->renderLayout();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_redirect('adminhtml/*/');
        }
    }
}
